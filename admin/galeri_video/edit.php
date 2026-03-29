<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM galeri_video WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data video tidak ditemukan";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $url_video = mysqli_real_escape_string($conn, $_POST['url_video']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $urutan = (int)$_POST['urutan'];
    
    $errors = [];
    if (empty($judul)) {
        $errors[] = "Judul harus diisi";
    }
    if (empty($url_video)) {
        $errors[] = "URL video harus diisi";
    }
    
    $thumbnail = $row['thumbnail'];
    
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['thumbnail']['size'];
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file thumbnail harus JPG, JPEG, PNG, atau GIF";
        } elseif ($size > 2 * 1024 * 1024) {
            $errors[] = "Ukuran file thumbnail maksimal 2MB";
        } else {
            if (!empty($row['thumbnail']) && file_exists("../../uploads/galeri_video/" . $row['thumbnail'])) {
                unlink("../../uploads/galeri_video/" . $row['thumbnail']);
            }
            
            $thumbnail = 'thumb_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = "../../uploads/galeri_video/" . $thumbnail;
            
            if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_path)) {
                $errors[] = "Gagal upload thumbnail";
                $thumbnail = $row['thumbnail'];
            }
        }
    }
    
    if (empty($errors)) {
        $query = "UPDATE galeri_video SET 
                  judul='$judul',
                  url_video='$url_video',
                  thumbnail=" . ($thumbnail ? "'$thumbnail'" : "NULL") . ",
                  keterangan=" . ($keterangan ? "'$keterangan'" : "NULL") . ",
                  urutan=$urutan
                  WHERE id=$id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Video <strong>$judul</strong> berhasil diupdate";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal mengupdate data: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper galeri-video-page">
    <div class="content-header">
        <h1><i class="fas fa-edit"></i> Edit Video</h1>
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
        <form method="POST" enctype="multipart/form-data" id="videoForm">
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Judul Video <span style="color: red;">*</span></label>
                <input type="text" name="judul" class="form-control" required value="<?= htmlspecialchars($row['judul']) ?>">
            </div>

            <div class="form-group">
                <label><i class="fab fa-youtube"></i> URL Video <span style="color: red;">*</span></label>
                <input type="url" name="url_video" class="form-control" required value="<?= htmlspecialchars($row['url_video']) ?>">
            </div>

            <div class="form-row">
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
                <label><i class="fas fa-image"></i> Thumbnail</label>
                
                <?php if (!empty($row['thumbnail'])): ?>
                <div style="margin-bottom: 15px;">
                    <img src="../../uploads/galeri_video/<?= $row['thumbnail'] ?>" alt="Current" style="max-width: 150px; border-radius: 5px;">
                    <p style="margin-top: 5px;"><?= $row['thumbnail'] ?></p>
                </div>
                <?php endif; ?>
                
                <div class="file-upload" id="fileUploadArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Klik untuk ganti thumbnail</p>
                    <small>Format: JPG, PNG, GIF (Maks. 2MB)</small>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*" style="display: none;">
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