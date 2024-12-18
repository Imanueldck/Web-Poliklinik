<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pasien') {
    header('Location: login.php');
    exit();
}

include('../includes/db.php');

// Tangani form pendaftaran poli
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['daftar_poli'])) {
    $id_pasien = $_SESSION['user_id']; // Ambil ID pasien dari sesi
    $id_poli = intval($_POST['id_poli']);
    $tanggal_kunjungan = mysqli_real_escape_string($conn, $_POST['tanggal_kunjungan']);

    $query = "INSERT INTO Daftar_Poli (id_pasien, id_jadwal, keluhan, no_antrian) 
              VALUES ($id_pasien, $id_poli, 'Belum ada keluhan', 1)";

    if (mysqli_query($conn, $query)) {
        $success_message = "Pendaftaran poli berhasil!";
    } else {
        $error_message = "Terjadi kesalahan saat mendaftar.";
    }
}

// Ambil daftar poli dari database
$poli_result = mysqli_query($conn, "SELECT * FROM Poli");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Poli</title>
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

            <!-- Konten Utama -->
            <div class="col-md-9 col-lg-10 content p-4" id="content">
                <h2>Selamat Datang, Pasien <?= $_SESSION['username'] ?></h2>
                <hr>

                <!-- Tampilkan pesan jika sukses atau error -->
                <?php if (isset($success_message)) { ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                <?php } ?>
                <?php if (isset($error_message)) { ?>
                    <div class="alert alert-danger"><?= $error_message ?></div>
                <?php } ?>
            </div>
        </div>
    </div>

    

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
