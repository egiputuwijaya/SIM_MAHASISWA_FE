<?php
require_once 'Controller.php';
require_once __DIR__ . '/../Helpers/ApiClient.php';

class ProgramStudiController extends Controller {
    private function checkAuth() {
        if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
            $this->redirect('/login');
            exit;
        }
    }

    private function handleApiAuthError($response) {
        if (isset($response['httpCode']) && $response['httpCode'] === 401) {
            unset($_SESSION['token'], $_SESSION['user']);
            $_SESSION['flash_error'] = "Sesi login telah berakhir. Silakan login kembali.";
            $this->redirect('/login');
            exit;
        }
    }

    public function index() {
        $this->checkAuth();

        $filterFakultas = (int)($_GET['fakultas'] ?? 0);

        // Ambil daftar fakultas untuk dropdown filter
        $fakultasRes = ApiClient::get('/fakultas', ['limit' => 100], $_SESSION['token']);
        $this->handleApiAuthError($fakultasRes);
        $listFakultas = $fakultasRes['data'] ?? [];

        // Parameter query prodi
        $params = ['limit' => 100];
        if ($filterFakultas > 0) {
            $params['id_fakultas'] = $filterFakultas;
        }

        // Ambil daftar prodi dari API
        $prodiRes = ApiClient::get('/program-studi', $params, $_SESSION['token']);
        $this->handleApiAuthError($prodiRes);
        $prodiData = $prodiRes['data'] ?? [];

        $this->view('program_studi/index', [
            'title'          => 'Kelola Program Studi',
            'listFakultas'   => $listFakultas,
            'filterFakultas' => $filterFakultas,
            'prodiData'      => $prodiData
        ]);
    }

    public function create() {
        $this->checkAuth();

        // Ambil daftar fakultas untuk dropdown pilihan
        $fakultasRes = ApiClient::get('/fakultas', ['limit' => 100], $_SESSION['token']);
        $this->handleApiAuthError($fakultasRes);
        $listFakultas = $fakultasRes['data'] ?? [];

        if (empty($listFakultas)) {
            $_SESSION['flash_error'] = 'Anda harus memiliki minimal 1 Fakultas sebelum menambahkan Program Studi.';
            return $this->redirect('/fakultas/create');
        }

        $this->view('program_studi/create', [
            'title'        => 'Tambah Program Studi',
            'listFakultas' => $listFakultas
        ]);
    }

    public function store() {
        $this->checkAuth();

        $idFakultas = (int)($_POST['id_fakultas'] ?? 0);
        $kode       = strtoupper(trim($_POST['kode_program_studi'] ?? ''));
        $nama       = trim($_POST['nama_program_studi'] ?? '');
        $jenjang    = trim($_POST['jenjang'] ?? 'S1');
        $status     = trim($_POST['status_aktif'] ?? 'Aktif');

        if (empty($idFakultas) || empty($kode) || empty($nama)) {
            $_SESSION['flash_error'] = 'Semua kolom bertanda bintang (*) wajib diisi.';
            return $this->redirect('/program-studi/create');
        }

        $payload = [
            'idFakultas'       => $idFakultas,
            'kodeProgramStudi' => $kode,
            'namaProgramStudi' => $nama,
            'jenjang'          => $jenjang,
            'statusAktif'      => $status
        ];

        $res = ApiClient::post('/program-studi', $payload, $_SESSION['token']);
        $this->handleApiAuthError($res);

        if (isset($res['success']) && $res['success']) {
            $_SESSION['flash_success'] = 'Program Studi berhasil ditambahkan.';
            return $this->redirect('/program-studi');
        } else {
            $errMsg = $res['error']['message'] ?? $res['message'] ?? 'Gagal menambahkan Program Studi.';
            if (isset($res['error']['code']) && $res['error']['code'] === 'CONFLICT') {
                $errMsg = 'Gagal: Kode Program Studi sudah digunakan pada fakultas ini.';
            } elseif (!empty($res['error']['details']) && is_array($res['error']['details'])) {
                $detailMsgs = array_map(function($d) {
                    $field = !empty($d['path']) ? ucfirst($d['path']) . ': ' : '';
                    return $field . ($d['message'] ?? '');
                }, $res['error']['details']);
                $errMsg .= ' (' . implode(', ', $detailMsgs) . ')';
            }
            $_SESSION['flash_error'] = $errMsg;
            return $this->redirect('/program-studi/create');
        }
    }

    public function edit($id) {
        $this->checkAuth();

        // Ambil data prodi
        $prodiRes = ApiClient::get("/program-studi/{$id}", [], $_SESSION['token']);
        $this->handleApiAuthError($prodiRes);
        $prodi = $prodiRes['data'] ?? null;

        if (!$prodi) {
            $_SESSION['flash_error'] = 'Data Program Studi tidak ditemukan.';
            return $this->redirect('/program-studi');
        }

        // Ambil daftar fakultas
        $fakultasRes = ApiClient::get('/fakultas', ['limit' => 100], $_SESSION['token']);
        $this->handleApiAuthError($fakultasRes);
        $listFakultas = $fakultasRes['data'] ?? [];

        $this->view('program_studi/edit', [
            'title'        => 'Edit Program Studi',
            'prodi'        => $prodi,
            'listFakultas' => $listFakultas
        ]);
    }

    public function update($id) {
        $this->checkAuth();

        $idFakultas = (int)($_POST['id_fakultas'] ?? 0);
        $kode       = strtoupper(trim($_POST['kode_program_studi'] ?? ''));
        $nama       = trim($_POST['nama_program_studi'] ?? '');
        $jenjang    = trim($_POST['jenjang'] ?? 'S1');
        $status     = trim($_POST['status_aktif'] ?? 'Aktif');

        if (empty($idFakultas) || empty($kode) || empty($nama)) {
            $_SESSION['flash_error'] = 'Semua kolom bertanda bintang (*) wajib diisi.';
            return $this->redirect("/program-studi/edit/{$id}");
        }

        $payload = [
            'idFakultas'       => $idFakultas,
            'kodeProgramStudi' => $kode,
            'namaProgramStudi' => $nama,
            'jenjang'          => $jenjang,
            'statusAktif'      => $status
        ];

        $res = ApiClient::patch("/program-studi/{$id}", $payload, $_SESSION['token']);
        $this->handleApiAuthError($res);

        if (isset($res['success']) && $res['success']) {
            $_SESSION['flash_success'] = 'Data Program Studi berhasil diperbarui.';
            return $this->redirect('/program-studi');
        } else {
            $errMsg = $res['error']['message'] ?? $res['message'] ?? 'Gagal memperbarui Program Studi.';
            if (isset($res['error']['code']) && $res['error']['code'] === 'CONFLICT') {
                $errMsg = 'Gagal: Kode Program Studi sudah digunakan.';
            } elseif (!empty($res['error']['details']) && is_array($res['error']['details'])) {
                $detailMsgs = array_map(function($d) {
                    $field = !empty($d['path']) ? ucfirst($d['path']) . ': ' : '';
                    return $field . ($d['message'] ?? '');
                }, $res['error']['details']);
                $errMsg .= ' (' . implode(', ', $detailMsgs) . ')';
            }
            $_SESSION['flash_error'] = $errMsg;
            return $this->redirect("/program-studi/edit/{$id}");
        }
    }

    public function destroy($id) {
        $this->checkAuth();

        $res = ApiClient::delete("/program-studi/{$id}", $_SESSION['token']);
        $this->handleApiAuthError($res);

        if (isset($res['success']) && $res['success']) {
            $_SESSION['flash_success'] = 'Program Studi berhasil dihapus.';
        } else {
            $errMsg = $res['error']['message'] ?? $res['message'] ?? 'Gagal menghapus Program Studi.';
            if (isset($res['error']['code']) && $res['error']['code'] === 'CONFLICT') {
                $errMsg = 'Tidak dapat menghapus program studi yang masih memiliki relasi data mahasiswa atau pengguna.';
            }
            $_SESSION['flash_error'] = $errMsg;
        }

        return $this->redirect('/program-studi');
    }
}
