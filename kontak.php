<?php
include('includes/header.php');
include('includes/db.php');

// Ambil data kontak
$query = mysqli_query($conn, "SELECT * FROM kontak LIMIT 1");
$kontak = mysqli_fetch_assoc($query);

// Ambil data testimoni yang sudah disetujui (BUKAN SPAM)
$query_testimoni = mysqli_query($conn, "SELECT * FROM testimoni WHERE status = 'approved' AND is_spam = 0 ORDER BY created_at DESC LIMIT 6");
$testimonis = [];
while ($row = mysqli_fetch_assoc($query_testimoni)) {
    $testimonis[] = $row;
}

// Tentukan BASE URL
$base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . '/mim_bodaskarangjati';

// Cek parameter error dari proses_testimoni.php
$error_type = $_GET['error'] ?? '';
$error_message = '';
if ($error_type == 'spam_detected') {
    $error_message = '⚠️ Email ini sudah pernah mengirim testimoni sebelumnya. Hanya satu testimoni per email yang diperbolehkan.';
} elseif ($error_type == 'already_submitted') {
    $error_message = '⚠️ Anda sudah pernah mengirim testimoni dengan email ini. Terima kasih atas partisipasinya!';
} elseif ($error_type == 'email_required') {
    $error_message = '❌ Email wajib diisi untuk mencegah spam.';
} elseif ($error_type == 'invalid_email') {
    $error_message = '❌ Format email tidak valid.';
} elseif ($error_type == 'db_error') {
    $error_message = '❌ Terjadi kesalahan sistem. Silakan coba lagi.';
}
?>
<link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css?v=<?= time() ?>">

<script>
document.body.classList.add('kontak-page');
</script>

