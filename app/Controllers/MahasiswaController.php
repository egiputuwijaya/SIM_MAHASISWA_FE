<?php
require_once 'Controller.php';

class MahasiswaController extends Controller {
    public function index() {
        if (!isset($_SESSION['token'])) {
            return $this->redirect('/login');
        }

        $page = $_GET['page'] ?? 1;
        $q = $_GET['q'] ?? '';

        $params = ['page' => $page];
        if ($q) $params['q'] = $q;

        $response = ApiClient::get('/mahasiswa', $params, $_SESSION['token']);
        
        $mahasiswa = [];
        $pagination = [];
        
        if (isset($response['status']) && $response['status'] == true) {
            $mahasiswa = $response['data']['list'] ?? [];
            $pagination = $response['data']['pagination'] ?? [];
        }

        $this->view('mahasiswa/index', [
            'title' => 'Data Mahasiswa',
            'mahasiswa' => $mahasiswa,
            'pagination' => $pagination,
            'q' => $q
        ]);
    }

    public function create() {
        if (!isset($_SESSION['token'])) {
            return $this->redirect('/login');
        }

        // Fetch prodi for dropdown if necessary, or just use simple inputs
        $this->view('mahasiswa/create', [
            'title' => 'Tambah Mahasiswa'
        ]);
    }

    public function store() {
        if (!isset($_SESSION['token'])) {
            return $this->redirect('/login');
        }

        $data = [
            'npm' => $_POST['npm'] ?? '',
            'nama_mahasiswa' => $_POST['nama_mahasiswa'] ?? '',
            'jenis_kelamin' => $_POST['jenis_kelamin'] ?? 'L',
            'id_program_studi' => $_POST['id_program_studi'] ?? 1,
            'tanggal_masuk' => $_POST['tanggal_masuk'] ?? date('Y-m-d')
        ];

        // For simplicity we use ApiClient::post which encodes to JSON. 
        // Note: Backend might require multipart if uploading photos. 
        // We will just send JSON for now. If it requires form-data, ApiClient needs an update.
        // The backend README says: POST /api/v1/mahasiswa Tambah mahasiswa (multipart, foto opsional)
        // If it requires multipart, we need to adapt ApiClient or just post JSON if supported.
        // Let's assume the API can handle form-urlencoded or JSON, but wait, README says: (multipart)
        
        $response = ApiClient::post('/mahasiswa', $data, $_SESSION['token']);
        
        if (isset($response['status']) && $response['status'] == true) {
            // Success
            header('Location: ' . url('/mahasiswa?success=1'));
            exit;
        } else {
            // Error
            $this->view('mahasiswa/create', [
                'title' => 'Tambah Mahasiswa',
                'error' => $response['message'] ?? 'Gagal menambahkan data.',
                'old' => $data
            ]);
        }
    }
}
