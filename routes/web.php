<?php
return [
    ['method' => 'GET', 'path' => '/', 'controller' => 'AuthController', 'action' => 'index'],
    ['method' => 'GET', 'path' => '/login', 'controller' => 'AuthController', 'action' => 'index'],
    ['method' => 'POST', 'path' => '/login', 'controller' => 'AuthController', 'action' => 'login'],
    ['method' => 'GET', 'path' => '/logout', 'controller' => 'AuthController', 'action' => 'logout'],
    
    ['method' => 'GET', 'path' => '/dashboard', 'controller' => 'DashboardController', 'action' => 'index'],
    
    ['method' => 'GET', 'path' => '/mahasiswa', 'controller' => 'MahasiswaController', 'action' => 'index'],
    ['method' => 'GET', 'path' => '/mahasiswa/create', 'controller' => 'MahasiswaController', 'action' => 'create'],
    ['method' => 'POST', 'path' => '/mahasiswa/store', 'controller' => 'MahasiswaController', 'action' => 'store'],
    
    ['method' => 'GET', 'path' => '/profil-universitas', 'controller' => 'ProfilController', 'action' => 'index'],
    ['method' => 'GET', 'path' => '/profil-universitas/edit', 'controller' => 'ProfilController', 'action' => 'edit'],
    ['method' => 'POST', 'path' => '/profil-universitas/update', 'controller' => 'ProfilController', 'action' => 'update'],

    // Pengguna
    ['method' => 'GET', 'path' => '/pengguna', 'controller' => 'PenggunaController', 'action' => 'index'],
    ['method' => 'GET', 'path' => '/pengguna/create', 'controller' => 'PenggunaController', 'action' => 'create'],
    ['method' => 'POST', 'path' => '/pengguna/store', 'controller' => 'PenggunaController', 'action' => 'store'],
    ['method' => 'GET', 'path' => '/pengguna/edit/(\d+)', 'controller' => 'PenggunaController', 'action' => 'edit'],
    ['method' => 'POST', 'path' => '/pengguna/update/(\d+)', 'controller' => 'PenggunaController', 'action' => 'update'],
    ['method' => 'POST', 'path' => '/pengguna/delete/(\d+)', 'controller' => 'PenggunaController', 'action' => 'destroy'],
];
