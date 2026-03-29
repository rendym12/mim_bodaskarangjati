<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $q = mysqli_query($conn, "SELECT nama_kegiatan FROM pembiasaan WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    $check = mysqli_query($conn, "SELECT * FROM pembiasaan WHERE id = $id");
    
    if (mysqli_num_rows($check) > 0) {
        $nama = $data['nama_kegiatan'];
        mysqli_query($conn, "DELETE FROM pembiasaan WHERE id = $id");
        
        $_SESSION['success'] = [
            'message' => "Kegiatan pembiasaan <strong>\"$nama\"</strong> berhasil dihapus",
            'type' => 'pembiasaan'
        ];
    } else {
        $_SESSION['error'] = "Data pembiasaan tidak ditemukan";
    }
    
    header("Location: index.php");
    exit;
}

// ========== AMBIL DATA ==========
$query = mysqli_query($conn, "SELECT * FROM pembiasaan ORDER BY urutan ASC, id DESC");

include "../includes/header.php";
?>

<div class="content-wrapper pembiasaan-page">
    <div class="content-header">
        <h1><i class="fas fa-sun"></i> Kelola Pembiasaan Pagi</h1>
        <a href="tambah.php" class="btn-primary">
            <i class="fas fa-plus"></i> Tambah Pembiasaan
        </a>
    </div>

    <!-- NOTIFICATION CONTAINER -->
    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <?php if (is_array($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <i class="fas fa-check-circle"></i>
                    <?= $_SESSION['success']['message'] ?>
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
            <h3><i class="fas fa-list"></i> Daftar Kegiatan Pembiasaan</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Kegiatan</th>
                            <th width="10%">Ikon</th>
                            <th width="10%">Urutan</th>
                            <th width="35%">Deskripsi</th>
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
                            <td><strong><?= htmlspecialchars($row['nama_kegiatan']) ?></strong></td>
                            <td><i class="fas <?= $row['ikon'] ?? 'fa-sun' ?>"></i></td>
                            <td><?= $row['urutan'] ?? '0' ?></td>
                            <td><?= htmlspecialchars(substr($row['deskripsi'] ?? '-', 0, 100)) ?>...</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="detail.php?id=<?= $row['id'] ?>" class="btn-view" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" onclick="confirmDeletePembiasaan(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nama_kegiatan']) ?>')" class="btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="fas fa-sun"></i>
                                <p>Belum ada data pembiasaan</p>
                                <a href="tambah.php" class="btn-primary">Tambah Pembiasaan</a>
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