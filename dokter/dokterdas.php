<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dokter') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dokter</title>
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
                <a href="#" class="text-white mb-2"><i class="fas fa-home"></i> Dashboard</a>
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
                <h2>Selamat Datang, Dokter <?= $_SESSION['username'] ?></h2>
                <hr>
                <p>Selamat datang di panel dokter. Gunakan menu di samping untuk mengelola jadwal praktek dan daftar pasien.</p>
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
