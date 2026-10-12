<?php ob_start(); ?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Data Mahasiswa</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi akademik dan data pribadi mahasiswa.</p>
    </div>
    <a href="<?= url('/mahasiswa/create') ?>" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <i class="fas fa-plus mr-2"></i> Tambah Data
    </a>
</div>

<?php if(isset($_GET['success'])): ?>
<div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-100 text-green-700 text-sm flex items-center shadow-sm" x-data="{ show: true }" x-show="show" x-transition>
    <i class="fas fa-check-circle mr-3 text-green-500 text-lg"></i>
    <span class="flex-1 font-medium">Data mahasiswa berhasil ditambahkan!</span>
    <button @click="show = false" class="text-green-400 hover:text-green-600 focus:outline-none"><i class="fas fa-times"></i></button>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
        <form action="<?= url('/mahasiswa') ?>" method="GET" class="relative max-w-md w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" name="q" value="<?= htmlspecialchars($q ?? '') ?>" 
                   class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors shadow-sm" 
                   placeholder="Cari berdasarkan NPM atau Nama...">
        </form>
        <div class="flex items-center gap-2">
            <button class="px-3 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors shadow-sm">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NPM / Nama</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Program Studi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <?php if (empty($mahasiswa)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                            <p>Tidak ada data mahasiswa ditemukan.</p>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($mahasiswa as $mhs): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <?php if(!empty($mhs['foto'])): ?>
                                        <img class="h-10 w-10 rounded-full object-cover shadow-sm" src="<?= htmlspecialchars($mhs['foto']) ?>" alt="">
                                    <?php else: ?>
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 flex items-center justify-center font-bold text-lg shadow-sm border border-blue-200">
                                            <?= substr($mhs['nama_mahasiswa'] ?? 'M', 0, 1) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($mhs['nama_mahasiswa'] ?? '-') ?></div>
                                    <div class="text-xs text-gray-500 font-mono mt-0.5"><?= htmlspecialchars($mhs['npm'] ?? '-') ?> &bull; <?= htmlspecialchars($mhs['jenis_kelamin'] ?? '-') ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= htmlspecialchars($mhs['nama_program_studi'] ?? 'Prodi TI') ?></div>
                            <div class="text-xs text-gray-500 mt-0.5">Fakultas Teknik</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                Aktif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mx-1 p-2 rounded-md hover:bg-blue-50 transition-colors" title="Detail"><i class="fas fa-eye"></i></button>
                            <button class="text-amber-600 hover:text-amber-900 mx-1 p-2 rounded-md hover:bg-amber-50 transition-colors" title="Edit"><i class="fas fa-edit"></i></button>
                            <button class="text-red-600 hover:text-red-900 mx-1 p-2 rounded-md hover:bg-red-50 transition-colors" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if(!empty($pagination)): ?>
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-slate-50/50">
        <div class="text-sm text-gray-500">
            Menampilkan halaman <span class="font-medium"><?= $pagination['current_page'] ?? 1 ?></span> dari <span class="font-medium"><?= $pagination['total_pages'] ?? 1 ?></span>
        </div>
        <div class="flex gap-1">
            <button class="px-3 py-1.5 border border-gray-200 rounded-md text-sm text-gray-600 hover:bg-white bg-transparent disabled:opacity-50 disabled:cursor-not-allowed shadow-sm transition-colors">&laquo; Prev</button>
            <button class="px-3 py-1.5 border border-gray-200 rounded-md text-sm text-gray-600 hover:bg-white bg-transparent disabled:opacity-50 disabled:cursor-not-allowed shadow-sm transition-colors">Next &raquo;</button>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
