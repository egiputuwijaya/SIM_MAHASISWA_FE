<?php ob_start(); ?>

<section class="max-w-3xl mb-8">
    <div class="mb-6 flex items-center gap-3">
        <a href="<?= url('/program-studi') ?>" class="w-10 h-10 rounded-md bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="font-bold text-2xl text-gray-900">Tambah Program Studi</h2>
            <p class="text-sm text-gray-500 mt-0.5">Daftarkan prodi baru ke dalam fakultas.</p>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_error'])): ?>
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); endif; ?>

    <div class="bg-white rounded-md shadow-sm border border-gray-200">
        <form action="<?= url('/program-studi/store') ?>" method="POST" class="p-6 sm:p-8">
            <div class="space-y-6">
                <!-- Pilih Fakultas -->
                <div>
                    <label for="id_fakultas" class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Fakultas <span class="text-red-500">*</span></label>
                    <select name="id_fakultas" id="id_fakultas" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-md text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-gray-700">
                        <option value="">-- Pilih Fakultas --</option>
                        <?php if (!empty($listFakultas)): ?>
                            <?php foreach ($listFakultas as $fak): ?>
                                <?php $fakId = $fak['id'] ?? $fak['id_fakultas'] ?? 0; ?>
                                <option value="<?= $fakId ?>">
                                    <?= htmlspecialchars($fak['namaFakultas'] ?? $fak['nama_fakultas']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Grid Kode Prodi & Jenjang -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="kode_program_studi" class="block text-sm font-semibold text-gray-700 mb-1.5">Kode Prodi <span class="text-red-500">*</span></label>
                        <input type="text" id="kode_program_studi" name="kode_program_studi" required maxlength="20" placeholder="CONTOH: TI, SI, AKT"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-md text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors uppercase text-gray-700">
                        <p class="text-[11px] text-gray-500 mt-1">Harus unik di dalam fakultas yang sama.</p>
                    </div>

                    <div>
                        <label for="jenjang" class="block text-sm font-semibold text-gray-700 mb-1.5">Jenjang <span class="text-red-500">*</span></label>
                        <select name="jenjang" id="jenjang" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-md text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-gray-700">
                            <option value="D3">Diploma 3 (D3)</option>
                            <option value="D4">Diploma 4 (D4)</option>
                            <option value="S1" selected>Strata 1 (S1)</option>
                            <option value="S2">Magister (S2)</option>
                            <option value="S3">Doktor (S3)</option>
                        </select>
                    </div>
                </div>

                <!-- Nama Program Studi -->
                <div>
                    <label for="nama_program_studi" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Program Studi <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_program_studi" name="nama_program_studi" required maxlength="200" placeholder="Contoh: Teknik Informatika"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-md text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-gray-700">
                </div>

                <!-- Status -->
                <div>
                    <label for="status_aktif" class="block text-sm font-semibold text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status_aktif" id="status_aktif" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-md text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-gray-700">
                        <option value="Aktif" selected>Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="<?= url('/program-studi') ?>" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-[#2563eb] hover:bg-blue-700 rounded-md transition-colors flex items-center gap-2 shadow-sm">
                    <i class="fas fa-save"></i> Simpan Data
                </button>
            </div>
        </form>
    </div>
</section>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
