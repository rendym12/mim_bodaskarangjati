<?php
include "../includes/auth.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $urutan = (int)$_POST['urutan'];
    
    $errors = [];
    if (empty($judul)) {
        $errors[] = "Judul harus diisi";
    }
    
    // Upload file foto
    $file_foto = null;
    if (isset($_FILES['file_foto']) && $_FILES['file_foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['file_foto']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['file_foto']['size'];
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file harus JPG, JPEG, PNG, atau GIF";
        } elseif ($size > 2 * 1024 * 1024) {
            $errors[] = "Ukuran file maksimal 2MB";
        } else {
            $file_foto = 'foto_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = "../../uploads/galeri_foto/" . $file_foto;
            
            if (!move_uploaded_file($_FILES['file_foto']['tmp_name'], $upload_path)) {
                $errors[] = "Gagal upload file";
                $file_foto = null;
            }
        }
    } else {
        $errors[] = "File foto harus diupload";
    }
    
    if (empty($errors)) {
        $query = "INSERT INTO galeri_foto (judul, kategori, keterangan, file_foto, urutan) 
                  VALUES ('$judul', " . ($kategori ? "'$kategori'" : "NULL") . ", " . ($keterangan ? "'$keterangan'" : "NULL") . ", '$file_foto', $urutan)";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Foto <strong>$judul</strong> berhasil ditambahkan";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal menambahkan data: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper galeri-foto-page">
    <div class="content-header">
        <h1><i class="fas fa-plus-circle"></i> Tambah Foto</h1>
        <a href="index.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data" id="fotoForm">
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Judul <span style="color: red;">*</span></label>
                <input type="text" name="judul" class="form-control" required value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : '' ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-folder"></i> Kategori</label>
                    <input type="text" name="kategori" class="form-control" value="<?= isset($_POST['kategori']) ? htmlspecialchars($_POST['kategori']) : '' ?>" placeholder="Contoh: Kegiatan, Prestasi, Wisuda">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-sort-numeric-up"></i> Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="<?= isset($_POST['urutan']) ? (int)$_POST['urutan'] : 0 ?>">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="4"><?= isset($_POST['keterangan']) ? htmlspecialchars($_POST['keterangan']) : '' ?></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> File Foto <span style="color: red;">*</span></label>
                <div class="file-upload" id="fileUploadArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Klik untuk upload foto</p>
                    <small>Format: JPG, PNG, GIF (Maks. 2MB)</small>
                    <input type="file" name="file_foto" id="file_foto" accept="image/*" style="display: none;" required>
                </div>
                <div id="previewContainer" class="preview-container" style="display: none; margin-top: 15px;">
                    <img id="previewImage" src="#" alt="Preview" class="preview-image" style="max-width: 150px; border-radius: 5px;">
                </div>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn-secondary">Reset</button>
                <button type="submit" class="btn-primary" id="btnSubmit"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>