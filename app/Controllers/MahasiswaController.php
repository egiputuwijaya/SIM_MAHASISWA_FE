<?php
require_once 'Controller.php';
require_once __DIR__ . '/../Helpers/ApiClient.php';

class MahasiswaController extends Controller {
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

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 15;
        $q      = trim((string)($_GET['q'] ?? ''));
        $fProdi = (int)($_GET['prodi'] ?? 0);
        $fStatus = trim((string)($_GET['status'] ?? ''));

        // Ambil daftar prodi untuk filter dropdown
        $prodiRes = ApiClient::get('/program-studi', ['limit' => 100], $_SESSION['token']);
        $this->handleApiAuthError($prodiRes);
        $prodiList = $prodiRes['data'] ?? [];

        // Parameter query ke API
        $params = [
            'page'  => $page,
            'limit' => $limit
        ];
        if ($q !== '') {
            $params['q'] = $q;
        }
        if ($fProdi > 0) {
            $params['id_program_studi'] = $fProdi;
        }
        if ($fStatus !== '' && in_array($fStatus, ['Aktif', 'Cuti', 'Lulus', 'Mengundurkan Diri', 'Drop Out', 'Tidak Aktif'], true)) {
            $params['status_mahasiswa'] = $fStatus;
        }

        $res = ApiClient::get('/mahasiswa', $params, $_SESSION['token']);
        $this->handleApiAuthError($res);

        $mahasiswaList = $res['data'] ?? [];
        $meta = $res['meta'] ?? [];
        $totalRows = (int)($meta['total'] ?? count($mahasiswaList));
        $totalPages = (int)($meta['totalPages'] ?? max(1, (int)ceil($totalRows / $limit)));

