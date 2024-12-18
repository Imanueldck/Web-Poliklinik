<?php
session_start();

// Koneksi database
include('includes/db.php');

// Cek jika form login dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    // Validasi Admin
    $query_admin = "SELECT * FROM Admin WHERE username = '$nama' AND role = 'admin'";
    $result_admin = mysqli_query($conn, $query_admin);

    if (mysqli_num_rows($result_admin) > 0) {
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = $nama;
        header('Location: ./admin/dashboard.php');
        exit();
    }

    // Validasi Dokter
    $query_dokter = "SELECT * FROM Dokter WHERE nama_dokter = '$nama' AND alamat = '$alamat'";
    $result_dokter = mysqli_query($conn, $query_dokter);

    if (mysqli_num_rows($result_dokter) > 0) {
        $dokter_data = mysqli_fetch_assoc($result_dokter); // Ambil hasil query sebagai array
        $_SESSION['role'] = 'dokter';
        $_SESSION['username'] = $dokter_data['nama_dokter']; // Gunakan nama dokter dari database
        $_SESSION['user_id'] = $dokter_data['id_dokter']; // Set ID dokter ke sesi
        header('Location: ./dokter/dokterdas.php');
        exit();
    }
    


    // Validasi Pasien
    $query_pasien = "SELECT * FROM Pasien WHERE nama_pasien = '$nama' AND alamat = '$alamat'";
    $result_pasien = mysqli_query($conn, $query_pasien);

    if (mysqli_num_rows($result_pasien) > 0) {
        $row_pasien = mysqli_fetch_assoc($result_pasien);
        $_SESSION['role'] = 'pasien';
        $_SESSION['username'] = $row_pasien['nama_pasien'];
        $_SESSION['user_id'] = $row_pasien['id_pasien'];
        $_SESSION['no_rm'] = $row_pasien['no_rm'];
        header('Location: ./pasien/pasiendas.php');
        exit();
    } else {
        $error = "Nama atau alamat tidak cocok!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f8;
            font-family: 'Arial', sans-serif;
        }
        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 100px auto;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-container .form-control {
            border-radius: 10px;
        }
        .login-container .btn {
            border-radius: 10px;
        }
        .login-container .alert {
            margin-top: 10px;
        }
        .login-container .icon {
            font-size: 3rem;
            text-align: center;
            margin-bottom: 20px;
            color: #007BFF;
        }
        footer {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="icon">
        <i class="fas fa-user-circle"></i>
    </div>
    <h2>Login</h2>
    <?php if (isset($error)) { ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php } ?>
    <form method="POST">
        <div class="mb-3">
            <label for="nama" class="form-label">Username</label>
            <input type="text" name="nama" class="form-control" id="nama" placeholder="Enter username" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Password</label>
            <input type="password" name="alamat" class="form-control" id="alamat" placeholder="Enter password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Poliklinik Management System. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
