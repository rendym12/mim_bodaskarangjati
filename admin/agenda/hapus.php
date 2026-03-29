<?php
include "includes/auth.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    mysqli_query($conn, "DELETE FROM agenda WHERE id = $id");
    $_SESSION['success'] = "Data agenda berhasil dihapus";
}

header("Location: agenda.php");
exit;
?>