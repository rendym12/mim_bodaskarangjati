<?php
// Include config untuk BASE_URL
require_once '../includes/config.php';
include '../includes/header.php';

// Ambil data guru & staff dari database
$query = mysqli_query($conn, "SELECT * FROM guru_staff ORDER BY urutan ASC, id DESC");

// Cek apakah ada gambar struktur organisasi
$struktur_exists = false;
$struktur_file = '';
$struktur_files = glob('../uploads/struktur/*.*');
if (!empty($struktur_files)) {
    $struktur_exists = true;
    $struktur_file = basename($struktur_files[0]);
}
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="container">
        <h1>Guru & Staff</h1>
        <p>Tenaga pendidik dan kependidikan MI Muhammadiyah Bodaskarangjati</p>
    </div>
</div>

<div class="container">
    
    <!-- ============================================= -->
    <!-- STRUKTUR ORGANISASI - GAMBAR DI ATAS -->
    <!-- ============================================= -->
    <div class="struktur-section">
        <div class="section-title">
            <h2><i class="fas fa-sitemap"></i> Struktur Organisasi</h2>
            <p>Bagan struktur organisasi MI Muhammadiyah Bodaskarangjati</p>
        </div>
        
        <div class="struktur-image">
            <?php if ($struktur_exists): ?>
                <img src="<?= BASE_URL ?>/uploads/struktur/<?= $struktur_file ?>" 
                     alt="Struktur Organisasi MI Muhammadiyah Bodaskarangjati"
                     class="img-struktur"
                     loading="lazy">
            <?php else: ?>
                <div class="struktur-placeholder">
                    <i class="fas fa-sitemap"></i>
                    <p>Struktur Organisasi sedang disiapkan</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="struktur-caption">
            <p><i class="fas fa-info-circle"></i> Struktur Organisasi MI Muhammadiyah Bodaskarangjati Tahun Pelajaran <?= date('Y') . '/' . (date('Y')+1) ?></p>
        </div>
    </div>
    
    <!-- ============================================= -->
    <!-- DAFTAR GURU & STAFF - DI BAWAH STRUKTUR -->
    <!-- ============================================= -->
    <div class="guru-section">
        <div class="section-title">
            <h2><i class="fas fa-chalkboard-teacher"></i> Guru & Staff</h2>
            <p>Tenaga pendidik dan kependidikan yang berdedikasi</p>
        </div>
        
        <?php if (mysqli_num_rows($query) > 0): ?>
            <div class="guru-grid">
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                <div class="guru-card">
                    <div class="guru-foto">
                        <?php if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg'): ?>
                            <img src="<?= BASE_URL ?>/uploads/guru/<?= $row['foto'] ?>" 
                                 alt="<?= htmlspecialchars($row['nama']) ?>"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="guru-foto-placeholder">
                                <i class="fas fa-user-circle"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="guru-info">
                        <h3 class="guru-nama"><?= htmlspecialchars($row['nama']) ?></h3>
                        <?php if (!empty($row['nip'])): ?>
                        <p class="guru-nip">NIP. <?= htmlspecialchars($row['nip']) ?></p>
                        <?php endif; ?>
                        <p class="guru-jabatan"><?= htmlspecialchars($row['jabatan'] ?? 'Guru') ?></p>
                        <?php if (!empty($row['mapel'])): ?>
                        <p class="guru-mapel">
                            <i class="fas fa-book-open"></i> <?= htmlspecialchars($row['mapel']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>Belum ada data guru & staff</p>
            </div>
        <?php endif; ?>
    </div>
    
</div>

<?php include '../includes/footer.php'; ?>