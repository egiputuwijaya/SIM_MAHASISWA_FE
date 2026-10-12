<?php ob_start(); ?>

<section class="max-w-2xl mb-8">
    <div class="mb-6 flex items-center gap-3">
        <a href="<?= url('/fakultas') ?>" class="w-10 h-10 rounded-md bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="font-bold text-2xl text-gray-900">Tambah Fakultas Baru</h2>
            <p class="text-sm text-gray-500 mt-0.5">Masukkan kode dan nama fakultas.</p>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_error'])): ?>
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); endif; ?>

    <div class="bg-white rounded-md shadow-sm border border-gray-200">
        <form action="<?= url('/fakultas/store') ?>" method="POST" class="p-6 sm:p-8">
            <input type="hidden" name="id_universitas" value="<?= htmlspecialchars($univ['id'] ?? $univ['id_universitas'] ?? 1) ?>">

            <div class="space-y-6">
                <!-- Info Universitas Readonly -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Universitas</label>
                    <input type="text" disabled value="<?= htmlspecialchars($univ['namaUniversitas'] ?? $univ['nama_universitas'] ?? 'Universitas Muhammadiyah Bengkulu') ?>"
                           class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-md text-sm text-gray-500 cursor-not-allowed">
                </div>

                <div>
                    <label for="kode_fakultas" class="block text-sm font-semibold text-gray-700 mb-1.5">Kode Fakultas <span class="text-red-500">*</span></label>
                    <input type="text" id="kode_fakultas" name="kode_fakultas" required maxlength="20" placeholder="CONTOH: FT, FKIP, FEB"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-md text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors uppercase">
                    <p class="text-[11px] text-gray-500 mt-1">Kode harus unik dan maksimal 20 karakter.</p>
                </div>

                <div>
                    <label for="nama_fakultas" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Fakultas <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_fakultas" name="nama_fakultas" required maxlength="200" placeholder="Contoh: Fakultas Teknik"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-md text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="<?= url('/fakultas') ?>" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-[#2563eb] hover:bg-blue-700 rounded-md transition-colors flex items-center gap-2 shadow-sm">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
            </div>
        </form>
    </div>
</section>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
