<?php
require_once 'Controller.php';
require_once __DIR__ . '/../Helpers/ApiClient.php';

class DashboardController extends Controller {

    public function index() {
        if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
            return $this->redirect('/login');
        }

        $token = $_SESSION['token'];

        // Ambil data profil pengguna terbaru
        $meRes = ApiClient::get('/auth/me', [], $token);
        if (isset($meRes['httpCode']) && $meRes['httpCode'] === 401) {
            unset($_SESSION['token'], $_SESSION['user']);
            $_SESSION['flash_error'] = "Sesi login telah berakhir. Silakan login kembali.";
            return $this->redirect('/login');
        }

        $user = $meRes['data'] ?? $_SESSION['user'] ?? [];
        if (!empty($meRes['data'])) {
            $_SESSION['user'] = $meRes['data'];
        }

        // 1. Ambil data Mahasiswa Terbaru & Total Mahasiswa
        $resAllMhs = ApiClient::get('/mahasiswa', [
            'page'  => 1,
            'limit' => 5,
            'order' => 'desc',
            'sort'  => 'id'
        ], $token);
        $totalMahasiswa  = (int)($resAllMhs['meta']['total'] ?? 0);
        $recentMahasiswa = $resAllMhs['data'] ?? [];

        // 2. Mahasiswa Aktif
        $resAktif = ApiClient::get('/mahasiswa', [
            'page'             => 1,
            'limit'            => 1,
            'status_mahasiswa' => 'Aktif'
        ], $token);
        $mhsAktif = (int)($resAktif['meta']['total'] ?? 0);

        // 3. Mahasiswa Cuti
        $resCuti = ApiClient::get('/mahasiswa', [
            'page'             => 1,
            'limit'            => 1,
            'status_mahasiswa' => 'Cuti'
        ], $token);
        $mhsCuti = (int)($resCuti['meta']['total'] ?? 0);

        // 4. Mahasiswa Lulus
        $resLulus = ApiClient::get('/mahasiswa', [
            'page'             => 1,
            'limit'            => 1,
            'status_mahasiswa' => 'Lulus'
        ], $token);
        $mhsLulus = (int)($resLulus['meta']['total'] ?? 0);

        // 5. Total Fakultas
        $resFakultas = ApiClient::get('/fakultas', [
            'page'  => 1,
            'limit' => 1
        ], $token);
        $totalFakultas = (int)($resFakultas['meta']['total'] ?? 0);

        // 6. Total Program Studi Aktif
        $resProdiAktif = ApiClient::get('/program-studi', [
            'page'         => 1,
            'limit'        => 1,
            'status_aktif' => 'Aktif'
        ], $token);
        $totalProdiAktif = (int)($resProdiAktif['meta']['total'] ?? 0);

        // 7. Total Semua Program Studi
        $resAllProdi = ApiClient::get('/program-studi', [
            'page'  => 1,
            'limit' => 1
        ], $token);
        $totalProdi = (int)($resAllProdi['meta']['total'] ?? $totalProdiAktif);

        $stats = [
            'total_mahasiswa'   => $totalMahasiswa,
            'mahasiswa_aktif'   => $mhsAktif,
            'mahasiswa_cuti'    => $mhsCuti,
            'mahasiswa_lulus'   => $mhsLulus,
            'total_fakultas'    => $totalFakultas,
            'total_prodi'       => $totalProdi,
            'total_prodi_aktif' => $totalProdiAktif,
        ];

        $this->view('dashboard/index', [
            'title'           => 'Dashboard',
            'user'            => $user,
            'stats'           => $stats,
            'recentMahasiswa' => $recentMahasiswa
        ]);
    }
}
