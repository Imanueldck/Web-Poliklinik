<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dokter') {
    header('Location: ../login.php');
    exit();
}

include('../includes/db.php');

// Ambil ID daftar dari query parameter
if (!isset($_GET['id_daftar'])) {
    echo "ID Daftar tidak ditemukan!";
    exit();
}

$id_daftar = intval($_GET['id_daftar']);

// Query untuk detail periksa
$detail_query = "
    SELECT p.nama_pasien, d.nama_dokter, dp.keluhan, jp.hari, jp.jam_mulai, jp.jam_selesai, per.tgl_periksa, per.catatan, per.biaya_periksa
    FROM Daftar_Poli dp
    JOIN Pasien p ON dp.id_pasien = p.id_pasien
    JOIN Jadwal_Periksa jp ON dp.id_jadwal = jp.id_jadwal
    JOIN Dokter d ON jp.id_dokter = d.id_dokter
    LEFT JOIN Periksa per ON dp.id_daftar = per.id_daftar
    WHERE dp.id_daftar = $id_daftar
";

$detail_result = mysqli_query($conn, $detail_query);
$detail = mysqli_fetch_assoc($detail_result);

if (!$detail) {
    echo "Detail periksa tidak ditemukan!";
    exit();
}

// Query untuk daftar obat
$query_obat = "
    SELECT o.nama_obat 
    FROM Detail_Periksa dp
    JOIN Obat o ON dp.id_obat = o.id_obat
    WHERE dp.id_periksa = (SELECT id_periksa FROM Periksa WHERE id_daftar = $id_daftar)
";
$result_obat = mysqli_query($conn, $query_obat);
$obat_list = [];
while ($obat = mysqli_fetch_assoc($result_obat)) {
    $obat_list[] = $obat['nama_obat'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Periksa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Detail Periksa</h2>
        <hr>
        <div class="card">
            <div class="card-body">
                <p><strong>Nama Pasien:</strong> <?= $detail['nama_pasien'] ?></p>
                <p><strong>Dokter:</strong> <?= $detail['nama_dokter'] ?></p>
                <p><strong>Keluhan:</strong> <?= $detail['keluhan'] ?></p>
                <p><strong>Hari:</strong> <?= $detail['hari'] ?></p>
                <p><strong>Jam:</strong> <?= $detail['jam_mulai'] ?> - <?= $detail['jam_selesai'] ?></p>
                <p><strong>Tanggal Periksa:</strong> <?= $detail['tgl_periksa'] ?: '-' ?></p>
                <p><strong>Catatan:</strong> <?= $detail['catatan'] ?: '-' ?></p>
                <p><strong>Biaya Periksa:</strong> <?= $detail['biaya_periksa'] ? 'Rp ' . number_format($detail['biaya_periksa'], 0, ',', '.') : '-' ?></p>
                <p><strong>Obat:</strong></p>
                <ul>
                    <?php if (!empty($obat_list)) {
                        foreach ($obat_list as $obat) { ?>
                            <li><?= $obat ?></li>
                        <?php }
                    } else { ?>
                        <li>Tidak ada obat yang diresepkan.</li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <a href="riwayat-pasien.php" class="btn btn-secondary mt-3">Kembali</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
