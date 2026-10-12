<?php
require_once 'Controller.php';

class LaporanController extends Controller {

    private function getReportData($token, $fStatus, $fProdi, $fTahun, $fQ) {
        $params = [
            'status'   => $fStatus,
            'id_prodi' => $fProdi,
            'tahun'    => $fTahun,
            'q'        => $fQ,
        ];

        $response = ApiClient::get('/reports/rekapitulasi', $params, $token);

        if (!empty($response['data'])) {
            return [
                'mahasiswa' => $response['data']['mahasiswa'] ?? [],
                'stats'     => $response['data']['stats'] ?? ['Aktif' => 0, 'Cuti' => 0, 'Lulus' => 0, 'Drop Out' => 0, 'total' => 0],
                'prodiList' => $response['data']['prodiList'] ?? [],
            ];
        }

        // Fallback jika API belum memperbarui endpoint /reports
        $userProfile = $_SESSION['user'] ?? [];
        $userFak = $userProfile['idFakultas'] ?? $userProfile['id_fakultas'] ?? null;
        $userProdi = $userProfile['idProgramStudi'] ?? $userProfile['id_program_studi'] ?? null;
        $userRoleCode = is_array($userProfile['role'] ?? null) 
            ? strtoupper($userProfile['role']['kodeRole'] ?? $userProfile['role']['namaRole'] ?? '') 
            : strtoupper((string)($userProfile['role'] ?? ''));
        $isDekanat = in_array($userRoleCode, ['DEKANAT', 'ADMIN_DEKANAT']) && !empty($userFak);
        $isProdi = in_array($userRoleCode, ['KAPRODI', 'ADMIN_PRODI']) && !empty($userProdi);

        $mhsRes = ApiClient::get('/mahasiswa', ['limit' => 200], $token);
        $rawMhs = $mhsRes['data'] ?? [];

        $prodiRes = ApiClient::get('/program-studi', ['limit' => 100], $token);
        $rawProdi = $prodiRes['data'] ?? [];

        $prodiList = [];
        foreach ($rawProdi as $p) {
            if ($isDekanat && (string)($p['idFakultas'] ?? '') !== (string)$userFak) {
                continue;
            }
            if ($isProdi && (string)($p['id'] ?? '') !== (string)$userProdi) {
                continue;
            }
            $prodiList[] = [
                'id'               => (string)($p['id'] ?? ''),
                'kodeProgramStudi' => $p['kodeProgramStudi'] ?? '',
                'namaProgramStudi' => $p['namaProgramStudi'] ?? '',
                'jenjang'          => $p['jenjang'] ?? 'S1',
            ];
        }

        $mahasiswa = [];
        $stats = ['Aktif' => 0, 'Cuti' => 0, 'Lulus' => 0, 'Drop Out' => 0, 'total' => 0];

        foreach ($rawMhs as $m) {
            if ($isDekanat) {
                $mFak = $m['programStudi']['idFakultas'] ?? $m['idFakultas'] ?? '';
                if ((string)$mFak !== (string)$userFak) {
                    continue;
                }
            }
            if ($isProdi) {
                $mProdi = $m['idProgramStudi'] ?? $m['programStudi']['id'] ?? '';
                if ((string)$mProdi !== (string)$userProdi) {
                    continue;
                }
            }

            $st = $m['statusMahasiswa'] ?? 'Aktif';
            if (isset($stats[$st])) {
                $stats[$st]++;
            } else {
                $stats[$st] = 1;
            }
            $stats['total']++;

            if (!empty($fStatus) && $fStatus !== 'Semua' && $st !== $fStatus) continue;
            if (!empty($fProdi) && (string)($m['idProgramStudi'] ?? '') !== (string)$fProdi) continue;
            if (!empty($fTahun)) {
                $thnMhs = date('Y', strtotime($m['tanggalMasuk'] ?? 'now'));
                if ($thnMhs !== $fTahun) continue;
            }

            $mahasiswa[] = [
                'id'               => (string)($m['id'] ?? ''),
                'npm'              => $m['npm'] ?? '',
                'namaMahasiswa'    => $m['namaMahasiswa'] ?? '',
                'jenisKelamin'     => $m['jenisKelamin'] ?? 'L',
                'tempatLahir'      => $m['tempatLahir'] ?? '-',
                'tanggalLahir'     => $m['tanggalLahir'] ?? '-',
                'tanggalMasuk'     => $m['tanggalMasuk'] ?? '-',
                'statusMahasiswa'  => $st,
                'idProgramStudi'   => (string)($m['idProgramStudi'] ?? ''),
                'namaProgramStudi' => $m['programStudi']['namaProgramStudi'] ?? '-',
                'namaFakultas'     => $m['programStudi']['fakultas']['namaFakultas'] ?? '-',
            ];
        }

        return [
            'mahasiswa' => $mahasiswa,
            'stats'     => $stats,
            'prodiList' => $prodiList,
        ];
    }

