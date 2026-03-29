<?php
// Include config dulu untuk mendapatkan BASE_URL
require_once '../includes/config.php';

// Include header (sudah include config)
include '../includes/header.php';

// Koneksi database sudah ada di config, tinggal pakai $conn
$query = "SELECT * FROM sejarah ORDER BY tahun_berdiri ASC";
$result = mysqli_query($conn, $query);
$sejarah = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Ambil tahun berdiri pertama untuk header (yang tertua)
$tahun_pertama = !empty($sejarah) ? $sejarah[0]['tahun_berdiri'] : '';
?>

<!-- PAGE HEADER - HANYA JUDUL -->
<div class="page-header">
    <div class="container">
        <h1>
            <?php if (!empty($sejarah)): ?>
                <?= htmlspecialchars($sejarah[0]['judul']) ?>
            <?php else: ?>
                Sejarah MI Muhammadiyah Bodaskarangjati
            <?php endif; ?>
        </h1>
        <?php if (!empty($tahun_pertama)): ?>
        <div class="header-badge">
            <i class="fas fa-calendar-alt"></i> Berdiri Sejak <?= htmlspecialchars($tahun_pertama) ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <div class="sejarah-wrapper">
        <!-- Sidebar Timeline -->
        <aside class="sejarah-sidebar">
            <div class="sidebar-title">
                <i class="fas fa-history"></i>
                <h3>Timeline Perjalanan</h3>
            </div>
            <div class="timeline-list">
                <?php if (!empty($sejarah)): ?>
                    <?php foreach ($sejarah as $index => $item): ?>
                    <a href="#sejarah-<?= $item['id'] ?>" class="timeline-item <?= $index === 0 ? 'active' : '' ?>">
                        <span class="timeline-tahun"><?= htmlspecialchars($item['tahun_berdiri']) ?></span>
                        <span class="timeline-judul"><?= htmlspecialchars($item['judul']) ?></span>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="timeline-empty">Belum ada data timeline</div>
                <?php endif; ?>
            </div>
        </aside>

        <!-- Konten Sejarah -->
        <div class="sejarah-content">
            <?php if (empty($sejarah)): ?>
                <div class="sejarah-empty">
                    <i class="fas fa-history"></i>
                    <p>Belum ada data sejarah</p>
                </div>
            <?php else: ?>
                <?php foreach ($sejarah as $item): ?>
                <div class="sejarah-card" id="sejarah-<?= $item['id'] ?>">
                    <div class="card-tahun">
                        <span><?= htmlspecialchars($item['tahun_berdiri']) ?></span>
                    </div>
                    
                    <h2 class="card-judul"><?= htmlspecialchars($item['judul']) ?></h2>
                    
                    <?php if (!empty($item['gambar'])): ?>
                    <div class="card-gambar">
                        <!-- Gambar menggunakan BASE_URL -->
                        <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($item['gambar']) ?>" 
                             alt="<?= htmlspecialchars($item['judul']) ?>"
                             onerror="this.src='<?= BASE_URL ?>/assets/img/placeholder.jpg'">
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-isi">
                        <?= nl2br(htmlspecialchars($item['isi_sejarah'])) ?>
                    </div>
                    
                    <div class="card-footer">
                        <div class="footer-info">
                            <i class="far fa-clock"></i>
                            <span>Terakhir diperbarui: <?= date('d F Y', strtotime($item['updated_at'])) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- CTA Section dengan BASE_URL -->
<div class="sejarah-cta">
    <div class="container">
        <h2>Bergabunglah dengan Kami</h2>
        <p>Jadilah bagian dari perjalanan sejarah MI Muhammadiyah Bodaskarangjati</p>
        <a href="<?= BASE_URL ?>/ppdb.php" class="btn-cta">
            <i class="fas fa-user-plus"></i> Daftar PPDB
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>