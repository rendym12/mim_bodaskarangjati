<?php
include "../includes/auth.php";

// ========== FUNGSI GESER URUTAN ==========
function shiftUrutanSarana($conn, $urutan_baru) {
    mysqli_query($conn, "UPDATE sarana SET urutan = urutan + 1 WHERE urutan >= $urutan_baru");
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
    
    if ($urutan <= 0) {
        $errors[] = "Urutan harus diisi dengan angka 1, 2, 3, dst!";
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
            $gambar = 'sarana_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = "../../uploads/sarana/" . $gambar;
            
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $errors[] = "Gagal upload gambar";
                $gambar = null;
            }
        }
    }
    
    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        // Geser urutan yang lebih besar atau sama
        shiftUrutanSarana($conn, $urutan);
        
        $query = "INSERT INTO sarana (nama_sarana, ikon, keterangan, gambar, urutan) 
                  VALUES ('$nama_sarana', " . ($ikon ? "'$ikon'" : "'fa-building'") . ", " . ($keterangan ? "'$keterangan'" : "NULL") . ", " . ($gambar ? "'$gambar'" : "NULL") . ", $urutan)";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Sarana <strong>$nama_sarana</strong> berhasil ditambahkan";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal menambahkan data: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper sarana-page">
    <div class="content-header">
        <h1><i class="fas fa-plus-circle"></i> Tambah Sarana</h1>
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
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fas fa-tag"></i> Nama Sarana <span style="color: red;">*</span></label>
                <input type="text" name="nama_sarana" class="form-control" required 
                       value="<?= isset($_POST['nama_sarana']) ? htmlspecialchars($_POST['nama_sarana']) : '' ?>"
                       placeholder="Contoh: Ruang Kelas, Laboratorium, Perpustakaan">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-icons"></i> Ikon Font Awesome</label>
                    <input type="text" name="ikon" class="form-control" 
                           value="<?= isset($_POST['ikon']) ? htmlspecialchars($_POST['ikon']) : 'fa-building' ?>"
                           placeholder="Contoh: fa-building, fa-book, fa-flask">
                    <small style="color: #6c757d;">Kosongkan untuk menggunakan ikon default (fa-building)</small>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-sort-numeric-up"></i> Urutan <span style="color: red;">*</span></label>
                    <input type="number" name="urutan" class="form-control" required min="1"
                           value="<?= isset($_POST['urutan']) ? (int)$_POST['urutan'] : '' ?>"
                           placeholder="Contoh: 1, 2, 3">
                    <small>Masukkan angka urutan. Data dengan urutan lebih besar atau sama akan bergeser ke bawah.</small>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="4" 
                          placeholder="Deskripsi singkat tentang sarana ini"><?= isset($_POST['keterangan']) ? htmlspecialchars($_POST['keterangan']) : '' ?></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> Gambar</label>
                <input type="file" name="gambar" class="form-control" accept="image/*">
                <small>Format: JPG, PNG, GIF (Maks. 2MB)</small>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn-secondary">
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