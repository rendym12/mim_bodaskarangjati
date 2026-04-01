<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM guru_staff WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    header("Location: index.php");
    exit;
}

include "../includes/header.php";
?>

<div class="content-wrapper guru-page">
    <div class="content-header">
        <h1><i class="fas fa-chalkboard-teacher"></i> Detail Guru/Staff</h1>
        <div>
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">Edit</a>
            <a href="index.php" class="btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <h2><?= htmlspecialchars($row['nama']) ?></h2>
        </div>
        
        <div class="detail-body">
            <div class="detail-photo">
                <?php if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg'): ?>
                    <img src="../../uploads/guru/<?= $row['foto'] ?>" alt="Foto">
                <?php else: ?>
                    <i class="fas fa-user-circle"></i>
                <?php endif; ?>
            </div>
            
            <div class="detail-info">
                <div class="info-row">
                    <span class="info-label">NIP</span>
                    <span class="info-value"><?= !empty($row['nip']) ? htmlspecialchars($row['nip']) : '-' ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jabatan</span>
                    <span class="info-value"><?= !empty($row['jabatan']) ? htmlspecialchars($row['jabatan']) : '-' ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mata Pelajaran</span>
                    <span class="info-value"><?= !empty($row['mapel']) ? htmlspecialchars($row['mapel']) : '-' ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Urutan Tampil</span>
                    <span class="info-value"><?= $row['urutan'] ?? '0' ?></span>
                </div>
            </div>
        </div>
        
        <div class="detail-footer">
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">Edit</a>
            <a href="#" class="btn-danger btn-delete-detail" data-id="<?= $id ?>" data-name="<?= htmlspecialchars($row['nama']) ?>" data-has-foto="<?= (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg') ? 'true' : 'false' ?>">Hapus</a>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</h3>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus data berikut?</p>
            <p class="delete-item-name" id="deleteItemName"></p>
            <div id="fileWarning" style="display: none;">
                <p><i class="fas fa-exclamation-circle"></i> <span id="fileWarningText"></span></p>
            </div>
            <p class="warning-text"><i class="fas fa-exclamation-circle"></i> Data yang sudah dihapus tidak dapat dikembalikan!</p>
        </div>
        <div class="modal-footer">
            <a href="#" id="confirmDeleteBtn" class="btn-danger">Ya, Hapus</a>
            <button type="button" class="btn-secondary" id="btnCloseModal">Batal</button>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>