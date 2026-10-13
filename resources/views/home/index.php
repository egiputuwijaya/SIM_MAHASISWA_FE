<?php
ob_start();

$stats = $landing['stats'] ?? [];
$uni   = $landing['universitas'] ?? null;
if (empty($uni)) {
    require_once __DIR__ . '/../../app/Controllers/HomeController.php';
    $uni = HomeController::loadProfileCache() ?? [];
}

$totalFakultas  = $stats['totalFakultas'] ?? null;
$totalProdi     = $stats['totalProdi'] ?? null;
$totalMhs       = $stats['totalMahasiswa'] ?? null;
$totalMhsAktif  = $stats['totalMahasiswaAktif'] ?? $totalMhs;

$kodeUni = $uni['kodeUniversitas'] ?? $uni['kode_universitas'] ?? 'UMB';
$namaUni = !empty($uni['namaUniversitas']) ? $uni['namaUniversitas'] : (!empty($uni['nama_universitas']) ? $uni['nama_universitas'] : 'Universitas Mercu Buana');

/** Render angka statistik (dengan animasi counter) atau tanda strip bila belum tersedia. */
$num = function ($v) {
    if ($v === null || $v === '') return '<span>&ndash;</span>';
    $n = (int)$v;
    return '<span data-count="' . $n . '">' . number_format($n, 0, ',', '.') . '</span>';
};

$metaDescription = 'Portal informasi akademik terintegrasi ' . $namaUni . ' untuk kelola data mahasiswa, program studi, dan fakultas.';


$pubUnivDir = dirname(__DIR__, 3) . '/public/assets/university';
$srcUnivDir = dirname(__DIR__, 3) . '/assets/university';
if (!is_dir($pubUnivDir)) {
    @mkdir($pubUnivDir, 0777, true);
}
$heroImageFiles = [];
if (is_dir($srcUnivDir)) {
    $scanned = scandir($srcUnivDir);
    foreach ($scanned as $f) {
        if ($f !== '.' && $f !== '..' && preg_match('/\.(jpe?g|png|webp|avif)$/i', $f)) {
            $heroImageFiles[] = $f;
            if (!file_exists($pubUnivDir . '/' . $f)) {
                @copy($srcUnivDir . '/' . $f, $pubUnivDir . '/' . $f);
            }
        }
    }
}
if (empty($heroImageFiles)) {
    $heroImageFiles = ['1.jpeg', '2.jpeg', '3.jpeg', '4.jpg', '5.jpeg'];
}
sort($heroImageFiles);
$heroImages = array_map(fn($f) => url('assets/university/' . $f), $heroImageFiles);

$pubPersonDir = dirname(__DIR__, 3) . '/public/assets/person';
$srcPersonDir = dirname(__DIR__, 3) . '/assets/person';
if (!is_dir($pubPersonDir)) {
    @mkdir($pubPersonDir, 0777, true);
}
$personImageFiles = [];
if (is_dir($srcPersonDir)) {
    $scannedPerson = scandir($srcPersonDir);
    foreach ($scannedPerson as $f) {
        if ($f !== '.' && $f !== '..' && preg_match('/\.(jpe?g|png|webp|avif)$/i', $f)) {
            $personImageFiles[] = $f;
            if (!file_exists($pubPersonDir . '/' . $f)) {
                @copy($srcPersonDir . '/' . $f, $pubPersonDir . '/' . $f);
            }
        }
    }
}
if (empty($personImageFiles)) {
    $personImageFiles = ['1.jpeg', '2.jpeg', '3.jpeg'];
}
sort($personImageFiles);
$personImages = array_map(fn($f) => url('assets/person/' . $f), $personImageFiles);

// Data Pengembang & Repositori GitHub (3 Pengembang sesuai tampilan inspirasi)
$developerCards = [
    [
        'name'        => 'Egi Putu Wijaya',
        'desc'        => 'Bertanggung jawab atas arsitektur sistem, basis data, dan antarmuka pengguna secara keseluruhan.',
        'github_url'  => 'https://github.com/egiputuwijaya',
    ],
    [
        'name'        => 'Khairul Pratama',
        'desc'        => 'Fokus pada perancangan basis data, integrasi sistem, dan manajemen keamanan backend.',
        'github_url'  => 'https://github.com/Pratama12340',
    ],
    [
        'name'        => 'Rahman Badio',
        'desc'        => 'Membangun komponen antarmuka yang responsif, modern, dan memberikan pengalaman pengguna yang optimal.',
        'github_url'  => 'https://github.com/erxanz',
    ],
];


