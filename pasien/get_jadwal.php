<?php
include('../includes/db.php');

if (isset($_GET['id_poli'])) {
    $id_poli = intval($_GET['id_poli']);

    $query = "
        SELECT Jadwal_Periksa.id_jadwal, Jadwal_Periksa.hari, Jadwal_Periksa.jam_mulai, Jadwal_Periksa.jam_selesai, Dokter.nama_dokter 
        FROM Jadwal_Periksa
        INNER JOIN Dokter ON Jadwal_Periksa.id_dokter = Dokter.id_dokter
        WHERE Dokter.id_poli = $id_poli AND Jadwal_Periksa.aktif = 1
    ";

    $result = mysqli_query($conn, $query);

    $jadwal = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $jadwal[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($jadwal);
    exit();
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit();
}
