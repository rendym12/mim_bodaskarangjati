<?php
include "../includes/auth.php";

// Ambil data kontak
$query = mysqli_query($conn, "SELECT * FROM kontak LIMIT 1");
$kontak = mysqli_fetch_assoc($query);

if (!$kontak) {
    // Buat data default
    $default = [
        'email' => '',
        'telepon' => '',
        'alamat' => '',
        'jam_operasional' => '',
        'maps_embed' => '',
        'instagram' => '',
        'youtube' => '',
        'tiktok' => '',
        'facebook' => ''
    ];
    $kontak = $default;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $jam_operasional = mysqli_real_escape_string($conn, $_POST['jam_operasional']);
    $maps_embed = mysqli_real_escape_string($conn, $_POST['maps_embed']);
    $instagram = mysqli_real_escape_string($conn, $_POST['instagram']);
    $youtube = mysqli_real_escape_string($conn, $_POST['youtube']);
    $tiktok = mysqli_real_escape_string($conn, $_POST['tiktok']);
    $facebook = mysqli_real_escape_string($conn, $_POST['facebook']);
    
    if (isset($kontak['id'])) {
        // Update
        $sql = "UPDATE kontak SET 
                email='$email',
                telepon='$telepon',
                alamat='$alamat',
                jam_operasional='$jam_operasional',
                maps_embed='$maps_embed',
                instagram='$instagram',
                youtube='$youtube',
                tiktok='$tiktok',
                facebook='$facebook'
                WHERE id={$kontak['id']}";
    } else {
        // Insert
        $sql = "INSERT INTO kontak (email, telepon, alamat, jam_operasional, maps_embed, instagram, youtube, tiktok, facebook) 
                VALUES ('$email', '$telepon', '$alamat', '$jam_operasional', '$maps_embed', '$instagram', '$youtube', '$tiktok', '$facebook')";
    }
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Data kontak berhasil diperbarui!";
        header("Location: index.php");
        exit;
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper kontak-page">
    <div class="content-header">
        <h1><i class="fas fa-address-book"></i> Kelola Kontak</h1>
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
        <form method="POST" id="kontakForm">
            <!-- Informasi Kontak -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i>
                    <h3>Informasi Kontak</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($kontak['email'] ?? '') ?>" placeholder="info@sekolah.sch.id">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone-alt"></i> Telepon</label>
                            <input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars($kontak['telepon'] ?? '') ?>" placeholder="0811-2222-3333">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Jl. Contoh No. 123, Kecamatan, Kabupaten, Provinsi"><?= htmlspecialchars($kontak['alamat'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Jam Operasional</label>
                        <textarea name="jam_operasional" class="form-control" rows="2" placeholder="Senin - Jumat: 07.00 - 15.00 WIB"><?= htmlspecialchars($kontak['jam_operasional'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Google Maps -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-map-marked-alt"></i>
                    <h3>Google Maps</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label><i class="fas fa-code"></i> Embed Code Maps</label>
                        <textarea name="maps_embed" class="form-control" rows="5" placeholder="<iframe src='...'></iframe>"><?= htmlspecialchars($kontak['maps_embed'] ?? '') ?></textarea>
                    </div>
                    
                    <?php if (!empty($kontak['maps_embed'])): ?>
                    <div class="maps-preview">
                        <label>Preview Maps:</label>
                        <div class="maps-frame">
                            <?= $kontak['maps_embed'] ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Media Sosial -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-share-alt"></i>
                    <h3>Media Sosial</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fab fa-instagram"></i> Instagram</label>
                            <input type="url" name="instagram" class="form-control" value="<?= htmlspecialchars($kontak['instagram'] ?? '') ?>" placeholder="https://instagram.com/username">
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-youtube"></i> YouTube</label>
                            <input type="url" name="youtube" class="form-control" value="<?= htmlspecialchars($kontak['youtube'] ?? '') ?>" placeholder="https://youtube.com/@channel">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fab fa-tiktok"></i> TikTok</label>
                            <input type="url" name="tiktok" class="form-control" value="<?= htmlspecialchars($kontak['tiktok'] ?? '') ?>" placeholder="https://tiktok.com/@username">
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-facebook-f"></i> Facebook</label>
                            <input type="url" name="facebook" class="form-control" value="<?= htmlspecialchars($kontak['facebook'] ?? '') ?>" placeholder="https://facebook.com/username">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-primary" id="btnSubmit">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>