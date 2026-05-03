<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];

$query = mysqli_query($conn, "SELECT nama FROM testimoni WHERE id = $id");
$data = mysqli_fetch_assoc($query);

if ($data) {
    $update = mysqli_query($conn, "UPDATE testimoni SET is_spam = 1, status = 'rejected' WHERE id = $id");
    
    if ($update) {
        $_SESSION['success'] = "🚫 Testimoni dari <strong>\"" . htmlspecialchars($data['nama']) . "\"</strong> ditandai sebagai SPAM!";
    } else {
        $_SESSION['error'] = "❌ Gagal menandai sebagai spam!";
    }
} else {
    $_SESSION['error'] = "❌ Data tidak ditemukan!";
}

header("Location: index.php");
exit;
?>