$features = [
    [
        'icon'  => 'fa-laptop-code',
        'title' => 'Akses Akademik',
        'desc'  => 'Akses terpadu untuk informasi fakultas, program studi, dan data kependudukan mahasiswa secara responsif.',
        'items' => ['Informasi terstruktur', 'Data terverifikasi', 'Akses multi-peran'],
    ],
    [
        'icon'  => 'fa-database',
        'title' => 'Transparansi Data',
        'desc'  => 'Seluruh data disajikan secara akurat dan konsisten berdasarkan basis data yang dapat dipertanggungjawabkan.',
        'items' => ['Data real-time', 'Konsistensi antar-unit', 'Tampilan mudah dipahami'],
    ],
    [
        'icon'  => 'fa-shield-halved',
        'title' => 'Audit System',
        'desc'  => 'Setiap aktivitas login dan proses administrasi dicatat otomatis untuk menjamin akuntabilitas sistem.',
        'items' => ['Log otomatis', 'Jejak pengguna tersimpan', 'Monitoring administrator'],
    ],
];
?>

<style>
/* Animasi Sinematik Ken Burns untuk Slideshow Kampus */
@keyframes kenBurnsSlow {
    0% {
        transform: scale(1) translate(0, 0);
    }
    50% {
        transform: scale(1.08) translate(-1%, -1%);
    }
    100% {
        transform: scale(1.12) translate(0.5%, -0.5%);
    }
}

.hero-slide {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0;
    transform: scale(1);
    transition: opacity 1400ms cubic-bezier(0.4, 0, 0.2, 1);
    will-change: transform, opacity;
    filter: brightness(1) contrast(1.02);
}

.hero-slide.is-active {
    opacity: 1;
    z-index: 1;
    animation: kenBurnsSlow 9s ease-in-out infinite alternate;
}

