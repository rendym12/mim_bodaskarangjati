<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Ambil data testimoni sebelum dihapus untuk notifikasi
    $query_data = mysqli_query($conn, "SELECT nama FROM testimoni WHERE id = $id");
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
    
    // Ambil data testimoni sebelum diupdate
    $query_data = mysqli_query($conn, "SELECT nama FROM testimoni WHERE id = $id");
    $data = mysqli_fetch_assoc($query_data);
    $nama = $data['nama'] ?? 'Testimoni';
    
    if (mysqli_query($conn, "UPDATE testimoni SET status = 'approved' WHERE id = $id")) {
        $_SESSION['success'] = "✅ Testimoni dari <strong>\"" . htmlspecialchars($nama) . "\"</strong> berhasil disetujui dan akan tampil di halaman publik!";
    } else {
        $_SESSION['error'] = "❌ Gagal menyetujui testimoni!";
    }
    header("Location: index.php");
    exit;
}

// ========== PROSES TOLAK ==========
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    
    // Ambil data testimoni sebelum diupdate
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

// ========== AMBIL DATA TESTIMONI ==========
$query = mysqli_query($conn, "SELECT * FROM testimoni ORDER BY created_at DESC");
$testimonis = [];
while ($row = mysqli_fetch_assoc($query)) {
    $testimonis[] = $row;
}

// Hitung statistik untuk ditampilkan
$total_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimoni WHERE status = 'pending'"))['total'] ?? 0;
$total_approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimoni WHERE status = 'approved'"))['total'] ?? 0;
$total_rejected = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimoni WHERE status = 'rejected'"))['total'] ?? 0;
$total_all = $total_pending + $total_approved + $total_rejected;

include "../includes/header.php";
?>

<style>
/* Tambahan style untuk notifikasi dan statistik */
.stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 600;
}

.stat-badge i {
    font-size: 0.9rem;
}

.stat-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.stat-badge.approved {
    background: #d4edda;
    color: #155724;
}

.stat-badge.rejected {
    background: #f8d7da;
    color: #721c24;
}

.stat-badge.total {
    background: #e6f0ff;
    color: #0B3D91;
}

.info-stats {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.alert-success-custom {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    border-left: 5px solid #28a745;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideInRight 0.3s ease;
}

.alert-error-custom {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    border-left: 5px solid #dc3545;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.alert-success-custom i,
.alert-error-custom i {
    font-size: 1.3rem;
}

.alert-success-custom strong,
.alert-error-custom strong {
    font-weight: 600;
}

.close-notif {
    margin-left: auto;
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.3s;
}

.close-notif:hover {
    opacity: 1;
}
</style>

<div class="content-wrapper">
    <div class="content-header">
        <h1><i class="fas fa-star"></i> Kelola Testimoni & Ulasan</h1>
    </div>

    <!-- STATISTIK TESTIMONI -->
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
    </div>

    <!-- NOTIFICATION CONTAINER -->
    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert-success-custom" id="notificationAlert">
                <i class="fas fa-check-circle"></i>
                <div><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <button class="close-notif" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert-error-custom" id="notificationAlert">
                <i class="fas fa-exclamation-triangle"></i>
                <div><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <button class="close-notif" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Daftar Testimoni</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Nama / Email</th>
                            <th width="10%">Rating</th>
                            <th width="35%">Ulasan</th>
                            <th width="12%">Tanggal</th>
                            <th width="10%">Status</th>
                            <th width="13%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($testimonis) > 0): 
                            $no = 1;
                            foreach ($testimonis as $t): 
                        ?>
                        <tr>
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
                            <td>
                                <?= htmlspecialchars(substr($t['ulasan'], 0, 80)) ?>...
                            </td>
                            <td><?= date('d/m/Y', strtotime($t['created_at'])) ?></td>
                            <td>
                                <?php if($t['status'] == 'approved'): ?>
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
                                    <?php if($t['status'] == 'pending'): ?>
                                        <a href="?approve=<?= $t['id'] ?>" class="btn-success" title="Setujui" onclick="return confirm('Setujui testimoni dari <?= htmlspecialchars($t['nama']) ?>?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <a href="?reject=<?= $t['id'] ?>" class="btn-danger" title="Tolak" onclick="return confirm('Tolak testimoni dari <?= htmlspecialchars($t['nama']) ?>?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="?delete=<?= $t['id'] ?>" class="btn-delete" title="Hapus" onclick="return confirm('Hapus testimoni dari <?= htmlspecialchars($t['nama']) ?>?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="fas fa-star"></i>
                                <p>Belum ada testimoni</p>
                                <small>Testimoni akan muncul di sini setelah ada yang mengirim dari halaman kontak</small>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script auto close notifikasi setelah 3 detik -->
<script>
// Auto close notification after 3 seconds
setTimeout(function() {
    var alert = document.getElementById('notificationAlert');
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