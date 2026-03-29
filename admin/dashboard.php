<?php
include "includes/header.php";

// ==============================================
// AMBIL DATA ADMIN UNTUK PROFIL
// ==============================================
$admin_id = $_SESSION['admin_id'];
$admin_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM admin_users WHERE id = $admin_id"));
$admin_foto = !empty($admin_data['foto']) && $admin_data['foto'] != 'default-avatar.jpg' ? $admin_data['foto'] : 'default-avatar.jpg';
$admin_nama = $admin_data['nama_lengkap'] ?? $_SESSION['admin_nama'] ?? 'Admin';
$admin_username = $admin_data['username'] ?? 'admin';

// ==============================================
// GREETING BERDASARKAN WAKTU
// ==============================================
$jam = date('H');
if ($jam >= 3 && $jam < 12) {
    $greeting = "Selamat Pagi";
    $emoji = "☀️";
} elseif ($jam >= 12 && $jam < 15) {
    $greeting = "Selamat Siang";
    $emoji = "⛅";
} elseif ($jam >= 15 && $jam < 18) {
    $greeting = "Selamat Sore";
    $emoji = "🌆";
} else {
    $greeting = "Selamat Malam";
    $emoji = "🌙";
}

// ==============================================
// STATISTIK UTAMA - SEMUA TABEL
// ==============================================

// Master Data
$total_pengumuman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman"))['total'] ?? 0;
$total_agenda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM agenda"))['total'] ?? 0;
$total_guru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM guru_staff"))['total'] ?? 0;
$total_ekstra = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ekstrakurikuler"))['total'] ?? 0;
$total_prestasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM prestasi"))['total'] ?? 0;
$total_sarana = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM sarana"))['total'] ?? 0;
$total_pembiasaan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pembiasaan"))['total'] ?? 0;
$total_sambutan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM sambutan"))['total'] ?? 0;
$total_visi_misi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM visi_misi"))['total'] ?? 0;
$total_sejarah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM sejarah"))['total'] ?? 0;
$total_slider = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hero_slider WHERE status='aktif'"))['total'] ?? 0;
$total_slider_all = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hero_slider"))['total'] ?? 0;

// Galeri
$total_foto = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM galeri_foto"))['total'] ?? 0;
$total_video = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM galeri_video"))['total'] ?? 0;

// Konfigurasi
$ppdb_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM ppdb LIMIT 1"));
$kontak_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kontak LIMIT 1"));

$total_ppdb = $ppdb_data ? 1 : 0;
$total_kontak = $kontak_data ? 1 : 0;

// Users
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM admin_users"))['total'] ?? 0;

// ==============================================
// DATA UNTUK INFO PANEL
// ==============================================

// Cek status PPDB
$ppdb = $ppdb_data;
$ppdb_status = $ppdb ? $ppdb['status'] : 'nonaktif';
$ppdb_tahun = $ppdb ? $ppdb['tahun_ajaran'] : date('Y') . '/' . (date('Y')+1);

// Hitung agenda hari ini
$today = date('Y-m-d');
$agenda_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM agenda WHERE tanggal_mulai <= '$today' AND tanggal_selesai >= '$today'"))['total'] ?? 0;

// Hitung pengumuman bulan ini
$bulan_ini = date('Y-m');
$pengumuman_bulan_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_ini'"))['total'] ?? 0;

// ==============================================
// DATA TERBARU
// ==============================================
$recent_pengumuman = mysqli_query($conn, "SELECT * FROM pengumuman ORDER BY tanggal DESC LIMIT 5");
$upcoming_agenda = mysqli_query($conn, "SELECT * FROM agenda WHERE tanggal_mulai >= CURDATE() ORDER BY tanggal_mulai ASC LIMIT 5");
$recent_prestasi = mysqli_query($conn, "SELECT * FROM prestasi ORDER BY tahun DESC LIMIT 5");
$recent_galeri = mysqli_query($conn, "SELECT * FROM galeri_foto ORDER BY id DESC LIMIT 4");
?>

