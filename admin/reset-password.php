<?php
session_start();
include "../includes/db.php";

$error = '';
$success = '';
$token = mysqli_real_escape_string($conn, $_GET['token'] ?? '');

if (empty($token)) { header("Location: login.php"); exit; }

$query = "SELECT * FROM admin_users WHERE reset_token='$token' AND reset_expiry > NOW()";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    $error = "Token tidak valid atau sudah kadaluarsa!";
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
                   password='$hashed_password', 
                   reset_token=NULL, 
                   reset_expiry=NULL 
                   WHERE id=" . $user['id'];
        
        if (mysqli_query($conn, $update)) {
            $success = "Password berhasil diperbarui!";
        } else {
            $error = "Terjadi kesalahan database.";
        }
    }
}
?>