<main>
    <div class="kontak-container">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <div class="container">
                <h1>Kontak</h1>
                <p>Hubungi kami untuk informasi lebih lanjut tentang MI Muhammadiyah Bodaskarangjati</p>
            </div>
        </div>

        <div class="container">
            <div class="kontak-wrapper">
                <!-- LEFT SIDE - DENAH LOKASI -->
                <div class="lokasi-section">
                    <h2 class="section-title">Denah Lokasi</h2>
                    <h3 class="sub-title">MI Muhammadiyah Bodaskarangjati</h3>
                    
                    <?php if (!empty($kontak['maps_embed'])): ?>
                    <div class="maps-container">
                        <?= $kontak['maps_embed'] ?>
                    </div>
                    <?php else: ?>
                    <div class="maps-container">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.456789!2d109.456789!3d-7.456789!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7...!2sMI%20Muhammadiyah%20Bodaskarangjati!5e0!3m2!1sid!2sid!4v1234567890" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- RIGHT SIDE - KONTAK INFO -->
                <div class="kontak-info-section">
                    <h2 class="section-title">Kontak</h2>
                    
                    <div class="kontak-item">
                        <div class="kontak-icon email-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="kontak-detail">
                            <span class="kontak-label">EMAIL</span>
                            <span class="kontak-value"><?= htmlspecialchars($kontak['email'] ?? 'info@mim-bodaskarangjati.sch.id') ?></span>
                        </div>
                    </div>
                    
                    <div class="kontak-item">
                        <div class="kontak-icon address-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="kontak-detail">
                            <span class="kontak-label">ADDRESS</span>
                            <span class="kontak-value"><?= nl2br(htmlspecialchars($kontak['alamat'] ?? 'Jl. Raya Bodaskarangjati No. 123, Kec. Rembang, Kab. Purbalingga, Jawa Tengah 53356')) ?></span>
                        </div>
                    </div>
                    
                    <div class="kontak-item">
                        <div class="kontak-icon phone-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="kontak-detail">
                            <span class="kontak-label">PHONE</span>
                            <span class="kontak-value"><?= htmlspecialchars($kontak['telepon'] ?? '0811-3320-8870') ?></span>
                        </div>
                    </div>
                    
                    <div class="kontak-item">
                        <div class="kontak-icon hours-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="kontak-detail">
                            <span class="kontak-label">JAM OPERASIONAL</span>
                            <span class="kontak-value"><?= nl2br(htmlspecialchars($kontak['jam_operasional'] ?? 'Senin - Jumat: 07.00 - 15.00 WIB')) ?></span>
                        </div>
                    </div>
                    
                    <div class="social-media-section">
                        <h3 class="social-title">Media Sosial</h3>
                        
                        <div class="social-list">
                            <a href="<?= htmlspecialchars($kontak['instagram'] ?? 'https://instagram.com/mim_bodaskarangjati') ?>" target="_blank" class="social-item instagram">
                                <div class="social-item-icon"><i class="fab fa-instagram"></i></div>
                                <div class="social-item-info">
                                    <span class="social-item-name">Instagram</span>
                                    <span class="social-item-username">@mim_bodaskarangjati</span>
                                </div>
                            </a>
                            
                            <a href="<?= htmlspecialchars($kontak['youtube'] ?? 'https://youtube.com/@mim_bodaskarangjati') ?>" target="_blank" class="social-item youtube">
                                <div class="social-item-icon"><i class="fab fa-youtube"></i></div>
                                <div class="social-item-info">
                                    <span class="social-item-name">YouTube</span>
                                    <span class="social-item-username">MI Muhammadiyah</span>
                                </div>
                            </a>
                            
                            <a href="<?= htmlspecialchars($kontak['tiktok'] ?? 'https://tiktok.com/@mim_bodaskarangjati') ?>" target="_blank" class="social-item tiktok">
                                <div class="social-item-icon"><i class="fab fa-tiktok"></i></div>
                                <div class="social-item-info">
                                    <span class="social-item-name">TikTok</span>
                                    <span class="social-item-username">@mim_bodaskarangjati</span>
                                </div>
                            </a>
                            
                            <a href="<?= htmlspecialchars($kontak['facebook'] ?? 'https://facebook.com/mim.bodaskarangjati') ?>" target="_blank" class="social-item facebook">
                                <div class="social-item-icon"><i class="fab fa-facebook-f"></i></div>
                                <div class="social-item-info">
                                    <span class="social-item-name">Facebook</span>
                                    <span class="social-item-username">MI Muhammadiyah</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FORM KRITIK & SARAN + TESTIMONI - LAYOUT 2 KOLOM -->
    <div class="kontak-testimoni-wrapper">
        
        <!-- KOLOM KIRI: FORM KRITIK & SARAN -->
        <div class="form-testimoni-section">
            <div class="section-header">
                <span class="section-tag">BERIKAN ULASAN</span>
                <h2 class="section-title">Kritik & Saran</h2>
                <p class="section-subtitle">Kami senang mendengar pendapat Anda</p>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Terima kasih! Ulasan Anda akan kami proses.
                </div>
                <script>window.history.replaceState({}, document.title, window.location.pathname);</script>
            <?php endif; ?>

            <?php if($error_message): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
                </div>
                <script>window.history.replaceState({}, document.title, window.location.pathname);</script>
            <?php endif; ?>

            <div class="testimoni-form-wrapper">
                <form method="POST" action="proses_testimoni.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Lengkap <span style="color: red;">*</span></label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email <span style="color: red;">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                            <small>Email wajib diisi dan hanya Satu testimoni per email</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Rating <span style="color: red;">*</span></label>
                        <select name="rating" class="form-control" required>
                            <option value="5">⭐⭐⭐⭐⭐ (5) - Sangat Baik</option>
                            <option value="4">⭐⭐⭐⭐ (4) - Baik</option>
                            <option value="3">⭐⭐⭐ (3) - Cukup</option>
                            <option value="2">⭐⭐ (2) - Kurang</option>
                            <option value="1">⭐ (1) - Buruk</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Ulasan / Kritik & Saran <span style="color: red;">*</span></label>
                        <textarea name="ulasan" class="form-control" rows="4" required placeholder="Tulis ulasan, kritik, atau saran Anda di sini..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Kirim Ulasan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- KOLOM KANAN: TESTIMONI -->
        <div class="testimoni-section">
            <div class="section-header">
                <span class="section-tag">TESTIMONI</span>
                <h2 class="section-title">Apa Kata Mereka?</h2>
                <p class="section-subtitle">Ulasan dari orang tua siswa dan masyarakat</p>
            </div>

            <div class="testimoni-grid">
                <?php if (count($testimonis) > 0): ?>
                    <?php foreach ($testimonis as $testi): ?>
                    <div class="testimoni-card">
                        <div class="testimoni-header">
                            <div class="testimoni-avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="testimoni-info">
                                <h4><?= htmlspecialchars($testi['nama']) ?></h4>
                                <div class="testimoni-rating">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <?php if($i <= $testi['rating']): ?>
                                            <i class="fas fa-star"></i>
                                        <?php else: ?>
                                            <i class="far fa-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        <div class="testimoni-body">
                            <i class="fas fa-quote-left"></i>
                            <p><?= htmlspecialchars($testi['ulasan']) ?></p>
                        </div>
                        <div class="testimoni-footer">
                            <small><?= date('d F Y', strtotime($testi['created_at'])) ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="testimoni-empty">
                        <i class="fas fa-star"></i>
                        <p>Belum ada testimoni. Jadilah yang pertama!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include('includes/footer.php'); ?>