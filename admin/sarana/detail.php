<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM sarana WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    $_SESSION['error'] = "Data sarana tidak ditemukan";
    header("Location: index.php");
    exit;
}

include "../includes/header.php";
?>

<div class="content-wrapper sarana-page">
    <div class="content-header">
        <h1><i class="fas fa-building"></i> Detail Sarana</h1>
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
                <div style="flex: 0 0 250px;">
                    <?php if (!empty($row['gambar'])): ?>
                        <img src="../../uploads/sarana/<?= $row['gambar'] ?>" 
                             alt="<?= htmlspecialchars($row['nama_sarana']) ?>" 
                             style="width: 100%; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <?php else: ?>
                        <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #f0f4ff, #e6f0ff); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas <?= $row['ikon'] ?? 'fa-building' ?>" style="font-size: 5rem; color: #0B3D91; opacity: 0.3;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Data Detail -->
                <div style="flex: 1;">
                    <h2 style="margin-bottom: 20px; color: var(--primary);"><?= htmlspecialchars($row['nama_sarana']) ?></h2>
                    
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; width: 150px;">
                                <strong><i class="fas fa-icons"></i> Ikon</strong>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <i class="fas <?= $row['ikon'] ?? 'fa-building' ?>"></i> <?= $row['ikon'] ?? 'fa-building' ?>
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
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                <strong><i class="fas fa-align-left"></i> Keterangan</strong>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                : <?= nl2br(htmlspecialchars($row['keterangan'] ?? '-')) ?>
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
            <a href="#" onclick="confirmDelete(<?= $id ?>, '<?= htmlspecialchars($row['nama_sarana']) ?>', 'sarana', <?= (!empty($row['gambar']) ? 'true' : 'false') ?>)" class="btn-danger">
                <i class="fas fa-trash"></i> Hapus
            </a>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>