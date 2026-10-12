<?php 
ob_start(); 

// Pastikan file logo tersedia di public/assets
$baseDir = dirname(dirname(__DIR__));
$sourceLogo = $baseDir . '/assets/logoumb.png';
$pubAssetsDir = $baseDir . '/public/assets';
$pubLogo = $pubAssetsDir . '/logoumb.png';

if (!is_dir($pubAssetsDir)) {
    @mkdir($pubAssetsDir, 0777, true);
}
if (file_exists($sourceLogo) && !file_exists($pubLogo)) {
    @copy($sourceLogo, $pubLogo);
}

// Data URI untuk logo agar tidak bergantung pada routing atau path URL
$logoSrc = '';
if (file_exists($sourceLogo)) {
    $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($sourceLogo));
} else {
    $logoSrc = url('assets/logoumb.png');
}
?>

<div class="w-full max-w-5xl grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">

    <!-- ===== Panel Kiri: Branding ===== -->
    <div class="hidden lg:block text-center lg:text-left">
        <div class="inline-flex items-center gap-3 mb-8">
            <div class="w-16 h-16 rounded-lg bg-white/90 border border-white flex items-center justify-center shadow-lg backdrop-blur p-2">
                <img src="<?= $logoSrc ?>" alt="Logo UMB" class="w-full h-full object-contain">
            </div>
            <div class="text-left">
                <p class="font-heading font-extrabold text-white text-lg leading-tight">SIM Mahasiswa</p>
                <p class="text-elegant-300 text-xs font-semibold">Universitas Muhammadiyah Bengkulu</p>
            </div>
        </div>

        <h1 class="font-heading font-extrabold text-white text-4xl leading-tight mb-5">
            Selamat Datang<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-elegant-300 to-white">
                Kembali
            </span>
        </h1>

        <p class="text-slate-200/90 text-sm leading-relaxed max-w-md mx-auto lg:mx-0 mb-8">
            Masuk untuk mengelola data akademik, program studi, dan layanan
            mahasiswa Universitas Muhammadiyah Bengkulu secara terintegrasi.
        </p>

        <div class="grid grid-cols-3 gap-4 max-w-md mx-auto lg:mx-0">
            <div class="glass rounded-xl p-4 text-center">
                <i class="fa-solid fa-building-columns text-elegant-300 text-lg mb-2"></i>
                <p class="text-white font-bold text-lg leading-none">5+</p>
                <p class="text-[11px] text-slate-300 mt-1">Role</p>
            </div>
            <div class="glass rounded-xl p-4 text-center">
                <i class="fa-solid fa-shield-halved text-elegant-300 text-lg mb-2"></i>
                <p class="text-white font-bold text-lg leading-none">Audit</p>
                <p class="text-[11px] text-slate-300 mt-1">Log Aktif</p>
            </div>
            <div class="glass rounded-xl p-4 text-center">
                <i class="fa-solid fa-lock text-elegant-300 text-lg mb-2"></i>
                <p class="text-white font-bold text-lg leading-none">Hash</p>
                <p class="text-[11px] text-slate-300 mt-1">bcrypt</p>
            </div>
        </div>
    </div>

    <!-- ===== Panel Kanan: Form Login ===== -->
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-soft p-7 sm:p-9 border border-slate-100">

            <!-- Logo mobile -->
            <div class="lg:hidden flex items-center gap-3 mb-7 justify-center">
                <div class="w-12 h-12 rounded-lg bg-white border border-slate-200 flex items-center justify-center shadow-sm p-1.5">
                    <img src="<?= $logoSrc ?>" alt="Logo UMB" class="w-full h-full object-contain">
                </div>
                <div>
                    <p class="font-heading font-extrabold text-slate-900 text-sm leading-tight">SIM Mahasiswa</p>
                    <p class="text-elegant-700 text-[11px] font-semibold">UMB — Login Sistem</p>
                </div>
            </div>

            <!-- Judul -->
            <div class="mb-7">
                <h2 class="font-heading font-extrabold text-2xl text-slate-900 mb-1.5">Login Sistem</h2>
                <p class="text-slate-500 text-sm">Masukkan kredensial akun Anda untuk melanjutkan.</p>
            </div>

            <!-- Flash Success Message (misal setelah logout) -->
            <?php if (!empty($success)): ?>
            <div class="flex items-start gap-3 p-4 rounded-xl border bg-green-50 border-green-200 text-green-800 mb-5" role="alert">
                <i class="fa-solid fa-circle-check text-green-600 mt-0.5 text-base flex-shrink-0"></i>
                <div class="text-sm font-medium leading-relaxed flex-1"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <?php endif; ?>

            <!-- Flash Error Message -->
            <?php if (!empty($error)): ?>
            <div class="flex items-start gap-3 p-4 rounded-xl border bg-red-50 border-red-200 text-red-800 mb-5" role="alert">
                <i class="fa-solid fa-circle-exclamation text-red-600 mt-0.5 text-base flex-shrink-0"></i>
                <div class="text-sm font-medium leading-relaxed flex-1"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="<?= url('/login') ?>" method="POST" autocomplete="on" class="space-y-5">

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Username
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text"
                               id="username"
                               name="username"
                               required
                               maxlength="100"
                               placeholder="Masukkan username"
                               value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full pl-10 pr-4 py-3 rounded-md border border-slate-200 bg-slate-50 text-sm text-slate-900 placeholder-slate-400 focus:border-academic-500 focus:bg-white focus:ring-2 focus:ring-academic-100 transition-colors">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-semibold text-slate-700">
                            Password
                        </label>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               placeholder="Masukkan password"
                               class="w-full pl-10 pr-12 py-3 rounded-md border border-slate-200 bg-slate-50 text-sm text-slate-900 placeholder-slate-400 focus:border-academic-500 focus:bg-white focus:ring-2 focus:ring-academic-100 transition-colors">
                        <button type="button"
                                id="btn-toggle-password"
                                aria-label="Tampilkan password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-academic-700 focus:outline-none transition-colors">
                            <i class="fa-solid fa-eye" id="icon-eye"></i>
                            <i class="fa-solid fa-eye-slash hidden" id="icon-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-md bg-academic-600 hover:bg-academic-700 text-white font-bold text-sm transition-all focus:outline-none focus:ring-4 focus:ring-academic-200 cursor-pointer">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Masuk ke Sistem
                </button>
            </form>

            <!-- Link kembali -->
            <div class="mt-7 pt-6 border-t border-slate-100 text-center">
                <a href="<?= url('/') ?>"
                   class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-academic-700 font-medium transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali ke Beranda
                </a>
            </div>

        </div>

        <!-- Info keamanan -->
        <p class="text-center text-[11px] text-slate-400 mt-5 leading-relaxed">
            <i class="fa-solid fa-shield-halved text-elegant-600"></i>
            Sesi Anda terlindungi. Seluruh aktivitas login dicatat dalam sistem audit.
        </p>
    </div>

</div>

<!-- Toggle password visibility -->
<script>
(function () {
    var btn = document.getElementById('btn-toggle-password');
    var input = document.getElementById('password');
    var eye = document.getElementById('icon-eye');
    var eyeSlash = document.getElementById('icon-eye-slash');
    if (!btn || !input) return;

    btn.addEventListener('click', function () {
        var isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        eye.classList.toggle('hidden', !isPassword);
        eyeSlash.classList.toggle('hidden', isPassword);
        btn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
    });
})();
</script>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/auth.php'; 
?>
