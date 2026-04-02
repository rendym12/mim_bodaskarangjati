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
// DATA UNTUK GRAFIK KONTEN (6 BULAN TERAKHIR)
// ==============================================
$bulan_labels = [];
$konten_data = [];

for ($i = 5; $i >= 0; $i--) {
    $bulan = date('Y-m', strtotime("-$i months"));
    $bulan_labels[] = date('M Y', strtotime("-$i months"));
    
    $pengumuman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'"))['total'] ?? 0;
    $agenda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM agenda WHERE DATE_FORMAT(tanggal_mulai, '%Y-%m') = '$bulan'"))['total'] ?? 0;
    $tahun = substr($bulan, 0, 4);
    $prestasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM prestasi WHERE tahun = '$tahun'"))['total'] ?? 0;
    
    $konten_data[] = $pengumuman + $agenda + $prestasi;
}

$total_konten_6_bulan = array_sum($konten_data);
$rata_rata_bulan = $total_konten_6_bulan > 0 ? round($total_konten_6_bulan / 6, 1) : 0;
$bulan_terbanyak_index = array_keys($konten_data, max($konten_data))[0] ?? 0;
$bulan_terbanyak = $bulan_labels[$bulan_terbanyak_index] ?? date('M Y');
$nilai_terbanyak = max($konten_data);

// ==============================================
// DATA UNTUK AKTIVITAS TERBARU
// ==============================================
$activities = [];

$pengumuman_list = mysqli_query($conn, "SELECT 'pengumuman' as type, judul as title, tanggal as date, 'fa-bullhorn' as icon, id FROM pengumuman ORDER BY tanggal DESC LIMIT 5");
while($row = mysqli_fetch_assoc($pengumuman_list)) {
    $row['link'] = "pengumuman/edit.php?id=" . $row['id'];
    $activities[] = $row;
}

$agenda_list = mysqli_query($conn, "SELECT 'agenda' as type, nama_agenda as title, tanggal_mulai as date, 'fa-calendar' as icon, id FROM agenda ORDER BY tanggal_mulai DESC LIMIT 5");
while($row = mysqli_fetch_assoc($agenda_list)) {
    $row['link'] = "agenda/edit.php?id=" . $row['id'];
    $activities[] = $row;
}

$prestasi_list = mysqli_query($conn, "SELECT 'prestasi' as type, nama_prestasi as title, tahun as date, 'fa-trophy' as icon, id FROM prestasi ORDER BY tahun DESC LIMIT 5");
while($row = mysqli_fetch_assoc($prestasi_list)) {
    $row['link'] = "prestasi/edit.php?id=" . $row['id'];
    $activities[] = $row;
}

usort($activities, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

$activities = array_slice($activities, 0, 10);

// ==============================================
// DATA TERBARU UNTUK CARD
// ==============================================
$recent_pengumuman = mysqli_query($conn, "SELECT * FROM pengumuman ORDER BY tanggal DESC LIMIT 5");
$upcoming_agenda = mysqli_query($conn, "SELECT * FROM agenda WHERE tanggal_mulai >= CURDATE() ORDER BY tanggal_mulai ASC LIMIT 5");
$recent_prestasi = mysqli_query($conn, "SELECT * FROM prestasi ORDER BY tahun DESC LIMIT 5");
$recent_galeri = mysqli_query($conn, "SELECT * FROM galeri_foto ORDER BY id DESC LIMIT 4");
?>

<!-- CONTENT WRAPPER -->
<div class="content-wrapper">
    
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

    <!-- DUA KOLOM: GRAFIK & AKTIVITAS TERBARU -->
    <div class="dashboard-two-col">
        
        <!-- KOLOM KIRI: GRAFIK TREN KONTEN -->
        <div class="left-col">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line"></i> Tren Konten 6 Bulan Terakhir</h3>
                    <span class="badge"><?= $total_konten_6_bulan ?> total konten</span>
                </div>
                <div class="card-body">
                    <canvas id="contentTrendChart" height="250"></canvas>
                    
                    <div class="chart-stats">
                        <div class="chart-stat-item">
                            <span class="stat-label">Rata-rata per bulan</span>
                            <span class="stat-value"><?= $rata_rata_bulan ?> konten</span>
                        </div>
                        <div class="chart-stat-item">
                            <span class="stat-label">Bulan tertinggi</span>
                            <span class="stat-value"><?= $bulan_terbanyak ?> (<?= $nilai_terbanyak ?> konten)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- KOLOM KANAN: AKTIVITAS TERBARU -->
        <div class="right-col">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-clock"></i> Aktivitas Terbaru</h3>
                </div>
                <div class="card-body">
                    <div class="activity-timeline" id="activityTimeline">
                        <?php if (count($activities) > 0): ?>
                            <?php foreach($activities as $act): 
                                $date_diff = floor((strtotime(date('Y-m-d')) - strtotime($act['date'])) / (60 * 60 * 24));
                                if ($date_diff == 0) {
                                    $time_ago = "Hari ini";
                                } elseif ($date_diff == 1) {
                                    $time_ago = "Kemarin";
                                } elseif ($date_diff <= 7) {
                                    $time_ago = $date_diff . " hari lalu";
                                } else {
                                    $time_ago = date('d M Y', strtotime($act['date']));
                                }
                                
                                $type_class = '';
                                if ($act['type'] == 'pengumuman') $type_class = 'pengumuman';
                                if ($act['type'] == 'agenda') $type_class = 'agenda';
                                if ($act['type'] == 'prestasi') $type_class = 'prestasi';
                            ?>
                            <div class="timeline-item">
                                <div class="timeline-icon <?= $type_class ?>">
                                    <i class="fas <?= $act['icon'] ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <a href="<?= $act['link'] ?>" class="timeline-title">
                                        <?= htmlspecialchars($act['title']) ?>
                                    </a>
                                    <div class="timeline-meta">
                                        <span class="timeline-type <?= $type_class ?>"><?= ucfirst($act['type']) ?></span>
                                        <span class="timeline-date"><i class="far fa-calendar-alt"></i> <?= $time_ago ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Belum ada aktivitas</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- INFO PANELS - STATUS PENTING -->
    <div class="info-panels-grid">
        <div class="info-panel <?= $ppdb_status ?>">
            <div class="info-panel-icon bg-primary">
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

    <!-- DASHBOARD GRID - 2 KOLOM UTAMA -->
    <div class="dashboard-grid">
        
        <div class="grid-col main-col">
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
        
        <div class="grid-col">
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

</div>

<!-- Chart.js untuk Grafik -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php include "includes/footer.php"; ?>