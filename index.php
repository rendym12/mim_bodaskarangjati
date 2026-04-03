<?php
include "includes/db.php";
include "includes/header.php";

// ==============================================
// 1. AMBIL DATA UNTUK HERO SLIDER
// ==============================================
$q_hero = mysqli_query($conn, "SELECT * FROM hero_slider WHERE status = 'aktif' ORDER BY urutan ASC");
$hero_slides = [];
while ($row = mysqli_fetch_assoc($q_hero)) {
    $hero_slides[] = $row;
}

// ==============================================
// 2. AMBIL DATA VISI & MISI
// ==============================================
$q_visi = mysqli_query($conn, "SELECT * FROM visi_misi LIMIT 1");
$data_visi = mysqli_fetch_assoc($q_visi);
$visi_tersedia = ($data_visi && !empty($data_visi['visi']));
$misi_tersedia = ($data_visi && !empty($data_visi['misi']));

// ==============================================
// 3. AMBIL DATA SAMBUTAN
// ==============================================
$q_sambutan = mysqli_query($conn, "SELECT * FROM sambutan LIMIT 1");
$data_sambutan = mysqli_fetch_assoc($q_sambutan);
$sambutan_tersedia = ($data_sambutan && !empty($data_sambutan['sambutan']));

// ==============================================
// 4. HITUNG STATISTIK
// ==============================================
$jml_guru      = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM guru_staff"));
$jml_ekstra    = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM ekstrakurikuler"));
$jml_sarana    = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM sarana"));
$jml_prestasi  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM prestasi"));

// ==============================================
// 5. AMBIL DATA UNTUK KONTEN
// ==============================================
$q_berita = mysqli_query($conn, "SELECT * FROM pengumuman WHERE status='publish' ORDER BY tanggal DESC LIMIT 3");
$q_prestasi_terbaru = mysqli_query($conn, "SELECT * FROM prestasi ORDER BY tahun DESC LIMIT 4");
$q_pembiasaan = mysqli_query($conn, "SELECT * FROM pembiasaan ORDER BY urutan ASC");
$q_ekstra = mysqli_query($conn, "SELECT * FROM ekstrakurikuler ORDER BY urutan ASC LIMIT 4");
$q_agenda_terbaru = mysqli_query($conn, "SELECT * FROM agenda WHERE tanggal_mulai >= CURDATE() ORDER BY tanggal_mulai ASC LIMIT 4");
$q_galeri = mysqli_query($conn, "SELECT * FROM galeri_foto ORDER BY id DESC LIMIT 6");
$q_sarana = mysqli_query($conn, "SELECT * FROM sarana ORDER BY urutan ASC LIMIT 4");

// ==============================================
// 6. AMBIL DATA KONTAK
// ==============================================
$q_kontak = mysqli_query($conn, "SELECT * FROM kontak LIMIT 1");
$kontak = mysqli_fetch_assoc($q_kontak);

// Format nomor WhatsApp
$wa_number = "6281133208870";
if($kontak && !empty($kontak['telepon'])) {
    $wa_number = preg_replace('/[^0-9]/', '', $kontak['telepon']);
    if(substr($wa_number, 0, 1) == '0') {
        $wa_number = '62' . substr($wa_number, 1);
    }
}

// Base URL untuk gambar
$base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
?>

