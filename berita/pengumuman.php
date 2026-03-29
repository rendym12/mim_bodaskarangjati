<?php
// Include config untuk BASE_URL
require_once '../includes/config.php';
include '../includes/header.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Hitung total data
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman WHERE status = 'publish'");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data pengumuman
$query = mysqli_query($conn, "SELECT * FROM pengumuman WHERE status = 'publish' ORDER BY tanggal DESC, id DESC LIMIT $offset, $limit");
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="container">
        <h1>Pengumuman</h1>
        <p>Informasi terbaru dan pengumuman resmi dari MI Muhammadiyah Bodaskarangjati</p>
    </div>
</div>

<div class="container">
    <?php if (mysqli_num_rows($query) > 0): ?>
        
        <!-- GRID PENGUMUMAN -->
        <div class="pengumuman-grid">
            <?php while ($row = mysqli_fetch_assoc($query)): ?>
            <div class="pengumuman-card">
                <?php if (!empty($row['gambar'])): ?>
                <div class="pengumuman-gambar">
                    <img src="<?= BASE_URL ?>/uploads/pengumuman/<?= $row['gambar'] ?>" 
                         alt="<?= htmlspecialchars($row['judul']) ?>"
                         onerror="this.src='<?= BASE_URL ?>/assets/img/pengumuman-default.jpg'">
                    <div class="pengumuman-date-badge">
                        <span class="date-day"><?= date('d', strtotime($row['tanggal'])) ?></span>
                        <span class="date-month"><?= date('M', strtotime($row['tanggal'])) ?></span>
                    </div>
                </div>
                <?php else: ?>
                <div class="pengumuman-gambar pengumuman-gambar-default">
                    <i class="fas fa-bullhorn"></i>
                    <div class="pengumuman-date-badge">
                        <span class="date-day"><?= date('d', strtotime($row['tanggal'])) ?></span>
                        <span class="date-month"><?= date('M', strtotime($row['tanggal'])) ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="pengumuman-content">
                    <h3 class="pengumuman-judul">
                        <a href="pengumuman_detail.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['judul']) ?></a>
                    </h3>
                    
                    <div class="pengumuman-meta">
                        <span class="pengumuman-penulis">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($row['penulis'] ?? 'Admin') ?>
                        </span>
                        <span class="pengumuman-kategori">
                            <i class="fas fa-tag"></i> Pengumuman
                        </span>
                    </div>
                    
                    <p class="pengumuman-excerpt">
                        <?= htmlspecialchars(substr(strip_tags($row['isi']), 0, 150)) ?>...
                    </p>
                    
                    <div class="pengumuman-footer">
                        <a href="pengumuman_detail.php?id=<?= $row['id'] ?>" class="btn-baca">
                            Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                        </a>
                        
                        <?php if (!empty($row['file_lampiran'])): ?>
                        <a href="<?= BASE_URL ?>/uploads/lampiran/<?= $row['file_lampiran'] ?>" class="btn-lampiran" download>
                            <i class="fas fa-paperclip"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <!-- PAGINATION -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="page-link prev">
                <i class="fas fa-chevron-left"></i> Sebelumnya
            </a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="page-link active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?>" class="page-link"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>" class="page-link next">
                Selanjutnya <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-bullhorn"></i>
            <p>Belum ada pengumuman</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>