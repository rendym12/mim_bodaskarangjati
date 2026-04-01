<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM guru_staff WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    header("Location: index.php");
    exit;
}

// ========== PROSES UPDATE ==========
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $mapel = mysqli_real_escape_string($conn, $_POST['mapel']);
    $urutan = (int)$_POST['urutan'];
    
    $foto = $row['foto'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && $_FILES['foto']['size'] <= 2 * 1024 * 1024) {
            if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg' && file_exists("../../uploads/guru/" . $row['foto'])) {
                unlink("../../uploads/guru/" . $row['foto']);
            }
            
            $foto = 'guru_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], '../../uploads/guru/' . $foto);
        }
    }
    
    $query = "UPDATE guru_staff SET 
              nama = '$nama',
              nip = " . ($nip ? "'$nip'" : "NULL") . ",
              jabatan = " . ($jabatan ? "'$jabatan'" : "NULL") . ",
              mapel = " . ($mapel ? "'$mapel'" : "NULL") . ",
              foto = '$foto',
              urutan = $urutan
              WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Data guru/staff <strong>$nama</strong> berhasil diupdate";
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal mengupdate data: " . mysqli_error($conn);
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper guru-page">
    <div class="content-header">
        <h1><i class="fas fa-edit"></i> Edit Guru/Staff</h1>
        <a href="index.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data" id="guruForm">
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($row['nama']) ?>">
                </div>
                <div class="form-group">
                    <label>NIP</label>
                    <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($row['nip'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" value="<?= htmlspecialchars($row['jabatan'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Mata Pelajaran</label>
                    <input type="text" name="mapel" class="form-control" value="<?= htmlspecialchars($row['mapel'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Urutan Tampil</label>
                    <input type="number" name="urutan" class="form-control" value="<?= $row['urutan'] ?? 0 ?>" min="0">
                    <small>Semakin kecil angka, semakin atas tampilnya</small>
                </div>
                <div class="form-group">
                    <label>Foto</label>
                    
                    <?php if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg'): ?>
                    <div class="current-file">
                        <img src="../../uploads/guru/<?= $row['foto'] ?>" alt="Foto">
                        <p><?= $row['foto'] ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="file-upload" id="fileUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Klik untuk ganti foto</p>
                        <small>Format: JPG, PNG (Maks. 2MB)</small>
                        <input type="file" name="foto" id="foto" accept="image/*">
                    </div>
                    <div id="previewContainer" style="display: none;">
                        <img id="previewImage" src="#" alt="Preview">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary" id="btnSubmit">Update</button>
                <a href="index.php" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>