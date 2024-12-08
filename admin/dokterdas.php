<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dokter') {
    header('Location: login.php');
    exit();
}
?>

<h1>Selamat Datang, Dokter <?= $_SESSION['username'] ?></h1>
