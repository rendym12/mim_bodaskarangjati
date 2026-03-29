<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM sarana WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data sarana tidak ditemukan";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_sarana = mysqli_real_escape_string($conn, $_POST['nama_sarana']);
    $ikon = mysqli_real_escape_string($conn, $_POST['ikon']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $urutan = (int)$_POST['urutan'];
    
    // Validasi
    $errors = [];
    if (empty($nama_sarana)) {
        $errors[] = "Nama sarana harus diisi";
    }
    
    $gambar = $row['gambar']; // default pakai gambar lama
    
    // Upload gambar baru jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['gambar']['size'];
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file harus JPG, JPEG, PNG, atau GIF";
        } elseif ($size > 2 * 1024 * 1024) {
            $errors[] = "Ukuran file maksimal 2MB";
        } else {
            // Hapus gambar lama jika ada
            if (!empty($row['gambar']) && file_exists("../../uploads/sarana/" . $row['gambar'])) {
                unlink("../../uploads/sarana/" . $row['gambar']);
            }
            
            $gambar = 'sarana_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = "../../uploads/sarana/" . $gambar;
            
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $errors[] = "Gagal upload gambar";
                $gambar = $row['gambar'];
            }
        }
    }
    
    // Jika tidak ada error, update database
    if (empty($errors)) {
        $query = "UPDATE sarana SET 
                  nama_sarana = '$nama_sarana',
                  ikon = " . ($ikon ? "'$ikon'" : "'fa-building'") . ",
                  keterangan = " . ($keterangan ? "'$keterangan'" : "NULL") . ",
                  gambar = " . ($gambar ? "'$gambar'" : "NULL") . ",
                  urutan = $urutan
                  WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Sarana <strong>$nama_sarana</strong> berhasil diupdate";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal mengupdate data: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper sarana-page">
    <div class="content-header">
        <h1><i class="fas fa-edit"></i> Edit Sarana</h1>
        <a href="index.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Error Messages -->
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
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fas fa-tag"></i> Nama Sarana <span style="color: red;">*</span></label>
                <input type="text" name="nama_sarana" class="form-control" required 
                       value="<?= htmlspecialchars($row['nama_sarana']) ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-icons"></i> Ikon Font Awesome</label>
                    <input type="text" name="ikon" class="form-control" 
                           value="<?= htmlspecialchars($row['ikon'] ?? 'fa-building') ?>"
                           placeholder="Contoh: fa-building, fa-book, fa-flask">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-sort-numeric-up"></i> Urutan</label>
                    <input type="number" name="urutan" class="form-control" 
                           value="<?= $row['urutan'] ?? 0 ?>" min="0">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="4"><?= htmlspecialchars($row['keterangan'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> Gambar</label>
                
                <!-- Gambar Lama -->
                <?php if (!empty($row['gambar'])): ?>
                <div style="margin-bottom: 15px; padding: 10px; background: #f8fafc; border-radius: 8px;">
                    <img src="../../uploads/sarana/<?= $row['gambar'] ?>" alt="Current" style="max-width: 100px; max-height: 100px; border-radius: 5px;">
                    <p style="margin-top: 5px; font-size: 0.9rem;"><?= $row['gambar'] ?></p>
                </div>
                <?php endif; ?>
                
                <div class="file-upload" onclick="document.getElementById('gambar').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Klik untuk ganti gambar</p>
                    <small>Format: JPG, PNG, GIF (Maks. 2MB)</small>
                    <input type="file" name="gambar" id="gambar" accept="image/*" style="display: none;">
                </div>
                
                <!-- Preview Image Baru -->
                <div id="preview-container" class="preview-container" style="display: none; margin-top: 15px;">
                    <img id="preview-image" src="#" alt="Preview" class="preview-image">
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>