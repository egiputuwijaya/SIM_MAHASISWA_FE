<?php
require_once 'Controller.php';

class ProfilController extends Controller {
    public function index() {
        if (!isset($_SESSION['token'])) return $this->redirect('/login');
        $this->view('profil/index', ['title' => 'Profil Universitas']);
    }

    public function edit() {
        if (!isset($_SESSION['token'])) return $this->redirect('/login');
        $this->view('profil/edit', ['title' => 'Edit Profil Universitas']);
    }

    public function update() {
        if (!isset($_SESSION['token'])) return $this->redirect('/login');
        
        // Coba memanggil API yang sebenarnya tidak ada di Backend
        // Menggunakan helper ApiClient ke endpoint yang tidak ada (karena backend belum memilikinya)
        $response = ApiClient::post('/universitas/update', $_POST, $_SESSION['token']);
        
        // Kita tangkap errornya untuk diberitahukan ke UI
        $_SESSION['flash_error'] = "API Endpoint '/api/v1/universitas/update' belum dibuat di sisi Backend!";
        
        return $this->redirect('/profil-universitas/edit');
    }
}
