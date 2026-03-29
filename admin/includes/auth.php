<?php
// includes/db.php
$host = 'localhost';
$user = 'root'; // Sesuaikan dengan user database Anda
$pass = ''; // Sesuaikan dengan password database Anda
$dbname = 'mim_bodaskarangjati';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");
?>