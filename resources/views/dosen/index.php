<?php 
ob_start(); 
$userProfile = $_SESSION['user'] ?? [];
$userRoleCode = is_array($userProfile['role'] ?? null) 
    ? strtoupper($userProfile['role']['kodeRole'] ?? $userProfile['role']['namaRole'] ?? '') 
    : strtoupper((string)($userProfile['role'] ?? ''));
$isProdiUser = in_array($userRoleCode, ['KAPRODI', 'ADMIN_PRODI']);
$pageHeading = $isProdiUser ? 'Daftar Dosen Program Studi' : 'Daftar Dosen Fakultas';
$pageSubtitle = $isProdiUser 
    ? 'Data tenaga pengajar dan dosen di bawah naungan program studi Anda.' 
    : 'Data tenaga pengajar dan dosen program studi di bawah naungan fakultas.';
$title = $title ?? $pageHeading;
?>

<section class="mb-8">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="font-heading font-bold text-2xl text-slate-900"><?= htmlspecialchars($pageHeading) ?></h2>
            <p class="text-sm text-slate-500 mt-1"><?= htmlspecialchars($pageSubtitle) ?></p>
        </div>
        <div class="flex items-center gap-2">
            <?php 
                $qs = http_build_query([
                    'id_prodi'     => $fProdi ?? '',
                    'status_aktif' => $fStatus ?? '',
                    'q'            => $fQ ?? '',
                ]);
            ?>
            <a href="<?= url('/dosen/export-csv?' . $qs) ?>" 
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-md transition-colors focus:ring-4 focus:ring-emerald-200 inline-flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>
            <a href="<?= url('/dosen/cetak-pdf?' . $qs) ?>" target="_blank" 
               class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-md transition-colors focus:ring-4 focus:ring-rose-200 inline-flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-print"></i> Cetak PDF
            </a>
        </div>
    </div>

    <!-- Ringkasan Statistik 3 Kartu -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Dosen</p>
                <h4 class="font-heading font-bold text-2xl text-slate-800 mt-1"><?= number_format($total ?? 0) ?></h4>
                <p class="text-[11px] text-slate-500 mt-0.5">Terdaftar di fakultas</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-blue-50 text-blue-600 shadow-sm">
                <i class="fa-solid fa-chalkboard-user text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dosen Aktif</p>
                <h4 class="font-heading font-bold text-2xl text-slate-800 mt-1"><?= number_format($totalAktif ?? 0) ?></h4>
                <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Status aktif mengajar</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-600 shadow-sm">
                <i class="fa-solid fa-user-check text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Program Studi</p>
                <h4 class="font-heading font-bold text-2xl text-slate-800 mt-1"><?= number_format(count($prodiList ?? [])) ?></h4>
                <p class="text-[11px] text-slate-500 mt-0.5">Di bawah naungan fakultas</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-purple-50 text-purple-600 shadow-sm">
                <i class="fa-solid fa-graduation-cap text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Panel Tabel Data Dosen -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-100 flex flex-col overflow-hidden">
        <!-- Toolbar Filter -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/50">
            <form action="<?= url('/dosen') ?>" method="GET" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Program Studi</label>
                    <select name="id_prodi" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                        <option value="">Semua Prodi</option>
                        <?php foreach ($prodiList as $pl): ?>
                            <option value="<?= htmlspecialchars($pl['id']) ?>" <?= ($fProdi == $pl['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pl['namaProgramStudi'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="w-36">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                    <select name="status_aktif" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="Aktif" <?= $fStatus === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Tidak Aktif" <?= $fStatus === 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[180px]">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pencarian</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" name="q" value="<?= htmlspecialchars($fQ ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Nama, NIP/username, email..." class="w-full pl-8 pr-3 py-2 bg-white border border-slate-200 rounded-md text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-md transition-colors focus:ring-2 focus:ring-blue-200 shadow-sm cursor-pointer">
                        Filter
                    </button>
                    <?php if (!empty($fProdi) || !empty($fStatus) || !empty($fQ)): ?>
                        <a href="<?= url('/dosen') ?>" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold rounded-md transition-colors" title="Reset Filter">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabel List Dosen -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-b border-slate-100 text-left">
                <thead class="bg-slate-50 text-slate-600 text-xs">
                    <tr>
                        <th class="text-center font-semibold px-4 py-3 w-14">No</th>
                        <th class="text-left font-semibold px-4 py-3">NIP / Username & Nama Dosen</th>
                        <th class="text-left font-semibold px-4 py-3">Program Studi</th>
                        <th class="text-left font-semibold px-4 py-3">Kontak</th>
                        <th class="text-center font-semibold px-4 py-3 w-28">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($dosen)): ?>
                        <tr><td colspan="5" class="px-4 py-12 text-center text-slate-400">Tidak ada data dosen ditemukan di bawah fakultas ini.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($dosen as $d): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3.5 text-center text-slate-500 font-mono text-xs"><?= $no++ ?></td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs shrink-0">
                                            <?= strtoupper(substr($d['namaLengkap'] ?? 'D', 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900"><?= htmlspecialchars($d['namaLengkap'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                                            <div class="text-xs font-mono text-slate-500 mt-0.5">
                                                NIP/ID: <span class="text-slate-700"><?= htmlspecialchars($d['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="text-slate-800 font-semibold text-xs uppercase block">
                                        <?= htmlspecialchars($d['namaProgramStudi'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="text-[11px] text-slate-400">
                                        <?= htmlspecialchars($d['namaFakultas'] ?? 'Fakultas Teknik Informasi', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-600">
                                    <div class="flex items-center gap-1.5 text-slate-700">
                                        <i class="fa-regular fa-envelope text-slate-400"></i>
                                        <span><?= htmlspecialchars($d['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-slate-500 mt-1">
                                        <i class="fa-solid fa-phone text-slate-400 text-[10px]"></i>
                                        <span><?= htmlspecialchars($d['noHp'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <?php 
                                        $st = $d['statusAktif'] ?? 'Aktif';
                                        $bBg = ($st === 'Aktif') 
                                            ? 'bg-green-100 text-green-700 border-green-200' 
                                            : 'bg-slate-100 text-slate-600 border-slate-200';
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
</section>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
