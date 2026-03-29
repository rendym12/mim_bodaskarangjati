<?php
// ../sarana.php - Halaman publik untuk menampilkan sarana
include "../includes/config.php";
include "../includes/header.php";

// Tentukan base URL untuk gambar
$base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . "/mim_bodaskarangjati";

// Ambil data sarana
$query = mysqli_query($conn, "SELECT * FROM sarana ORDER BY urutan ASC, id ASC");
?>

<!-- Page Header -->
<section class="page-header" style="background: linear-gradient(135deg, #0B3D91, #082e6b); padding: 60px 0; text-align: center; color: white;">
    <div class="container">
        <h1 style="font-size: 2.5rem; margin-bottom: 15px;">Sarana & Prasarana</h1>
        <p style="font-size: 1.1rem; max-width: 700px; margin: 0 auto; opacity: 0.9;">
            Fasilitas pendukung kegiatan belajar mengajar di MI Muhammadiyah Bodaskarangjati
        </p>
    </div>
</section>

<!-- Sarana Section -->
<section style="padding: 60px 0; background: #f8fafc;">
    <div class="container">
        <?php if (mysqli_num_rows($query) > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px;">
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #eef2f6; transition: all 0.3s ease;"
                     onmouseover="this.style.boxShadow='0 20px 40px rgba(11,61,145,0.15)'; this.style.borderColor='#FFD700'"
                     onmouseout="this.style.boxShadow='0 10px 30px rgba(0,0,0,0.05)'; this.style.borderColor='#eef2f6'">
                    
                    <!-- Gambar -->
                    <?php if (!empty($row['gambar'])): ?>
                    <div style="height: 220px; overflow: hidden;">
                        <img src="<?= $base_url ?>/uploads/sarana/<?= $row['gambar'] ?>" 
                             alt="<?= htmlspecialchars($row['nama_sarana']) ?>"
                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;"
                             onmouseover="this.style.transform='scale(1.1)'"
                             onmouseout="this.style.transform='scale(1)'"
                             onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'height:220px; background:linear-gradient(135deg, #f0f4ff, #e6f0ff); display:flex; align-items:center; justify-content:center;\'><i class=\'fas <?= $row['ikon'] ?? 'fa-building' ?>\' style=\'font-size:5rem; color:#0B3D91; opacity:0.3;\'></i></div>';">
                    </div>
                    <?php else: ?>
                    <div style="height: 150px; background: linear-gradient(135deg, #f0f4ff, #e6f0ff); display: flex; align-items: center; justify-content: center;">
                        <i class="fas <?= $row['ikon'] ?? 'fa-building' ?>" style="font-size: 5rem; color: #0B3D91; opacity: 0.3;"></i>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Konten -->
                    <div style="padding: 25px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                            <i class="fas <?= $row['ikon'] ?? 'fa-building' ?>" style="font-size: 1.8rem; color: #FFD700;"></i>
                            <h3 style="margin: 0; font-size: 1.3rem; color: #0B3D91; font-weight: 600;"><?= htmlspecialchars($row['nama_sarana']) ?></h3>
                        </div>
                        
                        <?php if (!empty($row['keterangan'])): ?>
                        <p style="color: #4a5568; line-height: 1.7; margin-bottom: 0;">
                            <?= nl2br(htmlspecialchars($row['keterangan'])) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 16px;">
                <i class="fas fa-building" style="font-size: 5rem; color: #cbd5e0; margin-bottom: 20px;"></i>
                <p style="color: #718096; font-size: 1.2rem;">Belum ada data sarana prasarana</p>
            </div>
        <?php endif; ?>
    </div>
</section>


<?php include "../includes/footer.php"; ?>