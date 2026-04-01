<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Ambil data untuk notifikasi
    $q = mysqli_query($conn, "SELECT nama_agenda FROM agenda WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    // Cek apakah data ada
    $check = mysqli_query($conn, "SELECT * FROM agenda WHERE id = $id");
    
    if (mysqli_num_rows($check) > 0) {
        $nama = $data['nama_agenda'];
        
        if (mysqli_query($conn, "DELETE FROM agenda WHERE id = $id")) {
            // Buat pesan notifikasi
            $_SESSION['success'] = [
                'message' => "Agenda <strong>\"$nama\"</strong> berhasil dihapus",
                'type' => 'agenda'
            ];
        } else {
            $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Data agenda tidak ditemukan";
    }
    
    header("Location: index.php");
    exit;
}

// ========== AMBIL DATA ==========
$query = mysqli_query($conn, "SELECT * FROM agenda ORDER BY tanggal_mulai DESC");

include "../includes/header.php";
?>

<div class="content-wrapper agenda-page">
    <!-- Content Header -->
    <div class="content-header">
        <h1><i class="fas fa-calendar-alt"></i> Kelola Agenda</h1>
        <a href="tambah.php" class="btn-primary">
            <i class="fas fa-plus"></i> Tambah Agenda
        </a>
    </div>

    <!-- NOTIFICATION CONTAINER -->
    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <?php if (is_array($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <i class="fas fa-check-circle"></i>
                    <?= $_SESSION['success']['message'] ?>
                    <button type="button" class="close">&times;</button>
                </div>
            <?php else: ?>
                <div class="alert alert-success alert-dismissible">
                    <i class="fas fa-check-circle"></i>
                    <?= $_SESSION['success'] ?>
                    <button type="button" class="close">&times;</button>
                </div>
            <?php endif; ?>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible">
                <i class="fas fa-exclamation-circle"></i>
                <?= $_SESSION['error'] ?>
                <button type="button" class="close">&times;</button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>

    <!-- Card Tabel -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Daftar Agenda</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Agenda</th>
                            <th width="15%">Tanggal Mulai</th>
                            <th width="15%">Tanggal Selesai</th>
                            <th width="15%">Lokasi</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)): 
                                $today = date('Y-m-d');
                                if ($row['tanggal_mulai'] <= $today && $row['tanggal_selesai'] >= $today) {
                                    $status = '<span class="badge-success">Berlangsung</span>';
                                } elseif ($row['tanggal_selesai'] < $today) {
                                    $status = '<span class="badge-secondary">Selesai</span>';
                                } else {
                                    $status = '<span class="badge-warning">Mendatang</span>';
                                }
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?>  </td>
                            <td><strong><?= htmlspecialchars($row['nama_agenda']) ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_mulai'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_selesai'])) ?></td>
                            <td><?= htmlspecialchars($row['lokasi'] ?? '-') ?></td>
                            <td class="text-center"><?= $status ?></td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="detail.php?id=<?= $row['id'] ?>" class="btn-view" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn-delete" 
                                            data-id="<?= $row['id'] ?>" 
                                            data-name="<?= htmlspecialchars($row['nama_agenda']) ?>"
                                            data-module="agenda"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="fas fa-calendar-alt"></i>
                                <p>Belum ada data agenda</p>
                                <a href="tambah.php" class="btn-primary">Tambah Agenda</a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i> Konfirmasi Hapus</h3>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus <span id="itemType">Agenda</span> berikut?</p>
            <p style="font-weight: bold; font-size: 1.1rem; margin: 10px 0;" id="deleteItemName"></p>
            <div id="fileWarningContainer" style="display: none;">
                <div style="color: #ef4444; background: #fee2e2; padding: 12px; border-radius: 5px; margin-bottom: 10px;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="fileWarningText"></span>
                </div>
            </div>
            <div style="color: #ef4444; background: #fee2e2; padding: 8px; border-radius: 5px;">
                <i class="fas fa-exclamation-circle"></i>
                Data yang sudah dihapus tidak dapat dikembalikan!
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" id="confirmDeleteBtn" class="btn-danger">Ya, Hapus</a>
            <button type="button" id="btnCloseModal" class="btn-secondary">Batal</button>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>