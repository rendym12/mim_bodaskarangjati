<?php
// File konfigurasi untuk BASE_URL dan koneksi database
// Simpan di includes/config.php

// Tentukan base URL secara dinamis
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$folder = '/mim_bodaskarangjati'; // Sesuaikan dengan nama folder project Anda

define('BASE_URL', $protocol . $host . $folder);
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . $folder);

// Koneksi database
$host_db = 'localhost';
$user_db = 'root';
$pass_db = '';
$name_db = 'mim_bodaskarangjati';

$conn = mysqli_connect($host_db, $user_db, $pass_db, $name_db);

if (!$conn) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>