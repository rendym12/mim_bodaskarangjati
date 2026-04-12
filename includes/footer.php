<?php
// Footer dengan BASE_URL (otomatis dari config yang sudah di-include di header)
// Pastikan koneksi database tersedia
if (!isset($conn)) {
    require_once __DIR__ . '/db.php';
}

// Ambil data kontak untuk alamat, telepon, email, jam operasional, dan maps
$q_kontak = mysqli_query($conn, "SELECT * FROM kontak LIMIT 1");
$kontak = mysqli_fetch_assoc($q_kontak);

// Default values jika data kontak kosong
$alamat = $kontak['alamat'] ?? 'Desa Bodaskarangjati, Kec. Rembang, Kab. Purbalingga';
$telepon = $kontak['telepon'] ?? '(0281) 123456';
$email = $kontak['email'] ?? 'info@mim-bodaskarangjati.sch.id';
$jam_operasional = $kontak['jam_operasional'] ?? 'Senin - Jumat, 07.00 - 15.00 WIB';
$maps_embed = $kontak['maps_embed'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.456789!2d109.456789!3d-7.456789!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7...!2sMI%20Muhammadiyah%20Bodaskarangjati!5e0!3m2!1sid!2sid!4v1234567890';

// Sosial media (tetap sama)
$sosmed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT facebook, instagram, youtube, tiktok FROM kontak LIMIT 1"));
$fb = $sosmed['facebook'] ?? 'https://facebook.com/mim.bodaskarangjati';
$ig = $sosmed['instagram'] ?? 'https://instagram.com/mim_bodaskarangjati';
$yt = $sosmed['youtube'] ?? 'https://youtube.com/@mim_bodaskarangjati';
$tt = $sosmed['tiktok'] ?? 'https://tiktok.com/@mim_bodaskarangjati';
$wa = 'https://wa.me/6281133208870';
?>
    </main> <!-- Penutup tag main dari header -->
    
    <footer class="modern-footer">
        <!-- Wave Decoration -->
        <div class="footer-wave">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="#0B3D91" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,170.7C1248,160,1344,128,1392,112L1440,96L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
            </svg>
        </div>
        
        <!-- Footer Main Content -->
        <div class="footer-main">
            <div class="container">
                <!-- 4 Kolom Grid -->
                <div class="footer-grid">
                    
                    <!-- Kolom 1: Brand & Sosmed -->
                    <div class="footer-col">
                        <div class="footer-brand">
                            <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo" class="footer-logo">
                            <div class="footer-brand-text">
                                <h3>MI Muhammadiyah</h3>
                                <span>Bodaskarangjati</span>
                            </div>
                        </div>
                        <p class="footer-description">
                            Madrasah Ibtidaiyah yang unggul dalam prestasi, berkarakter Islami, dan berwawasan global.
                        </p>
                        
                        <!-- Social Media Icons -->
                        <div class="footer-social">
                            <h4>Ikuti Kami</h4>
                            <div class="social-links">
                                <a href="<?= $fb ?>" target="_blank" class="social-link facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="<?= $ig ?>" target="_blank" class="social-link instagram"><i class="fab fa-instagram"></i></a>
                                <a href="<?= $yt ?>" target="_blank" class="social-link youtube"><i class="fab fa-youtube"></i></a>
                                <a href="<?= $tt ?>" target="_blank" class="social-link tiktok"><i class="fab fa-tiktok"></i></a>
                                <a href="<?= $wa ?>" target="_blank" class="social-link whatsapp"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kolom 2: Link Cepat -->
                    <div class="footer-col">
                        <h4 class="footer-title">Link Cepat</h4>
                        <ul class="footer-links">
                            <li><a href="<?= BASE_URL ?>/index.php">Beranda</a></li>
                            <li><a href="<?= BASE_URL ?>/profil/sejarah.php">Sejarah</a></li>
                            <li><a href="<?= BASE_URL ?>/profil/visi_misi.php">Visi & Misi</a></li>
                            <li><a href="<?= BASE_URL ?>/profil/guru_staff.php">Guru & Staff</a></li>
                            <li><a href="<?= BASE_URL ?>/kesiswaan/prestasi.php">Prestasi</a></li>
                            <li><a href="<?= BASE_URL ?>/berita/pengumuman.php">Berita</a></li>
                        </ul>
                    </div>
                    
                    <!-- Kolom 3: Layanan -->
                    <div class="footer-col">
                        <h4 class="footer-title">Layanan</h4>
                        <ul class="footer-links">
                            <li><a href="<?= BASE_URL ?>/ppdb.php">PPDB Online</a></li>
                            <li><a href="<?= BASE_URL ?>/galeri/foto.php">Galeri Foto</a></li>
                            <li><a href="<?= BASE_URL ?>/galeri/video.php">Galeri Video</a></li>
                            <li><a href="<?= BASE_URL ?>/berita/agenda.php">Agenda</a></li>
                            <li><a href="<?= BASE_URL ?>/kesiswaan/ekstrakurikuler.php">Ekstrakurikuler</a></li>
                            <li><a href="<?= BASE_URL ?>/profil/sarana.php">Sarana</a></li>
                        </ul>
                    </div>
                    
                    <!-- Kolom 4: Kontak & Map -->
                    <div class="footer-col">
                        <h4 class="footer-title">Kontak & Lokasi</h4>
                        
                        <ul class="footer-contact">
                            <li><i class="fas fa-map-marker-alt"></i> <span><?= htmlspecialchars($alamat) ?></span></li>
                            <li><i class="fas fa-phone-alt"></i> <span><?= htmlspecialchars($telepon) ?></span></li>
                            <li><i class="fas fa-envelope"></i> <span><?= htmlspecialchars($email) ?></span></li>
                            <li><i class="fas fa-clock"></i> <span><?= htmlspecialchars($jam_operasional) ?></span></li>
                        </ul>
                        
                        <!-- Map Mini -->
                        <div class="footer-map">
                            <?php if (!empty($maps_embed)): ?>
                                <?= $maps_embed ?>
                            <?php else: ?>
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.456789!2d109.456789!3d-7.456789!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7...!2sMI%20Muhammadiyah%20Bodaskarangjati!5e0!3m2!1sid!2sid!4v1234567890" width="100%" height="80" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            <?php endif; ?>
                            <a href="https://www.google.com/maps/search/<?= urlencode($alamat) ?>" target="_blank" class="map-link">
                                <i class="fas fa-directions"></i> Petunjuk Arah
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom dengan ICON LOGIN SAJA -->
        <div class="footer-bottom">
            <div class="container">
                <!-- Copyright dengan icon login setelah Bodaskarangjati -->
                <div class="copyright-wrapper">
                    <span class="copyright-text">
                        © <?= date('Y') ?> MI Muhammadiyah Bodaskarangjati
                    </span>
                    <a href="<?= BASE_URL ?>/admin/login.php" class="admin-login-icon" target="_blank" title="Login Admin">
                        <i class="fas fa-lock"></i>
                    </a>
                </div>
                
                <div class="credit">
                    All rights reserved
                    <span class="developer-credit"> <span class="developer-credit"> | Developed by: Rendi Mulyadi (UM Purwokerto)</span></span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>