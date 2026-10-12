<?php 
ob_start(); 
$currentUserId = $_SESSION['user']['id'] ?? null;
$currentUsername = $_SESSION['user']['username'] ?? '';
?>

<div x-data="{ deleteModalOpen: false, deleteAction: '', deleteUserName: '' }">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Manajemen Pengguna</h2>
            <p class="text-[13px] text-gray-500 mt-1">Kelola akun dan hak akses pengguna sistem.</p>
        </div>
        <div>
            <a href="<?= url('/pengguna/create') ?>" class="bg-[#2563eb] hover:bg-blue-700 text-white text-[13px] font-medium py-2 px-4 rounded-md transition-colors flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus"></i> Tambah Pengguna
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
    <div x-data="{ show: true }" x-show="show" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center justify-between" role="alert">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2 text-green-600"></i> 
            <span><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
        </div>
        <button type="button" @click="show = false" class="text-green-600 hover:text-green-800 text-sm">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <?php unset($_SESSION['flash_success']); endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
    <div x-data="{ show: true }" x-show="show" class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center justify-between" role="alert">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2 text-red-600"></i> 
            <span><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
        </div>
        <button type="button" @click="show = false" class="text-red-600 hover:text-red-800 text-sm">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <?php unset($_SESSION['flash_error']); endif; ?>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <!-- Filter Bar -->
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="text-[13px] font-semibold text-gray-700">Filter Role:</span>
                <select onchange="window.location.href = '<?= url('/pengguna') ?>' + (this.value ? '?id_role=' + this.value : '')" class="text-[13px] bg-gray-50 border border-gray-200 text-gray-700 rounded py-1.5 px-3 focus:outline-none focus:ring-1 focus:ring-blue-500 w-56">
                    <option value="">-- Semua Role --</option>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= (isset($selectedRole) && $selectedRole == $r['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['namaRole'] ?? $r['nama_role'] ?? $r['kodeRole']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="text-[12px] text-gray-400">
                Total: <?= count($pengguna ?? []) ?> pengguna
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-[12px] uppercase border-b border-gray-100 font-semibold tracking-wider">
                        <th class="py-4 px-6 font-semibold w-16 text-center">No</th>
                        <th class="py-4 px-6 font-semibold">User & Email</th>
                        <th class="py-4 px-6 font-semibold">Role / Akses</th>
                        <th class="py-4 px-6 font-semibold">Status</th>
                        <th class="py-4 px-6 font-semibold text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-[14px] text-gray-700 divide-y divide-gray-100">
                    <?php if (empty($pengguna)): ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 text-sm">
                                <i class="fas fa-users text-3xl mb-2 text-gray-300 block"></i>
                                Tidak ada data pengguna yang ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        foreach ($pengguna as $p): 
                            $nama = $p['namaLengkap'] ?? $p['nama_lengkap'] ?? $p['username'] ?? '-';
                            $uname = $p['username'] ?? '-';
                            $email = $p['email'] ?? '-';
                            $roleName = $p['role']['namaRole'] ?? $p['role']['nama_role'] ?? ($p['idRole'] == 1 ? 'Administrator' : 'Pengguna');
                            $status = $p['statusAktif'] ?? $p['status_aktif'] ?? 'Aktif';
                            $idUser = $p['id'] ?? $p['id_pengguna'] ?? $no;
                            $isSelf = ($currentUserId && (int)$idUser === (int)$currentUserId) || ($uname === $currentUsername);
                        ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 text-center text-gray-500"><?= $no++ ?></td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-800 flex items-center gap-2">
                                    <span><?= htmlspecialchars($nama) ?></span>
                                    <?php if ($isSelf): ?>
                                        <span class="text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-normal">Anda</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-[12px] text-gray-500 mt-0.5">
                                    <span class="font-medium text-gray-400">@<?= htmlspecialchars($uname) ?></span> 
                                    <?php if ($email && $email !== '-'): ?> &bull; <?= htmlspecialchars($email) ?><?php endif; ?>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-semibold bg-blue-100 text-blue-700">
                                    <?= htmlspecialchars($roleName) ?>
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <?php if ($status === 'Aktif'): ?>
                                    <span class="text-green-500 font-bold text-[12px]">Aktif</span>
                                <?php else: ?>
                                    <span class="text-gray-400 font-bold text-[12px]">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?= url('/pengguna/edit/' . $idUser) ?>" class="w-7 h-7 rounded flex items-center justify-center bg-orange-50 text-orange-500 hover:bg-orange-100 transition-colors" title="Edit Pengguna">
                                        <i class="fas fa-edit text-[11px]"></i>
                                    </a>

                                    <?php if ($isSelf): ?>
                                        <button type="button" disabled class="w-7 h-7 rounded flex items-center justify-center bg-gray-100 text-gray-300 cursor-not-allowed" title="Akun Anda yang sedang aktif tidak dapat dihapus">
                                            <i class="fas fa-trash text-[11px]"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" 
                                                @click="deleteModalOpen = true; deleteAction = '<?= url('/pengguna/delete/' . $idUser) ?>'; deleteUserName = '<?= addslashes(htmlspecialchars($nama)) ?> (@<?= addslashes(htmlspecialchars($uname)) ?>)';"
                                                class="w-7 h-7 rounded flex items-center justify-center bg-red-50 text-red-500 hover:bg-red-100 transition-colors" 
                                                title="Hapus Pengguna">
                                            <i class="fas fa-trash text-[11px]"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="deleteModalOpen" 
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="deleteModalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.outside="deleteModalOpen = false"
                 class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                
                <div class="bg-white p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                            <i class="fas fa-trash-alt text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900" id="modal-title">Hapus Pengguna</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Apakah Anda yakin ingin menghapus akun <span class="font-semibold text-gray-800" x-text="deleteUserName"></span>?
                            </p>
                            <p class="mt-1 text-xs text-red-500">
                                Tindakan ini permanen. Jika pengguna memiliki relasi aktif, penghapusan akan dicegah.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <form :action="deleteAction" method="POST" class="inline">
                        <button type="submit" class="inline-flex justify-center items-center gap-1.5 rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash text-xs"></i> Ya, Hapus Pengguna
                        </button>
                    </form>
                    <button type="button" @click="deleteModalOpen = false" class="inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
