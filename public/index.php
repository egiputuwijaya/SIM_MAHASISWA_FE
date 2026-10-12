<?php
session_start();

// Helper to base URL
function url($path = '') {
    $base = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
    $base = rtrim($base, '/');
    return $base . '/' . ltrim($path, '/');
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
    if ($route['method'] == $method && $route['path'] == $path_info) {
        $matched = true;
        
        $controllerName = $route['controller'];
        $action = $route['action'];
        
        require_once __DIR__ . '/../app/Controllers/' . $controllerName . '.php';
        $controller = new $controllerName();
        $controller->$action();
        break;
    }
}

if (!$matched) {
    http_response_code(404);
    echo "404 Not Found";
}
