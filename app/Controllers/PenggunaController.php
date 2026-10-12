<?php
require_once 'Controller.php';
require_once __DIR__ . '/../Helpers/ApiClient.php';

class PenggunaController extends Controller {
    public function index() {
        if (!isset($_SESSION['token'])) return $this->redirect('/login');
        
        // Coba memanggil API yang sebenarnya tidak ada di Backend
        $response = ApiClient::get('/pengguna', [], $_SESSION['token']);
        
        // Mock data karena API tidak ada
        $pengguna = [];
        $error = null;
        
        if (isset($response['error'])) {
            $error = "API Endpoint '/api/v1/pengguna' belum dibuat di sisi Backend!";
        }

        $this->view('pengguna/index', [
            'title' => 'Manajemen Pengguna',
            'pengguna' => $pengguna,
            'error' => $error
        ]);
    }

    public function create() {
        if (!isset($_SESSION['token'])) return $this->redirect('/login');
        $this->view('pengguna/create', ['title' => 'Tambah Pengguna']);
    }

    public function store() {
        if (!isset($_SESSION['token'])) return $this->redirect('/login');
        
        // Memanggil API yang sebenarnya tidak ada
        ApiClient::post('/pengguna', $_POST, $_SESSION['token']);
        
        $_SESSION['flash_error'] = "Gagal menyimpan. API Endpoint '/api/v1/pengguna' (POST) belum dibuat di sisi Backend!";
        return $this->redirect('/pengguna/create');
    }

    public function edit($id) {
        if (!isset($_SESSION['token'])) return $this->redirect('/login');
        $this->view('pengguna/edit', ['title' => 'Edit Pengguna']);
    }

    public function update($id) {
        if (!isset($_SESSION['token'])) return $this->redirect('/login');
        
        // Memanggil API yang sebenarnya tidak ada
        ApiClient::post("/pengguna/{$id}", $_POST, $_SESSION['token']); // Method spoofing (PUT) not possible since API doesn't exist anyway
        
        $_SESSION['flash_error'] = "Gagal update. API Endpoint '/api/v1/pengguna/{$id}' belum dibuat di sisi Backend!";
        return $this->redirect("/pengguna/edit/{$id}");
    }

    public function destroy($id) {
        if (!isset($_SESSION['token'])) return $this->redirect('/login');
        
        // Memanggil API yang sebenarnya tidak ada
        ApiClient::post("/pengguna/delete/{$id}", [], $_SESSION['token']);
        
        $_SESSION['flash_error'] = "Gagal menghapus. API Endpoint '/api/v1/pengguna/{$id}' (DELETE) belum dibuat di sisi Backend!";
        return $this->redirect('/pengguna');
    }
}
