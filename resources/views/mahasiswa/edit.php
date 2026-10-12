<?php ob_start(); 

$idMhs = (int)($mahasiswa['id'] ?? $mahasiswa['id_mahasiswa'] ?? 0);
$currentNpm = $old['npm'] ?? $mahasiswa['npm'] ?? '';
$currentNama = $old['nama_mahasiswa'] ?? $mahasiswa['namaMahasiswa'] ?? $mahasiswa['nama_mahasiswa'] ?? '';
$currentJk = $old['jenis_kelamin'] ?? $mahasiswa['jenisKelamin'] ?? $mahasiswa['jenis_kelamin'] ?? 'L';
$currentStatus = $old['status_mahasiswa'] ?? $mahasiswa['statusMahasiswa'] ?? $mahasiswa['status_mahasiswa'] ?? 'Aktif';
$currentTempatLahir = $old['tempat_lahir'] ?? $mahasiswa['tempatLahir'] ?? $mahasiswa['tempat_lahir'] ?? '';
$rawTglLahir = $old['tanggal_lahir'] ?? $mahasiswa['tanggalLahir'] ?? $mahasiswa['tanggal_lahir'] ?? '';
$currentTglLahir = !empty($rawTglLahir) ? date('Y-m-d', strtotime($rawTglLahir)) : '';
$rawTglMasuk = $old['tanggal_masuk'] ?? $mahasiswa['tanggalMasuk'] ?? $mahasiswa['tanggal_masuk'] ?? '';
$currentTglMasuk = !empty($rawTglMasuk) ? date('Y-m-d', strtotime($rawTglMasuk)) : date('Y-m-d');
$currentProdiId = (int)($old['id_program_studi'] ?? $mahasiswa['idProgramStudi'] ?? $mahasiswa['id_program_studi'] ?? 0);
$currentAlamat = $old['alamat'] ?? $mahasiswa['alamat'] ?? '';
?>

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
        <li class="text-slate-900 font-semibold">Edit</li>
    </ol>
</nav>

<div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
    <div>
        <h2 class="font-bold text-xl text-slate-900">Edit Data Mahasiswa</h2>
        <p class="text-sm text-slate-500 mt-1">
            NPM:
            <span class="font-mono font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                <?= htmlspecialchars($mahasiswa['npm'] ?? '') ?>
            </span>
        </p>
    </div>
    <div class="flex gap-2">
        <a href="<?= url('/mahasiswa/detail/' . $idMhs) ?>"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition-colors">
            <i class="fa-solid fa-eye"></i>
            Detail
        </a>
        <a href="<?= url('/mahasiswa') ?>"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>
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

<!-- Form Edit -->
<div class="bg-white rounded-md shadow-sm border border-slate-200">
    <form action="<?= url('/mahasiswa/update/' . $idMhs) ?>" method="POST" class="p-6 sm:p-8">

        <!-- Identitas Mahasiswa -->
        <fieldset class="mb-8">
            <legend class="font-bold text-slate-900 flex items-center gap-2 mb-5">
                <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                    <i class="fa-solid fa-id-card text-sm"></i>
                </span>
                Identitas Mahasiswa
            </legend>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label for="npm" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        NPM <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="npm" id="npm" required maxlength="30"
                           value="<?= htmlspecialchars($currentNpm) ?>"
                           class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                </div>

                <div>
                    <label for="nama_mahasiswa" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_mahasiswa" id="nama_mahasiswa" required maxlength="200"
                           value="<?= htmlspecialchars($currentNama) ?>"
                           class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                </div>

                <div>
                    <label for="jenis_kelamin" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_kelamin" id="jenis_kelamin" required
                            class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                        <option value="L" <?= $currentJk === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= $currentJk === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label for="status_mahasiswa" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Status Mahasiswa <span class="text-red-500">*</span>
                    </label>
                    <select name="status_mahasiswa" id="status_mahasiswa" required
                            class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                        <?php foreach (['Aktif', 'Cuti', 'Lulus', 'Mengundurkan Diri', 'Drop Out', 'Tidak Aktif'] as $s): ?>
                            <option value="<?= htmlspecialchars($s) ?>" <?= ($currentStatus === $s) ? 'selected' : '' ?>>
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
                    <label for="tempat_lahir" class="block text-sm font-semibold text-slate-700 mb-1.5">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="tempat_lahir" maxlength="100"
                           value="<?= htmlspecialchars($currentTempatLahir) ?>"
                           class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                </div>
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                           value="<?= htmlspecialchars($currentTglLahir) ?>"
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
                           value="<?= htmlspecialchars($currentTglMasuk) ?>"
                           class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                </div>

                <div>
                    <label for="id_program_studi" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Program Studi <span class="text-red-500">*</span>
                    </label>
                    <select name="id_program_studi" id="id_program_studi" required
                            class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors">
                        <?php foreach ($prodiList as $p): 
                            $pId = (int)($p['id'] ?? $p['id_program_studi'] ?? 0);
                            $pNama = $p['namaProgramStudi'] ?? $p['nama_program_studi'] ?? '';
                            $pJenjang = $p['jenjang'] ?? 'S1';
                        ?>
                            <option value="<?= $pId ?>" <?= ($currentProdiId === $pId) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pNama) ?> (<?= htmlspecialchars($pJenjang) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mt-5">
                <label for="alamat" class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" maxlength="1000"
                          class="w-full px-4 py-2.5 rounded-md border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-colors resize-y"><?= htmlspecialchars($currentAlamat) ?></textarea>
            </div>
        </fieldset>

        <!-- Info akun -->
        <div class="mb-6 p-4 rounded-md bg-blue-50 border border-blue-200 flex items-start gap-3">
            <i class="fa-solid fa-circle-info text-blue-600 mt-0.5 text-base"></i>
            <p class="text-xs text-blue-900 leading-relaxed">
                Perubahan NPM tidak memperbarui username akun login yang sudah ada secara otomatis.
                Jika akun login perlu disesuaikan, lakukan melalui modul <strong>Data Pengguna</strong> (Admin).
            </p>
        </div>

        <!-- Tombol aksi -->
        <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
            <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-2.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm transition-colors shadow-sm focus:outline-none focus:ring-4 focus:ring-blue-100">
                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Perubahan
            </button>
            <a href="<?= url('/mahasiswa') ?>"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-2.5 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm transition-colors">
                <i class="fa-solid fa-xmark"></i>
                Batal
            </a>
        </div>

    </form>
</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
