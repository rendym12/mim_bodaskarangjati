<?php
include "includes/auth.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    mysqli_query($conn, "DELETE FROM ekstrakurikuler WHERE id = $id");
    $_SESSION['success'] = "Data ekstrakurikuler berhasil dihapus";
}

header("Location: ekstrakurikuler.php");
exit;
?>