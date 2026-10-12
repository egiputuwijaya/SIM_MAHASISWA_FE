<?php ob_start(); 

// Penentuan nama pengguna, peran, dan sapaan waktu
$userProfile = $user ?? $_SESSION['user'] ?? [];
$userName = $userProfile['namaLengkap'] ?? $userProfile['nama_lengkap'] ?? $userProfile['username'] ?? 'Admin SIM Mhs UMB';
$userRole = is_array($userProfile['role'] ?? null) 
    ? ($userProfile['role']['namaRole'] ?? 'Admin') 
    : ($userProfile['role'] ?? 'Admin');

$hour = (int)date('H');
if ($hour >= 4 && $hour < 11) {
    $sapaan = 'Pagi';
} elseif ($hour >= 11 && $hour < 15) {
    $sapaan = 'Siang';
} elseif ($hour >= 15 && $hour < 18) {
    $sapaan = 'Sore';
} else {
    $sapaan = 'Malam';
}

$lastLoginRaw = $userProfile['lastLogin'] ?? $userProfile['last_login'] ?? null;
$lastLoginTime = $lastLoginRaw ? date('Y-m-d H:i:s', strtotime($lastLoginRaw)) : date('Y-m-d H:i:s');

function status_badge_class(string $status): string {
    return match ($status) {
        'Aktif'             => 'bg-green-100 text-green-700 border-green-200',
        'Cuti'              => 'bg-amber-100 text-amber-700 border-amber-200',
        'Lulus'             => 'bg-blue-100 text-blue-700 border-blue-200',
        'Mengundurkan Diri' => 'bg-orange-100 text-orange-700 border-orange-200',
        'Drop Out'          => 'bg-red-100 text-red-700 border-red-200',
        'Tidak Aktif'       => 'bg-slate-100 text-slate-700 border-slate-200',
        default             => 'bg-slate-100 text-slate-700 border-slate-200',
    };
}
?>

