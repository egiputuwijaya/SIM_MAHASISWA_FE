<?php
ob_start();
$metaDescription = 'Informasi pengembang, arsitektur, dan teknologi yang digunakan pada SIM Mahasiswa Universitas Muhammadiyah Bengkulu.';

$repos = [
    [
        'icon'  => 'fa-server',
        'name'  => 'sim-mhs-api',
        'role'  => 'Backend REST API',
        'desc'  => 'Node.js + Express 5, Prisma ORM, PostgreSQL (Neon), autentikasi JWT, RBAC, dan audit log.',
        'url'   => 'https://github.com/erxanz/sim-mhs-api',
    ],
    [
        'icon'  => 'fa-display',
        'name'  => 'SIM_MAHASISWA_FE',
        'role'  => 'Frontend Web',
        'desc'  => 'PHP Native MVC, Tailwind CSS, Alpine.js, terhubung ke REST API melalui ApiClient.',
        'url'   => 'https://github.com/egiputuwijaya/SIM_MAHASISWA_FE',
    ],
];

$stack = [
    ['fa-brands fa-php', 'PHP Native'], ['fa-brands fa-node-js', 'Node.js'], ['fa-solid fa-database', 'PostgreSQL'],
    ['fa-solid fa-diamond', 'Prisma ORM'], ['fa-solid fa-wind', 'Tailwind CSS'], ['fa-solid fa-key', 'JWT Auth'],
    ['fa-solid fa-cloud', 'Vercel'], ['fa-brands fa-github', 'GitHub'],
];
?>

<section class="hero-bg relative overflow-hidden">
    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-20 text-center reveal">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[11px] font-semibold text-elegant-300 mb-4">
            <i class="fa-solid fa-code"></i> Tim Pengembang
        </span>
        <h1 class="font-heading font-extrabold text-white text-3xl sm:text-4xl">About Dev</h1>
        <p class="text-slate-300 mt-3 max-w-xl mx-auto text-[15px]">
            SIM Mahasiswa dibangun dengan arsitektur terpisah antara frontend dan backend agar mudah dikembangkan dan diintegrasikan.
        </p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
    <h2 class="font-heading font-bold text-2xl text-slate-900 text-center reveal">Repositori Proyek</h2>
    <div class="grid gap-6 md:grid-cols-2 mt-10">
        <?php foreach ($repos as $r): ?>
            <a href="<?= $r['url'] ?>" target="_blank" rel="noopener"
               class="reveal card-hover group block bg-white rounded-xl border border-slate-100 shadow-sm p-7">
                <div class="flex items-center justify-between">
                    <span class="w-11 h-11 rounded-lg bg-slate-900 text-white flex items-center justify-center">
                        <i class="fa-solid <?= $r['icon'] ?>"></i>
                    </span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-slate-300 group-hover:text-academic-600 transition-colors"></i>
                </div>
                <p class="text-[11px] uppercase tracking-wider font-semibold text-elegant-600 mt-5"><?= $r['role'] ?></p>
                <h3 class="font-heading font-bold text-lg text-slate-900 font-mono"><?= $r['name'] ?></h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed"><?= $r['desc'] ?></p>
            </a>
        <?php endforeach; ?>
    </div>

    <h2 class="font-heading font-bold text-2xl text-slate-900 text-center mt-20 reveal">Teknologi yang Digunakan</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-10">
        <?php foreach ($stack as $i => $s): ?>
            <div class="reveal card-hover bg-white rounded-xl border border-slate-100 shadow-sm py-6 text-center" style="transition-delay:<?= $i * 0.04 ?>s">
                <i class="<?= $s[0] ?> text-2xl text-academic-700"></i>
                <p class="text-sm font-semibold text-slate-700 mt-3"><?= $s[1] ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
