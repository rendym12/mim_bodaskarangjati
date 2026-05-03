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
        // Dashboard
        'dashboard.php' => 'Dashboard',
        // Profil
        'sejarah/index.php' => 'Sejarah',
        'visi_misi/index.php' => 'Visi & Misi',
        'sarana/index.php' => 'Sarana Prasarana',
        'sarana/tambah.php' => 'Tambah Sarana',
        'sarana/edit.php' => 'Edit Sarana',
        'sarana/detail.php' => 'Detail Sarana',
        'guru_staff/index.php' => 'Guru & Staff',
        'guru_staff/tambah.php' => 'Tambah Guru',
        'guru_staff/edit.php' => 'Edit Guru',
        'guru_staff/detail.php' => 'Detail Guru',
        // Kesiswaan
        'ekstrakurikuler/index.php' => 'Ekstrakurikuler',
        'ekstrakurikuler/tambah.php' => 'Tambah Ekstra',
        'ekstrakurikuler/edit.php' => 'Edit Ekstra',
        'ekstrakurikuler/detail.php' => 'Detail Ekstra',
        'prestasi/index.php' => 'Prestasi',
        'prestasi/tambah.php' => 'Tambah Prestasi',
        'prestasi/edit.php' => 'Edit Prestasi',
        'prestasi/detail.php' => 'Detail Prestasi',
        // Berita
        'pengumuman/index.php' => 'Pengumuman',
        'pengumuman/tambah.php' => 'Tambah Pengumuman',
        'pengumuman/edit.php' => 'Edit Pengumuman',
        'pengumuman/detail.php' => 'Detail Pengumuman',
        'agenda/index.php' => 'Agenda',
        'agenda/tambah.php' => 'Tambah Agenda',
        'agenda/edit.php' => 'Edit Agenda',
        'agenda/detail.php' => 'Detail Agenda',
        // PPDB
        'ppdb/index.php' => 'PPDB',
        // Galeri
        'galeri_foto/index.php' => 'Galeri Foto',
        'galeri_foto/tambah.php' => 'Tambah Foto',
        'galeri_foto/edit.php' => 'Edit Foto',
        'galeri_foto/detail.php' => 'Detail Foto',
        'galeri_video/index.php' => 'Galeri Video',
        'galeri_video/tambah.php' => 'Tambah Video',
        'galeri_video/edit.php' => 'Edit Video',
        'galeri_video/detail.php' => 'Detail Video',
        // Kontak
        'kontak/index.php' => 'Kontak',
        // Pengaturan Tampilan
        'sambutan/index.php' => 'Sambutan Kepala',
        'pembiasaan/index.php' => 'Pembiasaan Pagi',
        'pembiasaan/tambah.php' => 'Tambah Pembiasaan',
        'pembiasaan/edit.php' => 'Edit Pembiasaan',
        'pembiasaan/detail.php' => 'Detail Pembiasaan',
        'hero_slider/index.php' => 'Hero Slider',
        'hero_slider/tambah.php' => 'Tambah Slide',
        'hero_slider/edit.php' => 'Edit Slide',
        // Manajemen Data
        'testimoni/index.php' => 'Testimoni',
        'testimoni/detail.php' => 'Detail Testimoni',
        'data_siswa/index.php' => 'Data Siswa',
        'data_siswa/tambah.php' => 'Tambah Data Siswa',
        'data_siswa/edit.php' => 'Edit Data Siswa',
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
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
        
        <!-- MOBILE HEADER - STICKY DI ATAS (HANYA UNTUK MOBILE) -->
        <?php if (!$is_public_page): ?>
        <div class="mobile-header">
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="mobile-header-title">
                <i class="fas fa-school"></i>
                <span><?= $page_title ?></span>
            </div>
            <div class="mobile-header-right">
                <div class="mobile-profile" id="mobileProfileBtn">
                    <div class="profile-avatar">
                        <img src="<?= $base_url ?>/uploads/<?= $admin_foto ?>" alt="Profile">
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            
            <!-- Dropdown profile mobile -->
            <div class="mobile-profile-dropdown" id="mobileProfileDropdown">
                <a href="users/index.php" class="dropdown-item">
                    <i class="fas fa-users-cog"></i>
                    <span>Kelola Admin</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- OVERLAY untuk mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
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
            
            <!-- ==================== SIDEBAR MENU ==================== -->
            <div class="sidebar-menu">
                
                <!-- 1. DASHBOARD -->
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
                
                <!-- 2. PROFIL -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>Profil</span>
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
                    
                    <div class="menu-item <?= ($active_folder == 'sarana') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/sarana/index.php">
                            <i class="fas fa-building"></i>
                            <span>Sarana Prasarana</span>
                        </a>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'guru_staff') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/guru_staff/index.php">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>Guru & Staff</span>
                        </a>
                    </div>
                </div>
                
                <!-- 3. KESISWAAN -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>Kesiswaan</span>
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
                </div>
                
                <!-- 4. BERITA -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>Berita</span>
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
                </div>
                
                <!-- 5. PPDB -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>PPDB</span>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'ppdb') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/ppdb/index.php">
                            <i class="fas fa-graduation-cap"></i>
                            <span>PPDB</span>
                        </a>
                    </div>
                </div>
                
                <!-- 6. GALERI -->
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
                
                <!-- 7. KONTAK -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>Kontak</span>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'kontak') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/kontak/index.php">
                            <i class="fas fa-address-book"></i>
                            <span>Kontak</span>
                        </a>
                    </div>
                </div>
                
                <!-- ========== PEMISAH (TAMBAHAN ADMIN) ========== -->
                <div class="menu-divider"></div>
                
                <!-- 8. PENGATURAN TAMPILAN -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>Pengaturan Tampilan</span>
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
                    
                    <div class="menu-item <?= ($active_folder == 'hero_slider') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/hero_slider/index.php">
                            <i class="fas fa-images"></i>
                            <span>Hero Slider</span>
                        </a>
                    </div>
                </div>
                
                <!-- 9. MANAJEMEN DATA -->
                <div class="menu-section">
                    <div class="menu-header">
                        <span>Manajemen Data</span>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'testimoni') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/testimoni/index.php">
                            <i class="fas fa-star"></i>
                            <span>Testimoni</span>
                        </a>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'data_siswa') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/data_siswa/index.php">
                            <i class="fas fa-users"></i>
                            <span>Data Siswa</span>
                        </a>
                    </div>
                    
                    <div class="menu-item <?= ($active_folder == 'users') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>/admin/users/index.php">
                            <i class="fas fa-users-cog"></i>
                            <span>Kelola Admin</span>
                        </a>
                    </div>
                    
                    <div class="menu-divider-light"></div>
                    
                    <div class="menu-item">
                        <a href="<?= $base_url ?>/admin/logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Keluar</span>
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
            <!-- KONTEN DINAMIS AKAN DITAMPILKAN DI SINI OLEH FILE PHP LAINNYA -->