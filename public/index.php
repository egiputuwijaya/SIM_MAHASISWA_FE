<?php
// Konfigurasi session cookie agar tidak cepat kedaluwarsa saat idle (7 hari)
ini_set('session.gc_maxlifetime', 604800);
session_set_cookie_params([
    'lifetime' => 604800,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// Helper to base URL
function url($path = '') {
    $base = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
    $base = rtrim($base, '/');
    return $base . '/' . ltrim($path, '/');
}

// Pastikan aset gambar logo tersedia di folder public
$pubAssets = __DIR__ . '/assets';
if (!is_dir($pubAssets)) {
    @mkdir($pubAssets, 0777, true);
}
if (!file_exists($pubAssets . '/logoumb.png') && file_exists(__DIR__ . '/../assets/logoumb.png')) {
    @copy(__DIR__ . '/../assets/logoumb.png', $pubAssets . '/logoumb.png');
}

// Simple Router
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

$path_info = '/';
if (strpos($request_uri, dirname($script_name)) === 0) {
    $path_info = substr($request_uri, strlen(dirname($script_name)));
}
$path_info = parse_url($path_info, PHP_URL_PATH);
if (empty($path_info)) $path_info = '/';

// Require helpers and routes
require_once __DIR__ . '/../app/Helpers/ApiClient.php';
$routes = require_once __DIR__ . '/../routes/web.php';

// Route matching
$method = $_SERVER['REQUEST_METHOD'];
$matched = false;

foreach ($routes as $route) {
    if ($route['method'] !== $method) {
        continue;
    }

    $routePath = $route['path'];
    $matches = [];

    // Cek kecocokan exact string
    if ($routePath === $path_info) {
        $matched = true;
        $controllerName = $route['controller'];
        $action = $route['action'];
        
        require_once __DIR__ . '/../app/Controllers/' . $controllerName . '.php';
        $controller = new $controllerName();
        $controller->$action();
        break;
    }

    // Cek kecocokan regex seperti /pengguna/edit/(\d+) atau parameter {id}
    $pattern = '@^' . $routePath . '$@';
    if (@preg_match($pattern, $path_info, $matches)) {
        $matched = true;
        $controllerName = $route['controller'];
        $action = $route['action'];
        
        require_once __DIR__ . '/../app/Controllers/' . $controllerName . '.php';
        $controller = new $controllerName();
        
        // Buang elemen pertama (full match) agar hanya parameter yang dikirim
        array_shift($matches);
        call_user_func_array([$controller, $action], $matches);
        break;
    }
}

if (!$matched) {
    http_response_code(404);
    echo "404 Not Found - Path '{$path_info}' tidak ditemukan.";
}
