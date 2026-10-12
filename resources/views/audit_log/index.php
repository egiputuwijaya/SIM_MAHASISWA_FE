<?php ob_start(); 

function badge_aksi_class(string $aksi): string {
    return match (strtoupper($aksi)) {
        'LOGIN'           => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'LOGOUT'          => 'bg-slate-100 text-slate-700 border-slate-200',
        'INSERT'          => 'bg-blue-100 text-blue-700 border-blue-200',
        'UPDATE'          => 'bg-amber-100 text-amber-700 border-amber-200',
        'DELETE'          => 'bg-red-100 text-red-700 border-red-200',
        'UPDATE_PASSWORD' => 'bg-purple-100 text-purple-700 border-purple-200',
        default           => 'bg-slate-100 text-slate-600 border-slate-200',
    };
}
?>

<div x-data="{ 
    modalOpen: false, 
    selectedOld: null, 
    selectedNew: null, 
    selectedTable: '',
    openDetail(oldData, newData, table) {
        this.selectedOld = oldData;
        this.selectedNew = newData;
        this.selectedTable = table || 'DATA';
        this.modalOpen = true;
    },
    formatJson(data) {
        if (!data || data === 'null' || (typeof data === 'string' && data.trim() === '')) {
            return '(Tidak ada data)';
        }
        if (typeof data === 'object') {
            return JSON.stringify(data, null, 4);
        }
        try {
            var parsed = JSON.parse(data);
            return JSON.stringify(parsed, null, 4);
        } catch(e) {
            return data;
        }
    }
}">

    <!-- Header halaman -->
    <div class="mb-6">
        <h2 class="font-bold text-2xl text-slate-900 tracking-tight">Audit Log</h2>
        <p class="text-sm text-slate-500 mt-1">Pantau seluruh riwayat aktivitas yang terjadi pada sistem.</p>
    </div>

    <!-- Kartu Filter & Pencarian -->
    <div class="bg-white rounded-md shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="p-5 border-b border-slate-100 bg-slate-50/50">
            <form action="<?= url('/audit-log') ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                
                <!-- Mulai Tanggal -->
                <div>
                    <label for="start_date" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mulai Tanggal</label>
                    <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($filterStart) ?>"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors">
                </div>

                <!-- Sampai Tanggal -->
                <div>
                    <label for="end_date" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($filterEnd) ?>"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors">
                </div>

                <!-- Jenis Aksi -->
                <div>
                    <label for="aksi" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jenis Aksi</label>
                    <select id="aksi" name="aksi"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors">
                        <option value="">Semua Aksi</option>
                        <option value="LOGIN" <?= $filterAksi === 'LOGIN' ? 'selected' : '' ?>>LOGIN</option>
                        <option value="LOGOUT" <?= $filterAksi === 'LOGOUT' ? 'selected' : '' ?>>LOGOUT</option>
                        <option value="INSERT" <?= $filterAksi === 'INSERT' ? 'selected' : '' ?>>INSERT</option>
                        <option value="UPDATE" <?= $filterAksi === 'UPDATE' ? 'selected' : '' ?>>UPDATE</option>
                        <option value="DELETE" <?= $filterAksi === 'DELETE' ? 'selected' : '' ?>>DELETE</option>
                    </select>
                </div>

                <!-- Username Pelaku -->
                <div>
                    <label for="username" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Username Pelaku</label>
                    <input type="text" id="username" name="username" placeholder="Cari username..." value="<?= htmlspecialchars($filterUsername) ?>"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors">
                </div>

                <!-- Tombol Filter & Reset -->
                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-md shadow-sm transition-colors focus:ring-2 focus:ring-blue-200">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                    <?php if ($filterAksi !== '' || $filterStart !== '' || $filterEnd !== '' || $filterUsername !== ''): ?>
                        <a href="<?= url('/audit-log') ?>"
                           class="inline-flex items-center justify-center px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold rounded-md transition-colors"
                           title="Reset filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </div>

            </form>
        </div>

        <!-- Tabel Log Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-100">
                    <tr>
                        <th class="text-left font-semibold px-4 py-3 w-40">Waktu</th>
                        <th class="text-left font-semibold px-4 py-3 w-48">Pelaku</th>
                        <th class="text-center font-semibold px-4 py-3 w-28">Aksi</th>
                        <th class="text-left font-semibold px-4 py-3 w-40">Target</th>
                        <th class="text-left font-semibold px-4 py-3">IP & Client</th>
                        <th class="text-center font-semibold px-4 py-3 w-24">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                    <i class="fa-solid fa-file-shield text-xl"></i>
                                </div>
                                <p class="font-medium text-slate-600">Tidak ada catatan log yang sesuai.</p>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter pencarian atau tanggal.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $row): 
                            $aksi = $row['aksi'] ?? '-';
                            $waktu = $row['waktu'] ?? null;
                            $tabel = $row['tabelNama'] ?? $row['tabel_nama'] ?? '';
                            $recordId = $row['recordId'] ?? $row['record_id'] ?? '';
                            $ip = $row['ipAddress'] ?? $row['ip_address'] ?? '::1';
                            $userAgent = $row['userAgent'] ?? $row['user_agent'] ?? '-';
                            
                            $pelakuUser = $row['pengguna'] ?? [];
                            $pelakuNama = $pelakuUser['namaLengkap'] ?? $row['nama_lengkap'] ?? null;
                            $pelakuUname = $pelakuUser['username'] ?? $row['username'] ?? null;

                            $dataLama = $row['dataLama'] ?? $row['data_lama'] ?? null;
                            $dataBaru = $row['dataBaru'] ?? $row['data_baru'] ?? null;
                            $hasDetail = !empty($dataLama) || !empty($dataBaru);
                        ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                
                                <!-- Waktu -->
                                <td class="px-4 py-3 align-top whitespace-nowrap">
                                    <span class="block font-semibold text-slate-800">
                                        <?= $waktu ? date('d M Y', strtotime($waktu)) : '-' ?>
                                    </span>
                                    <span class="text-xs text-slate-500 font-mono">
                                        <?= $waktu ? date('H:i:s', strtotime($waktu)) : '-' ?>
                                    </span>
                                </td>

                                <!-- Pelaku -->
                                <td class="px-4 py-3 align-top">
                                    <?php if ($pelakuUname): ?>
                                        <div class="font-semibold text-slate-800">
                                            <?= htmlspecialchars($pelakuNama ?: $pelakuUname) ?>
                                        </div>
                                        <div class="text-xs font-mono text-slate-500">
                                            @<?= htmlspecialchars($pelakuUname) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic">Sistem / Guest</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi -->
                                <td class="px-4 py-3 align-top text-center">
                                    <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-bold border <?= badge_aksi_class($aksi) ?>">
                                        <?= htmlspecialchars($aksi) ?>
                                    </span>
                                </td>

                                <!-- Target -->
                                <td class="px-4 py-3 align-top">
                                    <?php if ($tabel): ?>
                                        <div class="text-xs font-semibold uppercase text-slate-700">
                                            <?= htmlspecialchars($tabel) ?>
                                        </div>
                                        <?php if ($recordId !== null && $recordId !== ''): ?>
                                            <div class="text-xs font-mono text-slate-500">
                                                ID: <?= htmlspecialchars($recordId) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- IP & Client -->
                                <td class="px-4 py-3 align-top">
                                    <div class="text-xs font-mono text-slate-600">
                                        <?= htmlspecialchars($ip) ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400 truncate max-w-[220px] mt-0.5" title="<?= htmlspecialchars($userAgent) ?>">
                                        <?= htmlspecialchars($userAgent) ?>
                                    </div>
                                </td>

                                <!-- Detail Action -->
                                <td class="px-4 py-3 align-top text-center">
                                    <?php if ($hasDetail): ?>
                                        <button type="button"
                                                @click='openDetail(<?= json_encode($dataLama) ?>, <?= json_encode($dataBaru) ?>, "<?= htmlspecialchars($tabel, ENT_QUOTES) ?>")'
                                                class="w-7 h-7 rounded bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-700 transition-colors inline-flex items-center justify-center shadow-2xs"
                                                title="Lihat detail perubahan">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-slate-300">-</span>
                                    <?php endif; ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <?php if ($totalPages > 1): ?>
            <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-50/30">
                <p class="text-xs text-slate-500">
                    Halaman <span class="font-semibold text-slate-700"><?= $page ?></span> dari
                    <span class="font-semibold text-slate-700"><?= $totalPages ?></span>
                    (<?= number_format($totalRows) ?> log)
                </p>
                <nav class="flex items-center gap-1" aria-label="Navigasi halaman">
                    <?php
                    $qs = $_GET;
                    unset($qs['page']);
                    $baseQs = http_build_query($qs);
                    $sep = $baseQs !== '' ? '&' : '';
                    ?>

                    <?php if ($page > 1): ?>
                        <a href="<?= url('/audit-log?' . $baseQs . $sep . 'page=' . ($page - 1)) ?>"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50">
                            <i class="fa-solid fa-chevron-left"></i> Prev
                        </a>
                    <?php endif; ?>

                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <a href="<?= url('/audit-log?' . $baseQs . $sep . 'page=' . $i) ?>"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold <?= $i === $page ? 'bg-blue-600 text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= url('/audit-log?' . $baseQs . $sep . 'page=' . ($page + 1)) ?>"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50">
                            Next <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>

    </div>

    <!-- Modal Detail Perubahan Data (Screenshot 2) -->
    <div x-cloak x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        
        <!-- Backdrop blur -->
        <div x-show="modalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
             @click="modalOpen = false"></div>

        <!-- Modal Dialog -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden z-10">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                    <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass-chart text-blue-600"></i>
                        <span>Detail Perubahan Data</span>
                        <span class="text-xs bg-slate-200 text-slate-700 px-2.5 py-0.5 rounded font-mono font-bold uppercase ml-1" x-text="selectedTable"></span>
                    </h3>
                    <button type="button" @click="modalOpen = false" class="w-8 h-8 rounded-lg text-slate-400 hover:bg-slate-200 hover:text-slate-700 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 flex-1 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Data Lama (Sebelumnya) -->
                        <div>
                            <h4 class="text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                                Data Lama (Sebelumnya)
                            </h4>
                            <pre class="bg-slate-900 text-slate-300 p-4 rounded-xl text-xs font-mono overflow-x-auto whitespace-pre-wrap min-h-[170px] max-h-[420px] shadow-inner" x-text="formatJson(selectedOld)"></pre>
                        </div>

                        <!-- Data Baru (Setelahnya) -->
                        <div>
                            <h4 class="text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                Data Baru (Setelahnya)
                            </h4>
                            <pre class="bg-slate-900 text-emerald-400 p-4 rounded-xl text-xs font-mono overflow-x-auto whitespace-pre-wrap min-h-[170px] max-h-[420px] shadow-inner" x-text="formatJson(selectedNew)"></pre>
                        </div>

                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                    <button type="button" @click="modalOpen = false"
                            class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-bold rounded-xl transition-colors">
                        Tutup
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
