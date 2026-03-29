<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM pembiasaan WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data pembiasaan tidak ditemukan";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kegiatan = mysqli_real_escape_string($conn, $_POST['nama_kegiatan']);
    $ikon = mysqli_real_escape_string($conn, $_POST['ikon']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $urutan = (int)$_POST['urutan'];
    
    $errors = [];
    if (empty($nama_kegiatan)) {
        $errors[] = "Nama kegiatan harus diisi";
    }
    
    if (empty($errors)) {
        $query = "UPDATE pembiasaan SET 
                  nama_kegiatan='$nama_kegiatan',
                  ikon=" . ($ikon ? "'$ikon'" : "'fa-sun'") . ",
                  deskripsi=" . ($deskripsi ? "'$deskripsi'" : "NULL") . ",
                  urutan=$urutan
                  WHERE id=$id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Kegiatan pembiasaan <strong>$nama_kegiatan</strong> berhasil diupdate";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal mengupdate data: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper pembiasaan-page">
    <div class="content-header">
        <h1><i class="fas fa-edit"></i> Edit Pembiasaan Pagi</h1>
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
        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-tag"></i> Nama Kegiatan <span style="color: red;">*</span></label>
                <input type="text" name="nama_kegiatan" class="form-control" required value="<?= htmlspecialchars($row['nama_kegiatan']) ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-icons"></i> Ikon Font Awesome</label>
                    <input type="text" name="ikon" class="form-control" value="<?= htmlspecialchars($row['ikon'] ?? 'fa-sun') ?>" placeholder="Contoh: fa-sun, fa-hand-sparkles, fa-book-open">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-sort-numeric-up"></i> Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="<?= $row['urutan'] ?? 0 ?>">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="5"><?= htmlspecialchars($row['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>