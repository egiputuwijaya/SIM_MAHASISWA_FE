<?php
/**
 * Partial: Navbar publik (dipakai oleh semua halaman publik).
 * Variabel opsional: $activePage ('home' | 'fakultas-prodi' | 'about-dev')
 */
$activePage = $activePage ?? '';

$uni = $landing['universitas'] ?? null;
if (empty($uni)) {
    require_once __DIR__ . '/../../app/Controllers/HomeController.php';
    $uni = HomeController::loadProfileCache() ?? [];
}

$kodeUni = $uni['kodeUniversitas'] ?? $uni['kode_universitas'] ?? 'UMB';
$namaUni = !empty($uni['namaUniversitas']) ? $uni['namaUniversitas'] : (!empty($uni['nama_universitas']) ? $uni['nama_universitas'] : 'Universitas Mercu Buana');
$e       = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

$navItems = [
    ['key' => 'home',           'label' => 'Home',            'icon' => 'fa-house',          'href' => url('/')],
    ['key' => 'fakultas-prodi', 'label' => 'Fakultas & Prodi', 'icon' => 'fa-graduation-cap', 'href' => url('/fakultas-prodi')],
    ['key' => 'about-dev',      'label' => 'About Dev',       'icon' => 'fa-users',          'href' => url('/about-dev')],
];
?>
<header id="site-navbar" class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/70 transition-shadow">
    <nav class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <!-- Brand -->
        <a href="<?= url('/') ?>" id="nav-brand" class="flex items-center gap-2.5 group">
            <span class="w-9 h-9 rounded-md bg-white border border-slate-200 shadow-sm p-1 flex items-center justify-center group-hover:scale-105 transition-transform">
                <img src="<?= url('assets/logoumb.png') ?>" alt="Logo <?= $e($kodeUni) ?>" class="w-full h-full object-contain">
            </span>
            <span class="leading-tight">
                <span class="block font-heading font-bold text-slate-900 text-[13px]">SIM Mahasiswa <?= $e($kodeUni) ?></span>
                <span class="block text-[10px] font-semibold text-elegant-600 truncate max-w-[200px] sm:max-w-none"><?= $e($namaUni) ?></span>
            </span>
        </a>

        <!-- Menu Desktop -->
        <ul class="hidden md:flex items-center gap-1">
            <?php foreach ($navItems as $item): $active = $activePage === $item['key']; ?>
                <li>
                    <a href="<?= $item['href'] ?>" id="nav-<?= $item['key'] ?>"
                       class="nav-link relative inline-flex items-center gap-2 px-3.5 py-2 text-[13px] font-semibold rounded-md transition-colors <?= $active ? 'text-academic-700 is-active' : 'text-slate-600 hover:text-academic-700 hover:bg-slate-50' ?>">
                        <i class="fa-solid <?= $item['icon'] ?> text-academic-600 text-xs"></i>
                        <?= $item['label'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Aksi -->
        <div class="flex items-center gap-2">
            <a href="<?= url('/login') ?>" id="nav-cta"
               class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-md bg-academic-600 hover:bg-academic-700 text-white text-[13px] font-semibold shadow-sm shadow-academic-600/30 transition-all hover:-translate-y-0.5">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </a>

            <button type="button" id="nav-toggle" aria-label="Buka menu" aria-expanded="false"
                    class="md:hidden w-10 h-10 inline-flex items-center justify-center rounded-md text-slate-600 hover:bg-slate-100">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>
        </div>
    </nav>

    <!-- Menu Mobile -->
    <div id="nav-mobile" class="md:hidden hidden border-t border-slate-100 bg-white">
        <ul class="px-4 py-3 space-y-1">
            <?php foreach ($navItems as $item): $active = $activePage === $item['key']; ?>
                <li>
                    <a href="<?= $item['href'] ?>"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold <?= $active ? 'bg-academic-50 text-academic-700' : 'text-slate-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid <?= $item['icon'] ?> w-4 text-center"></i> <?= $item['label'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li class="pt-2">
                <a href="<?= url('/login') ?>"
                   class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-md bg-academic-600 text-white text-sm font-semibold">
                    <i class="fa-solid fa-right-to-bracket"></i> Login
                </a>
            </li>
        </ul>
    </div>
</header>
