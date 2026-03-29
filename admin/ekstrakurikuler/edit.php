<?php
require_once dirname(__DIR__) . '/includes/auth.php';

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM ekstrakurikuler WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_eks = mysqli_real_escape_string($conn, $_POST['nama_eks']);
    $pembina = mysqli_real_escape_string($conn, $_POST['pembina']);
    $jadwal = mysqli_real_escape_string($conn, $_POST['jadwal']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $ikon = mysqli_real_escape_string($conn, $_POST['ikon']);
    $urutan = (int)$_POST['urutan'];
    
    $query = "UPDATE ekstrakurikuler SET 
              nama_eks='$nama_eks', 
              pembina='$pembina', 
              jadwal='$jadwal', 
              deskripsi='$deskripsi', 
              ikon='$ikon', 
              urutan=$urutan 
              WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Ekstrakurikuler berhasil diupdate";
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal mengupdate: " . mysqli_error($conn);
    }
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="content-wrapper ekstra-page">
    <div class="content-header">
        <h1><i class="fas fa-edit"></i> Edit Ekstrakurikuler</h1>
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
                <input type="text" name="nama_eks" class="form-control" required value="<?= htmlspecialchars($row['nama_eks']) ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Pembina</label>
                    <input type="text" name="pembina" class="form-control" value="<?= htmlspecialchars($row['pembina'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Jadwal</label>
                    <input type="text" name="jadwal" class="form-control" value="<?= htmlspecialchars($row['jadwal'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-icons"></i> Ikon Font Awesome</label>
                    <input type="text" name="ikon" class="form-control" value="<?= htmlspecialchars($row['ikon'] ?? 'fa-futbol') ?>">
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
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>