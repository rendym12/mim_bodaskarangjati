<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM guru_staff WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    header("Location: index.php");
    exit;
}

$urutan_lama = $row['urutan'];

// ========== FUNGSI VALIDASI KOMBINASI JABATAN ==========
function isValidJabatanCombination($jabatan, $mapel) {
    // Jika jabatan kosong, dianggap guru mapel (boleh)
    if (empty($jabatan)) {
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

// ========== PROSES UPDATE ==========
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan'] ?? '');
    $mapel = mysqli_real_escape_string($conn, $_POST['mapel'] ?? '');
    $urutan_baru = (int)$_POST['urutan'];
    
    // Validasi: Nama wajib diisi
    if (empty($nama)) {
        $error = "Nama lengkap harus diisi!";
    } 
    // Validasi: Urutan harus lebih dari 0
    elseif ($urutan_baru <= 0) {
        $error = "Urutan harus diisi dengan angka 1, 2, 3, dst!";
    }
    else {
        $validasi = isValidJabatanCombination($jabatan, $mapel);
        if (!$validasi['valid']) {
            $error = $validasi['message'];
        } else {
            // LOGIKA GESER URUTAN SAAT EDIT
            if ($urutan_baru != $urutan_lama) {
                if ($urutan_baru > $urutan_lama) {
                    mysqli_query($conn, "UPDATE guru_staff SET urutan = urutan - 1 
                                         WHERE urutan > $urutan_lama AND urutan <= $urutan_baru AND id != $id");
                } else {
                    mysqli_query($conn, "UPDATE guru_staff SET urutan = urutan + 1 
                                         WHERE urutan >= $urutan_baru AND urutan < $urutan_lama AND id != $id");
                }
            }
            
            // Upload foto
            $foto = $row['foto'];
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $size = $_FILES['foto']['size'];
                $max_size = 2 * 1024 * 1024;
                
                if (in_array($ext, $allowed) && $size <= $max_size) {
                    if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg' && file_exists("../../uploads/guru/" . $row['foto'])) {
                        unlink("../../uploads/guru/" . $row['foto']);
                    }
                    $foto = 'guru_' . time() . '_' . uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['foto']['tmp_name'], '../../uploads/guru/' . $foto);
                }
            }
            
            // Jika jabatan kosong, simpan sebagai NULL
            $jabatan_sql = empty($jabatan) ? "NULL" : "'$jabatan'";
            
            $query = "UPDATE guru_staff SET 
                      nama = '$nama',
                      nip = " . ($nip ? "'$nip'" : "NULL") . ",
                      jabatan = $jabatan_sql,
                      mapel = " . ($mapel ? "'$mapel'" : "NULL") . ",
                      foto = '$foto',
                      urutan = $urutan_baru
                      WHERE id = $id";
            
            if (mysqli_query($conn, $query)) {
                $_SESSION['success'] = "Data guru/staff $nama berhasil diupdate";
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal mengupdate data: " . mysqli_error($conn);
            }
        }
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
        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Lengkap <span class="text-danger">*</span></label>
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
                    <input type="text" name="jabatan" class="form-control" value="<?= htmlspecialchars($row['jabatan'] ?? '') ?>" placeholder="Kosongkan jika hanya guru mapel">
                    <small>Kosongkan jika hanya mengajar mapel tertentu. Gunakan " & " untuk multiple jabatan.</small>
                </div>
                <div class="form-group">
                    <label>Mata Pelajaran</label>
                    <input type="text" name="mapel" class="form-control" value="<?= htmlspecialchars($row['mapel'] ?? '') ?>" placeholder="Contoh: Matematika, IPA, Olahraga">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Urutan Tampil <span class="text-danger">*</span></label>
                    <input type="number" name="urutan" class="form-control" required min="1" value="<?= $row['urutan'] ?? 0 ?>">
                    <small>Ubah urutan sesuai keinginan. Data lain akan otomatis menyesuaikan.</small>
                </div>
                <div class="form-group">
                    <label>Foto</label>
                    <?php if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg'): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="../../uploads/guru/<?= $row['foto'] ?>" width="80" height="80" style="object-fit: cover; border-radius: 10px;">
                            <p><small><?= $row['foto'] ?></small></p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Update</button>
                <a href="index.php" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>