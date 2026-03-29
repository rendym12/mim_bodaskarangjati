<?php
include "../includes/auth.php";

// ========== PROSES SIMPAN ==========
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $mapel = mysqli_real_escape_string($conn, $_POST['mapel']);
    $urutan = (int)$_POST['urutan'];
    
    // Upload foto
    $foto = 'default-avatar.jpg';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && $_FILES['foto']['size'] <= 2 * 1024 * 1024) {
            $foto = 'guru_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], '../../uploads/guru/' . $foto);
        }
    }
    
    $query = "INSERT INTO guru_staff (nama, nip, jabatan, mapel, foto, urutan) 
              VALUES ('$nama', " . ($nip ? "'$nip'" : "NULL") . ", " . ($jabatan ? "'$jabatan'" : "NULL") . ", " . ($mapel ? "'$mapel'" : "NULL") . ", '$foto', $urutan)";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Data guru/staff <strong>$nama</strong> berhasil ditambahkan";
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal menambahkan data: " . mysqli_error($conn);
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper guru-page">
    <!-- Content Header -->
    <div class="content-header">
        <h1><i class="fas fa-plus-circle"></i> Tambah Guru/Staff</h1>
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
                    <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama lengkap">
                </div>
                <div class="form-group">
                    <label>
                        <i class="fas fa-id-card"></i>
                        NIP
                    </label>
                    <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP (jika ada)">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>
                        <i class="fas fa-briefcase"></i>
                        Jabatan
                    </label>
                    <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Kepala Sekolah, Guru Kelas, dll">
                </div>
                <div class="form-group">
                    <label>
                        <i class="fas fa-book"></i>
                        Mata Pelajaran
                    </label>
                    <input type="text" name="mapel" class="form-control" placeholder="Contoh: Matematika, IPA, dll">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>
                        <i class="fas fa-sort-numeric-up"></i>
                        Urutan Tampil
                    </label>
                    <input type="number" name="urutan" class="form-control" value="0" min="0">
                    <small style="color: #6c757d;">Semakin kecil angka, semakin atas tampilnya</small>
                </div>
                <div class="form-group">
                    <label>
                        <i class="fas fa-camera"></i>
                        Foto
                    </label>
                    <div style="border: 2px dashed #ddd; padding: 20px; text-align: center; border-radius: 5px;">
                        <input type="file" name="foto" accept="image/*" style="margin-bottom: 10px;">
                        <small style="display: block; color: #6c757d;">Format: JPG, PNG (Max. 2MB)</small>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" style="background: #4f46e5; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="index.php" style="background: #6c757d; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block;">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>