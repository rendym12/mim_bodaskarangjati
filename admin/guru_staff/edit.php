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
            // Hapus foto lama jika bukan default
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
    <!-- Content Header -->
    <div class="content-header">
        <h1><i class="fas fa-edit"></i> Edit Guru/Staff</h1>
        <a href="index.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Alert Error -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- Form Container -->
    <div class="form-container">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>
                        <i class="fas fa-user"></i>
                        Nama Lengkap <span style="color: red;">*</span>
                    </label>
                    <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($row['nama']) ?>">
                </div>
                <div class="form-group">
                    <label>
                        <i class="fas fa-id-card"></i>
                        NIP
                    </label>
                    <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($row['nip'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>
                        <i class="fas fa-briefcase"></i>
                        Jabatan
                    </label>
                    <input type="text" name="jabatan" class="form-control" value="<?= htmlspecialchars($row['jabatan'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>
                        <i class="fas fa-book"></i>
                        Mata Pelajaran
                    </label>
                    <input type="text" name="mapel" class="form-control" value="<?= htmlspecialchars($row['mapel'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>
                        <i class="fas fa-sort-numeric-up"></i>
                        Urutan Tampil
                    </label>
                    <input type="number" name="urutan" class="form-control" value="<?= $row['urutan'] ?? 0 ?>" min="0">
                    <small style="color: #6c757d;">Semakin kecil angka, semakin atas tampilnya</small>
                </div>
                <div class="form-group">
                    <label>
                        <i class="fas fa-camera"></i>
                        Foto
                    </label>
                    
                    <?php if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg'): ?>
                    <div style="margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                        <img src="../../uploads/guru/<?= $row['foto'] ?>" alt="Foto" style="max-width: 100px; max-height: 100px; border-radius: 5px;">
                        <p style="margin-top: 5px;"><?= $row['foto'] ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div style="border: 2px dashed #ddd; padding: 20px; text-align: center; border-radius: 5px;">
                        <input type="file" name="foto" accept="image/*" style="margin-bottom: 10px;">
                        <small style="display: block; color: #6c757d;">Biarkan kosong jika tidak ingin mengganti foto</small>
                        <small style="display: block; color: #6c757d;">Format: JPG, PNG (Max. 2MB)</small>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" style="background: #4f46e5; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="index.php" style="background: #6c757d; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block;">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>