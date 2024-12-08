<?php
session_start();
include('../includes/db.php');

// Tangani aksi Create/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_or_update_dokter'])) {
    $id_dokter = isset($_POST['id_dokter']) ? intval($_POST['id_dokter']) : 0;
    $nama_dokter = mysqli_real_escape_string($conn, $_POST['nama_dokter']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $id_poli = intval($_POST['id_poli']);

    if ($id_dokter) {
        // Operasi Update
        $query = "UPDATE Dokter SET nama_dokter='$nama_dokter', alamat='$alamat', no_hp='$no_hp', id_poli=$id_poli WHERE id_dokter=$id_dokter";
    } else {
        // Operasi Insert
        $query = "INSERT INTO Dokter (nama_dokter, alamat, no_hp, id_poli) VALUES ('$nama_dokter', '$alamat', '$no_hp', $id_poli)";
    }

    mysqli_query($conn, $query);
    header('Location: dokter.php');
    exit();
}

// Tangani aksi Delete
if (isset($_GET['delete_id'])) {
    $id_dokter = intval($_GET['delete_id']);
    $query = "DELETE FROM Dokter WHERE id_dokter=$id_dokter";
    mysqli_query($conn, $query);
    header('Location: dokter.php');
    exit();
}

// Ambil data untuk dropdown dan daftar dokter
$dokter_result = mysqli_query($conn, "SELECT Dokter.*, Poli.nama_poli FROM Dokter JOIN Poli ON Dokter.id_poli = Poli.id_poli");
$poli_result = mysqli_query($conn, "SELECT * FROM Poli");

// Jika ingin mengisi formulir dengan data saat tombol edit ditekan
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $id_edit = intval($_GET['edit_id']);
    $edit_query = mysqli_query($conn, "SELECT * FROM Dokter WHERE id_dokter=$id_edit");
    $edit_data = mysqli_fetch_assoc($edit_query);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Dokter</title>
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

    <!-- Konten Utama -->
    <div class="content" id="content">
        <h2>Kelola Dokter</h2>
        <hr>
        <!-- Form Tambah/Edit Dokter -->
        <div class="mb-4">
            <h4><?= $edit_data ? "Edit Dokter" : "Tambah Dokter Baru" ?></h4>
            <form method="POST">
                <input type="hidden" name="id_dokter" value="<?= $edit_data ? $edit_data['id_dokter'] : '' ?>">
                <div class="mb-3">
                    <label>Nama Dokter</label>
                    <input type="text" name="nama_dokter" class="form-control" value="<?= $edit_data ? $edit_data['nama_dokter'] : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label>Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="<?= $edit_data ? $edit_data['alamat'] : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label>No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="<?= $edit_data ? $edit_data['no_hp'] : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label>Poli</label>
                    <select name="id_poli" class="form-control" required>
                        <?php while ($poli = mysqli_fetch_assoc($poli_result)) { ?>
                            <option value="<?= $poli['id_poli'] ?>" <?= $edit_data && $edit_data['id_poli'] == $poli['id_poli'] ? 'selected' : '' ?>>
                                <?= $poli['nama_poli'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" name="create_or_update_dokter" class="btn btn-success"><?= $edit_data ? "Perbarui Dokter" : "Tambah Dokter" ?></button>
            </form>
        </div>

        <hr>

        <h4>Daftar Dokter</h4>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th>Poli</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($dokter = mysqli_fetch_assoc($dokter_result)) { ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $dokter['nama_dokter'] ?></td>
                        <td><?= $dokter['alamat'] ?></td>
                        <td><?= $dokter['no_hp'] ?></td>
                        <td><?= $dokter['nama_poli'] ?></td>
                        <td>
                            <a href="dokter.php?edit_id=<?= $dokter['id_dokter'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="dokter.php?delete_id=<?= $dokter['id_dokter'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus dokter ini?')">Hapus</a>
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
