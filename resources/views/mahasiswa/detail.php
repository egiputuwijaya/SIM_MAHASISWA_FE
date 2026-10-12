<?php ob_start(); 

$idMhs = (int)($mhs['id'] ?? $mhs['id_mahasiswa'] ?? 0);
$npm = $mhs['npm'] ?? '-';
$nama = $mhs['namaMahasiswa'] ?? $mhs['nama_mahasiswa'] ?? '-';
$status = $mhs['statusMahasiswa'] ?? $mhs['status_mahasiswa'] ?? 'Aktif';
$jk = $mhs['jenisKelamin'] ?? $mhs['jenis_kelamin'] ?? '-';
$jkText = ($jk === 'L') ? 'Laki-laki' : (($jk === 'P') ? 'Perempuan' : $jk);
$tempatLahir = $mhs['tempatLahir'] ?? $mhs['tempat_lahir'] ?? '—';
$tglLahir = $mhs['tanggalLahir'] ?? $mhs['tanggal_lahir'] ?? null;
$tglMasuk = $mhs['tanggalMasuk'] ?? $mhs['tanggal_masuk'] ?? null;
$alamat = $mhs['alamat'] ?? '—';

$prodi = $mhs['programStudi'] ?? [];
$namaProdi = $prodi['namaProgramStudi'] ?? $mhs['nama_program_studi'] ?? '—';
$kodeProdi = $prodi['kodeProgramStudi'] ?? $mhs['kode_program_studi'] ?? '—';
$jenjang = $prodi['jenjang'] ?? $mhs['jenjang'] ?? 'S1';

$fakultas = $prodi['fakultas'] ?? [];
$namaFakultas = $fakultas['namaFakultas'] ?? $mhs['nama_fakultas'] ?? '—';
$universitas = $fakultas['universitas'] ?? [];
$namaUniversitas = $universitas['namaUniversitas'] ?? 'Universitas Muhammadiyah Bengkulu';

$akun = $mhs['pengguna'] ?? null;

function format_tanggal_indo(?string $tgl): string {
    if (!$tgl || $tgl === '' || $tgl === '0000-00-00') {
        return '—';
    }
    $ts = strtotime($tgl);
    return $ts ? date('d F Y', $ts) : '—';
}

function status_badge_pill(string $status): string {
    return match ($status) {
        'Aktif'             => 'bg-green-100 text-green-800 border-green-200',
        'Cuti'              => 'bg-amber-100 text-amber-800 border-amber-200',
        'Lulus'             => 'bg-blue-100 text-blue-800 border-blue-200',
        'Mengundurkan Diri' => 'bg-orange-100 text-orange-800 border-orange-200',
        'Drop Out'          => 'bg-red-100 text-red-800 border-red-200',
        'Tidak Aktif'       => 'bg-slate-100 text-slate-700 border-slate-200',
        default             => 'bg-slate-100 text-slate-700 border-slate-200',
    };
}

$initials = '';
$parts = preg_split('/\s+/', trim($nama));
foreach (array_slice($parts, 0, 2) as $p) {
    if (!empty($p)) $initials .= mb_strtoupper(mb_substr($p, 0, 1));
}
if ($initials === '') $initials = 'M';
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
        <li class="text-slate-900 font-semibold">Detail</li>
    </ol>
</nav>

