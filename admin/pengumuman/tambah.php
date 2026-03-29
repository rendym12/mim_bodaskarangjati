<?php
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal = $_POST['tanggal'];
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $status = $_POST['status'];
    
    // Upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../../uploads/pengumuman/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'pengumuman_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target_dir . $gambar);
    }
    
    // Upload lampiran
    $file_lampiran = '';
    if (isset($_FILES['file_lampiran']) && $_FILES['file_lampiran']['error'] == 0) {
        $target_dir = "../../uploads/lampiran/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $ext = pathinfo($_FILES['file_lampiran']['name'], PATHINFO_EXTENSION);
        $file_lampiran = 'lampiran_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['file_lampiran']['tmp_name'], $target_dir . $file_lampiran);
    }
    
    $query = "INSERT INTO pengumuman (judul, isi, tanggal, penulis, status, gambar, file_lampiran) 
              VALUES ('$judul', '$isi', '$tanggal', '$penulis', '$status', " . ($gambar ? "'$gambar'" : "NULL") . ", " . ($file_lampiran ? "'$file_lampiran'" : "NULL") . ")";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Pengumuman berhasil ditambahkan";
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal menambahkan pengumuman: " . mysqli_error($conn);
    }
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="content-wrapper pengumuman-page">
    <div class="content-header">
        <h1><i class="fas fa-plus-circle"></i> Tambah Pengumuman</h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Judul <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Penulis <span class="text-danger">*</span></label>
                    <input type="text" name="penulis" class="form-control" required value="<?= $_SESSION['admin_nama'] ?? 'Admin' ?>">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Isi Pengumuman <span class="text-danger">*</span></label>
                <textarea name="isi" class="form-control" rows="8" required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Gambar</label>
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                    <small class="text-muted">Format: JPG, PNG. Maks: 2MB</small>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-paperclip"></i> File Lampiran</label>
                    <input type="file" name="file_lampiran" class="form-control">
                    <small class="text-muted">Format: PDF, DOC, DOCX. Maks: 5MB</small>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-eye"></i> Status</label>
                <select name="status" class="form-control">
                    <option value="publish">Publish</option>
                    <option value="draft">Draft</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>