<div class="space-y-6">

    <!-- 1. Top Green Alert Banner -->
    <div class="p-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center shadow-sm">
        <i class="fa-solid fa-circle-check text-emerald-600 mr-2.5 text-base"></i>
        <span class="font-medium">Selamat datang, <?= htmlspecialchars($userName) ?>!</span>
    </div>

    <!-- 2. Hero Banner (Big Blue Card) -->
    <div class="rounded-xl p-6 sm:p-8 text-white shadow-md relative overflow-hidden bg-gradient-to-r from-[#173e89] via-[#1a56b4] to-[#1e88e5]">
        
        <!-- Background decorative icon -->
        <div class="absolute -right-6 -bottom-10 opacity-10 pointer-events-none transform -rotate-12">
            <i class="fa-solid fa-graduation-cap text-[14rem]"></i>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            
            <!-- Left Info -->
            <div class="max-w-2xl">
                <!-- Date Pill -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 border border-white/20 text-white text-xs font-medium mb-3 backdrop-blur-sm">
                    <i class="fa-regular fa-calendar"></i>
                    <span><?= date('d F Y') ?></span>
                </div>

                <!-- Greeting Title -->
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2 text-white">
                    Selamat <?= $sapaan ?>, <span class="text-emerald-300"><?= htmlspecialchars($userName) ?>!</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-blue-100 text-sm leading-relaxed">
                    Anda masuk dengan role <span class="font-semibold text-white"><?= htmlspecialchars($userRole) ?></span>. Semoga hari Anda menyenangkan dan produktif.
                </p>
            </div>

            <!-- Right: Sesi Aktif Card -->
            <div class="bg-black/20 backdrop-blur-md border border-white/20 rounded-lg p-4 sm:p-5 shrink-0 min-w-[200px] text-left md:text-right shadow-inner">
                <div class="flex items-center md:justify-end gap-1.5 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Sesi Aktif</span>
                </div>
                <p class="text-[11px] text-blue-200 font-medium">Login terakhir</p>
                <p class="text-xs font-mono font-bold text-white mt-0.5 tracking-tight">
                    <?= htmlspecialchars($lastLoginTime) ?>
                </p>
            </div>

        </div>
    </div>

    <!-- 3. Row of 4 Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Card 1: Mahasiswa Aktif -->
        <div class="bg-white rounded-lg p-5 border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase text-slate-500 tracking-wider">Mahasiswa Aktif</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight mt-0.5">
                        <?= number_format($stats['mahasiswa_aktif'] ?? 0) ?>
                    </h3>
                </div>
            </div>
        </div>

        <!-- Card 2: Mahasiswa Cuti -->
        <div class="bg-white rounded-lg p-5 border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase text-slate-500 tracking-wider">Mahasiswa Cuti</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight mt-0.5">
                        <?= number_format($stats['mahasiswa_cuti'] ?? 0) ?>
                    </h3>
                </div>
            </div>
        </div>

        <!-- Card 3: Mahasiswa Lulus -->
        <div class="bg-white rounded-lg p-5 border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase text-slate-500 tracking-wider">Mahasiswa Lulus</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight mt-0.5">
                        <?= number_format($stats['mahasiswa_lulus'] ?? 0) ?>
                    </h3>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Prodi Aktif -->
        <div class="bg-white rounded-lg p-5 border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-sitemap"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase text-slate-500 tracking-wider">Total Prodi Aktif</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight mt-0.5">
                        <?= number_format($stats['total_prodi_aktif'] ?? 0) ?>
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <!-- 4. Middle Section: Ringkasan Data (70%) & Aksi Cepat (30%) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Ringkasan Data (Col Span 8) -->
        <div class="lg:col-span-8 bg-white rounded-lg border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-slate-100">
                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                    <i class="fa-solid fa-chart-simple"></i>
                </div>
                <h3 class="font-bold text-base text-slate-800">Ringkasan Data</h3>
            </div>

            <!-- Grid 4 Kolom Stat Ringkasan -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                
                <!-- Box 1 -->
                <div class="p-4 rounded-lg bg-slate-50/70 border border-slate-100 text-center hover:bg-slate-50 transition-colors">
                    <h4 class="text-2xl font-extrabold text-slate-800 tracking-tight">
                        <?= number_format($stats['total_mahasiswa'] ?? 0) ?>
                    </h4>
                    <p class="text-xs text-slate-500 font-medium mt-1">Total Mahasiswa</p>
                </div>

                <!-- Box 2 -->
                <div class="p-4 rounded-lg bg-slate-50/70 border border-slate-100 text-center hover:bg-slate-50 transition-colors">
                    <h4 class="text-2xl font-extrabold text-slate-800 tracking-tight">
                        <?= number_format($stats['total_fakultas'] ?? 0) ?>
                    </h4>
                    <p class="text-xs text-slate-500 font-medium mt-1">Fakultas</p>
                </div>

                <!-- Box 3 -->
                <div class="p-4 rounded-lg bg-slate-50/70 border border-slate-100 text-center hover:bg-slate-50 transition-colors">
                    <h4 class="text-2xl font-extrabold text-slate-800 tracking-tight">
                        <?= number_format($stats['total_prodi'] ?? 0) ?>
                    </h4>
                    <p class="text-xs text-slate-500 font-medium mt-1">Program Studi</p>
                </div>

                <!-- Box 4 -->
                <div class="p-4 rounded-lg bg-slate-50/70 border border-slate-100 text-center hover:bg-slate-50 transition-colors">
                    <h4 class="text-2xl font-extrabold text-emerald-600 tracking-tight">
                        <?= number_format($stats['mahasiswa_aktif'] ?? 0) ?>
                    </h4>
                    <p class="text-xs text-slate-500 font-medium mt-1">Status Aktif</p>
                </div>

            </div>
        </div>

        <!-- Right: Aksi Cepat (Col Span 4) -->
        <div class="lg:col-span-4 bg-white rounded-lg border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-slate-100">
                <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <h3 class="font-bold text-base text-slate-800">Aksi Cepat</h3>
            </div>

            <!-- List Aksi Cepat -->
            <div class="space-y-3">
                <a href="<?= url('/mahasiswa') ?>" 
                   class="flex items-center gap-3.5 p-3 rounded-lg border border-slate-100 hover:border-blue-200 bg-slate-50/60 hover:bg-blue-50/60 transition-all text-slate-700 hover:text-blue-700 group">
                    <div class="w-9 h-9 rounded-md bg-blue-100/80 text-blue-600 flex items-center justify-center text-sm shrink-0 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <span class="text-xs font-semibold">Kelola Data Mahasiswa</span>
                </a>

                <a href="<?= url('/mahasiswa/create') ?>" 
                   class="flex items-center gap-3.5 p-3 rounded-lg border border-slate-100 hover:border-emerald-200 bg-slate-50/60 hover:bg-emerald-50/60 transition-all text-slate-700 hover:text-emerald-700 group">
                    <div class="w-9 h-9 rounded-md bg-emerald-100/80 text-emerald-600 flex items-center justify-center text-sm shrink-0 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <span class="text-xs font-semibold">Tambah Mahasiswa Baru</span>
                </a>
            </div>
        </div>

    </div>

    <!-- 5. Bottom Section: Data Mahasiswa Terbaru -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Header Tabel -->
        <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <h3 class="font-bold text-base text-slate-800">Data Mahasiswa Terbaru</h3>
            </div>
            <a href="<?= url('/mahasiswa') ?>" 
               class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1.5 transition-colors">
                <span>Lihat Semua</span>
                <i class="fa-solid fa-arrow-right text-[11px]"></i>
            </a>
        </div>

        <!-- Tabel Mahasiswa -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">NPM</th>
                        <th class="px-6 py-3.5">Nama Mahasiswa</th>
                        <th class="px-6 py-3.5">Program Studi</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5">Tgl Masuk</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($recentMahasiswa)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                <i class="fa-regular fa-folder-open text-2xl mb-2 block"></i>
                                <span class="text-xs">Belum ada data mahasiswa yang terdaftar.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentMahasiswa as $mhs): 
                            $prodiName = $mhs['programStudi']['namaProgramStudi'] ?? '-';
                            $status = $mhs['statusMahasiswa'] ?? 'Aktif';
                            $tglMasuk = !empty($mhs['tanggalMasuk']) ? date('d/m/Y', strtotime($mhs['tanggalMasuk'])) : '-';
                        ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- NPM -->
                                <td class="px-6 py-3.5 font-mono text-xs font-semibold text-slate-800">
                                    <?= htmlspecialchars($mhs['npm'] ?? '-') ?>
                                </td>

                                <!-- Nama Mahasiswa -->
                                <td class="px-6 py-3.5 font-semibold text-slate-800">
                                    <?= htmlspecialchars($mhs['namaMahasiswa'] ?? '-') ?>
                                </td>

                                <!-- Program Studi -->
                                <td class="px-6 py-3.5 text-slate-600 text-xs">
                                    <?= htmlspecialchars($prodiName) ?>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold border <?= status_badge_class($status) ?>">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                </td>

                                <!-- Tgl Masuk -->
                                <td class="px-6 py-3.5 text-slate-600 text-xs font-mono">
                                    <?= htmlspecialchars($tglMasuk) ?>
                                </td>

                                <!-- Aksi -->
                                <td class="px-6 py-3.5 text-center">
                                    <a href="<?= url('/mahasiswa/detail/' . $mhs['id']) ?>" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors shadow-2xs">
                                        <i class="fa-regular fa-eye"></i>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
