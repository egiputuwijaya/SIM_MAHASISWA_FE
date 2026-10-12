<?php
require_once 'Controller.php';
require_once __DIR__ . '/../Helpers/ApiClient.php';

class AuditLogController extends Controller {
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

        $page           = max(1, (int)($_GET['page'] ?? 1));
        $limit          = 25;
        $filterAksi     = trim((string)($_GET['aksi'] ?? ''));
        $filterStart    = trim((string)($_GET['start_date'] ?? ''));
        $filterEnd      = trim((string)($_GET['end_date'] ?? ''));
        $filterUsername = trim((string)($_GET['username'] ?? ''));

        $params = [
            'page'  => $page,
            'limit' => $limit,
            'order' => 'desc',
            'sort'  => 'waktu'
        ];

        if ($filterAksi !== '' && in_array($filterAksi, ['LOGIN', 'LOGOUT', 'INSERT', 'UPDATE', 'DELETE', 'VIEW'], true)) {
            $params['aksi'] = $filterAksi;
        }
        if ($filterStart !== '') {
            $params['dari'] = $filterStart . 'T00:00:00.000Z';
        }
        if ($filterEnd !== '') {
            $params['sampai'] = $filterEnd . 'T23:59:59.999Z';
        }

        $res = ApiClient::get('/audit-logs', $params, $_SESSION['token']);
        if (!isset($res['data'])) {
            $fallback = ApiClient::get('/audit-log', $params, $_SESSION['token']);
            if (isset($fallback['data'])) {
                $res = $fallback;
            }
        }
        $this->handleApiAuthError($res);

        $logs = $res['data'] ?? [];
        $meta = $res['meta'] ?? [];
        $totalRows = (int)($meta['total'] ?? count($logs));
        $totalPages = (int)($meta['totalPages'] ?? max(1, (int)ceil($totalRows / $limit)));

        // Filter username di memori jika ada input username pelaku
        if ($filterUsername !== '') {
            $logs = array_filter($logs, function($l) use ($filterUsername) {
                $uName = strtolower($l['pengguna']['username'] ?? '');
                $fName = strtolower($l['pengguna']['namaLengkap'] ?? '');
                $target = strtolower($filterUsername);
                return (strpos($uName, $target) !== false || strpos($fName, $target) !== false);
            });
        }

        $this->view('audit_log/index', [
            'title'          => 'Audit Log System',
            'logs'           => $logs,
            'totalRows'      => $totalRows,
            'totalPages'     => $totalPages,
            'page'           => $page,
            'filterAksi'     => $filterAksi,
            'filterStart'    => $filterStart,
            'filterEnd'      => $filterEnd,
            'filterUsername' => $filterUsername
        ]);
    }
}
