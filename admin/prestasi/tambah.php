<?php
include "../includes/auth.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_prestasi = mysqli_real_escape_string($conn, $_POST['nama_prestasi']);
    $tingkat = mysqli_real_escape_string($conn, $_POST['tingkat']);
    $penyelenggara = mysqli_real_escape_string($conn, $_POST['penyelenggara']);
    $tahun = (int)$_POST['tahun'];
    $juara = (int)$_POST['juara'];
    
    $errors = [];
    if (empty($nama_prestasi)) {
        $errors[] = "Nama prestasi harus diisi";
    }
    
    // Upload gambar
    $gambar = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['gambar']['size'];
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file harus JPG, JPEG, PNG, WEBP, atau GIF";
        } elseif ($size > 5 * 1024 * 1024) {
            $errors[] = "Ukuran file maksimal 5MB";
        } else {
            if (!file_exists('../../uploads/prestasi')) {
                mkdir('../../uploads/prestasi', 0777, true);
            }
            
            $gambar = 'prestasi_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = "../../uploads/prestasi/" . $gambar;
            
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $errors[] = "Gagal upload gambar";
                $gambar = null;
            }
        }
    }
    
    if (empty($errors)) {
        $query = "INSERT INTO prestasi (nama_prestasi, tingkat, penyelenggara, tahun, juara, gambar) 
                  VALUES ('$nama_prestasi', " . ($tingkat ? "'$tingkat'" : "NULL") . ", " . ($penyelenggara ? "'$penyelenggara'" : "NULL") . ", $tahun, $juara, " . ($gambar ? "'$gambar'" : "NULL") . ")";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Prestasi <strong>$nama_prestasi</strong> berhasil ditambahkan";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal menambahkan data: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper prestasi-page">
    <div class="content-header">
        <h1><i class="fas fa-plus-circle"></i> Tambah Prestasi</h1>
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

    <div class="card">
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Nama Prestasi <span style="color: red;">*</span></label>
                    <input type="text" name="nama_prestasi" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-chart-bar"></i> Tingkat</label>
                        <select name="tingkat" class="form-control">
                            <option value="">- Pilih Tingkat -</option>
                            <option value="Internasional">Internasional</option>
                            <option value="Nasional">Nasional</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                            <option value="Kecamatan">Kecamatan</option>
                            <option value="Sekolah">Sekolah</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-building"></i> Penyelenggara</label>
                        <input type="text" name="penyelenggara" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Tahun</label>
                        <select name="tahun" class="form-control">
                            <option value="">- Pilih Tahun -</option>
                            <?php 
                            $current_year = date('Y');
                            for ($year = $current_year; $year >= 2000; $year--): 
                            ?>
                                <option value="<?= $year ?>"><?= $year ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-medal"></i> Juara / Peringkat</label>
                        <select name="juara" class="form-control">
                            <option value="0">- Peserta / Tidak Berperingkat -</option>
                            <option value="1">🥇 Juara 1 (Emas)</option>
                            <option value="2">🥈 Juara 2 (Perak)</option>
                            <option value="3">🥉 Juara 3 (Perunggu)</option>
                            <?php for($i = 4; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>">Juara <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-image"></i> Gambar</label>
                    <div class="file-upload" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Klik atau drag & drop untuk upload gambar</p>
                        <small>Format: JPG, PNG, WEBP, GIF (Maks. 5MB)</small>
                        <input type="file" name="gambar" id="gambarInput" accept="image/*" style="display: none;">
                    </div>
                    
                    <div id="previewContainer" style="display: none; margin-top: 15px;">
                        <img id="previewImage" src="#" alt="Preview" style="max-width: 200px; border-radius: 8px;">
                        <button type="button" class="btn-remove" id="removePreviewBtn" style="margin-left: 10px;">
                            <i class="fas fa-times"></i> Hapus
                        </button>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 20px; display: flex; gap: 10px;">
                    <a href="index.php" class="btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>