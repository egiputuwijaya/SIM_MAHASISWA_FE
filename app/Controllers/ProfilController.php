<?php
require_once 'Controller.php';

class ProfilController extends Controller {
    public function index() {
        if (!isset($_SESSION['token'])) return $this->redirect('/login');
        
        $response = ApiClient::get('/universitas', [], $_SESSION['token']);
        $univ = [];
        if (!empty($response['data'])) {
            $univ = isset($response['data'][0]) ? $response['data'][0] : $response['data'];
            require_once __DIR__ . '/HomeController.php';
            HomeController::saveProfileCache($univ);
        }

        $this->view('profil/index', [
            'title' => 'Profil Universitas',
            'univ'  => $univ
        ]);
    }

    public function edit() {
        if (!isset($_SESSION['token'])) return $this->redirect('/login');

        $response = ApiClient::get('/universitas', [], $_SESSION['token']);
        $univ = [];
        if (!empty($response['data'])) {
            $univ = isset($response['data'][0]) ? $response['data'][0] : $response['data'];
        }

        $this->view('profil/edit', [
            'title' => 'Edit Profil Universitas',
            'univ'  => $univ
        ]);
    }

    public function update() {
        if (!isset($_SESSION['token'])) return $this->redirect('/login');
        
        // Ambil ID institusi
        $id = $_POST['id'] ?? 1;

        $payload = [
            'namaUniversitas' => $_POST['nama_universitas'] ?? $_POST['namaUniversitas'] ?? '',
            'slogan'          => $_POST['slogan'] ?? '',
            'alamat'          => $_POST['alamat'] ?? '',
            'kota'            => $_POST['kota'] ?? '',
            'provinsi'        => $_POST['provinsi'] ?? '',
            'kodePos'         => $_POST['kode_pos'] ?? $_POST['kodePos'] ?? '',
            'telepon'         => $_POST['telepon'] ?? '',
            'email'           => $_POST['email'] ?? '',
            'website'         => $_POST['website'] ?? ''
        ];
        
        $response = ApiClient::patch("/universitas/{$id}", $payload, $_SESSION['token']);
        
        if (isset($response['success']) && $response['success']) {
            unset($_SESSION['landing_cache']);
            require_once __DIR__ . '/HomeController.php';
            $currentCache = HomeController::loadProfileCache() ?: [];
            HomeController::saveProfileCache(array_merge($currentCache, $payload));
            $_SESSION['flash_success'] = "Profil universitas berhasil diperbarui!";
            return $this->redirect('/profil-universitas');
        } else {
            $_SESSION['flash_error'] = $response['error']['message'] ?? $response['message'] ?? 'Gagal memperbarui profil universitas.';
            return $this->redirect('/profil-universitas/edit');
        }
    }
}

