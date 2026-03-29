<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Ambil data gambar untuk dihapus
    $q = mysqli_query($conn, "SELECT judul, gambar FROM hero_slider WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    $file_deleted = false;
    
    $check = mysqli_query($conn, "SELECT * FROM hero_slider WHERE id = $id");
    
    if (mysqli_num_rows($check) > 0) {
        $judul = $data['judul'];
        
        // Hapus file gambar jika ada
        if ($data && !empty($data['gambar']) && file_exists("../../uploads/hero/" . $data['gambar'])) {
            if (unlink("../../uploads/hero/" . $data['gambar'])) {
                $file_deleted = true;
            }
        }
        
        mysqli_query($conn, "DELETE FROM hero_slider WHERE id = $id");
        
        if ($file_deleted) {
            $_SESSION['success'] = [
                'message' => "Slider <strong>\"$judul\"</strong> berhasil dihapus",
                'file_deleted' => true,
                'type' => 'slider'
            ];
        } else {
            $_SESSION['success'] = [
                'message' => "Slider <strong>\"$judul\"</strong> berhasil dihapus",
                'type' => 'slider'
            ];
        }
    } else {
        $_SESSION['error'] = "Data slider tidak ditemukan";
    }
    
    header("Location: index.php");
    exit;
}

// ========== PROSES UBAH STATUS ==========
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $status = $_GET['status'] == 'aktif' ? 'nonaktif' : 'aktif';
    mysqli_query($conn, "UPDATE hero_slider SET status='$status' WHERE id=$id");
    $_SESSION['success'] = "Status slider berhasil diubah";
    header("Location: index.php");
    exit;
}

// ========== AMBIL DATA ==========
$query = mysqli_query($conn, "SELECT * FROM hero_slider ORDER BY urutan ASC, id DESC");

include "../includes/header.php";
?>

<div class="content-wrapper slider-page">
    <div class="content-header">
        <h1><i class="fas fa-images"></i> Kelola Hero Slider</h1>
        <a href="tambah.php" class="btn-primary">
            <i class="fas fa-plus"></i> Tambah Slide
        </a>
    </div>

    <!-- NOTIFICATION CONTAINER -->
    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <?php if (is_array($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <i class="fas fa-check-circle"></i>
                    <?= $_SESSION['success']['message'] ?>
                    
                    <?php if (isset($_SESSION['success']['file_deleted'])): ?>
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

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Daftar Slide</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Gambar</th>
                            <th width="20%">Judul</th>
                            <th width="15%">Badge</th>
                            <th width="10%">Urutan</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <?php if (!empty($row['gambar'])): ?>
                                    <img src="../../uploads/hero/<?= $row['gambar'] ?>" alt="<?= $row['judul'] ?>" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    <i class="fas fa-image" style="font-size: 2rem; color: var(--gray);"></i>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($row['judul']) ?></strong></td>
                            <td><?= htmlspecialchars($row['badge'] ?? '-') ?></td>
                            <td><?= $row['urutan'] ?></td>
                            <td>
                                <?php if ($row['status'] == 'aktif'): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <!-- Tombol Detail - Icon Mata -->
                                    <a href="detail.php?id=<?= $row['id'] ?>" class="btn-view" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- Tombol Edit - Icon Pensil -->
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <!-- Tombol Toggle Status - Icon Toggle On/Off (BERBEDA) -->
                                    <a href="?toggle=<?= $row['id'] ?>&status=<?= $row['status'] ?>" class="btn-toggle" title="<?= $row['status'] == 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                        <i class="fas <?= $row['status'] == 'aktif' ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                    </a>
                                    
                                    <!-- Tombol Hapus - Icon Tong sampah -->
                                    <a href="#" onclick="confirmDelete(<?= $row['id'] ?>, '<?= htmlspecialchars($row['judul']) ?>', 'slider', <?= (!empty($row['gambar']) ? 'true' : 'false') ?>)" class="btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="fas fa-images"></i>
                                <p>Belum ada data slider</p>
                                <a href="tambah.php" class="btn-primary">Tambah Slide</a>
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
            <span class="modal-close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus <span id="itemType"></span> berikut?</p>
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
            <a href="#" id="confirmDeleteBtn" style="background: #ef4444; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none;">Ya, Hapus</a>
            <button type="button" onclick="closeModal()" style="background: #6c757d; color: white; padding: 8px 15px; border-radius: 5px; border: none; cursor: pointer;">Batal</button>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>