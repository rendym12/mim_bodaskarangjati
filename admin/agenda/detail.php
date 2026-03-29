<?php
require_once dirname(__DIR__) . '/includes/auth.php';

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM agenda WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    header("Location: index.php");
    exit;
}

require_once dirname(__DIR__) . '/includes/header.php';

$today = date('Y-m-d');
if ($row['tanggal_mulai'] > $today) {
    $status = '<span class="badge badge-info">Akan Datang</span>';
    $status_text = "Akan Datang";
} elseif ($row['tanggal_mulai'] <= $today && $row['tanggal_selesai'] >= $today) {
    $status = '<span class="badge badge-success">Sedang Berlangsung</span>';
    $status_text = "Sedang Berlangsung";
} else {
    $status = '<span class="badge badge-secondary">Selesai</span>';
    $status_text = "Selesai";
}
?>

<div class="content-wrapper agenda-page">
    <div class="content-header">
        <h1><i class="fas fa-calendar-alt"></i> Detail Agenda</h1>
        <div>
            <a href="edit.php?id=<?= $id ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <i class="fas fa-calendar-check"></i>
            <h2><?= htmlspecialchars($row['nama_agenda']) ?></h2>
        </div>
        
        <div class="detail-body">
            <!-- Meta Information -->
            <div class="detail-meta">
                <div class="detail-meta-item">
                    <i class="fas fa-calendar"></i>
                    <strong>Tanggal Mulai:</strong> <?= date('d F Y', strtotime($row['tanggal_mulai'])) ?>
                </div>
                <?php if ($row['tanggal_selesai']): ?>
                <div class="detail-meta-item">
                    <i class="fas fa-calendar"></i>
                    <strong>Tanggal Selesai:</strong> <?= date('d F Y', strtotime($row['tanggal_selesai'])) ?>
                </div>
                <?php endif; ?>
                <div class="detail-meta-item">
                    <i class="fas fa-info-circle"></i>
                    <strong>Status:</strong> <?= $status ?>
                </div>
            </div>
            
            <!-- Detail Information -->
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-map-marker-alt"></i> Lokasi
                </div>
                <div class="detail-value">
                    <?= htmlspecialchars($row['lokasi'] ?? '-') ?>
                </div>
            </div>
            
            <?php if (!empty($row['deskripsi'])): ?>
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-align-left"></i> Deskripsi
                </div>
                <div class="detail-value">
                    <?= nl2br(htmlspecialchars($row['deskripsi'])) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer Actions dengan TOMBOL HAPUS -->
        <div class="detail-footer">
            <a href="edit.php?id=<?= $id ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="#" onclick="return confirmDelete(<?= $id ?>, '<?= htmlspecialchars($row['nama_agenda']) ?>', 'agenda')" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus
            </a>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>