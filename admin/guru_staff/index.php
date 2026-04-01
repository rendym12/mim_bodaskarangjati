<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $q = mysqli_query($conn, "SELECT nama, foto FROM guru_staff WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    $file_deleted = false;
    $check = mysqli_query($conn, "SELECT * FROM guru_staff WHERE id = $id");
    
    if (mysqli_num_rows($check) > 0) {
        $nama = $data['nama'];
        
        if ($data && !empty($data['foto']) && $data['foto'] != 'default-avatar.jpg' && file_exists("../../uploads/guru/" . $data['foto'])) {
            if (unlink("../../uploads/guru/" . $data['foto'])) {
                $file_deleted = true;
            }
        }
        
        mysqli_query($conn, "DELETE FROM guru_staff WHERE id = $id");
        
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

// ========== PROSES UPLOAD STRUKTUR ==========
if (isset($_POST['upload_struktur'])) {
    $errors = [];
    
    if (isset($_FILES['gambar_struktur']) && $_FILES['gambar_struktur']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['gambar_struktur']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['gambar_struktur']['size'];
        $max_size = 5 * 1024 * 1024;
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file harus JPG, JPEG, PNG, atau WEBP";
        } elseif ($size > $max_size) {
            $errors[] = "Ukuran file maksimal 5MB";
        } else {
            if (!file_exists('../../uploads/struktur')) {
                mkdir('../../uploads/struktur', 0777, true);
            }
            
            $old_files = glob('../../uploads/struktur/*.*');
            foreach ($old_files as $old_file) {
                if (is_file($old_file)) unlink($old_file);
            }
            
            $new_filename = 'struktur_organisasi.' . $ext;
            $upload_path = "../../uploads/struktur/" . $new_filename;
            
            if (move_uploaded_file($_FILES['gambar_struktur']['tmp_name'], $upload_path)) {
                $_SESSION['success_struktur'] = "Gambar Struktur Organisasi berhasil diupload!";
            } else {
                $errors[] = "Gagal upload gambar";
            }
        }
    } else {
        $errors[] = "Pilih file gambar terlebih dahulu";
    }
    
    if (!empty($errors)) {
        $_SESSION['error_struktur'] = implode("<br>", $errors);
    }
    
    header("Location: index.php");
    exit;
}

// ========== PROSES HAPUS STRUKTUR ==========
if (isset($_GET['delete_struktur'])) {
    $struktur_files = glob('../../uploads/struktur/*.*');
    $deleted = false;
    
    foreach ($struktur_files as $file) {
        if (is_file($file) && unlink($file)) $deleted = true;
    }
    
    if ($deleted) {
        $_SESSION['success_struktur'] = "Gambar Struktur Organisasi berhasil dihapus!";
    } else {
        $_SESSION['error_struktur'] = "Gagal menghapus gambar";
    }
    
    header("Location: index.php");
    exit;
}

// ========== AMBIL DATA GURU ==========
$query = mysqli_query($conn, "SELECT * FROM guru_staff ORDER BY urutan ASC, id DESC");

// ========== CEK APAKAH ADA GAMBAR STRUKTUR ==========
$struktur_exists = false;
$struktur_file = '';
$struktur_files = glob('../../uploads/struktur/*.*');
if (!empty($struktur_files)) {
    $struktur_exists = true;
    $struktur_file = basename($struktur_files[0]);
}

include "../includes/header.php";
?>

<div class="content-wrapper guru-page">
    <div class="content-header">
        <h1><i class="fas fa-chalkboard-teacher"></i> Kelola Guru & Staff</h1>
        <div style="display: flex; gap: 10px;">
            <a href="tambah.php" class="btn-primary">
                <i class="fas fa-plus"></i> Tambah Guru/Staff
            </a>
            <button class="btn-secondary" id="btnKelolaStruktur">
                <i class="fas fa-sitemap"></i> Kelola Struktur
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi Guru -->
    <?php if (isset($_SESSION['success'])): ?>
        <?php if (is_array($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success']['message'] ?>
                <?php if ($_SESSION['success']['file_deleted']): ?>
                    <div class="file-info"><i class="fas fa-camera"></i> File foto ikut terhapus</div>
                <?php endif; ?>
                <button type="button" class="close">&times;</button>
            </div>
        <?php else: ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="close">&times;</button>
            </div>
        <?php endif; ?>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible">
            <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
            <button type="button" class="close">&times;</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Alert Notifikasi Struktur -->
    <?php if (isset($_SESSION['success_struktur'])): ?>
        <div class="alert alert-success alert-dismissible">
            <i class="fas fa-check-circle"></i> <?= $_SESSION['success_struktur'] ?>
            <button type="button" class="close">&times;</button>
        </div>
        <?php unset($_SESSION['success_struktur']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_struktur'])): ?>
        <div class="alert alert-danger alert-dismissible">
            <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error_struktur'] ?>
            <button type="button" class="close">&times;</button>
        </div>
        <?php unset($_SESSION['error_struktur']); ?>
    <?php endif; ?>

    <!-- Card Tabel Guru -->
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
                                    <img src="../../uploads/guru/<?= $row['foto'] ?>" alt="Foto" class="guru-avatar">
                                <?php else: ?>
                                    <i class="fas fa-user-circle avatar-placeholder"></i>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                            <td><?= !empty($row['nip']) ? htmlspecialchars($row['nip']) : '-' ?></td>
                            <td><?= !empty($row['jabatan']) ? htmlspecialchars($row['jabatan']) : '-' ?></td>
                            <td><?= !empty($row['mapel']) ? htmlspecialchars($row['mapel']) : '-' ?></td>
                            <td><?= $row['urutan'] ?? '0' ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="detail.php?id=<?= $row['id'] ?>" class="btn-view"><i class="fas fa-eye"></i></a>
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i></a>
                                    <a href="#" class="btn-delete" data-id="<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['nama']) ?>" data-has-foto="<?= (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg') ? 'true' : 'false' ?>"><i class="fas fa-trash"></i></a>
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

<!-- MODAL KELOLA STRUKTUR ORGANISASI -->
<div id="strukturModal" class="modal">
    <div class="modal-content modal-sm">
        <div class="modal-header">
            <h3><i class="fas fa-sitemap"></i> Kelola Struktur Organisasi</h3>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body">
            <!-- Preview Gambar Saat Ini -->
            <div id="strukturPreviewContainer">
                <?php if ($struktur_exists): ?>
                <div class="current-struktur">
                    <h4>Gambar Saat Ini</h4>
                    <img src="../../uploads/struktur/<?= $struktur_file ?>" alt="Struktur Organisasi">
                    <p><?= $struktur_file ?></p>
                    <a href="?delete_struktur=1" class="btn-danger" id="btnDeleteStruktur" onclick="return confirm('Yakin ingin menghapus gambar struktur organisasi?')">Hapus Gambar</a>
                </div>
                <?php else: ?>
                <div class="no-struktur">
                    <i class="fas fa-info-circle"></i>
                    <p>Belum ada gambar struktur organisasi</p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Form Upload -->
            <form method="POST" enctype="multipart/form-data" id="strukturForm">
                <div class="form-group">
                    <label>Upload Gambar Baru</label>
                    <div class="file-upload" id="strukturUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Klik untuk pilih gambar</p>
                        <small>Format: JPG, PNG, WEBP (Maks. 5MB)</small>
                        <input type="file" name="gambar_struktur" id="strukturInput" accept="image/*">
                    </div>
                    <div id="strukturPreview" style="display: none;">
                        <img id="strukturPreviewImg" src="#" alt="Preview">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="btnCloseStrukturModal">Batal</button>
                    <button type="submit" name="upload_struktur" class="btn-primary" id="btnUploadStruktur">Upload Gambar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS GURU -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</h3>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus data berikut?</p>
            <p class="delete-item-name" id="deleteItemName"></p>
            <div id="fileWarning" style="display: none;">
                <p><i class="fas fa-exclamation-circle"></i> <span id="fileWarningText"></span></p>
            </div>
            <p class="warning-text"><i class="fas fa-exclamation-circle"></i> Data yang sudah dihapus tidak dapat dikembalikan!</p>
        </div>
        <div class="modal-footer">
            <a href="#" id="confirmDeleteBtn" class="btn-danger">Ya, Hapus</a>
            <button type="button" class="btn-secondary" id="btnCloseModal">Batal</button>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>