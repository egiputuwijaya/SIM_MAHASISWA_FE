<?php ob_start(); ?>

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

<?php if (isset($_SESSION['flash_error'])): ?>
<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
    <i class="fas fa-exclamation-circle mr-2"></i> <?= $_SESSION['flash_error'] ?>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<?php if (isset($error)): ?>
<div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
    <i class="fas fa-info-circle mr-2"></i> <?= $error ?> <br>
    <span class="text-gray-600 mt-1 block">Tabel di bawah ini menampilkan data statis (contoh) karena API belum tersedia.</span>
</div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <!-- Filter Bar -->
    <div class="px-6 py-4 border-b border-gray-100 flex items-center">
        <div class="flex items-center gap-3">
            <span class="text-[13px] font-semibold text-gray-700">Filter Role:</span>
            <select class="text-[13px] bg-gray-50 border border-gray-200 text-gray-700 rounded py-1.5 px-3 focus:outline-none focus:ring-1 focus:ring-blue-500 w-48">
                <option>-- Semua Role --</option>
            </select>
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
                <!-- Row 1 -->
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-4 px-6 text-center text-gray-500">1</td>
                    <td class="py-4 px-6">
                        <div class="font-bold text-gray-800">Admin SIM Mhs UMB</div>
                        <div class="text-[12px] text-gray-500 mt-0.5"><span class="font-medium text-gray-400">@admin</span> &bull; harrywitriyono@umb.ac.id</div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-semibold bg-purple-100 text-purple-700">Admin</span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-green-500 font-bold text-[12px]">Aktif</span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <a href="<?= url('/pengguna/edit/1') ?>" class="w-7 h-7 rounded flex items-center justify-center bg-orange-50 text-orange-500 hover:bg-orange-100 transition-colors">
                                <i class="fas fa-edit text-[11px]"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                
                <!-- Row 2 -->
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-4 px-6 text-center text-gray-500">2</td>
                    <td class="py-4 px-6">
                        <div class="font-bold text-gray-800">Operator Program Studi Teknik Informatika</div>
                        <div class="text-[12px] text-gray-500 mt-0.5"><span class="font-medium text-gray-400">@operator.ti</span> &bull; operator.ti@umb.ac.id</div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-semibold bg-blue-100 text-blue-700">Operator Program Studi</span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-green-500 font-bold text-[12px]">Aktif</span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <a href="<?= url('/pengguna/edit/2') ?>" class="w-7 h-7 rounded flex items-center justify-center bg-orange-50 text-orange-500 hover:bg-orange-100 transition-colors">
                                <i class="fas fa-edit text-[11px]"></i>
                            </a>
                            <form action="<?= url('/pengguna/delete/2') ?>" method="POST" onsubmit="return confirm('Hapus data?')" class="inline">
                                <button type="submit" class="w-7 h-7 rounded flex items-center justify-center bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                                    <i class="fas fa-trash text-[11px]"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Row 3 -->
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-4 px-6 text-center text-gray-500">3</td>
                    <td class="py-4 px-6">
                        <div class="font-bold text-gray-800">Operator Dekanat Fakultas Teknik</div>
                        <div class="text-[12px] text-gray-500 mt-0.5"><span class="font-medium text-gray-400">@dekanat.ft</span> &bull; dekanat.ft@umb.ac.id</div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-semibold bg-amber-100 text-amber-700">Dekanat</span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-green-500 font-bold text-[12px]">Aktif</span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <a href="<?= url('/pengguna/edit/3') ?>" class="w-7 h-7 rounded flex items-center justify-center bg-orange-50 text-orange-500 hover:bg-orange-100 transition-colors">
                                <i class="fas fa-edit text-[11px]"></i>
                            </a>
                            <form action="<?= url('/pengguna/delete/3') ?>" method="POST" onsubmit="return confirm('Hapus data?')" class="inline">
                                <button type="submit" class="w-7 h-7 rounded flex items-center justify-center bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                                    <i class="fas fa-trash text-[11px]"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Row 4 -->
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-4 px-6 text-center text-gray-500">4</td>
                    <td class="py-4 px-6">
                        <div class="font-bold text-gray-800">Operator Rektorat</div>
                        <div class="text-[12px] text-gray-500 mt-0.5"><span class="font-medium text-gray-400">@rektorat</span> &bull; rektorat@umb.ac.id</div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-700">Rektorat</span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-green-500 font-bold text-[12px]">Aktif</span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <a href="<?= url('/pengguna/edit/4') ?>" class="w-7 h-7 rounded flex items-center justify-center bg-orange-50 text-orange-500 hover:bg-orange-100 transition-colors">
                                <i class="fas fa-edit text-[11px]"></i>
                            </a>
                            <form action="<?= url('/pengguna/delete/4') ?>" method="POST" onsubmit="return confirm('Hapus data?')" class="inline">
                                <button type="submit" class="w-7 h-7 rounded flex items-center justify-center bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                                    <i class="fas fa-trash text-[11px]"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Row 5 -->
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-4 px-6 text-center text-gray-500">5</td>
                    <td class="py-4 px-6">
                        <div class="font-bold text-gray-800">Contoh Mahasiswa</div>
                        <div class="text-[12px] text-gray-500 mt-0.5"><span class="font-medium text-gray-400">@20250001</span> &bull; 20250001@student.umb.ac.id</div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-semibold bg-gray-100 text-gray-600">Mahasiswa</span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-green-500 font-bold text-[12px]">Aktif</span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <a href="<?= url('/pengguna/edit/5') ?>" class="w-7 h-7 rounded flex items-center justify-center bg-orange-50 text-orange-500 hover:bg-orange-100 transition-colors">
                                <i class="fas fa-edit text-[11px]"></i>
                            </a>
                            <form action="<?= url('/pengguna/delete/5') ?>" method="POST" onsubmit="return confirm('Hapus data?')" class="inline">
                                <button type="submit" class="w-7 h-7 rounded flex items-center justify-center bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                                    <i class="fas fa-trash text-[11px]"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
