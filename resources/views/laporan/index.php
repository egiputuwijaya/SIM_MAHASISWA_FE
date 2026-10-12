<?php 
ob_start(); 
$title = 'Laporan Data Mahasiswa';
?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<section class="mb-8">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="font-heading font-bold text-2xl text-slate-900">Rekapitulasi Mahasiswa</h2>
            <p class="text-sm text-slate-500 mt-1">Laporan analitik, grafik, dan cetak data akademik.</p>
        </div>
        <div class="flex items-center gap-2">
            <?php 
                $qs = http_build_query([
                    'status'   => $fStatus ?? '',
                    'id_prodi' => $fProdi ?? '',
                    'tahun'    => $fTahun ?? '',
                    'q'        => $fQ ?? '',
                ]);
            ?>
            <a href="<?= url('/laporan/export-csv?' . $qs) ?>" 
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-md transition-colors focus:ring-4 focus:ring-emerald-200 inline-flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>
            <a href="<?= url('/laporan/cetak-pdf?' . $qs) ?>" target="_blank" 
               class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-md transition-colors focus:ring-4 focus:ring-rose-200 inline-flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-print"></i> Cetak PDF
            </a>
        </div>
    </div>

    <!-- Ringkasan Statistik 4 Kartu -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?php 
            $stsData = [
                'Aktif'    => ['bg' => 'bg-emerald-50 text-emerald-600', 'ic' => 'fa-user-check'],
                'Cuti'     => ['bg' => 'bg-amber-50 text-amber-600',     'ic' => 'fa-pause'],
                'Lulus'    => ['bg' => 'bg-blue-50 text-blue-600',       'ic' => 'fa-graduation-cap'],
                'Drop Out' => ['bg' => 'bg-rose-50 text-rose-600',       'ic' => 'fa-user-xmark'],
            ];
            foreach ($stsData as $st => $cfg): 
                $tot = $stats[$st] ?? 0;
        ?>
        <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider"><?= $st ?></p>
                <h4 class="font-heading font-bold text-2xl text-slate-800 mt-1"><?= number_format($tot) ?></h4>
            </div>
            <div class="w-10 h-10 rounded-full flex items-center justify-center <?= $cfg['bg'] ?>">
                <i class="fa-solid <?= $cfg['ic'] ?> text-lg"></i>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Panel Kiri: Grafik Distribusi -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-6 flex flex-col">
            <h3 class="font-bold text-slate-800 mb-4">Distribusi Status Mahasiswa</h3>
            <div class="flex-1 min-h-[260px] relative w-full flex justify-center items-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Panel Kanan: Filter & Tabel Data -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-slate-100 flex flex-col overflow-hidden">
            <!-- Filter Toolbar -->
            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                <form action="<?= url('/laporan') ?>" method="GET" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Prodi</label>
                        <select name="id_prodi" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                            <option value="">Semua Prodi</option>
                            <?php foreach ($prodiList as $pl): ?>
                                <option value="<?= htmlspecialchars($pl['id']) ?>" <?= ($fProdi == $pl['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pl['namaProgramStudi'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="w-32">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                            <option value="">Semua</option>
                            <option value="Aktif" <?= $fStatus === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Cuti" <?= $fStatus === 'Cuti' ? 'selected' : '' ?>>Cuti</option>
                            <option value="Lulus" <?= $fStatus === 'Lulus' ? 'selected' : '' ?>>Lulus</option>
                            <option value="Drop Out" <?= $fStatus === 'Drop Out' ? 'selected' : '' ?>>Drop Out</option>
                        </select>
                    </div>

                    <div class="w-28">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Thn. Masuk</label>
                        <input type="number" name="tahun" value="<?= htmlspecialchars($fTahun ?? '') ?>" placeholder="Contoh: 2026" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-md transition-colors focus:ring-2 focus:ring-blue-200 shadow-sm cursor-pointer">
                            Filter
                        </button>
                        <?php if (!empty($fStatus) || !empty($fProdi) || !empty($fTahun) || !empty($fQ)): ?>
                            <a href="<?= url('/laporan') ?>" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold rounded-md transition-colors" title="Reset Filter">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Tabel Data Mahasiswa -->
            <div class="overflow-y-auto max-h-[420px]">
                <table class="w-full text-sm border-b border-slate-100 text-left">
                    <thead class="bg-slate-50 text-slate-600 sticky top-0 shadow-sm z-10 text-xs">
                        <tr>
                            <th class="text-center font-semibold px-4 py-2.5 w-14">No</th>
                            <th class="text-left font-semibold px-4 py-2.5">NPM & Nama</th>
                            <th class="text-left font-semibold px-4 py-2.5">Program Studi</th>
                            <th class="text-center font-semibold px-4 py-2.5 w-28">Tahun Masuk</th>
                            <th class="text-center font-semibold px-4 py-2.5 w-24">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($mahasiswa)): ?>
                            <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">Tidak ada data rekapitulasi ditemukan.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($mahasiswa as $m): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-4 py-3 text-center text-slate-500 font-mono text-xs"><?= $no++ ?></td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900"><?= htmlspecialchars($m['namaMahasiswa'] ?? $m['nama_mahasiswa'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                                        <div class="text-xs font-mono text-slate-500 mt-0.5"><?= htmlspecialchars($m['npm'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 text-xs font-semibold uppercase">
                                        <?= htmlspecialchars($m['namaProgramStudi'] ?? $m['nama_program_studi'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-4 py-3 text-center text-slate-600 font-mono text-xs">
                                        <?= !empty($m['tanggalMasuk']) ? date('Y', strtotime($m['tanggalMasuk'])) : (!empty($m['tanggal_masuk']) ? date('Y', strtotime($m['tanggal_masuk'])) : '-') ?>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <?php 
                                            $st = $m['statusMahasiswa'] ?? $m['status_mahasiswa'] ?? 'Aktif';
                                            $bBg = 'bg-slate-100 text-slate-600 border-slate-200';
                                            if ($st === 'Aktif') $bBg = 'bg-green-100 text-green-700 border-green-200';
                                            elseif ($st === 'Lulus') $bBg = 'bg-blue-100 text-blue-700 border-blue-200';
                                            elseif ($st === 'Cuti') $bBg = 'bg-amber-100 text-amber-700 border-amber-200';
                                            elseif ($st === 'Drop Out') $bBg = 'bg-red-100 text-red-700 border-red-200';
                                        ?>
                                        <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold border <?= $bBg ?>">
                                            <?= htmlspecialchars($st) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Script Doughnut Chart -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('statusChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const dataVals = [
        <?= (int)($stats['Aktif'] ?? 0) ?>,
        <?= (int)($stats['Cuti'] ?? 0) ?>,
        <?= (int)($stats['Lulus'] ?? 0) ?>,
        <?= (int)($stats['Drop Out'] ?? 0) ?>
    ];
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Aktif', 'Cuti', 'Lulus', 'Drop Out'],
            datasets: [{
                data: dataVals,
                backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { 
                    position: 'bottom', 
                    labels: { 
                        boxWidth: 12, 
                        font: { size: 11, family: "'Inter', sans-serif" } 
                    } 
                }
            }
        }
    });
});
</script>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
