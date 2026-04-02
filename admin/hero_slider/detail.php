<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM hero_slider WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data slider tidak ditemukan";
    header("Location: index.php");
    exit;
}

include "../includes/header.php";
?>

<div class="content-wrapper slider-page">
    <div class="content-header">
        <h1><i class="fas fa-images"></i> Detail Slide</h1>
        <a href="index.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <i class="fas fa-image"></i>
            <h2><?= htmlspecialchars($row['judul']) ?></h2>
        </div>
        
        <div class="detail-body">
            <div class="detail-info-grid">
                <div class="detail-info-card">
                    <i class="fas fa-tag"></i>
                    <div class="detail-info-content">
                        <span class="info-label">Badge</span>
                        <span class="info-value"><?= htmlspecialchars($row['badge'] ?? '-') ?></span>
                    </div>
                </div>
                <div class="detail-info-card">
                    <i class="fas fa-sort-numeric-down"></i>
                    <div class="detail-info-content">
                        <span class="info-label">Urutan</span>
                        <span class="info-value"><?= $row['urutan'] ?? '0' ?></span>
                    </div>
                </div>
                <div class="detail-info-card">
                    <i class="fas fa-toggle-on"></i>
                    <div class="detail-info-content">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <?php if ($row['status'] == 'aktif'): ?>
                                <span class="badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($row['subjudul'])): ?>
            <div class="detail-deskripsi">
                <strong><i class="fas fa-align-left"></i> Sub Judul:</strong><br>
                <?= nl2br(htmlspecialchars($row['subjudul'])) ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($row['tombol_text']) && !empty($row['tombol_link'])): ?>
            <div class="detail-deskripsi" style="margin-top: 15px;">
                <strong><i class="fas fa-link"></i> Tombol:</strong><br>
                <a href="<?= htmlspecialchars($row['tombol_link']) ?>" target="_blank" class="btn-primary" style="display: inline-block; margin-top: 8px;">
                    <?= htmlspecialchars($row['tombol_text']) ?>
                </a>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($row['gambar'])): ?>
            <div class="detail-gambar">
                <img src="../../uploads/hero/<?= $row['gambar'] ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
            </div>
            <?php endif; ?>
        </div>
        
        <div class="detail-footer">
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <button type="button" 
                    class="btn-danger" 
                    data-id="<?= $id ?>" 
                    data-name="<?= htmlspecialchars($row['judul']) ?>"
                    data-has-gambar="<?= (!empty($row['gambar']) ? 'true' : 'false') ?>"
                    data-module="slider">
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