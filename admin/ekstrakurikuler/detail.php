<?php
require_once dirname(__DIR__) . '/includes/auth.php';

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM ekstrakurikuler WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    header("Location: index.php");
    exit;
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="content-wrapper ekstra-page">
    <div class="content-header">
        <h1><i class="fas fa-futbol"></i> Detail Ekstrakurikuler</h1>
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
            <i class="fas <?= $row['ikon'] ?? 'fa-futbol' ?>"></i>
            <h2><?= htmlspecialchars($row['nama_eks']) ?></h2>
        </div>
        
        <div class="detail-body">
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-user"></i> Pembina
                </div>
                <div class="detail-value">
                    <?= htmlspecialchars($row['pembina'] ?? '-') ?>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-clock"></i> Jadwal
                </div>
                <div class="detail-value">
                    <?= htmlspecialchars($row['jadwal'] ?? '-') ?>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-icons"></i> Ikon
                </div>
                <div class="detail-value">
                    <i class="fas <?= $row['ikon'] ?? 'fa-futbol' ?>"></i> <?= $row['ikon'] ?? 'fa-futbol' ?>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-sort-numeric-up"></i> Urutan
                </div>
                <div class="detail-value">
                    <?= $row['urutan'] ?? '0' ?>
                </div>
            </div>
            
            <?php if (!empty($row['deskripsi'])): ?>
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-align-left"></i> Deskripsi
                </div>
                <div class="detail-value" style="white-space: pre-line;">
                    <?= nl2br(htmlspecialchars($row['deskripsi'])) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="detail-footer">
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="#" class="btn-danger btn-delete-detail" data-id="<?= $id ?>" data-name="<?= htmlspecialchars($row['nama_eks']) ?>">
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
            <p>Apakah Anda yakin ingin menghapus ekstrakurikuler berikut?</p>
            <p style="font-weight: bold; font-size: 1.1rem; margin: 10px 0;" id="deleteItemName"></p>
            <p style="color: #ef4444; background: #fee2e2; padding: 8px; border-radius: 5px;">
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