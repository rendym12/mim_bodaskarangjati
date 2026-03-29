<?php
// Include config untuk mendapatkan BASE_URL
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>MI Muhammadiyah Bodaskarangjati</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Global -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    
    <!-- JavaScript Global -->
    <script src="<?= BASE_URL ?>/assets/js/public.js"></script>
</head>
<body>
<header>
    <!-- HAMBURGER MENU - MODERN & PROFESIONAL -->
    <button class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </button>
    
    <!-- LOGO -->
    <div class="logo">
        <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo">
        <div class="logo-text">
            <h1>MI Muhammadiyah <span>Bodaskarangjati</span></h1>
        </div>
    </div>
    
    <nav class="nav-menu" id="navMenu">
        <ul>
            <li><a href="<?= BASE_URL ?>/index.php">Beranda</a></li>
            
            <li class="dropdown">
                <a href="#" class="dropdown-link">Profil <i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown-content">
                    <li><a href="<?= BASE_URL ?>/profil/sejarah.php">Sejarah</a></li>
                    <li><a href="<?= BASE_URL ?>/profil/visi_misi.php">Visi & Misi</a></li>
                    <li><a href="<?= BASE_URL ?>/profil/guru_staff.php">Guru & Staff</a></li>
                    <li><a href="<?= BASE_URL ?>/profil/sarana.php">Sarana Prasarana</a></li>
                </ul>
            </li>
            
            <li class="dropdown">
                <a href="#" class="dropdown-link">Kesiswaan <i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown-content">
                    <li><a href="<?= BASE_URL ?>/kesiswaan/ekstrakurikuler.php">Ekstrakurikuler</a></li>
                    <li><a href="<?= BASE_URL ?>/kesiswaan/prestasi.php">Prestasi</a></li>
                </ul>
            </li>
            
            <li class="dropdown">
                <a href="#" class="dropdown-link">Berita <i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown-content">
                    <li><a href="<?= BASE_URL ?>/berita/pengumuman.php">Pengumuman</a></li>
                    <li><a href="<?= BASE_URL ?>/berita/agenda.php">Agenda</a></li>
                </ul>
            </li>
            
            <li class="dropdown">
                <a href="#" class="dropdown-link">Galeri <i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown-content">
                    <li><a href="<?= BASE_URL ?>/galeri/foto.php">Foto</a></li>
                    <li><a href="<?= BASE_URL ?>/galeri/video.php">Video</a></li>
                </ul>
            </li>
            
            <li><a href="<?= BASE_URL ?>/ppdb.php" class="btn-ppdb">PPDB</a></li>
            <li><a href="<?= BASE_URL ?>/kontak.php">Kontak</a></li>
        </ul>
    </nav>
</header>
<main></main>