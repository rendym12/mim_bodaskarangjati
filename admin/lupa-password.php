<?php
session_start();
include "../includes/db.php";

$error = '';
$success = '';
$step = 1;

// ==============================================
// DETEKSI BASE URL OTOMATIS (SIAP HOSTING)
// ==============================================
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . '://' . $host . '/mim_bodaskarangjati';

// ==============================================
// GENERATE CAPTCHA (CASE INSENSITIVE)
// ==============================================
$alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ123456789';
if (!isset($_SESSION['captcha_text']) || empty($_SESSION['captcha_text'])) {
    $_SESSION['captcha_text'] = strtoupper(substr(str_shuffle($alphabet), 0, 6));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['email'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $captcha_input = strtoupper(trim($_POST['captcha'] ?? ''));
        
        if ($captcha_input !== $_SESSION['captcha_text']) {
            $error = "❌ Kode keamanan (CAPTCHA) salah!";
            $_SESSION['captcha_text'] = strtoupper(substr(str_shuffle($alphabet), 0, 6));
        } else {
            $query = "SELECT id, nama_lengkap, email FROM admin_users WHERE email = '$email'";
            $result = mysqli_query($conn, $query);
            $user = mysqli_fetch_assoc($result);
            
            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                $update = "UPDATE admin_users SET reset_token = '$token', reset_expiry = '$expiry' WHERE id = " . $user['id'];
                mysqli_query($conn, $update);
                
                $reset_link = $base_url . "/admin/reset-password.php?token=" . $token;
                
                $_SESSION['reset_link_display'] = $reset_link;
                $_SESSION['reset_email_sent'] = true;
                $_SESSION['reset_user_name'] = $user['nama_lengkap'];
                $_SESSION['reset_user_email'] = $user['email'];
                $step = 2;
                
                $_SESSION['captcha_text'] = strtoupper(substr(str_shuffle($alphabet), 0, 6));
                
            } else {
                $error = "❌ Email tidak ditemukan dalam database!";
            }
        }
    }
    
    if (isset($_POST['resend'])) {
        $step = 1;
        unset($_SESSION['reset_email_sent']);
        unset($_SESSION['reset_link_display']);
        unset($_SESSION['reset_user_name']);
        unset($_SESSION['reset_user_email']);
    }
}

if (isset($_GET['refresh_captcha'])) {
    $_SESSION['captcha_text'] = strtoupper(substr(str_shuffle($alphabet), 0, 6));
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
            <p>Masukkan email Anda untuk mendapatkan link reset password</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($step == 2 && isset($_SESSION['reset_email_sent'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <h3>Link Reset Password</h3>
                <p>Halo <strong><?= htmlspecialchars($_SESSION['reset_user_name']) ?></strong></p>
                <p>Email: <strong><?= htmlspecialchars($_SESSION['reset_user_email']) ?></strong></p>
                
                <div class="link-box">
                    <i class="fas fa-link"></i>
                    <strong>Klik link di bawah untuk mereset password:</strong><br><br>
                    <a href="<?= $_SESSION['reset_link_display'] ?>" target="_blank">
                        <?= $_SESSION['reset_link_display'] ?>
                    </a>
                    <br><br>
                    <small><i class="fas fa-info-circle"></i> Link reset akan kadaluarsa dalam 1 jam.</small>
                </div>
                
                <div class="action-buttons-reset">
                    <a href="login.php" class="btn-login btn-success">Kembali ke Login</a>
                    <form method="POST" style="display: inline-block;">
                        <button type="submit" name="resend" class="btn-login btn-secondary">Kirim Ulang</button>
                    </form>
                </div>
            </div>
            
        <?php elseif ($step == 1): ?>
            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Terdaftar</label>
                    <input type="email" name="email" class="form-control" required autofocus placeholder="Masukkan email Anda">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-shield-alt"></i> Kode Keamanan (CAPTCHA)</label>
                    <div class="captcha-container">
                        <div class="captcha-code"><?= $_SESSION['captcha_text'] ?></div>
                        <button type="button" class="refresh-captcha" onclick="refreshCaptcha()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <input type="text" name="captcha" class="form-control" required placeholder="Masukkan kode di atas (HURUF BESAR)" autocomplete="off">
                </div>
                
                <button type="submit" class="btn-login btn-primary" style="width: 100%;">
                    <i class="fas fa-paper-plane"></i> Kirim Link Reset
                </button>
                
                <div class="text-center">
                    <a href="login.php">← Kembali ke Login</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
    
    <script>
    function refreshCaptcha() {
        window.location.href = '?refresh_captcha=1';
    }
    </script>
</body>
</html>