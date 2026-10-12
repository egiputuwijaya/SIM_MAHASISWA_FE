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
    ['method' => 'GET', 'path' => '/mahasiswa/detail/(\d+)', 'controller' => 'MahasiswaController', 'action' => 'detail'],
    ['method' => 'GET', 'path' => '/mahasiswa/show/(\d+)', 'controller' => 'MahasiswaController', 'action' => 'detail'],
    ['method' => 'GET', 'path' => '/mahasiswa/edit/(\d+)', 'controller' => 'MahasiswaController', 'action' => 'edit'],
    ['method' => 'POST', 'path' => '/mahasiswa/update/(\d+)', 'controller' => 'MahasiswaController', 'action' => 'update'],
    ['method' => 'POST', 'path' => '/mahasiswa/delete/(\d+)', 'controller' => 'MahasiswaController', 'action' => 'destroy'],
    
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

    // Fakultas
    ['method' => 'GET', 'path' => '/fakultas', 'controller' => 'FakultasController', 'action' => 'index'],
    ['method' => 'GET', 'path' => '/fakultas/create', 'controller' => 'FakultasController', 'action' => 'create'],
    ['method' => 'POST', 'path' => '/fakultas/store', 'controller' => 'FakultasController', 'action' => 'store'],
    ['method' => 'GET', 'path' => '/fakultas/edit/(\d+)', 'controller' => 'FakultasController', 'action' => 'edit'],
    ['method' => 'POST', 'path' => '/fakultas/update/(\d+)', 'controller' => 'FakultasController', 'action' => 'update'],
    ['method' => 'POST', 'path' => '/fakultas/delete/(\d+)', 'controller' => 'FakultasController', 'action' => 'destroy'],

    // Program Studi
    ['method' => 'GET', 'path' => '/program-studi', 'controller' => 'ProgramStudiController', 'action' => 'index'],
    ['method' => 'GET', 'path' => '/program-studi/create', 'controller' => 'ProgramStudiController', 'action' => 'create'],
    ['method' => 'POST', 'path' => '/program-studi/store', 'controller' => 'ProgramStudiController', 'action' => 'store'],
    ['method' => 'GET', 'path' => '/program-studi/edit/(\d+)', 'controller' => 'ProgramStudiController', 'action' => 'edit'],
    ['method' => 'POST', 'path' => '/program-studi/update/(\d+)', 'controller' => 'ProgramStudiController', 'action' => 'update'],
    ['method' => 'POST', 'path' => '/program-studi/delete/(\d+)', 'controller' => 'ProgramStudiController', 'action' => 'destroy'],

    // Audit Log
    ['method' => 'GET', 'path' => '/audit-log', 'controller' => 'AuditLogController', 'action' => 'index'],

    // Pengaturan Akun
    ['method' => 'GET', 'path' => '/pengaturan-akun', 'controller' => 'PengaturanAkunController', 'action' => 'index'],
    ['method' => 'POST', 'path' => '/pengaturan-akun/update-profil', 'controller' => 'PengaturanAkunController', 'action' => 'updateProfil'],
    ['method' => 'POST', 'path' => '/pengaturan-akun/update-password', 'controller' => 'PengaturanAkunController', 'action' => 'updatePassword'],

    // Laporan & Rekapitulasi Mahasiswa (Rektorat, Dekanat, Admin, dll)
    ['method' => 'GET', 'path' => '/laporan', 'controller' => 'LaporanController', 'action' => 'index'],
    ['method' => 'GET', 'path' => '/laporan-mahasiswa', 'controller' => 'LaporanController', 'action' => 'index'],
    ['method' => 'GET', 'path' => '/rekapitulasi', 'controller' => 'LaporanController', 'action' => 'index'],
    ['method' => 'GET', 'path' => '/laporan/export-csv', 'controller' => 'LaporanController', 'action' => 'exportCsv'],
    ['method' => 'GET', 'path' => '/laporan/cetak-pdf', 'controller' => 'LaporanController', 'action' => 'cetakPdf'],

    // Daftar Dosen Fakultas (Dekanat, Admin, Rektorat)
    ['method' => 'GET', 'path' => '/dosen', 'controller' => 'DosenController', 'action' => 'index'],
    ['method' => 'GET', 'path' => '/dosen/export-csv', 'controller' => 'DosenController', 'action' => 'exportCsv'],
    ['method' => 'GET', 'path' => '/dosen/cetak-pdf', 'controller' => 'DosenController', 'action' => 'cetakPdf'],
];
