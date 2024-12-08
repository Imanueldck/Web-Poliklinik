<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'appointmentsystem';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Koneksi Gagal: ' . $conn->connect_error);
}
?>
