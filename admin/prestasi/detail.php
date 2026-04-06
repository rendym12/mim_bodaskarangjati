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
        <a href="index.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <i class="fas fa-trophy"></i>
            <h2><?= htmlspecialchars($row['nama_prestasi']) ?></h2>
        </div>
        
        <div class="detail-body">
            <?php if (!empty($row['gambar'])): ?>
            <div class="detail-gambar">
                <img src="../../uploads/prestasi/<?= $row['gambar'] ?>" 
                     alt="<?= htmlspecialchars($row['nama_prestasi']) ?>">
            </div>
            <?php endif; ?>
            
            <div class="detail-info-grid">
                <div class="detail-info-card">
                    <i class="fas fa-users"></i>
                    <div>
                        <span class="info-label">Jenis Peserta</span>
                        <span class="info-value">
                            <?php if ($row['jenis_peserta'] == 'individu'): ?>
                                <i class="fas fa-user"></i> Individu
                            <?php else: ?>
                                <i class="fas fa-users"></i> Regu / Tim
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                
                <div class="detail-info-card">
                    <i class="fas <?= $row['jenis_peserta'] == 'individu' ? 'fa-user' : 'fa-users' ?>"></i>
                    <div>
                        <span class="info-label"><?= $row['jenis_peserta'] == 'individu' ? 'Nama Siswa' : 'Nama Tim / Anggota' ?></span>
                        <span class="info-value"><?= htmlspecialchars($row['nama_peserta'] ?? '-') ?></span>
                    </div>
                </div>
                
                <div class="detail-info-card">
                    <i class="fas fa-chart-bar"></i>
                    <div>
                        <span class="info-label">Tingkat</span>
                        <span class="info-value"><?= htmlspecialchars($row['tingkat'] ?? '-') ?></span>
                    </div>
                </div>
                
                <div class="detail-info-card">
                    <i class="fas fa-building"></i>
                    <div>
                        <span class="info-label">Penyelenggara</span>
                        <span class="info-value"><?= htmlspecialchars($row['penyelenggara'] ?? '-') ?></span>
                    </div>
                </div>
                
                <div class="detail-info-card">
                    <i class="fas fa-calendar"></i>
                    <div>
                        <span class="info-label">Tahun</span>
                        <span class="info-value"><?= $row['tahun'] ?? '-' ?></span>
                    </div>
                </div>
                
                <div class="detail-info-card">
                    <i class="fas fa-medal"></i>
                    <div>
                        <span class="info-label">Juara / Peringkat</span>
                        <span class="info-value">
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
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="detail-footer">
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="#" class="btn-danger btn-delete-detail" 
               data-id="<?= $id ?>" 
               data-name="<?= htmlspecialchars($row['nama_prestasi']) ?>" 
               data-has-gambar="<?= (!empty($row['gambar']) ? 'true' : 'false') ?>">
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
            <button type="button" id="btnCloseModal" class="btn-secondary">Batal</button>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>