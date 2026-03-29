<?php
include "../includes/auth.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Ambil data admin
$query = mysqli_query($conn, "SELECT * FROM admin_users WHERE id = $id");
$admin = mysqli_fetch_assoc($query);

if (!$admin) {
    header("Location: index.php");
    exit;
}

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
    
    // Cek username sudah ada (kecuali untuk admin yang sedang diedit)
    $check_username = mysqli_query($conn, "SELECT id FROM admin_users WHERE username = '$username' AND id != $id");
    if (mysqli_num_rows($check_username) > 0) {
        $errors[] = "Username sudah digunakan";
    }
    
    // Cek email sudah ada (kecuali untuk admin yang sedang diedit)
    $check_email = mysqli_query($conn, "SELECT id FROM admin_users WHERE email = '$email' AND id != $id");
    if (mysqli_num_rows($check_email) > 0) {
        $errors[] = "Email sudah digunakan";
    }
    
    // Validasi password jika diisi
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errors[] = "Password minimal 6 karakter";
        }
        if ($password != $konfirmasi_password) {
            $errors[] = "Konfirmasi password tidak cocok";
        }
    }
    
    // Upload foto baru
    $foto = $admin['foto'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['foto']['size'];
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file harus JPG atau PNG";
        } elseif ($size > 2 * 1024 * 1024) {
            $errors[] = "Ukuran file maksimal 2MB";
        } else {
            // Hapus foto lama jika bukan default
            if ($foto != 'default-avatar.jpg' && file_exists("../../uploads/" . $foto)) {
                unlink("../../uploads/" . $foto);
            }
            $foto = 'user_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], '../../uploads/' . $foto);
        }
    }
    
    if (empty($errors)) {
        // Update data
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE admin_users SET 
                      nama_lengkap = '$nama_lengkap',
                      username = '$username',
                      email = '$email',
                      password = '$hashed_password',
                      foto = '$foto'
                      WHERE id = $id";
        } else {
            $query = "UPDATE admin_users SET 
                      nama_lengkap = '$nama_lengkap',
                      username = '$username',
                      email = '$email',
                      foto = '$foto'
                      WHERE id = $id";
        }
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Admin <strong>$nama_lengkap</strong> berhasil diperbarui";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal memperbarui data: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper users-page">
    <div class="content-header">
        <h1><i class="fas fa-edit"></i> Edit Admin</h1>
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
                <input type="text" name="nama_lengkap" class="form-control" required value="<?= htmlspecialchars($admin['nama_lengkap']) ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user-circle"></i> Username <span style="color: red;">*</span></label>
                    <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($admin['username']) ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email <span style="color: red;">*</span></label>
                    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($admin['email'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" id="password" class="form-control" minlength="6">
                    <small style="color: #6c757d;">Kosongkan jika tidak ingin mengubah password</small>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Konfirmasi Password</label>
                    <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control">
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
                <div id="previewContainer" class="preview-container" style="margin-top: 15px;">
                    <?php if (!empty($admin['foto']) && $admin['foto'] != 'default-avatar.jpg'): ?>
                        <img id="previewImage" src="../../uploads/<?= $admin['foto'] ?>" alt="Preview" class="preview-image" style="max-width: 150px; border-radius: 10px;">
                    <?php else: ?>
                        <img id="previewImage" src="#" alt="Preview" class="preview-image" style="max-width: 150px; border-radius: 10px; display: none;">
                    <?php endif; ?>
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