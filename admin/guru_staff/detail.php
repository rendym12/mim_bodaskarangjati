<?php
include "../includes/auth.php";

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM guru_staff WHERE id = $id");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    header("Location: index.php");
    exit;
}

include "../includes/header.php";
?>

<div class="content-wrapper guru-page">
    <!-- Content Header -->
    <div class="content-header">
        <h1><i class="fas fa-chalkboard-teacher"></i> Detail Guru/Staff</h1>
        <div>
            <a href="edit.php?id=<?= $id ?>" class="btn-primary" style="background: #4f46e5; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; margin-right: 5px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="index.php" class="btn-secondary" style="background: #6c757d; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Detail Card -->
    <div style="background: white; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #4f46e5, #818cf8); color: white; padding: 20px;">
            <h2 style="margin: 0;"><i class="fas fa-user-tie"></i> <?= htmlspecialchars($row['nama']) ?></h2>
        </div>
        
        <div style="display: flex; padding: 30px; gap: 30px; flex-wrap: wrap;">
            <!-- Foto -->
            <div style="flex: 0 0 200px; text-align: center;">
                <?php if (!empty($row['foto']) && $row['foto'] != 'default-avatar.jpg'): ?>
                    <img src="../../uploads/guru/<?= $row['foto'] ?>" alt="Foto" style="width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 5px solid #f0f0f0;">
                <?php else: ?>
                    <i class="fas fa-user-circle" style="font-size: 200px; color: #ccc;"></i>
                <?php endif; ?>
            </div>
            
            <!-- Informasi Detail -->
            <div style="flex: 1;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #eee; width: 150px;">
                            <strong><i class="fas fa-id-card"></i> NIP</strong>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <?= !empty($row['nip']) ? htmlspecialchars($row['nip']) : '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <strong><i class="fas fa-briefcase"></i> Jabatan</strong>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <?= !empty($row['jabatan']) ? htmlspecialchars($row['jabatan']) : '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <strong><i class="fas fa-book"></i> Mata Pelajaran</strong>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <?= !empty($row['mapel']) ? htmlspecialchars($row['mapel']) : '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <strong><i class="fas fa-sort-numeric-up"></i> Urutan Tampil</strong>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <?= $row['urutan'] ?? '0' ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>