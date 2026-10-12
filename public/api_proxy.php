<?php
// Script ini digunakan di Frontend untuk memanggil (proxy) Backend secara lokal 
// tanpa mengubah kode Backend sama sekali dan tanpa memindahkan folder.

if (!isset($_GET['endpoint'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Endpoint missing']);
    exit;
}

// 1. Ubah variabel $_SERVER['REQUEST_URI'] menjadi persis seperti yang diharapkan router Backend
// Misalnya: /api/v1/login
$endpoint = $_GET['endpoint'];
$_SERVER['REQUEST_URI'] = $endpoint;

// 2. Berpindah (Change Directory) ke folder Backend agar include config dll berjalan normal
chdir(__DIR__ . '/../../SIM_MAHASISWA_BE');

// 3. Masukkan script index.php dari Backend untuk memproses request
require __DIR__ . '/../../SIM_MAHASISWA_BE/index.php';
