<?php
require_once dirname(__DIR__) . '/includes/auth.php';

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM pengumuman WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    header("Location: index.php");
    exit;
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="content-wrapper pengumuman-page">
    <div class="content-header">
        <h1><i class="fas fa-bullhorn"></i> Detail Pengumuman</h1>
        <div>
            <a href="edit.php?id=<?= $id ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <i class="fas fa-bullhorn"></i>
            <h2><?= htmlspecialchars($row['judul']) ?></h2>
        </div>
        
        <div class="detail-body">
            <div class="detail-meta">
                <div class="detail-meta-item">
                    <i class="fas fa-calendar"></i>
                    <strong>Tanggal:</strong> <?= date('d F Y', strtotime($row['tanggal'])) ?>
                </div>
                <div class="detail-meta-item">
                    <i class="fas fa-user"></i>
                    <strong>Penulis:</strong> <?= htmlspecialchars($row['penulis']) ?>
                </div>
                <div class="detail-meta-item">
                    <i class="fas fa-eye"></i>
                    <strong>Status:</strong> 
                    <?php if ($row['status'] == 'publish'): ?>
                        <span class="badge badge-success">Publish</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Draft</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($row['gambar'])): ?>
            <div class="detail-gambar">
                <img src="../../uploads/pengumuman/<?= $row['gambar'] ?>" alt="Gambar Pengumuman">
            </div>
            <?php endif; ?>
            
            <div class="detail-isi">
                <?= nl2br(htmlspecialchars($row['isi'])) ?>
            </div>
            
            <?php if (!empty($row['file_lampiran'])): ?>
            <div class="detail-lampiran">
                <h4><i class="fas fa-paperclip"></i> File Lampiran</h4>
                <a href="../../uploads/lampiran/<?= $row['file_lampiran'] ?>" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Download <?= $row['file_lampiran'] ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="detail-footer">
            <a href="edit.php?id=<?= $id ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="?delete=<?= $id ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus pengumuman ini?')">
                <i class="fas fa-trash"></i> Hapus
            </a>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>