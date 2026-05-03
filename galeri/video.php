<?php
require_once '../includes/config.php';
include '../includes/header.php';

$query = mysqli_query($conn, "SELECT * FROM galeri_video ORDER BY urutan ASC, id DESC");
?>

<div class="page-header galeri-video-page">
    <div class="container">
        <h1>Galeri Video</h1>
        <p>Dokumentasi kegiatan dalam bentuk video di MI Muhammadiyah Bodaskarangjati</p>
    </div>
</div>

<div class="container">
    <?php if (mysqli_num_rows($query) > 0): ?>
        
        <div class="video-grid">
            <?php while ($row = mysqli_fetch_assoc($query)): 
                $video_id = '';
                $youtube_url = $row['url_video'];
                
                if (strpos($youtube_url, 'youtube.com') !== false || strpos($youtube_url, 'youtu.be') !== false) {
                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtube_url, $matches);
                    $video_id = $matches[1] ?? '';
                }
            ?>
            <div class="video-card" data-aos="fade-up">
                <!-- LANGSUNG LINK KE YOUTUBE -->
                <a href="<?= htmlspecialchars($row['url_video']) ?>" target="_blank" class="video-thumbnail">
                    
                    <?php if (!empty($row['thumbnail'])): ?>
                        <img src="<?= BASE_URL ?>/uploads/galeri_video/<?= $row['thumbnail'] ?>" 
                             alt="<?= htmlspecialchars($row['judul']) ?>"
                             loading="lazy">
                    <?php elseif (!empty($video_id)): ?>
                        <img src="https://img.youtube.com/vi/<?= $video_id ?>/maxresdefault.jpg" 
                             alt="<?= htmlspecialchars($row['judul']) ?>"
                             loading="lazy"
                             onerror="this.src='https://img.youtube.com/vi/<?= $video_id ?>/hqdefault.jpg'">
                    <?php else: ?>
                        <div class="video-thumbnail-default">
                            <i class="fas fa-video"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="video-play">
                        <i class="fas fa-play"></i>
                    </div>
                    
                    <div class="video-youtube-badge">
                        <i class="fab fa-youtube"></i> Buka di YouTube
                    </div>
                </a>
                
                <div class="video-content">
                    <h3 class="video-title">
                        <a href="<?= htmlspecialchars($row['url_video']) ?>" target="_blank">
                            <?= htmlspecialchars($row['judul']) ?>
                            <i class="fab fa-youtube" style="color:#ff0000; font-size:14px; margin-left:5px;"></i>
                        </a>
                    </h3>
                    
                    <?php if (!empty($row['keterangan'])): ?>
                    <p class="video-description"><?= htmlspecialchars($row['keterangan']) ?></p>
                    <?php endif; ?>
                    
                    <div class="video-meta">
                        <span class="video-date">
                            <i class="far fa-calendar-alt"></i> 
                            <?= date('d M Y', strtotime($row['created_at'] ?? 'now')) ?>
                        </span>
                        <span class="video-youtube-link">
                            <i class="fab fa-youtube"></i> Tonton di YouTube
                        </span>
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

<?php include '../includes/footer.php'; ?>