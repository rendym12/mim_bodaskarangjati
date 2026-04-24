<?php
include "includes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $ulasan = mysqli_real_escape_string($conn, $_POST['ulasan']);
    $rating = (int) ($_POST['rating'] ?? 5);
    
    $sql = "INSERT INTO testimoni (nama, email, ulasan, rating, status) 
            VALUES ('$nama', '$email', '$ulasan', '$rating', 'pending')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: kontak.php?testimoni_success=1");
    } else {
        header("Location: kontak.php?testimoni_error=1");
    }
    exit;
}
?>