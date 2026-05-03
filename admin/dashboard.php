<?php
include "includes/header.php";

// ==============================================
// AMBIL DATA ADMIN UNTUK PROFIL
// ==============================================
$admin_id = $_SESSION['admin_id'];
$admin_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM admin_users WHERE id = $admin_id"));
$admin_foto = !empty($admin_data['foto']) && $admin_data['foto'] != 'default-avatar.jpg' ? $admin_data['foto'] : 'default-avatar.jpg';
$admin_nama = $admin_data['nama_lengkap'] ?? $_SESSION['admin_nama'] ?? 'Admin';

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
// STATISTIK UTAMA - SEMUA TABEL (LENGKAP)
// ==============================================

// === MASTER DATA (Frontend) ===
$total_pengumuman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman"))['total'] ?? 0;
$total_agenda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM agenda"))['total'] ?? 0;
$total_guru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM guru_staff"))['total'] ?? 0;
$total_ekstra = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ekstrakurikuler"))['total'] ?? 0;
$total_prestasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM prestasi"))['total'] ?? 0;
$total_sarana = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM sarana"))['total'] ?? 0;
$total_slider = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hero_slider WHERE status='aktif'"))['total'] ?? 0;
$total_slider_all = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hero_slider"))['total'] ?? 0;

// === DATA SISWA ===
$bulan_sekarang = (int) date('n');
$tahun_sekarang = (int) date('Y');
if ($bulan_sekarang >= 7) {
    $tahun_ajaran_aktif = $tahun_sekarang . '/' . ($tahun_sekarang + 1);
} else {
    $tahun_ajaran_aktif = ($tahun_sekarang - 1) . '/' . $tahun_sekarang;
}

if (isset($_GET['tahun']) && !empty($_GET['tahun'])) {
    $tahun_ajaran_aktif = mysqli_real_escape_string($conn, $_GET['tahun']);
}

$query_total = mysqli_query($conn, "SELECT SUM(total) as total_siswa FROM data_siswa WHERE tahun_ajaran = '$tahun_ajaran_aktif'");
$total_siswa = mysqli_fetch_assoc($query_total)['total_siswa'] ?? 0;
$total_siswa_all = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) as total_siswa FROM data_siswa"))['total_siswa'] ?? 0;

$query_kelas = mysqli_query($conn, "SELECT COUNT(DISTINCT kelas) as total_kelas FROM data_siswa WHERE tahun_ajaran = '$tahun_ajaran_aktif' AND (laki_laki > 0 OR perempuan > 0)");
$total_kelas_terisi = mysqli_fetch_assoc($query_kelas)['total_kelas'] ?? 0;

// === DATA SISWA PER KELAS (Untuk statistik tambahan) ===
$siswa_laki = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(laki_laki) as total FROM data_siswa WHERE tahun_ajaran = '$tahun_ajaran_aktif'"))['total'] ?? 0;
$siswa_perempuan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(perempuan) as total FROM data_siswa WHERE tahun_ajaran = '$tahun_ajaran_aktif'"))['total'] ?? 0;

// === TESTIMONI ===
$total_testimoni_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimoni WHERE status = 'pending' AND is_spam = 0"))['total'] ?? 0;
$total_testimoni_approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimoni WHERE status = 'approved' AND is_spam = 0"))['total'] ?? 0;
$total_testimoni_rejected = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimoni WHERE status = 'rejected' AND is_spam = 0"))['total'] ?? 0;
$total_testimoni_spam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimoni WHERE is_spam = 1"))['total'] ?? 0;
$total_testimoni_all = $total_testimoni_pending + $total_testimoni_approved + $total_testimoni_rejected + $total_testimoni_spam;

// === GALERI ===
$total_foto = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM galeri_foto"))['total'] ?? 0;
$total_video = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM galeri_video"))['total'] ?? 0;

