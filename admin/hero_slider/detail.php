<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM hero_slider WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data slider tidak ditemukan";
    header("Location: index.php");
    exit;
}

include "../includes/header.php";
?>

<div class="content-wrapper slider-page">
    <div class="content-header">
        <h1><i class="fas fa-image"></i> Detail Slide</h1>
        <div class="action-buttons">
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="index.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="detail-container">
        <!-- Preview Gambar Full -->
        <div class="detail-image-section">
            <h3><i class="fas fa-image"></i> Preview Gambar</h3>
            <div class="detail-image-frame">
                <?php if (!empty($row['gambar'])): ?>
                    <img src="../../uploads/hero/<?= $row['gambar'] ?>" alt="<?= $row['judul'] ?>" class="detail-image">
                <?php else: ?>
                    <div class="no-image">
                        <i class="fas fa-image"></i>
                        <p>Tidak ada gambar</p>
                    </div>
                <?php endif; ?>
            </div>
            <p class="image-filename"><i class="fas fa-file-image"></i> <?= $row['gambar'] ?? '-' ?></p>
        </div>

        <!-- Detail Informasi -->
        <div class="detail-info-section">
            <h3><i class="fas fa-info-circle"></i> Informasi Slide</h3>
            
            <table class="detail-table">
                <tr>
                    <th width="150">ID Slide</th>
                    <td>: <strong>#<?= $row['id'] ?></strong></td>
                </tr>
                <tr>
                    <th><i class="fas fa-tag"></i> Badge</th>
                    <td>: <?= !empty($row['badge']) ? '<span class="badge-preview">' . htmlspecialchars($row['badge']) . '</span>' : '-' ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-heading"></i> Judul</th>
                    <td>: <strong><?= htmlspecialchars($row['judul']) ?></strong></td>
                </tr>
                <tr>
                    <th><i class="fas fa-align-left"></i> Sub Judul</th>
                    <td>: <?= !empty($row['subjudul']) ? nl2br(htmlspecialchars($row['subjudul'])) : '-' ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-font"></i> Teks Tombol</th>
                    <td>: <?= !empty($row['tombol_text']) ? htmlspecialchars($row['tombol_text']) : '-' ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-link"></i> Link Tombol</th>
                    <td>: <?php if (!empty($row['tombol_link'])): ?>
                        <a href="<?= htmlspecialchars($row['tombol_link']) ?>" target="_blank" class="link-preview">
                            <?= htmlspecialchars($row['tombol_link']) ?> <i class="fas fa-external-link-alt"></i>
                        </a>
                    <?php else: ?>
                        -
                    <?php endif; ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-sort-numeric-up"></i> Urutan</th>
                    <td>: <?= $row['urutan'] ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-toggle-on"></i> Status</th>
                    <td>: 
                        <?php if ($row['status'] == 'aktif'): ?>
                            <span class="status-badge aktif">Aktif</span>
                        <?php else: ?>
                            <span class="status-badge nonaktif">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar"></i> Ditambahkan</th>
                    <td>: <?= date('d/m/Y H:i', strtotime($row['created_at'] ?? 'now')) ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-clock"></i> Terakhir Update</th>
                    <td>: <?= date('d/m/Y H:i', strtotime($row['updated_at'] ?? 'now')) ?></td>
                </tr>
            </table>
            
            <!-- Preview Tampilan di Halaman Depan -->
            <div class="preview-frontend">
                <h4><i class="fas fa-eye"></i> Preview Tampilan di Website</h4>
                <div class="frontend-simulator">
                    <div class="simulator-slide" style="background: linear-gradient(135deg, #0B3D91, #1e4ca0);">
                        <div class="simulator-content">
                            <?php if (!empty($row['badge'])): ?>
                                <span class="simulator-badge"><?= htmlspecialchars($row['badge']) ?></span>
                            <?php endif; ?>
                            <h2 class="simulator-title"><?= htmlspecialchars($row['judul']) ?></h2>
                            <?php if (!empty($row['subjudul'])): ?>
                                <p class="simulator-subtitle"><?= htmlspecialchars($row['subjudul']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($row['tombol_text']) && !empty($row['tombol_link'])): ?>
                                <a href="#" class="simulator-button"><?= htmlspecialchars($row['tombol_text']) ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <p class="preview-note"><i class="fas fa-info-circle"></i> Tampilan sebenarnya di halaman depan dengan background gambar yang diupload</p>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>