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
        <div class="action-buttons">
            <a href="edit.php?id=<?= $id ?>" class="btn-edit">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="index.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                <!-- Thumbnail / Video Embed -->
                <div style="flex: 0 0 400px;">
                    <?php if (!empty($video_id)): ?>
                        <iframe width="100%" height="225" src="https://www.youtube.com/embed/<?= $video_id ?>" frameborder="0" allowfullscreen style="border-radius: 12px;"></iframe>
                    <?php elseif (!empty($row['thumbnail'])): ?>
                        <img src="../../uploads/galeri_video/<?= $row['thumbnail'] ?>" 
                             alt="<?= htmlspecialchars($row['judul']) ?>" 
                             style="width: 100%; border-radius: 12px;">
                    <?php else: ?>
                        <div style="width: 100%; height: 225px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-video" style="font-size: 5rem; color: #cbd5e1;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div style="flex: 1;">
                    <h2 style="margin-bottom: 20px; color: var(--primary);"><?= htmlspecialchars($row['judul']) ?></h2>
                    
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; width: 150px;">
                                <strong><i class="fab fa-youtube"></i> URL Video</strong>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <a href="<?= htmlspecialchars($row['url_video']) ?>" target="_blank"><?= htmlspecialchars($row['url_video']) ?></a>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                <strong><i class="fas fa-sort-numeric-up"></i> Urutan</strong>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <?= $row['urutan'] ?? '0' ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px;">
                                <strong><i class="fas fa-align-left"></i> Keterangan</strong>
                            </td>
                            <td style="padding: 12px;">
                                : <?= nl2br(htmlspecialchars($row['keterangan'] ?? '-')) ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card-footer" style="padding: 20px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: flex-end;">
            <a href="edit.php?id=<?= $id ?>" class="btn-edit">
                <i class="fas fa-edit"></i> Edit
            </a>
            <button type="button" 
                    class="btn-delete" 
                    data-id="<?= $id ?>" 
                    data-name="<?= htmlspecialchars($row['judul']) ?>"
                    data-module="video"
                    data-has-thumbnail="<?= (!empty($row['thumbnail']) ? 'true' : 'false') ?>"
                    class="btn-danger">
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
            <p>Apakah Anda yakin ingin menghapus video berikut?</p>
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