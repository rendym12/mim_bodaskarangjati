<?php
require_once dirname(__DIR__) . '/includes/auth.php';

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM pengumuman WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    header("Location: index.php");
    exit;
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="content-wrapper pengumuman-page">
    <div class="content-header">
        <h1><i class="fas fa-bullhorn"></i> Detail Pengumuman</h1>
        <div>
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="index.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <i class="fas fa-bullhorn"></i>
            <h2><?= htmlspecialchars($row['judul']) ?></h2>
        </div>
        
        <div class="detail-body">
            <div class="detail-meta">
                <div class="detail-meta-item">
                    <i class="fas fa-calendar"></i>
                    <strong>Tanggal:</strong> <?= date('d F Y', strtotime($row['tanggal'])) ?>
                </div>
                <div class="detail-meta-item">
                    <i class="fas fa-user"></i>
                    <strong>Penulis:</strong> <?= htmlspecialchars($row['penulis']) ?>
                </div>
                <div class="detail-meta-item">
                    <i class="fas fa-eye"></i>
                    <strong>Status:</strong> 
                    <?php if ($row['status'] == 'publish'): ?>
                        <span class="badge-success">Publish</span>
                    <?php else: ?>
                        <span class="badge-warning">Draft</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($row['gambar'])): ?>
            <div class="detail-gambar">
                <img src="../../uploads/pengumuman/<?= $row['gambar'] ?>" alt="Gambar Pengumuman">
            </div>
            <?php endif; ?>
            
            <div class="detail-isi">
                <?= nl2br(htmlspecialchars($row['isi'])) ?>
            </div>
            
            <?php if (!empty($row['file_lampiran'])): ?>
            <div class="detail-lampiran">
                <h4><i class="fas fa-paperclip"></i> File Lampiran</h4>
                <a href="../../uploads/lampiran/<?= $row['file_lampiran'] ?>" class="btn-primary" download>
                    <i class="fas fa-download"></i> Download <?= $row['file_lampiran'] ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="detail-footer">
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="#" class="btn-danger btn-delete-detail" data-id="<?= $id ?>" data-name="<?= htmlspecialchars($row['judul']) ?>" data-has-gambar="<?= (!empty($row['gambar']) ? 'true' : 'false') ?>" data-has-lampiran="<?= (!empty($row['file_lampiran']) ? 'true' : 'false') ?>">
                <i class="fas fa-trash"></i> Hapus
            </a>
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
            <p>Apakah Anda yakin ingin menghapus pengumuman berikut?</p>
            <p style="font-weight: bold; font-size: 1.1rem; margin: 10px 0;" id="deleteItemName"></p>
            <div id="fileWarningContainer" style="display: none;">
                <div style="color: #ef4444; background: #fee2e2; padding: 12px; border-radius: 5px;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="fileWarningText"></span>
                </div>
            </div>
            <p style="color: #ef4444; background: #fee2e2; padding: 8px; border-radius: 5px; margin-top: 10px;">
                <i class="fas fa-exclamation-circle"></i>
                Data yang sudah dihapus tidak dapat dikembalikan!
            </p>
        </div>
        <div class="modal-footer">
            <a href="#" id="confirmDeleteBtn" style="background: #ef4444; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none;">Ya, Hapus</a>
            <button type="button" class="btn-secondary">Batal</button>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>