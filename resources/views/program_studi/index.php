<?php ob_start(); ?>

<div x-data="{ deleteModalOpen: false, deleteAction: '', deleteProdiName: '' }">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Daftar Program Studi</h2>
            <p class="text-[13px] text-gray-500 mt-1">Kelola data seluruh program studi di universitas.</p>
        </div>
        <div>
            <a href="<?= url('/program-studi/create') ?>" class="bg-[#2563eb] hover:bg-blue-700 text-white text-[13px] font-medium py-2 px-4 rounded-md transition-colors flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus"></i> Tambah Prodi
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
    <div x-data="{ show: true }" x-show="show" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center justify-between" role="alert">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2 text-green-600"></i> 
            <span><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
        </div>
        <button type="button" @click="show = false" class="text-green-600 hover:text-green-800 text-sm">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <?php unset($_SESSION['flash_success']); endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
    <div x-data="{ show: true }" x-show="show" class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center justify-between" role="alert">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2 text-red-600"></i> 
            <span><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
        </div>
        <button type="button" @click="show = false" class="text-red-600 hover:text-red-800 text-sm">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <?php unset($_SESSION['flash_error']); endif; ?>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
        <!-- Filter Bar -->
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <span class="text-[13px] font-semibold text-gray-700">Filter Fakultas:</span>
                <select onchange="window.location.href = '<?= url('/program-studi') ?>' + (this.value > 0 ? '?fakultas=' + this.value : '')" class="w-full sm:w-64 px-3 py-2 bg-white border border-gray-200 rounded-lg text-[13px] text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="0">-- Semua Fakultas --</option>
                    <?php if (!empty($listFakultas)): ?>
                        <?php foreach ($listFakultas as $fak): ?>
                            <?php $fakId = $fak['id'] ?? $fak['id_fakultas'] ?? 0; ?>
                            <option value="<?= $fakId ?>" <?= ($filterFakultas == $fakId) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($fak['namaFakultas'] ?? $fak['nama_fakultas']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if ($filterFakultas > 0): ?>
                    <a href="<?= url('/program-studi') ?>" class="text-sm text-gray-400 hover:text-gray-600" title="Reset Filter">
                        <i class="fas fa-times-circle"></i>
                    </a>
                <?php endif; ?>
            </div>
            <div class="text-[12px] text-gray-400">
                Total: <?= count($prodiData ?? []) ?> program studi
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-[12px] uppercase border-b border-gray-100 font-semibold tracking-wider">
                        <th class="py-4 px-6 font-semibold w-16 text-center">No</th>
                        <th class="py-4 px-6 font-semibold">Fakultas</th>
                        <th class="py-4 px-6 font-semibold w-32">Kode Prodi</th>
                        <th class="py-4 px-6 font-semibold">Nama Program Studi</th>
                        <th class="py-4 px-6 font-semibold text-center w-24">Jenjang</th>
                        <th class="py-4 px-6 font-semibold text-center w-28">Status</th>
                        <th class="py-4 px-6 font-semibold text-right w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-[14px] text-gray-700 divide-y divide-gray-100">
                    <?php if (empty($prodiData)): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400 text-sm">
                                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3 text-gray-400">
                                    <i class="fas fa-graduation-cap text-xl"></i>
                                </div>
                                Belum ada data program studi yang sesuai.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        foreach ($prodiData as $row): 
                            $idProdi = $row['id'] ?? $row['id_program_studi'] ?? 0;
                            $namaFak = $row['fakultas']['namaFakultas'] ?? $row['fakultas']['nama_fakultas'] ?? $row['nama_fakultas'] ?? '-';
                            $kode = $row['kodeProgramStudi'] ?? $row['kode_program_studi'] ?? '-';
                            $nama = $row['namaProgramStudi'] ?? $row['nama_program_studi'] ?? '-';
                            $jenjang = $row['jenjang'] ?? 'S1';
                            $status = $row['statusAktif'] ?? $row['status_aktif'] ?? 'Aktif';
                        ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 text-center text-gray-500"><?= $no++ ?></td>
                            <td class="py-4 px-6 text-gray-600"><?= htmlspecialchars($namaFak) ?></td>
                            <td class="py-4 px-6 font-mono font-bold text-blue-600"><?= htmlspecialchars($kode) ?></td>
                            <td class="py-4 px-6 font-semibold text-gray-900"><?= htmlspecialchars($nama) ?></td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-bold border border-gray-200 bg-gray-50 text-gray-700">
                                    <?= htmlspecialchars($jenjang) ?>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <?php if ($status === 'Aktif'): ?>
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-green-100 text-green-700">
                                        Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-gray-100 text-gray-500">
                                        Tidak Aktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= url('/program-studi/edit/' . $idProdi) ?>" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-100 transition-colors" title="Edit Program Studi">
                                        <i class="fas fa-pen-to-square text-xs"></i>
                                    </a>
                                    <button type="button" 
                                            @click="deleteModalOpen = true; deleteAction = '<?= url('/program-studi/delete/' . $idProdi) ?>'; deleteProdiName = '<?= addslashes(htmlspecialchars($nama)) ?> (<?= addslashes(htmlspecialchars($kode)) ?>)';"
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition-colors" 
                                            title="Hapus Program Studi">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="deleteModalOpen" 
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="deleteModalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.outside="deleteModalOpen = false"
                 class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                
                <div class="bg-white p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                            <i class="fas fa-trash-alt text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900" id="modal-title">Hapus Program Studi</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Apakah Anda yakin ingin menghapus Program Studi <span class="font-semibold text-gray-800" x-text="deleteProdiName"></span>?
                            </p>
                            <p class="mt-1 text-xs text-red-500">
                                Tindakan ini permanen. Jika masih ada data mahasiswa atau pengguna yang terdaftar di prodi ini, penghapusan akan dicegah.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <form :action="deleteAction" method="POST" class="inline">
                        <button type="submit" class="inline-flex justify-center items-center gap-1.5 rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash text-xs"></i> Ya, Hapus Program Studi
                        </button>
                    </form>
                    <button type="button" @click="deleteModalOpen = false" class="inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
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
