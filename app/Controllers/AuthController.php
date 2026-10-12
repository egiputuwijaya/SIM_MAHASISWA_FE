<?php
require_once 'Controller.php';

class AuthController extends Controller {
    public function index() {
        if (isset($_SESSION['token'])) {
            return $this->redirect('/dashboard');
        }
        $this->view('auth/login');
    }

    public function login() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $response = ApiClient::post('/login', [
            'username' => $username,
            'password' => $password
        ]);

        if (isset($response['status']) && $response['status'] == true) {
            $_SESSION['token'] = $response['data']['token'] ?? '';
            $_SESSION['user'] = $response['data']['profil'] ?? [];
            return $this->redirect('/dashboard');
        } else {
            $error = $response['message'] ?? 'Login failed. Please check your credentials.';
            $this->view('auth/login', ['error' => $error]);
        }
    }

    public function logout() {
        if (isset($_SESSION['token'])) {
            ApiClient::post('/logout', [], $_SESSION['token']);
            session_destroy();
        }
        return $this->redirect('/login');
    }
}
