<?php
require_once 'Controller.php';

class DashboardController extends Controller {
    public function index() {
        if (!isset($_SESSION['token'])) {
            return $this->redirect('/login');
        }

        // We can fetch stats from backend if available, or just dummy for now
        $stats = [
            'total_mahasiswa' => 1250,
            'aktif' => 1100,
            'lulus' => 150
        ];

        $this->view('dashboard/index', [
            'title' => 'Dashboard Overview',
            'stats' => $stats
        ]);
    }
}
