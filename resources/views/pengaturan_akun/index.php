<?php ob_start(); ?>

<div x-data="{ activeTab: '<?= htmlspecialchars($tab ?? 'profil') ?>' }">

    <!-- Header Halaman -->
    <div class="mb-6">
        <h2 class="font-bold text-2xl text-slate-900 tracking-tight">Pengaturan Akun</h2>
        <p class="text-sm text-slate-500 mt-1">Perbarui informasi dasar profil Anda.</p>
    </div>

    <!-- Flash message -->
    <?php if (isset($_SESSION['flash_success'])): ?>
    <div x-data="{ show: true }" x-show="show" class="p-4 mb-5 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center justify-between shadow-sm" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-circle-check mr-2.5 text-green-600 text-base"></i> 
            <span class="font-medium"><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
        </div>
        <button type="button" @click="show = false" class="text-green-600 hover:text-green-800 text-sm">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <?php unset($_SESSION['flash_success']); endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
    <div x-data="{ show: true }" x-show="show" class="p-4 mb-5 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center justify-between shadow-sm" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-circle-exclamation mr-2.5 text-red-600 text-base"></i> 
            <span class="font-medium"><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
        </div>
        <button type="button" @click="show = false" class="text-red-600 hover:text-red-800 text-sm">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <?php unset($_SESSION['flash_error']); endif; ?>

    <!-- Tab Navigasi -->
    <div class="border-b border-slate-200 mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button type="button"
                    @click="activeTab = 'profil'"
                    :class="activeTab === 'profil' 
                        ? 'border-blue-600 text-blue-600 font-semibold' 
                        : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
                    class="py-3 px-1 border-b-2 text-sm transition-colors whitespace-nowrap focus:outline-none">
                Profil Dasar
            </button>
            <button type="button"
                    @click="activeTab = 'password'"
                    :class="activeTab === 'password' 
                        ? 'border-blue-600 text-blue-600 font-semibold' 
                        : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
                    class="py-3 px-1 border-b-2 text-sm transition-colors whitespace-nowrap focus:outline-none">
                Ubah Password
            </button>
        </nav>
    </div>

    <!-- Kartu Konten Pengaturan -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 sm:p-8 max-w-4xl">

        <!-- TAB 1: Profil Dasar -->
        <div x-show="activeTab === 'profil'" x-cloak>
            <form action="<?= url('/pengaturan-akun/update-profil') ?>" method="POST" class="space-y-6">
                
                <!-- Username (Read-Only) -->
                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-700 mb-2">
                        Username (Read-Only)
                    </label>
                    <input type="text" id="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" readonly
                           class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-md text-sm text-slate-600 cursor-not-allowed select-none focus:outline-none">
                    <p class="text-xs text-slate-400 mt-1.5">
                        Username tidak dapat diubah sendiri. Hubungi Administrator jika terdapat kesalahan.
                    </p>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="nama_lengkap" class="block text-sm font-semibold text-slate-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required
                           value="<?= htmlspecialchars($user['namaLengkap'] ?? '') ?>"
                           placeholder="Masukkan nama lengkap Anda"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-md text-sm text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors">
                </div>

                <!-- Alamat Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                        Alamat Email
                    </label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                           placeholder="nama@email.com"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-md text-sm text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors">
                </div>

                <!-- Tombol Simpan -->
                <div class="flex justify-end pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1e88e5] hover:bg-blue-700 text-white text-sm font-semibold rounded-md shadow-sm transition-colors focus:ring-2 focus:ring-blue-200">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>

        <!-- TAB 2: Ubah Password -->
        <div x-show="activeTab === 'password'" x-cloak>
            <form action="<?= url('/pengaturan-akun/update-password') ?>" method="POST" class="space-y-6">
                
                <!-- Kata Sandi Saat Ini -->
                <div>
                    <label for="password_lama" class="block text-sm font-semibold text-slate-700 mb-2">
                        Kata Sandi Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_lama" name="password_lama" required
                           placeholder="Masukkan kata sandi saat ini"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-md text-sm text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors">
                </div>

                <!-- Kata Sandi Baru -->
                <div>
                    <label for="password_baru" class="block text-sm font-semibold text-slate-700 mb-2">
                        Kata Sandi Baru <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_baru" name="password_baru" required
                           placeholder="Masukkan kata sandi baru"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-md text-sm text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors">
                    <p class="text-xs text-slate-400 mt-1.5">
                        Minimal 8 karakter, harus mengandung huruf besar dan angka.
                    </p>
                </div>

                <!-- Ulangi Kata Sandi Baru -->
                <div>
                    <label for="konfirmasi_password" class="block text-sm font-semibold text-slate-700 mb-2">
                        Ulangi Kata Sandi Baru <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" required
                           placeholder="Ulangi kata sandi baru"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-md text-sm text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors">
                </div>

                <!-- Tombol Perbarui -->
                <div class="flex justify-end pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1e88e5] hover:bg-blue-700 text-white text-sm font-semibold rounded-md shadow-sm transition-colors focus:ring-2 focus:ring-blue-200">
                        <i class="fa-solid fa-key"></i> Perbarui Kata Sandi
                    </button>
                </div>

            </form>
        </div>

    </div>

</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
