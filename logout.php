<?php
session_start();

// Hancurkan sesi
session_destroy();

// Arahkan pengguna ke halaman login
header('Location: index.php');
exit();
?>
