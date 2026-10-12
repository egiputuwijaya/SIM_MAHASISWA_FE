<?php
require_once 'Controller.php';
require_once __DIR__ . '/../Helpers/ApiClient.php';

class PenggunaController extends Controller {
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
        
        $selectedRole = $_GET['id_role'] ?? '';
        $params = ['limit' => 50];
        if (!empty($selectedRole)) {
            $params['id_role'] = $selectedRole;
        }

        // Ambil data pengguna dari API
        $response = ApiClient::get('/pengguna', $params, $_SESSION['token']);
        $this->handleApiAuthError($response);

        $pengguna = [];
        $error = null;
        
        if (isset($response['success']) && $response['success']) {
            $pengguna = $response['data'] ?? [];
        } else if (isset($response['error'])) {
            $error = $response['error']['message'] ?? 'Gagal memuat data pengguna.';
        }

        // Ambil daftar roles dari API untuk dropdown Filter Role
        $rolesRes = ApiClient::get('/roles', ['limit' => 50], $_SESSION['token']);
        $this->handleApiAuthError($rolesRes);
        $roles = $rolesRes['data'] ?? [];

        $this->view('pengguna/index', [
            'title'        => 'Manajemen Pengguna',
            'pengguna'     => $pengguna,
            'roles'        => $roles,
            'selectedRole' => $selectedRole,
            'error'        => $error
        ]);
    }

    public function create() {
        $this->checkAuth();

        $rolesRes = ApiClient::get('/roles', ['limit' => 50], $_SESSION['token']);
        $this->handleApiAuthError($rolesRes);
        $roles = $rolesRes['data'] ?? [];

        $fakultasRes = ApiClient::get('/fakultas', ['limit' => 50], $_SESSION['token']);
        $fakultas = $fakultasRes['data'] ?? [];

        $prodiRes = ApiClient::get('/program-studi', ['limit' => 50], $_SESSION['token']);
        $prodi = $prodiRes['data'] ?? [];

        $this->view('pengguna/create', [
            'title'    => 'Tambah Pengguna',
            'roles'    => $roles,
            'fakultas' => $fakultas,
            'prodi'    => $prodi
        ]);
    }

    public function store() {
        $this->checkAuth();
        
        $payload = [
            'username'    => trim($_POST['username'] ?? ''),
            'password'    => trim($_POST['password'] ?? ''),
            'namaLengkap' => trim($_POST['nama_lengkap'] ?? $_POST['namaLengkap'] ?? ''),
            'idRole'      => (int)($_POST['id_role'] ?? $_POST['idRole'] ?? 1),
            'statusAktif' => $_POST['status_aktif'] ?? 'Aktif'
        ];

        if (!empty($_POST['email'])) {
            $payload['email'] = trim($_POST['email']);
        }
        if (!empty($_POST['no_hp'])) {
            $payload['noHp'] = trim($_POST['no_hp']);
        }
        if (!empty($_POST['id_fakultas'])) {
            $payload['idFakultas'] = (int)$_POST['id_fakultas'];
        }
        if (!empty($_POST['id_program_studi'])) {
            $payload['idProgramStudi'] = (int)$_POST['id_program_studi'];
        }

        $res = ApiClient::post('/pengguna', $payload, $_SESSION['token']);
        $this->handleApiAuthError($res);
        
        if (isset($res['success']) && $res['success']) {
            $_SESSION['flash_success'] = "Pengguna baru berhasil ditambahkan!";
            return $this->redirect('/pengguna');
        } else {
            $errMsg = $res['error']['message'] ?? $res['message'] ?? 'Gagal menambahkan pengguna.';
            if (!empty($res['error']['details']) && is_array($res['error']['details'])) {
                $detailMsgs = array_map(function($d) {
                    $field = !empty($d['path']) ? ucfirst($d['path']) . ': ' : '';
                    return $field . ($d['message'] ?? '');
                }, $res['error']['details']);
                $errMsg .= ' (' . implode(', ', $detailMsgs) . ')';
            }
            $_SESSION['flash_error'] = $errMsg;
            return $this->redirect('/pengguna/create');
        }
    }

    public function edit($id) {
        $this->checkAuth();

        $userRes = ApiClient::get("/pengguna/{$id}", [], $_SESSION['token']);
        $this->handleApiAuthError($userRes);
        $user = $userRes['data'] ?? [];

        if (empty($user)) {
            $_SESSION['flash_error'] = "Data pengguna dengan ID {$id} tidak ditemukan.";
            return $this->redirect('/pengguna');
        }

        $rolesRes = ApiClient::get('/roles', ['limit' => 50], $_SESSION['token']);
        $this->handleApiAuthError($rolesRes);
        $roles = $rolesRes['data'] ?? [];

        $fakultasRes = ApiClient::get('/fakultas', ['limit' => 50], $_SESSION['token']);
        $fakultas = $fakultasRes['data'] ?? [];

        $prodiRes = ApiClient::get('/program-studi', ['limit' => 50], $_SESSION['token']);
        $prodi = $prodiRes['data'] ?? [];

        $this->view('pengguna/edit', [
            'title'    => 'Edit Pengguna',
            'user'     => $user,
            'roles'    => $roles,
            'fakultas' => $fakultas,
            'prodi'    => $prodi
        ]);
    }

    public function update($id) {
        $this->checkAuth();
        
        $payload = [
            'username'    => trim($_POST['username'] ?? ''),
            'namaLengkap' => trim($_POST['nama_lengkap'] ?? $_POST['namaLengkap'] ?? ''),
            'idRole'      => (int)($_POST['id_role'] ?? $_POST['idRole'] ?? 1),
            'statusAktif' => $_POST['status_aktif'] ?? 'Aktif'
        ];

        if (!empty($_POST['email'])) {
            $payload['email'] = trim($_POST['email']);
        } else {
            $payload['email'] = null;
        }

        if (!empty($_POST['no_hp'])) {
            $payload['noHp'] = trim($_POST['no_hp']);
        } else {
            $payload['noHp'] = null;
        }

        if (!empty($_POST['id_fakultas'])) {
            $payload['idFakultas'] = (int)$_POST['id_fakultas'];
        } else {
            $payload['idFakultas'] = null;
        }

        if (!empty($_POST['id_program_studi'])) {
            $payload['idProgramStudi'] = (int)$_POST['id_program_studi'];
        } else {
            $payload['idProgramStudi'] = null;
        }

        if (!empty($_POST['password'])) {
            $payload['password'] = trim($_POST['password']);
        }

        $res = ApiClient::patch("/pengguna/{$id}", $payload, $_SESSION['token']);
        $this->handleApiAuthError($res);
        
        if (isset($res['success']) && $res['success']) {
            $_SESSION['flash_success'] = "Data pengguna berhasil diperbarui!";
            return $this->redirect('/pengguna');
        } else {
            $errMsg = $res['error']['message'] ?? $res['message'] ?? 'Gagal memperbarui pengguna.';
            if (!empty($res['error']['details']) && is_array($res['error']['details'])) {
                $detailMsgs = array_map(function($d) {
                    $field = !empty($d['path']) ? ucfirst($d['path']) . ': ' : '';
                    return $field . ($d['message'] ?? '');
                }, $res['error']['details']);
                $errMsg .= ' (' . implode(', ', $detailMsgs) . ')';
            }
            $_SESSION['flash_error'] = $errMsg;
            return $this->redirect("/pengguna/edit/{$id}");
        }
    }

    public function destroy($id) {
        $this->checkAuth();
        
        // Cegah menghapus akun sendiri yang sedang aktif digunakan
        $currentUser = $_SESSION['user'] ?? [];
        $currentUserId = $currentUser['id'] ?? null;
        if ($currentUserId && (int)$id === (int)$currentUserId) {
            $_SESSION['flash_error'] = "Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.";
            return $this->redirect('/pengguna');
        }

        $res = ApiClient::delete("/pengguna/{$id}", $_SESSION['token']);
        $this->handleApiAuthError($res);
        
        if (isset($res['success']) && $res['success']) {
            $_SESSION['flash_success'] = "Pengguna berhasil dihapus!";
        } else {
            $errMsg = $res['error']['message'] ?? $res['message'] ?? 'Gagal menghapus pengguna.';
            if (!empty($res['error']['details']) && is_array($res['error']['details'])) {
                $detailMsgs = array_map(function($d) {
                    return $d['message'] ?? '';
                }, $res['error']['details']);
                $errMsg .= ' (' . implode(', ', $detailMsgs) . ')';
            }
            $_SESSION['flash_error'] = $errMsg;
        }

        return $this->redirect('/pengguna');
    }
}
