<?php
session_start();
include('../includes/db.php');

// Tangani aksi Create/Update Pasien
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_or_update_pasien'])) {
    $id_pasien = isset($_POST['id_pasien']) ? intval($_POST['id_pasien']) : 0;
    $nama_pasien = mysqli_real_escape_string($conn, $_POST['nama_pasien']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_ktp = mysqli_real_escape_string($conn, $_POST['no_ktp']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);

    if ($id_pasien) {
        // Operasi Update
        $query = "UPDATE Pasien SET nama_pasien='$nama_pasien', alamat='$alamat', no_ktp='$no_ktp', no_hp='$no_hp' WHERE id_pasien=$id_pasien";
    } else {
        // Operasi Insert
        // Buat nomor rekam medis (no_rm)
        $current_month = date('Ym'); // Format YYYYMM
        $result = mysqli_query($conn, "SELECT COUNT(*) + 1 AS next_number FROM Pasien WHERE no_rm LIKE '$current_month%'");
        $row = mysqli_fetch_assoc($result);
        $next_number = str_pad($row['next_number'], 4, '0', STR_PAD_LEFT);
        $no_rm = "$current_month-$next_number";

        $query = "INSERT INTO Pasien (nama_pasien, alamat, no_ktp, no_hp, no_rm) VALUES ('$nama_pasien', '$alamat', '$no_ktp', '$no_hp', '$no_rm')";
    }

    mysqli_query($conn, $query);
    header('Location: pasien.php');
    exit();
}

// Tangani aksi Delete
if (isset($_GET['delete_id'])) {
    $id_pasien = intval($_GET['delete_id']);
    $query = "DELETE FROM Pasien WHERE id_pasien=$id_pasien";
    mysqli_query($conn, $query);
    header('Location: pasien.php');
    exit();
}

// Ambil semua data untuk tabel daftar pasien
$pasien_result = mysqli_query($conn, "SELECT * FROM Pasien");

// Jika ingin mengisi formulir dengan data saat tombol edit ditekan
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $id_edit = intval($_GET['edit_id']);
    $edit_query = mysqli_query($conn, "SELECT * FROM Pasien WHERE id_pasien=$id_edit");
    $edit_data = mysqli_fetch_assoc($edit_query);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Pasien</title>
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
            <div class="col-md-9 col-lg-10 content p-4">
                <h2>Kelola Pasien</h2>
                <hr>

                <!-- Form Tambah/Edit Pasien -->
                <div class="mb-4">
                    <h4><?= $edit_data ? "Edit Pasien" : "Tambah Pasien Baru" ?></h4>
                    <form method="POST">
                        <input type="hidden" name="id_pasien" value="<?= $edit_data ? $edit_data['id_pasien'] : '' ?>">
                        <div class="mb-3">
                            <label>Nama Pasien</label>
                            <input type="text" name="nama_pasien" class="form-control" value="<?= $edit_data ? $edit_data['nama_pasien'] : '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control" required><?= $edit_data ? $edit_data['alamat'] : '' ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>No KTP</label>
                            <input type="text" name="no_ktp" class="form-control" maxlength="16" value="<?= $edit_data ? $edit_data['no_ktp'] : '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>No HP</label>
                            <input type="text" name="no_hp" class="form-control" maxlength="15" value="<?= $edit_data ? $edit_data['no_hp'] : '' ?>" required>
                        </div>
                        <button type="submit" name="create_or_update_pasien" class="btn btn-success"><?= $edit_data ? "Perbarui Pasien" : "Tambah Pasien" ?></button>
                    </form>
                </div>

                <hr>

                <!-- Tabel Daftar Pasien -->
                <h4>Daftar Pasien</h4>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>No KTP</th>
                            <th>No HP</th>
                            <th>No RM</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1; // Inisialisasi nomor urut
                        while ($pasien = mysqli_fetch_assoc($pasien_result)) { ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $pasien['nama_pasien'] ?></td>
                                <td><?= $pasien['alamat'] ?></td>
                                <td><?= $pasien['no_ktp'] ?></td>
                                <td><?= $pasien['no_hp'] ?></td>
                                <td><?= $pasien['no_rm'] ?></td>
                                <td>
                                    <a href="pasien.php?edit_id=<?= $pasien['id_pasien'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="pasien.php?delete_id=<?= $pasien['id_pasien'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pasien ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
