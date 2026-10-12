<?php 
ob_start(); 
$f = $fakultas ?? [];
$idFakultas = $f['id'] ?? $f['id_fakultas'] ?? 0;
$kode = $f['kodeFakultas'] ?? $f['kode_fakultas'] ?? '';
$nama = $f['namaFakultas'] ?? $f['nama_fakultas'] ?? '';
?>

<section class="max-w-2xl mb-8">
    <div class="mb-6 flex items-center gap-3">
        <a href="<?= url('/fakultas') ?>" class="w-10 h-10 rounded-md bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="font-bold text-2xl text-gray-900">Edit Data Fakultas</h2>
            <p class="text-sm text-gray-500 mt-0.5">Perbarui kode atau nama fakultas.</p>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_error'])): ?>
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); endif; ?>

    <div class="bg-white rounded-md shadow-sm border border-gray-200">
        <form action="<?= url('/fakultas/update/' . $idFakultas) ?>" method="POST" class="p-6 sm:p-8">
            <div class="space-y-6">
                <div>
                    <label for="kode_fakultas" class="block text-sm font-semibold text-gray-700 mb-1.5">Kode Fakultas <span class="text-red-500">*</span></label>
                    <input type="text" id="kode_fakultas" name="kode_fakultas" required maxlength="20"
                           value="<?= htmlspecialchars($kode) ?>"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-md text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors uppercase">
                    <p class="text-[11px] text-gray-500 mt-1">Kode harus unik dan maksimal 20 karakter.</p>
                </div>

                <div>
                    <label for="nama_fakultas" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Fakultas <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_fakultas" name="nama_fakultas" required maxlength="200"
                           value="<?= htmlspecialchars($nama) ?>"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-md text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="<?= url('/fakultas') ?>" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-[#2563eb] hover:bg-blue-700 rounded-md transition-colors flex items-center gap-2 shadow-sm">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</section>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
