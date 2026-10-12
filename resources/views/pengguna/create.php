<?php ob_start(); ?>

<div class="mb-6 flex items-center gap-4">
    <a href="<?= url('/pengguna') ?>" class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 transition-colors">
        <i class="fas fa-arrow-left text-sm"></i>
    </a>
    <div>
        <h2 class="text-xl font-bold text-gray-800">Tambah Pengguna</h2>
        <p class="text-[13px] text-gray-500 mt-1">Buat akun baru untuk staf atau mahasiswa.</p>
    </div>
</div>

<?php if (isset($_SESSION['flash_error'])): ?>
<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
    <i class="fas fa-exclamation-circle mr-2"></i> <?= $_SESSION['flash_error'] ?>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <form action="<?= url('/pengguna/store') ?>" method="POST">
        <div class="p-8">
            
            <!-- INFORMASI AKUN -->
            <div class="mb-8">
                <h4 class="text-[13px] font-bold text-gray-400 uppercase tracking-wider mb-6">INFORMASI AKUN</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" required class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                    </div>
                </div>
            </div>

            <hr class="border-gray-100 mb-8">

            <!-- PROFIL PENGGUNA -->
            <div class="mb-8">
                <h4 class="text-[13px] font-bold text-gray-400 uppercase tracking-wider mb-6">PROFIL PENGGUNA</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" required class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                    </div>
                </div>
            </div>

            <hr class="border-gray-100 mb-8">

            <!-- HAK AKSES & PENEMPATAN -->
            <div class="mb-8">
                <h4 class="text-[13px] font-bold text-gray-400 uppercase tracking-wider mb-6">HAK AKSES & PENEMPATAN</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Role / Tingkat Akses <span class="text-red-500">*</span></label>
                        <select name="role" required class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                            <option value="">-- Pilih Role --</option>
                            <option value="Admin">Admin</option>
                            <option value="Operator Program Studi">Operator Program Studi</option>
                            <option value="Dekanat">Dekanat</option>
                            <option value="Rektorat">Rektorat</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="px-8 py-5 flex items-center justify-end gap-3 bg-white">
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-[#2563eb] hover:bg-blue-700 rounded-md transition-colors flex items-center gap-2 shadow-sm">
                <i class="fas fa-save"></i> Simpan Data
            </button>
        </div>
    </form>
</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
