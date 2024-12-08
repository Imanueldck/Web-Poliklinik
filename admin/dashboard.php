<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Koneksi ke database
include('../includes/db.php');

// Query untuk mendapatkan jumlah data
$jumlah_dokter = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM dokter"))['jumlah'];
$jumlah_pasien = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM pasien"))['jumlah'];
$jumlah_poli = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM poli"))['jumlah'];
$jumlah_obat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM obat"))['jumlah'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="../assets/style.css" rel="stylesheet">

</head>
<body>
    <!-- Sidebar -->
    <button class="sidebar-toggle d-md-none" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="sidebar d-md-block" id="sidebar">
        <h3 class="text-center mb-3">Admin Panel</h3>
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard Admin</a>
        <a href="dokter.php"><i class="fas fa-user-md"></i> Kelola Dokter</a>
        <a href="poli.php"><i class="fas fa-clinic-medical"></i> Kelola Poli</a>
        <a href="pasien.php"><i class="fas fa-users"></i> Kelola Pasien</a>
        <a href="obat.php"><i class="fas fa-pills"></i> Kelola Obat</a>
        <div class="logout mt-auto">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content" id="content">
        <h1 class="mt-3">Selamat Datang di Dashboard Admin</h1><hr>
        <div class="row">
            <!-- Card untuk Dokter -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-user-md"></i> Jumlah Dokter</h5>
                        <p class="card-text"><?php echo $jumlah_dokter; ?> Dokter terdaftar.</p>
                    </div>
                </div>
            </div>
            <!-- Card untuk Pasien -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users"></i> Jumlah Pasien</h5>
                        <p class="card-text"><?php echo $jumlah_pasien; ?> Pasien terdaftar.</p>
                    </div>
                </div>
            </div>
            <!-- Card untuk Poli -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-clinic-medical"></i> Jumlah Poli</h5>
                        <p class="card-text"><?php echo $jumlah_poli; ?> Poli tersedia.</p>
                    </div>
                </div>
            </div>
            <!-- Card untuk Obat -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-pills"></i> Jumlah Obat</h5>
                        <p class="card-text"><?php echo $jumlah_obat; ?> Obat tersedia.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
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
