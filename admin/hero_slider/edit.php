<?php
include "../includes/auth.php";

// ===== PERBAIKAN UPLOAD 10MB =====
@ini_set('upload_max_filesize', '10M');
@ini_set('post_max_size', '20M');
@ini_set('max_execution_time', '300');
@ini_set('memory_limit', '256M');

$max_upload = ini_get('upload_max_filesize');
// =================================

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM hero_slider WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data slider tidak ditemukan";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $subjudul = mysqli_real_escape_string($conn, $_POST['subjudul'] ?? '');
    $badge = mysqli_real_escape_string($conn, $_POST['badge'] ?? '');
    $tombol_text = mysqli_real_escape_string($conn, $_POST['tombol_text'] ?? '');
    $tombol_link = mysqli_real_escape_string($conn, $_POST['tombol_link'] ?? '');
    $urutan = (int)$_POST['urutan'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $errors = [];
    if (empty($judul)) {
        $errors[] = "Judul harus diisi";
    }
    
    $gambar = $row['gambar'];
    
    // Proses upload gambar baru jika ada - MAKSIMAL 10MB
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['gambar']['size'];
        $max_size = 10 * 1024 * 1024; // 10MB
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file harus JPG, JPEG, PNG, GIF, atau WEBP";
        } elseif ($size > $max_size) {
            $errors[] = "Ukuran file maksimal 10MB! (Ukuran file Anda: " . round($size / (1024 * 1024), 2) . "MB)";
        } else {
            // Hapus gambar lama jika ada
            if (!empty($row['gambar']) && file_exists("../../uploads/hero/" . $row['gambar'])) {
                unlink("../../uploads/hero/" . $row['gambar']);
            }
            
            $gambar = 'slider_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = "../../uploads/hero/" . $gambar;
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                // Sukses upload
            } else {
                $errors[] = "Gagal upload gambar";
                $gambar = $row['gambar'];
            }
        }
    }
    
    if (empty($errors)) {
        $query = "UPDATE hero_slider SET 
                  judul = '$judul',
                  subjudul = " . ($subjudul ? "'$subjudul'" : "NULL") . ",
                  badge = " . ($badge ? "'$badge'" : "NULL") . ",
                  tombol_text = " . ($tombol_text ? "'$tombol_text'" : "NULL") . ",
                  tombol_link = " . ($tombol_link ? "'$tombol_link'" : "NULL") . ",
                  gambar = '$gambar',
                  urutan = $urutan,
                  status = '$status'
                  WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Slider <strong>$judul</strong> berhasil diupdate";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal mengupdate data: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper slider-page">
    <div class="content-header">
        <h1><i class="fas fa-edit"></i> Edit Slide</h1>
        <a href="index.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- NOTIFICATION CONTAINER -->
    <div class="notification-container">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible">
                <i class="fas fa-exclamation-circle"></i>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data" id="sliderForm">
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Judul <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control" required 
                       value="<?= htmlspecialchars($row['judul']) ?>">
            </div>

            <div class="form-group">
                <label><i class="fas fa-heading"></i> Sub Judul</label>
                <textarea name="subjudul" class="form-control" rows="2"><?= htmlspecialchars($row['subjudul'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-tag"></i> Badge</label>
                <input type="text" name="badge" class="form-control" 
                       value="<?= htmlspecialchars($row['badge'] ?? '') ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-font"></i> Teks Tombol</label>
                    <input type="text" name="tombol_text" class="form-control" 
                           value="<?= htmlspecialchars($row['tombol_text'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-link"></i> Link Tombol</label>
                    <input type="text" name="tombol_link" class="form-control" 
                           value="<?= htmlspecialchars($row['tombol_link'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-sort-numeric-up"></i> Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="<?= $row['urutan'] ?>" min="0">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-toggle-on"></i> Status</label>
                    <select name="status" class="form-control">
                        <option value="aktif" <?= $row['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= $row['status'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Gambar Lama -->
            <?php if (!empty($row['gambar'])): ?>
            <div class="form-group">
                <label><i class="fas fa-image"></i> Gambar Saat Ini</label>
                <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e1e1e1; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 25px; flex-wrap: wrap;">
                        <img src="../../uploads/hero/<?= $row['gambar'] ?>" alt="Current" 
                             style="max-width: 150px; max-height: 100px; border-radius: 10px; border: 3px solid #FFD700; object-fit: cover;">
                        <div>
                            <p style="margin: 0 0 5px 0;"><i class="fas fa-image" style="color: #FFD700;"></i> <strong><?= $row['gambar'] ?></strong></p>
                            <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                <i class="fas fa-info-circle"></i> Upload gambar baru untuk mengganti
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- BAGIAN FORM UPLOAD GAMBAR -->
            <div class="form-group">
                <label><i class="fas fa-image"></i> Ganti Gambar <?= empty($row['gambar']) ? '<span class="text-danger">*</span>' : '' ?></label>
                
                <div style="background: #e3f2fd; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; border-left: 5px solid #0B3D91;">
                    <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                        <i class="fas fa-info-circle" style="color: #0B3D91; font-size: 1.5rem;"></i>
                        <div style="flex: 1;">
                            <span style="color: #0B3D91; font-weight: 600; display: block; margin-bottom: 3px;">Informasi Upload Gambar</span>
                            <span style="color: #2c3e50; font-size: 0.9rem;">Format: JPG, PNG, GIF, WEBP | Maksimal <strong>10MB</strong></span>
                        </div>
                        <span style="background: white; padding: 5px 15px; border-radius: 30px; font-size: 0.9rem; font-weight: 600; color: #0B3D91; border: 1px solid #0B3D91;">
                            <i class="fas fa-server"></i> Server: <?= $max_upload ?>
                        </span>
                    </div>
                </div>
                
                <div style="position: relative; margin-bottom: 15px;">
                    <input type="file" name="gambar" id="gambarInput" accept="image/*" 
                           style="width: 100%; padding: 40px 20px; border: 3px dashed #0B3D91; border-radius: 16px; 
                                  background: #f0f7ff; cursor: pointer; opacity: 0; position: absolute; top: 0; left: 0; z-index: 2;">
                    
                    <div style="width: 100%; padding: 40px 20px; border: 3px dashed #0B3D91; border-radius: 16px; 
                                background: #f0f7ff; text-align: center; box-sizing: border-box; transition: all 0.3s ease;"
                         id="fileUploadArea">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #0B3D91; opacity: 0.5; margin-bottom: 10px; display: block;"></i>
                        <p style="font-size: 1.1rem; font-weight: 600; color: #0B3D91; margin: 0 0 5px 0;">Klik atau tarik file ke sini</p>
                        <p style="color: #666; font-size: 0.85rem; margin: 0;">Format: JPG, PNG, GIF, WEBP (Maks. 10MB)</p>
                    </div>
                </div>
                
                <div id="fileInfo" style="display: none; margin-top: 15px;">
                    <div style="background: #e8f5e9; padding: 15px; border-radius: 10px; border-left: 4px solid #28a745;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-check-circle" style="color: #28a745; font-size: 1.3rem;"></i>
                            <div>
                                <span style="font-weight: 600; color: #2e7d32; display: block; margin-bottom: 3px;" id="fileName"></span>
                                <span style="color: #2e7d32; font-size: 0.9rem;" id="fileSize"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="previewContainer" style="display: none; margin-top: 20px;">
                    <h4 style="margin-bottom: 12px; color: #0B3D91; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-eye"></i> Preview Gambar Baru:
                    </h4>
                    <div style="position: relative; display: inline-block; max-width: 100%;">
                        <img id="previewImage" src="#" alt="Preview" 
                             style="max-width: 100%; max-height: 300px; border-radius: 12px; border: 4px solid #FFD700; box-shadow: 0 10px 25px -10px #0B3D91;">
                        <div style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 5px 12px; border-radius: 30px; font-size: 0.8rem;">
                            <i class="fas fa-file-image"></i> <span id="previewFileSize"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary" id="btnSubmit"><i class="fas fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>