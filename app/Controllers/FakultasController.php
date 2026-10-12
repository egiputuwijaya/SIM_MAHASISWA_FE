<?php
require_once 'Controller.php';
require_once __DIR__ . '/../Helpers/ApiClient.php';

class FakultasController extends Controller {
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

        // Ambil daftar fakultas dari API
        $fakultasRes = ApiClient::get('/fakultas', ['limit' => 100], $_SESSION['token']);
        $this->handleApiAuthError($fakultasRes);
        $fakultas = $fakultasRes['data'] ?? [];

        // Ambil daftar prodi dari API untuk menghitung relasi jumlah prodi per fakultas
        $prodiRes = ApiClient::get('/program-studi', ['limit' => 200], $_SESSION['token']);
        $prodi = $prodiRes['data'] ?? [];

        $prodiCounts = [];
        foreach ($prodi as $p) {
            $fId = $p['idFakultas'] ?? $p['id_fakultas'] ?? null;
            if ($fId) {
                $prodiCounts[$fId] = ($prodiCounts[$fId] ?? 0) + 1;
            }
        }

        // Sematkan jumlah_prodi ke setiap baris fakultas
        foreach ($fakultas as &$f) {
            $fId = $f['id'] ?? $f['id_fakultas'] ?? 0;
            $f['jumlah_prodi'] = $prodiCounts[$fId] ?? 0;
        }
        unset($f);

        $this->view('fakultas/index', [
            'title'        => 'Kelola Fakultas',
            'fakultasData' => $fakultas
        ]);
    }

    public function create() {
        $this->checkAuth();

        // Ambil data profil universitas
        $univRes = ApiClient::get('/universitas', ['limit' => 1], $_SESSION['token']);
        $univList = $univRes['data'] ?? [];
        $univ = !empty($univList[0]) ? $univList[0] : [
            'id' => 1,
            'namaUniversitas' => 'Universitas Muhammadiyah Bengkulu'
        ];

        $this->view('fakultas/create', [
            'title' => 'Tambah Fakultas',
            'univ'  => $univ
        ]);
    }

    public function store() {
        $this->checkAuth();

        $idUniv = (int)($_POST['id_universitas'] ?? 1);
        $kode   = strtoupper(trim($_POST['kode_fakultas'] ?? ''));
        $nama   = trim($_POST['nama_fakultas'] ?? '');

        if (empty($kode) || empty($nama)) {
            $_SESSION['flash_error'] = 'Semua kolom wajib diisi.';
            return $this->redirect('/fakultas/create');
        }

        $payload = [
            'idUniversitas' => $idUniv,
            'kodeFakultas'  => $kode,
            'namaFakultas'  => $nama
        ];

        $res = ApiClient::post('/fakultas', $payload, $_SESSION['token']);
        $this->handleApiAuthError($res);

        if (isset($res['success']) && $res['success']) {
            $_SESSION['flash_success'] = 'Fakultas berhasil ditambahkan.';
            return $this->redirect('/fakultas');
        } else {
            $errMsg = $res['error']['message'] ?? $res['message'] ?? 'Gagal menambahkan fakultas.';
            if (isset($res['error']['code']) && $res['error']['code'] === 'CONFLICT') {
                $errMsg = 'Gagal: Kode Fakultas sudah digunakan.';
            } elseif (!empty($res['error']['details']) && is_array($res['error']['details'])) {
                $detailMsgs = array_map(function($d) {
                    $field = !empty($d['path']) ? ucfirst($d['path']) . ': ' : '';
                    return $field . ($d['message'] ?? '');
                }, $res['error']['details']);
                $errMsg .= ' (' . implode(', ', $detailMsgs) . ')';
            }
            $_SESSION['flash_error'] = $errMsg;
            return $this->redirect('/fakultas/create');
        }
    }

    public function edit($id) {
        $this->checkAuth();

        $fakultasRes = ApiClient::get("/fakultas/{$id}", [], $_SESSION['token']);
        $this->handleApiAuthError($fakultasRes);
        $fakultas = $fakultasRes['data'] ?? null;

        if (!$fakultas) {
            $_SESSION['flash_error'] = 'Data fakultas tidak ditemukan.';
            return $this->redirect('/fakultas');
        }

        $this->view('fakultas/edit', [
            'title'    => 'Edit Fakultas',
            'fakultas' => $fakultas
        ]);
    }

    public function update($id) {
        $this->checkAuth();

        $kode = strtoupper(trim($_POST['kode_fakultas'] ?? ''));
        $nama = trim($_POST['nama_fakultas'] ?? '');

        if (empty($kode) || empty($nama)) {
            $_SESSION['flash_error'] = 'Semua kolom wajib diisi.';
            return $this->redirect("/fakultas/edit/{$id}");
        }

        $payload = [
            'kodeFakultas' => $kode,
            'namaFakultas' => $nama
        ];

        $res = ApiClient::patch("/fakultas/{$id}", $payload, $_SESSION['token']);
        $this->handleApiAuthError($res);

        if (isset($res['success']) && $res['success']) {
            $_SESSION['flash_success'] = 'Data Fakultas berhasil diperbarui.';
            return $this->redirect('/fakultas');
        } else {
            $errMsg = $res['error']['message'] ?? $res['message'] ?? 'Gagal memperbarui fakultas.';
            if (isset($res['error']['code']) && $res['error']['code'] === 'CONFLICT') {
                $errMsg = 'Gagal: Kode Fakultas sudah digunakan.';
            } elseif (!empty($res['error']['details']) && is_array($res['error']['details'])) {
                $detailMsgs = array_map(function($d) {
                    $field = !empty($d['path']) ? ucfirst($d['path']) . ': ' : '';
                    return $field . ($d['message'] ?? '');
                }, $res['error']['details']);
                $errMsg .= ' (' . implode(', ', $detailMsgs) . ')';
            }
            $_SESSION['flash_error'] = $errMsg;
            return $this->redirect("/fakultas/edit/{$id}");
        }
    }

    public function destroy($id) {
        $this->checkAuth();

        // Validasi: tidak bisa menghapus Fakultas jika masih ada program studi di dalamnya
        $prodiRes = ApiClient::get('/program-studi', ['id_fakultas' => $id, 'limit' => 10], $_SESSION['token']);
        $prodiList = $prodiRes['data'] ?? [];
        if (!empty($prodiList)) {
            $_SESSION['flash_error'] = 'Tidak dapat menghapus fakultas yang masih memiliki Program Studi. Hapus atau pindahkan Prodi terlebih dahulu.';
            return $this->redirect('/fakultas');
        }

        $res = ApiClient::delete("/fakultas/{$id}", $_SESSION['token']);
        $this->handleApiAuthError($res);

        if (isset($res['success']) && $res['success']) {
            $_SESSION['flash_success'] = 'Fakultas berhasil dihapus.';
        } else {
            $errMsg = $res['error']['message'] ?? $res['message'] ?? 'Gagal menghapus fakultas.';
            if (isset($res['error']['code']) && $res['error']['code'] === 'CONFLICT') {
                $errMsg = 'Tidak dapat menghapus fakultas yang masih memiliki Program Studi atau relasi pengguna.';
            }
            $_SESSION['flash_error'] = $errMsg;
        }

        return $this->redirect('/fakultas');
    }
}
