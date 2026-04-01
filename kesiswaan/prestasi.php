<?php
// Include config untuk BASE_URL
require_once '../includes/config.php';
include '../includes/header.php';

// Ambil data prestasi - urutkan berdasarkan tahun terbaru dan juara (1,2,3 lebih atas)
$query = mysqli_query($conn, "SELECT * FROM prestasi ORDER BY tahun DESC, juara ASC, id DESC");
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="container">
        <h1>Prestasi</h1>
        <p>Berbagai prestasi yang telah diraih oleh siswa/i MI Muhammadiyah Bodaskarangjati</p>
    </div>
</div>

<div class="container">
    <?php if (mysqli_num_rows($query) > 0): ?>
        
        <!-- STATISTIK PRESTASI -->
        <?php
        $total = mysqli_num_rows($query);
        // Reset query untuk statistik
        $stat_query = mysqli_query($conn, "SELECT * FROM prestasi");
        $total_prestasi = mysqli_num_rows($stat_query);
        
        $tahun_terbanyak = mysqli_query($conn, "SELECT tahun, COUNT(*) as jumlah FROM prestasi GROUP BY tahun ORDER BY jumlah DESC LIMIT 1");
        $tahun_data = mysqli_fetch_assoc($tahun_terbanyak);
        
        // Hitung jumlah juara 1,2,3
        $juara1 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM prestasi WHERE juara = 1"))['total'];
        $juara2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM prestasi WHERE juara = 2"))['total'];
        $juara3 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM prestasi WHERE juara = 3"))['total'];
        ?>
        
        <div class="prestasi-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-number"><?= $total_prestasi ?></span>
                    <span class="stat-label">Total Prestasi</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-number">🥇 <?= $juara1 ?> 🥈 <?= $juara2 ?> 🥉 <?= $juara3 ?></span>
                    <span class="stat-label">Juara 1, 2, 3</span>
                </div>
            </div>
            
            <?php if ($tahun_data): ?>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-star"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-number"><?= $tahun_data['tahun'] ?></span>
                    <span class="stat-label">Tahun Terbanyak<br>(<?= $tahun_data['jumlah'] ?> prestasi)</span>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- FILTER TAHUN -->
        <div class="prestasi-filter">
            <button class="filter-btn active" data-filter="all">Semua</button>
            <?php
            $tahun_query = mysqli_query($conn, "SELECT DISTINCT tahun FROM prestasi ORDER BY tahun DESC");
            while ($tahun = mysqli_fetch_assoc($tahun_query)):
            ?>
            <button class="filter-btn" data-filter="<?= $tahun['tahun'] ?>"><?= $tahun['tahun'] ?></button>
            <?php endwhile; ?>
        </div>
        
        <!-- GRID PRESTASI -->
        <div class="prestasi-grid">
            <?php 
            // Reset query untuk menampilkan data
            $query = mysqli_query($conn, "SELECT * FROM prestasi ORDER BY tahun DESC, juara ASC, id DESC");
            while ($row = mysqli_fetch_assoc($query)): 
                // Tentukan kelas badge berdasarkan juara
                $badge_class = '';
                $medali = '';
                if ($row['juara'] == 1) {
                    $badge_class = 'badge-gold';
                    $medali = '🥇';
                    $juara_text = 'Juara 1';
                } elseif ($row['juara'] == 2) {
                    $badge_class = 'badge-silver';
                    $medali = '🥈';
                    $juara_text = 'Juara 2';
                } elseif ($row['juara'] == 3) {
                    $badge_class = 'badge-bronze';
                    $medali = '🥉';
                    $juara_text = 'Juara 3';
                } elseif ($row['juara'] > 0) {
                    $badge_class = 'badge-juara';
                    $medali = '🏆';
                    $juara_text = 'Juara ' . $row['juara'];
                } else {
                    $badge_class = 'badge-peserta';
                    $medali = '📋';
                    $juara_text = 'Peserta';
                }
            ?>
            <div class="prestasi-card" data-tahun="<?= $row['tahun'] ?>">
                <?php if (!empty($row['gambar'])): ?>
                <div class="prestasi-gambar">
                    <img src="<?= BASE_URL ?>/uploads/prestasi/<?= $row['gambar'] ?>" 
                         alt="<?= htmlspecialchars($row['nama_prestasi']) ?>"
                         onerror="this.src='<?= BASE_URL ?>/assets/img/prestasi-default.jpg'">
                    <div class="prestasi-overlay">
                        <span class="prestasi-tahun"><?= $row['tahun'] ?></span>
                        <span class="prestasi-juara <?= $badge_class ?>"><?= $medali ?> <?= $juara_text ?></span>
                    </div>
                </div>
                <?php else: ?>
                <div class="prestasi-gambar prestasi-gambar-default">
                    <i class="fas fa-trophy"></i>
                    <div class="prestasi-overlay">
                        <span class="prestasi-tahun"><?= $row['tahun'] ?></span>
                        <span class="prestasi-juara <?= $badge_class ?>"><?= $medali ?> <?= $juara_text ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="prestasi-content">
                    <h3 class="prestasi-nama"><?= htmlspecialchars($row['nama_prestasi']) ?></h3>
                    
                    <?php if (!empty($row['tingkat'])): ?>
                    <div class="prestasi-tingkat">
                        <i class="fas fa-chart-line"></i>
                        <span><?= htmlspecialchars($row['tingkat']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($row['penyelenggara'])): ?>
                    <div class="prestasi-penyelenggara">
                        <i class="fas fa-building"></i>
                        <span><?= htmlspecialchars($row['penyelenggara']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-trophy"></i>
            <p>Belum ada data prestasi</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>