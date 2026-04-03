<?php
ob_start();
include "../includes/auth.php";

// Ambil data PPDB
$query = mysqli_query($conn, "SELECT * FROM ppdb LIMIT 1");
$ppdb = mysqli_fetch_assoc($query);

if (!$ppdb) {
    $default = [
        'id' => null,
        'status' => 'nonaktif',
        'judul' => 'PMBM',
        'sub_judul' => 'PENERIMAAN MURID BARU MADRASAH',
        'tahun_ajaran' => date('Y') . '/' . (date('Y')+1),
        'tanggal_mulai' => date('Y-m-d'),
        'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
        'tanggal_pemetaan' => '',
        'info_tambahan' => '',
        'syarat' => "Syarat Pendaftaran :\n1. Kartu Keluarga (asli dan terbaru)\n2. Akta Kelahiran (asli)\n3. KTP salah satu orang tua\n4. Kartu dari desa bila memiliki\n\nCatatan :\n- Syarat pendaftaran bersifat wajib untuk validasi data siswa.\n- Dibawa saat datang ke MIM Bodaskarangjati (Pemetaan)",
        'link_pendaftaran' => 'https://docs.google.com/forms/d/e/1FAIpQLSf0W7h990gxeeh-Y-t-BU0bWAWZRmlSRVhjMJTQtsSij07j2g/viewform',
        'qr_code' => '',
        'kontak_telepon' => '',
        'kontak_nama' => '',
        'kontak_keterangan' => '',
        'catatan' => ''
    ];
    $ppdb = $default;
}

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $sub_judul = mysqli_real_escape_string($conn, $_POST['sub_judul']);
    $tahun_ajaran = mysqli_real_escape_string($conn, $_POST['tahun_ajaran']);
    $tanggal_mulai = !empty($_POST['tanggal_mulai']) ? mysqli_real_escape_string($conn, $_POST['tanggal_mulai']) : null;
    $tanggal_selesai = !empty($_POST['tanggal_selesai']) ? mysqli_real_escape_string($conn, $_POST['tanggal_selesai']) : null;
    $tanggal_pemetaan = !empty($_POST['tanggal_pemetaan']) ? mysqli_real_escape_string($conn, $_POST['tanggal_pemetaan']) : null;
    $info_tambahan = !empty($_POST['info_tambahan']) ? mysqli_real_escape_string($conn, $_POST['info_tambahan']) : null;
    $syarat = mysqli_real_escape_string($conn, $_POST['syarat']);
    $link_pendaftaran = !empty($_POST['link_pendaftaran']) ? mysqli_real_escape_string($conn, $_POST['link_pendaftaran']) : null;
    $kontak_telepon = !empty($_POST['kontak_telepon']) ? mysqli_real_escape_string($conn, $_POST['kontak_telepon']) : null;
    $kontak_nama = !empty($_POST['kontak_nama']) ? mysqli_real_escape_string($conn, $_POST['kontak_nama']) : null;
    $kontak_keterangan = !empty($_POST['kontak_keterangan']) ? mysqli_real_escape_string($conn, $_POST['kontak_keterangan']) : null;
    $catatan = !empty($_POST['catatan']) ? mysqli_real_escape_string($conn, $_POST['catatan']) : null;
    
    $errors = [];
    
    $qr_code = $ppdb['qr_code'] ?? null;
    if (isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['qr_code']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['qr_code']['size'];
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file QR Code harus JPG, JPEG, PNG, GIF, atau WEBP";
        } elseif ($size > 2 * 1024 * 1024) {
            $errors[] = "Ukuran file QR Code maksimal 2MB";
        } else {
            $upload_dir = '../../uploads/qr/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (!empty($ppdb['qr_code']) && file_exists($upload_dir . $ppdb['qr_code'])) {
                unlink($upload_dir . $ppdb['qr_code']);
            }
            
            $qr_code = 'qr_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['qr_code']['tmp_name'], $upload_dir . $qr_code);
        }
    }
    
    if (empty($errors)) {
        if (isset($ppdb['id']) && $ppdb['id']) {
            $sql = "UPDATE ppdb SET 
                    status='$status',
                    judul='$judul',
                    sub_judul='$sub_judul',
                    tahun_ajaran='$tahun_ajaran',
                    tanggal_mulai=" . ($tanggal_mulai ? "'$tanggal_mulai'" : "NULL") . ",
                    tanggal_selesai=" . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ",
                    tanggal_pemetaan=" . ($tanggal_pemetaan ? "'$tanggal_pemetaan'" : "NULL") . ",
                    info_tambahan=" . ($info_tambahan ? "'$info_tambahan'" : "NULL") . ",
                    syarat='$syarat',
                    link_pendaftaran=" . ($link_pendaftaran ? "'$link_pendaftaran'" : "NULL") . ",
                    qr_code=" . ($qr_code ? "'$qr_code'" : "NULL") . ",
                    kontak_telepon=" . ($kontak_telepon ? "'$kontak_telepon'" : "NULL") . ",
                    kontak_nama=" . ($kontak_nama ? "'$kontak_nama'" : "NULL") . ",
                    kontak_keterangan=" . ($kontak_keterangan ? "'$kontak_keterangan'" : "NULL") . ",
                    catatan=" . ($catatan ? "'$catatan'" : "NULL") . ",
                    updated_at=NOW()
                    WHERE id={$ppdb['id']}";
        } else {
            $sql = "INSERT INTO ppdb (status, judul, sub_judul, tahun_ajaran, tanggal_mulai, tanggal_selesai, tanggal_pemetaan, info_tambahan, syarat, link_pendaftaran, qr_code, kontak_telepon, kontak_nama, kontak_keterangan, catatan, updated_at) 
                    VALUES ('$status', '$judul', '$sub_judul', '$tahun_ajaran', " . ($tanggal_mulai ? "'$tanggal_mulai'" : "NULL") . ", " . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", " . ($tanggal_pemetaan ? "'$tanggal_pemetaan'" : "NULL") . ", " . ($info_tambahan ? "'$info_tambahan'" : "NULL") . ", '$syarat', " . ($link_pendaftaran ? "'$link_pendaftaran'" : "NULL") . ", " . ($qr_code ? "'$qr_code'" : "NULL") . ", " . ($kontak_telepon ? "'$kontak_telepon'" : "NULL") . ", " . ($kontak_nama ? "'$kontak_nama'" : "NULL") . ", " . ($kontak_keterangan ? "'$kontak_keterangan'" : "NULL") . ", " . ($catatan ? "'$catatan'" : "NULL") . ", NOW())";
        }
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success'] = "Data PPDB berhasil diperbarui!";
            ob_end_clean();
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper ppdb-admin">
    <div class="content-header">
        <h1><i class="fas fa-graduation-cap"></i> Kelola PPDB</h1>
        <a href="../dashboard.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>

    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <ul style="margin: 5px 0 0 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-toggle-on"></i>
                    <h3>Status Pendaftaran</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Status PPDB</label>
                        <select name="status" class="form-control">
                            <option value="aktif" <?= ($ppdb['status'] ?? '') == 'aktif' ? 'selected' : '' ?>>Aktif (Pendaftaran Dibuka)</option>
                            <option value="nonaktif" <?= ($ppdb['status'] ?? '') == 'nonaktif' ? 'selected' : '' ?>>Nonaktif (Pendaftaran Ditutup)</option>
                        </select>
                        <small class="text-muted">Status akan menampilkan badge yang sesuai di halaman publik</small>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-heading"></i>
                    <h3>Hero Section</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Judul Utama</label>
                        <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($ppdb['judul'] ?? 'PMBM') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Sub Judul</label>
                        <input type="text" name="sub_judul" class="form-control" value="<?= htmlspecialchars($ppdb['sub_judul'] ?? 'PENERIMAAN MURID BARU MADRASAH') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" class="form-control" value="<?= htmlspecialchars($ppdb['tahun_ajaran'] ?? date('Y') . '/' . (date('Y')+1)) ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i>
                    <h3>Info Cards</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tanggal Mulai Pendaftaran</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="<?= $ppdb['tanggal_mulai'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Selesai Pendaftaran</label>
                            <input type="date" name="tanggal_selesai" class="form-control" value="<?= $ppdb['tanggal_selesai'] ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Tanggal Pemetaan</label>
                        <input type="text" name="tanggal_pemetaan" class="form-control" value="<?= htmlspecialchars($ppdb['tanggal_pemetaan'] ?? '') ?>" placeholder="Contoh: 20-22 Juni 2026">
                    </div>
                    
                    <div class="form-group">
                        <label>Info Tambahan</label>
                        <input type="text" name="info_tambahan" class="form-control" value="<?= htmlspecialchars($ppdb['info_tambahan'] ?? '') ?>" placeholder="Contoh: Kuota Terbatas">
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Syarat Pendaftaran</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Isi Syarat & Catatan</label>
                        <textarea name="syarat" class="form-control" rows="14" 
                            placeholder="Contoh format:
Syarat Pendaftaran :
1. Kartu Keluarga (asli dan terbaru)
2. Akta Kelahiran (asli)
3. KTP salah satu orang tua
4. Kartu dari desa bila memiliki

Catatan :
- Syarat pendaftaran bersifat wajib untuk validasi data siswa.
- Dibawa saat datang ke MIM Bodaskarangjati (Pemetaan)"><?= htmlspecialchars($ppdb['syarat'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-link"></i>
                    <h3>Link Pendaftaran & QR Code</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Link Pendaftaran Online</label>
                        <input type="url" name="link_pendaftaran" class="form-control" value="<?= htmlspecialchars($ppdb['link_pendaftaran'] ?? '') ?>" placeholder="https://forms.google.com/...">
                    </div>

                    <div class="form-group">
                        <label>QR Code Pendaftaran</label>
                        <?php if (!empty($ppdb['qr_code']) && file_exists('../../uploads/qr/' . $ppdb['qr_code'])): ?>
                        <div style="margin-bottom: 15px;">
                            <img src="../../uploads/qr/<?= $ppdb['qr_code'] ?>" alt="QR Code" style="max-width: 100px; border: 1px solid #ddd; border-radius: 8px; padding: 5px;">
                        </div>
                        <?php endif; ?>
                        <input type="file" name="qr_code" class="form-control" accept="image/*">
                        <small>Format: JPG, PNG, GIF, WEBP (Maks. 2MB)</small>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-phone-alt"></i>
                    <h3>Kontak Panitia</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nomor Telepon / WhatsApp</label>
                            <input type="text" name="kontak_telepon" class="form-control" value="<?= htmlspecialchars($ppdb['kontak_telepon'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Nama Kontak Person</label>
                            <input type="text" name="kontak_nama" class="form-control" value="<?= htmlspecialchars($ppdb['kontak_nama'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan Kontak</label>
                        <input type="text" name="kontak_keterangan" class="form-control" value="<?= htmlspecialchars($ppdb['kontak_keterangan'] ?? '') ?>" placeholder="Contoh: Hubungi saat jam kerja">
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-sticky-note"></i>
                    <h3>Catatan Penting Tambahan</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan..."><?= htmlspecialchars($ppdb['catatan'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="../dashboard.php" class="btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>