.hero-slide.was-active {
    opacity: 0;
    z-index: 0;
    transition: opacity 1400ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* Lapisan Overlay Biru Semi-Transparan (Opasiti Lebih Terang agar Foto Tampak Jelas) */
.hero-blue-overlay {
    position: absolute;
    inset: 0;
    z-index: 2;
    pointer-events: none;
    /* Gradasi biru royal semi-transparan yang lebih cerah (~42% - 54%) */
    background: linear-gradient(135deg, rgba(14, 42, 105, 0.52) 0%, rgba(24, 70, 160, 0.42) 50%, rgba(12, 36, 92, 0.54) 100%);
}

.hero-blue-overlay::before {
    content: '';
    position: absolute;
    inset: 0;
    /* Efek tone warna biru serasi yang lembut */
    background-color: #1e40af;
    mix-blend-mode: color;
    opacity: 0.15;
}

.hero-blue-overlay::after {
    content: '';
    position: absolute;
    inset: 0;
    /* Vignette halus di pinggiran tanpa menggelapkan tengah */
    background: radial-gradient(ellipse at center, transparent 45%, rgba(6, 18, 48, 0.35) 100%);
}

@media (prefers-reduced-motion: reduce) {
    .hero-slide.is-active {
        animation: none;
        transform: scale(1);
    }
}

/* Styling Slider Foto Tim Pengembang */
.team-slide {
    position: absolute;
    inset: 0;
    opacity: 0;
    transform: scale(1.04);
    transition: opacity 800ms cubic-bezier(0.4, 0, 0.2, 1), transform 1000ms cubic-bezier(0.25, 1, 0.5, 1);
    pointer-events: none;
}
.team-slide.is-active {
    opacity: 1;
    transform: scale(1);
    pointer-events: auto;
    z-index: 2;
}
.team-thumb {
    transition: all 0.2s ease;
}
.team-thumb.is-active {
    border-color: #3b82f6 !important;
    background-color: rgba(59, 130, 246, 0.22) !important;
    opacity: 1 !important;
}
</style>

<!-- ================= HERO SECTION (FULLSCREEN & CINEMATIC SLIDESHOW) ================= -->
<section id="hero" class="relative overflow-hidden min-h-[calc(100vh-4rem)] flex flex-col justify-between bg-slate-950">

    <!-- 1. Background Image Slideshow with Ken Burns Effect (Tanpa Kotak-Kotak) -->
    <div id="hero-slider" class="absolute inset-0 overflow-hidden pointer-events-none select-none z-0">
        <?php foreach ($heroImages as $idx => $imgSrc): ?>
            <div class="hero-slide absolute inset-0 bg-cover bg-center <?= $idx === 0 ? 'opacity-100 is-active' : 'opacity-0' ?>"
                 style="background-image: url('<?= $imgSrc ?>');"
                 data-slide-index="<?= $idx ?>"
                 role="img"
                 aria-label="Foto Kampus <?= $idx + 1 ?>">
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 2. Overlay Biru Semi-Transparan (Lebih Terang agar Foto Mudah Terlihat) -->
    <div aria-hidden="true" class="hero-blue-overlay"
         style="position:absolute; inset:0; z-index:2; pointer-events:none; background: linear-gradient(135deg, rgba(14, 42, 105, 0.52) 0%, rgba(24, 70, 160, 0.42) 50%, rgba(12, 36, 92, 0.54) 100%);">
    </div>

    <!-- 3. Hero Core Content (Centered Vertically) -->
    <div class="relative z-10 w-full flex-1 flex items-center">
        <div class="max-w-6xl mx-auto w-full px-4 sm:px-6 py-14 md:py-20 grid lg:grid-cols-2 gap-12 lg:gap-14 items-center">
            
            <!-- Left Column: Title & Actions -->
            <div class="reveal">
                <h1 class="font-heading font-extrabold text-white text-4xl sm:text-5xl lg:text-6xl leading-[1.14] tracking-tight drop-shadow-md">
                    Selamat Datang di<br>
                    <span class="text-emerald-400 font-extrabold">
                        SIM Mahasiswa <?= htmlspecialchars($kodeUni) ?>
                    </span>
                </h1>

                <p class="text-slate-200 text-base sm:text-lg leading-relaxed mt-6 max-w-xl font-normal drop-shadow">
                    Portal informasi akademik terintegrasi untuk kelola data mahasiswa, program studi, dan fakultas
                    cepat, transparan, serta tercatat dalam sistem audit universitas.
                </p>

                <div class="flex flex-wrap items-center gap-3.5 mt-9">
                    <a href="<?= url('/fakultas-prodi') ?>" id="hero-btn-prodi"
                       class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-lg bg-white text-academic-950 text-sm font-bold shadow-xl shadow-black/30 hover:-translate-y-0.5 hover:shadow-2xl hover:bg-slate-100 transition-all">
                        <i class="fa-solid fa-building-columns text-academic-600"></i> Lihat Program Studi
                    </a>
                    <a href="<?= url('/login') ?>" id="hero-btn-login"
                       class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-lg bg-white/10 hover:bg-white/20 border border-white/30 backdrop-blur-md text-white text-sm font-bold hover:-translate-y-0.5 shadow-lg shadow-black/15 transition-all">
                        <i class="fa-solid fa-right-to-bracket text-emerald-300"></i> Login Portal
                    </a>
                </div>
            </div>

            <!-- Right Column: Ringkasan Akademik Glass Card -->
            <div class="reveal lg:justify-self-end w-full max-w-md floaty">
                <div class="glass relative rounded-2xl p-7 shadow-2xl shadow-black/40 border border-white/25 overflow-hidden">
                    <!-- Accent top glow line -->
                    <div class="absolute top-0 inset-x-0 h-[2px] bg-gradient-to-r from-transparent via-emerald-400 to-transparent"></div>

                    <div class="flex items-center gap-3.5 pb-6 border-b border-white/15">
                        <span class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/25 to-academic-500/25 border border-white/20 text-emerald-300 flex items-center justify-center text-xl shadow-inner">
                            <i class="fa-solid fa-chart-line"></i>
                        </span>
                        <div>
                            <p class="text-white font-bold text-base tracking-wide">Ringkasan Akademik</p>
                            <p class="text-slate-300 text-xs mt-0.5">Data real-time dari basis data SIM</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 divide-x divide-white/15 pt-7 text-center">
                        <div class="px-2">
                            <p class="font-heading font-black text-white text-3xl sm:text-4xl tracking-tight drop-shadow-sm"><?= $num($totalFakultas) ?></p>
                            <p class="text-[10px] tracking-widest text-emerald-300 font-bold uppercase mt-2">FAKULTAS</p>
                        </div>
                        <div class="px-2">
                            <p class="font-heading font-black text-white text-3xl sm:text-4xl tracking-tight drop-shadow-sm"><?= $num($totalProdi) ?></p>
                            <p class="text-[10px] tracking-widest text-emerald-300 font-bold uppercase mt-2">PRODI</p>
                        </div>
                        <div class="px-2">
                            <p class="font-heading font-black text-white text-3xl sm:text-4xl tracking-tight drop-shadow-sm"><?= $num($totalMhsAktif) ?></p>
                            <p class="text-[10px] tracking-widest text-emerald-300 font-bold uppercase mt-2">MHS AKTIF</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Hero Bottom Navigation & Scroll Indicator -->
    <div class="relative z-10 w-full max-w-6xl mx-auto px-4 sm:px-6 pb-14 pt-2 flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Slideshow Indicators & Counter -->
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-black/40 backdrop-blur-md px-3.5 py-2 rounded-full border border-white/15" id="hero-dots">
                <?php foreach ($heroImages as $idx => $imgSrc): ?>
                    <button type="button"
                            class="hero-dot-btn transition-all duration-300 h-2 rounded-full <?= $idx === 0 ? 'w-8 bg-emerald-400' : 'w-2 bg-white/40 hover:bg-white/70' ?>"
                            data-target-slide="<?= $idx ?>"
                            aria-label="Tampilkan foto kampus <?= $idx + 1 ?>">
                    </button>
                <?php endforeach; ?>
                <span class="text-[11px] font-mono font-semibold text-slate-300 ml-2 select-none" id="hero-slide-counter">1 / <?= count($heroImages) ?></span>
            </div>

            <!-- Arrows -->
            <div class="flex items-center gap-1.5">
                <button type="button" id="hero-prev" aria-label="Foto kampus sebelumnya"
                        class="w-8 h-8 rounded-full bg-black/35 hover:bg-black/60 border border-white/15 text-white flex items-center justify-center text-xs transition-all hover:scale-105 active:scale-95">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button type="button" id="hero-next" aria-label="Foto kampus selanjutnya"
                        class="w-8 h-8 rounded-full bg-black/35 hover:bg-black/60 border border-white/15 text-white flex items-center justify-center text-xs transition-all hover:scale-105 active:scale-95">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Scroll down prompt -->
        <a href="#statistik" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-300 hover:text-white transition-colors group">
            <span>Scroll ke bawah</span>
            <span class="w-6 h-6 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-[10px] group-hover:translate-y-0.5 transition-transform text-emerald-400">
                <i class="fa-solid fa-arrow-down"></i>
            </span>
        </a>
    </div>
</section>


<!-- ================= KARTU STATISTIK ================= -->
<section id="statistik" class="relative z-20 -mt-10 sm:-mt-12 max-w-6xl mx-auto px-4 sm:px-6 scroll-mt-20">
    <div class="grid gap-5 md:grid-cols-3">
        <article class="reveal card-hover bg-white rounded-xl border border-slate-100 shadow-xl shadow-slate-900/5 p-6">
            <span class="w-10 h-10 rounded-lg bg-academic-50 text-academic-700 flex items-center justify-center mb-5">
                <i class="fa-solid fa-building-columns"></i>
            </span>
            <h2 class="font-heading font-bold text-xl text-slate-900"><?= $num($totalFakultas) ?> Fakultas</h2>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Tersedia berbagai pilihan fakultas unggulan dengan fasilitas lengkap yang siap mendukung seluruh aktivitas akademik Anda.</p>
        </article>
        <article class="reveal card-hover bg-white rounded-xl border border-slate-100 shadow-xl shadow-slate-900/5 p-6" style="transition-delay:.08s">
            <span class="w-10 h-10 rounded-lg bg-elegant-50 text-elegant-700 flex items-center justify-center mb-5">
                <i class="fa-solid fa-book-open"></i>
            </span>
            <h2 class="font-heading font-bold text-xl text-slate-900"><?= $num($totalProdi) ?> Program Studi</h2>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Pilihan program studi yang beragam dan terspesialisasi, dirancang khusus untuk menjawab tantangan dunia kerja masa depan.</p>
        </article>
        <article class="reveal card-hover bg-white rounded-xl border border-slate-100 shadow-xl shadow-slate-900/5 p-6" style="transition-delay:.16s">
            <span class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-5">
                <i class="fa-solid fa-user-graduate"></i>
            </span>
            <h2 class="font-heading font-bold text-xl text-slate-900"><?= $num($totalMhs) ?> Mahasiswa</h2>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Ribuan mahasiswa aktif yang tergabung dalam lingkungan akademik dinamis, inovatif, dan berprestasi tinggi.</p>
        </article>
    </div>
</section>

<!-- ================= FITUR ================= -->
<section id="fitur" class="max-w-6xl mx-auto px-4 sm:px-6 py-24 scroll-mt-20">
    <div class="text-center max-w-2xl mx-auto reveal">
        <h2 class="font-heading font-bold text-3xl text-slate-900">Fitur Utama SIM Mahasiswa</h2>
        <p class="text-slate-500 mt-3 text-[15px] leading-relaxed">
            Dibangun dengan pendekatan akademik yang modern untuk mendukung proses administrasi dan informasi kampus secara terstruktur.
        </p>
    </div>

    <div class="grid gap-5 md:grid-cols-3 mt-14">
        <?php foreach ($features as $i => $f): ?>
            <article class="reveal card-hover bg-white rounded-xl border border-slate-100 shadow-sm p-7" style="transition-delay:<?= $i * 0.08 ?>s">
                <span class="w-11 h-11 rounded-lg bg-slate-900 text-white flex items-center justify-center mb-5">
                    <i class="fa-solid <?= $f['icon'] ?>"></i>
                </span>
                <h3 class="font-heading font-bold text-lg text-slate-900"><?= $f['title'] ?></h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed"><?= $f['desc'] ?></p>
                <ul class="mt-5 space-y-2.5">
                    <?php foreach ($f['items'] as $it): ?>
                        <li class="flex items-center gap-2.5 text-sm text-slate-600">
                            <i class="fa-solid fa-check text-elegant-500 text-xs"></i> <?= $it ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<!-- ================= TIM PENGEMBANG ================= -->
<section id="tim-pengembang" class="max-w-6xl mx-auto px-4 sm:px-6 pt-16 pb-24 scroll-mt-20">
    <div class="text-center max-w-3xl mx-auto reveal mb-10">
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-academic-50 border border-academic-100 text-academic-700 text-xs font-semibold tracking-wide mb-3">
            <i class="fa-solid fa-code-branch text-academic-600"></i> Tim Pengembang SIM Mahasiswa
        </span>
        <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">Kreator di Balik SIM Mahasiswa</h2>
        <p class="text-slate-500 mt-3 text-[15px] sm:text-base leading-relaxed">
            Sistem Informasi Manajemen Mahasiswa ini dirancang dan dikembangkan dengan dedikasi tinggi oleh mahasiswa Fakultas Teknik Universitas Muhammadiyah Bengkulu melalui kolaborasi lintas teknologi modern.
        </p>
    </div>

    <!-- 1. Showcase Foto Tim (Slideshow / Carousel Animasi Profesional) -->
    <div class="reveal">
        <div class="relative rounded-[28px] sm:rounded-[36px] overflow-hidden bg-slate-950 border border-slate-200/90 shadow-2xl shadow-slate-900/10" id="team-carousel">
            <!-- Frame Gambar Utama dengan Aspect Ratio Proporsional & Tinggi Nyaman -->
            <div class="relative w-full h-[360px] sm:h-[440px] md:h-[500px] overflow-hidden bg-slate-900">
                <?php foreach ($personImages as $pIdx => $pSrc): ?>
                    <div class="team-slide <?= $pIdx === 0 ? 'is-active' : '' ?>" data-team-index="<?= $pIdx ?>">
                        <img src="<?= $pSrc ?>" alt="Dokumentasi Tim Pengembang <?= $pIdx + 1 ?>" class="w-full h-full object-cover object-top sm:object-center select-none" loading="lazy">
                        <!-- Lapisan Gradasi Sinematik -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-slate-950/20"></div>
                    </div>
                <?php endforeach; ?>


                <!-- Caption Halus di Bawah Foto (Tengah Kiri) -->
                <div class="hidden sm:block absolute bottom-36 sm:bottom-40 left-6 z-10 text-white max-w-md">
                    <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-emerald-400 mb-0.5">
                        Solidaritas &amp; Dedikasi
                    </span>
                    <h3 class="font-heading font-bold text-lg sm:text-xl text-white drop-shadow leading-tight">
                        Kolaborasi Pengembang SIM Mahasiswa
                    </h3>
                </div>
            </div>
        </div>

        <!-- 2. Tiga Kartu Pengembang (Floating Overlap Persis Seperti Gambar Inspirasi) -->
        <div class="relative z-20 -mt-24 sm:-mt-28 md:-mt-32 px-4 sm:px-6">
            <div class="grid gap-6 md:grid-cols-3">
                <?php foreach ($developerCards as $i => $dev): ?>
                    <article class="reveal card-hover bg-white rounded-[26px] sm:rounded-[32px] border border-slate-100 shadow-2xl shadow-slate-900/10 hover:shadow-2xl hover:border-academic-300 transition-all duration-300 p-8 sm:p-9 flex flex-col justify-between items-center text-center group" style="transition-delay:<?= $i * 0.08 ?>s">
                        <div class="flex-1 flex flex-col items-center w-full">
                            <h3 class="font-heading font-extrabold text-xl sm:text-2xl text-slate-900 tracking-tight mb-3 group-hover:text-academic-700 transition-colors">
                                <?= $dev['name'] ?>
                            </h3>
                            <p class="text-slate-500 text-sm leading-relaxed mb-8">
                                <?= $dev['desc'] ?>
                            </p>
                        </div>

                        <!-- Tombol Bulat GitHub di Bawah Kartu -->
                        <div class="pt-2">
                            <a href="<?= $dev['github_url'] ?>" target="_blank" rel="noopener"
                               title="Kunjungi GitHub <?= $dev['name'] ?>" aria-label="GitHub <?= $dev['name'] ?>"
                               class="w-12 h-12 rounded-full bg-slate-50 hover:bg-slate-900 text-slate-400 hover:text-white flex items-center justify-center transition-all duration-300 border border-slate-200/80 shadow-sm hover:scale-110 hover:shadow-md">
                                <i class="fa-brands fa-github text-xl"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ================= CTA ================= -->
<section id="cta" class="max-w-6xl mx-auto px-4 sm:px-6 pb-24">
    <div class="reveal relative overflow-hidden max-w-3xl mx-auto text-center bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-14">
        <div aria-hidden="true" class="absolute -top-20 -right-20 w-56 h-56 rounded-full bg-academic-100/60 blur-3xl"></div>
        <div aria-hidden="true" class="absolute -bottom-20 -left-20 w-56 h-56 rounded-full bg-elegant-100/60 blur-3xl"></div>
        <div class="relative">
            <h2 class="font-heading font-bold text-2xl sm:text-3xl text-slate-900">Siap Mengakses Portal Akademik?</h2>
            <p class="text-slate-500 mt-3 text-[15px] leading-relaxed max-w-xl mx-auto">
                Masuk ke SIM Mahasiswa <?= htmlspecialchars($kodeUni) ?> untuk mengelola data akademik secara resmi,
                atau telusuri daftar fakultas dan program studi kami terlebih dahulu.
            </p>
            <div class="flex flex-wrap justify-center gap-3 mt-8">
                <a href="<?= url('/fakultas-prodi') ?>" id="cta-btn-prodi"
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-md border border-slate-300 text-slate-700 text-sm font-bold hover:border-academic-400 hover:text-academic-700 transition-colors">
                    <i class="fa-solid fa-building-columns"></i> Jelajahi Program Studi
                </a>
                <a href="<?= url('/login') ?>" id="cta-btn-login"
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-md bg-academic-600 hover:bg-academic-700 text-white text-sm font-bold shadow-md shadow-academic-600/30 hover:-translate-y-0.5 transition-all">
                    <i class="fa-solid fa-right-to-bracket"></i> Login Sekarang
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Script Controller Slideshow Background Hero -->
<script>
(function () {
    var slides = document.querySelectorAll('.hero-slide');
    var dots = document.querySelectorAll('.hero-dot-btn');
    var counterEl = document.getElementById('hero-slide-counter');
    var prevBtn = document.getElementById('hero-prev');
    var nextBtn = document.getElementById('hero-next');
    var totalSlides = slides.length;
    if (totalSlides <= 1) return;

    var currentIndex = 0;
    var slideDuration = 6000; // 6 detik per gambar
    var timer = null;

    function showSlide(index) {
        if (index === currentIndex) return;
        var prevIndex = currentIndex;
        currentIndex = (index + totalSlides) % totalSlides;

        slides[prevIndex].classList.remove('is-active');
        slides[prevIndex].classList.add('was-active');

        slides[currentIndex].classList.remove('was-active');
        slides[currentIndex].classList.add('is-active');

        // Update indikator dots
        dots.forEach(function (dot, i) {
            if (i === currentIndex) {
                dot.classList.remove('w-2', 'bg-white/40');
                dot.classList.add('w-8', 'bg-emerald-400');
            } else {
                dot.classList.remove('w-8', 'bg-emerald-400');
                dot.classList.add('w-2', 'bg-white/40');
            }
        });

        if (counterEl) {
            counterEl.textContent = (currentIndex + 1) + ' / ' + totalSlides;
        }
    }

    function nextSlide() {
        showSlide(currentIndex + 1);
    }

    function prevSlide() {
        showSlide(currentIndex - 1);
    }

    function startAutoPlay() {
        stopAutoPlay();
        timer = setInterval(nextSlide, slideDuration);
    }

    function stopAutoPlay() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
            var target = parseInt(this.getAttribute('data-target-slide'), 10);
            showSlide(target);
            startAutoPlay();
        });
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            prevSlide();
            startAutoPlay();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            nextSlide();
            startAutoPlay();
        });
    }

    // Pause autoplay saat kursor berada di area hero
    var heroEl = document.getElementById('hero');
    if (heroEl) {
        heroEl.addEventListener('mouseenter', stopAutoPlay);
        heroEl.addEventListener('mouseleave', startAutoPlay);

        // Dukungan touch swipe untuk perangkat layar sentuh
        var touchStartX = 0;
        heroEl.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        heroEl.addEventListener('touchend', function (e) {
            var touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50) {
                nextSlide();
                startAutoPlay();
            } else if (touchEndX - touchStartX > 50) {
                prevSlide();
                startAutoPlay();
            }
        }, { passive: true });
    }

    startAutoPlay();
})();
</script>

