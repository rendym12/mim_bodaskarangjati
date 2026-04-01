<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $q = mysqli_query($conn, "SELECT nama_prestasi, gambar FROM prestasi WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    $file_deleted = false;
    $check = mysqli_query($conn, "SELECT * FROM prestasi WHERE id = $id");
    
    if (mysqli_num_rows($check) > 0) {
        $nama = $data['nama_prestasi'];
        
        if ($data && !empty($data['gambar']) && file_exists("../../uploads/prestasi/" . $data['gambar'])) {
            if (unlink("../../uploads/prestasi/" . $data['gambar'])) {
                $file_deleted = true;
            }
        }
        
        mysqli_query($conn, "DELETE FROM prestasi WHERE id = $id");
        
        if ($file_deleted) {
            $_SESSION['success'] = [
                'message' => "Prestasi <strong>\"$nama\"</strong> berhasil dihapus",
                'file_deleted' => true,
                'type' => 'prestasi'
            ];
        } else {
            $_SESSION['success'] = [
                'message' => "Prestasi <strong>\"$nama\"</strong> berhasil dihapus",
                'type' => 'prestasi'
            ];
        }
    } else {
        $_SESSION['error'] = "Data prestasi tidak ditemukan";
    }
    
    header("Location: index.php");
    exit;
}

// ========== AMBIL DATA ==========
$query = mysqli_query($conn, "SELECT * FROM prestasi ORDER BY tahun DESC, juara ASC, id DESC");

include "../includes/header.php";
?>

<div class="notification-container">
    <?php if (isset($_SESSION['success'])): ?>
        <?php if (is_array($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i>
                <?= $_SESSION['success']['message'] ?>
                <?php if (isset($_SESSION['success']['file_deleted']) && $_SESSION['success']['file_deleted']): ?>
                    <div class="file-list" style="margin-top: 10px;">
                        <span class="file-badge"><i class="fas fa-image"></i> Gambar</span>
                        <small style="display: block; margin-top: 8px; color: #0b5e2e;">
                            <i class="fas fa-info-circle"></i> File gambar ikut terhapus
                        </small>
                    </div>
                <?php endif; ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php else: ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible">
            <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
            <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</div>

<div class="content-wrapper prestasi-page">
    <div class="content-header">
        <h1><i class="fas fa-trophy"></i> Kelola Prestasi</h1>
        <a href="tambah.php" class="btn-primary">
            <i class="fas fa-plus"></i> Tambah Prestasi
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Daftar Prestasi</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Nama Prestasi</th>
                            <th width="12%">Tingkat</th>
                            <th width="15%">Penyelenggara</th>
                            <th width="8%">Tahun</th>
                            <th width="10%">Juara</th>
                            <th width="10%">Gambar</th>
                            <th width="10%">Aksi</th>
                        </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)): 
                                // Format juara
                                $juara_text = '';
                                if ($row['juara'] == 1) {
                                    $juara_text = '<span class="badge-gold"><i class="fas fa-medal"></i> Juara 1</span>';
                                } elseif ($row['juara'] == 2) {
                                    $juara_text = '<span class="badge-silver"><i class="fas fa-medal"></i> Juara 2</span>';
                                } elseif ($row['juara'] == 3) {
                                    $juara_text = '<span class="badge-bronze"><i class="fas fa-medal"></i> Juara 3</span>';
                                } elseif ($row['juara'] > 0) {
                                    $juara_text = '<span class="badge-juara"><i class="fas fa-star"></i> Juara ' . $row['juara'] . '</span>';
                                } else {
                                    $juara_text = '<span class="badge-peserta"><i class="fas fa-user"></i> Peserta</span>';
                                }
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($row['nama_prestasi']) ?></strong></td>
                            <td><?= htmlspecialchars($row['tingkat'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['penyelenggara'] ?? '-') ?></td>
                            <td><?= $row['tahun'] ?? '-' ?></td>
                            <td class="text-center"><?= $juara_text ?></td>
                            <td class="text-center">
                                <?php if (!empty($row['gambar'])): ?>
                                    <span class="badge-success"><i class="fas fa-check-circle"></i> Ada</span>
                                <?php else: ?>
                                    <span class="badge-danger"><i class="fas fa-times-circle"></i> Tidak</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="detail.php?id=<?= $row['id'] ?>" class="btn-view" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn-delete" data-id="<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['nama_prestasi']) ?>" data-has-gambar="<?= (!empty($row['gambar']) ? 'true' : 'false') ?>" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="fas fa-trophy"></i>
                                <p>Belum ada data prestasi</p>
                                <a href="tambah.php" class="btn-primary">Tambah Prestasi</a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
            <p>Apakah Anda yakin ingin menghapus <span id="itemType"></span> berikut?</p>
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