<!-- ========== 1. HERO SLIDER - Promosi Utama/PPDB ========== -->
<section class="hero-slider">
    <div class="slider-container">
        <?php if (empty($hero_slides)): ?>
        <div class="slide active" style="background-image: url('assets/img/hero-bg-1.jpg');">
            <div class="slide-content">
                <span class="slide-badge">MI MUHAMMADIYAH BODASKARANGJATI</span>
                <h1 class="slide-title">Mencetak Generasi Beriman & Berakhlak</h1>
                <p class="slide-subtitle">Madrasah Ibtidaiyah yang berkomitmen memberikan pendidikan berkualitas dengan nilai-nilai Islam</p>
                <div class="slide-buttons">
                    <a href="ppdb.php" class="btn-primary btn-large">
                        <i class="fas fa-edit"></i> Daftar PPDB 2026/2027
                    </a>
                    <a href="profil/sejarah.php" class="btn-outline btn-large">
                        <i class="fas fa-university"></i> Profil Madrasah
                    </a>
                </div>
            </div>
        </div>
        <?php else: ?>
        <?php foreach ($hero_slides as $index => $slide): ?>
        <div class="slide <?= $index === 0 ? 'active' : '' ?>" 
             style="background-image: url('<?= !empty($slide['gambar']) ? 'uploads/hero/' . $slide['gambar'] : 'assets/img/hero-bg-' . ($index + 1) . '.jpg' ?>');">
            <div class="slide-content">
                <?php if (!empty($slide['badge'])): ?>
                <span class="slide-badge"><?= htmlspecialchars($slide['badge']) ?></span>
                <?php endif; ?>
                
                <h1 class="slide-title"><?= htmlspecialchars($slide['judul']) ?></h1>
                
                <?php if (!empty($slide['subjudul'])): ?>
                <p class="slide-subtitle"><?= htmlspecialchars($slide['subjudul']) ?></p>
                <?php endif; ?>
                
                <div class="slide-buttons">
                    <?php if (!empty($slide['tombol_text']) && !empty($slide['tombol_link'])): ?>
                    <a href="<?= htmlspecialchars($slide['tombol_link']) ?>" class="btn-primary btn-large">
                        <i class="fas fa-edit"></i> <?= htmlspecialchars($slide['tombol_text']) ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($index === 0): ?>
                    <a href="profil/sejarah.php" class="btn-outline btn-large">
                        <i class="fas fa-university"></i> Profil Madrasah
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php if (count($hero_slides) > 1): ?>
    <div class="slider-nav">
        <button class="slider-prev"><i class="fas fa-chevron-left"></i></button>
        <button class="slider-next"><i class="fas fa-chevron-right"></i></button>
    </div>
    <div class="slider-dots"></div>
    <?php endif; ?>
</section>

