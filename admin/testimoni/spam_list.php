<?php
include "../includes/auth.php";

// Hapus semua spam
if (isset($_GET['delete_all'])) {
    mysqli_query($conn, "DELETE FROM testimoni WHERE is_spam = 1");
    $_SESSION['success_spam'] = "🚮 Semua data spam berhasil dihapus!";
    header("Location: spam_list.php");
    exit;
}

// Hapus satu spam
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $query = mysqli_query($conn, "SELECT nama FROM testimoni WHERE id = $id AND is_spam = 1");
    $data = mysqli_fetch_assoc($query);
    
    if (mysqli_query($conn, "DELETE FROM testimoni WHERE id = $id AND is_spam = 1")) {
        $_SESSION['success_spam'] = "🗑️ Spam dari <strong>" . htmlspecialchars($data['nama'] ?? '') . "</strong> berhasil dihapus!";
    } else {
        $_SESSION['error_spam'] = "❌ Gagal menghapus data spam!";
    }
    header("Location: spam_list.php");
    exit;
}

// Ambil data spam
$query = mysqli_query($conn, "SELECT * FROM testimoni WHERE is_spam = 1 ORDER BY created_at DESC");
$spams = [];
while ($row = mysqli_fetch_assoc($query)) {
    $spams[] = $row;
}

$total_spam = count($spams);
$unique_emails = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT email) as total FROM testimoni WHERE is_spam = 1 AND email IS NOT NULL AND email != ''"))['total'] ?? 0;

include "../includes/header.php";
?>

<div class="content-wrapper">
    <div class="spam-list-header">
        <h1>
            <i class="fas fa-trash-alt"></i> 
            Manajemen Spam Testimoni
        </h1>
        <div class="spam-list-actions">
            <a href="index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <?php if($total_spam > 0): ?>
                <a href="?delete_all=1" class="btn-delete-all-spam" onclick="return confirm('⚠️ PERINGATAN! Anda akan menghapus SEMUA data spam (' + <?= $total_spam ?> + ' data). Lanjutkan?')">
                    <i class="fas fa-trash-alt"></i> Hapus Semua Spam
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- STATISTIK SPAM -->
    <div class="spam-stats-grid">
        <div class="spam-stat-card total-spam">
            <i class="fas fa-trash-alt"></i>
            <div class="stat-info">
                <h3>Total Spam</h3>
                <p class="stat-number"><?= $total_spam ?></p>
            </div>
        </div>
        <div class="spam-stat-card unique-email">
            <i class="fas fa-envelope"></i>
            <div class="stat-info">
                <h3>Email Unik Spammer</h3>
                <p class="stat-number"><?= $unique_emails ?></p>
            </div>
        </div>
    </div>

    <!-- NOTIFICATION -->
    <?php if (isset($_SESSION['success_spam'])): ?>
        <div class="alert-success-spam">
            <i class="fas fa-check-circle"></i>
            <span><?= $_SESSION['success_spam']; unset($_SESSION['success_spam']); ?></span>
            <button class="close-spam" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_spam'])): ?>
        <div class="alert-spam">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?= $_SESSION['error_spam']; unset($_SESSION['error_spam']); ?></span>
            <button class="close-spam" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
    <?php endif; ?>

    <!-- FILTER & SEARCH -->
    <div class="spam-filter-bar">
        <div class="spam-search">
            <input type="text" id="searchSpam" placeholder="🔍 Cari nama atau email..." onkeyup="filterSpam()">
        </div>
        <div class="spam-filter">
            <select id="filterRating" onchange="filterSpam()">
                <option value="">Semua Rating</option>
                <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                <option value="4">⭐⭐⭐⭐ (4)</option>
                <option value="3">⭐⭐⭐ (3)</option>
                <option value="2">⭐⭐ (2)</option>
                <option value="1">⭐ (1)</option>
            </select>
        </div>
    </div>

    <?php if(count($spams) > 0): ?>
        <div class="spam-table-container">
            <table class="spam-table" id="spamTable">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Nama</th>
                        <th width="25%">Email</th>
                        <th width="8%">Rating</th>
                        <th width="32%">Ulasan</th>
                        <th width="10%">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach($spams as $s): ?>
                    <tr class="spam-item" data-rating="<?= $s['rating'] ?>" data-name="<?= strtolower(htmlspecialchars($s['nama'])) ?>" data-email="<?= strtolower(htmlspecialchars($s['email'])) ?>">
                        <td data-label="No"><?= $no++ ?></td>
                        <td data-label="Nama">
                            <strong><?= htmlspecialchars($s['nama']) ?></strong>
                        </td>
                        <td data-label="Email">
                            <small><?= htmlspecialchars($s['email'] ?? '-') ?></small>
                        </td>
                        <td data-label="Rating">
                            <div class="spam-rating">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <?php if($i <= $s['rating']): ?>
                                        <span class="star active">⭐</span>
                                    <?php else: ?>
                                        <span class="star inactive">☆</span>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        </td>
                        <td data-label="Ulasan">
                            <?= htmlspecialchars(substr($s['ulasan'], 0, 80)) ?>...
                            <?php if(strlen($s['ulasan']) > 80): ?>
                                <a href="#" class="spam-detail-toggle" onclick="toggleDetail(this, event)">[selengkapnya]</a>
                                <div class="spam-detail-content">
                                    <?= nl2br(htmlspecialchars($s['ulasan'])) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td data-label="Tanggal">
                            <small><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></small>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-spam-state">
            <i class="fas fa-check-circle"></i>
            <h3>Belum Ada Spam!</h3>
            <p>Sistem anti-spam bekerja dengan baik. Tidak ada testimoni spam yang terdeteksi.</p>
            <a href="index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Testimoni
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function filterSpam() {
    const searchTerm = document.getElementById('searchSpam').value.toLowerCase();
    const ratingFilter = document.getElementById('filterRating').value;
    const rows = document.querySelectorAll('#spamTable tbody tr');
    
    rows.forEach(row => {
        const name = row.getAttribute('data-name') || '';
        const email = row.getAttribute('data-email') || '';
        const rating = row.getAttribute('data-rating') || '';
        
        let show = true;
        
        if (searchTerm && !name.includes(searchTerm) && !email.includes(searchTerm)) {
            show = false;
        }
        
        if (ratingFilter && rating !== ratingFilter) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

function toggleDetail(element, event) {
    event.preventDefault();
    const detailDiv = element.nextElementSibling;
    detailDiv.classList.toggle('show');
    element.textContent = detailDiv.classList.contains('show') ? '[sembunyikan]' : '[selengkapnya]';
}

setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-spam, .alert-success-spam');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert) alert.style.display = 'none';
            }, 500);
        }, 3000);
    });
}, 100);
</script>

<?php include "../includes/footer.php"; ?>