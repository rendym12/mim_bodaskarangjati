<?php
ob_start();
include "../includes/auth.php";

// Ambil data visi misi
$query = mysqli_query($conn, "SELECT * FROM visi_misi WHERE id = 1");
$data = mysqli_fetch_assoc($query);

// Jika data belum ada, buat default
if (!$data) {
    $default_visi = "<p>Menjadi lembaga pendidikan dasar yang unggul dalam membentuk generasi islami, berakhlak mulia, cerdas, dan berwawasan lingkungan berdasarkan nilai-nilai Muhammadiyah.</p>";
    $default_misi = "<ul>
        <li>Menyelenggarakan pendidikan yang berkualitas dengan mengintegrasikan nilai-nilai keislaman dan keilmuan</li>
        <li>Membentuk peserta didik yang berakhlak mulia melalui pembiasaan ibadah dan perilaku islami</li>
        <li>Mengembangkan potensi akademik dan non-akademik peserta didik secara optimal</li>
        <li>Menciptakan lingkungan madrasah yang bersih, nyaman, dan kondusif untuk belajar</li>
        <li>Menjalin kerjasama yang harmonis dengan orang tua, masyarakat, dan stakeholder pendidikan</li>
    </ul>";
    
    mysqli_query($conn, "INSERT INTO visi_misi (visi, misi) VALUES ('$default_visi', '$default_misi')");
    $query = mysqli_query($conn, "SELECT * FROM visi_misi WHERE id = 1");
    $data = mysqli_fetch_assoc($query);
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $visi = mysqli_real_escape_string($conn, $_POST['visi']);
    $misi = mysqli_real_escape_string($conn, $_POST['misi']);
    
    $query = "UPDATE visi_misi SET visi='$visi', misi='$misi' WHERE id=1";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Visi & Misi berhasil diperbarui!";
        ob_end_clean();
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal mengupdate: " . mysqli_error($conn);
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper visimisi-page">
    <div class="content-header">
        <h1><i class="fas fa-eye"></i> Kelola Visi & Misi</h1>
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
        <form method="POST" id="visiMisiForm">
            <!-- Visi Card -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-flag"></i>
                    <h3>Visi</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <textarea name="visi" id="editor_visi" class="form-control" rows="5"><?= htmlspecialchars($data['visi'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Misi Card -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-tasks"></i>
                    <h3>Misi</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <textarea name="misi" id="editor_misi" class="form-control" rows="10"><?= htmlspecialchars($data['misi'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-primary" id="btnSubmit">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="../dashboard.php" class="btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>

<?php include "../includes/footer.php"; ?>