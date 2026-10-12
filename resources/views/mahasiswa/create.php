<?php ob_start(); ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Tambah Mahasiswa</h1>
        <p class="text-sm text-gray-500 mt-1">Masukkan data lengkap mahasiswa baru.</p>
    </div>
    <a href="<?= url('/mahasiswa') ?>" class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
</div>

<?php if(isset($error)): ?>
<div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm flex items-center shadow-sm" x-data="{ show: true }" x-show="show" x-transition>
    <i class="fas fa-exclamation-triangle mr-3 text-red-500 text-lg"></i>
    <span class="flex-1 font-medium"><?= htmlspecialchars($error) ?></span>
    <button @click="show = false" class="text-red-400 hover:text-red-600 focus:outline-none"><i class="fas fa-times"></i></button>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6">
        <form action="<?= url('/mahasiswa/store') ?>" method="POST" enctype="multipart/form-data">
            
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2">Informasi Pribadi</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="npm" class="block text-sm font-medium text-gray-700 mb-1">NPM <span class="text-red-500">*</span></label>
                    <input type="text" name="npm" id="npm" required 
                           class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-shadow shadow-sm"
                           value="<?= htmlspecialchars($old['npm'] ?? '') ?>" placeholder="Misal: 20260001">
                </div>
                
                <div>
                    <label for="nama_mahasiswa" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_mahasiswa" id="nama_mahasiswa" required 
                           class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-shadow shadow-sm"
                           value="<?= htmlspecialchars($old['nama_mahasiswa'] ?? '') ?>" placeholder="Nama sesuai KTP">
                </div>

                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select name="jenis_kelamin" id="jenis_kelamin" required 
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-shadow shadow-sm bg-white">
                        <option value="L" <?= isset($old['jenis_kelamin']) && $old['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= isset($old['jenis_kelamin']) && $old['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2 mt-8">Informasi Akademik</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="id_program_studi" class="block text-sm font-medium text-gray-700 mb-1">Program Studi <span class="text-red-500">*</span></label>
                    <select name="id_program_studi" id="id_program_studi" required 
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-shadow shadow-sm bg-white">
                        <!-- Normally fetched from DB -->
                        <option value="1">Teknik Informatika</option>
                        <option value="2">Sistem Informasi</option>
                    </select>
                </div>

                <div>
                    <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" required 
                           class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-shadow shadow-sm"
                           value="<?= htmlspecialchars($old['tanggal_masuk'] ?? date('Y-m-d')) ?>">
                </div>
            </div>

            <div class="pt-5 border-t border-gray-100 flex justify-end gap-3">
                <a href="<?= url('/mahasiswa') ?>" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-colors shadow-sm">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors shadow-sm inline-flex items-center">
                    <i class="fas fa-save mr-2"></i> Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
