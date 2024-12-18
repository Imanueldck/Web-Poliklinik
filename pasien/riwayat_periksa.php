<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pasien') {
    header('Location: ../login.php');
    exit();
}

include('../includes/db.php');

// Ambil ID pasien dari sesi
$id_pasien = $_SESSION['user_id'];

// Ambil data riwayat pemeriksaan pasien dari database
$query = "
    SELECT dp.no_antrian, jp.hari, jp.jam_mulai, jp.jam_selesai, d.nama_dokter, dp.keluhan, p.tgl_periksa, p.catatan, p.biaya_periksa 
    FROM Daftar_Poli dp
    JOIN Jadwal_Periksa jp ON dp.id_jadwal = jp.id_jadwal
    JOIN Dokter d ON jp.id_dokter = d.id_dokter
    LEFT JOIN Periksa p ON dp.id_daftar = p.id_daftar
    WHERE dp.id_pasien = $id_pasien
    ORDER BY dp.id_daftar DESC
";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Periksa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <button class="sidebar-toggle d-md-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="sidebar d-md-block" id="sidebar">
                <h3 class="text-center mb-3">Pasien Panel</h3>
                <a href="dashboard-pasien.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="pendaftaran-poli.php"><i class="fas fa-clinic-medical"></i> Pendaftaran Poli</a>
                <a href="riwayat_periksa.php" class="active"><i class="fas fa-history"></i> Riwayat Periksa</a>
                <div class="logout mt-auto">
                    <a href="../logout.php" class="btn btn-danger text-center"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="content" id="content">
                <h2>Riwayat Periksa</h2>
                <hr>
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No Antrian</th>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Dokter</th>
                                <th>Keluhan</th>
                                <th>Tanggal Periksa</th>
                                <th>Catatan</th>
                                <th>Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?= $row['no_antrian'] ?></td>
                                    <td><?= $row['hari'] ?></td>
                                    <td><?= $row['jam_mulai'] ?> - <?= $row['jam_selesai'] ?></td>
                                    <td><?= $row['nama_dokter'] ?></td>
                                    <td><?= $row['keluhan'] ?></td>
                                    <td><?= $row['tgl_periksa'] ? date('d-m-Y', strtotime($row['tgl_periksa'])) : '-' ?></td>
                                    <td><?= $row['catatan'] ?: '-' ?></td>
                                    <td><?= $row['biaya_periksa'] ? 'Rp ' . number_format($row['biaya_periksa'], 0, ',', '.') : '-' ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="alert alert-info">Belum ada riwayat periksa.</div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            sidebar.classList.toggle('show');
            content.classList.toggle('toggled');
        }
    </script>
</body>

</html>
