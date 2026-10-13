<?php
/**
 * Partial: Footer publik (dipakai oleh semua halaman publik).
 * Data alamat & kontak diambil dari database institusi melalui API ($landing['universitas']).
 */
$uni = $landing['universitas'] ?? null;

if (empty($uni)) {
    require_once __DIR__ . '/../../app/Controllers/HomeController.php';
    $uni = HomeController::loadProfileCache() ?? [];
}

$kodeUni   = $uni['kodeUniversitas'] ?? $uni['kode_universitas'] ?? 'UMB';
$namaUni   = !empty($uni['namaUniversitas']) ? $uni['namaUniversitas'] : (!empty($uni['nama_universitas']) ? $uni['nama_universitas'] : 'Universitas Mercu Buana');
$sloganUni = $uni['slogan'] ?? '';
$email     = $uni['email'] ?? '';
$telepon   = $uni['telepon'] ?? '';
$website   = $uni['website'] ?? '';

// Format alamat lengkap sesuai data institusi (Jalan, Kota, Provinsi Kode Pos)
$alamatJalan = trim((string)($uni['alamat'] ?? ''));
$wilayahArr  = array_filter([$uni['kota'] ?? '', $uni['provinsi'] ?? '']);
$wilayahStr  = implode(', ', $wilayahArr);
$kodePosStr  = trim((string)($uni['kodePos'] ?? $uni['kode_pos'] ?? ''));
if (!empty($kodePosStr)) {
    $wilayahStr = trim($wilayahStr . ' ' . $kodePosStr);
}

$alamatParts   = array_filter([$alamatJalan, $wilayahStr]);
$alamatLengkap = implode(', ', $alamatParts);

$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$websiteHref = $website ? (preg_match('~^https?://~i', $website) ? $website : 'https://' . $website) : '';
?>
<footer id="site-footer" class="bg-[#0b1530] text-slate-300">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-14 grid gap-10 md:grid-cols-3">

        <!-- Kolom 1: Identitas Profil Institusi -->
        <div>
            <div class="flex items-center gap-3 mb-5">
                <span class="w-10 h-10 rounded-md bg-white p-1 flex items-center justify-center shadow">
                    <img src="<?= url('assets/logoumb.png') ?>" alt="Logo <?= $e($kodeUni) ?>" class="w-full h-full object-contain">
                </span>
                <span class="leading-tight">
                    <span class="block font-heading font-bold text-white text-sm">SIM Mahasiswa <?= $e($kodeUni) ?></span>
                    <span class="block text-[11px] text-elegant-400 font-medium">Sistem Informasi Manajemen</span>
                </span>
            </div>
            <p class="font-semibold text-white text-sm"><?= $e($namaUni) ?></p>
            <?php if ($sloganUni): ?>
                <p class="text-xs italic text-slate-400 mt-1">&ldquo;<?= $e($sloganUni) ?>&rdquo;</p>
            <?php endif; ?>
            <p class="flex items-start gap-2 text-xs text-slate-400 mt-4 leading-relaxed">
                <i class="fa-solid fa-location-dot text-elegant-400 mt-0.5 shrink-0"></i>
                <span><?= $alamatLengkap ? $e($alamatLengkap) : '-' ?></span>
            </p>
        </div>

        <!-- Kolom 2: Tautan Cepat -->
        <div>
            <h3 class="flex items-center gap-3 text-xs font-bold tracking-[0.18em] text-white uppercase mb-5">
                <span class="w-6 h-px bg-elegant-400"></span> Tautan Cepat
            </h3>
            <ul class="space-y-3 text-sm">
                <li><a href="<?= url('/') ?>" class="footer-link"><i class="fa-solid fa-house"></i> Beranda</a></li>
                <li><a href="<?= url('/fakultas-prodi') ?>" class="footer-link"><i class="fa-solid fa-building-columns"></i> Fakultas &amp; Program Studi</a></li>
                <li><a href="<?= url('/login') ?>" class="footer-link"><i class="fa-solid fa-right-to-bracket"></i> Login Portal</a></li>
                <li><a href="<?= url('/#fitur') ?>" class="footer-link"><i class="fa-solid fa-layer-group"></i> Fitur SIM</a></li>
            </ul>
        </div>

        <!-- Kolom 3: Kontak Institusi dari API -->
        <div>
            <h3 class="flex items-center gap-3 text-xs font-bold tracking-[0.18em] text-white uppercase mb-5">
                <span class="w-6 h-px bg-elegant-400"></span> Kontak
            </h3>
            <ul class="space-y-4 text-sm">
                <li class="flex items-center gap-3">
                    <span class="contact-icon"><i class="fa-solid fa-envelope"></i></span>
                    <span>
                        <span class="block text-[10px] uppercase tracking-wider text-slate-500">Email</span>
                        <?php if ($email): ?>
                            <a href="mailto:<?= $e($email) ?>" class="text-white hover:text-elegant-400 transition-colors"><?= $e($email) ?></a>
                        <?php else: ?>
                            <span class="text-slate-400">-</span>
                        <?php endif; ?>
                    </span>
                </li>
                <li class="flex items-center gap-3">
                    <span class="contact-icon"><i class="fa-solid fa-phone"></i></span>
                    <span>
                        <span class="block text-[10px] uppercase tracking-wider text-slate-500">Telepon</span>
                        <?php if ($telepon): ?>
                            <a href="tel:<?= $e(preg_replace('/[^0-9+]/', '', $telepon)) ?>" class="text-white hover:text-elegant-400 transition-colors"><?= $e($telepon) ?></a>
                        <?php else: ?>
                            <span class="text-slate-400">-</span>
                        <?php endif; ?>
                    </span>
                </li>
                <li class="flex items-center gap-3">
                    <span class="contact-icon"><i class="fa-solid fa-globe"></i></span>
                    <span>
                        <span class="block text-[10px] uppercase tracking-wider text-slate-500">Website</span>
                        <?php if ($website && $websiteHref): ?>
                            <a href="<?= $e($websiteHref) ?>" target="_blank" rel="noopener" class="text-white hover:text-elegant-400 transition-colors"><?= $e($website) ?></a>
                        <?php else: ?>
                            <span class="text-slate-400">-</span>
                        <?php endif; ?>
                    </span>
                </li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-500">
            <p>&copy; <?= date('Y') ?> <span class="text-slate-300 font-semibold"><?= $e($namaUni) ?></span>. Seluruh hak cipta dilindungi.</p>
            <p class="flex items-center gap-1.5">
                <i class="fa-solid fa-shield-halved text-elegant-400"></i>
                Dibangun dengan <span class="text-elegant-400 font-semibold">PHP Native + REST API</span> untuk layanan akademik
            </p>
        </div>
    </div>
</footer>

<!-- Tombol kembali ke atas -->
<button type="button" id="btn-scroll-top" aria-label="Kembali ke atas"
        class="fixed bottom-5 right-5 z-40 w-10 h-10 rounded-full bg-academic-600 hover:bg-academic-700 text-white shadow-lg shadow-academic-600/40 flex items-center justify-center opacity-0 pointer-events-none translate-y-3 transition-all">
    <i class="fa-solid fa-arrow-up text-sm"></i>
</button>

