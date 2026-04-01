<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM agenda WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data agenda tidak ditemukan";
    header("Location: index.php");
    exit;
}

include "../includes/header.php";
?>

<div class="content-wrapper agenda-page">
    <div class="content-header">
        <h1><i class="fas fa-calendar-alt"></i> Detail Agenda</h1>
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
                <div style="flex: 1;">
                    <h2 style="margin-bottom: 20px; color: var(--primary);"><?= htmlspecialchars($row['nama_agenda']) ?></h2>
                    
                    <table class="detail-table" style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; width: 150px;">
                                <strong><i class="fas fa-calendar-alt"></i> Tanggal Mulai</strong>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <?= date('d/m/Y', strtotime($row['tanggal_mulai'])) ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                <strong><i class="fas fa-calendar-check"></i> Tanggal Selesai</strong>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <?= date('d/m/Y', strtotime($row['tanggal_selesai'])) ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                <strong><i class="fas fa-map-marker-alt"></i> Lokasi</strong>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <?= htmlspecialchars($row['lokasi'] ?? '-') ?>
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
        
        <div class="card-footer" style="padding: 20px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: flex-end;">
            <a href="edit.php?id=<?= $id ?>" class="btn-edit">
                <i class="fas fa-edit"></i> Edit
            </a>
            <button type="button" 
                    class="btn-delete" 
                    data-id="<?= $id ?>" 
                    data-name="<?= htmlspecialchars($row['nama_agenda']) ?>"
                    data-module="agenda"
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
            <p>Apakah Anda yakin ingin menghapus Agenda berikut?</p>
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