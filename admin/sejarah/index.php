<?php
include "../includes/auth.php";

// Ambil data sejarah (hanya 1 baris)
$query = mysqli_query($conn, "SELECT * FROM sejarah ORDER BY id DESC LIMIT 1");
$sejarah = mysqli_fetch_assoc($query);

// Jika data belum ada, buat default
if (!$sejarah) {
    // Insert data default
    $default_judul = "Sejarah MI Muhammadiyah Bodaskarangjati";
    $default_tahun = "1980";
    $default_isi = "<p>MI Muhammadiyah Bodaskarangjati didirikan pada tahun 1980 atas prakarsa tokoh-tokoh Muhammadiyah di wilayah Bodaskarangjati. Madrasah ini berdiri dengan tujuan memberikan pendidikan dasar yang berkualitas dengan landasan nilai-nilai Islam dan ke-Muhammadiyahan.</p>
    <p>Pada awal berdirinya, madrasah hanya memiliki 3 ruang kelas dan 5 orang tenaga pengajar. Seiring berjalannya waktu, MI Muhammadiyah Bodaskarangjati terus berkembang dan kini telah memiliki fasilitas yang lebih lengkap serta tenaga pendidik yang profesional.</p>
    <p>Hingga saat ini, MI Muhammadiyah Bodaskarangjati telah meluluskan ribuan siswa yang tersebar di berbagai penjuru dan berkontribusi positif di masyarakat.</p>";
    
    mysqli_query($conn, "INSERT INTO sejarah (judul, tahun_berdiri, isi_sejarah) VALUES ('$default_judul', $default_tahun, '$default_isi')");
    
    // Ambil data yang baru diinsert
    $query = mysqli_query($conn, "SELECT * FROM sejarah ORDER BY id DESC LIMIT 1");
    $sejarah = mysqli_fetch_assoc($query);
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $tahun_berdiri = (int)$_POST['tahun_berdiri'];
    $isi_sejarah = mysqli_real_escape_string($conn, $_POST['isi_sejarah']);
    
    $errors = [];
    
    // Upload gambar baru jika ada
    $gambar = $sejarah['gambar'] ?? null;
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
            if (!empty($sejarah['gambar']) && file_exists("../../uploads/" . $sejarah['gambar'])) {
                unlink("../../uploads/" . $sejarah['gambar']);
            }
            
            $gambar = 'sejarah_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['gambar']['tmp_name'], '../../uploads/' . $gambar);
        }
    }
    
    if (empty($errors)) {
        $sql = "UPDATE sejarah SET 
                judul = '$judul',
                tahun_berdiri = $tahun_berdiri,
                isi_sejarah = '$isi_sejarah',
                gambar = " . ($gambar ? "'$gambar'" : "NULL") . "
                WHERE id = {$sejarah['id']}";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success'] = "Data sejarah berhasil diperbarui!";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper sejarah-page">
    <div class="content-header">
        <h1><i class="fas fa-history"></i> Edit Sejarah Madrasah</h1>
        <a href="../dashboard.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>

    <!-- NOTIFICATION CONTAINER -->
    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>

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
        <form method="POST" enctype="multipart/form-data" id="sejarahForm">
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Judul Sejarah</label>
                <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($sejarah['judul'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-calendar"></i> Tahun Berdiri</label>
                <input type="number" name="tahun_berdiri" class="form-control" value="<?= $sejarah['tahun_berdiri'] ?? date('Y') ?>" min="1900" max="<?= date('Y') ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> Gambar</label>
                
                <?php if (!empty($sejarah['gambar'])): ?>
                <div class="current-file" id="currentFile">
                    <img src="../../uploads/<?= $sejarah['gambar'] ?>" alt="Current" style="max-width: 100px; border-radius: 8px;">
                    <span class="file-name"><i class="fas fa-image"></i> <?= $sejarah['gambar'] ?></span>
                    <span class="text-muted">Kosongkan jika tidak ingin mengganti</span>
                </div>
                <?php endif; ?>
                
                <div class="file-upload" id="fileUploadArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Klik untuk upload gambar</p>
                    <small>Format: JPG, PNG, GIF (Maks. 2MB)</small>
                    <input type="file" name="gambar" id="gambar" accept="image/*" style="display: none;">
                </div>
                <div id="previewContainer" class="preview-container" style="display: none; margin-top: 15px;">
                    <img id="previewImage" src="#" alt="Preview" class="preview-image" style="max-width: 150px; border-radius: 10px;">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Isi Sejarah</label>
                <textarea name="isi_sejarah" id="editor" class="form-control" rows="12" required><?= htmlspecialchars($sejarah['isi_sejarah'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary" id="btnSubmit">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="../dashboard.php" class="btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>

<?php include "../includes/footer.php"; ?>