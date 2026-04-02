<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM galeri_video WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data video tidak ditemukan";
    header("Location: index.php");
    exit;
}

// Extract YouTube ID
$video_id = '';
$youtube_url = $row['url_video'];
if (strpos($youtube_url, 'youtube.com') !== false || strpos($youtube_url, 'youtu.be') !== false) {
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtube_url, $matches);
    $video_id = $matches[1] ?? '';
}

include "../includes/header.php";
?>

<div class="content-wrapper galeri-video-page">
    <div class="content-header">
        <h1><i class="fas fa-video"></i> Detail Video</h1>
        <a href="index.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <i class="fas fa-video"></i>
            <h2><?= htmlspecialchars($row['judul']) ?></h2>
        </div>
        
        <div class="detail-body">
            <div class="detail-info-grid">
                <div class="detail-info-card">
                    <i class="fab fa-youtube"></i>
                    <div class="detail-info-content">
                        <span class="info-label">URL Video</span>
                        <span class="info-value">
                            <a href="<?= htmlspecialchars($row['url_video']) ?>" target="_blank" style="color: var(--primary);">
                                <?= htmlspecialchars($row['url_video']) ?>
                            </a>
                        </span>
                    </div>
                </div>
                <div class="detail-info-card">
                    <i class="fas fa-sort-numeric-down"></i>
                    <div class="detail-info-content">
                        <span class="info-label">Urutan</span>
                        <span class="info-value"><?= $row['urutan'] ?? '0' ?></span>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($video_id)): ?>
            <div class="detail-gambar">
                <iframe width="100%" height="400" src="https://www.youtube.com/embed/<?= $video_id ?>" frameborder="0" allowfullscreen style="border-radius: 12px;"></iframe>
            </div>
            <?php elseif (!empty($row['thumbnail'])): ?>
            <div class="detail-gambar">
                <img src="../../uploads/galeri_video/<?= $row['thumbnail'] ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
            </div>
            <?php endif; ?>
            
            <div class="detail-deskripsi">
                <?= nl2br(htmlspecialchars($row['keterangan'] ?? '-')) ?>
            </div>
        </div>
        
        <div class="detail-footer">
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <button type="button" 
                    class="btn-danger" 
                    data-id="<?= $id ?>" 
                    data-name="<?= htmlspecialchars($row['judul']) ?>"
                    data-module="video"
                    data-has-thumbnail="<?= (!empty($row['thumbnail']) ? 'true' : 'false') ?>">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i> Konfirmasi Hapus</h3>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus data berikut?</p>
            <p style="font-weight: bold; font-size: 1.1rem; margin: 10px 0;" id="deleteItemName"></p>
            <div id="fileWarningContainer" style="display: none;">
                <div style="color: #ef4444; background: #fee2e2; padding: 12px; border-radius: 5px; margin-bottom: 10px;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="fileWarningText"></span>
                </div>
            </div>
            <div style="color: #ef4444; background: #fee2e2; padding: 8px; border-radius: 5px;">
                <i class="fas fa-exclamation-circle"></i>
                Data yang sudah dihapus tidak dapat dikembalikan!
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" id="confirmDeleteBtn" class="btn-danger">Ya, Hapus</a>
            <button type="button" id="btnCloseModal" class="btn-secondary">Batal</button>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>