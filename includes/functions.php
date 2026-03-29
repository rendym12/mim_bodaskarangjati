<?php
// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Redirect to URL
function redirect($url) {
    header("Location: $url");
    exit;
}

// Sanitize input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Upload file
function uploadFile($file, $target_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
    $file_name = time() . '_' . basename($file['name']);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if file type is allowed
    if (!in_array($file_type, $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan'];
    }
    
    // Check file size (max 2MB)
    if ($file['size'] > 2000000) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 2MB)'];
    }
    
    // Create directory if not exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => $file_name];
    } else {
        return ['success' => false, 'message' => 'Gagal upload file'];
    }
}

// Format tanggal Indonesia
function tgl_indo($date) {
    if ($date == '0000-00-00' || $date == null) return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecah = explode('-', $date);
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}

// Limit text
function limit_text($text, $limit = 100) {
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . '...';
    }
    return $text;
}

// Get setting
function getSetting($key) {
    global $conn;
    $query = mysqli_query($conn, "SELECT value FROM settings WHERE key = '$key'");
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        return $row['value'];
    }
    return null;
}
?>