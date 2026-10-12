<?php
$userActive = $_SESSION['user'] ?? [];
$userName = $userActive['namaLengkap'] ?? $userActive['nama_lengkap'] ?? $userActive['username'] ?? 'Administrator';
$userRole = is_array($userActive['role'] ?? null) ? ($userActive['role']['namaRole'] ?? 'Administrator') : ($userActive['role'] ?? 'Administrator');
$userRoleCode = is_array($userActive['role'] ?? null) ? strtoupper($userActive['role']['kodeRole'] ?? '') : strtoupper((string)$userRole);

$signTitle = 'Mengetahui,';
if (in_array($userRoleCode, ['KAPRODI', 'ADMIN_PRODI'])) {
    $signTitle = 'Mengetahui Ketua Program Studi,';
} elseif (in_array($userRoleCode, ['DEKANAT', 'ADMIN_DEKANAT'])) {
    $signTitle = 'Mengetahui Dekan,';
} elseif ($userRoleCode === 'REKTORAT') {
    $signTitle = 'Mengetahui Rektor,';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekapitulasi Data Mahasiswa — Universitas Muhammadiyah Bengkulu</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 12px; color: #000; margin: 0; padding: 25px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0 0 5px 0; font-size: 18px; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 12px; }
        .meta { margin-bottom: 15px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; font-size: 11px; }
        .text-center { text-align: center; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print" style="margin-bottom: 20px; display: flex; gap: 10px;">
    <button onclick="window.print()" style="padding: 8px 18px; font-size: 13px; font-weight: bold; background: #2563eb; color: #fff; border: none; cursor: pointer; border-radius: 4px;">
        Cetak Dokumen
    </button>
    <button onclick="window.close()" style="padding: 8px 18px; font-size: 13px; background: #64748b; color: #fff; border: none; cursor: pointer; border-radius: 4px;">
        Tutup
    </button>
</div>

<div class="header">
    <h1>SIM Mahasiswa</h1>
    <p><strong>Universitas Muhammadiyah Bengkulu</strong></p>
    <p>Laporan Rekapitulasi Data Akademik Mahasiswa</p>
</div>

<div class="meta">
    <table style="border:none; width: auto; margin-bottom: 15px;">
        <tr>
            <td style="border:none; padding: 2px 8px; width: 120px;"><strong>Tanggal Cetak</strong></td>
            <td style="border:none; padding: 2px 4px; width: 10px;">:</td>
            <td style="border:none; padding: 2px 8px;"><?= date('d F Y H:i:s') ?></td>
        </tr>
        <tr>
            <td style="border:none; padding: 2px 8px;"><strong>Dicetak Oleh</strong></td>
            <td style="border:none; padding: 2px 4px;">:</td>
            <td style="border:none; padding: 2px 8px;"><?= htmlspecialchars($userName) ?> (<?= htmlspecialchars($userRole) ?>)</td>
        </tr>
    </table>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th style="width: 90px;">NPM</th>
            <th>Nama Lengkap</th>
            <th style="width: 35px;">L/P</th>
            <th>Program Studi</th>
            <th style="width: 80px;">Tahun Masuk</th>
            <th style="width: 75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($mahasiswa)): ?>
            <tr><td colspan="7" class="text-center" style="padding: 20px;">Tidak ada data mahasiswa ditemukan.</td></tr>
        <?php else: ?>
            <?php $no = 1; foreach ($mahasiswa as $m): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center"><?= htmlspecialchars($m['npm'] ?? '') ?></td>
                    <td><strong><?= htmlspecialchars($m['namaMahasiswa'] ?? $m['nama_mahasiswa'] ?? '') ?></strong></td>
                    <td class="text-center"><?= htmlspecialchars($m['jenisKelamin'] ?? $m['jenis_kelamin'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($m['namaProgramStudi'] ?? $m['nama_program_studi'] ?? '-') ?></td>
                    <td class="text-center">
                        <?= !empty($m['tanggalMasuk']) ? date('Y', strtotime($m['tanggalMasuk'])) : (!empty($m['tanggal_masuk']) ? date('Y', strtotime($m['tanggal_masuk'])) : '-') ?>
                    </td>
                    <td class="text-center"><?= htmlspecialchars($m['statusMahasiswa'] ?? $m['status_mahasiswa'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div style="margin-top: 40px; text-align: right; font-size: 12px; width: 250px; float: right;">
    <p style="margin-bottom: 70px;">Bengkulu, <?= date('d F Y') ?><br><strong><?= $signTitle ?></strong></p>
    <p><strong>( <?= htmlspecialchars($userName) ?> )</strong></p>
</div>

</body>
</html>
