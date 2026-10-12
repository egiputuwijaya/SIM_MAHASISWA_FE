<?php 
ob_start(); 
$u = $univ ?? [];
?>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <!-- Card Header -->
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded bg-green-100 text-green-600 flex items-center justify-center text-lg">
                <i class="fas fa-building"></i>
            </div>
            <h3 class="text-[17px] font-bold text-gray-800">Profil Institusi</h3>
        </div>
        <a href="<?= url('/profil-universitas/edit') ?>" class="bg-[#3b82f6] hover:bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors flex items-center gap-2 shadow-sm">
            <i class="fas fa-edit"></i> Edit Data
        </a>
    </div>

    <!-- Flash message -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="m-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <!-- Card Body -->
    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
            
            <!-- Left Column -->
            <div class="space-y-6">
                <div>
                    <h4 class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">KODE / SINGKATAN</h4>
                    <p class="text-[15px] font-semibold text-gray-800"><?= htmlspecialchars($u['kodeUniversitas'] ?? $u['kode_universitas'] ?? 'UMB') ?></p>
                </div>
                
                <div>
                    <h4 class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">NAMA UNIVERSITAS</h4>
                    <p class="text-[16px] font-bold text-gray-800"><?= htmlspecialchars($u['namaUniversitas'] ?? $u['nama_universitas'] ?? 'Universitas Muhammadiyah Bengkulu') ?></p>
                </div>
                
                <div>
                    <h4 class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">SLOGAN / MOTTO</h4>
                    <p class="text-[14px] text-gray-600 italic"><?= htmlspecialchars($u['slogan'] ?? 'Belum ada slogan') ?></p>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <div>
                    <h4 class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">ALAMAT LENGKAP</h4>
                    <p class="text-[14px] text-gray-700 leading-relaxed">
                        <?= nl2br(htmlspecialchars($u['alamat'] ?? 'Jl. Bali, Kampung Bali')) ?><br>
                        <?= htmlspecialchars(($u['kota'] ?? 'Bengkulu') . ', ' . ($u['provinsi'] ?? 'Bengkulu') . ' ' . ($u['kodePos'] ?? $u['kode_pos'] ?? '')) ?>
                    </p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">TELEPON</h4>
                        <p class="text-[14px] text-gray-800"><?= htmlspecialchars($u['telepon'] ?? '-') ?></p>
                    </div>
                    <div>
                        <h4 class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">EMAIL</h4>
                        <p class="text-[14px] text-gray-800"><?= htmlspecialchars($u['email'] ?? '-') ?></p>
                    </div>
                </div>

                <div>
                    <h4 class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">WEBSITE</h4>
                    <p class="text-[14px] text-blue-500 hover:underline">
                        <?php if (!empty($u['website'])): ?>
                            <a href="<?= htmlspecialchars($u['website']) ?>" target="_blank"><?= htmlspecialchars($u['website']) ?></a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card Footer -->
    <div class="px-8 py-4 border-t border-gray-100 bg-gray-50/50">
        <p class="text-[11px] text-gray-400">Terakhir diubah: <?= htmlspecialchars($u['updatedAt'] ?? $u['updated_at'] ?? date('Y-m-d H:i:s')) ?></p>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
