<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Ambil data untuk dihapus DAN URUTAN
    $q = mysqli_query($conn, "SELECT judul, file_foto, urutan FROM galeri_foto WHERE id = $id");  // + tambahkan ", urutan"
    $data = mysqli_fetch_assoc($q);
    
    $file_deleted = false;
    
    // Cek apakah data ada
    $check = mysqli_query($conn, "SELECT * FROM galeri_foto WHERE id = $id");
    
    if (mysqli_num_rows($check) > 0) {
        $judul = $data['judul'];
        $urutan_yang_dihapus = $data['urutan'];  // + tambahkan baris ini
        
        // Hapus file gambar jika ada
        if ($data && !empty($data['file_foto']) && file_exists("../../uploads/galeri_foto/" . $data['file_foto'])) {
            if (unlink("../../uploads/galeri_foto/" . $data['file_foto'])) {
                $file_deleted = true;
            }
        }
        
        if (mysqli_query($conn, "DELETE FROM galeri_foto WHERE id = $id")) {
            
            // ========== REORDER URUTAN ==========  // + tambahkan 3 baris ini
            if ($urutan_yang_dihapus !== null && $urutan_yang_dihapus > 0) {
                mysqli_query($conn, "UPDATE galeri_foto SET urutan = urutan - 1 WHERE urutan > $urutan_yang_dihapus");
            }
            
            if ($file_deleted) {
                $_SESSION['success'] = [
                    'message' => "Foto <strong>\"$judul\"</strong> berhasil dihapus",
                    'file_deleted' => true,
                    'type' => 'foto'
                ];
            } else {
                $_SESSION['success'] = [
                    'message' => "Foto <strong>\"$judul\"</strong> berhasil dihapus",
                    'type' => 'foto'
                ];
            }
        } else {
            $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Data foto tidak ditemukan";
    }
    
    header("Location: index.php");
    exit;
}

// ========== AMBIL DATA ==========
$query = mysqli_query($conn, "SELECT * FROM galeri_foto ORDER BY urutan ASC, id DESC");

include "../includes/header.php";
?>

<!-- Sisanya tetap SAMA PERSIS seperti kode Anda yang asli -->
<div class="content-wrapper galeri-foto-page">
    <div class="content-header">
        <h1><i class="fas fa-image"></i> Kelola Galeri Foto</h1>
        <a href="tambah.php" class="btn-primary">
            <i class="fas fa-plus"></i> Tambah Foto
        </a>
    </div>

    <!-- NOTIFICATION CONTAINER -->
    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <?php if (is_array($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible <?= isset($_SESSION['success']['file_deleted']) ? 'file-deleted' : '' ?>">
                    <i class="fas fa-check-circle"></i>
                    <?= $_SESSION['success']['message'] ?>
                    
                    <?php if (isset($_SESSION['success']['file_deleted'])): ?>
                        <div class="file-list" style="margin-top: 10px;">
                            <span class="file-badge"><i class="fas fa-image"></i> File Gambar</span>
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
            <h3><i class="fas fa-list"></i> Daftar Foto</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Gambar</th>
                            <th width="25%">Judul</th>
                            <th width="10%">Urutan</th>
                            <th width="25%">Keterangan</th>
                            <th width="20%">Aksi</th>
                        </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)): 
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <?php if (!empty($row['file_foto'])): ?>
                                    <img src="../../uploads/galeri_foto/<?= $row['file_foto'] ?>" 
                                         alt="<?= htmlspecialchars($row['judul']) ?>" 
                                         style="width: 80px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid var(--secondary); cursor: pointer;"
                                         onclick="window.open('../../uploads/galeri_foto/<?= $row['file_foto'] ?>', '_blank')">
                                <?php else: ?>
                                    <div style="width: 80px; height: 60px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                        <i class="fas fa-image" style="font-size: 1.5rem; color: #94a3b8;"></i>
                                    </div>
                                <?php endif; ?>
                             </div>
                            </td>
                            <td><strong><?= htmlspecialchars($row['judul']) ?></strong></td>
                            <td class="text-center">
                                <span style="display: inline-block; padding: 4px 10px; background: #f1f5f9; border-radius: 20px; font-weight: 600; font-size: 0.8rem;">
                                    <?= $row['urutan'] ?? '0' ?>
                                </span>
                            </div>
                            <td>
                                <?php 
                                $keterangan = htmlspecialchars($row['keterangan'] ?? '-');
                                if (strlen($keterangan) > 50) {
                                    echo substr($keterangan, 0, 50) . '...';
                                } else {
                                    echo $keterangan;
                                }
                                ?>
                             </div>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="detail.php?id=<?= $row['id'] ?>" class="btn-view" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn-delete" 
                                            data-id="<?= $row['id'] ?>" 
                                            data-name="<?= htmlspecialchars($row['judul']) ?>"
                                            data-module="foto"
                                            data-has-file="<?= (!empty($row['file_foto']) ? 'true' : 'false') ?>"
                                            data-file-name="<?= htmlspecialchars($row['file_foto'] ?? '') ?>"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                             </div>
                         </tr>
                        <?php endwhile; else: ?>
                         <tr>
                            <td colspan="6" class="empty-state">
                                <i class="fas fa-image"></i>
                                <p>Belum ada data foto</p>
                                <a href="tambah.php" class="btn-primary">Tambah Foto</a>
                             </div>
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
            <p>Apakah Anda yakin ingin menghapus foto berikut?</p>
            <p style="font-weight: bold; font-size: 1.1rem; margin: 10px 0;" id="deleteItemName"></p>
            <div id="fileWarningContainer" style="display: none;">
                <div style="color: #ef4444; background: #fee2e2; padding: 12px; border-radius: 8px; margin-bottom: 10px;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="fileWarningText"></span>
                    <div id="fileList" style="margin-top: 8px; padding-left: 20px;"></div>
                </div>
            </div>
            <div style="color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 8px;">
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