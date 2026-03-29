<?php
include "../includes/auth.php";

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
    
    // Upload gambar
    $gambar = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['gambar']['size'];
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file harus JPG, JPEG, PNG, atau GIF";
        } elseif ($size > 2 * 1024 * 1024) {
            $errors[] = "Ukuran file maksimal 2MB";
        } else {
            $gambar = 'prestasi_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = "../../uploads/prestasi/" . $gambar;
            
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $errors[] = "Gagal upload gambar";
                $gambar = null;
            }
        }
    }
    
    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        $query = "INSERT INTO prestasi (nama_prestasi, tingkat, penyelenggara, tahun, gambar, urutan) 
                  VALUES ('$nama_prestasi', " . ($tingkat ? "'$tingkat'" : "NULL") . ", " . ($penyelenggara ? "'$penyelenggara'" : "NULL") . ", $tahun, " . ($gambar ? "'$gambar'" : "NULL") . ", $urutan)";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Prestasi <strong>$nama_prestasi</strong> berhasil ditambahkan";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal menambahkan data: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper prestasi-page">
    <div class="content-header">
        <h1><i class="fas fa-plus-circle"></i> Tambah Prestasi</h1>
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
                       value="<?= isset($_POST['nama_prestasi']) ? htmlspecialchars($_POST['nama_prestasi']) : '' ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-chart-bar"></i> Tingkat</label>
                    <select name="tingkat" class="form-control">
                        <option value="">- Pilih Tingkat -</option>
                        <option value="Internasional" <?= (isset($_POST['tingkat']) && $_POST['tingkat'] == 'Internasional') ? 'selected' : '' ?>>Internasional</option>
                        <option value="Nasional" <?= (isset($_POST['tingkat']) && $_POST['tingkat'] == 'Nasional') ? 'selected' : '' ?>>Nasional</option>
                        <option value="Provinsi" <?= (isset($_POST['tingkat']) && $_POST['tingkat'] == 'Provinsi') ? 'selected' : '' ?>>Provinsi</option>
                        <option value="Kabupaten/Kota" <?= (isset($_POST['tingkat']) && $_POST['tingkat'] == 'Kabupaten/Kota') ? 'selected' : '' ?>>Kabupaten/Kota</option>
                        <option value="Kecamatan" <?= (isset($_POST['tingkat']) && $_POST['tingkat'] == 'Kecamatan') ? 'selected' : '' ?>>Kecamatan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-building"></i> Penyelenggara</label>
                    <input type="text" name="penyelenggara" class="form-control" 
                           value="<?= isset($_POST['penyelenggara']) ? htmlspecialchars($_POST['penyelenggara']) : '' ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Tahun</label>
                    <input type="number" name="tahun" class="form-control" 
                           value="<?= isset($_POST['tahun']) ? (int)$_POST['tahun'] : date('Y') ?>" 
                           min="2000" max="<?= date('Y') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-sort-numeric-up"></i> Urutan</label>
                    <input type="number" name="urutan" class="form-control" 
                           value="<?= isset($_POST['urutan']) ? (int)$_POST['urutan'] : 0 ?>">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> Gambar</label>
                <div class="file-upload" onclick="document.getElementById('gambar').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Klik untuk upload gambar</p>
                    <small>Format: JPG, PNG, GIF (Maks. 2MB)</small>
                    <input type="file" name="gambar" id="gambar" accept="image/*" style="display: none;">
                </div>
                
                <!-- Preview Image -->
                <div id="preview-container" class="preview-container" style="display: none; margin-top: 15px;">
                    <img id="preview-image" src="#" alt="Preview" class="preview-image">
                </div>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn-secondary" onclick="return confirm('Reset form?')">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>