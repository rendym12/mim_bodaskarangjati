<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Cek apakah data ada
    $check = mysqli_query($conn, "SELECT * FROM admin_users WHERE id = $id");
    
    if (mysqli_num_rows($check) > 0) {
        $q = mysqli_query($conn, "SELECT nama_lengkap, foto FROM admin_users WHERE id = $id");
        $data = mysqli_fetch_assoc($q);
        $nama = $data['nama_lengkap'];
        
        // Hapus foto jika bukan default
        $hasFoto = false;
        if ($data && !empty($data['foto']) && $data['foto'] != 'default-avatar.jpg' && file_exists("../../uploads/" . $data['foto'])) {
            if (unlink("../../uploads/" . $data['foto'])) {
                $hasFoto = true;
            }
        }
        
        if (mysqli_query($conn, "DELETE FROM admin_users WHERE id = $id")) {
            if ($hasFoto) {
                $_SESSION['success'] = [
                    'message' => "Admin <strong>\"$nama\"</strong> berhasil dihapus",
                    'file_deleted' => true,
                    'type' => 'admin'
                ];
            } else {
                $_SESSION['success'] = [
                    'message' => "Admin <strong>\"$nama\"</strong> berhasil dihapus",
                    'type' => 'admin'
                ];
            }
        } else {
            $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Data admin tidak ditemukan";
    }
    
    header("Location: index.php");
    exit;
}

// ========== AMBIL DATA ==========
$query = mysqli_query($conn, "SELECT * FROM admin_users ORDER BY id DESC");

include "../includes/header.php";
?>

<div class="content-wrapper users-page">
    <div class="content-header">
        <h1><i class="fas fa-users-cog"></i> Kelola Admin</h1>
        <a href="tambah.php" class="btn-primary">
            <i class="fas fa-plus"></i> Tambah Admin
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
                            <span class="file-badge"><i class="fas fa-camera"></i> Foto profil</span>
                            <small style="display: block; margin-top: 8px; color: #0b5e2e;">
                                <i class="fas fa-info-circle"></i> File foto ikut terhapus
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
            <h3><i class="fas fa-list"></i> Daftar Admin</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Foto</th>
                            <th width="25%">Nama Lengkap</th>
                            <th width="20%">Username</th>
                            <th width="25%">Email</th>
                            <th width="15%">Aksi</th>
                        </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)): 
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <?php if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg'): ?>
                                    <img src="../../uploads/<?= $row['foto'] ?>" alt="Foto" class="user-avatar">
                                <?php else: ?>
                                    <i class="fas fa-user-circle default-avatar-icon"></i>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($row['id'] != $_SESSION['admin_id']): ?>
                                    <button type="button" 
                                            class="btn-delete" 
                                            data-id="<?= $row['id'] ?>" 
                                            data-name="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                            data-module="admin"
                                            data-has-foto="<?= (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg' ? 'true' : 'false') ?>"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="fas fa-users-cog"></i>
                                <p>Belum ada data admin</p>
                                <a href="tambah.php" class="btn-primary">Tambah Admin</a>
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
            <h3><i class="fas fa-exclamation-triangle" style="color: #FFD700;"></i> Konfirmasi Hapus</h3>
            <span class="modal-close">&times;</span>
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
            <a href="#" id="confirmDeleteBtn" class="btn-danger">Ya, Hapus</a>
            <button type="button" id="btnCloseModal" class="btn-secondary">Batal</button>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>