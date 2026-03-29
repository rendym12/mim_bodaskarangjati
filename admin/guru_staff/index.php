<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Ambil data foto untuk dihapus
    $q = mysqli_query($conn, "SELECT nama, foto FROM guru_staff WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    $file_deleted = false;
    
    // Cek apakah data ada
    $check = mysqli_query($conn, "SELECT * FROM guru_staff WHERE id = $id");
    
    if (mysqli_num_rows($check) > 0) {
        $nama = $data['nama'];
        
        // Hapus file foto jika ada dan bukan default
        if ($data && !empty($data['foto']) && $data['foto'] != 'default-avatar.jpg' && file_exists("../../uploads/guru/" . $data['foto'])) {
            if (unlink("../../uploads/guru/" . $data['foto'])) {
                $file_deleted = true;
            }
        }
        
        mysqli_query($conn, "DELETE FROM guru_staff WHERE id = $id");
        
        // Buat pesan notifikasi
        $_SESSION['success'] = [
            'message' => "Data guru/staff <strong>\"$nama\"</strong> berhasil dihapus",
            'file_deleted' => $file_deleted,
            'type' => 'guru'
        ];
    } else {
        $_SESSION['error'] = "Data guru/staff tidak ditemukan";
    }
    
    header("Location: index.php");
    exit;
}

// ========== AMBIL DATA ==========
$query = mysqli_query($conn, "SELECT * FROM guru_staff ORDER BY urutan ASC, id DESC");

// ========== INCLUDE HEADER ==========
include "../includes/header.php";
?>

<div class="content-wrapper guru-page">
    <!-- Content Header -->
    <div class="content-header">
        <h1><i class="fas fa-chalkboard-teacher"></i> Kelola Guru & Staff</h1>
        <a href="tambah.php" class="btn-primary">
            <i class="fas fa-plus"></i> Tambah Guru/Staff
        </a>
    </div>

    <!-- Alert Notifikasi Sukses -->
    <?php if (isset($_SESSION['success'])): ?>
        <?php if (is_array($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i>
                <?= $_SESSION['success']['message'] ?>
                
                <?php if ($_SESSION['success']['file_deleted']): ?>
                    <div style="margin-top: 10px; padding: 8px; background: #d4edda; border-radius: 5px;">
                        <small>
                            <i class="fas fa-camera"></i> File foto ikut terhapus
                        </small>
                    </div>
                <?php endif; ?>
                
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php else: ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i>
                <?= $_SESSION['success'] ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Alert Notifikasi Error -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible">
            <i class="fas fa-exclamation-circle"></i>
            <?= $_SESSION['error'] ?>
            <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Card Tabel -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Daftar Guru & Staff</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Foto</th>
                            <th width="20%">Nama</th>
                            <th width="15%">NIP</th>
                            <th width="15%">Jabatan</th>
                            <th width="15%">Mata Pelajaran</th>
                            <th width="5%">Urutan</th>
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
                                <?php if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg'): ?>
                                    <img src="../../uploads/guru/<?= $row['foto'] ?>" alt="Foto" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;">
                                <?php else: ?>
                                    <i class="fas fa-user-circle" style="font-size: 2.5rem; color: #ccc;"></i>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                            <td><?= !empty($row['nip']) ? htmlspecialchars($row['nip']) : '-' ?></td>
                            <td><?= !empty($row['jabatan']) ? htmlspecialchars($row['jabatan']) : '-' ?></td>
                            <td><?= !empty($row['mapel']) ? htmlspecialchars($row['mapel']) : '-' ?></td>
                            <td><?= $row['urutan'] ?? '0' ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="detail.php?id=<?= $row['id'] ?>" class="btn-view" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" onclick="confirmDeleteGuru(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nama']) ?>', <?= (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg' ? 'true' : 'false') ?>)" class="btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <p>Belum ada data guru/staff</p>
                                <a href="tambah.php" class="btn-primary">Tambah Guru/Staff</a>
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
            <p id="fileWarning" style="display: none; color: #ef4444; background: #fee2e2; padding: 8px; border-radius: 5px;">
                <i class="fas fa-exclamation-circle"></i>
                <span id="fileWarningText"></span>
            </p>
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