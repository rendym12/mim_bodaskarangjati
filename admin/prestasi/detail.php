<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM prestasi WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data prestasi tidak ditemukan";
    header("Location: index.php");
    exit;
}

include "../includes/header.php";
?>

<div class="content-wrapper prestasi-page">
    <div class="content-header">
        <h1><i class="fas fa-trophy"></i> Detail Prestasi</h1>
        <div>
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="index.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                <!-- Gambar -->
                <?php if (!empty($row['gambar'])): ?>
                <div style="flex: 0 0 250px;">
                    <img src="../../uploads/prestasi/<?= $row['gambar'] ?>" 
                         alt="<?= htmlspecialchars($row['nama_prestasi']) ?>" 
                         style="width: 100%; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                </div>
                <?php endif; ?>
                
                <!-- Data Detail -->
                <div style="flex: 1;">
                    <h2 style="margin-bottom: 20px; color: var(--primary);"><?= htmlspecialchars($row['nama_prestasi']) ?></h2>
                    
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; width: 150px;">
                                <strong><i class="fas fa-chart-bar"></i> Tingkat</strong>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <?= htmlspecialchars($row['tingkat'] ?? '-') ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                <strong><i class="fas fa-building"></i> Penyelenggara</strong>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <?= htmlspecialchars($row['penyelenggara'] ?? '-') ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                <strong><i class="fas fa-calendar"></i> Tahun</strong>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <?= $row['tahun'] ?? '-' ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                <strong><i class="fas fa-sort-numeric-up"></i> Urutan</strong>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <?= $row['urutan'] ?? '0' ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Footer Actions -->
        <div class="card-footer" style="padding: 20px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px;">
            <a href="edit.php?id=<?= $id ?>" class="btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="#" onclick="confirmDelete(<?= $id ?>, '<?= htmlspecialchars($row['nama_prestasi']) ?>', 'prestasi', <?= (!empty($row['gambar']) ? 'true' : 'false') ?>)" class="btn-danger">
                <i class="fas fa-trash"></i> Hapus
            </a>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>