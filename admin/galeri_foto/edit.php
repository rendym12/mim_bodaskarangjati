<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM galeri_foto WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data foto tidak ditemukan";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $urutan = (int)$_POST['urutan'];
    
    $errors = [];
    if (empty($judul)) {
        $errors[] = "Judul harus diisi";
    }
    
    $file_foto = $row['file_foto'];
    
    if (isset($_FILES['file_foto']) && $_FILES['file_foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['file_foto']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['file_foto']['size'];
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file harus JPG, JPEG, PNG, atau GIF";
        } elseif ($size > 2 * 1024 * 1024) {
            $errors[] = "Ukuran file maksimal 2MB";
        } else {
            if (!empty($row['file_foto']) && file_exists("../../uploads/galeri_foto/" . $row['file_foto'])) {
                unlink("../../uploads/galeri_foto/" . $row['file_foto']);
            }
            
            $file_foto = 'foto_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = "../../uploads/galeri_foto/" . $file_foto;
            
            if (!move_uploaded_file($_FILES['file_foto']['tmp_name'], $upload_path)) {
                $errors[] = "Gagal upload file";
                $file_foto = $row['file_foto'];
            }
        }
    }
    
    if (empty($errors)) {
        $query = "UPDATE galeri_foto SET 
                  judul='$judul',
                  kategori=" . ($kategori ? "'$kategori'" : "NULL") . ",
                  keterangan=" . ($keterangan ? "'$keterangan'" : "NULL") . ",
                  file_foto='$file_foto',
                  urutan=$urutan
                  WHERE id=$id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Foto <strong>$judul</strong> berhasil diupdate";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal mengupdate data: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper galeri-foto-page">
    <div class="content-header">
        <h1><i class="fas fa-edit"></i> Edit Foto</h1>
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
                <input type="text" name="judul" class="form-control" required value="<?= htmlspecialchars($row['judul']) ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-folder"></i> Kategori</label>
                    <input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($row['kategori'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-sort-numeric-up"></i> Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="<?= $row['urutan'] ?? 0 ?>">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="4"><?= htmlspecialchars($row['keterangan'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> File Foto</label>
                
                <?php if (!empty($row['file_foto'])): ?>
                <div style="margin-bottom: 15px;">
                    <img src="../../uploads/galeri_foto/<?= $row['file_foto'] ?>" alt="Current" style="max-width: 150px; border-radius: 5px;">
                    <p style="margin-top: 5px;"><?= $row['file_foto'] ?></p>
                </div>
                <?php endif; ?>
                
                <div class="file-upload" id="fileUploadArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Klik untuk ganti foto</p>
                    <small>Format: JPG, PNG, GIF (Maks. 2MB)</small>
                    <input type="file" name="file_foto" id="file_foto" accept="image/*" style="display: none;">
                </div>
                <div id="previewContainer" class="preview-container" style="display: none; margin-top: 15px;">
                    <img id="previewImage" src="#" alt="Preview" class="preview-image" style="max-width: 150px; border-radius: 5px;">
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