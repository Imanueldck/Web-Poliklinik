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
    SELECT dp.no_antrian, po.nama_poli, jp.hari, jp.jam_mulai, jp.jam_selesai, d.nama_dokter, 
           dp.keluhan, p.tgl_periksa, p.catatan, p.biaya_periksa
    FROM Daftar_Poli dp
    JOIN Jadwal_Periksa jp ON dp.id_jadwal = jp.id_jadwal
    JOIN Dokter d ON jp.id_dokter = d.id_dokter
    JOIN Poli po ON d.id_poli = po.id_poli
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
                <h3 class="text-center mb-3">Pasien Panel</h3>
                <a href="pasiendas.php" class="text-white mb-2"><i class="fas fa-home"></i> Dashboard</a>
                <a href="pendaftaran-poli.php" class="text-white mb-2"><i class="fas fa-clinic-medical"></i> Pendaftaran Poli</a>
                <a href="riwayat_periksa.php" class="text-white mb-2"><i class="fas fa-history"></i> Riwayat Periksa</a>
                <div class="logout mt-auto">
                    <a href="../logout.php" class="text-white btn btn-danger mt-3 text-center"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content p-4" id="content">
                <h2>Riwayat Periksa</h2>
                <hr>
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status = $row['tgl_periksa'] ? 'Selesai' : 'Belum Diperiksa';
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nama_poli'] ?></td>
                                    <td><?= $row['nama_dokter'] ?></td>
                                    <td><?= $row['hari'] ?></td>
                                    <td><?= $row['jam_mulai'] ?> - <?= $row['jam_selesai'] ?></td>
                                    <td><?= $status ?></td>
                                    <td>
                                        <?php if ($row['tgl_periksa']) { ?>
                                            <!-- Tombol untuk detail modal -->
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?= $no ?>">Detail</button>

                                            <!-- Modal Detail -->
                                            <div class="modal fade" id="detailModal<?= $no ?>" tabindex="-1" aria-labelledby="modalLabel<?= $no ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalLabel<?= $no ?>">Detail Periksa</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Dokter:</strong> <?= $row['nama_dokter'] ?></p>
                                                            <p><strong>Hari:</strong> <?= $row['hari'] ?></p>
                                                            <p><strong>Jam:</strong> <?= $row['jam_mulai'] ?> - <?= $row['jam_selesai'] ?></p>
                                                            <p><strong>Keluhan:</strong> <?= $row['keluhan'] ?></p>
                                                            <p><strong>Tanggal Periksa:</strong> <?= $row['tgl_periksa'] ?></p>
                                                            <p><strong>Catatan:</strong> <?= $row['catatan'] ?></p>
                                                            <p><strong>Biaya:</strong> Rp <?= number_format($row['biaya_periksa'], 0, ',', '.') ?></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Modal -->
                                        <?php } else { ?>
                                            <span class="text-muted">Belum Diperiksa</span>
                                        <?php } ?>
                                    </td>
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
