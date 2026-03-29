<?php
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_eks = mysqli_real_escape_string($conn, $_POST['nama_eks']);
    $pembina = mysqli_real_escape_string($conn, $_POST['pembina']);
    $jadwal = mysqli_real_escape_string($conn, $_POST['jadwal']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $ikon = mysqli_real_escape_string($conn, $_POST['ikon']);
    $urutan = (int)$_POST['urutan'];
    
    $query = "INSERT INTO ekstrakurikuler (nama_eks, pembina, jadwal, deskripsi, ikon, urutan) 
              VALUES ('$nama_eks', '$pembina', '$jadwal', '$deskripsi', '$ikon', $urutan)";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Ekstrakurikuler berhasil ditambahkan";
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal menambahkan: " . mysqli_error($conn);
    }
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="content-wrapper ekstra-page">
    <div class="content-header">
        <h1><i class="fas fa-plus-circle"></i> Tambah Ekstrakurikuler</h1>
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
        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-tag"></i> Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                <input type="text" name="nama_eks" class="form-control" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Pembina</label>
                    <input type="text" name="pembina" class="form-control">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Jadwal</label>
                    <input type="text" name="jadwal" class="form-control" placeholder="Contoh: Senin & Kamis, 15.30-17.00">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-icons"></i> Ikon Font Awesome</label>
                    <input type="text" name="ikon" class="form-control" value="fa-futbol" placeholder="fa-futbol, fa-music, fa-palette">
                    <small>Cari ikon di <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a></small>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-sort-numeric-up"></i> Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="0">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="5"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>