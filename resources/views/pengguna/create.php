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
    <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($_SESSION['flash_error']) ?>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <form action="<?= url('/pengguna/store') ?>" method="POST">
        <div class="p-8">
            
            <!-- INFORMASI AKUN -->
            <div class="mb-8">
                <h4 class="text-[12px] font-bold text-gray-400 uppercase tracking-wider mb-6">INFORMASI AKUN</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" required placeholder="Contoh: user.staf" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                        <div class="relative" x-data="{ showPass: false }">
                            <input :type="showPass ? 'text' : 'password'" name="password" required placeholder="Minimal 8 karakter (huruf besar + angka)" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700 pr-10">
                            <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i class="fas" :class="showPass ? 'fa-eye' : 'fa-eye-slash'"></i>
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">Minimal 8 karakter, wajib ada huruf besar (A-Z) dan angka (0-9).</p>
                    </div>
                </div>
            </div>

            <!-- PROFIL PENGGUNA -->
            <div class="mb-8">
                <h4 class="text-[12px] font-bold text-gray-400 uppercase tracking-wider mb-6">PROFIL PENGGUNA</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" required placeholder="Nama lengkap beserta gelar" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" placeholder="email@mercu.ac.id" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">No. Handphone / WhatsApp</label>
                        <input type="text" name="no_hp" placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                    </div>
                </div>
            </div>

            <!-- HAK AKSES & PENEMPATAN -->
            <div class="mb-4">
                <h4 class="text-[12px] font-bold text-gray-400 uppercase tracking-wider mb-6">HAK AKSES & PENEMPATAN</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Role / Tingkat Akses <span class="text-red-500">*</span></label>
                        <select name="id_role" required class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                            <option value="">-- Pilih Role --</option>
                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['namaRole'] ?? $r['nama_role'] ?? $r['kodeRole'] ?? "Role {$r['id']}") ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="1">Administrator</option>
                                <option value="2">Bagian Akademik (BAAK)</option>
                                <option value="3">Dosen</option>
                                <option value="4">Mahasiswa</option>
                                <option value="5">Rektorat</option>
                                <option value="6">Dekanat</option>
                                <option value="7">Kaprodi</option>
                                <option value="8">Admin Prodi</option>
                                <option value="9">Admin Dekanat</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Status Akun <span class="text-red-500">*</span></label>
                        <select name="status_aktif" required class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Fakultas</label>
                        <select name="id_fakultas" id="create_id_fakultas" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                            <option value="">-- Pilih Fakultas (Opsional) --</option>
                            <?php if (!empty($fakultas)): ?>
                                <?php foreach ($fakultas as $f): ?>
                                    <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['namaFakultas'] ?? $f['nama_fakultas']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Program Studi</label>
                        <select name="id_program_studi" id="create_id_prodi" class="w-full px-4 py-2.5 text-[14px] bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-gray-700">
                            <option value="">-- Pilih Program Studi (Opsional) --</option>
                            <?php if (!empty($prodi)): ?>
                                <?php foreach ($prodi as $pr): ?>
                                    <option value="<?= $pr['id'] ?>" data-fakultas="<?= $pr['idFakultas'] ?? $pr['id_fakultas'] ?? '' ?>">
                                        <?= htmlspecialchars($pr['namaProgramStudi'] ?? $pr['nama_program_studi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-end gap-3 bg-white">
            <a href="<?= url('/pengguna') ?>" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-[#2563eb] hover:bg-blue-700 rounded-md transition-colors flex items-center gap-2 shadow-sm">
                <i class="fas fa-save"></i> Simpan Data
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fakultasSelect = document.getElementById('create_id_fakultas');
    const prodiSelect = document.getElementById('create_id_prodi');

    if (fakultasSelect && prodiSelect) {
        const prodiOptions = Array.from(prodiSelect.querySelectorAll('option:not([value=""])'));

        function filterProdi() {
            const selectedFakultas = fakultasSelect.value;
            const currentSelectedProdi = prodiSelect.value;

            prodiOptions.forEach(opt => {
                const optFakultas = opt.getAttribute('data-fakultas');
                if (!selectedFakultas || !optFakultas || optFakultas == selectedFakultas) {
                    opt.style.display = '';
                    opt.disabled = false;
                } else {
                    opt.style.display = 'none';
                    opt.disabled = true;
                    if (opt.value === currentSelectedProdi) {
                        prodiSelect.value = '';
                    }
                }
            });
        }

        fakultasSelect.addEventListener('change', filterProdi);
    }
});
</script>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
