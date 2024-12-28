<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dokter') {
    header('Location: ../login.php');
    exit();
}

include('../includes/db.php');

// Ambil ID dokter dari sesi
$id_dokter = $_SESSION['user_id'];

// Periksa apakah ID dokter tersedia
if (empty($id_dokter)) {
    $_SESSION['error_message'] = "Sesi telah habis. Silakan login kembali.";
    header('Location: ../login.php');
    exit();
}

// Ambil data dokter dari database
$query = "SELECT * FROM Dokter WHERE id_dokter = $id_dokter";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Data dokter tidak ditemukan.");
}

$dokter = mysqli_fetch_assoc($result);

// Tangani form edit profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_dokter = mysqli_real_escape_string($conn, $_POST['nama_dokter']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);

    // Update data dokter
    $update_query = "UPDATE Dokter 
                     SET nama_dokter = '$nama_dokter', 
                         alamat = '$alamat', 
                         no_hp = '$no_hp' 
                     WHERE id_dokter = $id_dokter";

    if (mysqli_query($conn, $update_query)) {
        $success_message = "Profil berhasil diperbarui!";
        // Refresh data dokter
        $result = mysqli_query($conn, $query);
        $dokter = mysqli_fetch_assoc($result);
    } else {
        $error_message = "Terjadi kesalahan saat memperbarui profil: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Dokter</title>
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
            <div class="col-md-9 col-lg-10 content p-4">
                <h2>Edit Profil Dokter</h2>
                <hr>

                <!-- Tampilkan pesan sukses atau error -->
                <?php if (isset($success_message)) { ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                <?php } ?>
                <?php if (isset($error_message)) { ?>
                    <div class="alert alert-danger"><?= $error_message ?></div>
                <?php } ?>

                <!-- Form Edit Profil -->
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nama_dokter" class="form-label">Nama Dokter</label>
                                <input type="text" name="nama_dokter" id="nama_dokter" class="form-control"
                                    value="<?= htmlspecialchars($dokter['nama_dokter']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea name="alamat" id="alamat" class="form-control" rows="3" required><?= htmlspecialchars($dokter['alamat']) ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">Nomor HP</label>
                                <input type="text" name="no_hp" id="no_hp" class="form-control"
                                    value="<?= htmlspecialchars($dokter['no_hp']) ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
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
