<?php ob_start(); ?>

<!-- Breadcrumb -->
<nav class="mb-5" aria-label="Breadcrumb">
    <ol class="flex items-center gap-2 text-xs text-slate-500 flex-wrap">
        <li>
            <a href="<?= url('/dashboard') ?>" class="hover:text-blue-600 font-medium">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
        </li>
        <li aria-hidden="true"><i class="fa-solid fa-chevron-right text-[9px]"></i></li>
        <li>
            <a href="<?= url('/mahasiswa') ?>" class="hover:text-blue-600 font-medium">Data Mahasiswa</a>
        </li>
        <li aria-hidden="true"><i class="fa-solid fa-chevron-right text-[9px]"></i></li>
        <li class="text-slate-900 font-semibold">Tambah</li>
    </ol>
</nav>

<div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
    <div>
        <h2 class="font-bold text-xl text-slate-900">Tambah Mahasiswa Baru</h2>
        <p class="text-sm text-slate-500 mt-1">Lengkapi formulir berikut untuk mendaftarkan mahasiswa baru.</p>
    </div>
    <a href="<?= url('/mahasiswa') ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition-colors">
        <i class="fa-solid fa-arrow-left"></i>
        Kembali
    </a>
</div>

<!-- Flash Error -->
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

<!-- Form -->
<div class="bg-white rounded-md shadow-sm border border-slate-200">
    <form action="<?= url('/mahasiswa/store') ?>" method="POST" id="form-mahasiswa" class="p-6 sm:p-8">

        <!-- Identitas Mahasiswa -->
        <fieldset class="mb-8">
            <legend class="font-bold text-slate-900 flex items-center gap-2 mb-5">
                <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                    <i class="fa-solid fa-id-card text-sm"></i>
                </span>
                Identitas Mahasiswa
            </legend>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- NPM -->
                <div>
                    <label for="npm" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        NPM <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="npm" id="npm" required maxlength="30"
                           placeholder="cth. 20260001"
                           value="<?= htmlspecialchars($old['npm'] ?? '') ?>"
                           class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                    <p class="text-[11px] text-slate-400 mt-1">Harus unik — digunakan juga sebagai username login bila akun dibuat.</p>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="nama_mahasiswa" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_mahasiswa" id="nama_mahasiswa" required maxlength="200"
                           placeholder="Nama lengkap sesuai ijazah"
                           value="<?= htmlspecialchars($old['nama_mahasiswa'] ?? '') ?>"
                           class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_kelamin" id="jenis_kelamin" required
                            class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                        <option value="">— Pilih Jenis Kelamin —</option>
                        <option value="L" <?= ($old['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($old['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>

                <!-- Status Mahasiswa -->
                <div>
                    <label for="status_mahasiswa" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Status Mahasiswa <span class="text-red-500">*</span>
                    </label>
                    <select name="status_mahasiswa" id="status_mahasiswa" required
                            class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                        <?php foreach (['Aktif', 'Cuti', 'Lulus', 'Mengundurkan Diri', 'Drop Out', 'Tidak Aktif'] as $s):
                            $selected = ($old['status_mahasiswa'] ?? 'Aktif') === $s ? 'selected' : '';
                        ?>
                            <option value="<?= htmlspecialchars($s) ?>" <?= $selected ?>>
                                <?= htmlspecialchars($s) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>
        </fieldset>

        <!-- Data Kelahiran -->
        <fieldset class="mb-8">
            <legend class="font-bold text-slate-900 flex items-center gap-2 mb-5">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <i class="fa-solid fa-cake-candles text-sm"></i>
                </span>
                Data Kelahiran
            </legend>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label for="tempat_lahir" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tempat Lahir
                    </label>
                    <input type="text" name="tempat_lahir" id="tempat_lahir" maxlength="100"
                           placeholder="cth. Bengkulu"
                           value="<?= htmlspecialchars($old['tempat_lahir'] ?? '') ?>"
                           class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                </div>

                <div>
                    <label for="tanggal_lahir" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tanggal Lahir
                    </label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                           value="<?= htmlspecialchars($old['tanggal_lahir'] ?? '') ?>"
                           class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                </div>

            </div>
        </fieldset>

        <!-- Data Akademik -->
        <fieldset class="mb-8">
            <legend class="font-bold text-slate-900 flex items-center gap-2 mb-5">
                <span class="w-8 h-8 rounded-lg bg-slate-800 text-white flex items-center justify-center">
                    <i class="fa-solid fa-graduation-cap text-sm"></i>
                </span>
                Data Akademik
            </legend>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label for="tanggal_masuk" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tanggal Masuk <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" required
                           value="<?= htmlspecialchars($old['tanggal_masuk'] ?? date('Y-m-d')) ?>"
                           class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                </div>

                <div>
                    <label for="id_program_studi" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Program Studi <span class="text-red-500">*</span>
                    </label>
                    <select name="id_program_studi" id="id_program_studi" required
                            class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                        <option value="">— Pilih Program Studi —</option>
                        <?php if (!empty($prodiList)): ?>
                            <?php foreach ($prodiList as $p): 
                                $pId = (int)($p['id'] ?? $p['id_program_studi'] ?? 0);
                                $pNama = $p['namaProgramStudi'] ?? $p['nama_program_studi'] ?? '';
                                $pJenjang = $p['jenjang'] ?? 'S1';
                                $selected = ((int)($old['id_program_studi'] ?? 0) === $pId) ? 'selected' : '';
                            ?>
                                <option value="<?= $pId ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($pNama) ?> (<?= htmlspecialchars($pJenjang) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

            </div>

            <!-- Alamat -->
            <div class="mt-5">
                <label for="alamat" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Alamat
                </label>
                <textarea name="alamat" id="alamat" rows="3" maxlength="1000"
                          placeholder="Alamat lengkap mahasiswa..."
                          class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors resize-y"><?= htmlspecialchars($old['alamat'] ?? '') ?></textarea>
            </div>
        </fieldset>

        <!-- Akun Login -->
        <fieldset class="mb-8 p-5 rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/40">
            <legend class="px-2 font-bold text-blue-800 flex items-center gap-2">
                <i class="fa-solid fa-key"></i>
                Akun Login
            </legend>

            <label for="buat_akun" class="flex items-start gap-3 cursor-pointer p-3 rounded-md hover:bg-white/60 transition-colors">
                <input type="checkbox" name="buat_akun" id="buat_akun" value="1"
                       <?= !empty($old['buat_akun']) ? 'checked' : '' ?>
                       class="mt-0.5 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>
                    <span class="block text-sm font-semibold text-slate-800">Buatkan Akun Login Otomatis</span>
                    <span class="block text-xs text-slate-500 mt-0.5">
                        Jika dicentang, sistem membuat akun di tabel <code class="bg-white px-1.5 py-0.5 rounded font-mono text-slate-700">pengguna</code>
                        dengan <strong>username = NPM</strong> dan <strong>password default = Mhs@[NPM]</strong>
                        (role: Mahasiswa). User dapat mengganti password setelah login.
                    </span>
                </span>
            </label>

            <div id="preview-akun" class="hidden mt-3 ml-3 p-3 rounded-md bg-white border border-blue-200 text-xs text-slate-600">
                <i class="fa-solid fa-circle-info text-blue-600 mr-1"></i>
                Preview: username <code id="preview-username" class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-blue-700 font-semibold">—</code>,
                password default = <code id="preview-password" class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-emerald-700 font-semibold">—</code>
            </div>
        </fieldset>

        <!-- Tombol aksi -->
        <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
            <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-2.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm transition-colors shadow-sm focus:outline-none focus:ring-4 focus:ring-blue-100">
                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Data Mahasiswa
            </button>
            <a href="<?= url('/mahasiswa') ?>"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-2.5 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm transition-colors">
                <i class="fa-solid fa-xmark"></i>
                Batal
            </a>
        </div>

    </form>
</div>

<script>
(function () {
    var chk = document.getElementById('buat_akun');
    var npm = document.getElementById('npm');
    var preview = document.getElementById('preview-akun');
    var previewUser = document.getElementById('preview-username');
    var previewPass = document.getElementById('preview-password');

    function syncPreview() {
        if (!chk || !preview) return;
        if (chk.checked) {
            preview.classList.remove('hidden');
            var val = (npm && npm.value.trim()) ? npm.value.trim() : '';
            if (previewUser) {
                previewUser.textContent = val ? val : '—';
            }
            if (previewPass) {
                previewPass.textContent = val ? ('Mhs@' + val) : '—';
            }
        } else {
            preview.classList.add('hidden');
        }
    }

    if (chk) chk.addEventListener('change', syncPreview);
    if (npm) npm.addEventListener('input', syncPreview);
    syncPreview();
})();
</script>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
