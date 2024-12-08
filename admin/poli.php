<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Koneksi database
include('../includes/db.php');

// Tangani aksi Create
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_or_update_poli'])) {
    $id_poli = isset($_POST['id_poli']) ? intval($_POST['id_poli']) : 0;
    $nama_poli = mysqli_real_escape_string($conn, $_POST['nama_poli']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    if ($id_poli) {
        // Update jika ID ada
        $query = "UPDATE Poli SET nama_poli='$nama_poli', keterangan='$keterangan' WHERE id_poli=$id_poli";
    } else {
        // Insert jika ini adalah data baru
        $query = "INSERT INTO Poli (nama_poli, keterangan) VALUES ('$nama_poli', '$keterangan')";
    }
    mysqli_query($conn, $query);
    header('Location: poli.php');
    exit();
}

// Tangani aksi Delete
if (isset($_GET['delete_id'])) {
    $id_poli = intval($_GET['delete_id']);
    $query = "DELETE FROM Poli WHERE id_poli=$id_poli";
    mysqli_query($conn, $query);
    header('Location: poli.php');
    exit();
}

// Ambil semua data poli untuk operasi Read
$poli_result = mysqli_query($conn, "SELECT * FROM Poli");

// Jika ingin mengisi formulir dengan data saat tombol edit ditekan
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $id_edit = intval($_GET['edit_id']);
    $edit_query = mysqli_query($conn, "SELECT * FROM Poli WHERE id_poli=$id_edit");
    $edit_data = mysqli_fetch_assoc($edit_query);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Poli</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h2>Kelola Poli</h2>
        <hr>

        <!-- Formulir Edit/Tambah Data Poli -->
        <div class="mb-4">
            <h4><?= $edit_data ? "Edit Poli" : "Tambah Poli" ?></h4>
            <form method="POST">
                <input type="hidden" name="id_poli" value="<?= $edit_data ? $edit_data['id_poli'] : '' ?>">
                <div class="mb-3">
                    <label>Nama Poli</label>
                    <input type="text" name="nama_poli" class="form-control" required value="<?= $edit_data ? $edit_data['nama_poli'] : '' ?>">
                </div>
                <div class="mb-3">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan" class="form-control" required value="<?= $edit_data ? $edit_data['keterangan'] : '' ?>">
                </div>
                <button type="submit" name="create_or_update_poli" class="btn btn-success"><?= $edit_data ? "Perbarui Poli" : "Tambah Poli" ?></button>
            </form>
        </div>

        <hr>

        <!-- Tabel Daftar Poli -->
        <h4>Daftar Poli</h4>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Poli</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($poli = mysqli_fetch_assoc($poli_result)) { ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $poli['nama_poli'] ?></td>
                        <td><?= $poli['keterangan'] ?></td>
                        <td>
                            <a href="poli.php?edit_id=<?= $poli['id_poli'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="poli.php?delete_id=<?= $poli['id_poli'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus poli ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Script -->
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
