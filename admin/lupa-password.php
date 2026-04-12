<?php
session_start();
include "../includes/db.php";

$error = '';
$success = '';
$step = 1;

if (!isset($_SESSION['reset_email'])) {
    $_SESSION['reset_email'] = null;
    $_SESSION['reset_user_id'] = null;
    $_SESSION['reset_user_name'] = null;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $query = "SELECT id, nama_lengkap, unique_code FROM admin_users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
    
    if ($user) {
        if (empty($user['unique_code'])) {
            $error = "Akun ini belum mengatur kode unik. Silakan login dan atur kode unik di menu profil.";
        } else {
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_user_id'] = $user['id'];
            $_SESSION['reset_user_name'] = $user['nama_lengkap'];
            $step = 2;
        }
    } else {
        $error = "Email tidak ditemukan dalam database!";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify'])) {
    $unique_code = mysqli_real_escape_string($conn, $_POST['unique_code']);
    
    $user_id = $_SESSION['reset_user_id'];
    $query = "SELECT * FROM admin_users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
    
    if ($user) {
        if ($unique_code === $user['unique_code']) {
            $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $new_password = substr(str_shuffle($alphabet), 0, 8);
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $update = "UPDATE admin_users SET password = '$hashed_password' WHERE id = " . $user['id'];
            
            if (mysqli_query($conn, $update)) {
                $success = true;
                $user_name = $user['nama_lengkap'];
                $new_pass = $new_password;
                
                $_SESSION['reset_email'] = null;
                $_SESSION['reset_user_id'] = null;
                $_SESSION['reset_user_name'] = null;
            } else {
                $error = "Gagal mereset password.";
            }
        } else {
            $error = "Kode unik salah!";
        }
    } else {
        $error = "Data tidak ditemukan.";
        $step = 1;
    }
}

if (isset($_GET['reset'])) {
    $_SESSION['reset_email'] = null;
    $_SESSION['reset_user_id'] = null;
    $_SESSION['reset_user_name'] = null;
    header("Location: lupa-password.php");
    exit;
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
    <div class="animated-bg">
        <div class="gradient-bg"></div>
        <div class="circle circle1"></div>
        <div class="circle circle2"></div>
    </div>

    <div class="login-container" style="max-width: 500px;">
        <div class="logo">
            <img src="/mim_bodaskarangjati/assets/img/logo.png" onerror="this.src='https://via.placeholder.com/90x90?text=MI'">
            <h2>Lupa Password</h2>
            <p>Verifikasi dengan kode unik untuk mereset password</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($success) && $success === true): ?>
            <div class="success-message">
                <i class="fas fa-check-circle" style="font-size: 48px; color: #2ecc71; margin-bottom: 15px; display: block;"></i>
                <h3>Password Berhasil Direset!</h3>
                <p>Akun: <strong><?= htmlspecialchars($user_name) ?></strong></p>
                <div style="background: #f0f0f0; padding: 20px; text-align: center; font-size: 28px; font-family: monospace; border-radius: 10px; margin: 15px 0;">
                    <?= $new_pass ?>
                </div>
                <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 12px; border-radius: 5px; margin-top: 15px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Segera login dan ganti password Anda!</strong>
                </div>
            </div>
            <a href="login.php" class="btn-login" style="display: block; text-align: center; margin-top: 15px;">Login Sekarang</a>
            
        <?php elseif ($step == 2): ?>
            <form method="POST">
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
                    <i class="fas fa-qrcode" style="font-size: 40px; color: #4CAF50; margin-bottom: 10px; display: block;"></i>
                    <strong>Masukkan Kode Unik Anda</strong>
                    <p style="font-size: 12px; color: #666; margin-top: 5px;">Kode unik sudah Anda buat saat mengatur profil</p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-key"></i> Kode Unik</label>
                    <input type="text" name="unique_code" class="form-control" required autofocus placeholder="Masukkan kode unik Anda">
                </div>
                <button type="submit" name="verify" class="btn-login">Verifikasi & Reset Password</button>
                <div style="margin-top: 15px; text-align: center;">
                    <a href="?reset=1">← Gunakan email lain</a>
                    &nbsp;|&nbsp;
                    <a href="login.php">Kembali ke Login</a>
                </div>
            </form>
            
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Terdaftar</label>
                    <input type="email" name="email" class="form-control" required autofocus placeholder="Masukkan email Anda">
                </div>
                <button type="submit" class="btn-login">Verifikasi Email</button>
                <div style="margin-top: 15px; text-align: center;">
                    <a href="login.php">← Kembali ke Login</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>