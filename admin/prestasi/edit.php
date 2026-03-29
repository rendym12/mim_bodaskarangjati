<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM prestasi WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data prestasi tidak ditemukan";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_prestasi = mysqli_real_escape_string($conn, $_POST['nama_prestasi']);
    $tingkat = mysqli_real_escape_string($conn, $_POST['tingkat']);
    $penyelenggara = mysqli_real_escape_string($conn, $_POST['penyelenggara']);
    $tahun = (int)$_POST['tahun'];
    $urutan = (int)$_POST['urutan'];
    
    // Validasi
    $errors = [];
    if (empty($nama_prestasi)) {
        $errors[] = "Nama prestasi harus diisi";
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
            if (!empty($row['gambar']) && file_exists("../../uploads/prestasi/" . $row['gambar'])) {
                unlink("../../uploads/prestasi/" . $row['gambar']);
            }
            
            $gambar = 'prestasi_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = "../../uploads/prestasi/" . $gambar;
            
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $errors[] = "Gagal upload gambar";
                $gambar = $row['gambar'];
            }
        }
    }
    
    // Jika tidak ada error, update database
    if (empty($errors)) {
        $query = "UPDATE prestasi SET 
                  nama_prestasi = '$nama_prestasi',
                  tingkat = " . ($tingkat ? "'$tingkat'" : "NULL") . ",
                  penyelenggara = " . ($penyelenggara ? "'$penyelenggara'" : "NULL") . ",
                  tahun = $tahun,
                  gambar = " . ($gambar ? "'$gambar'" : "NULL") . ",
                  urutan = $urutan
                  WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Prestasi <strong>$nama_prestasi</strong> berhasil diupdate";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal mengupdate data: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper prestasi-page">
    <div class="content-header">
        <h1><i class="fas fa-edit"></i> Edit Prestasi</h1>
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
                <label><i class="fas fa-tag"></i> Nama Prestasi <span style="color: red;">*</span></label>
                <input type="text" name="nama_prestasi" class="form-control" required 
                       value="<?= htmlspecialchars($row['nama_prestasi']) ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-chart-bar"></i> Tingkat</label>
                    <select name="tingkat" class="form-control">
                        <option value="">- Pilih Tingkat -</option>
                        <option value="Internasional" <?= $row['tingkat'] == 'Internasional' ? 'selected' : '' ?>>Internasional</option>
                        <option value="Nasional" <?= $row['tingkat'] == 'Nasional' ? 'selected' : '' ?>>Nasional</option>
                        <option value="Provinsi" <?= $row['tingkat'] == 'Provinsi' ? 'selected' : '' ?>>Provinsi</option>
                        <option value="Kabupaten/Kota" <?= $row['tingkat'] == 'Kabupaten/Kota' ? 'selected' : '' ?>>Kabupaten/Kota</option>
                        <option value="Kecamatan" <?= $row['tingkat'] == 'Kecamatan' ? 'selected' : '' ?>>Kecamatan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-building"></i> Penyelenggara</label>
                    <input type="text" name="penyelenggara" class="form-control" 
                           value="<?= htmlspecialchars($row['penyelenggara'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Tahun</label>
                    <input type="number" name="tahun" class="form-control" 
                           value="<?= $row['tahun'] ?? date('Y') ?>" 
                           min="2000" max="<?= date('Y') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-sort-numeric-up"></i> Urutan</label>
                    <input type="number" name="urutan" class="form-control" 
                           value="<?= $row['urutan'] ?? 0 ?>">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> Gambar</label>
                
                <!-- Gambar Lama -->
                <?php if (!empty($row['gambar'])): ?>
                <div style="margin-bottom: 15px; padding: 10px; background: #f8fafc; border-radius: 8px;">
                    <img src="../../uploads/prestasi/<?= $row['gambar'] ?>" alt="Current" style="max-width: 100px; max-height: 100px; border-radius: 5px;">
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