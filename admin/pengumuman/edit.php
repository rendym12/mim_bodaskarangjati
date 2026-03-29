<?php
require_once dirname(__DIR__) . '/includes/auth.php';

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM pengumuman WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal = $_POST['tanggal'];
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $status = $_POST['status'];
    
    $gambar = $row['gambar'];
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        // Hapus gambar lama
        if (!empty($row['gambar']) && file_exists("../../uploads/pengumuman/" . $row['gambar'])) {
            unlink("../../uploads/pengumuman/" . $row['gambar']);
        }
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'pengumuman_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../../uploads/pengumuman/" . $gambar);
    }
    
    $file_lampiran = $row['file_lampiran'];
    if (isset($_FILES['file_lampiran']) && $_FILES['file_lampiran']['error'] == 0) {
        // Hapus lampiran lama
        if (!empty($row['file_lampiran']) && file_exists("../../uploads/lampiran/" . $row['file_lampiran'])) {
            unlink("../../uploads/lampiran/" . $row['file_lampiran']);
        }
        $ext = pathinfo($_FILES['file_lampiran']['name'], PATHINFO_EXTENSION);
        $file_lampiran = 'lampiran_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['file_lampiran']['tmp_name'], "../../uploads/lampiran/" . $file_lampiran);
    }
    
    $query = "UPDATE pengumuman SET 
              judul='$judul', 
              isi='$isi', 
              tanggal='$tanggal', 
              penulis='$penulis', 
              status='$status',
              gambar=" . ($gambar ? "'$gambar'" : "NULL") . ",
              file_lampiran=" . ($file_lampiran ? "'$file_lampiran'" : "NULL") . "
              WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Pengumuman berhasil diupdate";
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal mengupdate pengumuman: " . mysqli_error($conn);
    }
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="content-wrapper pengumuman-page">
    <div class="content-header">
        <h1><i class="fas fa-edit"></i> Edit Pengumuman</h1>
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
                <input type="text" name="judul" class="form-control" required value="<?= htmlspecialchars($row['judul']) ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control" required value="<?= $row['tanggal'] ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Penulis <span class="text-danger">*</span></label>
                    <input type="text" name="penulis" class="form-control" required value="<?= htmlspecialchars($row['penulis']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Isi Pengumuman <span class="text-danger">*</span></label>
                <textarea name="isi" class="form-control" rows="8" required><?= htmlspecialchars($row['isi']) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Gambar</label>
                    <?php if (!empty($row['gambar'])): ?>
                        <div class="mb-2">
                            <img src="../../uploads/pengumuman/<?= $row['gambar'] ?>" style="max-width: 100px; max-height: 80px; border: 1px solid #ddd; padding: 5px;">
                            <br>
                            <small class="text-muted">Gambar saat ini. Upload baru untuk mengganti.</small>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-paperclip"></i> File Lampiran</label>
                    <?php if (!empty($row['file_lampiran'])): ?>
                        <div class="mb-2">
                            <i class="fas fa-file"></i> <?= $row['file_lampiran'] ?>
                            <br>
                            <small class="text-muted">Upload baru untuk mengganti.</small>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="file_lampiran" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-eye"></i> Status</label>
                <select name="status" class="form-control">
                    <option value="publish" <?= $row['status'] == 'publish' ? 'selected' : '' ?>>Publish</option>
                    <option value="draft" <?= $row['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>