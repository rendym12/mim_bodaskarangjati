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
        <div>
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">
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
                <div style="flex: 0 0 100px; text-align: center;">
                    <i class="fas <?= $row['ikon'] ?? 'fa-sun' ?>" style="font-size: 5rem; color: #FFD700;"></i>
                </div>
                
                <div style="flex: 1;">
                    <h2 style="margin-bottom: 20px; color: var(--primary);"><?= htmlspecialchars($row['nama_kegiatan']) ?></h2>
                    
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; width: 150px;">
                                <strong><i class="fas fa-icons"></i> Ikon</strong>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <i class="fas <?= $row['ikon'] ?? 'fa-sun' ?>"></i> <?= $row['ikon'] ?? 'fa-sun' ?>
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
                                <strong><i class="fas fa-align-left"></i> Deskripsi</strong>
                            </td>
                            <td style="padding: 12px;">
                                : <?= nl2br(htmlspecialchars($row['deskripsi'] ?? '-')) ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card-footer" style="padding: 20px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px;">
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="#" onclick="confirmDeletePembiasaan(<?= $id ?>, '<?= htmlspecialchars($row['nama_kegiatan']) ?>')" class="btn-danger">
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
            <span class="modal-close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus kegiatan pembiasaan berikut?</p>
            <p style="font-weight: bold; font-size: 1.1rem; margin: 10px 0;" id="deleteItemName"></p>
            <p style="color: #ef4444; background: #fee2e2; padding: 8px; border-radius: 5px;">
                <i class="fas fa-exclamation-circle"></i>
                Data yang sudah dihapus tidak dapat dikembalikan!
            </p>
        </div>
        <div class="modal-footer">
            <a href="#" id="confirmDeleteBtn" style="background: #ef4444; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none;">Ya, Hapus</a>
            <button type="button" onclick="closeModal()" style="background: #6c757d; color: white; padding: 8px 15px; border-radius: 5px; border: none; cursor: pointer;">Batal</button>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>