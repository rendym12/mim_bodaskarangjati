<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Ambil data untuk notifikasi
    $q = mysqli_query($conn, "SELECT judul, gambar, file_lampiran FROM pengumuman WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    $file_deleted = [];
    
    // Cek apakah data ada
    $check = mysqli_query($conn, "SELECT * FROM pengumuman WHERE id = $id");
    
    if (mysqli_num_rows($check) > 0) {
        $judul = $data['judul'];
        
        // Hapus file gambar jika ada
        if ($data && !empty($data['gambar']) && file_exists("../../uploads/pengumuman/" . $data['gambar'])) {
            if (unlink("../../uploads/pengumuman/" . $data['gambar'])) {
                $file_deleted[] = 'gambar';
            }
        }
        
        // Hapus file lampiran jika ada
        if ($data && !empty($data['file_lampiran']) && file_exists("../../uploads/lampiran/" . $data['file_lampiran'])) {
            if (unlink("../../uploads/lampiran/" . $data['file_lampiran'])) {
                $file_deleted[] = 'lampiran';
            }
        }
        
        mysqli_query($conn, "DELETE FROM pengumuman WHERE id = $id");
        
        // Buat pesan notifikasi dengan array
        $_SESSION['success'] = [
            'message' => "Pengumuman <strong>\"$judul\"</strong> berhasil dihapus",
            'file_deleted' => $file_deleted,
            'type' => 'pengumuman'
        ];
    } else {
        $_SESSION['error'] = "Data pengumuman tidak ditemukan";
    }
    
    header("Location: index.php");
    exit;
}

// ========== AMBIL DATA ==========
$query = mysqli_query($conn, "SELECT * FROM pengumuman ORDER BY tanggal DESC, id DESC");

include "../includes/header.php";
?>

<div class="content-wrapper pengumuman-page">
    <!-- Content Header -->
    <div class="content-header">
        <h1><i class="fas fa-bullhorn"></i> Kelola Pengumuman</h1>
        <a href="tambah.php" class="btn-primary">
            <i class="fas fa-plus"></i> Tambah Pengumuman
        </a>
    </div>

    <!-- Alert Notifikasi Sukses dengan File Info -->
    <?php if (isset($_SESSION['success'])): ?>
        <?php if (is_array($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i>
                <?= $_SESSION['success']['message'] ?>
                
                <?php if (!empty($_SESSION['success']['file_deleted'])): ?>
                    <div class="file-info">
                        <small>
                            <i class="fas fa-paperclip"></i> File yang ikut terhapus: 
                            <?= implode(' dan ', $_SESSION['success']['file_deleted']) ?>
                        </small>
                    </div>
                <?php endif; ?>
                
                <button type="button" class="close">&times;</button>
            </div>
        <?php else: ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i>
                <?= $_SESSION['success'] ?>
                <button type="button" class="close">&times;</button>
            </div>
        <?php endif; ?>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Alert Notifikasi Error -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible">
            <i class="fas fa-exclamation-circle"></i>
            <?= $_SESSION['error'] ?>
            <button type="button" class="close">&times;</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Card Tabel -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Daftar Pengumuman</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Judul</th>
                            <th width="10%">Tanggal</th>
                            <th width="10%">Penulis</th>
                            <th width="8%">Status</th>
                            <th width="8%">Gambar</th>
                            <th width="8%">Lampiran</th>
                            <th width="15%">Aksi</th>
                        </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row['judul']) ?></strong>
                                <br>
                                <small class="text-muted"><?= htmlspecialchars(substr(strip_tags($row['isi']), 0, 50)) ?>...</small>
                            </td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= htmlspecialchars($row['penulis'] ?? '-') ?></td>
                            <td>
                                <?php if ($row['status'] == 'publish'): ?>
                                    <span class="badge-success">Publish</span>
                                <?php else: ?>
                                    <span class="badge-warning">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($row['gambar'])): ?>
                                    <i class="fas fa-check-circle" style="color: #10b981;"></i> Ada
                                <?php else: ?>
                                    <i class="fas fa-times-circle" style="color: #ef4444;"></i> Tidak
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($row['file_lampiran'])): ?>
                                    <i class="fas fa-check-circle" style="color: #10b981;"></i> Ada
                                <?php else: ?>
                                    <i class="fas fa-times-circle" style="color: #ef4444;"></i> Tidak
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="detail.php?id=<?= $row['id'] ?>" class="btn-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn-delete" data-id="<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['judul']) ?>" data-has-gambar="<?= (!empty($row['gambar']) ? 'true' : 'false') ?>" data-has-lampiran="<?= (!empty($row['file_lampiran']) ? 'true' : 'false') ?>">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="fas fa-bullhorn"></i>
                                <p>Belum ada data pengumuman</p>
                                <a href="tambah.php" class="btn-primary">Tambah Pengumuman</a>
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
                    <span id="fileWarningText"></span>
                    <div id="fileList" style="margin-top: 8px; padding-left: 20px;"></div>
                </div>
            </div>
            <p style="color: #ef4444; background: #fee2e2; padding: 8px; border-radius: 5px;">
                <i class="fas fa-exclamation-circle"></i>
                Data yang sudah dihapus tidak dapat dikembalikan!
            </p>
        </div>
        <div class="modal-footer">
            <a href="#" id="confirmDeleteBtn" style="background: #ef4444; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none;">Ya, Hapus</a>
            <button type="button" class="btn-secondary">Batal</button>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>