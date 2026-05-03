<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM testimoni WHERE id = $id");
$t = mysqli_fetch_assoc($query);

if (!$t) {
    header("Location: index.php");
    exit;
}

include "../includes/header.php";
?>

<div class="content-wrapper">
    <div class="content-header">
        <h1><i class="fas fa-star"></i> Detail Testimoni</h1>
        <a href="index.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <i class="fas fa-user-circle"></i>
            <h2><?= htmlspecialchars($t['nama']) ?></h2>
            <?php if($t['is_spam'] == 1): ?>
                <span class="badge-spam" style="margin-left: 10px;">
                    <i class="fas fa-trash-alt"></i> TERDETEKSI SPAM
                </span>
            <?php endif; ?>
        </div>
        <div class="detail-body">
            <div class="detail-info-grid">
                <div class="detail-info-card">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <span class="info-label">Email</span>
                        <span class="info-value"><?= htmlspecialchars($t['email'] ?? '-') ?></span>
                    </div>
                </div>
                <div class="detail-info-card">
                    <i class="fas fa-calendar"></i>
                    <div>
                        <span class="info-label">Tanggal Dikirim</span>
                        <span class="info-value"><?= date('d F Y H:i', strtotime($t['created_at'])) ?></span>
                    </div>
                </div>
                <div class="detail-info-card">
                    <i class="fas fa-flag"></i>
                    <div>
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <?php if($t['is_spam'] == 1): ?>
                                <span class="badge-spam">🚫 SPAM</span>
                            <?php elseif($t['status'] == 'approved'): ?>
                                <span class="badge badge-success">Disetujui</span>
                            <?php elseif($t['status'] == 'pending'): ?>
                                <span class="badge badge-warning">Pending</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Ditolak</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="detail-rating">
                <label>Rating:</label>
                <div class="rating-display">
                    <?php for($i=1; $i<=5; $i++): ?>
                        <?php if($i <= $t['rating']): ?>
                            <i class="fas fa-star" style="color: #FFD700; font-size: 1.5rem;"></i>
                        <?php else: ?>
                            <i class="far fa-star" style="color: #ccc; font-size: 1.5rem;"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="detail-ulasan">
                <label><i class="fas fa-comment"></i> Ulasan / Kritik & Saran:</label>
                <div class="ulasan-content">
                    <?= nl2br(htmlspecialchars($t['ulasan'])) ?>
                </div>
            </div>
        </div>
        <div class="detail-footer">
            <?php if($t['is_spam'] == 0 && $t['status'] == 'pending'): ?>
                <a href="?approve=<?= $t['id'] ?>" class="btn-success" onclick="return confirm('Setujui testimoni ini?')">
                    <i class="fas fa-check"></i> Setujui
                </a>
                <a href="?reject=<?= $t['id'] ?>" class="btn-danger" onclick="return confirm('Tolak testimoni ini?')">
                    <i class="fas fa-times"></i> Tolak
                </a>
            <?php endif; ?>
            <a href="?delete=<?= $t['id'] ?>" class="btn-danger" onclick="return confirm('Hapus testimoni ini?')">
                <i class="fas fa-trash"></i> Hapus
            </a>
            <?php if($t['is_spam'] == 0): ?>
                <a href="mark_spam.php?id=<?= $t['id'] ?>" class="btn-warning" onclick="return confirm('Tandai sebagai SPAM?')">
                    <i class="fas fa-ban"></i> Tandai Spam
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>