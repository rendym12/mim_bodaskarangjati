<?php
// Perbaiki path include - naik 1 level ke root folder
require_once '../includes/config.php';
require_once '../includes/db.php';
include '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = mysqli_query($conn, "SELECT * FROM agenda WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    header("Location: agenda.php");
    exit;
}

$today = date('Y-m-d');
if ($row['tanggal_mulai'] > $today) {
    $status_class = 'akan-datang';
    $status_text = 'Akan Datang';
} elseif ($row['tanggal_mulai'] <= $today && $row['tanggal_selesai'] >= $today) {
    $status_class = 'berlangsung';
    $status_text = 'Sedang Berlangsung';
} else {
    $status_class = 'selesai';
    $status_text = 'Selesai';
}

$tgl_mulai = date('d F Y', strtotime($row['tanggal_mulai']));
$tgl_selesai = $row['tanggal_selesai'] ? date('d F Y', strtotime($row['tanggal_selesai'])) : null;

$related_query = mysqli_query($conn, "SELECT * FROM agenda WHERE id != $id ORDER BY 
    CASE 
        WHEN tanggal_mulai > '$today' THEN 1
        WHEN tanggal_mulai <= '$today' AND tanggal_selesai >= '$today' THEN 2
        ELSE 3
    END, tanggal_mulai ASC LIMIT 3");
?>

<div class="page-header page-header-small">
    <div class="container">
        <h1><?= htmlspecialchars($row['nama_agenda']) ?></h1>
        <div class="header-meta">
            <span class="status-badge-large <?= $status_class ?>"><?= $status_text ?></span>
        </div>
    </div>
</div>

<div class="container">
    <div class="detail-wrapper">
        <div class="detail-content">
            <div class="detail-header">
                <div class="detail-date">
                    <div class="date-box">
                        <span class="date-day"><?= date('d', strtotime($row['tanggal_mulai'])) ?></span>
                        <span class="date-month"><?= date('F', strtotime($row['tanggal_mulai'])) ?></span>
                        <span class="date-year"><?= date('Y', strtotime($row['tanggal_mulai'])) ?></span>
                    </div>
                    
                    <?php if ($tgl_selesai && $tgl_selesai != $tgl_mulai): ?>
                    <div class="date-separator">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <div class="date-box">
                        <span class="date-day"><?= date('d', strtotime($row['tanggal_selesai'])) ?></span>
                        <span class="date-month"><?= date('F', strtotime($row['tanggal_selesai'])) ?></span>
                        <span class="date-year"><?= date('Y', strtotime($row['tanggal_selesai'])) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="detail-info-grid">
                <?php if (!empty($row['lokasi'])): ?>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="info-content">
                        <h4>Lokasi</h4>
                        <p><?= htmlspecialchars($row['lokasi']) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-clock"></i></div>
                    <div class="info-content">
                        <h4>Waktu</h4>
                        <p><?= $tgl_mulai ?><?php if ($tgl_selesai && $tgl_selesai != $tgl_mulai): ?><br>s/d <?= $tgl_selesai ?><?php endif; ?></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-tag"></i></div>
                    <div class="info-content">
                        <h4>Status</h4>
                        <p><span class="status-badge-small <?= $status_class ?>"><?= $status_text ?></span></p>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($row['deskripsi'])): ?>
            <div class="detail-deskripsi">
                <h3><i class="fas fa-align-left"></i> Deskripsi Kegiatan</h3>
                <div class="deskripsi-content">
                    <?= nl2br(htmlspecialchars($row['deskripsi'])) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="detail-share">
                <h4>Bagikan:</h4>
                <div class="share-buttons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL . '/berita/agenda_detail.php?id=' . $id) ?>" target="_blank" class="share-btn facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode(BASE_URL . '/berita/agenda_detail.php?id=' . $id) ?>&text=<?= urlencode($row['nama_agenda']) ?>" target="_blank" class="share-btn twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://wa.me/?text=<?= urlencode($row['nama_agenda'] . ' - ' . BASE_URL . '/berita/agenda_detail.php?id=' . $id) ?>" target="_blank" class="share-btn whatsapp"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        
        <div class="detail-sidebar">
            <div class="sidebar-card">
                <h3><i class="fas fa-info-circle"></i> Informasi</h3>
                <ul class="info-list">
                    <li><i class="fas fa-calendar"></i> <span>Mulai: <?= date('d F Y', strtotime($row['tanggal_mulai'])) ?></span></li>
                    <?php if ($row['tanggal_selesai']): ?>
                    <li><i class="fas fa-calendar-check"></i> <span>Selesai: <?= date('d F Y', strtotime($row['tanggal_selesai'])) ?></span></li>
                    <?php endif; ?>
                    <li><i class="fas fa-clock"></i> <span>Status: <span class="status-badge-small <?= $status_class ?>"><?= $status_text ?></span></span></li>
                </ul>
            </div>
            
            <?php if (mysqli_num_rows($related_query) > 0): ?>
            <div class="sidebar-card">
                <h3><i class="fas fa-calendar-alt"></i> Agenda Lainnya</h3>
                <ul class="related-list">
                    <?php while ($related = mysqli_fetch_assoc($related_query)): 
                        $rel_check = date('Y-m-d');
                        $rel_status = ($related['tanggal_mulai'] > $rel_check) ? 'akan-datang' : (($related['tanggal_mulai'] <= $rel_check && $related['tanggal_selesai'] >= $rel_check) ? 'berlangsung' : 'selesai');
                    ?>
                    <li>
                        <a href="agenda_detail.php?id=<?= $related['id'] ?>">
                            <div class="related-date">
                                <span class="related-day"><?= date('d', strtotime($related['tanggal_mulai'])) ?></span>
                                <span class="related-month"><?= date('M', strtotime($related['tanggal_mulai'])) ?></span>
                            </div>
                            <div class="related-info">
                                <span class="related-title"><?= htmlspecialchars($related['nama_agenda']) ?></span>
                                <span class="related-location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($related['lokasi'] ?? 'Madrasah') ?></span>
                            </div>
                        </a>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <a href="agenda.php" class="btn-kembali"><i class="fas fa-arrow-left"></i> Kembali ke Agenda</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>