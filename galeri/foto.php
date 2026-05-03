<?php
// Include config untuk BASE_URL
require_once '../includes/config.php';
include '../includes/header.php';

// Filter kategori
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'semua';

// Query berdasarkan kategori
if ($kategori != 'semua') {
    $query = mysqli_query($conn, "SELECT * FROM galeri_foto WHERE kategori = '$kategori' ORDER BY urutan ASC, id DESC");
} else {
    $query = mysqli_query($conn, "SELECT * FROM galeri_foto ORDER BY urutan ASC, id DESC");
}

// Ambil semua kategori unik untuk filter
$kategori_query = mysqli_query($conn, "SELECT DISTINCT kategori FROM galeri_foto WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori");
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="container">
        <h1>Galeri Foto</h1>
        <p>Dokumentasi kegiatan dan momen berharga di MI Muhammadiyah Bodaskarangjati</p>
    </div>
</div>

<div class="container">
    <!-- FILTER KATEGORI -->
    <div class="gallery-filter">
        <a href="?kategori=semua" class="filter-btn <?= $kategori == 'semua' ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i> Semua
        </a>
        <?php while ($kat = mysqli_fetch_assoc($kategori_query)): ?>
        <a href="?kategori=<?= urlencode($kat['kategori']) ?>" class="filter-btn <?= $kategori == $kat['kategori'] ? 'active' : '' ?>">
            <i class="fas fa-folder"></i> <?= htmlspecialchars($kat['kategori']) ?>
        </a>
        <?php endwhile; ?>
    </div>
    
    <?php if (mysqli_num_rows($query) > 0): ?>
        
     <!-- GALLERY GRID -->
<div class="gallery-grid">
    <?php while ($row = mysqli_fetch_assoc($query)): ?>
    <div class="gallery-item" data-aos="fade-up">
        <div class="gallery-card">
            <div class="gallery-image">
                <img src="<?= BASE_URL ?>/uploads/galeri_foto/<?= $row['file_foto'] ?>" 
                     alt="<?= htmlspecialchars($row['judul']) ?>"
                     loading="lazy">
                <div class="gallery-overlay">
                    <div class="overlay-content">
                        <button class="btn-zoom" onclick="openLightbox(this)">
                            <i class="fas fa-search-plus"></i> Perbesar
                        </button>
                    </div>
                </div>
            </div>
            <?php if (!empty($row['keterangan'])): ?>
            <div class="gallery-caption">
                <p><?= htmlspecialchars($row['keterangan']) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; ?>
</div>
        
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-images"></i>
            <p>Belum ada foto dalam kategori ini</p>
            <a href="?kategori=semua" class="btn-back">Lihat Semua Foto</a>
        </div>
    <?php endif; ?>
</div>

<!-- LIGHTBOX MODAL -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close">&times;</span>
    <div class="lightbox-content" onclick="event.stopPropagation()">
        <img id="lightbox-img" src="" alt="">
        <div id="lightbox-caption" class="lightbox-caption"></div>
        <button class="lightbox-prev" onclick="changeImage(-1)">&#10094;</button>
        <button class="lightbox-next" onclick="changeImage(1)">&#10095;</button>
    </div>
</div>

<?php include '../includes/footer.php'; ?>