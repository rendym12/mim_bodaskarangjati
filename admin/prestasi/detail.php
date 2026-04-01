<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM prestasi WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data prestasi tidak ditemukan";
    header("Location: index.php");
    exit;
}

include "../includes/header.php";
?>

<div class="content-wrapper prestasi-page">
    <div class="content-header">
        <h1><i class="fas fa-trophy"></i> Detail Prestasi</h1>
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
                <?php if (!empty($row['gambar'])): ?>
                <div style="flex: 0 0 300px;">
                    <img src="../../uploads/prestasi/<?= $row['gambar'] ?>" 
                         alt="<?= htmlspecialchars($row['nama_prestasi']) ?>" 
                         style="width: 100%; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                </div>
                <?php endif; ?>
                
                <div style="flex: 1;">
                    <h2 style="color: var(--primary); margin-bottom: 20px;"><?= htmlspecialchars($row['nama_prestasi']) ?></h2>
                    
                    <table style="width: 100%; border-collapse: collapse;">
                         <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; width: 150px;">
                                <strong><i class="fas fa-chart-bar"></i> Tingkat</strong>
                             </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <?= htmlspecialchars($row['tingkat'] ?? '-') ?>
                             </td>
                         </tr>
                         <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                <strong><i class="fas fa-building"></i> Penyelenggara</strong>
                             </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <?= htmlspecialchars($row['penyelenggara'] ?? '-') ?>
                             </td>
                         </tr>
                         <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                <strong><i class="fas fa-calendar"></i> Tahun</strong>
                             </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <?= $row['tahun'] ?? '-' ?>
                             </td>
                         </tr>
                         <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                <strong><i class="fas fa-medal"></i> Juara / Peringkat</strong>
                             </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : 
                                <?php 
                                if ($row['juara'] == 1) {
                                    echo '<span class="badge-gold"><i class="fas fa-medal"></i> Juara 1 (Emas)</span>';
                                } elseif ($row['juara'] == 2) {
                                    echo '<span class="badge-silver"><i class="fas fa-medal"></i> Juara 2 (Perak)</span>';
                                } elseif ($row['juara'] == 3) {
                                    echo '<span class="badge-bronze"><i class="fas fa-medal"></i> Juara 3 (Perunggu)</span>';
                                } elseif ($row['juara'] > 0) {
                                    echo '<span class="badge-juara"><i class="fas fa-star"></i> Juara ' . $row['juara'] . '</span>';
                                } else {
                                    echo '<span class="badge-peserta"><i class="fas fa-user"></i> Peserta / Tidak Berperingkat</span>';
                                }
                                ?>
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
            <a href="#" class="btn-danger btn-delete-detail" data-id="<?= $id ?>" data-name="<?= htmlspecialchars($row['nama_prestasi']) ?>" data-has-gambar="<?= (!empty($row['gambar']) ? 'true' : 'false') ?>">
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
            <p>Apakah Anda yakin ingin menghapus prestasi berikut?</p>
            <p style="font-weight: bold; font-size: 1.1rem; margin: 10px 0;" id="deleteItemName"></p>
            
            <div id="fileWarningContainer" style="display: none;">
                <div style="color: #ef4444; background: #fee2e2; padding: 12px; border-radius: 5px; margin-bottom: 10px;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="fileWarningText">Prestasi ini memiliki GAMBAR yang akan ikut terhapus.</span>
                </div>
            </div>
            
            <div style="color: #ef4444; background: #fee2e2; padding: 8px; border-radius: 5px;">
                <i class="fas fa-exclamation-circle"></i>
                Data yang sudah dihapus tidak dapat dikembalikan!
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" id="confirmDeleteBtn" class="btn-danger">Ya, Hapus</a>
            <button type="button" class="btn-secondary" id="btnCloseModal">Batal</button>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>