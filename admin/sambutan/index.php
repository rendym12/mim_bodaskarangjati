<?php
ob_start();
include "../includes/auth.php";

// Ambil data sambutan
$query = mysqli_query($conn, "SELECT * FROM sambutan LIMIT 1");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    // Buat data default jika belum ada
    $default_nama = "Ahmad Sudirman, S.Pd.I";
    $default_jabatan = "Kepala Madrasah";
    $default_sambutan = "<p>Assalamu'alaikum warahmatullahi wabarakatuh,</p>
    <p>Puji syukur kehadirat Allah SWT yang telah melimpahkan rahmat dan hidayah-Nya sehingga website MI Muhammadiyah Bodaskarangjati dapat hadir di tengah-tengah kita. Website ini kami hadirkan sebagai sarana informasi dan komunikasi antara madrasah dengan seluruh stakeholder pendidikan.</p>
    <p>Sebagai lembaga pendidikan Islam, kami berkomitmen untuk memberikan pelayanan pendidikan terbaik bagi putra-putri kita. Kami percaya bahwa setiap anak memiliki potensi yang luar biasa yang perlu dikembangkan dengan bimbingan yang tepat.</p>
    <p>Melalui website ini, kami berharap dapat memberikan informasi yang transparan dan update mengenai berbagai kegiatan dan program di madrasah kami. Kami juga membuka diri untuk masukan dan saran yang membangun demi kemajuan madrasah tercinta.</p>
    <p>Wassalamu'alaikum warahmatullahi wabarakatuh.</p>";
    
    mysqli_query($conn, "INSERT INTO sambutan (nama_kepala, sambutan, jabatan, foto) VALUES ('$default_nama', '$default_sambutan', '$default_jabatan', 'default-kepala.jpg')");
    $query = mysqli_query($conn, "SELECT * FROM sambutan LIMIT 1");
    $data = mysqli_fetch_assoc($query);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kepala = mysqli_real_escape_string($conn, $_POST['nama_kepala']);
    $sambutan = mysqli_real_escape_string($conn, $_POST['sambutan']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    
    $foto = $data['foto'];
    
    // Upload foto baru jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['foto']['size'];
        
        if (in_array($ext, $allowed) && $size <= 2 * 1024 * 1024) {
            // Hapus foto lama jika bukan default
            if ($foto && $foto != 'default-kepala.jpg' && file_exists("../../uploads/" . $foto)) {
                unlink("../../uploads/" . $foto);
            }
            
            $foto = 'kepala_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], '../../uploads/' . $foto);
        }
    }
    
    $sql = "UPDATE sambutan SET 
            nama_kepala='$nama_kepala',
            sambutan='$sambutan',
            jabatan='$jabatan',
            foto='$foto'
            WHERE id={$data['id']}";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Data sambutan berhasil diperbarui!";
        ob_end_clean();
        header("Location: index.php");
        exit;
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper sambutan-page">
    <div class="content-header">
        <h1><i class="fas fa-microphone"></i> Edit Sambutan Kepala Madrasah</h1>
        <a href="../dashboard.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>

    <!-- NOTIFICATION CONTAINER -->
    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data" id="sambutanForm">
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nama Kepala Madrasah <span style="color: red;">*</span></label>
                    <input type="text" name="nama_kepala" class="form-control" required value="<?= htmlspecialchars($data['nama_kepala'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-briefcase"></i> Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" value="<?= htmlspecialchars($data['jabatan'] ?? 'Kepala Madrasah') ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-camera"></i> Foto</label>
                
                <?php if (!empty($data['foto']) && $data['foto'] != 'default-kepala.jpg'): ?>
                <div style="margin-bottom: 15px; padding: 10px; background: #f8fafc; border-radius: 8px;">
                    <img src="../../uploads/<?= $data['foto'] ?>" alt="Foto Kepala" style="max-width: 150px; max-height: 150px; border-radius: 10px;">
                    <p style="margin-top: 5px;"><?= $data['foto'] ?></p>
                </div>
                <?php endif; ?>
                
                <div class="file-upload" id="fileUploadArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Klik untuk upload foto</p>
                    <small>Format: JPG, PNG (Maks. 2MB)</small>
                    <input type="file" name="foto" id="foto" accept="image/*" style="display: none;">
                </div>
                <div id="previewContainer" class="preview-container" style="display: none; margin-top: 15px;">
                    <img id="previewImage" src="#" alt="Preview" class="preview-image" style="max-width: 150px; border-radius: 10px;">
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Isi Sambutan <span style="color: red;">*</span></label>
                <textarea name="sambutan" id="editor" class="form-control" rows="10" required><?= htmlspecialchars($data['sambutan'] ?? '') ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary" id="btnSubmit"><i class="fas fa-save"></i> Simpan</button>
                <a href="../dashboard.php" class="btn-secondary"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>

<?php include "../includes/footer.php"; ?>