<div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
    <div>
        <h2 class="font-bold text-xl text-slate-900">Profil Mahasiswa</h2>
        <p class="text-sm text-slate-500 mt-1">Informasi lengkap data akademik mahasiswa.</p>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="<?= url('/mahasiswa/edit/' . $idMhs) ?>"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold shadow-sm transition-colors">
            <i class="fa-solid fa-pen-to-square"></i>
            Edit
        </a>
        <a href="<?= url('/mahasiswa') ?>"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- ===== Kartu Profil Samping ===== -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Banner -->
            <div class="h-24 bg-gradient-to-r from-blue-700 via-indigo-600 to-blue-800 relative">
                <div class="absolute -bottom-10 left-1/2 -translate-x-1/2">
                    <div class="w-20 h-20 rounded-2xl bg-white shadow flex items-center justify-center border-4 border-white">
                        <span class="font-extrabold text-2xl text-blue-700">
                            <?= htmlspecialchars($initials) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="pt-14 pb-6 px-6 text-center">
                <h3 class="font-bold text-lg text-slate-900 leading-snug">
                    <?= htmlspecialchars($nama) ?>
                </h3>
                <p class="font-mono text-sm text-blue-700 font-semibold mt-1">
                    <?= htmlspecialchars($npm) ?>
                </p>
                <div class="mt-3 flex items-center justify-center gap-2 flex-wrap">
                    <span class="inline-flex px-3 py-1 rounded-full border text-xs font-bold <?= status_badge_pill($status) ?>">
                        <?= htmlspecialchars($status) ?>
                    </span>
                    <span class="inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                        <?= htmlspecialchars($jenjang) ?>
                    </span>
                </div>
            </div>

            <!-- Ringkas prodi -->
            <div class="px-6 pb-6">
                <div class="p-4 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100">
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1">Program Studi</p>
                    <p class="font-bold text-slate-900 text-sm">
                        <?= htmlspecialchars($namaProdi) ?>
                    </p>
                    <p class="text-xs text-blue-700 font-semibold mt-0.5">
                        <?= htmlspecialchars($kodeProdi) ?> · <?= htmlspecialchars($jenjang) ?>
                    </p>
                    <p class="text-xs text-slate-500 mt-2">
                        <?= htmlspecialchars($namaFakultas) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Detail Data ===== -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Data Diri -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h4 class="font-bold text-slate-900 flex items-center gap-2 mb-5">
                <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                    <i class="fa-solid fa-user text-sm"></i>
                </span>
                Data Diri
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                <div class="pb-3 border-b border-slate-100">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">NPM</p>
                    <p class="text-sm font-semibold text-slate-900 font-mono mt-0.5"><?= htmlspecialchars($npm) ?></p>
                </div>
                <div class="pb-3 border-b border-slate-100">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Nama Lengkap</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5"><?= htmlspecialchars($nama) ?></p>
                </div>
                <div class="pb-3 border-b border-slate-100">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Jenis Kelamin</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5"><?= htmlspecialchars($jkText) ?></p>
                </div>
                <div class="pb-3 border-b border-slate-100">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Status</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5"><?= htmlspecialchars($status) ?></p>
                </div>
                <div class="pb-3 border-b border-slate-100">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Tempat Lahir</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5"><?= htmlspecialchars($tempatLahir ?: '—') ?></p>
                </div>
                <div class="pb-3 border-b border-slate-100">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Tanggal Lahir</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5"><?= htmlspecialchars(format_tanggal_indo($tglLahir)) ?></p>
                </div>
                <div class="pb-3 border-b border-slate-100 sm:col-span-2">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Alamat</p>
                    <p class="text-sm text-slate-700 mt-0.5 leading-relaxed"><?= htmlspecialchars($alamat ?: '—') ?></p>
                </div>
            </div>
        </div>

        <!-- Data Akademik -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h4 class="font-bold text-slate-900 flex items-center gap-2 mb-5">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <i class="fa-solid fa-graduation-cap text-sm"></i>
                </span>
                Data Akademik
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                <div class="pb-3 border-b border-slate-100">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Tanggal Masuk</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5"><?= htmlspecialchars(format_tanggal_indo($tglMasuk)) ?></p>
                </div>
                <div class="pb-3 border-b border-slate-100">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Jenjang</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5"><?= htmlspecialchars($jenjang ?: '—') ?></p>
                </div>
                <div class="pb-3 border-b border-slate-100">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Kode Prodi</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5 font-mono"><?= htmlspecialchars($kodeProdi ?: '—') ?></p>
                </div>
                <div class="pb-3 border-b border-slate-100">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Program Studi</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5"><?= htmlspecialchars($namaProdi ?: '—') ?></p>
                </div>
                <div class="pb-3 border-b border-slate-100 sm:col-span-2">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Fakultas / Universitas</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">
                        <?= htmlspecialchars($namaFakultas ?: '—') ?>
                        <span class="text-slate-400 font-normal"> — </span>
                        <?= htmlspecialchars($namaUniversitas ?: '—') ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Akun Login Terkait -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h4 class="font-bold text-slate-900 flex items-center gap-2 mb-5">
                <span class="w-8 h-8 rounded-lg bg-slate-800 text-white flex items-center justify-center">
                    <i class="fa-solid fa-key text-sm"></i>
                </span>
                Akun Login Terkait
            </h4>

            <?php if ($akun): 
                $uName = $akun['username'] ?? '—';
                $uRole = $akun['role']['kodeRole'] ?? $akun['role']['namaRole'] ?? 'Mahasiswa';
                $uEmail = $akun['email'] ?? '—';
                $uStatus = $akun['statusAktif'] ?? 'Aktif';
                $uLogin = $akun['lastLogin'] ?? null;
            ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div class="pb-3 border-b border-slate-100">
                        <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Username</p>
                        <p class="text-sm font-semibold text-slate-900 font-mono mt-0.5"><?= htmlspecialchars($uName) ?></p>
                    </div>
                    <div class="pb-3 border-b border-slate-100">
                        <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Role</p>
                        <p class="text-sm font-semibold text-slate-900 mt-0.5"><?= htmlspecialchars($uRole) ?></p>
                    </div>
                    <div class="pb-3 border-b border-slate-100">
                        <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Email</p>
                        <p class="text-sm font-semibold text-slate-900 mt-0.5"><?= htmlspecialchars($uEmail ?: '—') ?></p>
                    </div>
                    <div class="pb-3 border-b border-slate-100">
                        <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Status Akun</p>
                        <p class="text-sm font-semibold mt-0.5">
                            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold <?= $uStatus === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= htmlspecialchars($uStatus) ?>
                            </span>
                        </p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Login Terakhir</p>
                        <p class="text-sm text-slate-700 mt-0.5"><?= $uLogin ? htmlspecialchars(date('d/m/Y H:i', strtotime($uLogin))) : 'Belum pernah login' ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <i class="fa-solid fa-user-slash text-slate-400 text-lg"></i>
                    <p class="text-sm text-slate-500">Belum ada akun login yang terhubung dengan mahasiswa ini.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
