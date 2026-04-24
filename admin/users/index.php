<?php
include "../includes/auth.php";

// CEK JUMLAH ADMIN SAAT INI
$query_jumlah = mysqli_query($conn, "SELECT COUNT(*) as total FROM admin_users");
$jumlah_admin = mysqli_fetch_assoc($query_jumlah)['total'];
$max_admin = 3;

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    if ($id != $_SESSION['admin_id']) {
        $_SESSION['error'] = "Anda tidak bisa menghapus akun orang lain!";
        header("Location: index.php");
        exit;
    }
    
    $check = mysqli_query($conn, "SELECT * FROM admin_users WHERE id = $id");
    
    if (mysqli_num_rows($check) > 0) {
        $q = mysqli_query($conn, "SELECT nama_lengkap, foto FROM admin_users WHERE id = $id");
        $data = mysqli_fetch_assoc($q);
        $nama = $data['nama_lengkap'];
        
        $hasFoto = false;
        if ($data && !empty($data['foto']) && $data['foto'] != 'default-avatar.jpg' && file_exists("../../uploads/" . $data['foto'])) {
            if (unlink("../../uploads/" . $data['foto'])) {
                $hasFoto = true;
            }
        }
        
        if (mysqli_query($conn, "DELETE FROM admin_users WHERE id = $id")) {
            if ($id == $_SESSION['admin_id']) {
                session_destroy();
                header("Location: ../login.php");
                exit;
            }
            
            if ($hasFoto) {
                $_SESSION['success'] = [
                    'message' => "Akun <strong>\"$nama\"</strong> berhasil dihapus",
                    'file_deleted' => true,
                    'type' => 'admin'
                ];
            } else {
                $_SESSION['success'] = [
                    'message' => "Akun <strong>\"$nama\"</strong> berhasil dihapus",
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

$query = mysqli_query($conn, "SELECT * FROM admin_users ORDER BY id DESC");

include "../includes/header.php";
?>

<div class="content-wrapper users-page">
    
    <!-- HEADER -->
    <div class="content-header">
        <h1><i class="fas fa-users-cog"></i> Kelola Admin</h1>
        <?php if ($jumlah_admin < $max_admin): ?>
            <a href="tambah.php" class="btn-primary">
                <i class="fas fa-plus"></i> Tambah Admin
            </a>
        <?php else: ?>
            <button class="btn-secondary" disabled style="cursor: not-allowed;">
                <i class="fas fa-ban"></i> Kuota Penuh
            </button>
        <?php endif; ?>
    </div>

    <!-- NOTIFICATION -->
    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <?php if (is_array($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['success']['message'] ?>
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

    <!-- TABEL -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Daftar Administrator</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">Foto</th>
                            <th width="25%">Nama</th>
                            <th width="15%">Username</th>
                            <th width="25%">Email</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)): 
                                $is_me = ($row['id'] == $_SESSION['admin_id']);
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <?php if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg'): ?>
                                    <img src="../../uploads/<?= $row['foto'] ?>" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                                <?php else: ?>
                                    <i class="fas fa-user-circle" style="font-size: 28px; color: #cbd5e1;"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['nama_lengkap']) ?>
                                <?php if ($is_me): ?>
                                    <span style="background: #FFD700; color: #0B3D91; font-size: 9px; padding: 2px 6px; border-radius: 20px; margin-left: 5px;">Anda</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td>
                                <?php if ($is_me): ?>
                                    <?= htmlspecialchars($row['email'] ?? '-') ?>
                                <?php else: ?>
                                    <?php 
                                    $email = $row['email'] ?? '';
                                    if (!empty($email)) {
                                        $parts = explode('@', $email);
                                        echo str_repeat('•', strlen($parts[0])) . '@' . ($parts[1] ?? '');
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($is_me): ?>
                                    <div style="display: flex; gap: 6px; justify-content: center;">
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn-delete" 
                                                data-id="<?= $row['id'] ?>" 
                                                data-name="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                                data-module="admin"
                                                data-has-foto="<?= (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg' ? 'true' : 'false') ?>"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 12px;">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="fas fa-users-cog"></i>
                                <p>Belum ada administrator</p>
                                <a href="tambah.php" class="btn-primary">Tambah Admin</a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- INFO PENTING (DI BAWAH TABEL) - MINIMALIS -->
    <div style="margin-top: 15px; padding: 10px 0; border-top: 1px solid #e2e8f0; display: flex; align-items: center; gap: 20px; flex-wrap: wrap; font-size: 12px; color: #64748b;">
        <span>
            <i class="fas fa-users"></i> Admin: <?= $jumlah_admin ?>/<?= $max_admin ?>
        </span>
        <span>
            <i class="fas fa-chart-line"></i> Terisi: <?= round(($jumlah_admin / $max_admin) * 100) ?>%
        </span>
        <?php if ($jumlah_admin < $max_admin): ?>
            <span style="color: #28a745;">
                <i class="fas fa-plus-circle"></i> Sisa: <?= $max_admin - $jumlah_admin ?> slot
            </span>
        <?php else: ?>
            <span style="color: #dc3545;">
                <i class="fas fa-ban"></i> Kuota penuh
            </span>
        <?php endif; ?>
        <span style="margin-left: auto;">
            <i class="fas fa-lock"></i> Hanya bisa edit akun sendiri
        </span>
    </div>
</div>

<!-- MODAL HAPUS -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</h3>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body">
            <p>Hapus akun <strong id="deleteItemName"></strong>?</p>
            <div id="fileWarningContainer" style="display: none;">
                <i class="fas fa-exclamation-circle"></i>
                <span id="fileWarningText"></span>
            </div>
            <div style="background: #fee2e2; padding: 8px; border-radius: 6px; font-size: 12px;">
                <i class="fas fa-exclamation-triangle"></i> Data tidak dapat dikembalikan!
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" id="confirmDeleteBtn" class="btn-danger">Hapus</a>
            <button type="button" id="btnCloseModal" class="btn-secondary">Batal</button>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>