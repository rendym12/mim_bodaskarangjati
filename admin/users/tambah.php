<?php
include "../includes/auth.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    
    $errors = [];
    
    // Validasi
    if (empty($nama_lengkap)) {
        $errors[] = "Nama lengkap harus diisi";
    }
    if (empty($username)) {
        $errors[] = "Username harus diisi";
    }
    if (empty($email)) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    if (empty($password)) {
        $errors[] = "Password harus diisi";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }
    if ($password != $konfirmasi_password) {
        $errors[] = "Konfirmasi password tidak cocok";
    }
    
    // Cek username sudah ada
    $check_username = mysqli_query($conn, "SELECT id FROM admin_users WHERE username = '$username'");
    if (mysqli_num_rows($check_username) > 0) {
        $errors[] = "Username sudah digunakan";
    }
    
    // Cek email sudah ada
    $check_email = mysqli_query($conn, "SELECT id FROM admin_users WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        $errors[] = "Email sudah digunakan";
    }
    
    // Upload foto
    $foto = 'default-avatar.jpg';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['foto']['size'];
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file harus JPG atau PNG";
        } elseif ($size > 2 * 1024 * 1024) {
            $errors[] = "Ukuran file maksimal 2MB";
        } else {
            $foto = 'user_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], '../../uploads/' . $foto);
        }
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO admin_users (nama_lengkap, username, email, password, foto) 
                  VALUES ('$nama_lengkap', '$username', '$email', '$hashed_password', '$foto')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Admin <strong>$nama_lengkap</strong> berhasil ditambahkan";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal menambahkan data: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper users-page">
    <div class="content-header">
        <h1><i class="fas fa-plus-circle"></i> Tambah Admin</h1>
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

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data" id="userForm">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nama Lengkap <span style="color: red;">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control" required value="<?= isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : '' ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user-circle"></i> Username <span style="color: red;">*</span></label>
                    <input type="text" name="username" class="form-control" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email <span style="color: red;">*</span></label>
                    <input type="email" name="email" class="form-control" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password <span style="color: red;">*</span></label>
                    <input type="password" name="password" id="password" class="form-control" required minlength="6">
                    <small style="color: #6c757d;">Minimal 6 karakter</small>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Konfirmasi Password <span style="color: red;">*</span></label>
                    <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-camera"></i> Foto Profil</label>
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

            <div class="form-actions">
                <button type="reset" class="btn-secondary" id="btnReset">Reset</button>
                <button type="submit" class="btn-primary" id="btnSubmit"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>