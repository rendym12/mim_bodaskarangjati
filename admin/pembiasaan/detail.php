<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM pembiasaan WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data pembiasaan tidak ditemukan";
    header("Location: index.php");
    exit;
}

include "../includes/header.php";
?>

<div class="content-wrapper pembiasaan-page">
    <div class="content-header">
        <h1><i class="fas fa-sun"></i> Detail Pembiasaan</h1>
        <a href="index.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <i class="fas <?= $row['ikon'] ?? 'fa-sun' ?>"></i>
            <h2><?= htmlspecialchars($row['nama_kegiatan']) ?></h2>
        </div>
        
        <div class="detail-body">
            <div class="detail-info-grid">
                <div class="detail-info-card">
                    <i class="fas fa-icons"></i>
                    <div class="detail-info-content">
                        <span class="info-label">Ikon</span>
                        <span class="info-value"><?= $row['ikon'] ?? 'fa-sun' ?></span>
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
            
            <div class="detail-deskripsi">
                <?= nl2br(htmlspecialchars($row['deskripsi'] ?? '-')) ?>
            </div>
        </div>
        
        <div class="detail-footer">
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <button type="button" 
                    class="btn-danger" 
                    data-id="<?= $id ?>" 
                    data-name="<?= htmlspecialchars($row['nama_kegiatan']) ?>"
                    data-module="pembiasaan">
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