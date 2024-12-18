<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dokter') {
    header('Location: ../login.php');
    exit();
}

include('../includes/db.php');

// Ambil ID daftar pasien
$id_daftar = $_GET['id_daftar'] ?? null;
$pesan_error = '';
$jasa_dokter = 150000;

// Ambil detail pasien dan jadwal
if ($id_daftar) {
    $query = "
        SELECT 
            dp.id_daftar, 
            p.nama_pasien, 
            dp.keluhan, 
            dp.no_antrian
        FROM Daftar_Poli dp
        INNER JOIN Pasien p ON dp.id_pasien = p.id_pasien
        WHERE dp.id_daftar = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_daftar);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pasien = $result->fetch_assoc();
    } else {
        $pesan_error = 'Pasien tidak ditemukan!';
    }
} else {
    $pesan_error = 'ID pasien tidak ditemukan!';
}

// Ambil daftar obat dari database
$query_obat = "SELECT * FROM Obat";
$result_obat = $conn->query($query_obat);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tgl_periksa = $_POST['tgl_periksa'] ?? '';
    $catatan = $_POST['catatan'] ?? '';
    $obat_id = $_POST['obat'] ?? [];
    $biaya_periksa = $_POST['total_harga'] ?? $jasa_dokter;

    // Validasi input
    if (empty($tgl_periksa) || empty($catatan) || empty($obat_id)) {
        $pesan_error = 'Tanggal periksa, catatan, dan obat harus diisi!';
    } else {
        // Simpan ke tabel Periksa
        $query_insert = "INSERT INTO Periksa (id_daftar, tgl_periksa, catatan, biaya_periksa) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($query_insert);
        $stmt_insert->bind_param('issi', $id_daftar, $tgl_periksa, $catatan, $biaya_periksa);
        $stmt_insert->execute();

        // Tandai pasien sebagai selesai
        $query_update = "UPDATE Daftar_Poli SET status = 'selesai' WHERE id_daftar = ?";
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param('i', $id_daftar);
        $stmt_update->execute();

        header('Location: memeriksa.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Periksa Pasien</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Form Pemeriksaan Pasien</h2>
        <?php if ($pesan_error) { ?>
            <div class="alert alert-danger"><?= $pesan_error ?></div>
        <?php } elseif ($id_daftar) { ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="nama_pasien" class="form-label">Nama Pasien</label>
                    <input type="text" id="nama_pasien" class="form-control" value="<?= $pasien['nama_pasien'] ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="tgl_periksa" class="form-label">Tanggal Periksa</label>
                    <input type="date" id="tgl_periksa" name="tgl_periksa" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea id="catatan" name="catatan" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="obat" class="form-label">Pilih Obat</label>
                    <select id="obat" name="obat[]" class="form-select" multiple required>
                        <?php while ($row_obat = $result_obat->fetch_assoc()) { ?>
                            <option value="<?= $row_obat['id_obat'] ?>" data-harga="<?= $row_obat['harga'] ?>">
                                <?= $row_obat['nama_obat'] ?> - Rp <?= number_format($row_obat['harga'], 0, ',', '.') ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="total_harga" class="form-label">Total Harga</label>
                    <input type="text" id="total_harga" name="total_harga" class="form-control" value="<?= $jasa_dokter ?>" readonly>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        <?php } ?>
    </div>

    <script>
        // Menghitung total harga
        const obatSelect = document.getElementById('obat');
        const totalHargaInput = document.getElementById('total_harga');
        const jasaDokter = <?= $jasa_dokter ?>;

        obatSelect.addEventListener('change', () => {
            let totalHarga = jasaDokter;
            const selectedOptions = Array.from(obatSelect.selectedOptions);
            selectedOptions.forEach(option => {
                totalHarga += parseInt(option.getAttribute('data-harga'));
            });
            totalHargaInput.value = totalHarga;
        });
    </script>
</body>

</html>
