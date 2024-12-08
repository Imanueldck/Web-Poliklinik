<?php
session_start();
include('../includes/db.php');

// Tangani aksi Create/Update Obat
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_or_update_obat'])) {
    $id_obat = isset($_POST['id_obat']) ? intval($_POST['id_obat']) : 0;
    $nama_obat = mysqli_real_escape_string($conn, $_POST['nama_obat']);
    $kemasan = mysqli_real_escape_string($conn, $_POST['kemasan']);
    $harga = intval($_POST['harga']);

    if ($id_obat) {
        // Operasi Update
        $query = "UPDATE Obat SET nama_obat='$nama_obat', kemasan='$kemasan', harga=$harga WHERE id_obat=$id_obat";
    } else {
        // Operasi Insert
        $query = "INSERT INTO Obat (nama_obat, kemasan, harga) VALUES ('$nama_obat', '$kemasan', $harga)";
    }

    mysqli_query($conn, $query);
    header('Location: obat.php');
    exit();
}

// Tangani aksi Delete
if (isset($_GET['delete_id'])) {
    $id_obat = intval($_GET['delete_id']);
    $query = "DELETE FROM Obat WHERE id_obat=$id_obat";
    mysqli_query($conn, $query);
    header('Location: obat.php');
    exit();
}

// Ambil semua data untuk tabel daftar obat
$obat_result = mysqli_query($conn, "SELECT * FROM Obat");

// Jika ingin mengisi formulir dengan data saat tombol edit ditekan
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $id_edit = intval($_GET['edit_id']);
    $edit_query = mysqli_query($conn, "SELECT * FROM Obat WHERE id_obat=$id_edit");
    $edit_data = mysqli_fetch_assoc($edit_query);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Obat</title>
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
        <h2>Kelola Obat</h2>
        <hr>

        <!-- Form Tambah/Edit Obat -->
        <div class="mb-4">
            <h4><?= $edit_data ? "Edit Obat" : "Tambah Obat Baru" ?></h4>
            <form method="POST">
                <input type="hidden" name="id_obat" value="<?= $edit_data ? $edit_data['id_obat'] : '' ?>">
                <div class="mb-3">
                    <label>Nama Obat</label>
                    <input type="text" name="nama_obat" class="form-control" value="<?= $edit_data ? $edit_data['nama_obat'] : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label>Kemasan</label>
                    <input type="text" name="kemasan" class="form-control" value="<?= $edit_data ? $edit_data['kemasan'] : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label>Harga</label>
                    <input type="number" name="harga" class="form-control" value="<?= $edit_data ? $edit_data['harga'] : '' ?>" required>
                </div>
                <button type="submit" name="create_or_update_obat" class="btn btn-success"><?= $edit_data ? "Perbarui Obat" : "Tambah Obat" ?></button>
            </form>
        </div>

        <hr>

        <!-- Tabel Daftar Obat -->
        <h4>Daftar Obat</h4>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Kemasan</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($obat = mysqli_fetch_assoc($obat_result)) { ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $obat['nama_obat'] ?></td>
                        <td><?= $obat['kemasan'] ?></td>
                        <td>Rp <?= number_format($obat['harga']) ?></td>
                        <td>
                            <a href="obat.php?edit_id=<?= $obat['id_obat'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="obat.php?delete_id=<?= $obat['id_obat'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus obat ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
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
