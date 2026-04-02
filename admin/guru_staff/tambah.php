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
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['foto']['size'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (in_array($ext, $allowed) && $size <= $max_size) {
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
    <div class="content-header">
        <h1><i class="fas fa-plus-circle"></i> Tambah Guru/Staff</h1>
        <a href="index.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            <button type="button" class="close">&times;</button>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data" id="guruForm">
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama lengkap">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> NIP</label>
                    <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP (jika ada)">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-briefcase"></i> Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Kepala Sekolah, Guru Kelas, dll">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-book"></i> Mata Pelajaran</label>
                    <input type="text" name="mapel" class="form-control" placeholder="Contoh: Matematika, IPA, dll">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-sort-numeric-down"></i> Urutan Tampil</label>
                    <input type="number" name="urutan" class="form-control" value="0" min="0">
                    <small class="text-muted">Semakin kecil angka, semakin atas tampilnya</small>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-camera"></i> Foto</label>
                    <div class="file-upload" id="fileUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Klik untuk upload foto</p>
                        <small>Format: JPG, PNG, WEBP (Maks. 2MB)</small>
                        <input type="file" name="foto" id="foto" accept="image/*">
                    </div>
                    <div id="previewContainer" style="display: none;">
                        <img id="previewImage" src="#" alt="Preview">
                    </div>
                </div>
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

<?php include "../includes/footer.php"; ?>