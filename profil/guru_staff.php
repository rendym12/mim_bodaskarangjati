<?php
// Include config untuk BASE_URL
require_once '../includes/config.php';
include '../includes/header.php';

$sql = "SELECT * FROM guru_staff ORDER BY urutan ASC, nama ASC";
$query = mysqli_query($conn, $sql);
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="container">
        <h1>Guru & Tenaga Kependidikan</h1>
        <p>Tenaga pendidik dan kependidikan yang profesional dan berdedikasi di MI Muhammadiyah Bodaskarangjati</p>
    </div>
</div>

<div class="container">
    <div class="guru-grid">
        <?php if (mysqli_num_rows($query) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($query)): ?>
            <div class="guru-card">
                <div class="guru-card-inner">
                    <!-- Foto -->
                    <div class="guru-foto">
                        <?php if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg'): ?>
                            <img src="<?= BASE_URL ?>/uploads/guru/<?= $row['foto'] ?>" 
                                 alt="<?= htmlspecialchars($row['nama']) ?>"
                                 onerror="this.src='<?= BASE_URL ?>/assets/img/default-avatar.jpg'">
                        <?php else: ?>
                            <img src="<?= BASE_URL ?>/assets/img/default-avatar.jpg" alt="Default Avatar">
                        <?php endif; ?>
                    </div>
                    
                    <!-- Info Guru -->
                    <div class="guru-info">
                        <h3 class="guru-nama"><?= htmlspecialchars($row['nama']) ?></h3>
                        
                        <?php if (!empty($row['nip'])): ?>
                        <div class="guru-nip">
                            <i class="fas fa-id-card"></i> NIP. <?= htmlspecialchars($row['nip']) ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="guru-detail">
                            <?php if (!empty($row['jabatan'])): ?>
                            <div class="guru-jabatan">
                                <i class="fas fa-briefcase"></i> <?= htmlspecialchars($row['jabatan']) ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($row['mapel'])): ?>
                            <div class="guru-mapel">
                                <i class="fas fa-book-open"></i> <?= htmlspecialchars($row['mapel']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>Belum ada data guru & staff</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>