<!-- ========== 2. BERITA & PENGUMUMAN ========== -->
<section class="berita-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">BERITA & PENGUMUMAN</span>
            <h2 class="section-title">Informasi Terbaru</h2>
        </div>
        
        <div class="berita-grid">
            <?php
            if(mysqli_num_rows($q_berita) > 0) {
                while($berita = mysqli_fetch_assoc($q_berita)){
            ?>
            <div class="berita-card">
                <div class="berita-gambar">
                    <?php if (!empty($berita['gambar'])): ?>
                        <img src="uploads/pengumuman/<?= $berita['gambar'] ?>" alt="<?= $berita['judul'] ?>">
                    <?php else: ?>
                        <div class="berita-gambar-default">
                            <i class="fas fa-newspaper"></i>
                        </div>
                    <?php endif; ?>
                    <div class="berita-date-badge">
                        <i class="far fa-calendar-alt"></i> <?= date('d M', strtotime($berita['tanggal'])) ?>
                    </div>
                </div>
                <div class="berita-content">
                    <h3><?= htmlspecialchars($berita['judul']) ?></h3>
                    <p><?= htmlspecialchars(substr(strip_tags($berita['isi']), 0, 120)) ?>...</p>
                    <div class="berita-footer">
                        <a href="berita/pengumuman.php?id=<?= $berita['id'] ?>" class="btn-read-more">
                            <span>Baca Selengkapnya</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php } } else { ?>
            <div class="berita-card">
                <div class="berita-gambar">
                    <div class="berita-gambar-default">
                        <i class="fas fa-newspaper"></i>
                    </div>
                </div>
                <div class="berita-content">
                    <h3>Belum Ada Berita</h3>
                    <p>Saat ini belum tersedia berita atau pengumuman.</p>
                    <div class="berita-footer">
                        <span class="text-muted">Informasi akan segera ditambahkan</span>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <?php if(mysqli_num_rows($q_berita) > 0): ?>
        <div class="section-footer">
            <a href="berita/pengumuman.php" class="btn-more">Lihat Semua Berita <i class="fas fa-arrow-right"></i></a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========== 3. SAMBUTAN KEPALA ========== -->
<section class="sambutan-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">SAMBUTAN KEPALA MADRASAH</span>
            <h2 class="section-title">Kata Sambutan</h2>
        </div>
        
        <div class="sambutan-wrapper">
            <div class="sambutan-image">
                <?php 
                $foto_kepala = $data_sambutan['foto'] ?? '';
                $path_foto = '';
                if (!empty($foto_kepala) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/mim_bodaskarangjati/uploads/' . $foto_kepala)) {
                    $path_foto = 'uploads/' . $foto_kepala;
                } else {
                    $path_foto = 'assets/img/default-avatar.jpg';
                }
                ?>
                <img src="<?= $path_foto ?>" alt="Kepala Madrasah">
                <div class="sambutan-quote-icon">
                    <i class="fas fa-quote-right"></i>
                </div>
            </div>
            <div class="sambutan-content">
                <h3><?= !empty($data_sambutan['nama_kepala']) ? htmlspecialchars($data_sambutan['nama_kepala']) : 'Kepala Madrasah' ?></h3>
                <span class="sambutan-jabatan">Kepala Madrasah</span>
                <div class="sambutan-text">
                    <p>
                        <?php 
                        if ($sambutan_tersedia) {
                            echo nl2br(htmlspecialchars($data_sambutan['sambutan']));
                        } else {
                            echo "Sambutan kepala madrasah belum tersedia.";
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== 4. STATISTIK ========== -->
<section class="stats-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">STATISTIK</span>
            <h2 class="section-title">Madrasah Dalam Angka</h2>
        </div>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat-number"><?= $jml_guru ?></div>
                <div class="stat-label">Guru & Staff</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-futbol"></i></div>
                <div class="stat-number"><?= $jml_ekstra ?></div>
                <div class="stat-label">Ekstrakurikuler</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-building"></i></div>
                <div class="stat-number"><?= $jml_sarana ?></div>
                <div class="stat-label">Fasilitas</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-trophy"></i></div>
                <div class="stat-number"><?= $jml_prestasi ?></div>
                <div class="stat-label">Total Prestasi</div>
            </div>
        </div>
    </div>
</section>

<!-- ========== 5. PRESTASI ========== -->
<section class="prestasi-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">PRESTASI</span>
            <h2 class="section-title">Prestasi Terbaru</h2>
        </div>
        
        <div class="prestasi-grid">
            <?php
            if(mysqli_num_rows($q_prestasi_terbaru) > 0) {
                while($prestasi = mysqli_fetch_assoc($q_prestasi_terbaru)) {
            ?>
            <div class="prestasi-card">
                <div class="prestasi-gambar">
                    <?php if (!empty($prestasi['gambar'])): ?>
                        <img src="uploads/prestasi/<?= $prestasi['gambar'] ?>" alt="<?= $prestasi['nama_prestasi'] ?>">
                    <?php else: ?>
                        <div class="prestasi-gambar-default">
                            <i class="fas fa-trophy"></i>
                        </div>
                    <?php endif; ?>
                    <div class="prestasi-overlay">
                        <span class="prestasi-badge"><?= $prestasi['tahun'] ?></span>
                    </div>
                </div>
                <div class="prestasi-content">
                    <div class="prestasi-header">
                        <span class="prestasi-year"><?= $prestasi['tahun'] ?></span>
                        <span class="prestasi-level"><?= htmlspecialchars($prestasi['tingkat']) ?></span>
                    </div>
                    <h3><?= htmlspecialchars($prestasi['nama_prestasi']) ?></h3>
                    <p class="prestasi-org">
                        <i class="fas fa-building"></i> <?= htmlspecialchars($prestasi['penyelenggara'] ?? '-') ?>
                    </p>
                    <p class="prestasi-desc"><?= htmlspecialchars(substr($prestasi['deskripsi'] ?? '', 0, 80)) ?>...</p>
                    <div class="prestasi-footer">
                        <span class="prestasi-medali">🏆</span>
                        <span class="prestasi-urutan">#<?= $prestasi['urutan'] ?? '1' ?></span>
                    </div>
                </div>
            </div>
            <?php } } else { ?>
            <div class="prestasi-card">
                <div class="prestasi-gambar">
                    <div class="prestasi-gambar-default">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
                <div class="prestasi-content">
                    <h3>Belum Ada Data Prestasi</h3>
                    <p>Saat ini belum tersedia data prestasi.</p>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <?php if(mysqli_num_rows($q_prestasi_terbaru) > 0): ?>
        <div class="section-footer">
            <a href="kesiswaan/prestasi.php" class="btn-more">Lihat Semua Prestasi<i class="fas fa-arrow-right"></i></a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========== 6. PEMBIASAAN PAGI ========== -->
<section class="pembiasaan-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">PEMBIASAAN PAGI</span>
            <h2 class="section-title">Kegiatan Rutin Harian</h2>
        </div>
        
        <div class="pembiasaan-grid">
            <?php
            if(mysqli_num_rows($q_pembiasaan) > 0) {
                while($pembiasaan = mysqli_fetch_assoc($q_pembiasaan)) {
            ?>
            <div class="pembiasaan-card">
                <div class="pembiasaan-icon">
                    <i class="fas <?= $pembiasaan['ikon'] ?>"></i>
                </div>
                <h3><?= htmlspecialchars($pembiasaan['nama_kegiatan']) ?></h3>
                <p><?= htmlspecialchars($pembiasaan['deskripsi'] ?? '') ?></p>
            </div>
            <?php } } else { ?>
            <div class="pembiasaan-card">
                <div class="pembiasaan-icon"><i class="fas fa-info-circle"></i></div>
                <h3>Belum Ada Data</h3>
                <p>Data kegiatan pembiasaan belum tersedia.</p>
            </div>
            <?php } ?>
        </div>
    </div>
</section>

<!-- ========== 7. EKSTRAKURIKULER ========== -->
<section class="eks-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">EKSTRAKURIKULER</span>
            <h2 class="section-title">Kegiatan Pengembangan Bakat</h2>
            <p class="section-subtitle">Berbagai pilihan kegiatan untuk mengembangkan potensi siswa</p>
        </div>
        
        <div class="eks-grid">
            <?php 
            if(mysqli_num_rows($q_ekstra) > 0) {
                while($ekstra = mysqli_fetch_assoc($q_ekstra)) { 
            ?>
            <div class="eks-card">
                <div class="eks-icon">
                    <i class="fas <?= $ekstra['ikon'] ?? 'fa-star' ?>"></i>
                </div>
                <h3><?= htmlspecialchars($ekstra['nama_eks']) ?></h3>
                <p class="eks-meta"><i class="fas fa-user"></i> <?= htmlspecialchars($ekstra['pembina'] ?? '-') ?></p>
                <p class="eks-desc"><?= htmlspecialchars(substr(strip_tags($ekstra['deskripsi'] ?? ''), 0, 70)) ?>...</p>
            </div>
            <?php } } else { ?>
            <div class="eks-card">
                <div class="eks-icon"><i class="fas fa-info-circle"></i></div>
                <h3>Belum Ada Data</h3>
                <p>Data ekstrakurikuler belum tersedia.</p>
            </div>
            <?php } ?>
        </div>
        
        <?php if(mysqli_num_rows($q_ekstra) > 0): ?>
        <div class="section-footer">
            <a href="kesiswaan/ekstrakurikuler.php" class="btn-more">Lihat Semua ekstrakurikuler <i class="fas fa-arrow-right"></i></a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========== 8. AGENDA ========== -->
<section class="agenda-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">AGENDA</span>
            <h2 class="section-title">Agenda Mendatang</h2>
        </div>
        
        <div class="agenda-list">
            <?php 
            if(mysqli_num_rows($q_agenda_terbaru) > 0) {
                while($agenda = mysqli_fetch_assoc($q_agenda_terbaru)) { 
                    $today = date('Y-m-d');
                    $status = 'akan-datang';
                    if ($agenda['tanggal_mulai'] <= $today && $agenda['tanggal_selesai'] >= $today) {
                        $status = 'berlangsung';
                    } elseif ($agenda['tanggal_selesai'] < $today) {
                        $status = 'selesai';
                    }
            ?>
            <div class="agenda-item <?= $status ?>">
                <div class="agenda-date">
                    <span class="day"><?= date('d', strtotime($agenda['tanggal_mulai'])) ?></span>
                    <span class="month"><?= date('M', strtotime($agenda['tanggal_mulai'])) ?></span>
                </div>
                <div class="agenda-content">
                    <h3><?= htmlspecialchars($agenda['nama_agenda']) ?>
                        <?php if($status == 'berlangsung'): ?>
                            <span class="agenda-status-badge berlangsung">Berlangsung</span>
                        <?php elseif($status == 'akan-datang'): ?>
                            <span class="agenda-status-badge akan-datang">Akan Datang</span>
                        <?php endif; ?>
                    </h3>
                    <p class="agenda-meta">
                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($agenda['lokasi'] ?? 'Madrasah') ?></span>
                        <span><i class="fas fa-clock"></i> <?= date('H:i', strtotime($agenda['tanggal_mulai'])) ?> WIB</span>
                    </p>
                </div>
            </div>
            <?php } } else { ?>
            <div class="agenda-item akan-datang">
                <div class="agenda-date">
                    <span class="day">-</span>
                    <span class="month">-</span>
                </div>
                <div class="agenda-content">
                    <h3>Belum Ada Agenda</h3>
                    <p class="agenda-meta">Saat ini belum tersedia agenda mendatang.</p>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <?php if(mysqli_num_rows($q_agenda_terbaru) > 0): ?>
        <div class="section-footer">
            <a href="berita/agenda.php" class="btn-more">Lihat Semua Agenda <i class="fas fa-arrow-right"></i></a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========== 9. GALERI ========== -->
<section class="galeri-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">GALERI</span>
            <h2 class="section-title">Dokumentasi Kegiatan</h2>
        </div>
        
        <div class="galeri-grid">
            <?php 
            if(mysqli_num_rows($q_galeri) > 0) {
                while($galeri = mysqli_fetch_assoc($q_galeri)) { 
            ?>
            <div class="galeri-item">
                <img src="uploads/galeri_foto/<?= $galeri['file_foto'] ?>" alt="<?= $galeri['judul'] ?>">
                <div class="galeri-overlay">
                    <h4><?= htmlspecialchars($galeri['judul']) ?></h4>
                    <?php if(!empty($galeri['kategori'])): ?>
                    <p><i class="fas fa-folder"></i> <?= htmlspecialchars($galeri['kategori']) ?></p>
                    <?php endif; ?>
                </div>
                <?php if(!empty($galeri['kategori'])): ?>
                <span class="galeri-badge"><?= htmlspecialchars($galeri['kategori']) ?></span>
                <?php endif; ?>
            </div>
            <?php } } else { ?>
            <div class="galeri-item" style="grid-column: 1/-1; text-align: center; padding: 60px; background: #f1f5f9; border-radius: 12px;">
                <i class="fas fa-images" style="font-size: 3rem; color: #94a3b8; margin-bottom: 15px;"></i>
                <p style="color: #64748b;">Belum ada dokumentasi galeri</p>
            </div>
            <?php } ?>
        </div>
        
        <?php if(mysqli_num_rows($q_galeri) > 0): ?>
        <div class="section-footer">
            <a href="galeri/foto.php" class="btn-more">Lihat Semua Dokumentasi<i class="fas fa-arrow-right"></i></a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========== 10. SARANA - DENGAN FOTO ========== -->
<section class="sarana-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">FASILITAS</span>
            <h2 class="section-title">Sarana Prasarana</h2>
        </div>
        
        <div class="sarana-grid">
            <?php
            if(mysqli_num_rows($q_sarana) > 0) {
                while($sarana = mysqli_fetch_assoc($q_sarana)) {
            ?>
            <div class="sarana-item">
                <!-- TAMPILKAN FOTO SEPERTI DI SARANA.PHP -->
                <?php if (!empty($sarana['gambar']) && file_exists('uploads/sarana/' . $sarana['gambar'])): ?>
                <div class="sarana-img">
                    <img src="uploads/sarana/<?= $sarana['gambar'] ?>" alt="<?= htmlspecialchars($sarana['nama_sarana']) ?>">
                </div>
                <?php else: ?>
                <div class="sarana-icon">
                    <i class="fas <?= $sarana['ikon'] ?? 'fa-building' ?>"></i>
                </div>
                <?php endif; ?>
                <h4><?= htmlspecialchars($sarana['nama_sarana']) ?></h4>
                <p><?= htmlspecialchars(substr($sarana['keterangan'] ?? '', 0, 60)) ?>...</p>
            </div>
            <?php } } else { ?>
            <div class="sarana-item">
                <div class="sarana-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h4>Belum Ada Data</h4>
                <p>Data sarana prasarana belum tersedia.</p>
            </div>
            <?php } ?>
        </div>
        
        <?php if(mysqli_num_rows($q_sarana) > 0): ?>
        <div class="section-footer">
            <a href="profil/sarana.php" class="btn-more">Lihat Semua Fasilitas<i class="fas fa-arrow-right"></i></a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========== 11. VISI MISI ========== -->
<section class="visi-misi-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">VISI & MISI</span>
            <h2 class="section-title">Landasan dan Tujuan</h2>
        </div>
        
        <div class="visi-misi-wrapper">
            <div class="visi-card">
                <div class="visi-icon"><i class="fas fa-eye"></i></div>
                <h3>Visi</h3>
                <p><?= $visi_tersedia ? strip_tags($data_visi['visi']) : 'Visi madrasah belum tersedia.' ?></p>
            </div>
            <div class="misi-card">
                <div class="misi-icon"><i class="fas fa-list"></i></div>
                <h3>Misi</h3>
                <?php 
                if ($misi_tersedia) {
                    echo $data_visi['misi'];
                } else {
                    echo '<p>Misi madrasah belum tersedia.</p>';
                }
                ?>
            </div>
        </div>
        
        <?php if($visi_tersedia || $misi_tersedia): ?>
        <div class="section-footer">
            <a href="profil/visi_misi.php" class="btn-more">Lihat Selengkapnya <i class="fas fa-arrow-right"></i></a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include "includes/footer.php"; ?>