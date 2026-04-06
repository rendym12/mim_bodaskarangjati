<?php
include "../includes/auth.php";

// ========== FUNGSI GESER URUTAN ==========
function shiftUrutan($conn, $urutan_baru) {
    mysqli_query($conn, "UPDATE guru_staff SET urutan = urutan + 1 WHERE urutan >= $urutan_baru");
}

// ========== FUNGSI VALIDASI KOMBINASI JABATAN ==========
function isValidJabatanCombination($jabatan, $mapel) {
    // Jika jabatan kosong, validasi hanya berdasarkan mapel
    if (empty($jabatan)) {
        // Jika hanya mengisi mapel, dianggap sebagai guru mapel (boleh)
        return ['valid' => true, 'message' => ''];
    }
    
    $guru_kelas = ['Guru Kelas', 'Guru Kelas 1', 'Guru Kelas 2', 'Guru Kelas 3', 'Guru Kelas 4', 'Guru Kelas 5', 'Guru Kelas 6'];
    $guru_mapel = ['Guru Olahraga', 'Guru Agama', 'Guru Bahasa Inggris', 'Guru Matematika', 'Guru IPA', 'Guru IPS', 'Guru PJOK', 'Guru Seni Budaya', 'Guru Prakarya', 'Guru BK'];
    
    $isGuruKelas = false;
    $isGuruMapel = false;
    
    foreach ($guru_kelas as $gk) {
        if (strpos($jabatan, $gk) !== false) {
            $isGuruKelas = true;
            break;
        }
    }
    
    foreach ($guru_mapel as $gm) {
        if (strpos($jabatan, $gm) !== false) {
            $isGuruMapel = true;
            break;
        }
    }
    
    if ($isGuruKelas && $isGuruMapel) {
        return ['valid' => false, 'message' => 'Guru tidak bisa menjadi Guru Kelas sekaligus Guru Mapel!'];
    }
    
    if (strpos($jabatan, 'Kepala Sekolah') !== false && substr_count($jabatan, '&') > 0) {
        return ['valid' => false, 'message' => 'Kepala Sekolah tidak boleh merangkap jabatan lain!'];
    }
    
    return ['valid' => true, 'message' => ''];
}

// ========== PROSES SIMPAN ==========
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan'] ?? '');
    $mapel = mysqli_real_escape_string($conn, $_POST['mapel'] ?? '');
    $urutan = (int)$_POST['urutan'];
    
    // Validasi: Nama wajib diisi
    if (empty($nama)) {
        $error = "Nama lengkap harus diisi!";
    } 
    // Validasi: Urutan harus lebih dari 0
    elseif ($urutan <= 0) {
        $error = "Urutan harus diisi dengan angka 1, 2, 3, dst!";
    }
    else {
        // Validasi kombinasi jabatan (jabatan boleh kosong)
        $validasi = isValidJabatanCombination($jabatan, $mapel);
        if (!$validasi['valid']) {
            $error = $validasi['message'];
        } else {
            // Geser urutan yang lebih besar atau sama
            shiftUrutan($conn, $urutan);
            
            // Upload foto
            $foto = 'default-avatar.jpg';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $size = $_FILES['foto']['size'];
                $max_size = 2 * 1024 * 1024;
                
                if (in_array($ext, $allowed) && $size <= $max_size) {
                    $foto = 'guru_' . time() . '_' . uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['foto']['tmp_name'], '../../uploads/guru/' . $foto);
                }
            }
            
            // Jika jabatan kosong, simpan sebagai NULL
            $jabatan_sql = empty($jabatan) ? "NULL" : "'$jabatan'";
            
            $query = "INSERT INTO guru_staff (nama, nip, jabatan, mapel, foto, urutan) 
                      VALUES ('$nama', " . ($nip ? "'$nip'" : "NULL") . ", $jabatan_sql, " . ($mapel ? "'$mapel'" : "NULL") . ", '$foto', $urutan)";
            
            if (mysqli_query($conn, $query)) {
                $_SESSION['success'] = "Data guru/staff $nama berhasil ditambahkan";
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal menambahkan data: " . mysqli_error($conn);
            }
        }
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
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>NIP</label>
                    <input type="text" name="nip" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" placeholder="Kosongkan jika hanya guru mapel">
                    <small>Kosongkan jika hanya mengajar mapel tertentu. Gunakan " & " untuk multiple jabatan.</small>
                </div>
                <div class="form-group">
                    <label>Mata Pelajaran</label>
                    <input type="text" name="mapel" class="form-control" placeholder="Contoh: Matematika, IPA, Olahraga">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Urutan Tampil <span class="text-danger">*</span></label>
                    <input type="number" name="urutan" class="form-control" required min="1">
                    <small>Masukkan angka urutan. Data dengan urutan lebih besar atau sama akan bergeser ke bawah.</small>
                </div>
                <div class="form-group">
                    <label>Foto</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="index.php" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>