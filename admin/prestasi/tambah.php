<?php
include "../includes/auth.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_prestasi = mysqli_real_escape_string($conn, $_POST['nama_prestasi']);
    $jenis_peserta = mysqli_real_escape_string($conn, $_POST['jenis_peserta']);
    
    // Ambil nama peserta berdasarkan jenis peserta
    if ($jenis_peserta == 'individu') {
        $nama_peserta = mysqli_real_escape_string($conn, $_POST['nama_peserta_individu']);
    } else {
        $nama_peserta = mysqli_real_escape_string($conn, $_POST['nama_peserta_regu']);
    }
    
    $tingkat = mysqli_real_escape_string($conn, $_POST['tingkat']);
    $penyelenggara = mysqli_real_escape_string($conn, $_POST['penyelenggara']);
    $tahun = (int)$_POST['tahun'];
    $juara = (int)$_POST['juara'];
    
    $errors = array();
    if (empty($nama_prestasi)) {
        $errors[] = "Nama prestasi harus diisi";
    }
    if (empty($nama_peserta)) {
        $errors[] = "Nama peserta harus diisi";
    }
    
    // Upload gambar
    $gambar = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'webp', 'gif');
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
        $query = "INSERT INTO prestasi (nama_prestasi, jenis_peserta, nama_peserta, tingkat, penyelenggara, tahun, juara, gambar) 
                  VALUES ('$nama_prestasi', '$jenis_peserta', '$nama_peserta', " . ($tingkat ? "'$tingkat'" : "NULL") . ", " . ($penyelenggara ? "'$penyelenggara'" : "NULL") . ", $tahun, $juara, " . ($gambar ? "'$gambar'" : "NULL") . ")";
        
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

                <div class="form-group">
                    <label><i class="fas fa-users"></i> Jenis Peserta <span style="color: red;">*</span></label>
                    <select name="jenis_peserta" class="form-control" required>
                        <option value="individu">Individu (Perorangan)</option>
                        <option value="regu">Regu (Tim/Beregu)</option>
                    </select>
                    <small class="form-text text-muted">Pilih apakah prestasi ini diraih oleh individu atau tim</small>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nama Siswa <span style="color: red;">*</span></label>
                    <input type="text" name="nama_peserta_individu" class="form-control" placeholder="Contoh: Ahmad Fauzi">
                    <small class="form-text text-muted">Untuk jenis individu: masukkan nama siswa yang meraih prestasi</small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-users"></i> Nama Tim / Anggota</label>
                    <input type="text" name="nama_peserta_regu" class="form-control" placeholder="Contoh: Tim Olimpiade Sains">
                    <small class="form-text text-muted">Untuk jenis regu: bisa diisi nama tim atau daftar nama anggota (pisahkan dengan koma)</small>
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
                            <option value="1">Juara 1 (Emas)</option>
                            <option value="2">Juara 2 (Perak)</option>
                            <option value="3">Juara 3 (Perunggu)</option>
                            <?php for($i = 4; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>">Juara <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-image"></i> Gambar</label>
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                    <small>Format: JPG, PNG, WEBP, GIF (Maks. 5MB)</small>
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