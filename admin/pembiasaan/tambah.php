<?php
include "../includes/auth.php";

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
        $query = "INSERT INTO pembiasaan (nama_kegiatan, ikon, deskripsi, urutan) 
                  VALUES ('$nama_kegiatan', " . ($ikon ? "'$ikon'" : "'fa-sun'") . ", " . ($deskripsi ? "'$deskripsi'" : "NULL") . ", $urutan)";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Kegiatan pembiasaan <strong>$nama_kegiatan</strong> berhasil ditambahkan";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal menambahkan data: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper pembiasaan-page">
    <div class="content-header">
        <h1><i class="fas fa-plus-circle"></i> Tambah Pembiasaan Pagi</h1>
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
                <input type="text" name="nama_kegiatan" class="form-control" required value="<?= isset($_POST['nama_kegiatan']) ? htmlspecialchars($_POST['nama_kegiatan']) : '' ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-icons"></i> Ikon Font Awesome</label>
                    <input type="text" name="ikon" class="form-control" value="<?= isset($_POST['ikon']) ? htmlspecialchars($_POST['ikon']) : 'fa-sun' ?>" placeholder="Contoh: fa-sun, fa-hand-sparkles, fa-book-open">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-sort-numeric-up"></i> Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="<?= isset($_POST['urutan']) ? (int)$_POST['urutan'] : 0 ?>">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="5"><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn-secondary">Reset</button>
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>