<!-- CONTENT WRAPPER -->
<div class="content-wrapper">
    <!-- CSS UNTUK SEMBUNYIKAN TOPBAR DI DASHBOARD -->
    <style>
        /* Sembunyikan topbar mobile hanya di halaman dashboard */
        .mobile-topbar {
            display: none !important;
        }
        
        /* Pastikan konten dashboard tetap rapi */
        .dashboard-header {
            margin-top: 0;
        }
        
        /* Responsive untuk mobile */
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 15px;
            }
            
            .header-left h1 {
                font-size: 1.5rem;
            }
            
            .header-left p {
                font-size: 0.85rem;
            }
            
            .topbar-profile {
                padding: 5px 10px;
            }
            
            .profile-avatar {
                width: 35px;
                height: 35px;
            }
            
            .profile-name {
                font-size: 0.8rem;
            }
            
            .profile-role {
                font-size: 0.65rem;
            }
        }
    </style>
    
    <!-- DASHBOARD HEADER dengan GREETING PERSONAL -->
    <div class="dashboard-header">
        <div class="header-left">
            <div class="greeting-badge">
                <span class="greeting-emoji"><?= $emoji ?></span>
                <span class="greeting-text"><?= $greeting ?>,</span>
            </div>
            <h1><?= htmlspecialchars(explode(' ', $admin_nama)[0]) ?>! 👋</h1>
            <p><?= date('l, d F Y') ?> • Senang melihat Anda kembali</p>
        </div>
        <div class="header-right">
            <!-- Profil Admin di Kanan Atas -->
            <div class="topbar-profile" id="profileDropdown">
                <div class="profile-info">
                    <span class="profile-name"><?= htmlspecialchars($admin_nama) ?></span>
                    <span class="profile-role">Administrator</span>
                </div>
                <div class="profile-avatar">
                    <img src="../uploads/<?= $admin_foto ?>" alt="Profile">
                </div>
                <i class="fas fa-chevron-down"></i>
            </div>
            
            <!-- Dropdown Menu -->
            <div class="profile-dropdown" id="profileDropdownMenu">
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
    </div>

    <!-- STATISTIK UTAMA - 6 KOLOM PENTING -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="stat-content">
                <h3><?= $total_pengumuman ?></h3>
                <p>Pengumuman</p>
                <small class="stat-sub"><?= $pengumuman_bulan_ini ?> bulan ini</small>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-content">
                <h3><?= $total_agenda ?></h3>
                <p>Agenda</p>
                <small class="stat-sub"><?= $agenda_hari_ini ?> hari ini</small>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-content">
                <h3><?= $total_guru ?></h3>
                <p>Guru & Staff</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="stat-content">
                <h3><?= $total_prestasi ?></h3>
                <p>Prestasi</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-danger">
                <i class="fas fa-images"></i>
            </div>
            <div class="stat-content">
                <h3><?= $total_foto + $total_video ?></h3>
                <p>Galeri</p>
                <small class="stat-sub"><?= $total_foto ?> foto • <?= $total_video ?> video</small>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-purple">
                <i class="fas fa-users-cog"></i>
            </div>
            <div class="stat-content">
                <h3><?= $total_users ?></h3>
                <p>Admin</p>
            </div>
        </div>
    </div>

    <!-- INFO PANELS - STATUS PENTING -->
    <div class="info-panels-grid">
        <!-- Panel PPDB -->
        <div class="info-panel <?= $ppdb_status ?>">
            <div class="info-panel-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="info-panel-content">
                <h3>PPDB <?= $ppdb_tahun ?></h3>
                <div class="status-indicator">
                    <span class="status-dot <?= $ppdb_status ?>"></span>
                    <span class="status-text"><?= strtoupper($ppdb_status) ?></span>
                </div>
                <?php if ($ppdb_status == 'aktif' && $ppdb): ?>
                <p class="info-deadline">Pendaftaran: <?= date('d/m', strtotime($ppdb['tanggal_mulai'])) ?> - <?= date('d/m/Y', strtotime($ppdb['tanggal_selesai'])) ?></p>
                <?php endif; ?>
            </div>
            <div class="info-panel-badge">
                <a href="ppdb/index.php" class="badge-user">
                    <i class="fas fa-cog"></i> Kelola
                </a>
            </div>
        </div>

        <!-- Panel Slider -->
        <div class="info-panel">
            <div class="info-panel-icon bg-primary">
                <i class="fas fa-images"></i>
            </div>
            <div class="info-panel-content">
                <h3>Hero Slider</h3>
                <p><?= $total_slider ?> dari <?= $total_slider_all ?> slide aktif</p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $total_slider_all > 0 ? ($total_slider/$total_slider_all)*100 : 0 ?>%"></div>
                </div>
            </div>
            <div class="info-panel-badge">
                <a href="hero_slider/index.php" class="badge-user">
                    <i class="fas fa-edit"></i> Kelola
                </a>
            </div>
        </div>

        <!-- Panel Kontak -->
        <div class="info-panel">
            <div class="info-panel-icon bg-success">
                <i class="fas fa-address-book"></i>
            </div>
            <div class="info-panel-content">
                <h3>Informasi Kontak</h3>
                <p><?= $kontak_data ? '✓ Data kontak tersedia' : '✗ Data kontak belum diisi' ?></p>
            </div>
            <div class="info-panel-badge">
                <a href="kontak/index.php" class="badge-user">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
    </div>

    <!-- WELCOME SECTION - FITUR UNGGULAN -->
    <div class="welcome-section">
        <div class="welcome-content">
            <h2>Ringkasan Aktivitas</h2>
            <div class="activity-stats">
                <div class="activity-item">
                    <span class="activity-value"><?= $total_pengumuman + $total_agenda + $total_prestasi ?></span>
                    <span class="activity-label">Total Konten</span>
                </div>
                <div class="activity-item">
                    <span class="activity-value"><?= $total_guru + $total_ekstra ?></span>
                    <span class="activity-label">SDM & Ekstra</span>
                </div>
                <div class="activity-item">
                    <span class="activity-value"><?= $total_foto + $total_video ?></span>
                    <span class="activity-label">Dokumentasi</span>
                </div>
            </div>
        </div>
        <div class="welcome-illustration">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>

    <!-- DASHBOARD GRID - 2 KOLOM UTAMA -->
    <div class="dashboard-grid">
        
        <!-- KOLOM KIRI (Konten Terbaru) -->
        <div class="grid-col main-col">
            <!-- Card Pengumuman Terbaru -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-newspaper"></i> Pengumuman Terbaru</h3>
                    <a href="pengumuman/index.php" class="btn-sm">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($recent_pengumuman) > 0): ?>
                        <ul class="item-list">
                            <?php while($row = mysqli_fetch_assoc($recent_pengumuman)): ?>
                            <li>
                                <a href="pengumuman/edit.php?id=<?= $row['id'] ?>">
                                    <div class="item-icon">
                                        <i class="fas fa-bullhorn"></i>
                                    </div>
                                    <div class="item-content">
                                        <span class="item-title"><?= htmlspecialchars($row['judul']) ?></span>
                                        <span class="item-meta">
                                            <i class="fas fa-calendar"></i> <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                            <?php if(isset($row['status']) && $row['status'] == 'draft'): ?>
                                                <span class="badge-draft">Draft</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-newspaper"></i>
                            <p>Belum ada pengumuman</p>
                            <a href="pengumuman/tambah.php" class="btn-sm">Buat Pengumuman</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Card Prestasi Terbaru -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-trophy"></i> Prestasi Terbaru</h3>
                    <a href="prestasi/index.php" class="btn-sm">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($recent_prestasi) > 0): ?>
                        <ul class="item-list">
                            <?php while($row = mysqli_fetch_assoc($recent_prestasi)): ?>
                            <li>
                                <a href="prestasi/edit.php?id=<?= $row['id'] ?>">
                                    <div class="item-icon">
                                        <i class="fas fa-medal"></i>
                                    </div>
                                    <div class="item-content">
                                        <span class="item-title"><?= htmlspecialchars($row['nama_prestasi']) ?></span>
                                        <span class="item-meta">
                                            <span class="badge-year"><?= $row['tahun'] ?></span>
                                            <span class="badge-level"><?= htmlspecialchars($row['tingkat'] ?? '') ?></span>
                                        </span>
                                    </div>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-trophy"></i>
                            <p>Belum ada prestasi</p>
                            <a href="prestasi/tambah.php" class="btn-sm">Tambah Prestasi</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- KOLOM KANAN (Agenda & Galeri) -->
        <div class="grid-col">
            <!-- Card Agenda Mendatang -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-alt"></i> Agenda Mendatang</h3>
                    <a href="agenda/index.php" class="btn-sm">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($upcoming_agenda) > 0): ?>
                        <ul class="item-list">
                            <?php while($row = mysqli_fetch_assoc($upcoming_agenda)): 
                                $selisih = floor((strtotime($row['tanggal_mulai']) - strtotime($today)) / (60 * 60 * 24));
                            ?>
                            <li>
                                <a href="agenda/edit.php?id=<?= $row['id'] ?>">
                                    <div class="item-icon agenda">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="item-content">
                                        <span class="item-title"><?= htmlspecialchars($row['nama_agenda']) ?></span>
                                        <span class="item-meta">
                                            <i class="fas fa-calendar-day"></i> 
                                            <?php if($selisih == 0): ?>
                                                <span class="badge-today">Hari Ini</span>
                                            <?php elseif($selisih == 1): ?>
                                                <span class="badge-tomorrow">Besok</span>
                                            <?php else: ?>
                                                <?= date('d M', strtotime($row['tanggal_mulai'])) ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar"></i>
                            <p>Tidak ada agenda mendatang</p>
                            <a href="agenda/tambah.php" class="btn-sm">Buat Agenda</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Card Galeri Preview -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-images"></i> Galeri Terbaru</h3>
                    <a href="galeri_foto/index.php" class="btn-sm">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($recent_galeri) > 0): ?>
                        <div class="gallery-preview">
                            <?php while($row = mysqli_fetch_assoc($recent_galeri)): ?>
                            <a href="galeri_foto/detail.php?id=<?= $row['id'] ?>" class="gallery-thumb">
                                <img src="../uploads/galeri_foto/<?= $row['file_foto'] ?>" alt="<?= $row['judul'] ?? 'Foto' ?>">
                                <span class="gallery-overlay">
                                    <i class="fas fa-search-plus"></i>
                                </span>
                            </a>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state small">
                            <i class="fas fa-images"></i>
                            <p>Belum ada foto</p>
                            <a href="galeri_foto/tambah.php" class="btn-sm">Upload Foto</a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="galeri_foto/index.php">Kelola Galeri Foto <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            
            <!-- Card Admin Users -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-users-cog"></i> Admin Aktif</h3>
                    <a href="users/index.php" class="btn-sm">Kelola <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="card-body">
                    <?php
                    $users = mysqli_query($conn, "SELECT id, username, nama_lengkap, foto FROM admin_users ORDER BY id LIMIT 4");
                    ?>
                    <?php if (mysqli_num_rows($users) > 0): ?>
                        <div class="admin-avatars">
                            <?php while($row = mysqli_fetch_assoc($users)): ?>
                            <div class="admin-avatar-item" title="<?= htmlspecialchars($row['nama_lengkap']) ?>">
                                <?php if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg'): ?>
                                    <img src="../uploads/<?= $row['foto'] ?>" alt="User">
                                <?php else: ?>
                                    <div class="avatar-placeholder">
                                        <?= strtoupper(substr($row['nama_lengkap'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php endwhile; ?>
                            <?php if($total_users > 4): ?>
                            <div class="admin-avatar-item more">
                                +<?= $total_users - 4 ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <p class="admin-count">Total <?= $total_users ?> administrator</p>
                    <?php else: ?>
                        <div class="empty-state small">
                            <i class="fas fa-users-cog"></i>
                            <p>Belum ada admin</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS - AKSI CEPAT -->
    <div class="quick-actions">
        <h3><i class="fas fa-bolt"></i> Aksi Cepat</h3>
        <div class="actions-grid">
            <a href="pengumuman/tambah.php" class="action-item">
                <i class="fas fa-bullhorn"></i>
                <span>Pengumuman</span>
                <small>Tambah baru</small>
            </a>
            <a href="agenda/tambah.php" class="action-item">
                <i class="fas fa-calendar-plus"></i>
                <span>Agenda</span>
                <small>Buat agenda</small>
            </a>
            <a href="prestasi/tambah.php" class="action-item">
                <i class="fas fa-trophy"></i>
                <span>Prestasi</span>
                <small>Tambah prestasi</small>
            </a>
            <a href="galeri_foto/tambah.php" class="action-item">
                <i class="fas fa-camera"></i>
                <span>Upload Foto</span>
                <small>Tambah ke galeri</small>
            </a>
            <a href="hero_slider/tambah.php" class="action-item">
                <i class="fas fa-sliders-h"></i>
                <span>Hero Slider</span>
                <small>Tambah slide</small>
            </a>
            <a href="guru_staff/tambah.php" class="action-item">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Guru/Staff</span>
                <small>Tambah data</small>
            </a>
            <a href="ekstrakurikuler/tambah.php" class="action-item">
                <i class="fas fa-futbol"></i>
                <span>Ekstra</span>
                <small>Tambah kegiatan</small>
            </a>
            <a href="users/tambah.php" class="action-item">
                <i class="fas fa-user-plus"></i>
                <span>Admin</span>
                <small>Tambah admin</small>
            </a>
        </div>
    </div>

    <!-- TIPS & TRICKS / INFORMASI TAMBAHAN -->
    <div class="tips-section">
        <div class="tip-card">
            <i class="fas fa-lightbulb"></i>
            <div class="tip-content">
                <h4>Tips Cepat</h4>
                <p>Gunakan menu <strong>Quick Actions</strong> untuk menambah konten dengan cepat tanpa harus membuka menu lengkap.</p>
            </div>
        </div>
        <div class="tip-card">
            <i class="fas fa-info-circle"></i>
            <div class="tip-content">
                <h4>Informasi</h4>
                <p>Pastikan data kontak dan PPDB selalu diperbarui menjelang tahun ajaran baru.</p>
            </div>
        </div>
    </div>

</div> <!-- Tutup content-wrapper -->

<?php include "includes/footer.php"; ?>