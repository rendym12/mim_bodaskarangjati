<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = mysqli_prepare($conn, "SELECT nama, foto FROM guru_staff WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    $file_deleted = false;
    
    $check_stmt = mysqli_prepare($conn, "SELECT id FROM guru_staff WHERE id = ?");
    mysqli_stmt_bind_param($check_stmt, "i", $id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        $nama = $data['nama'];
        
        if ($data && !empty($data['foto']) && $data['foto'] != 'default-avatar.jpg') {
            $foto_path = "../../uploads/guru/" . $data['foto'];
            if (file_exists($foto_path)) {
                if (unlink($foto_path)) {
                    $file_deleted = true;
                }
            }
        }
        
        $delete_stmt = mysqli_prepare($conn, "DELETE FROM guru_staff WHERE id = ?");
        mysqli_stmt_bind_param($delete_stmt, "i", $id);
        mysqli_stmt_execute($delete_stmt);
        mysqli_stmt_close($delete_stmt);

                // ========== REORDER URUTAN ==========  // + tambahkan 3 baris ini
        if ($urutan_yang_dihapus !== null && $urutan_yang_dihapus > 0) {
            mysqli_query($conn, "UPDATE guru_staff SET urutan = urutan - 1 WHERE urutan > $urutan_yang_dihapus");
        }
        
        $_SESSION['success'] = [
            'message' => "Data guru/staff <strong>\"$nama\"</strong> berhasil dihapus",
            'file_deleted' => $file_deleted,
            'type' => 'guru'
        ];
    } else {
        $_SESSION['error'] = "Data guru/staff tidak ditemukan";
    }
    
    mysqli_stmt_close($check_stmt);
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
            $upload_dir = '../../uploads/struktur/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Hapus file lama
            $old_files = glob($upload_dir . '*.*');
            if ($old_files !== false) {
                foreach ($old_files as $old_file) {
                    if (is_file($old_file)) {
                        @unlink($old_file);
                    }
                }
            }
            
            $new_filename = 'struktur_organisasi.' . $ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['gambar_struktur']['tmp_name'], $upload_path)) {
                $_SESSION['success_struktur'] = "Gambar Struktur Organisasi berhasil diupload!";
            } else {
                $errors[] = "Gagal upload gambar. Periksa permission folder.";
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
    $upload_dir = '../../uploads/struktur/';
    $struktur_files = glob($upload_dir . '*.*');
    $deleted = false;
    
    if ($struktur_files !== false) {
        foreach ($struktur_files as $file) {
            if (is_file($file) && @unlink($file)) {
                $deleted = true;
            }
        }
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
$struktur_file_url = '';
$upload_dir = '../../uploads/struktur/';

// Pastikan folder ada
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// 🔥 PERBAIKAN: Cek file tanpa GLOB_BRACE
$struktur_files = [];
$extensions = ['jpg', 'jpeg', 'png', 'webp'];

foreach ($extensions as $ext) {
    $files = glob($upload_dir . '*.' . $ext);
    if ($files !== false && !empty($files)) {
        foreach ($files as $file) {
            $struktur_files[] = $file;
        }
    }
}

// Hapus duplikat dan urutkan
$struktur_files = array_unique($struktur_files);
sort($struktur_files);

if (!empty($struktur_files)) {
    $struktur_exists = true;
    $struktur_file = basename($struktur_files[0]);
    $struktur_file_url = '../../uploads/struktur/' . $struktur_file;
    
    // Cek file benar-benar ada
    if (!file_exists($struktur_file_url)) {
        $struktur_exists = false;
        $struktur_file = '';
        $struktur_file_url = '';
    }
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
            <button class="btn-secondary" id="btnKelolaStruktur" type="button">
                <i class="fas fa-sitemap"></i> Kelola Struktur
            </button>
        </div>
    </div>

    <!-- NOTIFICATION CONTAINER -->
    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <?php if (is_array($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <i class="fas fa-check-circle"></i>
                    <?= $_SESSION['success']['message'] ?>
                    <?php if ($_SESSION['success']['file_deleted']): ?>
                        <div class="file-info"><i class="fas fa-camera"></i> File foto ikut terhapus</div>
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

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible">
                <i class="fas fa-exclamation-circle"></i>
                <?= $_SESSION['error'] ?>
                <button type="button" class="close">&times;</button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_struktur'])): ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i>
                <?= $_SESSION['success_struktur'] ?>
                <button type="button" class="close">&times;</button>
            </div>
            <?php unset($_SESSION['success_struktur']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_struktur'])): ?>
            <div class="alert alert-danger alert-dismissible">
                <i class="fas fa-exclamation-circle"></i>
                <?= $_SESSION['error_struktur'] ?>
                <button type="button" class="close">&times;</button>
            </div>
            <?php unset($_SESSION['error_struktur']); ?>
        <?php endif; ?>
    </div>

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
                                    <img src="../../uploads/guru/<?= $row['foto'] ?>" alt="Foto" class="guru-avatar">
                                <?php else: ?>
                                    <i class="fas fa-user-circle avatar-placeholder"></i>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                            <td><?= !empty($row['nip']) ? htmlspecialchars($row['nip']) : '-' ?></td>
                            <td><?= !empty($row['jabatan']) ? htmlspecialchars($row['jabatan']) : '-' ?></td>
                            <td><?= !empty($row['mapel']) ? htmlspecialchars($row['mapel']) : '-' ?></td>
                            <td class="text-center"><?= $row['urutan'] ?? '0' ?></td>
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
                                            data-name="<?= htmlspecialchars($row['nama']) ?>"
                                            data-has-foto="<?= (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg') ? 'true' : 'false' ?>"
                                            data-module="guru"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<!-- MODAL KELOLA STRUKTUR ORGANISASI - RAPI VERSION -->
<div id="strukturModal" class="modal struktur-modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-sitemap"></i> Kelola Struktur Organisasi</h3>
            <span class="modal-close">&times;</span>
        </div>
        
        <div class="modal-body">
            <!-- Current Struktur -->
            <div id="currentStrukturWrapper" class="current-wrapper" style="<?= $struktur_exists ? 'display:block' : 'display:none' ?>">
                <div class="current-struktur">
                    <h4><i class="fas fa-image"></i> Gambar Saat Ini</h4>
                    <div class="current-struktur-img-wrapper">
                        <?php if ($struktur_exists && !empty($struktur_file_url)): ?>
                            <img id="currentStrukturImg" 
                                 src="<?= $struktur_file_url ?>?v=<?= time() ?>" 
                                 alt="Struktur Organisasi">
                        <?php else: ?>
                            <div class="empty-gambar">
                                <i class="fas fa-image"></i>
                                <p>Gambar tidak tersedia</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p class="file-name" id="currentStrukturName"><?= htmlspecialchars($struktur_file) ?></p>
                    <button type="button" class="btn-delete-struktur" id="btnDeleteStruktur">
                        <i class="fas fa-trash"></i> Hapus Gambar
                    </button>
                </div>
            </div>
            
            <div id="noStrukturWrapper" class="no-wrapper" style="<?= !$struktur_exists ? 'display:flex' : 'display:none' ?>">
                <div class="no-struktur">
                    <i class="fas fa-info-circle"></i>
                    <p>Belum ada gambar struktur organisasi</p>
                    <small>Silakan upload gambar di bawah</small>
                </div>
            </div>
            
            <!-- Form Upload -->
            <form method="POST" enctype="multipart/form-data" id="strukturForm" class="struktur-form">
                <div class="upload-section">
                    <label class="upload-label">
                        <i class="fas fa-cloud-upload-alt"></i> Upload Gambar Baru
                    </label>
                    
                    <div class="file-upload" id="strukturUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Klik untuk pilih gambar</p>
                        <small>Format: JPG, JPEG, PNG, WEBP (Maks. 5MB)</small>
                        <input type="file" name="gambar_struktur" id="strukturInput" accept="image/jpeg,image/jpg,image/png,image/webp">
                    </div>
                    
                    <div id="strukturPreviewArea" class="preview-area" style="display: none;">
                        <div class="preview-wrapper">
                            <p><i class="fas fa-image"></i> Preview Gambar:</p>
                            <div class="preview-img-wrapper">
                                <img id="strukturPreviewImg" src="#" alt="Preview">
                            </div>
                            <div class="preview-buttons">
                                <button type="button" class="btn-outline" id="cancelPreviewBtn">
                                    <i class="fas fa-times"></i> Batal
                                </button>
                                <button type="button" class="btn-primary-small" id="selectAnotherBtn">
                                    <i class="fas fa-undo"></i> Pilih Ulang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer-actions">
                    <button type="button" class="btn-custom-secondary" id="btnCloseStrukturModal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" name="upload_struktur" class="btn-custom-primary" id="submitStrukturBtn">
                        <i class="fas fa-upload"></i> Upload Gambar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS GURU -->
<div id="deleteModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i> Konfirmasi Hapus</h3>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus data berikut?</p>
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