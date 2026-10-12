<?php
require_once 'Controller.php';
require_once __DIR__ . '/../Helpers/ApiClient.php';

class PengaturanAkunController extends Controller {

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

        $token = $_SESSION['token'];
        $tab   = $_GET['tab'] ?? 'profil';
        if (!in_array($tab, ['profil', 'password'], true)) {
            $tab = 'profil';
        }

        // Ambil data profil terbaru dari API
        $res = ApiClient::get('/auth/me', [], $token);
        $this->handleApiAuthError($res);

        $user = $res['data'] ?? $_SESSION['user'] ?? [];
        if (!empty($res['data'])) {
            $_SESSION['user'] = $res['data'];
        }

        $this->view('pengaturan_akun/index', [
            'title' => 'Pengaturan Profil',
            'user'  => $user,
            'tab'   => $tab
        ]);
    }

    public function updateProfil() {
        $this->checkAuth();

        $token       = $_SESSION['token'];
        $namaLengkap = trim($_POST['nama_lengkap'] ?? $_POST['namaLengkap'] ?? '');
        $email       = trim($_POST['email'] ?? '');

        if ($namaLengkap === '') {
            $_SESSION['flash_error'] = "Nama lengkap wajib diisi.";
            return $this->redirect('/pengaturan-akun?tab=profil');
        }

        // Ambil ID pengguna saat ini
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            $me = ApiClient::get('/auth/me', [], $token);
            $userId = $me['data']['id'] ?? null;
        }

        if (!$userId) {
            $_SESSION['flash_error'] = "Gagal mengidentifikasi akun pengguna.";
            return $this->redirect('/pengaturan-akun?tab=profil');
        }

        $payload = [
            'namaLengkap' => $namaLengkap,
            'email'       => $email !== '' ? $email : null
        ];

        // Lakukan pembaruan profil melalui API PATCH /pengguna/{id}
        $response = ApiClient::patch("/pengguna/{$userId}", $payload, $token);
        $this->handleApiAuthError($response);

        if (isset($response['success']) && $response['success']) {
            // Perbarui sesi lokal
            $_SESSION['user']['namaLengkap'] = $namaLengkap;
            $_SESSION['user']['email']       = $email;
            
            $_SESSION['flash_success'] = "Profil dasar berhasil diperbarui!";
            return $this->redirect('/pengaturan-akun?tab=profil');
        } else {
            $msg = $response['error']['message'] ?? $response['message'] ?? 'Gagal memperbarui profil.';
            if (isset($response['error']['details']) && is_array($response['error']['details'])) {
                $detailMsgs = array_map(function($d) {
                    return $d['message'] ?? '';
                }, $response['error']['details']);
                $msg .= ' (' . implode(', ', array_filter($detailMsgs)) . ')';
            }
            $_SESSION['flash_error'] = $msg;
            return $this->redirect('/pengaturan-akun?tab=profil');
        }
    }

    public function updatePassword() {
        $this->checkAuth();

        $token              = $_SESSION['token'];
        $passwordLama       = (string)($_POST['password_lama'] ?? '');
        $passwordBaru       = (string)($_POST['password_baru'] ?? '');
        $konfirmasiPassword = (string)($_POST['konfirmasi_password'] ?? '');

        if ($passwordLama === '' || $passwordBaru === '' || $konfirmasiPassword === '') {
            $_SESSION['flash_error'] = "Semua field kata sandi wajib diisi.";
            return $this->redirect('/pengaturan-akun?tab=password');
        }

        if ($passwordBaru !== $konfirmasiPassword) {
            $_SESSION['flash_error'] = "Konfirmasi kata sandi baru tidak sesuai.";
            return $this->redirect('/pengaturan-akun?tab=password');
        }

        if (strlen($passwordBaru) < 8) {
            $_SESSION['flash_error'] = "Kata sandi baru minimal 8 karakter.";
            return $this->redirect('/pengaturan-akun?tab=password');
        }

        if (!preg_match('/[A-Z]/', $passwordBaru)) {
            $_SESSION['flash_error'] = "Kata sandi baru harus mengandung minimal 1 huruf besar (A-Z).";
            return $this->redirect('/pengaturan-akun?tab=password');
        }

        if (!preg_match('/[0-9]/', $passwordBaru)) {
            $_SESSION['flash_error'] = "Kata sandi baru harus mengandung minimal 1 angka (0-9).";
            return $this->redirect('/pengaturan-akun?tab=password');
        }

        if ($passwordLama === $passwordBaru) {
            $_SESSION['flash_error'] = "Kata sandi baru tidak boleh sama dengan kata sandi lama.";
            return $this->redirect('/pengaturan-akun?tab=password');
        }

        // Panggil endpoint PUT /auth/password
        $payload = [
            'passwordLama' => $passwordLama,
            'passwordBaru' => $passwordBaru
        ];

        $response = ApiClient::put('/auth/password', $payload, $token);
        $this->handleApiAuthError($response);

        if (isset($response['success']) && $response['success']) {
            $_SESSION['flash_success'] = "Kata sandi berhasil diperbarui!";
            return $this->redirect('/pengaturan-akun?tab=password');
        } else {
            $msg = $response['error']['message'] ?? $response['message'] ?? 'Gagal memperbarui kata sandi.';
            if (isset($response['error']['details']) && is_array($response['error']['details'])) {
                $detailMsgs = array_map(function($d) {
                    return $d['message'] ?? '';
                }, $response['error']['details']);
                $msg .= ' (' . implode(', ', array_filter($detailMsgs)) . ')';
            }
            $_SESSION['flash_error'] = $msg;
            return $this->redirect('/pengaturan-akun?tab=password');
        }
    }
}
