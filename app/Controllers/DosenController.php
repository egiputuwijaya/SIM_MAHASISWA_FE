<?php
require_once 'Controller.php';

class DosenController extends Controller {

    private function getDosenData($token, $fProdi, $fStatus, $fQ) {
        $params = [
            'id_prodi'     => $fProdi,
            'status_aktif' => $fStatus,
            'q'            => $fQ,
        ];

        $response = ApiClient::get('/reports/dosen', $params, $token);

        if (!empty($response['data'])) {
            return [
                'dosen'      => $response['data']['dosen'] ?? [],
                'total'      => $response['data']['total'] ?? 0,
                'totalAktif' => $response['data']['totalAktif'] ?? 0,
                'prodiList'  => $response['data']['prodiList'] ?? [],
            ];
        }

        // Fallback jika API Vercel belum dideploy
        $userProfile = $_SESSION['user'] ?? [];
        $userFak = $userProfile['idFakultas'] ?? $userProfile['id_fakultas'] ?? null;
        $userProdi = $userProfile['idProgramStudi'] ?? $userProfile['id_program_studi'] ?? null;
        $userRoleCode = is_array($userProfile['role'] ?? null) 
            ? strtoupper($userProfile['role']['kodeRole'] ?? $userProfile['role']['namaRole'] ?? '') 
            : strtoupper((string)($userProfile['role'] ?? ''));
        $isDekanat = in_array($userRoleCode, ['DEKANAT', 'ADMIN_DEKANAT']) && !empty($userFak);
        $isProdi = in_array($userRoleCode, ['KAPRODI', 'ADMIN_PRODI']) && !empty($userProdi);

        // Ambil daftar pengguna
        $userRes = ApiClient::get('/pengguna', ['limit' => 200], $token);
        $rawUsers = $userRes['data'] ?? [];

        // Ambil prodi
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

        $dosenList = [];
        foreach ($rawUsers as $u) {
            $roleCode = is_array($u['role'] ?? null) 
                ? ($u['role']['kodeRole'] ?? '') 
                : ($u['role'] ?? '');
            
            // Hanya ambil role Dosen
            if (strtoupper($roleCode) !== 'DOSEN') {
                continue;
            }

            // Kaprodi hanya melihat dosen pada prodinya, Dekanat pada fakultasnya
            if ($isProdi) {
                $uProdi = $u['idProgramStudi'] ?? $u['programStudi']['id'] ?? '';
                if ((string)$uProdi !== (string)$userProdi) {
                    continue;
                }
            } elseif ($isDekanat) {
                $uFak = $u['idFakultas'] ?? $u['fakultas']['id'] ?? '';
                if ((string)$uFak !== (string)$userFak) {
                    continue;
                }
            }

            // Filter prodi
            if (!empty($fProdi) && (string)($u['idProgramStudi'] ?? '') !== (string)$fProdi) {
                continue;
            }

            // Filter status aktif
            $st = $u['statusAktif'] ?? 'Aktif';
            if (!empty($fStatus) && $fStatus !== 'Semua' && $st !== $fStatus) {
                continue;
            }

            // Filter pencarian
            if (!empty($fQ)) {
                $haystack = strtolower(($u['namaLengkap'] ?? '') . ' ' . ($u['username'] ?? '') . ' ' . ($u['email'] ?? ''));
                if (strpos($haystack, strtolower($fQ)) === false) {
                    continue;
                }
            }

            $dosenList[] = [
                'id'               => (string)($u['id'] ?? ''),
                'username'         => $u['username'] ?? '',
                'namaLengkap'      => $u['namaLengkap'] ?? '',
                'email'            => $u['email'] ?? '-',
                'noHp'             => $u['noHp'] ?? '-',
                'statusAktif'      => $st,
                'namaFakultas'     => $u['fakultas']['namaFakultas'] ?? 'Fakultas Teknik Informasi',
                'namaProgramStudi' => $u['programStudi']['namaProgramStudi'] ?? 'Teknik Informatika',
                'jenjang'          => $u['programStudi']['jenjang'] ?? 'S1',
            ];
        }

        $totalAktif = 0;
        foreach ($dosenList as $d) {
            if ($d['statusAktif'] === 'Aktif') $totalAktif++;
        }

        return [
            'dosen'      => $dosenList,
            'total'      => count($dosenList),
            'totalAktif' => $totalAktif,
            'prodiList'  => $prodiList,
        ];
    }

