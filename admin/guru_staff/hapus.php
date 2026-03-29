<?php
include "includes/auth.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Ambil data foto untuk dihapus
    $q = mysqli_query($conn, "SELECT foto FROM guru_staff WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    // Hapus file foto jika ada dan bukan default
    if ($data && $data['foto'] != 'default-avatar.jpg' && file_exists("../uploads/guru/" . $data['foto'])) {
        unlink("../uploads/guru/" . $data['foto']);
    }
    
    mysqli_query($conn, "DELETE FROM guru_staff WHERE id = $id");
    $_SESSION['success'] = "Data guru/staff berhasil dihapus";
}

header("Location: guru_staff.php");
exit;
?>