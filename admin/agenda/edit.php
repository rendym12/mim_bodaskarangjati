<?php
require_once dirname(__DIR__) . '/includes/auth.php';

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM agenda WHERE id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    $_SESSION['error'] = "Data agenda tidak ditemukan";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_agenda = mysqli_real_escape_string($conn, $_POST['nama_agenda']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'] ?: NULL;
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $query_update = "UPDATE agenda SET 
                        nama_agenda = '$nama_agenda',
                        tanggal_mulai = '$tanggal_mulai',
                        tanggal_selesai = " . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ",
                        lokasi = '$lokasi',
                        deskripsi = '$deskripsi'
                    WHERE id = $id";
    
    if (mysqli_query($conn, $query_update)) {
        $_SESSION['success'] = "Agenda berhasil diperbarui";
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal memperbarui agenda: " . mysqli_error($conn);
    }
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="content-wrapper agenda-page">
    <div class="content-header">
        <h1><i class="fas fa-edit"></i> Edit Agenda</h1>
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
                <input type="text" name="nama_agenda" class="form-control" required 
                       value="<?= htmlspecialchars($data['nama_agenda']) ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>
                        <i class="fas fa-calendar"></i>
                        Tanggal Mulai <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="tanggal_mulai" class="form-control" required 
                           value="<?= $data['tanggal_mulai'] ?>">
                </div>
                <div class="form-group">
                    <label>
                        <i class="fas fa-calendar"></i>
                        Tanggal Selesai
                    </label>
                    <input type="date" name="tanggal_selesai" class="form-control" 
                           value="<?= $data['tanggal_selesai'] ?>">
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-map-marker-alt"></i>
                    Lokasi
                </label>
                <input type="text" name="lokasi" class="form-control" 
                       value="<?= htmlspecialchars($data['lokasi']) ?>">
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-align-left"></i>
                    Deskripsi
                </label>
                <textarea name="deskripsi" class="form-control" rows="5"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="index.php" class="btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>