    public function index() {
        if (!isset($_SESSION['token'])) {
            return $this->redirect('/login');
        }

        $token   = $_SESSION['token'];
        $fProdi  = trim($_GET['id_prodi'] ?? '');
        $fStatus = trim($_GET['status_aktif'] ?? '');
        $fQ      = trim($_GET['q'] ?? '');

        $data = $this->getDosenData($token, $fProdi, $fStatus, $fQ);

        $userProfile = $_SESSION['user'] ?? [];
        $userRoleCode = is_array($userProfile['role'] ?? null) 
            ? strtoupper($userProfile['role']['kodeRole'] ?? $userProfile['role']['namaRole'] ?? '') 
            : strtoupper((string)($userProfile['role'] ?? ''));
        $title = in_array($userRoleCode, ['KAPRODI', 'ADMIN_PRODI']) ? 'Daftar Dosen Program Studi' : 'Daftar Dosen Fakultas';

        $this->view('dosen/index', [
            'dosen'      => $data['dosen'],
            'total'      => $data['total'],
            'totalAktif' => $data['totalAktif'],
            'prodiList'  => $data['prodiList'],
            'fProdi'     => $fProdi,
            'fStatus'    => $fStatus,
            'fQ'         => $fQ,
            'title'      => $title,
        ]);
    }

    public function exportCsv() {
        if (!isset($_SESSION['token'])) {
            return $this->redirect('/login');
        }

        $token   = $_SESSION['token'];
        $fProdi  = trim($_GET['id_prodi'] ?? '');
        $fStatus = trim($_GET['status_aktif'] ?? '');
        $fQ      = trim($_GET['q'] ?? '');

        $data = $this->getDosenData($token, $fProdi, $fStatus, $fQ);
        $dosen = $data['dosen'];

        $userProfile = $_SESSION['user'] ?? [];
        $userRoleCode = is_array($userProfile['role'] ?? null) 
            ? strtoupper($userProfile['role']['kodeRole'] ?? $userProfile['role']['namaRole'] ?? '') 
            : strtoupper((string)($userProfile['role'] ?? ''));
        $filePrefix = in_array($userRoleCode, ['KAPRODI', 'ADMIN_PRODI']) ? 'Daftar_Dosen_Prodi_UMB_' : 'Daftar_Dosen_FTI_UMB_';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filePrefix . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM

        fputcsv($output, ['No', 'NIP / Username', 'Nama Lengkap Dosen', 'Program Studi', 'Fakultas', 'Email', 'No. Handphone', 'Status']);

        $no = 1;
        foreach ($dosen as $d) {
            fputcsv($output, [
                $no++,
                $d['username'] ?? '',
                $d['namaLengkap'] ?? '',
                $d['namaProgramStudi'] ?? '-',
                $d['namaFakultas'] ?? '-',
                $d['email'] ?? '-',
                $d['noHp'] ?? '-',
                $d['statusAktif'] ?? 'Aktif'
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
        $fProdi  = trim($_GET['id_prodi'] ?? '');
        $fStatus = trim($_GET['status_aktif'] ?? '');
        $fQ      = trim($_GET['q'] ?? '');

        $data = $this->getDosenData($token, $fProdi, $fStatus, $fQ);
        $dosen = $data['dosen'];

        require_once dirname(__DIR__, 2) . '/resources/views/dosen/cetak-pdf.php';
        exit;
    }
}
