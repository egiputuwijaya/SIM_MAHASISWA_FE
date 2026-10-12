<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SIM UMB - Sistem Informasi Mahasiswa' ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.3/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#1d4ed8', // Blue
                        secondary: '#f3f4f6', // Light gray background
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f0f2f5; }
        [x-cloak] { display: none !important; }
        .sidebar-item { transition: all 0.2s ease; }
        .sidebar-item:hover {
            color: #1d4ed8;
            background-color: #eff6ff;
        }
        .sidebar-item.active { 
            color: #1d4ed8; 
            background-color: #eff6ff;
            font-weight: 500;
        }
        .sidebar-icon {
            color: #6b7280;
        }
        .sidebar-item:hover .sidebar-icon, .sidebar-item.active .sidebar-icon {
            color: #1d4ed8;
        }
    </style>
</head>
<body class="text-gray-800 antialiased flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

<?php
$userProfile = $_SESSION['user'] ?? [];
$userName = $userProfile['namaLengkap'] ?? $userProfile['nama_lengkap'] ?? $userProfile['nama_pengguna'] ?? $userProfile['username'] ?? 'Administrator';
$userRole = is_array($userProfile['role'] ?? null) ? ($userProfile['role']['namaRole'] ?? $userProfile['role']['kodeRole'] ?? 'Admin') : ($userProfile['role'] ?? 'Admin');
$userRoleCode = is_array($userProfile['role'] ?? null) ? strtoupper($userProfile['role']['kodeRole'] ?? $userProfile['role']['namaRole'] ?? 'ADMIN') : strtoupper((string)$userRole);
$userInitial = strtoupper(substr($userName, 0, 2)); // e.g. "AD" or "OR"
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$currentPath = basename($currentPath);
if ($currentPath == 'public' || $currentPath == 'index.php' || $currentPath == '') $currentPath = 'dashboard';
?>
    <!-- Sidebar -->
    <aside class="bg-white border-r border-gray-200 flex flex-col transition-all duration-300 shadow-sm z-20 shrink-0 relative"
           :class="sidebarOpen ? 'w-[260px]' : 'w-[80px]'">
        
        <!-- Toggle Button (Absolute on edge) -->
        <button @click="sidebarOpen = !sidebarOpen" class="absolute -right-3 top-6 bg-white border border-gray-200 text-gray-400 rounded-full w-6 h-6 flex items-center justify-center hover:text-blue-600 shadow-sm z-50">
            <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right text-xs'"></i>
        </button>

        <div class="h-20 flex items-center border-b border-gray-100 transition-all duration-300" :class="sidebarOpen ? 'px-6' : 'justify-center px-0'">
            <div class="flex items-center gap-3">
                <!-- Logo Circle -->
                <div class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center shadow-sm shrink-0 p-1">
                    <img src="<?= url('assets/logoumb.png') ?>" alt="Logo UMB" class="w-full h-full object-contain">
                </div>
                <div class="flex flex-col whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarOpen ? 'w-auto opacity-100' : 'w-0 opacity-0'">
                    <span class="text-[15px] font-bold text-gray-800 leading-tight">SIM UMB</span>
                    <span class="text-[11px] font-medium text-blue-600 leading-tight">Sistem Informasi Mahasiswa</span>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto py-4 space-y-2 overflow-x-hidden">
            <!-- Dashboard (Semua Role) -->
            <a href="<?= url('/dashboard') ?>" class="sidebar-item <?= strpos($currentPath, 'dashboard') !== false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-gauge w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Dashboard</span>
            </a>

            <?php if ($userRoleCode === 'ADMIN'): ?>
            <!-- Menu Khusus Admin -->
            <a href="<?= url('/profil-universitas') ?>" class="sidebar-item <?= strpos($currentPath, 'profil-universitas') !== false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-building w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Profil Universitas</span>
            </a>
            <a href="<?= url('/pengguna') ?>" class="sidebar-item <?= strpos($currentPath, 'pengguna') !== false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-user-friends w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Data Pengguna</span>
            </a>
            <a href="<?= url('/fakultas') ?>" class="sidebar-item <?= strpos($currentPath, 'fakultas') !== false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-landmark w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Fakultas</span>
            </a>
            <a href="<?= url('/program-studi') ?>" class="sidebar-item <?= strpos($currentPath, 'program-studi') !== false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-graduation-cap w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Program Studi</span>
            </a>
            <?php endif; ?>

            <?php if (!in_array($userRoleCode, ['REKTORAT', 'DEKANAT'])): ?>
            <!-- Data Mahasiswa (Admin, BAAK, Prodi, Dosen) -->
            <a href="<?= url('/mahasiswa') ?>" class="sidebar-item <?= strpos($currentPath, 'mahasiswa') !== false && strpos($currentPath, 'laporan') === false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-users w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Data Mahasiswa</span>
            </a>
            <?php endif; ?>

            <!-- Laporan Mahasiswa (Rektorat, Dekanat, Admin, BAAK, dll) -->
            <a href="<?= url('/laporan') ?>" class="sidebar-item <?= (strpos($currentPath, 'laporan') !== false || strpos($currentPath, 'rekapitulasi') !== false) ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-file-lines w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Laporan Mahasiswa</span>
            </a>

            <?php if (in_array($userRoleCode, ['DEKANAT', 'ADMIN_DEKANAT', 'KAPRODI', 'ADMIN_PRODI', 'REKTORAT', 'ADMIN'])): ?>
            <!-- Daftar Dosen (Dekanat, Kaprodi, Rektorat, Admin) -->
            <a href="<?= url('/dosen') ?>" class="sidebar-item <?= strpos($currentPath, 'dosen') !== false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-chalkboard-user w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Daftar Dosen</span>
            </a>
            <?php endif; ?>

            <?php if ($userRoleCode === 'ADMIN'): ?>
            <!-- Audit Log (Admin) -->
            <a href="<?= url('/audit-log') ?>" class="sidebar-item <?= strpos($currentPath, 'audit-log') !== false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-history w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Audit Log</span>
            </a>
            <?php endif; ?>

            <!-- Pengaturan Akun (Semua Role) -->
            <a href="<?= url('/pengaturan-akun') ?>" class="sidebar-item <?= strpos($currentPath, 'pengaturan-akun') !== false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-user-gear w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Pengaturan Akun</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden bg-[#f4f7fb]">
        <!-- Topbar -->
        <header class="bg-white h-20 flex items-center justify-between px-8 border-b border-gray-200 z-10 shrink-0 shadow-sm">
            <!-- Breadcrumb / Title Area -->
            <div class="flex flex-col">
                <h2 class="text-lg font-bold text-gray-800"><?= $title ?? 'Dashboard' ?></h2>
                <p class="text-xs text-gray-500 mt-0.5">SIM Mahasiswa — Universitas Muhammadiyah Bengkulu</p>
            </div>
            
            <!-- User Menu -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 bg-gray-50 rounded-full pr-4 p-1 border border-gray-100">
                    <div class="w-9 h-9 rounded-full bg-[#1e88e5] text-white flex items-center justify-center text-sm font-semibold shadow-sm">
                        <?= htmlspecialchars($userInitial) ?>
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-[13px] font-bold text-gray-800"><?= htmlspecialchars($userName) ?></span>
                        <span class="text-[11px] text-blue-600 font-medium"><?= htmlspecialchars($userRole) ?></span>
                    </div>
                </div>
                
                <a href="<?= url('/logout') ?>" class="bg-[#d32f2f] hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded transition-colors flex items-center gap-2 shadow-sm">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </div>
        </header>

        <!-- Main Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-8 relative">
            <div class="max-w-full mx-auto">
                <?= $content ?? '' ?>
            </div>
        </main>
    </div>

</body>
</html>
