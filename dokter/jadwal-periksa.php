<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dokter') {
    header('Location: ../login.php');
    exit();
}

include('../includes/db.php');

// Ambil ID dokter dari sesi
$id_dokter = $_SESSION['user_id'];

// Tangani form tambah/edit jadwal
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_jadwal = isset($_POST['id_jadwal']) ? intval($_POST['id_jadwal']) : 0;
    $aktif = isset($_POST['aktif']) ? 1 : 0;
    $hari = mysqli_real_escape_string($conn, $_POST['hari'] ?? '');
    $jam_mulai = mysqli_real_escape_string($conn, $_POST['jam_mulai'] ?? '');
    $jam_selesai = mysqli_real_escape_string($conn, $_POST['jam_selesai'] ?? '');

    // Validasi: Cek apakah sudah ada jadwal pada hari yang sama
    if (!$id_jadwal) { // Jika ini adalah input jadwal baru
        $check_query = "SELECT * FROM Jadwal_Periksa WHERE id_dokter = $id_dokter AND hari = '$hari'";
        $check_result = mysqli_query($conn, $check_query);
        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "Jadwal untuk hari $hari sudah ada.";
        } else {
            // Tambah jadwal baru (nonaktif)
            if ($aktif) {
                $disable_query = "UPDATE Jadwal_Periksa SET aktif = 0 WHERE id_dokter = $id_dokter";
                mysqli_query($conn, $disable_query);
            }
            $query = "INSERT INTO Jadwal_Periksa (id_dokter, hari, jam_mulai, jam_selesai, aktif) 
                      VALUES ($id_dokter, '$hari', '$jam_mulai', '$jam_selesai', $aktif)";
            if (mysqli_query($conn, $query)) {
                $success_message = "Jadwal berhasil ditambahkan.";
            } else {
                $error_message = "Terjadi kesalahan saat menambahkan jadwal: " . mysqli_error($conn);
            }
        }
    } else { // Jika ini adalah edit status jadwal
        if ($aktif) {
            $disable_query = "UPDATE Jadwal_Periksa SET aktif = 0 WHERE id_dokter = $id_dokter";
            mysqli_query($conn, $disable_query);
        }
        $query = "UPDATE Jadwal_Periksa SET aktif = $aktif WHERE id_jadwal = $id_jadwal AND id_dokter = $id_dokter";
        if (mysqli_query($conn, $query)) {
            $success_message = "Jadwal berhasil diperbarui.";
        } else {
            $error_message = "Terjadi kesalahan saat memperbarui jadwal: " . mysqli_error($conn);
        }
    }
}


// Ambil daftar jadwal periksa dokter
$jadwal_result = mysqli_query($conn, "SELECT * FROM Jadwal_Periksa WHERE id_dokter = $id_dokter");

// Data untuk form edit
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $edit_query = mysqli_query($conn, "SELECT * FROM Jadwal_Periksa WHERE id_jadwal = $edit_id AND id_dokter = $id_dokter");
    $edit_data = mysqli_fetch_assoc($edit_query);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Periksa Dokter</title>
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

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content p-4">
                <h2>Jadwal Periksa</h2>
                <hr>

                <!-- Pesan Sukses atau Error -->
                <?php if (isset($success_message)) { ?>
                    <div class="alert alert-success"> <?= $success_message ?> </div>
                <?php } ?>
                <?php if (isset($error_message)) { ?>
                    <div class="alert alert-danger"> <?= $error_message ?> </div>
                <?php } ?>

                <!-- Form Tambah/Edit Jadwal -->
                <div class="mb-4">
                    <h4><?= $edit_data ? "Edit Status Jadwal" : "Tambah Jadwal Baru" ?></h4>
                    <form method="POST">
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="id_jadwal" value="<?= $edit_data['id_jadwal'] ?>">
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="aktif" id="aktif" class="form-check-input" <?= $edit_data['aktif'] ? 'checked' : '' ?>>
                                <label for="aktif" class="form-check-label">Jadwal Aktif</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Perbarui Status</button>
                        <?php else: ?>
                            <div class="mb-3">
                                <label for="hari" class="form-label">Hari</label>
                                <select name="hari" id="hari" class="form-control" required>
                                    <option value="" disabled selected>Pilih Hari</option>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" required>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" name="aktif" id="aktif" class="form-check-input">
                                <label for="aktif" class="form-check-label">Jadwal Aktif</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Tambah Jadwal</button>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Daftar Jadwal -->
                <h4>Daftar Jadwal</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Hari</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($jadwal_result) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($jadwal_result)) { ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['hari'] ?></td>
                                    <td><?= $row['jam_mulai'] ?></td>
                                    <td><?= $row['jam_selesai'] ?></td>
                                    <td><?= $row['aktif'] ? 'Aktif' : 'Tidak Aktif' ?></td>
                                    <td>
                                        <a href="?edit_id=<?= $row['id_jadwal'] ?>" class="btn btn-warning btn-sm">Edit Status</a>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr><td colspan="6" class="text-center">Tidak ada jadwal</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
