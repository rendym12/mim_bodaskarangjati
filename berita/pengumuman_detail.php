<?php
// Include config untuk BASE_URL
require_once '../includes/config.php';
include '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data pengumuman
$query = mysqli_query($conn, "SELECT * FROM pengumuman WHERE id = $id AND status = 'publish'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: pengumuman.php");
    exit;
}

// Ambil pengumuman terkait
$related_query = mysqli_query($conn, "SELECT * FROM pengumuman WHERE id != $id AND status = 'publish' ORDER BY tanggal DESC LIMIT 3");
?>

<!-- PAGE HEADER -->
<div class="page-header page-header-small">
    <div class="container">
        <h1><?= htmlspecialchars($data['judul']) ?></h1>
        <div class="header-meta">
            <span><i class="fas fa-calendar"></i> <?= date('d F Y', strtotime($data['tanggal'])) ?></span>
            <span><i class="fas fa-user"></i> <?= htmlspecialchars($data['penulis'] ?? 'Admin') ?></span>
        </div>
    </div>
</div>

<div class="container">
    <div class="detail-wrapper">
        <!-- Konten Utama -->
        <article class="detail-content">
            <?php if (!empty($data['gambar'])): ?>
            <div class="detail-gambar">
                <img src="<?= BASE_URL ?>/uploads/pengumuman/<?= $data['gambar'] ?>" 
                     alt="<?= htmlspecialchars($data['judul']) ?>">
            </div>
            <?php endif; ?>
            
            <div class="detail-isi">
                <?= nl2br(htmlspecialchars($data['isi'])) ?>
            </div>
            
            <?php if (!empty($data['file_lampiran'])): ?>
            <div class="detail-lampiran">
                <h3><i class="fas fa-paperclip"></i> Lampiran</h3>
                <a href="<?= BASE_URL ?>/uploads/lampiran/<?= $data['file_lampiran'] ?>" class="btn-download" download>
                    <i class="fas fa-download"></i> Download Lampiran
                </a>
            </div>
            <?php endif; ?>
            
            <div class="detail-share">
                <h4>Bagikan Pengumuman:</h4>
                <div class="share-buttons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL . '/pengumuman/detail.php?id=' . $id) ?>" target="_blank" class="share-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode(BASE_URL . '/pengumuman/detail.php?id=' . $id) ?>&text=<?= urlencode($data['judul']) ?>" target="_blank" class="share-btn twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://wa.me/?text=<?= urlencode($data['judul'] . ' - ' . BASE_URL . '/pengumuman/detail.php?id=' . $id) ?>" target="_blank" class="share-btn whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </article>
        
        <!-- Sidebar -->
        <aside class="detail-sidebar">
            <!-- Informasi -->
            <div class="sidebar-card info-card">
                <h3><i class="fas fa-info-circle"></i> Informasi</h3>
                <ul class="info-list">
                    <li><i class="fas fa-calendar-alt"></i> Tanggal: <?= date('d/m/Y', strtotime($data['tanggal'])) ?></li>
                    <li><i class="fas fa-user"></i> Penulis: <?= htmlspecialchars($data['penulis'] ?? 'Admin') ?></li>
                    <li><i class="fas fa-tag"></i> Kategori: Pengumuman</li>
                    <!-- BARIS DILIHAT DIHAPUS DARI SINI -->
                </ul>
            </div>
            
            <!-- Pengumuman Terkait -->
            <?php if (mysqli_num_rows($related_query) > 0): ?>
            <div class="sidebar-card related-card">
                <h3><i class="fas fa-link"></i> Pengumuman Lainnya</h3>
                <ul class="related-list">
                    <?php while ($related = mysqli_fetch_assoc($related_query)): ?>
                    <li>
                        <a href="pengumuman_detail.php?id=<?= $related['id'] ?>">
                            <i class="fas fa-file-alt"></i>
                            <span><?= htmlspecialchars($related['judul']) ?></span>
                            <small><?= date('d/m/Y', strtotime($related['tanggal'])) ?></small>
                        </a>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Tombol Kembali -->
            <a href="pengumuman.php" class="btn-kembali">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
        </aside>
    </div>
</div>

<?php include '../includes/footer.php'; ?>