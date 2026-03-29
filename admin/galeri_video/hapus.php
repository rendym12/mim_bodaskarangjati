<?php
include "includes/auth.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Ambil data thumbnail untuk dihapus
    $q = mysqli_query($conn, "SELECT thumbnail FROM galeri_video WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    if ($data && !empty($data['thumbnail']) && file_exists("../uploads/galeri_video/" . $data['thumbnail'])) {
        unlink("../uploads/galeri_video/" . $data['thumbnail']);
    }
    
    mysqli_query($conn, "DELETE FROM galeri_video WHERE id = $id");
    $_SESSION['success'] = "Data video berhasil dihapus";
}

header("Location: galeri_video.php");
exit;
?>