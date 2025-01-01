<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dokter') {
    header('Location: ../login.php');
    exit();
}

include('../includes/db.php');

// Ambil ID dokter dari sesi
$id_dokter = $_SESSION['user_id'];

// Query untuk mengambil daftar pasien yang belum diperiksa
$query_belum = "
    SELECT 
        dp.id_daftar, 
        p.nama_pasien, 
        dp.keluhan, 
        dp.no_antrian, 
        jp.hari, 
        jp.jam_mulai, 
        jp.jam_selesai 
    FROM Daftar_Poli dp
    INNER JOIN Pasien p ON dp.id_pasien = p.id_pasien
    INNER JOIN Jadwal_Periksa jp ON dp.id_jadwal = jp.id_jadwal
    WHERE jp.id_dokter = ? AND dp.status = 'belum'
    ORDER BY dp.no_antrian ASC";

$stmt_belum = $conn->prepare($query_belum);
$stmt_belum->bind_param('i', $id_dokter);
$stmt_belum->execute();
$result_belum = $stmt_belum->get_result();

// Query untuk mengambil daftar pasien yang sudah diperiksa
$query_selesai = "
    SELECT 
        dp.id_daftar, 
        p.nama_pasien, 
        dp.keluhan, 
        dp.no_antrian, 
        jp.hari, 
        jp.jam_mulai, 
        jp.jam_selesai, 
        per.tgl_periksa, 
        per.catatan 
    FROM Daftar_Poli dp
    INNER JOIN Pasien p ON dp.id_pasien = p.id_pasien
    INNER JOIN Jadwal_Periksa jp ON dp.id_jadwal = jp.id_jadwal
    LEFT JOIN Periksa per ON dp.id_daftar = per.id_daftar
    WHERE jp.id_dokter = ? AND dp.status = 'selesai'
    ORDER BY dp.no_antrian ASC";

$stmt_selesai = $conn->prepare($query_selesai);
$stmt_selesai->bind_param('i', $id_dokter);
$stmt_selesai->execute();
$result_selesai = $stmt_selesai->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Periksa Pasien</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="../assets/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <button class="sidebar-toggle d-md-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="sidebar d-md-block" id="sidebar">
                <h3 class="text-center mb-3">Dokter Panel</h3>
                <a href="dokterdas.php" class="text-white mb-2"><i class="fas fa-home"></i> Dashboard</a>
                <a href="jadwal-periksa.php" class="text-white mb-2"><i class="fas fa-calendar-alt"></i> Jadwal Periksa</a>
                <a href="memeriksa.php" class="text-white mb-2"><i class="fas fa-calendar-alt"></i> Periksa Pasien</a>
                <a href="riwayat-pasien.php" class="text-white mb-2"><i class="fa-solid fa-briefcase-medical" style="color: #ffffff;"></i></i> Riwayat Pasien</a>
                <a href="editdok.php" class="text-white mb-2"><i class="fas fa-user-edit"></i> Profil</a>
                <div class="logout mt-auto">
                    <a href="../logout.php" class="text-white btn btn-danger mt-3 text-center"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <!-- Konten Utama -->
            <div class="col-md-9 col-lg-10 content p-4" id="content">
                <h2>Daftar Pasien</h2>
                
                <!-- Pasien Belum Diperiksa -->
                <h4>Belum Diperiksa</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No. Antrian</th>
                            <th>Nama Pasien</th>
                            <th>Keluhan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_belum->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['no_antrian'] ?></td>
                                <td><?= $row['nama_pasien'] ?></td>
                                <td><?= $row['keluhan'] ?></td>
                                <td>
                                    <a href="periksa_pasien.php?id_daftar=<?= $row['id_daftar'] ?>" class="btn btn-success">Periksa</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Pasien Sudah Diperiksa -->
                <h4>Sudah Diperiksa</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Pasien</th>
                            <th>Keluhan</th>
                            <th>Tanggal Periksa</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $no = 1; // Inisialisasi nomor urut
        while ($row = $result_selesai->fetch_assoc()) { ?>
            <tr>
                <td><?= $no++ ?></td>
                                <td><?= $row['nama_pasien'] ?></td>
                                <td><?= $row['keluhan'] ?></td>
                                <td><?= $row['tgl_periksa'] ? date('d-m-Y', strtotime($row['tgl_periksa'])) : '-' ?></td>
                                <td><?= $row['catatan'] ?: '-' ?></td>
                                <td>
                                    <a href="edit_periksa.php?id_daftar=<?= $row['id_daftar'] ?>" class="btn btn-primary">Edit</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
