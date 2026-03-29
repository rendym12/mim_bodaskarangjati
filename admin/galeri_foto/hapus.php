<?php
include "includes/auth.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Ambil data foto untuk dihapus
    $q = mysqli_query($conn, "SELECT file_foto FROM galeri_foto WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    if ($data && !empty($data['file_foto']) && file_exists("../uploads/galeri_foto/" . $data['file_foto'])) {
        unlink("../uploads/galeri_foto/" . $data['file_foto']);
    }
    
    mysqli_query($conn, "DELETE FROM galeri_foto WHERE id = $id");
    $_SESSION['success'] = "Data foto berhasil dihapus";
}

header("Location: galeri_foto.php");
exit;
?>