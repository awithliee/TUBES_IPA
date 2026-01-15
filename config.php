<?php
$host = "localhost";
$user = "admin_inventaris";
$pass = "password_aman_123";
$db   = "sistem_inventaris";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) { die("Koneksi Gagal: " . mysqli_connect_error()); }
mysqli_set_charset($conn, "utf8mb4");
?>