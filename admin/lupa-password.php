<?php
session_start();
include "../includes/db.php";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = "SELECT * FROM admin_users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Buat password baru yang mudah dibaca (tanpa karakter membingungkan)
        $alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $new_password = substr(str_shuffle($alphabet), 0, 8);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $update = "UPDATE admin_users SET password='$hashed_password' WHERE id=" . $user['id'];
        
        if (mysqli_query($conn, $update)) {
            $success = "Password baru Anda: <strong style='font-size: 1.2em; color: #2ecc71;'>$new_password</strong><br>";
            $success .= "Catat dan login, lalu segera ganti di menu profil.";
        } else {
            $error = "Gagal memproses data.";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/mim_bodaskarangjati/assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-container" style="max-width: 500px;">
        <div class="logo">
            <h2>Lupa Password</h2>
            <p>Sistem akan membuatkan password baru untuk Anda</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><i class="fas fa-check-circle"></i> <?= $success ?></div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="login.php" class="btn-login" style="text-decoration: none; display:block;">Login Sekarang</a>
            </div>
        <?php else: ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Masukkan Email Terdaftar</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" required placeholder="email@sekolah.com">
                </div>
            </div>
            <button type="submit" class="btn-login">GENERATE PASSWORD BARU</button>
            <div class="forgot-password">
                <a href="login.php"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>