<?php
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_agenda = mysqli_real_escape_string($conn, $_POST['nama_agenda']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'] ?: NULL;
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $query = "INSERT INTO agenda (nama_agenda, tanggal_mulai, tanggal_selesai, lokasi, deskripsi) 
              VALUES ('$nama_agenda', '$tanggal_mulai', " . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", '$lokasi', '$deskripsi')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Agenda berhasil ditambahkan";
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal menambahkan agenda: " . mysqli_error($conn);
    }
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="content-wrapper agenda-page">
    <div class="content-header">
        <h1><i class="fas fa-plus-circle"></i> Tambah Agenda</h1>
        <a href="index.php" class="btn-secondary">
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
                <label>
                    <i class="fas fa-tag"></i>
                    Nama Agenda <span class="text-danger">*</span>
                </label>
                <input type="text" name="nama_agenda" class="form-control" required placeholder="Masukkan nama agenda">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>
                        <i class="fas fa-calendar"></i>
                        Tanggal Mulai <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="tanggal_mulai" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label>
                        <i class="fas fa-calendar"></i>
                        Tanggal Selesai
                    </label>
                    <input type="date" name="tanggal_selesai" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-map-marker-alt"></i>
                    Lokasi
                </label>
                <input type="text" name="lokasi" class="form-control" placeholder="Masukkan lokasi agenda">
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-align-left"></i>
                    Deskripsi
                </label>
                <textarea name="deskripsi" class="form-control" rows="5" placeholder="Masukkan deskripsi agenda"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="index.php" class="btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>