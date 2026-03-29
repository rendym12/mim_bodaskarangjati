<?php
include('includes/header.php');
include('includes/db.php');

// Ambil data kontak
$query = mysqli_query($conn, "SELECT * FROM kontak LIMIT 1");
$kontak = mysqli_fetch_assoc($query);

// Tentukan BASE URL
$base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . '/mim_bodaskarangjati';
?>
<link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css?v=<?= time() ?>">

<!-- TAMBAHKAN CLASS PADA BODY UNTUK TARGET KHUSUS -->
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
                    
                    <!-- Maps -->
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
                
                <!-- RIGHT SIDE - KONTAK INFO DENGAN ICON -->
                <div class="kontak-info-section">
                    <h2 class="section-title">Kontak</h2>
                    
                    <!-- EMAIL DENGAN ICON -->
                    <div class="kontak-item">
                        <div class="kontak-icon email-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="kontak-detail">
                            <span class="kontak-label">EMAIL</span>
                            <span class="kontak-value"><?= htmlspecialchars($kontak['email'] ?? 'info@mim-bodaskarangjati.sch.id') ?></span>
                        </div>
                    </div>
                    
                    <!-- ADDRESS DENGAN ICON -->
                    <div class="kontak-item">
                        <div class="kontak-icon address-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="kontak-detail">
                            <span class="kontak-label">ADDRESS</span>
                            <span class="kontak-value"><?= nl2br(htmlspecialchars($kontak['alamat'] ?? 'Jl. Raya Bodaskarangjati No. 123, Kec. Rembang, Kab. Purbalingga, Jawa Tengah 53356')) ?></span>
                        </div>
                    </div>
                    
                    <!-- PHONE DENGAN ICON -->
                    <div class="kontak-item">
                        <div class="kontak-icon phone-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="kontak-detail">
                            <span class="kontak-label">PHONE</span>
                            <span class="kontak-value"><?= htmlspecialchars($kontak['telepon'] ?? '0811-3320-8870') ?></span>
                        </div>
                    </div>
                    
                    <!-- JAM OPERASIONAL DENGAN ICON -->
                    <div class="kontak-item">
                        <div class="kontak-icon hours-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="kontak-detail">
                            <span class="kontak-label">JAM OPERASIONAL</span>
                            <span class="kontak-value"><?= nl2br(htmlspecialchars($kontak['jam_operasional'] ?? 'Senin - Jumat: 07.00 - 15.00 WIB')) ?></span>
                        </div>
                    </div>
                    
                    <!-- SOCIAL MEDIA SECTION - VERTIKAL -->
                    <div class="social-media-section">
                        <h3 class="social-title">Media Sosial</h3>
                        
                        <div class="social-list">
                            <!-- Instagram -->
                            <a href="<?= htmlspecialchars($kontak['instagram'] ?? 'https://instagram.com/mim_bodaskarangjati') ?>" target="_blank" class="social-item instagram">
                                <div class="social-item-icon">
                                    <i class="fab fa-instagram"></i>
                                </div>
                                <div class="social-item-info">
                                    <span class="social-item-name">Instagram</span>
                                    <span class="social-item-username">@mim_bodaskarangjati</span>
                                </div>
                            </a>
                            
                            <!-- YouTube -->
                            <a href="<?= htmlspecialchars($kontak['youtube'] ?? 'https://youtube.com/@mim_bodaskarangjati') ?>" target="_blank" class="social-item youtube">
                                <div class="social-item-icon">
                                    <i class="fab fa-youtube"></i>
                                </div>
                                <div class="social-item-info">
                                    <span class="social-item-name">YouTube</span>
                                    <span class="social-item-username">MI Muhammadiyah</span>
                                </div>
                            </a>
                            
                            <!-- TikTok -->
                            <a href="<?= htmlspecialchars($kontak['tiktok'] ?? 'https://tiktok.com/@mim_bodaskarangjati') ?>" target="_blank" class="social-item tiktok">
                                <div class="social-item-icon">
                                    <i class="fab fa-tiktok"></i>
                                </div>
                                <div class="social-item-info">
                                    <span class="social-item-name">TikTok</span>
                                    <span class="social-item-username">@mim_bodaskarangjati</span>
                                </div>
                            </a>
                            
                            <!-- Facebook -->
                            <a href="<?= htmlspecialchars($kontak['facebook'] ?? 'https://facebook.com/mim.bodaskarangjati') ?>" target="_blank" class="social-item facebook">
                                <div class="social-item-icon">
                                    <i class="fab fa-facebook-f"></i>
                                </div>
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
</main>

<?php include('includes/footer.php'); ?>