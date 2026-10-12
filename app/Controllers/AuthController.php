<?php
require_once 'Controller.php';

class AuthController extends Controller {
    public function index() {
        if (isset($_SESSION['token'])) {
            return $this->redirect('/dashboard');
        }
        $success = $_SESSION['flash_success'] ?? null;
        if (!$success && isset($_GET['logout'])) {
            $success = 'Anda telah berhasil keluar. Sampai jumpa kembali!';
        }
        unset($_SESSION['flash_success']);

        $this->view('auth/login', [
            'success' => $success
        ]);
    }

    public function login() {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            return $this->view('auth/login', ['error' => 'Username dan password wajib diisi.']);
        }

        $response = ApiClient::post('/auth/login', [
            'username' => $username,
            'password' => $password
        ]);

        $token = $response['data']['accessToken'] ?? $response['data']['token'] ?? $response['token'] ?? null;
        $refreshToken = $response['data']['refreshToken'] ?? null;

        if ((isset($response['success']) && $response['success']) || !empty($token)) {
            $_SESSION['token'] = $token;
            if ($refreshToken) {
                $_SESSION['refreshToken'] = $refreshToken;
            }
            
            // Ambil detail profil pengguna yang login
            $me = ApiClient::get('/auth/me', [], $token);
            if (!empty($me['data'])) {
                $_SESSION['user'] = $me['data'];
            } else {
                $_SESSION['user'] = $response['data']['user'] ?? $response['data']['pengguna'] ?? [
                    'namaLengkap' => $username,
                    'username'    => $username,
                    'role'        => 'Admin'
                ];
            }

            return $this->redirect('/dashboard');
        } else {
            $error = $response['error']['message'] ?? $response['message'] ?? 'Username atau password salah.';
            $this->view('auth/login', ['error' => $error]);
        }
    }

    public function logout() {
        if (isset($_SESSION['token'])) {
            ApiClient::post('/auth/logout', [], $_SESSION['token']);
        }
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        session_start();
        $_SESSION['flash_success'] = 'Anda telah berhasil keluar. Sampai jumpa kembali!';
        return $this->redirect('/login');
    }
}