    public function index() {
        if (!isset($_SESSION['token'])) {
            return $this->redirect('/login');
        }

        $token   = $_SESSION['token'];
        $fStatus = trim($_GET['status'] ?? '');
        $fProdi  = trim($_GET['id_prodi'] ?? '');
        $fTahun  = trim($_GET['tahun'] ?? '');
        $fQ      = trim($_GET['q'] ?? '');

        $report = $this->getReportData($token, $fStatus, $fProdi, $fTahun, $fQ);

        $this->view('laporan/index', [
            'mahasiswa' => $report['mahasiswa'],
            'stats'     => $report['stats'],
            'prodiList' => $report['prodiList'],
            'fStatus'   => $fStatus,
            'fProdi'    => $fProdi,
            'fTahun'    => $fTahun,
            'fQ'        => $fQ,
            'title'     => 'Laporan Data Mahasiswa',
        ]);
    }

    public function exportCsv() {
        if (!isset($_SESSION['token'])) {
            return $this->redirect('/login');
        }

        $token   = $_SESSION['token'];
        $fStatus = trim($_GET['status'] ?? '');
        $fProdi  = trim($_GET['id_prodi'] ?? '');
        $fTahun  = trim($_GET['tahun'] ?? '');
        $fQ      = trim($_GET['q'] ?? '');

        $report = $this->getReportData($token, $fStatus, $fProdi, $fTahun, $fQ);
        $mahasiswa = $report['mahasiswa'];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Laporan_Mahasiswa_UMB_' . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM

        fputcsv($output, ['No', 'NPM', 'Nama Mahasiswa', 'L/P', 'Tempat Lahir', 'Tanggal Lahir', 'Tanggal Masuk', 'Program Studi', 'Fakultas', 'Status']);

        $no = 1;
        foreach ($mahasiswa as $m) {
            fputcsv($output, [
                $no++,
                $m['npm'] ?? '',
                $m['namaMahasiswa'] ?? $m['nama_mahasiswa'] ?? '',
                $m['jenisKelamin'] ?? $m['jenis_kelamin'] ?? '',
                $m['tempatLahir'] ?? $m['tempat_lahir'] ?? '-',
                $m['tanggalLahir'] ?? $m['tanggal_lahir'] ?? '-',
                $m['tanggalMasuk'] ?? $m['tanggal_masuk'] ?? '-',
                $m['namaProgramStudi'] ?? $m['nama_program_studi'] ?? '-',
                $m['namaFakultas'] ?? $m['nama_fakultas'] ?? '-',
                $m['statusMahasiswa'] ?? $m['status_mahasiswa'] ?? 'Aktif'
            ]);
        }

        fclose($output);
        exit;
    }

    public function cetakPdf() {
        if (!isset($_SESSION['token'])) {
            return $this->redirect('/login');
        }

        $token   = $_SESSION['token'];
        $fStatus = trim($_GET['status'] ?? '');
        $fProdi  = trim($_GET['id_prodi'] ?? '');
        $fTahun  = trim($_GET['tahun'] ?? '');
        $fQ      = trim($_GET['q'] ?? '');

        $report = $this->getReportData($token, $fStatus, $fProdi, $fTahun, $fQ);
        $mahasiswa = $report['mahasiswa'];

        require_once dirname(__DIR__, 2) . '/resources/views/laporan/cetak-pdf.php';
        exit;
    }
}
