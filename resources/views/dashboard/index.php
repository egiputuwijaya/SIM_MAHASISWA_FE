<?php ob_start(); ?>

<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-8 mb-8 text-white shadow-lg relative overflow-hidden">
    <div class="absolute -right-10 -top-20 opacity-20 transform rotate-12">
        <i class="fas fa-graduation-cap text-9xl"></i>
    </div>
    <div class="relative z-10">
        <h1 class="text-3xl font-bold mb-2">Selamat Datang, <?= htmlspecialchars(is_string($_SESSION['user']['nama_pengguna'] ?? null) ? $_SESSION['user']['nama_pengguna'] : 'Admin') ?>! 👋</h1>
        <p class="text-blue-100 opacity-90 max-w-2xl">Pantau dan kelola seluruh data akademik, statistik mahasiswa, dan informasi penting lainnya dengan mudah melalui dashboard ini.</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
        <div class="w-14 h-14 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mr-4">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Total Mahasiswa</p>
            <h3 class="text-2xl font-bold text-gray-800"><?= number_format($stats['total_mahasiswa'] ?? 0) ?></h3>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
        <div class="w-14 h-14 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-2xl mr-4">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Mahasiswa Aktif</p>
            <h3 class="text-2xl font-bold text-gray-800"><?= number_format($stats['aktif'] ?? 0) ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
        <div class="w-14 h-14 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mr-4">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Alumni / Lulus</p>
            <h3 class="text-2xl font-bold text-gray-800"><?= number_format($stats['lulus'] ?? 0) ?></h3>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">Akses Cepat</h3>
    </div>
    <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="<?= url('/mahasiswa/create') ?>" class="group flex flex-col items-center justify-center p-6 bg-slate-50 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-100 transition-all cursor-pointer">
            <div class="w-12 h-12 rounded-full bg-white text-blue-500 shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-plus"></i>
            </div>
            <span class="text-sm font-medium text-gray-600 group-hover:text-blue-600">Tambah Mahasiswa</span>
        </a>
        
        <a href="<?= url('/mahasiswa') ?>" class="group flex flex-col items-center justify-center p-6 bg-slate-50 rounded-xl hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition-all cursor-pointer">
            <div class="w-12 h-12 rounded-full bg-white text-indigo-500 shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-list"></i>
            </div>
            <span class="text-sm font-medium text-gray-600 group-hover:text-indigo-600">Lihat Data</span>
        </a>
        
        <div class="group flex flex-col items-center justify-center p-6 bg-slate-50 rounded-xl hover:bg-green-50 border border-transparent hover:border-green-100 transition-all cursor-pointer">
            <div class="w-12 h-12 rounded-full bg-white text-green-500 shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-file-excel"></i>
            </div>
            <span class="text-sm font-medium text-gray-600 group-hover:text-green-600">Export Laporan</span>
        </div>
        
        <div class="group flex flex-col items-center justify-center p-6 bg-slate-50 rounded-xl hover:bg-orange-50 border border-transparent hover:border-orange-100 transition-all cursor-pointer">
            <div class="w-12 h-12 rounded-full bg-white text-orange-500 shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-cog"></i>
            </div>
            <span class="text-sm font-medium text-gray-600 group-hover:text-orange-600">Pengaturan</span>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
