<?php
session_start();
include "../includes/db.php";

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if (isset($_GET['reset'])) {
    if ($_GET['reset'] == 'sent') $success = "Instruksi telah dikirim ke email Anda.";
    if ($_GET['reset'] == 'success') $success = "Password berhasil direset. Silakan login.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM admin_users WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password']) || md5($password) === $user['password']) {
            
            if (md5($password) === $user['password']) {
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE admin_users SET password='$new_hash' WHERE id=" . $user['id']);
            }

            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_nama'] = $user['nama_lengkap'];
            $_SESSION['admin_foto'] = $user['foto'] ?? 'default-avatar.jpg';
            
            mysqli_query($conn, "UPDATE admin_users SET last_login=NOW() WHERE id=" . $user['id']);
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - MI Muhammadiyah Bodaskarangjati</title>
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

    <div class="login-container">
        <div class="logo">
            <img src="/mim_bodaskarangjati/assets/img/logo.png" onerror="this.src='https://via.placeholder.com/90x90?text=MI'" alt="Logo">
            <h2>Admin Panel</h2>
            <p>MI Muhammadiyah Bodaskarangjati</p>
        </div>
        
        <?php if ($success): ?>
            <div class="success-message"><i class="fas fa-check-circle"></i> <?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <label>Username</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
            </div>
            <button type="submit" class="btn-login" id="btnLogin">
                <i class="fas fa-sign-in-alt"></i> LOGIN
            </button>
            <div class="forgot-password">
                <a href="lupa-password.php"><i class="fas fa-key"></i> Lupa Password?</a>
            </div>
        </form>
    </div>
    
    <script src="/mim_bodaskarangjati/assets/js/admin.js"></script>
</body>
</html>