// === KONFIGURASI ===
$ppdb_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM ppdb LIMIT 1"));
$kontak_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kontak LIMIT 1"));
$visi_misi_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM visi_misi LIMIT 1"));
$sejarah_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM sejarah LIMIT 1"));
$sambutan_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM sambutan LIMIT 1"));

// === USERS ===
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM admin_users"))['total'] ?? 0;

// === DATA UNTUK GRAFIK DONAT 5 MENU ===
$menu_dinamis = [
    'Pengumuman' => $total_pengumuman,
    'Agenda' => $total_agenda,
    'Prestasi' => $total_prestasi,
    'Galeri Foto' => $total_foto,
    'Galeri Video' => $total_video
];

$total_konten_dinamis = array_sum($menu_dinamis);

// === LAST UPDATE SEMUA MENU ===
$last_updates = [];

$tables = [
    'pengumuman' => ['label' => '📰 Pengumuman', 'date_col' => 'tanggal', 'icon' => 'fa-bullhorn'],
    'agenda' => ['label' => '📅 Agenda', 'date_col' => 'created_at', 'icon' => 'fa-calendar'],
    'guru_staff' => ['label' => '👨‍🏫 Guru & Staff', 'date_col' => 'created_at', 'icon' => 'fa-chalkboard-teacher'],
    'ekstrakurikuler' => ['label' => '⚽ Ekstrakurikuler', 'date_col' => 'created_at', 'icon' => 'fa-futbol'],
    'prestasi' => ['label' => '🏆 Prestasi', 'date_col' => 'created_at', 'icon' => 'fa-trophy'],
    'sarana' => ['label' => '🏢 Sarana', 'date_col' => 'created_at', 'icon' => 'fa-building'],
    'galeri_foto' => ['label' => '📸 Galeri Foto', 'date_col' => 'created_at', 'icon' => 'fa-images'],
    'galeri_video' => ['label' => '🎥 Galeri Video', 'date_col' => 'created_at', 'icon' => 'fa-video'],
    'testimoni' => ['label' => '⭐ Testimoni', 'date_col' => 'created_at', 'icon' => 'fa-star'],
    'hero_slider' => ['label' => '🎠 Hero Slider', 'date_col' => 'created_at', 'icon' => 'fa-sliders-h'],
    'data_siswa' => ['label' => '📊 Data Siswa', 'date_col' => 'updated_at', 'icon' => 'fa-users'],
    'ppdb' => ['label' => '🎓 PPDB', 'date_col' => 'updated_at', 'icon' => 'fa-graduation-cap'],
    'kontak' => ['label' => '📞 Kontak', 'date_col' => 'updated_at', 'icon' => 'fa-address-book'],
    'visi_misi' => ['label' => '👁️ Visi Misi', 'date_col' => 'updated_at', 'icon' => 'fa-eye'],
    'sejarah' => ['label' => '📜 Sejarah', 'date_col' => 'updated_at', 'icon' => 'fa-history'],
    'sambutan' => ['label' => '🎤 Sambutan', 'date_col' => 'updated_at', 'icon' => 'fa-microphone']
];

foreach ($tables as $table => $info) {
    // Cek apakah tabel ada
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($check_table) > 0) {
        $query = mysqli_query($conn, "SELECT MAX($info[date_col]) as last_update FROM $table");
        if ($query) {
            $result = mysqli_fetch_assoc($query);
            $last_update = $result['last_update'];
            
            if ($last_update && $last_update != '0000-00-00 00:00:00') {
                $diff = floor((time() - strtotime($last_update)) / (60 * 60 * 24));
                if ($diff == 0) $time_text = "Hari ini";
                elseif ($diff == 1) $time_text = "Kemarin";
                elseif ($diff < 7) $time_text = "$diff hari lalu";
                elseif ($diff < 30) $time_text = floor($diff/7) . " minggu lalu";
                else $time_text = date('d M Y', strtotime($last_update));
                
                $last_updates[] = [
                    'label' => $info['label'],
                    'icon' => $info['icon'],
                    'date' => $last_update,
                    'time_text' => $time_text,
                    'diff' => $diff
                ];
            }
        }
    }
}

