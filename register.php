<?php
session_start();
include('includes/db.php');

// Tangani Pendaftaran Pasien Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pasien = mysqli_real_escape_string($conn, $_POST['nama_pasien']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_ktp = mysqli_real_escape_string($conn, $_POST['no_ktp']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);

    // Buat nomor rekam medis
    $current_month = date('Ym'); // Format YYYYMM
    $query = "SELECT COUNT(*) + 1 AS next_number FROM Pasien WHERE no_rm LIKE '$current_month%'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $next_number = str_pad($row['next_number'], 4, '0', STR_PAD_LEFT); // Tambahkan padding 0
    $no_rm = "$current_month-$next_number";

    // Simpan data pasien ke database
    $query = "INSERT INTO Pasien (nama_pasien, alamat, no_ktp, no_hp, no_rm) 
              VALUES ('$nama_pasien', '$alamat', '$no_ktp', '$no_hp', '$no_rm')";
    if (mysqli_query($conn, $query)) {
        $success_message = "Pendaftaran berhasil! Nomor Rekam Medis Anda: $no_rm";
    } else {
        $error_message = "Terjadi kesalahan: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pasien Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .already-registered {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Pendaftaran Pasien Baru</h2>

        <!-- Tampilkan Pesan Sukses atau Error -->
        <?php if (isset($success_message)) { ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php } ?>
        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php } ?>

        <!-- Form Pendaftaran Pasien -->
        <form method="POST">
            <div class="mb-3">
                <label for="nama_pasien" class="form-label">Nama Pasien</label>
                <input type="text" name="nama_pasien" id="nama_pasien" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea name="alamat" id="alamat" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="no_ktp" class="form-label">Nomor KTP</label>
                <input type="text" name="no_ktp" id="no_ktp" class="form-control" maxlength="16" required>
            </div>
            <div class="mb-3">
                <label for="no_hp" class="form-label">Nomor HP</label>
                <input type="text" name="no_hp" id="no_hp" class="form-control" maxlength="15" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>

        <!-- Link untuk pengguna yang sudah memiliki akun -->
        <div class="already-registered">
            <p>Sudah memiliki akun? <a href="login.php">Masuk di sini</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
