<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dokter') {
    header('Location: ../login.php');
    exit();
}

include('../includes/db.php');

// Ambil ID dokter dari sesi
$id_dokter = $_SESSION['user_id'];

// Ambil data pendaftaran pasien yang terkait dengan dokter yang login
$query = "
    SELECT p.id_pasien, p.nama_pasien, p.alamat, p.no_ktp, p.no_hp, p.no_rm, dp.id_daftar
    FROM Daftar_Poli dp
    JOIN Pasien p ON dp.id_pasien = p.id_pasien
    ORDER BY dp.id_daftar DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pasien</title>
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

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content p-4" id="content">
                <h2>Riwayat Pasien</h2>
                <hr>
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pasien</th>
                                <th>Alamat</th>
                                <th>No KTP</th>
                                <th>No HP</th>
                                <th>No RM</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nama_pasien'] ?></td>
                                    <td><?= $row['alamat'] ?></td>
                                    <td><?= $row['no_ktp'] ?></td>
                                    <td><?= $row['no_hp'] ?></td>
                                    <td><?= $row['no_rm'] ?></td>
                                    <td>
                                         <!-- Tombol untuk membuka modal -->
                                         <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?= $row['id_daftar'] ?>">Detail Riwayat</button>
                                    </td>
                                </tr>

                                <!-- Modal Detail Riwayat -->
                                <div class="modal fade" id="detailModal<?= $row['id_daftar'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $row['id_daftar'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel<?= $row['id_daftar'] ?>">Detail Riwayat Periksa</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                // Query untuk detail riwayat
                                                $detail_query = "
                                                    SELECT p.nama_pasien, d.nama_dokter, dp.keluhan, jp.hari, jp.jam_mulai, jp.jam_selesai, per.tgl_periksa, per.catatan, per.biaya_periksa
                                                    FROM Daftar_Poli dp
                                                    JOIN Pasien p ON dp.id_pasien = p.id_pasien
                                                    JOIN Jadwal_Periksa jp ON dp.id_jadwal = jp.id_jadwal
                                                    JOIN Dokter d ON jp.id_dokter = d.id_dokter
                                                    LEFT JOIN Periksa per ON dp.id_daftar = per.id_daftar
                                                    WHERE dp.id_daftar = {$row['id_daftar']}
                                                ";
                                                $detail_result = mysqli_query($conn, $detail_query);
                                                $detail = mysqli_fetch_assoc($detail_result);
                                                ?>
                                                <p><strong>Nama Pasien:</strong> <?= $detail['nama_pasien'] ?></p>
                                                <p><strong>Dokter:</strong> <?= $detail['nama_dokter'] ?></p>
                                                <p><strong>Keluhan:</strong> <?= $detail['keluhan'] ?></p>
                                                <p><strong>Hari:</strong> <?= $detail['hari'] ?></p>
                                                <p><strong>Jam:</strong> <?= $detail['jam_mulai'] ?> - <?= $detail['jam_selesai'] ?></p>
                                                <p><strong>Tanggal Periksa:</strong> <?= $detail['tgl_periksa'] ?: '-' ?></p>
                                                <p><strong>Catatan:</strong> <?= $detail['catatan'] ?: '-' ?></p>
                                                <p><strong>Biaya Periksa:</strong> <?= $detail['biaya_periksa'] ? 'Rp ' . number_format($detail['biaya_periksa'], 0, ',', '.') : '-' ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="alert alert-info">Belum ada pasien yang mendaftar poli.</div>
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
