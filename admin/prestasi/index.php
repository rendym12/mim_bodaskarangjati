<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Ambil data gambar untuk dihapus
    $q = mysqli_query($conn, "SELECT nama_prestasi, gambar FROM prestasi WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    $file_deleted = false;
    
    // Cek apakah data ada
    $check = mysqli_query($conn, "SELECT * FROM prestasi WHERE id = $id");
    
    if (mysqli_num_rows($check) > 0) {
        $nama = $data['nama_prestasi'];
        
        // Hapus file gambar jika ada
        if ($data && !empty($data['gambar']) && file_exists("../../uploads/prestasi/" . $data['gambar'])) {
            if (unlink("../../uploads/prestasi/" . $data['gambar'])) {
                $file_deleted = true;
            }
        }
        
        mysqli_query($conn, "DELETE FROM prestasi WHERE id = $id");
        
        // Buat pesan notifikasi
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
$query = mysqli_query($conn, "SELECT * FROM prestasi ORDER BY tahun DESC, id DESC");

// ========== INCLUDE HEADER ==========
include "../includes/header.php";
?>

<!-- NOTIFICATION CONTAINER -->
<div class="notification-container">
    <?php if (isset($_SESSION['success'])): ?>
        <?php if (is_array($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible <?= isset($_SESSION['success']['file_deleted']) ? 'file-deleted' : '' ?>">
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
                            <th width="25%">Nama Prestasi</th>
                            <th width="15%">Tingkat</th>
                            <th width="15%">Penyelenggara</th>
                            <th width="10%">Tahun</th>
                            <th width="10%">Gambar</th>
                            <th width="10%">Urutan</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($row['nama_prestasi']) ?></strong></td>
                            <td><?= htmlspecialchars($row['tingkat'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['penyelenggara'] ?? '-') ?></td>
                            <td><?= $row['tahun'] ?? '-' ?></td>
                            <td>
                                <?php if (!empty($row['gambar'])): ?>
                                    <i class="fas fa-check-circle" style="color: #10b981;"></i> Ada
                                <?php else: ?>
                                    <i class="fas fa-times-circle" style="color: #ef4444;"></i> Tidak
                                <?php endif; ?>
                            </td>
                            <td><?= $row['urutan'] ?? '0' ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="detail.php?id=<?= $row['id'] ?>" class="btn-view" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" onclick="confirmDelete(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nama_prestasi']) ?>', 'prestasi', <?= (!empty($row['gambar']) ? 'true' : 'false') ?>)" class="btn-delete" title="Hapus">
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
            <span class="modal-close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus <span id="itemType"></span> berikut?</p>
            <p style="font-weight: bold; font-size: 1.1rem; margin: 10px 0;" id="deleteItemName"></p>
            
            <!-- Warning untuk file gambar -->
            <div id="fileWarningContainer" style="display: none;">
                <div style="color: #ef4444; background: #fee2e2; padding: 12px; border-radius: 5px; margin-bottom: 10px;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="fileWarningText">Prestasi ini memiliki GAMBAR yang akan ikut terhapus.</span>
                    <div style="margin-top: 8px; padding-left: 20px;">
                        <div><i class="fas fa-image"></i> File gambar</div>
                    </div>
                </div>
            </div>
            
            <!-- Warning umum -->
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