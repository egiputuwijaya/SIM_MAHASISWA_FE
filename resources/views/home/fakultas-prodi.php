<?php
ob_start();

$fakultas = $landing['fakultas'] ?? [];
$stats    = $landing['stats'] ?? [];
$metaDescription = 'Daftar fakultas dan program studi di Universitas Muhammadiyah Bengkulu beserta jenjang dan jumlah mahasiswanya.';

$fmt = fn($v) => ($v === null || $v === '') ? '–' : number_format((int)$v, 0, ',', '.');
$e   = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
?>

<!-- Header halaman -->
<section class="hero-bg relative overflow-hidden">
    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-20 text-center reveal">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[11px] font-semibold text-elegant-300 mb-4">
            <i class="fa-solid fa-graduation-cap"></i> Akademik
        </span>
        <h1 class="font-heading font-extrabold text-white text-3xl sm:text-4xl">Fakultas &amp; Program Studi</h1>
        <p class="text-slate-300 mt-3 max-w-xl mx-auto text-[15px]">
            Telusuri <?= $fmt($stats['totalFakultas'] ?? null) ?> fakultas dan <?= $fmt($stats['totalProdi'] ?? null) ?> program studi yang tersedia.
        </p>

        <div class="max-w-md mx-auto mt-8 relative">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="search" id="search-prodi" placeholder="Cari fakultas atau program studi..."
                   class="w-full pl-11 pr-4 py-3 rounded-lg bg-white text-sm text-slate-800 shadow-lg focus:outline-none focus:ring-4 focus:ring-academic-300/40">
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
    <?php if (empty($fakultas)): ?>
        <div class="text-center bg-white border border-slate-200 rounded-xl py-16 px-6">
            <i class="fa-solid fa-circle-info text-3xl text-slate-300"></i>
            <p class="text-slate-500 mt-3 text-sm">Data fakultas belum tersedia saat ini.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-6 lg:grid-cols-2" id="fakultas-list">
            <?php foreach ($fakultas as $f): ?>
                <article class="fakultas-card reveal card-hover bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden"
                         data-search="<?= $e(strtolower(($f['namaFakultas'] ?? '') . ' ' . ($f['kodeFakultas'] ?? ''))) ?>">
                    <header class="flex items-start justify-between gap-4 p-6 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="w-11 h-11 rounded-lg bg-academic-50 text-academic-700 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-building-columns"></i>
                            </span>
                            <div>
                                <h2 class="font-heading font-bold text-slate-900"><?= $e($f['namaFakultas'] ?? '-') ?></h2>
                                <p class="text-xs text-slate-500 font-mono mt-0.5"><?= $e($f['kodeFakultas'] ?? '') ?></p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold">Mahasiswa</p>
                            <p class="font-heading font-bold text-lg text-slate-900"><?= $fmt($f['totalMahasiswa'] ?? null) ?></p>
                        </div>
                    </header>

                    <ul class="divide-y divide-slate-100">
                        <?php if (empty($f['programStudi'])): ?>
                            <li class="px-6 py-4 text-sm text-slate-400">Belum ada program studi.</li>
                        <?php else: foreach ($f['programStudi'] as $p): $aktif = ($p['statusAktif'] ?? 'Aktif') === 'Aktif'; ?>
                            <li class="prodi-row flex items-center justify-between gap-3 px-6 py-3.5 hover:bg-slate-50/70 transition-colors"
                                data-search="<?= $e(strtolower(($p['namaProgramStudi'] ?? '') . ' ' . ($p['kodeProgramStudi'] ?? ''))) ?>">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="px-2 py-0.5 rounded bg-slate-900 text-white text-[10px] font-bold shrink-0"><?= $e($p['jenjang'] ?? 'S1') ?></span>
                                    <span class="text-sm font-semibold text-slate-800 truncate"><?= $e($p['namaProgramStudi'] ?? '-') ?></span>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="text-xs text-slate-500"><i class="fa-solid fa-user-graduate text-slate-400"></i> <?= $fmt($p['totalMahasiswa'] ?? null) ?></span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border <?= $aktif ? 'bg-elegant-50 text-elegant-700 border-elegant-200' : 'bg-slate-100 text-slate-500 border-slate-200' ?>">
                                        <?= $e($p['statusAktif'] ?? 'Aktif') ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; endif; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </div>
        <p id="search-empty" class="hidden text-center text-sm text-slate-500 py-12">Tidak ada fakultas / program studi yang cocok.</p>
    <?php endif; ?>
</section>

<script>
(function () {
    var input = document.getElementById('search-prodi');
    if (!input) return;
    input.addEventListener('input', function () {
        var q = input.value.trim().toLowerCase();
        var anyVisible = false;
        document.querySelectorAll('.fakultas-card').forEach(function (card) {
            var fakMatch = card.getAttribute('data-search').indexOf(q) !== -1;
            var rows = card.querySelectorAll('.prodi-row');
            var rowMatch = false;
            rows.forEach(function (r) {
                var m = !q || fakMatch || r.getAttribute('data-search').indexOf(q) !== -1;
                r.classList.toggle('hidden', !m);
                if (m) rowMatch = true;
            });
            var show = !q || fakMatch || rowMatch;
            card.classList.toggle('hidden', !show);
            if (show) anyVisible = true;
        });
        var empty = document.getElementById('search-empty');
        if (empty) empty.classList.toggle('hidden', anyVisible);
    });
})();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
