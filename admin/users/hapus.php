<?php
include "includes/auth.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Jangan hapus diri sendiri
if ($id == $_SESSION['admin_id']) {
    $_SESSION['error'] = "Tidak dapat menghapus akun sendiri!";
} elseif ($id > 0) {
    // Ambil data foto untuk dihapus
    $q = mysqli_query($conn, "SELECT foto FROM admin_users WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    if ($data && $data['foto'] != 'default-avatar.jpg' && file_exists("../uploads/" . $data['foto'])) {
        unlink("../uploads/" . $data['foto']);
    }
    
    mysqli_query($conn, "DELETE FROM admin_users WHERE id = $id");
    $_SESSION['success'] = "User berhasil dihapus";
}

header("Location: users.php");
exit;
?>