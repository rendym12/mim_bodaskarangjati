<?php
// Perbaiki path include - naik 1 level ke root folder
require_once '../includes/config.php';
require_once '../includes/db.php'; // Pastikan koneksi DB tersedia
include '../includes/header.php';

// Filter status
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
$today = date('Y-m-d');

// Query berdasarkan filter
if ($filter == 'akan-datang') {
    $query = mysqli_query($conn, "SELECT * FROM agenda WHERE tanggal_mulai > '$today' ORDER BY tanggal_mulai ASC");
} elseif ($filter == 'berlangsung') {
    $query = mysqli_query($conn, "SELECT * FROM agenda WHERE tanggal_mulai <= '$today' AND tanggal_selesai >= '$today' ORDER BY tanggal_mulai ASC");
} elseif ($filter == 'selesai') {
    $query = mysqli_query($conn, "SELECT * FROM agenda WHERE tanggal_selesai < '$today' ORDER BY tanggal_mulai DESC");
} else {
    $query = mysqli_query($conn, "SELECT * FROM agenda ORDER BY 
        CASE 
            WHEN tanggal_mulai > '$today' THEN 1
            WHEN tanggal_mulai <= '$today' AND tanggal_selesai >= '$today' THEN 2
            ELSE 3
        END, tanggal_mulai ASC");
}

// Hitung jumlah masing-masing status
$count_akan_datang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM agenda WHERE tanggal_mulai > '$today'"))['total'] ?? 0;
$count_berlangsung = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM agenda WHERE tanggal_mulai <= '$today' AND tanggal_selesai >= '$today'"))['total'] ?? 0;
$count_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM agenda WHERE tanggal_selesai < '$today'"))['total'] ?? 0;
$count_semua = $count_akan_datang + $count_berlangsung + $count_selesai;
?>

<div class="page-header">
    <div class="container">
        <h1>Agenda Madrasah</h1>
        <p>Jadwal kegiatan dan acara MI Muhammadiyah Bodaskarangjati</p>
    </div>
</div>

<div class="container">
    <div class="agenda-stats">
        <div class="stat-card <?= $filter == 'semua' ? 'active' : '' ?>" onclick="window.location.href='?filter=semua'">
            <div class="stat-icon">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-content">
                <span class="stat-number"><?= $count_semua ?></span>
                <span class="stat-label">Semua Agenda</span>
            </div>
        </div>
        
        <div class="stat-card <?= $filter == 'akan-datang' ? 'active' : '' ?>" onclick="window.location.href='?filter=akan-datang'">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <span class="stat-number"><?= $count_akan_datang ?></span>
                <span class="stat-label">Akan Datang</span>
            </div>
        </div>
        
        <div class="stat-card <?= $filter == 'berlangsung' ? 'active' : '' ?>" onclick="window.location.href='?filter=berlangsung'">
            <div class="stat-icon">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-content">
                <span class="stat-number"><?= $count_berlangsung ?></span>
                <span class="stat-label">Sedang Berlangsung</span>
            </div>
        </div>
        
        <div class="stat-card <?= $filter == 'selesai' ? 'active' : '' ?>" onclick="window.location.href='?filter=selesai'">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <span class="stat-number"><?= $count_selesai ?></span>
                <span class="stat-label">Selesai</span>
            </div>
        </div>
    </div>
    
    <?php if ($query && mysqli_num_rows($query) > 0): ?>
        <div class="agenda-grid">
            <?php while ($row = mysqli_fetch_assoc($query)): 
                $today_check = date('Y-m-d');
                if ($row['tanggal_mulai'] > $today_check) {
                    $status_class = 'akan-datang';
                    $status_text = 'Akan Datang';
                } elseif ($row['tanggal_mulai'] <= $today_check && $row['tanggal_selesai'] >= $today_check) {
                    $status_class = 'berlangsung';
                    $status_text = 'Sedang Berlangsung';
                } else {
                    $status_class = 'selesai';
                    $status_text = 'Selesai';
                }
                
                $tgl_mulai = date('j M Y', strtotime($row['tanggal_mulai']));
                $tgl_selesai = $row['tanggal_selesai'] ? date('j M Y', strtotime($row['tanggal_selesai'])) : null;
                $hari = date('d', strtotime($row['tanggal_mulai']));
                $bulan = date('M', strtotime($row['tanggal_mulai']));
            ?>
            <div class="agenda-card <?= $status_class ?>">
                <div class="agenda-date-badge">
                    <span class="date-day"><?= $hari ?></span>
                    <span class="date-month"><?= $bulan ?></span>
                </div>
                
                <div class="agenda-content">
                    <div class="agenda-status">
                        <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                    </div>
                    
                    <h3 class="agenda-judul">
                        <a href="agenda_detail.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_agenda']) ?></a>
                    </h3>
                    
                    <div class="agenda-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>
                                <?= $tgl_mulai ?>
                                <?php if ($tgl_selesai && $tgl_selesai != $tgl_mulai): ?>
                                    - <?= $tgl_selesai ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <?php if (!empty($row['lokasi'])): ?>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars($row['lokasi']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($row['deskripsi'])): ?>
                    <p class="agenda-deskripsi">
                        <?= htmlspecialchars(substr(strip_tags($row['deskripsi']), 0, 120)) ?>...
                    </p>
                    <?php endif; ?>
                    
                    <div class="agenda-footer">
                        <a href="agenda_detail.php?id=<?= $row['id'] ?>" class="btn-detail">
                            Lihat Detail <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <p>Tidak ada agenda untuk kategori ini</p>
            <a href="?filter=semua" class="btn-back">Lihat Semua Agenda</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>