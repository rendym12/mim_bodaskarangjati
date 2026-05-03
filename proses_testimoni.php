<?php
include('includes/db.php');

$nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
$email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$rating = (int)($_POST['rating'] ?? 5);
$ulasan = mysqli_real_escape_string($conn, $_POST['ulasan'] ?? '');

// Email wajib
if (empty($email)) {
    header("Location: kontak.php?error=email_required");
    exit;
}

// Validasi format email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: kontak.php?error=invalid_email");
    exit;
}

// Cek apakah email sudah pernah kirim testimoni
$check = mysqli_query($conn, "SELECT id FROM testimoni WHERE email = '$email'");
if (mysqli_num_rows($check) > 0) {
    header("Location: kontak.php?error=already_submitted");
    exit;
}

// Validasi dasar
if (empty($nama) || empty($ulasan)) {
    header("Location: kontak.php?error=empty_fields");
    exit;
}

// Simpan data
$query = "INSERT INTO testimoni (nama, email, ulasan, rating, status, created_at, updated_at) 
          VALUES ('$nama', '$email', '$ulasan', $rating, 'pending', NOW(), NOW())";

if (mysqli_query($conn, $query)) {
    header("Location: kontak.php?success=1");
} else {
    header("Location: kontak.php?error=db_error");
}
exit;
?>