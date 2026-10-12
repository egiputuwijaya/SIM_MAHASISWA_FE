<?php 
ob_start(); 
$u = $univ ?? [];
?>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <!-- Card Header -->
    <div class="px-6 py-6 border-b border-gray-100">
        <h3 class="text-[17px] font-bold text-gray-800">Perbarui Profil Universitas</h3>
        <p class="text-[13px] text-gray-500 mt-1">Pastikan informasi di bawah ini akurat dan valid karena akan tampil di halaman publik.</p>
    </div>

    <?php if (isset($_SESSION['flash_error'])): ?>
    <div class="mx-6 mt-4 p-4 mb-2 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); endif; ?>

    <!-- Form Body -->
    <form action="<?= url('/profil-universitas/update') ?>" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($u['id'] ?? '1') ?>">

        <div class="p-6 md:p-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <!-- Kode / Singkatan -->
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-2">Kode / Singkatan <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_universitas" value="<?= htmlspecialchars($u['kodeUniversitas'] ?? $u['kode_universitas'] ?? 'UMB') ?>" required class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                </div>

                <!-- Nama Universitas -->
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-2">Nama Universitas <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_universitas" value="<?= htmlspecialchars($u['namaUniversitas'] ?? $u['nama_universitas'] ?? 'Universitas Muhammadiyah Bengkulu') ?>" required class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                </div>
            </div>

            <!-- Slogan -->
            <div>
                <label class="block text-[13px] font-semibold text-gray-700 mb-2">Slogan / Motto</label>
                <input type="text" name="slogan" value="<?= htmlspecialchars($u['slogan'] ?? '') ?>" placeholder="Masukkan slogan kampus" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
            </div>

            <!-- Alamat -->
            <div>
                <label class="block text-[13px] font-semibold text-gray-700 mb-2">Alamat Jalan</label>
                <textarea name="alamat" rows="3" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700 resize-none"><?= htmlspecialchars($u['alamat'] ?? 'Jl. Bali, Kampung Bali') ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <!-- Kota -->
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-2">Kota / Kabupaten</label>
                    <input type="text" name="kota" value="<?= htmlspecialchars($u['kota'] ?? 'Bengkulu') ?>" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                </div>

                <!-- Provinsi -->
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-2">Provinsi</label>
                    <input type="text" name="provinsi" value="<?= htmlspecialchars($u['provinsi'] ?? 'Bengkulu') ?>" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                </div>

                <!-- Kode Pos -->
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-2">Kode Pos</label>
                    <input type="text" name="kode_pos" value="<?= htmlspecialchars($u['kodePos'] ?? $u['kode_pos'] ?? '38119') ?>" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                </div>

                <!-- Telepon -->
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-2">Telepon</label>
                    <input type="text" name="telepon" value="<?= htmlspecialchars($u['telepon'] ?? '') ?>" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                </div>

                <!-- Email Resmi -->
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-2">Email Resmi</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($u['email'] ?? '') ?>" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                </div>

                <!-- Website -->
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-2">Website Utama</label>
                    <input type="text" name="website" value="<?= htmlspecialchars($u['website'] ?? 'https://umb.ac.id') ?>" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                </div>
            </div>
            
        </div>

        <!-- Footer / Action Buttons -->
        <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-end gap-3 bg-white">
            <a href="<?= url('/profil-universitas') ?>" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-[#3b82f6] hover:bg-blue-600 rounded-md transition-colors flex items-center gap-2">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
