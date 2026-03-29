<?php
// Include config untuk BASE_URL
require_once '../includes/config.php';

// Include header
include '../includes/header.php';

$sql   = "SELECT * FROM visi_misi WHERE id = 1";
$query = mysqli_query($conn, $sql);
$data  = mysqli_fetch_assoc($query);
?>

<!-- PAGE HEADER - VISI & MISI -->
<div class="page-header">
    <div class="container">
        <h1>Visi & Misi</h1>
        <p>Landasan dan tujuan pendidikan MI Muhammadiyah Bodaskarangjati</p>
    </div>
</div>

<div class="container">
    <div class="visi-misi-wrapper">
        <!-- Visi Card -->
        <div class="visi-card">
            <div class="card-icon">
                <i class="fas fa-eye"></i>
            </div>
            <h2 class="card-title">Visi</h2>
            <div class="card-content">
                <?php if (!empty($data['visi'])): ?>
                    <?= nl2br(htmlspecialchars($data['visi'])) ?>
                <?php else: ?>
                    <p class="text-muted">Belum ada data visi</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Misi Card -->
        <div class="misi-card">
            <div class="card-icon">
                <i class="fas fa-list"></i>
            </div>
            <h2 class="card-title">Misi</h2>
            <div class="card-content">
                <?php if (!empty($data['misi'])): ?>
                    <?= nl2br(htmlspecialchars($data['misi'])) ?>
                <?php else: ?>
                    <p class="text-muted">Belum ada data misi</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>