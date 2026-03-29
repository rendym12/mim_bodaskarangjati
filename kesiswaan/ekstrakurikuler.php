<?php
// Include config untuk BASE_URL - Diperbaiki jalurnya menggunakan ../
require_once '../includes/config.php';
require_once '../includes/db.php'; // Pastikan file koneksi database Anda ter-include
include '../includes/header.php';

// Ambil data ekstrakurikuler
$query = mysqli_query($conn, "SELECT * FROM ekstrakurikuler ORDER BY urutan ASC, id ASC");
?>

<div class="page-header">
    <div class="container">
        <h1>Ekstrakurikuler</h1>
        <p>Kegiatan pengembangan bakat dan minat siswa di MI Muhammadiyah Bodaskarangjati</p>
    </div>
</div>

<div class="container">
    <?php if ($query && mysqli_num_rows($query) > 0): ?>
        <div class="eks-grid">
            <?php while ($row = mysqli_fetch_assoc($query)): ?>
            <div class="eks-card">
                <div class="eks-icon">
                    <i class="fas <?= htmlspecialchars($row['ikon'] ?? 'fa-star') ?>"></i>
                </div>
                
                <h3 class="eks-nama"><?= htmlspecialchars($row['nama_eks']) ?></h3>
                
                <?php if (!empty($row['pembina'])): ?>
                <div class="eks-pembina">
                    <i class="fas fa-user-tie"></i>
                    <span><?= htmlspecialchars($row['pembina']) ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($row['jadwal'])): ?>
                <div class="eks-jadwal">
                    <i class="fas fa-calendar-alt"></i>
                    <span><?= htmlspecialchars($row['jadwal']) ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($row['deskripsi'])): ?>
                <div class="eks-deskripsi">
                    <?= nl2br(htmlspecialchars($row['deskripsi'])) ?>
                </div>
                <?php endif; ?>
                
                <div class="eks-badge">#<?= $row['urutan'] ?? '1' ?></div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-futbol"></i>
            <p>Belum ada data ekstrakurikuler</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>