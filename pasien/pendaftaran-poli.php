<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pasien' || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include('../includes/db.php');

// Tangani pendaftaran poli
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['daftar_poli'])) {
    $id_pasien = $_SESSION['user_id'];
    $id_poli = intval($_POST['id_poli']);
    $id_jadwal = intval($_POST['id_jadwal']);
    $keluhan = mysqli_real_escape_string($conn, $_POST['keluhan']);

    // Hitung nomor antrean terakhir berdasarkan id_jadwal
    $query_antrian = "SELECT MAX(no_antrian) AS last_antrian FROM Daftar_Poli WHERE id_jadwal = $id_jadwal";
    $result_antrian = mysqli_query($conn, $query_antrian);
    $last_antrian = mysqli_fetch_assoc($result_antrian)['last_antrian'];
    $no_antrian = $last_antrian ? $last_antrian + 1 : 1;

    // Insert ke database
    $query = "INSERT INTO Daftar_Poli (id_pasien, id_jadwal, keluhan, no_antrian) 
              VALUES ($id_pasien, $id_jadwal, '$keluhan', $no_antrian)";

    if (mysqli_query($conn, $query)) {
        $success_message = "Pendaftaran poli berhasil! Nomor antrean Anda adalah: $no_antrian";
    } else {
        $error_message = "Terjadi kesalahan saat mendaftar: " . mysqli_error($conn);
    }
}

// Ambil daftar poli dan jadwal dari database
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
                <a href="pasiendas.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="pendaftaran-poli.php" class="text-white mb-2"><i class="fas fa-clinic-medical"></i> Pendaftaran Poli</a>
                <a href="riwayat_periksa.php" class="text-white mb-2"><i class="fas fa-history"></i> Riwayat Periksa</a>
                <div class="logout mt-auto">
                    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <!-- Konten Utama -->
            <div class="col-md-9 col-lg-10 content p-4">
                <h2 class="text-center mb-3">Pendaftaran Poli</h2>

                <!-- Tampilkan pesan jika sukses atau error -->
                <?php if (isset($success_message)) { ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                <?php } ?>
                <?php if (isset($error_message)) { ?>
                    <div class="alert alert-danger"><?= $error_message ?></div>
                <?php } ?>

                <!-- Form Pendaftaran Poli -->
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <!-- Nomor Rekam Medis yang diambil dari sesi login -->
                            <div class="mb-3">
                                <label for="no_rm" class="form-label">Nomor Rekam Medis Anda</label>
                                <input type="text" name="no_rm" class="form-control" value="<?= $_SESSION['no_rm'] ?>" disabled>
                            </div>

                            <!-- Pilih Poli dari database -->
                            <div class="mb-3">
                                <label for="id_poli" class="form-label">Pilih Poli</label>
                                <select name="id_poli" id="id_poli" class="form-control" required onchange="loadJadwal(this.value)">
                                    <option value="" disabled selected>Pilih Poli</option>
                                    <?php while ($poli = mysqli_fetch_assoc($poli_result)) { ?>
                                        <option value="<?= $poli['id_poli'] ?>"><?= $poli['nama_poli'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <!-- Pilih Jadwal Periksa dari database -->
                            <div class="mb-3">
                                <label for="id_jadwal" class="form-label">Pilih Jadwal</label>
                                <select name="id_jadwal" id="id_jadwal" class="form-control" required>
                                    <option value="" disabled selected>Pilih Jadwal</option>
                                </select>
                            </div>

                            <!-- Area untuk mengisi keluhan -->
                            <div class="mb-3">
                                <label for="keluhan" class="form-label">Keluhan</label>
                                <textarea name="keluhan" class="form-control" rows="4" placeholder="Tuliskan keluhan Anda di sini..." required></textarea>
                            </div>

                            <button type="submit" name="daftar_poli" class="btn btn-primary w-100">Daftar Poli</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('d-none');
        }

        function loadJadwal(id_poli) {
            fetch(`get_jadwal.php?id_poli=${id_poli}`)
                .then(response => response.json())
                .then(data => {
                    const jadwalSelect = document.getElementById('id_jadwal');
                    jadwalSelect.innerHTML = '<option value="" disabled selected>Pilih Jadwal</option>';
                    data.forEach(jadwal => {
                        jadwalSelect.innerHTML += `<option value="${jadwal.id_jadwal}">${jadwal.hari} | ${jadwal.jam_mulai} - ${jadwal.jam_selesai} (Dokter: ${jadwal.nama_dokter})</option>`;
                    });
                })
                .catch(error => console.error('Error:', error));
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
