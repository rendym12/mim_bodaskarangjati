<?php
include "../includes/auth.php";

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Ambil data untuk dihapus
    $q = mysqli_query($conn, "SELECT judul, thumbnail FROM galeri_video WHERE id = $id");
    $data = mysqli_fetch_assoc($q);
    
    $file_deleted = false;
    
    // Cek apakah data ada
    $check = mysqli_query($conn, "SELECT * FROM galeri_video WHERE id = $id");
    
    if (mysqli_num_rows($check) > 0) {
        $judul = $data['judul'];
        
        // Hapus file thumbnail jika ada
        if ($data && !empty($data['thumbnail']) && file_exists("../../uploads/galeri_video/" . $data['thumbnail'])) {
            if (unlink("../../uploads/galeri_video/" . $data['thumbnail'])) {
                $file_deleted = true;
            }
        }
        
        if (mysqli_query($conn, "DELETE FROM galeri_video WHERE id = $id")) {
            if ($file_deleted) {
                $_SESSION['success'] = [
                    'message' => "Video <strong>\"$judul\"</strong> berhasil dihapus",
                    'file_deleted' => true,
                    'type' => 'video'
                ];
            } else {
                $_SESSION['success'] = [
                    'message' => "Video <strong>\"$judul\"</strong> berhasil dihapus",
                    'type' => 'video'
                ];
            }
        } else {
            $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Data video tidak ditemukan";
    }
    
    header("Location: index.php");
    exit;
}

// ========== AMBIL DATA ==========
$query = mysqli_query($conn, "SELECT * FROM galeri_video ORDER BY urutan ASC, id DESC");

include "../includes/header.php";
?>

<div class="content-wrapper galeri-video-page">
    <div class="content-header">
        <h1><i class="fas fa-video"></i> Kelola Galeri Video</h1>
        <a href="tambah.php" class="btn-primary">
            <i class="fas fa-plus"></i> Tambah Video
        </a>
    </div>

    <!-- NOTIFICATION CONTAINER -->
    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <?php if (is_array($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible <?= isset($_SESSION['success']['file_deleted']) ? 'file-deleted' : '' ?>">
                    <i class="fas fa-check-circle"></i>
                    <?= $_SESSION['success']['message'] ?>
                    
                    <?php if (isset($_SESSION['success']['file_deleted'])): ?>
                        <div class="file-list" style="margin-top: 10px;">
                            <span class="file-badge"><i class="fas fa-image"></i> Thumbnail</span>
                            <small style="display: block; margin-top: 8px; color: #0b5e2e;">
                                <i class="fas fa-info-circle"></i> File thumbnail ikut terhapus
                            </small>
                        </div>
                    <?php endif; ?>
                    
                    <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            <?php else: ?>
                <div class="alert alert-success alert-dismissible">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
                    <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            <?php endif; ?>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible">
                <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Daftar Video</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Thumbnail</th>
                            <th width="20%">Judul</th>
                            <th width="20%">URL Video</th>
                            <th width="8%">Urutan</th>
                            <th width="15%">Keterangan</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)): 
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <?php if (!empty($row['thumbnail'])): ?>
                                    <img src="../../uploads/galeri_video/<?= $row['thumbnail'] ?>" 
                                         alt="<?= htmlspecialchars($row['judul']) ?>" 
                                         style="width: 80px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid var(--secondary);">
                                <?php else: ?>
                                    <div style="width: 80px; height: 60px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                        <i class="fas fa-video" style="font-size: 1.5rem; color: #94a3b8;"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($row['judul']) ?></strong></td>
                            <td>
                                <!-- TOMBOL LIHAT VIDEO YANG MENARIK -->
                                <div class="video-url-cell">
                                    <?php 
                                    $url_video = htmlspecialchars($row['url_video']);
                                    // Extract video ID from YouTube URL if needed
                                    $video_id = '';
                                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\?]+)/', $url_video, $matches)) {
                                        $video_id = $matches[1];
                                        $embed_url = "https://www.youtube.com/embed/$video_id";
                                    } else {
                                        $embed_url = $url_video;
                                    }
                                    ?>
                                    <a href="<?= $embed_url ?>" target="_blank" class="btn-video-link" title="Tonton Video">
                                        <i class="fas fa-play"></i> Lihat Video
                                    </a>
                                </div>
                             </div>
                            </td>
                            <td class="text-center">
                                <span style="display: inline-block; padding: 4px 10px; background: #f1f5f9; border-radius: 20px; font-weight: 600; font-size: 0.8rem;">
                                    <?= $row['urutan'] ?? '0' ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $keterangan = htmlspecialchars($row['keterangan'] ?? '-');
                                if (strlen($keterangan) > 40) {
                                    echo substr($keterangan, 0, 40) . '...';
                                } else {
                                    echo $keterangan;
                                }
                                ?>
                            </td>
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
                                            data-name="<?= htmlspecialchars($row['judul']) ?>"
                                            data-module="video"
                                            data-has-thumbnail="<?= (!empty($row['thumbnail']) ? 'true' : 'false') ?>"
                                            data-thumbnail-name="<?= htmlspecialchars($row['thumbnail'] ?? '') ?>"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="fas fa-video"></i>
                                <p>Belum ada data video</p>
                                <a href="tambah.php" class="btn-primary">Tambah Video</a>
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
            <p>Apakah Anda yakin ingin menghapus video berikut?</p>
            <p style="font-weight: bold; font-size: 1.1rem; margin: 10px 0;" id="deleteItemName"></p>
            <div id="fileWarningContainer" style="display: none;">
                <div style="color: #ef4444; background: #fee2e2; padding: 12px; border-radius: 8px; margin-bottom: 10px;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="fileWarningText"></span>
                    <div id="fileList" style="margin-top: 8px; padding-left: 20px;"></div>
                </div>
            </div>
            <div style="color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 8px;">
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