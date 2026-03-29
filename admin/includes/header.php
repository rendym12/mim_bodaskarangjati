<?php
ob_start(); // TAMBAHKAN INI DI BARIS PERTAMA

// Cek session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = 'http://localhost/mim_bodaskarangjati';

// Cek koneksi database
if (!isset($conn) && file_exists('../includes/db.php')) {
    include '../includes/db.php';
}

// AMBIL NAMA HALAMAN SAAT INI
$current_page = basename($_SERVER['PHP_SELF']);
$current_folder = basename(dirname($_SERVER['PHP_SELF']));

// DAFTAR HALAMAN YANG TIDAK PERLU LOGIN
$public_pages = ['login.php', 'lupa-password.php', 'reset-password.php'];

// CEK APAKAH INI HALAMAN PUBLIK
$is_public_page = in_array($current_page, $public_pages);

// UNTUK HALAMAN PUBLIK, JANGAN AKSES SESSION ADMIN
if ($is_public_page) {
    $admin_nama = 'Guest';
    $admin_foto = 'default-avatar.jpg';
    $page_title = 'Login Admin';
} else {
    // UNTUK HALAMAN NON-PUBLIK, PASTIKAN SUDAH LOGIN
    if (!isset($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit;
    }
    
    // AMBIL DATA ADMIN
    $admin_nama = $_SESSION['admin_nama'] ?? 'Admin';
    $admin_foto = $_SESSION['admin_foto'] ?? 'default-avatar.jpg';
    
    // JUDUL HALAMAN
    $page_titles = [
        'dashboard.php' => 'Dashboard',
        'pengumuman/index.php' => 'Pengumuman',
        'pengumuman/tambah.php' => 'Tambah Pengumuman',
        'pengumuman/edit.php' => 'Edit Pengumuman',
        'pengumuman/detail.php' => 'Detail Pengumuman',
        'agenda/index.php' => 'Agenda',
        'agenda/tambah.php' => 'Tambah Agenda',
        'agenda/edit.php' => 'Edit Agenda',
        'agenda/detail.php' => 'Detail Agenda',
        'guru_staff/index.php' => 'Guru & Staff',
        'guru_staff/tambah.php' => 'Tambah Guru',
        'guru_staff/edit.php' => 'Edit Guru',
        'guru_staff/detail.php' => 'Detail Guru',
        'ekstrakurikuler/index.php' => 'Ekstrakurikuler',
        'ekstrakurikuler/tambah.php' => 'Tambah Ekstra',
        'ekstrakurikuler/edit.php' => 'Edit Ekstra',
        'ekstrakurikuler/detail.php' => 'Detail Ekstra',
        'prestasi/index.php' => 'Prestasi',
        'prestasi/tambah.php' => 'Tambah Prestasi',
        'prestasi/edit.php' => 'Edit Prestasi',
        'prestasi/detail.php' => 'Detail Prestasi',
        'sarana/index.php' => 'Sarana',
        'sarana/tambah.php' => 'Tambah Sarana',
        'sarana/edit.php' => 'Edit Sarana',
        'sarana/detail.php' => 'Detail Sarana',
        'hero_slider/index.php' => 'Hero Slider',
        'hero_slider/tambah.php' => 'Tambah Slide',
        'hero_slider/edit.php' => 'Edit Slide',
        'sejarah/index.php' => 'Sejarah',
        'visi_misi/index.php' => 'Visi & Misi',
        'sambutan/index.php' => 'Sambutan',
        'pembiasaan/index.php' => 'Pembiasaan',
        'pembiasaan/tambah.php' => 'Tambah Pembiasaan',
        'pembiasaan/edit.php' => 'Edit Pembiasaan',
        'pembiasaan/detail.php' => 'Detail Pembiasaan',
        'kontak/index.php' => 'Kontak',
        'ppdb/index.php' => 'PPDB',
        'galeri_foto/index.php' => 'Galeri Foto',
        'galeri_foto/tambah.php' => 'Tambah Foto',
        'galeri_foto/edit.php' => 'Edit Foto',
        'galeri_foto/detail.php' => 'Detail Foto',
        'galeri_video/index.php' => 'Galeri Video',
        'galeri_video/tambah.php' => 'Tambah Video',
        'galeri_video/edit.php' => 'Edit Video',
        'galeri_video/detail.php' => 'Detail Video',
        'users/index.php' => 'Kelola Admin',
        'users/tambah.php' => 'Tambah Admin',
        'users/edit.php' => 'Edit Admin',
    ];
    
    // Buat key untuk pencarian judul
    $current_key = '';
    if ($current_folder != 'admin') {
        $current_key = $current_folder . '/' . $current_page;
    } else {
        $current_key = $current_page;
    }
    
    $page_title = $page_titles[$current_key] ?? 'Panel Admin';
    
    // Tentukan folder aktif untuk menu
    $active_folder = $current_folder;
    if ($current_folder == 'admin') {
        $active_folder = 'root';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title><?= $page_title ?> - MI Muhammadiyah Bodaskarangjati</title>
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- OVERLAY untuk mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- FLOATING HAMBURGER BUTTON - HANYA UNTUK HALAMAN NON-PUBLIK -->
        <?php if (!$is_public_page): ?>
        <button class="mobile-hamburger" id="floatingMenuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <?php endif; ?>
        
        <!-- SIDEBAR - HANYA TAMPIL JIKA BUKAN HALAMAN PUBLIK -->
        <?php if (!$is_public_page): ?>
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="<?= $base_url ?>/assets/img/logo.png" alt="Logo MI" onerror="this.src='https://via.placeholder.com/50x50?text=MI'">
                    <div class="logo-text">
                        <h3>MI Muhammadiyah</h3>
                        <p>Bodaskarangjati</p>
                    </div>
                </div>
                <button class="sidebar-close" id="sidebarClose" aria-label="Tutup Sidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Menu Sidebar -->
            <div class="sidebar-menu">
                <!-- Dashboard -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>Dashboard</span>
                    </div>
                    
                    <div class="menu-item <?= ($current_page == 'dashboard.php' && $current_folder == 'admin') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/dashboard.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>
                
                <!-- Master Data -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>Master Data</span>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'pengumuman') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/pengumuman/index.php">
                            <i class="fas fa-bullhorn"></i>
                            <span>Pengumuman</span>
                        </a>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'agenda') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/agenda/index.php">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Agenda</span>
                        </a>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'guru_staff') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/guru_staff/index.php">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>Guru & Staff</span>
                        </a>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'ekstrakurikuler') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/ekstrakurikuler/index.php">
                            <i class="fas fa-futbol"></i>
                            <span>Ekstrakurikuler</span>
                        </a>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'prestasi') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/prestasi/index.php">
                            <i class="fas fa-trophy"></i>
                            <span>Prestasi</span>
                        </a>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'sarana') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/sarana/index.php">
                            <i class="fas fa-building"></i>
                            <span>Sarana Prasarana</span>
                        </a>
                    </div>
                </div>
                
                <!-- Slider -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>Slider</span>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'hero_slider') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/hero_slider/index.php">
                            <i class="fas fa-images"></i>
                            <span>Hero Slider</span>
                        </a>
                    </div>
                </div>
                
                <!-- Profil Madrasah -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>Profil Madrasah</span>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'sejarah') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/sejarah/index.php">
                            <i class="fas fa-history"></i>
                            <span>Sejarah</span>
                        </a>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'visi_misi') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/visi_misi/index.php">
                            <i class="fas fa-eye"></i>
                            <span>Visi & Misi</span>
                        </a>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'sambutan') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/sambutan/index.php">
                            <i class="fas fa-microphone"></i>
                            <span>Sambutan Kepala</span>
                        </a>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'pembiasaan') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/pembiasaan/index.php">
                            <i class="fas fa-sun"></i>
                            <span>Pembiasaan Pagi</span>
                        </a>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'kontak') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/kontak/index.php">
                            <i class="fas fa-address-book"></i>
                            <span>Kontak</span>
                        </a>
                    </div>
                </div>
                
                <!-- Galeri -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>Galeri</span>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'galeri_foto') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/galeri_foto/index.php">
                            <i class="fas fa-image"></i>
                            <span>Galeri Foto</span>
                        </a>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'galeri_video') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/galeri_video/index.php">
                            <i class="fas fa-video"></i>
                            <span>Galeri Video</span>
                        </a>
                    </div>
                </div>
                
                <!-- PPDB -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>PPDB</span>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'ppdb') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/ppdb/index.php">
                            <i class="fas fa-cog"></i>
                            <span>Konfigurasi PPDB</span>
                        </a>
                    </div>
                </div>
                
                <!-- Pengaturan -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>Pengaturan</span>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'users') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/users/index.php">
                            <i class="fas fa-users-cog"></i>
                            <span>Kelola Admin</span>
                        </a>
                    </div>
                    
                    <div class="menu-item">
                        <a href="<?= $base_url ?>/admin/logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="sidebar-footer">
                <p>© <?= date('Y') ?> MI Muhammadiyah Bodaskarangjati</p>
            </div>
        </aside>
        <?php endif; ?>

        <!-- MAIN CONTENT AREA -->
        <main class="admin-main">
            <!-- Topbar untuk mobile - HANYA UNTUK MOBILE -->
            <?php if (!$is_public_page): ?>
            <div class="topbar mobile-topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-toggle" id="mobileMenuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="page-title"><?= $page_title ?></span>
                </div>
                <div class="topbar-right">
                    <!-- Notifikasi bisa ditambahkan di sini -->
                </div>
            </div>
            <?php endif; ?>