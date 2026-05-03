<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $query_data = mysqli_query($conn, "SELECT nama, email FROM testimoni WHERE id = $id");
    $data = mysqli_fetch_assoc($query_data);
    $nama = $data['nama'] ?? 'Testimoni';
    
    if (mysqli_query($conn, "DELETE FROM testimoni WHERE id = $id")) {
        $_SESSION['success'] = "✅ Testimoni dari <strong>\"" . htmlspecialchars($nama) . "\"</strong> berhasil dihapus!";
    } else {
        $_SESSION['error'] = "❌ Gagal menghapus testimoni!";
    }
    header("Location: index.php");
    exit;
}

// ========== PROSES SETUJUI ==========
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    
    $query_data = mysqli_query($conn, "SELECT nama FROM testimoni WHERE id = $id");
    $data = mysqli_fetch_assoc($query_data);
    $nama = $data['nama'] ?? 'Testimoni';
    
    if (mysqli_query($conn, "UPDATE testimoni SET status = 'approved' WHERE id = $id")) {
        $_SESSION['success'] = "✅ Testimoni dari <strong>\"" . htmlspecialchars($nama) . "\"</strong> berhasil disetujui!";
    } else {
        $_SESSION['error'] = "❌ Gagal menyetujui testimoni!";
    }
    header("Location: index.php");
    exit;
}

// ========== PROSES TOLAK ==========
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    
    $query_data = mysqli_query($conn, "SELECT nama FROM testimoni WHERE id = $id");
    $data = mysqli_fetch_assoc($query_data);
    $nama = $data['nama'] ?? 'Testimoni';
    
    if (mysqli_query($conn, "UPDATE testimoni SET status = 'rejected' WHERE id = $id")) {
        $_SESSION['success'] = "⚠️ Testimoni dari <strong>\"" . htmlspecialchars($nama) . "\"</strong> ditolak!";
    } else {
        $_SESSION['error'] = "❌ Gagal menolak testimoni!";
    }
    header("Location: index.php");
    exit;
}

// ========== TAMPILKAN DATA ==========
$query = mysqli_query($conn, "SELECT * FROM testimoni ORDER BY created_at DESC");
$testimonis = [];
while ($row = mysqli_fetch_assoc($query)) {
    $testimonis[] = $row;
}

// Statistik
$total_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimoni WHERE status = 'pending' AND is_spam = 0"))['total'] ?? 0;
$total_approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimoni WHERE status = 'approved' AND is_spam = 0"))['total'] ?? 0;
$total_rejected = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimoni WHERE status = 'rejected' AND is_spam = 0"))['total'] ?? 0;
$total_spam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimoni WHERE is_spam = 1"))['total'] ?? 0;
$total_all = $total_pending + $total_approved + $total_rejected + $total_spam;

include "../includes/header.php";
?>

<div class="content-wrapper">
    <div class="content-header">
        <h1><i class="fas fa-star"></i> Kelola Testimoni & Ulasan</h1>
        <a href="spam_list.php" class="btn-spam-manage">
            <i class="fas fa-trash-alt"></i> Kelola Spam
            <?php if($total_spam > 0): ?>
                <span class="badge-spam-count"><?= $total_spam ?></span>
            <?php endif; ?>
        </a>
    </div>

    <!-- STATISTIK -->
    <div class="info-stats">
        <span class="stat-badge total">
            <i class="fas fa-database"></i> Total: <?= $total_all ?>
        </span>
        <span class="stat-badge pending">
            <i class="fas fa-clock"></i> Pending: <?= $total_pending ?>
        </span>
        <span class="stat-badge approved">
            <i class="fas fa-check-circle"></i> Disetujui: <?= $total_approved ?>
        </span>
        <span class="stat-badge rejected">
            <i class="fas fa-times-circle"></i> Ditolak: <?= $total_rejected ?>
        </span>
        <span class="stat-badge spam">
            <i class="fas fa-trash-alt"></i> Spam: <?= $total_spam ?>
        </span>
    </div>

    <!-- NOTIFICATION -->
    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible">
                <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Daftar Testimoni</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Nama / Email</th>
                            <th width="8%">Rating</th>
                            <th width="35%">Ulasan</th>
                            <th width="10%">Tanggal</th>
                            <th width="10%">Status</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($testimonis) > 0): 
                            $no = 1;
                            foreach ($testimonis as $t): 
                                $is_spam = $t['is_spam'] == 1;
                                $row_class = $is_spam ? 'spam-row' : '';
                        ?>
                        <tr class="<?= $row_class ?>">
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($t['nama']) ?></strong><br>
                                <small><?= htmlspecialchars($t['email'] ?? '-') ?></small>
                            </td>
                            <td>
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <?php if($i <= $t['rating']): ?>
                                        <i class="fas fa-star" style="color: #FFD700;"></i>
                                    <?php else: ?>
                                        <i class="far fa-star" style="color: #ccc;"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </td>
                            <td><?= htmlspecialchars(substr($t['ulasan'], 0, 60)) ?>...</td>
                            <td><?= date('d/m/Y', strtotime($t['created_at'])) ?></td>
                            <td>
                                <?php if($is_spam): ?>
                                    <span class="badge-spam"><i class="fas fa-trash-alt"></i> SPAM</span>
                                <?php elseif($t['status'] == 'approved'): ?>
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Disetujui</span>
                                <?php elseif($t['status'] == 'pending'): ?>
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="detail.php?id=<?= $t['id'] ?>" class="btn-view" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if(!$is_spam && $t['status'] == 'pending'): ?>
                                        <a href="?approve=<?= $t['id'] ?>" class="btn-success" title="Setujui" onclick="return confirm('Setujui testimoni dari <?= htmlspecialchars($t['nama']) ?>?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <a href="?reject=<?= $t['id'] ?>" class="btn-danger" title="Tolak" onclick="return confirm('Tolak testimoni dari <?= htmlspecialchars($t['nama']) ?>?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if($is_spam): ?>
                                        <a href="?delete=<?= $t['id'] ?>" class="btn-delete" title="Hapus Spam" onclick="return confirm('Hapus data spam ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="mark_spam.php?id=<?= $t['id'] ?>" class="btn-warning" title="Tandai Spam" onclick="return confirm('Tandai sebagai SPAM?')">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="fas fa-star"></i>
                                <p>Belum ada testimoni</p>
                                <small>Testimoni akan muncul di sini setelah ada yang mengirim</small>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
setTimeout(function() {
    var alert = document.querySelector('.notification-container .alert');
    if (alert) {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(function() {
            if (alert) alert.style.display = 'none';
        }, 500);
    }
}, 3000);
</script>

<?php include "../includes/footer.php"; ?>