        $this->view('mahasiswa/index', [
            'title'         => 'Data Mahasiswa',
            'mahasiswaList' => $mahasiswaList,
            'prodiList'     => $prodiList,
            'totalRows'     => $totalRows,
            'totalPages'    => $totalPages,
            'page'          => $page,
            'q'             => $q,
            'fProdi'        => $fProdi,
            'fStatus'       => $fStatus
        ]);
    }

    public function detail($id) {
        $this->checkAuth();

        $res = ApiClient::get("/mahasiswa/{$id}", [], $_SESSION['token']);
        $this->handleApiAuthError($res);
        $mhs = $res['data'] ?? null;

        if (!$mhs) {
            $_SESSION['flash_error'] = 'Data mahasiswa tidak ditemukan.';
            return $this->redirect('/mahasiswa');
        }

        $this->view('mahasiswa/detail', [
            'title' => 'Detail Mahasiswa',
            'mhs'   => $mhs
        ]);
    }

    public function create() {
        $this->checkAuth();

        // Ambil daftar prodi
        $prodiRes = ApiClient::get('/program-studi', ['limit' => 100], $_SESSION['token']);
        $this->handleApiAuthError($prodiRes);
        $prodiList = $prodiRes['data'] ?? [];

        $old = $_SESSION['old_mahasiswa'] ?? [];
        unset($_SESSION['old_mahasiswa']);

        $this->view('mahasiswa/create', [
            'title'     => 'Tambah Mahasiswa',
            'prodiList' => $prodiList,
            'old'       => $old
        ]);
    }

    public function store() {
        $this->checkAuth();

        $npm            = trim($_POST['npm'] ?? '');
        $namaMahasiswa  = trim($_POST['nama_mahasiswa'] ?? '');
        $jenisKelamin   = trim($_POST['jenis_kelamin'] ?? '');
        $status         = trim($_POST['status_mahasiswa'] ?? 'Aktif');
        $tempatLahir    = trim($_POST['tempat_lahir'] ?? '');
        $tanggalLahir   = trim($_POST['tanggal_lahir'] ?? '');
        $tanggalMasuk   = trim($_POST['tanggal_masuk'] ?? '');
        $idProgramStudi = (int)($_POST['id_program_studi'] ?? 0);
        $alamat         = trim($_POST['alamat'] ?? '');
        $buatAkun       = !empty($_POST['buat_akun']) ? 1 : 0;

        $old = [
            'npm'              => $npm,
            'nama_mahasiswa'   => $namaMahasiswa,
            'jenis_kelamin'    => $jenisKelamin,
            'status_mahasiswa' => $status,
            'tempat_lahir'     => $tempatLahir,
            'tanggal_lahir'    => $tanggalLahir,
            'tanggal_masuk'    => $tanggalMasuk,
            'id_program_studi' => $idProgramStudi,
            'alamat'           => $alamat,
            'buat_akun'        => $buatAkun
        ];

        // Validasi
        $errors = [];
        if ($npm === '') $errors[] = 'NPM wajib diisi.';
        if ($namaMahasiswa === '') $errors[] = 'Nama lengkap mahasiswa wajib diisi.';
        if (!in_array($jenisKelamin, ['L', 'P'], true)) $errors[] = 'Jenis kelamin wajib dipilih.';
        if ($tanggalMasuk === '') $errors[] = 'Tanggal masuk wajib diisi.';
        if ($idProgramStudi <= 0) $errors[] = 'Program studi wajib dipilih.';

        if (!empty($errors)) {
            $_SESSION['old_mahasiswa'] = $old;
            $_SESSION['flash_error'] = implode(' ', $errors);
            return $this->redirect('/mahasiswa/create');
        }

        // Jika buat akun dicentang, buat akun pengguna terlebih dahulu
        $idPengguna = null;
        $defaultPassword = null;
        if ($buatAkun === 1) {
            // Ambil role MAHASISWA
            $rolesRes = ApiClient::get('/roles', ['limit' => 50], $_SESSION['token']);
            $this->handleApiAuthError($rolesRes);
            $roleMhsId = 4; // default ID role MAHASISWA
            if (!empty($rolesRes['data'])) {
                foreach ($rolesRes['data'] as $r) {
                    if (($r['kodeRole'] ?? '') === 'MAHASISWA') {
                        $roleMhsId = (int)$r['id'];
                        break;
                    }
                }
            }

            // API mensyaratkan password: min 8 karakter, ada huruf besar [A-Z], dan angka [0-9].
            // Jika NPM sudah memenuhi syarat maka gunakan NPM, jika tidak buat format Mhs@[NPM].
            if (strlen($npm) >= 8 && preg_match('/[A-Z]/', $npm) && preg_match('/[0-9]/', $npm)) {
                $defaultPassword = $npm;
            } else {
                $cleanedNpm = preg_replace('/[^a-zA-Z0-9]/', '', $npm);
                $defaultPassword = 'Mhs@' . $cleanedNpm;
                if (strlen($defaultPassword) < 8) {
                    $defaultPassword .= '1234';
                }
                if (!preg_match('/[0-9]/', $defaultPassword)) {
                    $defaultPassword .= '1';
                }
            }

            $userPayload = [
                'username'    => $npm,
                'password'    => $defaultPassword,
                'namaLengkap' => $namaMahasiswa,
                'idRole'      => $roleMhsId,
                'statusAktif' => 'Aktif'
            ];

            $userRes = ApiClient::post('/pengguna', $userPayload, $_SESSION['token']);
            $this->handleApiAuthError($userRes);

            if (isset($userRes['success']) && $userRes['success']) {
                $idPengguna = $userRes['data']['id'] ?? null;
            } else {
                $errMsg = $userRes['error']['message'] ?? $userRes['message'] ?? 'Gagal membuat akun login pengguna.';
                if (isset($userRes['error']['code']) && $userRes['error']['code'] === 'CONFLICT') {
                    $errMsg = "Username/NPM '{$npm}' sudah digunakan pada akun pengguna.";
                } elseif (!empty($userRes['error']['details']) && is_array($userRes['error']['details'])) {
                    $detailMsgs = array_map(function($d) {
                        $field = !empty($d['path']) ? ucfirst($d['path']) . ': ' : '';
                        return $field . ($d['message'] ?? '');
                    }, $userRes['error']['details']);
                    $errMsg = 'Validasi akun login gagal: ' . implode(', ', $detailMsgs);
                }
                $_SESSION['old_mahasiswa'] = $old;
                $_SESSION['flash_error'] = $errMsg;
                return $this->redirect('/mahasiswa/create');
            }
        }

        // Kirim payload mahasiswa ke API
        $mhsPayload = [
            'npm'             => $npm,
            'namaMahasiswa'   => $namaMahasiswa,
            'jenisKelamin'    => $jenisKelamin,
            'statusMahasiswa' => $status,
            'tanggalMasuk'    => $tanggalMasuk,
            'idProgramStudi'  => $idProgramStudi
        ];
        if ($tempatLahir !== '') {
            $mhsPayload['tempatLahir'] = $tempatLahir;
        }
        if ($tanggalLahir !== '') {
            $mhsPayload['tanggalLahir'] = $tanggalLahir;
        }
        if ($alamat !== '') {
            $mhsPayload['alamat'] = $alamat;
        }
        if ($idPengguna !== null) {
            $mhsPayload['idPengguna'] = (int)$idPengguna;
        }

        $res = ApiClient::post('/mahasiswa', $mhsPayload, $_SESSION['token']);
        $this->handleApiAuthError($res);

        if (isset($res['success']) && $res['success']) {
            $msg = "Data mahasiswa '{$namaMahasiswa}' berhasil ditambahkan.";
            if ($buatAkun === 1 && $defaultPassword) {
                $msg .= " Akun login dibuat (username: {$npm}, password default: {$defaultPassword}).";
            }
            $_SESSION['flash_success'] = $msg;
            return $this->redirect('/mahasiswa');
        } else {
            // Jika mahasiswa gagal dibuat tapi akun pengguna terlanjur dibuat, rollback hapus pengguna
            if ($idPengguna !== null) {
                ApiClient::delete("/pengguna/{$idPengguna}", $_SESSION['token']);
            }

            $errMsg = $res['error']['message'] ?? $res['message'] ?? 'Gagal menambahkan data mahasiswa.';
            if (isset($res['error']['code']) && $res['error']['code'] === 'CONFLICT') {
                $errMsg = "NPM '{$npm}' sudah terdaftar. Gunakan NPM lain.";
            } elseif (!empty($res['error']['details']) && is_array($res['error']['details'])) {
                $detailMsgs = array_map(function($d) {
                    $field = !empty($d['path']) ? ucfirst($d['path']) . ': ' : '';
                    return $field . ($d['message'] ?? '');
                }, $res['error']['details']);
                $errMsg = 'Validasi data mahasiswa gagal: ' . implode(', ', $detailMsgs);
            }

            $_SESSION['old_mahasiswa'] = $old;
            $_SESSION['flash_error'] = $errMsg;
            return $this->redirect('/mahasiswa/create');
        }
    }

    public function edit($id) {
        $this->checkAuth();

        $res = ApiClient::get("/mahasiswa/{$id}", [], $_SESSION['token']);
        $this->handleApiAuthError($res);
        $mahasiswa = $res['data'] ?? null;

        if (!$mahasiswa) {
            $_SESSION['flash_error'] = 'Data mahasiswa tidak ditemukan.';
            return $this->redirect('/mahasiswa');
        }

        // Ambil daftar prodi
        $prodiRes = ApiClient::get('/program-studi', ['limit' => 100], $_SESSION['token']);
        $this->handleApiAuthError($prodiRes);
        $prodiList = $prodiRes['data'] ?? [];

        $old = $_SESSION['old_mahasiswa'] ?? null;
        unset($_SESSION['old_mahasiswa']);

        $this->view('mahasiswa/edit', [
            'title'     => 'Edit Mahasiswa',
            'mahasiswa' => $mahasiswa,
            'prodiList' => $prodiList,
            'old'       => $old
        ]);
    }

    public function update($id) {
        $this->checkAuth();

        $npm            = trim($_POST['npm'] ?? '');
        $namaMahasiswa  = trim($_POST['nama_mahasiswa'] ?? '');
        $jenisKelamin   = trim($_POST['jenis_kelamin'] ?? '');
        $status         = trim($_POST['status_mahasiswa'] ?? 'Aktif');
        $tempatLahir    = trim($_POST['tempat_lahir'] ?? '');
        $tanggalLahir   = trim($_POST['tanggal_lahir'] ?? '');
        $tanggalMasuk   = trim($_POST['tanggal_masuk'] ?? '');
        $idProgramStudi = (int)($_POST['id_program_studi'] ?? 0);
        $alamat         = trim($_POST['alamat'] ?? '');

        $old = [
            'npm'              => $npm,
            'nama_mahasiswa'   => $namaMahasiswa,
            'jenis_kelamin'    => $jenisKelamin,
            'status_mahasiswa' => $status,
            'tempat_lahir'     => $tempatLahir,
            'tanggal_lahir'    => $tanggalLahir,
            'tanggal_masuk'    => $tanggalMasuk,
            'id_program_studi' => $idProgramStudi,
            'alamat'           => $alamat
        ];

        $errors = [];
        if ($npm === '') $errors[] = 'NPM wajib diisi.';
        if ($namaMahasiswa === '') $errors[] = 'Nama lengkap mahasiswa wajib diisi.';
        if (!in_array($jenisKelamin, ['L', 'P'], true)) $errors[] = 'Jenis kelamin wajib dipilih.';
        if ($tanggalMasuk === '') $errors[] = 'Tanggal masuk wajib diisi.';
        if ($idProgramStudi <= 0) $errors[] = 'Program studi wajib dipilih.';

        if (!empty($errors)) {
            $_SESSION['old_mahasiswa'] = $old;
            $_SESSION['flash_error'] = implode(' ', $errors);
            return $this->redirect("/mahasiswa/edit/{$id}");
        }

        $payload = [
            'npm'             => $npm,
            'namaMahasiswa'   => $namaMahasiswa,
            'jenisKelamin'    => $jenisKelamin,
            'statusMahasiswa' => $status,
            'tanggalMasuk'    => $tanggalMasuk,
            'idProgramStudi'  => $idProgramStudi,
            'tempatLahir'     => $tempatLahir !== '' ? $tempatLahir : null,
            'tanggalLahir'    => $tanggalLahir !== '' ? $tanggalLahir : null,
            'alamat'          => $alamat !== '' ? $alamat : null,
        ];

        $res = ApiClient::patch("/mahasiswa/{$id}", $payload, $_SESSION['token']);
        $this->handleApiAuthError($res);

        if (isset($res['success']) && $res['success']) {
            $_SESSION['flash_success'] = "Data mahasiswa '{$namaMahasiswa}' berhasil diperbarui.";
            return $this->redirect('/mahasiswa');
        } else {
            $errMsg = $res['error']['message'] ?? $res['message'] ?? 'Gagal memperbarui data mahasiswa.';
            if (isset($res['error']['code']) && $res['error']['code'] === 'CONFLICT') {
                $errMsg = "NPM '{$npm}' sudah digunakan mahasiswa lain.";
            } elseif (!empty($res['error']['details']) && is_array($res['error']['details'])) {
                $detailMsgs = array_map(function($d) {
                    $field = !empty($d['path']) ? ucfirst($d['path']) . ': ' : '';
                    return $field . ($d['message'] ?? '');
                }, $res['error']['details']);
                $errMsg = 'Validasi gagal: ' . implode(', ', $detailMsgs);
            }

            $_SESSION['old_mahasiswa'] = $old;
            $_SESSION['flash_error'] = $errMsg;
            return $this->redirect("/mahasiswa/edit/{$id}");
        }
    }

    public function destroy($id) {
        $this->checkAuth();

        // Ambil data sebelum hapus untuk cek linked akun pengguna
        $getRes = ApiClient::get("/mahasiswa/{$id}", [], $_SESSION['token']);
        $this->handleApiAuthError($getRes);
        $mhs = $getRes['data'] ?? null;

        if (!$mhs) {
            $_SESSION['flash_error'] = 'Data mahasiswa tidak ditemukan.';
            return $this->redirect('/mahasiswa');
        }

        $idPengguna = $mhs['idPengguna'] ?? null;
        $namaMhs = $mhs['namaMahasiswa'] ?? 'Mahasiswa';
        $npm = $mhs['npm'] ?? '';

        // Hapus mahasiswa di API
        $res = ApiClient::delete("/mahasiswa/{$id}", $_SESSION['token']);
        $this->handleApiAuthError($res);

        if (isset($res['success']) && $res['success']) {
            $msg = "Data mahasiswa '{$namaMhs}' ({$npm}) berhasil dihapus.";

            // Jika ada akun pengguna tertaut dan rolenya MAHASISWA, hapus juga akunnya
            if ($idPengguna) {
                $userRes = ApiClient::get("/pengguna/{$idPengguna}", [], $_SESSION['token']);
                if (isset($userRes['data']['role']['kodeRole']) && $userRes['data']['role']['kodeRole'] === 'MAHASISWA') {
                    $delUser = ApiClient::delete("/pengguna/{$idPengguna}", $_SESSION['token']);
                    if (isset($delUser['success']) && $delUser['success']) {
                        $msg .= ' Akun login terkait juga telah dihapus.';
                    }
                }
            }

            $_SESSION['flash_success'] = $msg;
        } else {
            $errMsg = $res['error']['message'] ?? $res['message'] ?? 'Gagal menghapus data mahasiswa.';
            $_SESSION['flash_error'] = $errMsg;
        }

        return $this->redirect('/mahasiswa');
    }
}
