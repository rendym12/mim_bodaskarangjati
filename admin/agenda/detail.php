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
        <a href="index.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <i class="fas fa-calendar-alt"></i>
            <h2><?= htmlspecialchars($row['nama_agenda']) ?></h2>
        </div>
        
        <div class="detail-body">
            <!-- Info Grid untuk informasi penting -->
            <div class="detail-info-grid">
                <div class="detail-info-card">
                    <i class="fas fa-calendar-alt"></i>
                    <div class="detail-info-content">
                        <span class="info-label">Tanggal Mulai</span>
                        <span class="info-value"><?= date('d F Y', strtotime($row['tanggal_mulai'])) ?></span>
                    </div>
                </div>
                <div class="detail-info-card">
                    <i class="fas fa-calendar-check"></i>
                    <div class="detail-info-content">
                        <span class="info-label">Tanggal Selesai</span>
                        <span class="info-value"><?= date('d F Y', strtotime($row['tanggal_selesai'])) ?></span>
                    </div>
                </div>
                <div class="detail-info-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="detail-info-content">
                        <span class="info-label">Lokasi</span>
                        <span class="info-value"><?= htmlspecialchars($row['lokasi'] ?? '-') ?></span>
                    </div>
                </div>
                <?php
                // Tentukan status agenda
                $today = date('Y-m-d');
                if ($row['tanggal_mulai'] <= $today && $row['tanggal_selesai'] >= $today) {
                    $status_text = 'Berlangsung';
                    $status_class = 'status-berlangsung';
                } elseif ($row['tanggal_selesai'] < $today) {
                    $status_text = 'Selesai';
                    $status_class = 'status-selesai';
                } else {
                    $status_text = 'Mendatang';
                    $status_class = 'status-mendatang';
                }
                ?>
                <div class="detail-info-card">
                    <i class="fas fa-info-circle"></i>
                    <div class="detail-info-content">
                        <span class="info-label">Status</span>
                        <span class="info-value <?= $status_class ?>"><?= $status_text ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Deskripsi Agenda -->
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
                    data-name="<?= htmlspecialchars($row['nama_agenda']) ?>"
                    data-module="agenda">
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