<!-- Script Controller Carousel Tim Pengembang -->
<script>
(function () {
    var teamSlides = document.querySelectorAll('.team-slide');
    var teamCounter = document.getElementById('team-counter');
    var teamPrev = document.getElementById('team-prev');
    var teamNext = document.getElementById('team-next');
    var teamCarousel = document.getElementById('team-carousel');
    var totalTeamSlides = teamSlides.length;
    if (totalTeamSlides <= 1) return;

    var currentTeamIdx = 0;
    var teamInterval = 5000; // 5 detik per pergantian
    var teamTimer = null;

    function showTeamSlide(index) {
        if (index === currentTeamIdx) return;
        var prevIdx = currentTeamIdx;
        currentTeamIdx = (index + totalTeamSlides) % totalTeamSlides;

        teamSlides[prevIdx].classList.remove('is-active');
        teamSlides[currentTeamIdx].classList.add('is-active');

        if (teamCounter) {
            teamCounter.textContent = (currentTeamIdx + 1) + ' / ' + totalTeamSlides;
        }
    }

    function nextTeamSlide() {
        showTeamSlide(currentTeamIdx + 1);
    }

    function prevTeamSlide() {
        showTeamSlide(currentTeamIdx - 1);
    }

    function startTeamAutoPlay() {
        stopTeamAutoPlay();
        teamTimer = setInterval(nextTeamSlide, teamInterval);
    }

    function stopTeamAutoPlay() {
        if (teamTimer) {
            clearInterval(teamTimer);
            teamTimer = null;
        }
    }

    if (teamPrev) {
        teamPrev.addEventListener('click', function () {
            prevTeamSlide();
            startTeamAutoPlay();
        });
    }

    if (teamNext) {
        teamNext.addEventListener('click', function () {
            nextTeamSlide();
            startTeamAutoPlay();
        });
    }

    if (teamCarousel) {
        teamCarousel.addEventListener('mouseenter', stopTeamAutoPlay);
        teamCarousel.addEventListener('mouseleave', startTeamAutoPlay);

        var touchStartX = 0;
        teamCarousel.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        teamCarousel.addEventListener('touchend', function (e) {
            var touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 45) {
                nextTeamSlide();
                startTeamAutoPlay();
            } else if (touchEndX - touchStartX > 45) {
                prevTeamSlide();
                startTeamAutoPlay();
            }
        }, { passive: true });
    }

    startTeamAutoPlay();
})();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>

