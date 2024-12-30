<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dokter') {
    header('Location: ../login.php');
    exit();
}

include('../includes/db.php');

// Ambil ID daftar pasien
$id_daftar = $_GET['id_daftar'] ?? null;
if (!$id_daftar) {
    header('Location: memeriksa.php');
    exit();
}

// Ambil data pemeriksaan dan pasien
$query = "
    SELECT 
        dp.id_daftar, 
        p.nama_pasien, 
        dp.keluhan, 
        per.tgl_periksa, 
        per.catatan
    FROM Daftar_Poli dp
    JOIN Pasien p ON dp.id_pasien = p.id_pasien
    LEFT JOIN Periksa per ON dp.id_daftar = per.id_daftar
    WHERE dp.id_daftar = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id_daftar);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die('Data pemeriksaan tidak ditemukan.');
}

$periksa = $result->fetch_assoc();

// Ambil daftar obat dari database
$query_obat = "SELECT * FROM Obat";
$result_obat = $conn->query($query_obat);

// Ambil obat yang sudah diresepkan
$query_detail_obat = "
    SELECT o.id_obat, o.nama_obat 
    FROM Detail_Periksa dp
    JOIN Obat o ON dp.id_obat = o.id_obat
    WHERE dp.id_periksa = (SELECT id_periksa FROM Periksa WHERE id_daftar = ?)";
$stmt_obat = $conn->prepare($query_detail_obat);
$stmt_obat->bind_param('i', $id_daftar);
$stmt_obat->execute();
$result_detail_obat = $stmt_obat->get_result();

$selected_obat = [];
while ($row = $result_detail_obat->fetch_assoc()) {
    $selected_obat[] = $row['id_obat'];
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tgl_periksa = $_POST['tgl_periksa'];
    $catatan = $_POST['catatan'];
    $obat_id = $_POST['obat'] ?? [];

    // Jika tidak ada obat yang dipilih, atur default biaya hanya biaya dokter
    $biaya_obat = 0;
    if (count($obat_id) > 0) {
        $placeholders = implode(',', array_fill(0, count($obat_id), '?'));
        $query_harga_obat = "SELECT SUM(harga) AS total_harga FROM Obat WHERE id_obat IN ($placeholders)";
        $stmt_harga = $conn->prepare($query_harga_obat);
        $stmt_harga->bind_param(str_repeat('i', count($obat_id)), ...$obat_id);
        $stmt_harga->execute();
        $result_harga = $stmt_harga->get_result();
        $biaya_obat = $result_harga->fetch_assoc()['total_harga'] ?? 0;
    }

    $biaya_periksa = 150000 + $biaya_obat;

    // Update tabel Periksa
    $query_update_periksa = "
        UPDATE Periksa 
        SET tgl_periksa = ?, catatan = ?, biaya_periksa = ?
        WHERE id_daftar = ?";
    $stmt_update = $conn->prepare($query_update_periksa);
    $stmt_update->bind_param('ssii', $tgl_periksa, $catatan, $biaya_periksa, $id_daftar);
    $stmt_update->execute();

    // Hapus data lama dari Detail_Periksa
    $query_delete_detail = "DELETE FROM Detail_Periksa WHERE id_periksa = (SELECT id_periksa FROM Periksa WHERE id_daftar = ?)";
    $stmt_delete = $conn->prepare($query_delete_detail);
    $stmt_delete->bind_param('i', $id_daftar);
    $stmt_delete->execute();

    // Tambahkan data baru ke Detail_Periksa
    if (count($obat_id) > 0) {
        $query_insert_detail = "INSERT INTO Detail_Periksa (id_periksa, id_obat) VALUES ((SELECT id_periksa FROM Periksa WHERE id_daftar = ?), ?)";
        $stmt_insert = $conn->prepare($query_insert_detail);
        foreach ($obat_id as $id_obat) {
            $stmt_insert->bind_param('ii', $id_daftar, $id_obat);
            $stmt_insert->execute();
        }
    }

    header('Location: memeriksa.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pemeriksaan Pasien</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function calculateBiayaPeriksa() {
            const biayaDokter = 150000; // Biaya dokter tetap
            const obatOptions = document.querySelectorAll('#obat option:checked');
            let totalHargaObat = 0;

            obatOptions.forEach(option => {
                totalHargaObat += parseInt(option.dataset.harga, 10);
            });

            const totalBiaya = biayaDokter + totalHargaObat;
            document.getElementById('biaya_periksa').value = totalBiaya;
        }
    </script>
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Pemeriksaan Pasien</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="nama_pasien" class="form-label">Nama Pasien</label>
                <input type="text" id="nama_pasien" class="form-control" value="<?= htmlspecialchars($periksa['nama_pasien']) ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="keluhan" class="form-label">Keluhan</label>
                <textarea id="keluhan" class="form-control" readonly><?= htmlspecialchars($periksa['keluhan']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="tgl_periksa" class="form-label">Tanggal Periksa</label>
                <input type="date" id="tgl_periksa" name="tgl_periksa" class="form-control" value="<?= htmlspecialchars($periksa['tgl_periksa']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan</label>
                <textarea id="catatan" name="catatan" class="form-control" rows="4" required><?= htmlspecialchars($periksa['catatan']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="obat" class="form-label">Pilih Obat</label>
                <select id="obat" name="obat[]" class="form-select" multiple onchange="calculateBiayaPeriksa()" required>
                    <?php while ($row_obat = $result_obat->fetch_assoc()) { ?>
                        <option value="<?= $row_obat['id_obat'] ?>" data-harga="<?= $row_obat['harga'] ?>" <?= in_array($row_obat['id_obat'], $selected_obat) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row_obat['nama_obat']) ?> (Rp<?= number_format($row_obat['harga'], 0, ',', '.') ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="biaya_periksa" class="form-label">Biaya Periksa</label>
                <input type="text" id="biaya_periksa" class="form-control" value="150000" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="memeriksa.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
