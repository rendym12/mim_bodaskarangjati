<?php
session_start();
include "../includes/db.php";

$error = '';
$success = '';
$token = isset($_GET['token']) ? mysqli_real_escape_string($conn, $_GET['token']) : '';

if (empty($token)) {
    header("Location: login.php");
    exit;
}

// Cek token valid
$query = "SELECT * FROM admin_users WHERE reset_token = '$token' AND reset_expiry > NOW()";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    $error = "❌ Link reset password tidak valid atau sudah kadaluarsa! Silakan minta reset ulang.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif ($password != $confirm_password) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $update = "UPDATE admin_users SET 
                   password = '$hashed_password',
                   reset_token = NULL,
                   reset_expiry = NULL
                   WHERE id = " . $user['id'];
        
        if (mysqli_query($conn, $update)) {
            $success = "✅ Password berhasil direset! Silakan login dengan password baru Anda.";
        } else {
            $error = "Gagal mereset password: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            <img src="/mim_bodaskarangjati/assets/img/logo.png" onerror="this.src='https://via.placeholder.com/90x90?text=MI'" alt="Logo">
            <h2>Reset Password</h2>
            <p>Buat password baru untuk akun <strong><?= isset($user) ? htmlspecialchars($user['nama_lengkap']) : '' ?></strong></p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php if (strpos($error, 'kadaluarsa') !== false): ?>
                <div style="text-align: center; margin-top: 15px;">
                    <a href="lupa-password.php" class="btn-login" style="text-decoration: none; display: inline-block; background: #2196F3;">
                        <i class="fas fa-key"></i> Minta Reset Ulang
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><i class="fas fa-check-circle"></i> <?= $success ?></div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="login.php" class="btn-login" style="text-decoration: none; display: inline-block;">
                    <i class="fas fa-sign-in-alt"></i> Login Sekarang
                </a>
            </div>
        <?php elseif ($user && !$success): ?>
        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password Baru</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" autofocus>
                </div>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Konfirmasi Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" required placeholder="Ulangi password baru">
                </div>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-save"></i> Simpan Password Baru
            </button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>