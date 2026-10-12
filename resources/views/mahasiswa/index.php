<?php ob_start(); 

function status_badge_class(string $status): string {
    return match ($status) {
        'Aktif'             => 'bg-green-100 text-green-800 border-green-200',
        'Cuti'              => 'bg-amber-100 text-amber-800 border-amber-200',
        'Lulus'             => 'bg-blue-100 text-blue-800 border-blue-200',
        'Mengundurkan Diri' => 'bg-orange-100 text-orange-800 border-orange-200',
        'Drop Out'          => 'bg-red-100 text-red-800 border-red-200',
        'Tidak Aktif'       => 'bg-slate-100 text-slate-700 border-slate-200',
        default             => 'bg-slate-100 text-slate-700 border-slate-200',
    };
}
?>

<div x-data="{ deleteModalOpen: false, deleteAction: '', deleteMhsName: '', deleteMhsNpm: '' }">

    <!-- Header halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="font-bold text-xl text-slate-900">Data Mahasiswa</h2>
            <p class="text-sm text-slate-500 mt-1">
                Total <span class="font-semibold text-slate-700"><?= number_format($totalRows ?? 0) ?></span> data ditemukan
            </p>
        </div>
        <a href="<?= url('/mahasiswa/create') ?>"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-md hover:bg-blue-700 transition-colors shadow-sm focus:ring-4 focus:ring-blue-100">
            <i class="fa-solid fa-user-plus"></i>
            Tambah Mahasiswa
        </a>
    </div>

    <!-- Flash message -->
    <?php if (isset($_SESSION['flash_success'])): ?>
    <div x-data="{ show: true }" x-show="show" class="p-4 mb-5 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center justify-between" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-circle-check mr-2.5 text-green-600 text-base"></i> 
            <span><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
        </div>
        <button type="button" @click="show = false" class="text-green-600 hover:text-green-800 text-sm">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <?php unset($_SESSION['flash_success']); endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
    <div x-data="{ show: true }" x-show="show" class="p-4 mb-5 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center justify-between" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-circle-exclamation mr-2.5 text-red-600 text-base"></i> 
            <span><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
        </div>
        <button type="button" @click="show = false" class="text-red-600 hover:text-red-800 text-sm">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <?php unset($_SESSION['flash_error']); endif; ?>

    <!-- Kartu filter & pencarian -->
    <div class="bg-white rounded-md shadow-sm border border-slate-200 p-4 sm:p-5 mb-6">
        <form action="<?= url('/mahasiswa') ?>" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">

            <!-- Pencarian -->
            <div class="md:col-span-5">
                <label for="q" class="block text-xs font-semibold text-slate-600 mb-1.5">Cari (Nama / NPM)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </span>
                    <input type="text" name="q" id="q" value="<?= htmlspecialchars($q ?? '') ?>"
                           placeholder="Ketik nama atau NPM..."
                           class="w-full pl-9 pr-3 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                </div>
            </div>

            <!-- Filter Prodi -->
            <div class="md:col-span-3">
                <label for="prodi" class="block text-xs font-semibold text-slate-600 mb-1.5">Program Studi</label>
                <select name="prodi" id="prodi"
                        class="w-full px-3 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                    <option value="0">— Semua Prodi —</option>
                    <?php if (!empty($prodiList)): ?>
                        <?php foreach ($prodiList as $p): 
                            $pId = (int)($p['id'] ?? $p['id_program_studi'] ?? 0);
                            $pNama = $p['namaProgramStudi'] ?? $p['nama_program_studi'] ?? '';
                        ?>
                            <option value="<?= $pId ?>" <?= ($fProdi === $pId) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pNama) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Filter Status -->
            <div class="md:col-span-2">
                <label for="status" class="block text-xs font-semibold text-slate-600 mb-1.5">Status</label>
                <select name="status" id="status"
                        class="w-full px-3 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                    <option value="">— Semua —</option>
                    <?php foreach (['Aktif', 'Cuti', 'Lulus', 'Mengundurkan Diri', 'Drop Out', 'Tidak Aktif'] as $s): ?>
                        <option value="<?= htmlspecialchars($s) ?>" <?= ($fStatus === $s) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tombol aksi filter -->
            <div class="md:col-span-2 flex gap-2">
                <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors focus:ring-4 focus:ring-blue-100 shadow-sm">
                    <i class="fa-solid fa-filter"></i>
                    Filter
                </button>
                <a href="<?= url('/mahasiswa') ?>"
                   class="inline-flex items-center justify-center px-3.5 py-2.5 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition-colors"
                   title="Reset filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel data -->
    <div class="bg-white rounded-md shadow-sm border border-slate-200 overflow-hidden">
        <?php if (empty($mahasiswaList)): ?>
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-user-graduate text-2xl"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base mb-1">Data Tidak Ditemukan</h3>
                <p class="text-slate-500 text-sm max-w-md mx-auto mb-5">
                    Belum ada data mahasiswa yang cocok dengan kriteria pencarian Anda. Coba ubah kata kunci atau filter.
                </p>
                <a href="<?= url('/mahasiswa/create') ?>"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors shadow-sm">
                    <i class="fa-solid fa-user-plus"></i>
                    Tambah Mahasiswa
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left font-semibold text-slate-600 px-5 py-3.5">NPM</th>
                            <th class="text-left font-semibold text-slate-600 px-5 py-3.5">Nama Mahasiswa</th>
                            <th class="text-left font-semibold text-slate-600 px-5 py-3.5">Jenis Kelamin</th>
                            <th class="text-left font-semibold text-slate-600 px-5 py-3.5">Program Studi</th>
                            <th class="text-left font-semibold text-slate-600 px-5 py-3.5">Jenjang</th>
                            <th class="text-left font-semibold text-slate-600 px-5 py-3.5">Status</th>
                            <th class="text-left font-semibold text-slate-600 px-5 py-3.5">Tgl Masuk</th>
                            <th class="text-right font-semibold text-slate-600 px-5 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($mahasiswaList as $m):
                            $idM = (int)($m['id'] ?? $m['id_mahasiswa'] ?? 0);
                            $npm = $m['npm'] ?? '-';
                            $nama = $m['namaMahasiswa'] ?? $m['nama_mahasiswa'] ?? '-';
                            $fakultas = $m['programStudi']['fakultas']['namaFakultas'] ?? $m['nama_fakultas'] ?? '';
                            $jk = $m['jenisKelamin'] ?? $m['jenis_kelamin'] ?? '-';
                            $jkText = ($jk === 'L') ? 'Laki-laki' : (($jk === 'P') ? 'Perempuan' : $jk);
                            $prodiNama = $m['programStudi']['namaProgramStudi'] ?? $m['nama_program_studi'] ?? '-';
                            $jenjang = $m['programStudi']['jenjang'] ?? $m['jenjang'] ?? 'S1';
                            $status = $m['statusMahasiswa'] ?? $m['status_mahasiswa'] ?? 'Aktif';
                            $tglMasuk = $m['tanggalMasuk'] ?? $m['tanggal_masuk'] ?? null;
                        ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="font-mono text-xs text-slate-700 bg-slate-100 px-2 py-1 rounded-md font-semibold">
                                        <?= htmlspecialchars($npm) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900"><?= htmlspecialchars($nama) ?></p>
                                    <?php if ($fakultas): ?>
                                        <p class="text-[11px] text-slate-400 mt-0.5"><?= htmlspecialchars($fakultas) ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-slate-600"><?= htmlspecialchars($jkText) ?></td>
                                <td class="px-5 py-4 text-slate-600"><?= htmlspecialchars($prodiNama) ?></td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-bold">
                                        <?= htmlspecialchars($jenjang) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg border text-[11px] font-bold <?= status_badge_class($status) ?>">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-500 text-xs">
                                    <?= $tglMasuk ? date('d/m/Y', strtotime($tglMasuk)) : '-' ?>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">

                                        <!-- Detail -->
                                        <a href="<?= url('/mahasiswa/detail/' . $idM) ?>"
                                           class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold hover:bg-blue-100 transition-colors"
                                           title="Lihat detail">
                                            <i class="fa-solid fa-eye"></i>
                                            Detail
                                        </a>

                                        <!-- Edit -->
                                        <a href="<?= url('/mahasiswa/edit/' . $idM) ?>"
                                           class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-xs font-semibold hover:bg-amber-100 transition-colors"
                                           title="Edit data">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            Edit
                                        </a>

                                        <!-- Hapus -->
                                        <button type="button"
                                                @click="deleteModalOpen = true; deleteAction = '<?= url('/mahasiswa/delete/' . $idM) ?>'; deleteMhsName = '<?= htmlspecialchars(addslashes($nama), ENT_QUOTES) ?>'; deleteMhsNpm = '<?= htmlspecialchars(addslashes($npm), ENT_QUOTES) ?>';"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100 transition-colors"
                                                title="Hapus data">
                                            <i class="fa-solid fa-trash-can"></i>
                                            Hapus
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-slate-500">
                        Halaman <span class="font-semibold text-slate-700"><?= $page ?></span> dari
                        <span class="font-semibold text-slate-700"><?= $totalPages ?></span>
                        (<?= number_format($totalRows) ?> data)
                    </p>
                    <nav class="flex items-center gap-1" aria-label="Navigasi halaman">
                        <?php
                        $qs = $_GET;
                        unset($qs['page']);
                        $baseQs = http_build_query($qs);
                        $sep = $baseQs !== '' ? '&' : '';
                        ?>

                        <?php if ($page > 1): ?>
                            <a href="<?= url('/mahasiswa?' . $baseQs . $sep . 'page=' . ($page - 1)) ?>"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50">
                                <i class="fa-solid fa-chevron-left"></i> Prev
                            </a>
                        <?php endif; ?>

                        <?php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <a href="<?= url('/mahasiswa?' . $baseQs . $sep . 'page=' . $i) ?>"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold <?= $i === $page ? 'bg-blue-600 text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="<?= url('/mahasiswa?' . $baseQs . $sep . 'page=' . ($page + 1)) ?>"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50">
                                Next <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-cloak x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="deleteModalOpen = false" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10 text-red-600">
                            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-semibold text-gray-900" id="modal-title">Hapus Data Mahasiswa</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menghapus data mahasiswa <strong class="text-gray-900" x-text="deleteMhsName"></strong> (<span class="font-mono" x-text="deleteMhsNpm"></span>)?
                                </p>
                                <p class="text-xs text-red-500 mt-2">
                                    Tindakan ini tidak dapat dibatalkan. Jika mahasiswa memiliki akun login, akun terkait juga akan dihapus.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <form :action="deleteAction" method="POST" class="inline">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                            Ya, Hapus Mahasiswa
                        </button>
                    </form>
                    <button type="button" @click="deleteModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