usort($last_updates, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// === DATA UNTUK CARD LAINNYA ===
$today = date('Y-m-d');
$agenda_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM agenda WHERE tanggal_mulai <= '$today' AND tanggal_selesai >= '$today'"))['total'] ?? 0;
$pengumuman_bulan_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman WHERE DATE_FORMAT(tanggal, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')"))['total'] ?? 0;

// === PPDB Status ===
$ppdb_status = $ppdb_data ? $ppdb_data['status'] : 'nonaktif';
$ppdb_tahun = $ppdb_data ? $ppdb_data['tahun_ajaran'] : date('Y') . '/' . (date('Y')+1);

// ==============================================
// DATA UNTUK GRAFIK DONAT 5 MENU + VIDEO
// ==============================================
$menu_dinamis = [
    'Pengumuman' => $total_pengumuman,
    'Agenda' => $total_agenda,
    'Prestasi' => $total_prestasi,
    'Galeri Foto' => $total_foto,
    'Galeri Video' => $total_video
];

$warna_dinamis = ['#0B3D91', '#28a745', '#FFD700', '#17a2b8', '#fd7e14'];
$total_konten_dinamis = array_sum($menu_dinamis);

// Cari menu terbanyak
arsort($menu_dinamis);
$terbanyak = key($menu_dinamis);
$jumlah_terbanyak = reset($menu_dinamis);

// ==============================================
// LAST UPDATE SEMUA MENU
// ==============================================
$last_updates = [];

$tables = [
    'pengumuman' => ['label' => '📰 Pengumuman', 'date_col' => 'tanggal', 'icon' => 'fa-bullhorn'],
    'agenda' => ['label' => '📅 Agenda', 'date_col' => 'created_at', 'icon' => 'fa-calendar'],
    'guru_staff' => ['label' => '👨‍🏫 Guru & Staff', 'date_col' => 'created_at', 'icon' => 'fa-chalkboard-teacher'],
    'ekstrakurikuler' => ['label' => '⚽ Ekstrakurikuler', 'date_col' => 'created_at', 'icon' => 'fa-futbol'],
    'prestasi' => ['label' => '🏆 Prestasi', 'date_col' => 'created_at', 'icon' => 'fa-trophy'],
    'sarana' => ['label' => '🏢 Sarana', 'date_col' => 'created_at', 'icon' => 'fa-building'],
    'galeri_foto' => ['label' => '📸 Galeri Foto', 'date_col' => 'created_at', 'icon' => 'fa-images'],
    'galeri_video' => ['label' => '🎥 Galeri Video', 'date_col' => 'created_at', 'icon' => 'fa-video'],
    'testimoni' => ['label' => '⭐ Testimoni', 'date_col' => 'created_at', 'icon' => 'fa-star'],
    'hero_slider' => ['label' => '🎠 Hero Slider', 'date_col' => 'created_at', 'icon' => 'fa-sliders-h']
];

foreach ($tables as $table => $info) {
    $query = mysqli_query($conn, "SELECT MAX($info[date_col]) as last_update FROM $table");
    $result = mysqli_fetch_assoc($query);
    $last_update = $result['last_update'];
    
    if ($last_update) {
        $diff = floor((time() - strtotime($last_update)) / (60 * 60 * 24));
        if ($diff == 0) $time_text = "Hari ini";
        elseif ($diff == 1) $time_text = "Kemarin";
        elseif ($diff < 7) $time_text = "$diff hari lalu";
        elseif ($diff < 30) $time_text = floor($diff/7) . " minggu lalu";
        else $time_text = date('d M Y', strtotime($last_update));
        
        $last_updates[] = [
            'label' => $info['label'],
            'icon' => $info['icon'],
            'date' => $last_update,
            'time_text' => $time_text,
            'diff' => $diff
        ];
    }
}

usort($last_updates, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// ==============================================
// DATA UNTUK CARD LAINNYA
// ==============================================
$today = date('Y-m-d');
$agenda_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM agenda WHERE tanggal_mulai <= '$today' AND tanggal_selesai >= '$today'"))['total'] ?? 0;
$pengumuman_bulan_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman WHERE DATE_FORMAT(tanggal, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')"))['total'] ?? 0;

$recent_pengumuman = mysqli_query($conn, "SELECT * FROM pengumuman ORDER BY tanggal DESC LIMIT 5");
$upcoming_agenda = mysqli_query($conn, "SELECT * FROM agenda WHERE tanggal_mulai >= CURDATE() ORDER BY tanggal_mulai ASC LIMIT 5");
$recent_prestasi = mysqli_query($conn, "SELECT * FROM prestasi ORDER BY tahun DESC LIMIT 5");
$recent_galeri = mysqli_query($conn, "SELECT * FROM galeri_foto ORDER BY id DESC LIMIT 4");
$users = mysqli_query($conn, "SELECT id, nama_lengkap, foto FROM admin_users ORDER BY id LIMIT 4");

$ppdb_status = $ppdb_data ? $ppdb_data['status'] : 'nonaktif';
$ppdb_tahun = $ppdb_data ? $ppdb_data['tahun_ajaran'] : date('Y') . '/' . (date('Y')+1);
?>

<!-- CONTENT WRAPPER -->
<div class="content-wrapper">
    
    <!-- DASHBOARD HEADER -->
    <div class="dashboard-header">
        <div class="header-left">
            <div class="greeting-badge">
                <span class="greeting-emoji"><?= $emoji ?></span>
                <span class="greeting-text"><?= $greeting ?>,</span>
            </div>
            <h1><?= htmlspecialchars(explode(' ', $admin_nama)[0]) ?>! 👋</h1>
            <p><?= date('l, d F Y') ?> • Selamat bekerja</p>
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
                <a href="users/edit.php?id=<?= $admin_id ?>" class="dropdown-item">
                    <i class="fas fa-user-circle"></i> Profil Saya
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- STATISTIK GRID 6 KOLOM -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary"><i class="fas fa-bullhorn"></i></div>
            <div class="stat-content">
                <h3><?= number_format($total_pengumuman) ?></h3>
                <p>Pengumuman</p>
                <small class="stat-sub"><?= $pengumuman_bulan_ini ?> bulan ini</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-success"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-content">
                <h3><?= number_format($total_agenda) ?></h3>
                <p>Agenda</p>
                <small class="stat-sub"><?= $agenda_hari_ini ?> hari ini</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-warning"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-content">
                <h3><?= number_format($total_guru) ?></h3>
                <p>Guru & Staff</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-purple"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <h3><?= number_format($total_siswa) ?></h3>
                <p>Siswa Aktif</p>
                <small class="stat-sub">TA <?= $tahun_ajaran_aktif ?></small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-info"><i class="fas fa-trophy"></i></div>
            <div class="stat-content">
                <h3><?= number_format($total_prestasi) ?></h3>
                <p>Prestasi</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-danger"><i class="fas fa-images"></i></div>
            <div class="stat-content">
                <h3><?= number_format($total_foto) ?></h3>
                <p>Galeri Foto</p>
            </div>
        </div>
    </div>

    <!-- GRAFIK DONAT 5 MENU + VIDEO -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-pie"></i> Komposisi Konten Dinamis</h3>
            <span class="badge">Total <?= number_format($total_konten_dinamis) ?> konten</span>
        </div>
        <div class="card-body">
            <?php if ($total_konten_dinamis > 0): ?>
                <div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: center;">
                    <!-- GRAFIK DONAT -->
                    <div style="flex: 1; min-width: 220px;">
                        <canvas id="dynamicMenuChart" height="200"></canvas>
                    </div>
                    
                    <!-- LEGENDA -->
                    <div style="flex: 1; min-width: 200px;">
                        <table style="width: 100%; font-size: 13px;">
                            <?php 
                            $no = 0;
                            foreach($menu_dinamis as $nama => $jumlah): 
                            ?>
                            <tr>
                                <td style="padding: 6px 4px;">
                                    <span style="display: inline-block; width: 12px; height: 12px; background: <?= $warna_dinamis[$no] ?>; border-radius: 2px;"></span>
                                    <?= $nama ?>
                                </td>
                                <td style="text-align: right; padding: 6px 0;">
                                    <?= number_format($jumlah) ?> 
                                    <span style="color: #64748b; font-size: 11px;">(<?= round(($jumlah/$total_konten_dinamis)*100) ?>%)</span>
                                </td>
                            </tr>
                            <?php $no++; endforeach; ?>
                            <tr style="border-top: 1px solid #e2e8f0;">
                                <td style="padding: 10px 4px 4px;"><strong>TOTAL</strong></td>
                                <td style="text-align: right; padding: 10px 0 4px;"><strong><?= number_format($total_konten_dinamis) ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- INFORMASI TERBANYAK -->
                <div style="margin-top: 15px; padding: 10px; background: #e6f0ff; border-radius: 8px; font-size: 12px; text-align: center;">
                    <i class="fas fa-chart-line"></i> 
                    Konten terbanyak: <strong><?= $terbanyak ?></strong> (<?= number_format($jumlah_terbanyak) ?> data)
                </div>
            <?php else: ?>
                <div class="empty-state" style="text-align: center; padding: 40px;">
                    <i class="fas fa-chart-pie" style="font-size: 48px; color: #ccc;"></i>
                    <p>Belum ada konten dinamis</p>
                    <p style="font-size: 12px;">Mulai tambahkan pengumuman, agenda, prestasi, atau galeri</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- DUA KOLOM: LAST UPDATE & PENGUMUMAN -->
    <div class="dashboard-two-col">
        
        <!-- KOLOM KIRI: LAST UPDATE SEMUA MENU -->
        <div class="left-col">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-clock"></i> Last Update Konten</h3>
                    <a href="#" class="btn-sm" onclick="location.reload();"><i class="fas fa-sync-alt"></i> Refresh</a>
                </div>
                <div class="card-body">
                    <?php if (count($last_updates) > 0): ?>
                        <div class="last-update-list">
                            <?php foreach ($last_updates as $item): ?>
                            <div class="update-item">
                                <div class="update-icon"><i class="fas <?= $item['icon'] ?>"></i></div>
                                <div class="update-info">
                                    <span class="update-label"><?= $item['label'] ?></span>
                                    <span class="update-time"><?= $item['time_text'] ?></span>
                                </div>
                                <div class="update-status <?= $item['diff'] <= 7 ? 'recent' : 'old' ?>">
                                    <?= $item['diff'] <= 7 ? '🟢 Aktif' : '🟡' ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state" style="text-align: center; padding: 40px;">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #ccc;"></i>
                            <p>Belum ada konten</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- KOLOM KANAN: PENGUMUMAN TERBARU -->
        <div class="right-col">
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
                                    <div class="item-icon"><i class="fas fa-bullhorn"></i></div>
                                    <div class="item-content">
                                        <span class="item-title"><?= htmlspecialchars($row['judul']) ?></span>
                                        <span class="item-meta"><?= date('d M Y', strtotime($row['tanggal'])) ?></span>
                                    </div>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state" style="text-align: center; padding: 40px;">
                            <i class="fas fa-newspaper" style="font-size: 48px; color: #ccc;"></i>
                            <p>Belum ada pengumuman</p>
                            <a href="pengumuman/tambah.php" class="btn-sm">Buat Pengumuman</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
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
                                    <div class="item-icon agenda"><i class="fas fa-clock"></i></div>
                                    <div class="item-content">
                                        <span class="item-title"><?= htmlspecialchars($row['nama_agenda']) ?></span>
                                        <span class="item-meta">
                                            <?php if($selisih == 0): ?><span class="badge-today">Hari Ini</span>
                                            <?php elseif($selisih == 1): ?><span class="badge-tomorrow">Besok</span>
                                            <?php else: ?><?= date('d M', strtotime($row['tanggal_mulai'])) ?><?php endif; ?>
                                        </span>
                                    </div>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state" style="text-align: center; padding: 40px;">
                            <i class="fas fa-calendar" style="font-size: 48px; color: #ccc;"></i>
                            <p>Tidak ada agenda mendatang</p>
                            <a href="agenda/tambah.php" class="btn-sm">Buat Agenda</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- GALERI TERBARU -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-images"></i> Galeri Foto Terbaru</h3>
            <a href="galeri_foto/index.php" class="btn-sm">Lihat Semua <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body">
            <?php if (mysqli_num_rows($recent_galeri) > 0): ?>
                <div class="gallery-grid">
                    <?php while($row = mysqli_fetch_assoc($recent_galeri)): ?>
                    <a href="galeri_foto/detail.php?id=<?= $row['id'] ?>" class="gallery-item">
                        <img src="../uploads/galeri_foto/<?= $row['file_foto'] ?>" alt="Foto">
                        <div class="gallery-overlay"><i class="fas fa-search-plus"></i></div>
                    </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state" style="text-align: center; padding: 40px;">
                    <i class="fas fa-images" style="font-size: 48px; color: #ccc;"></i>
                    <p>Belum ada foto</p>
                    <a href="galeri_foto/tambah.php" class="btn-sm">Upload Foto</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- INFO PANELS GRID -->
    <div class="info-panels-grid">
        <div class="info-panel">
            <div class="info-panel-icon bg-primary"><i class="fas fa-graduation-cap"></i></div>
            <div class="info-panel-content">
                <h3>PPDB <?= $ppdb_tahun ?></h3>
                <div class="status-indicator">
                    <span class="status-dot <?= $ppdb_status ?>"></span>
                    <span class="status-text"><?= strtoupper($ppdb_status) ?></span>
                </div>
            </div>
            <div class="info-panel-badge">
                <a href="ppdb/index.php" class="badge-user"><i class="fas fa-cog"></i> Kelola</a>
            </div>
        </div>
        
        <div class="info-panel">
            <div class="info-panel-icon bg-purple"><i class="fas fa-users"></i></div>
            <div class="info-panel-content">
                <h3>Data Siswa</h3>
                <p><?= number_format($total_siswa) ?> siswa</p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $total_kelas_terisi > 0 ? ($total_kelas_terisi/6)*100 : 0 ?>%"></div>
                </div>
                <small><?= $total_kelas_terisi ?> dari 6 kelas terisi</small>
            </div>
            <div class="info-panel-badge">
                <a href="data_siswa/index.php" class="badge-user"><i class="fas fa-edit"></i> Kelola</a>
            </div>
        </div>
        
        <div class="info-panel">
            <div class="info-panel-icon bg-warning"><i class="fas fa-star"></i></div>
            <div class="info-panel-content">
                <h3>Testimoni</h3>
                <p><?= number_format($total_testimoni_all) ?> ulasan</p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $total_testimoni_all > 0 ? ($total_testimoni_approved/$total_testimoni_all)*100 : 0 ?>%"></div>
                </div>
                <small><?= $total_testimoni_approved ?> disetujui</small>
            </div>
            <div class="info-panel-badge">
                <a href="testimoni/index.php" class="badge-user"><i class="fas fa-edit"></i> Kelola</a>
            </div>
        </div>
        
        <div class="info-panel">
            <div class="info-panel-icon bg-info"><i class="fas fa-images"></i></div>
            <div class="info-panel-content">
                <h3>Hero Slider</h3>
                <p><?= $total_slider ?> dari <?= $total_slider_all ?> slide aktif</p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $total_slider_all > 0 ? ($total_slider/$total_slider_all)*100 : 0 ?>%"></div>
                </div>
            </div>
            <div class="info-panel-badge">
                <a href="hero_slider/index.php" class="badge-user"><i class="fas fa-edit"></i> Kelola</a>
            </div>
        </div>
        
        <div class="info-panel">
            <div class="info-panel-icon bg-success"><i class="fas fa-address-book"></i></div>
            <div class="info-panel-content">
                <h3>Informasi Kontak</h3>
                <p><?= $kontak_data ? '✓ Data tersedia' : '✗ Belum diisi' ?></p>
            </div>
            <div class="info-panel-badge">
                <a href="kontak/index.php" class="badge-user"><i class="fas fa-edit"></i> Edit</a>
            </div>
        </div>
    </div>

    <!-- ADMIN AKTIF -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-users-cog"></i> Admin Aktif</h3>
            <a href="users/index.php" class="btn-sm">Kelola <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body">
            <div class="admin-list">
                <?php while($row = mysqli_fetch_assoc($users)): ?>
                <div class="admin-item">
                    <?php if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg'): ?>
                        <img src="../uploads/<?= $row['foto'] ?>" class="admin-avatar">
                    <?php else: ?>
                        <div class="admin-avatar-placeholder"><?= strtoupper(substr($row['nama_lengkap'], 0, 1)) ?></div>
                    <?php endif; ?>
                    <div class="admin-info">
                        <div class="admin-name"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                        <div class="admin-role">Administrator</div>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php if($total_users > 4): ?>
                    <div class="admin-more">+<?= $total_users - 4 ?> admin lainnya</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="quick-actions">
        <h3><i class="fas fa-bolt"></i> Aksi Cepat</h3>
        <div class="actions-grid">
            <a href="pengumuman/tambah.php" class="action-item"><i class="fas fa-bullhorn"></i><span>Pengumuman</span></a>
            <a href="agenda/tambah.php" class="action-item"><i class="fas fa-calendar-plus"></i><span>Agenda</span></a>
            <a href="prestasi/tambah.php" class="action-item"><i class="fas fa-trophy"></i><span>Prestasi</span></a>
            <a href="galeri_foto/tambah.php" class="action-item"><i class="fas fa-camera"></i><span>Upload Foto</span></a>
            <a href="galeri_video/tambah.php" class="action-item"><i class="fas fa-video"></i><span>Upload Video</span></a>
            <a href="guru_staff/tambah.php" class="action-item"><i class="fas fa-chalkboard-teacher"></i><span>Guru/Staff</span></a>
            <a href="data_siswa/index.php" class="action-item"><i class="fas fa-users"></i><span>Data Siswa</span></a>
            <a href="testimoni/index.php" class="action-item"><i class="fas fa-star"></i><span>Testimoni</span></a>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php if ($total_konten_dinamis > 0): ?>
<script>
// Grafik Donat 5 Menu + Video
new Chart(document.getElementById('dynamicMenuChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_keys($menu_dinamis)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($menu_dinamis)) ?>,
            backgroundColor: <?= json_encode($warna_dinamis) ?>,
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: { 
                callbacks: { 
                    label: function(ctx) { 
                        return ctx.label + ': ' + ctx.raw.toLocaleString() + ' konten'; 
                    } 
                } 
            }
        }
    }
});
</script>
<?php endif; ?>

<style>
/* Last Update Styles */
.last-update-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.update-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    background: #f8fafc;
    border-radius: 10px;
    transition: all 0.2s;
}
.update-item:hover {
    background: #f1f5f9;
}
.update-icon {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 10px;
    color: #0B3D91;
}
.update-info {
    flex: 1;
}
.update-label {
    display: block;
    font-weight: 600;
    font-size: 13px;
    color: #1e293b;
}
.update-time {
    font-size: 11px;
    color: #64748b;
}
.update-status {
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 20px;
}
.update-status.recent {
    background: #d4edda;
    color: #155724;
}
.update-status.old {
    background: #fff3cd;
    color: #856404;
}

/* Gallery Grid */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}
.gallery-item {
    position: relative;
    aspect-ratio: 1/1;
    border-radius: 10px;
    overflow: hidden;
}
.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0