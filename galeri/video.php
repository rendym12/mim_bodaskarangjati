<?php
// Include config untuk BASE_URL
require_once '../includes/config.php';
include '../includes/header.php';

// Ambil data video
$query = mysqli_query($conn, "SELECT * FROM galeri_video ORDER BY urutan ASC, id DESC");
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="container">
        <h1>Galeri Video</h1>
        <p>Dokumentasi kegiatan dalam bentuk video di MI Muhammadiyah Bodaskarangjati</p>
    </div>
</div>

<div class="container">
    <?php if (mysqli_num_rows($query) > 0): ?>
        
        <!-- VIDEO GRID -->
        <div class="video-grid">
            <?php while ($row = mysqli_fetch_assoc($query)): 
                // Extract YouTube ID jika URL YouTube
                $video_id = '';
                $youtube_url = $row['url_video'];
                if (strpos($youtube_url, 'youtube.com') !== false || strpos($youtube_url, 'youtu.be') !== false) {
                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtube_url, $matches);
                    $video_id = $matches[1] ?? '';
                }
            ?>
            <div class="video-card" data-aos="fade-up">
                <div class="video-thumbnail" onclick="openVideoModal('<?= $row['url_video'] ?>', '<?= htmlspecialchars($row['judul']) ?>', '<?= htmlspecialchars($row['keterangan'] ?? '') ?>')">
                    <?php if (!empty($row['thumbnail'])): ?>
                        <img src="<?= BASE_URL ?>/uploads/galeri_video/<?= $row['thumbnail'] ?>" 
                             alt="<?= htmlspecialchars($row['judul']) ?>"
                             loading="lazy">
                    <?php elseif (!empty($video_id)): ?>
                        <img src="https://img.youtube.com/vi/<?= $video_id ?>/maxresdefault.jpg" 
                             alt="<?= htmlspecialchars($row['judul']) ?>"
                             onerror="this.src='https://img.youtube.com/vi/<?= $video_id ?>/hqdefault.jpg'">
                    <?php else: ?>
                        <div class="video-thumbnail-default">
                            <i class="fas fa-video"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="video-play">
                        <i class="fas fa-play"></i>
                    </div>
                    
                    <div class="video-duration">
                        <i class="fas fa-clock"></i> 02:30
                    </div>
                </div>
                
                <div class="video-content">
                    <h3 class="video-title">
                        <a href="javascript:void(0)" onclick="openVideoModal('<?= $row['url_video'] ?>', '<?= htmlspecialchars($row['judul']) ?>', '<?= htmlspecialchars($row['keterangan'] ?? '') ?>')">
                            <?= htmlspecialchars($row['judul']) ?>
                        </a>
                    </h3>
                    
                    <?php if (!empty($row['keterangan'])): ?>
                    <p class="video-description"><?= htmlspecialchars($row['keterangan']) ?></p>
                    <?php endif; ?>
                    
                    <div class="video-meta">
                        <span class="video-date">
                            <i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($row['created_at'] ?? 'now')) ?>
                        </span>
                        <?php if (!empty($row['urutan'])): ?>
                        <span class="video-order">#<?= $row['urutan'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-video-slash"></i>
            <p>Belum ada video</p>
        </div>
    <?php endif; ?>
</div>

<!-- VIDEO MODAL -->
<div id="videoModal" class="video-modal" onclick="closeVideoModal()">
    <div class="video-modal-content" onclick="event.stopPropagation()">
        <span class="video-modal-close" onclick="closeVideoModal()">&times;</span>
        <div class="video-modal-header">
            <h3 id="modalVideoTitle"></h3>
        </div>
        <div class="video-modal-body">
            <div id="modalVideoContainer" class="video-container">
                <!-- Video akan dimuat di sini -->
            </div>
        </div>
        <div class="video-modal-footer">
            <p id="modalVideoDescription"></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>