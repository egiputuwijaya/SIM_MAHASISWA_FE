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

// Helper to base URL (Mendukung HTTPS & Root Hosting InfinityFree)
function url($path = '') {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $scheme = $isHttps ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

    // Jika di-rewrite dari root (URL di browser tidak mengandung /public)
    $reqUri = $_SERVER['REQUEST_URI'] ?? '/';
    if (substr($dir, -7) === '/public' && strpos($reqUri, '/public') === false) {
        $dir = substr($dir, 0, -7);
    }

    $base = rtrim($scheme . $host . ($dir === '/' ? '' : $dir), '/');
    return $base . '/' . ltrim($path, '/');
}

// Pastikan aset gambar logo, university, & person tersedia di folder public
$pubAssets = __DIR__ . '/assets';
if (!is_dir($pubAssets)) {
    @mkdir($pubAssets, 0777, true);
}
if (!file_exists($pubAssets . '/logoumb.png') && file_exists(__DIR__ . '/../assets/logoumb.png')) {
    @copy(__DIR__ . '/../assets/logoumb.png', $pubAssets . '/logoumb.png');
}

$pubUniv = $pubAssets . '/university';
$srcUniv = __DIR__ . '/../assets/university';
if (!is_dir($pubUniv)) {
    @mkdir($pubUniv, 0777, true);
}
if (is_dir($srcUniv)) {
    $univFiles = scandir($srcUniv);
    foreach ($univFiles as $uf) {
        if ($uf !== '.' && $uf !== '..' && !file_exists($pubUniv . '/' . $uf)) {
            @copy($srcUniv . '/' . $uf, $pubUniv . '/' . $uf);
        }
    }
}

$pubPerson = $pubAssets . '/person';
$srcPerson = __DIR__ . '/../assets/person';
if (!is_dir($pubPerson)) {
    @mkdir($pubPerson, 0777, true);
}
if (is_dir($srcPerson)) {
    $personFiles = scandir($srcPerson);
    foreach ($personFiles as $pf) {
        if ($pf !== '.' && $pf !== '..' && !file_exists($pubPerson . '/' . $pf)) {
            @copy($srcPerson . '/' . $pf, $pubPerson . '/' . $pf);
        }
    }
}


// Simple Router
$raw_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($raw_path === false || $raw_path === null || $raw_path === '') {
    $raw_path = '/';
}

$script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$script_dir  = dirname($script_name);
if ($script_dir === '.' || $script_dir === '/' || $script_dir === '\\') {
    $script_dir = '';
}

// Deteksi base directory aplikasi (mendukung rewrite di root domain hosting maupun subfolder seperti Laragon/XAMPP)
$base_dir = $script_dir;
if (!empty($script_dir) && strpos($raw_path, $script_dir) !== 0) {
    if (substr($script_dir, -7) === '/public') {
        $base_dir = substr($script_dir, 0, -7);
    }
}

$path_info = '/';
if (!empty($base_dir) && strpos($raw_path, $base_dir) === 0) {
    $path_info = substr($raw_path, strlen($base_dir));
} else {
    $path_info = $raw_path;
}

if (empty($path_info) || $path_info[0] !== '/') {
    $path_info = '/' . $path_info;
}
if (strlen($path_info) > 1 && substr($path_info, -1) === '/') {
    $path_info = rtrim($path_info, '/');
}

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
