<?php
include "includes/auth.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Ambil data gambar untuk dihapus
    $q = mysqli_query($conn, "SELECT gambar FROM prestasi WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    if ($data && !empty($data['gambar']) && file_exists("../uploads/prestasi/" . $data['gambar'])) {
        unlink("../uploads/prestasi/" . $data['gambar']);
    }
    
    mysqli_query($conn, "DELETE FROM prestasi WHERE id = $id");
    $_SESSION['success'] = "Data prestasi berhasil dihapus";
}

header("Location: prestasi.php");
exit;
?>