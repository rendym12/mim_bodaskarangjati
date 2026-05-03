<?php
include('includes/header.php');
include('includes/db.php');

// ==============================================
// AMBIL DATA PPDB
// ==============================================
$ppdb = getActivePpdb($conn);

// ==============================================
// AMBIL DATA FASILITAS (dari tabel sarana)
// ==============================================
$fasilitas = getFasilitasList($conn);

// ==============================================
// AMBIL DATA PEMBIASAAN (dari tabel pembiasaan)
// ==============================================
$pembiasaan_list = getPembiasaanList($conn);

// ==============================================
// FUNGSI HELPER
// ==============================================

function getActivePpdb($conn) {
    $query = mysqli_query($conn, "SELECT * FROM ppdb WHERE status = 'aktif' LIMIT 1");
    $ppdb = mysqli_fetch_assoc($query);
    
    if (!$ppdb) {
        $query = mysqli_query($conn, "SELECT * FROM ppdb LIMIT 1");
        $ppdb = mysqli_fetch_assoc($query);
    }
    
    if (!$ppdb) {
        return [
            'status' => 'nonaktif',
            'judul' => 'PMBM',
            'sub_judul' => 'PENERIMAAN MURID BARU MADRASAH',
            'tahun_ajaran' => date('Y') . '/' . (date('Y')+1),
            'syarat' => "Syarat Pendaftaran :\n1. Kartu Keluarga (asli dan terbaru)\n2. Akta Kelahiran (asli)\n3. KTP salah satu orang tua\n4. Kartu dari desa bila memiliki\n\nCatatan :\n- Syarat pendaftaran bersifat wajib untuk validasi data siswa.\n- Dibawa saat datang ke MIM Bodaskarangjati (Pemetaan)"
        ];
    }
    
    return $ppdb;
}

function getFasilitasList($conn) {
    $query = mysqli_query($conn, "SELECT * FROM sarana ORDER BY urutan ASC");
    $fasilitas = [];
    
    while ($row = mysqli_fetch_assoc($query)) {
        $fasilitas[] = $row;
    }
    
    return $fasilitas;
}

function getPembiasaanList($conn) {
    $query = mysqli_query($conn, "SELECT * FROM pembiasaan ORDER BY urutan ASC LIMIT 4");
    $list = [];
    
    while ($row = mysqli_fetch_assoc($query)) {
        $list[] = $row;
    }
    
    return $list;
}

function getFasilitasIcon($fasilitas) {
    $icon = $fasilitas['ikon'] ?? 'fa-school';
    return htmlspecialchars($icon);
}

function getFasilitasNama($fasilitas) {
    $nama = $fasilitas['nama_sarana'] ?? '';
    return htmlspecialchars($nama);
}

function getPembiasaanIcon($item) {
    $icon = $item['ikon'] ?? 'fa-check-circle';
    return htmlspecialchars($icon);
}

function getPembiasaanDeskripsi($item) {
    $deskripsi = $item['deskripsi'] ?? '';
    return htmlspecialchars($deskripsi);
}

$badge_text = ($ppdb['status'] ?? '') == 'aktif' ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup';
?>

<!-- DATA ATTRIBUTE UNTUK JS -->
<body data-ppdb-target-date="<?= !empty($ppdb['tanggal_selesai']) ? $ppdb['tanggal_selesai'] . ' 23:59:59' : '' ?>">

<div class="ppdb-page">
    
    <!-- Hero Section -->
    <div class="ppdb-hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <span class="hero-badge"><?= htmlspecialchars($badge_text) ?></span>
                    <h1><?= htmlspecialchars($ppdb['judul'] ?? 'PMBM') ?></h1>
                    <p><?= htmlspecialchars($ppdb['sub_judul'] ?? 'PENERIMAAN MURID BARU MADRASAH') ?></p>
                    <div class="hero-info">
                        <div class="hero-year">
                            <i class="fas fa-calendar-alt"></i>
                            <span>TAHUN PELAJARAN <?= htmlspecialchars($ppdb['tahun_ajaran'] ?? date('Y') . '/' . (date('Y')+1)) ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($ppdb['tanggal_selesai']) && ($ppdb['status'] ?? '') == 'aktif'): ?>
                <div class="hero-countdown">
                    <div class="countdown-wrapper">
                        <div class="countdown-label">Masa Pendaftaran Berakhir Dalam</div>
                        <div class="countdown-timer" id="countdownTimer">
                            <div class="countdown-item"><span class="countdown-value" id="days">00</span><span class="countdown-label">Hari</span></div>
                            <div class="countdown-item"><span class="countdown-value" id="hours">00</span><span class="countdown-label">Jam</span></div>
                            <div class="countdown-item"><span class="countdown-value" id="minutes">00</span><span class="countdown-label">Menit</span></div>
                            <div class="countdown-item"><span class="countdown-value" id="seconds">00</span><span class="countdown-label">Detik</span></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">
        
        <!-- Info Cards -->
        <div class="info-cards">
            <?php if (!empty($ppdb['tanggal_mulai']) || !empty($ppdb['tanggal_selesai'])): ?>
            <div class="info-card">
                <div class="card-icon calendar"><i class="fas fa-calendar-alt"></i></div>
                <h3>Waktu Pendaftaran</h3>
                <p>
                    <?php if (!empty($ppdb['tanggal_mulai'])): ?>
                        <?= date('d F Y', strtotime($ppdb['tanggal_mulai'])) ?>
                    <?php endif; ?>
                    <?php if (!empty($ppdb['tanggal_selesai'])): ?>
                        <br>– <?= date('d F Y', strtotime($ppdb['tanggal_selesai'])) ?>
                    <?php endif; ?>
                </p>
            </div>
            <?php endif; ?>

            <?php if (!empty($ppdb['tanggal_pemetaan'])): ?>
            <div class="info-card">
                <div class="card-icon mapping"><i class="fas fa-map-marked-alt"></i></div>
                <h3>Pemetaan</h3>
                <p><?= htmlspecialchars($ppdb['tanggal_pemetaan']) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($ppdb['info_tambahan'])): ?>
            <div class="info-card">
                <div class="card-icon info"><i class="fas fa-info-circle"></i></div>
                <h3>Informasi</h3>
                <p><?= htmlspecialchars($ppdb['info_tambahan']) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- SYARAT PENDAFTARAN -->
        <div class="syarat-wrapper">
            <div class="section-title-top">
                <h2>Syarat Pendaftaran</h2>
                <p>Lengkapi persyaratan berikut untuk proses pendaftaran calon siswa baru</p>
            </div>
            
            <?php 
            $syarat_text = $ppdb['syarat'] ?? '';
            $syarat_only = '';
            $catatan_only = '';
            
            if (!empty($syarat_text)) {
                // Cari kata Catatan (case insensitive)
                $pos = stripos($syarat_text, 'catatan');
                if ($pos !== false) {
                    $syarat_only = trim(substr($syarat_text, 0, $pos));
                    $catatan_only = trim(substr($syarat_text, $pos));
                } else {
                    $syarat_only = $syarat_text;
                }
            }
            ?>
            
            <div class="syarat-container">
                <?php if (!empty($syarat_only)): ?>
                <div class="syarat-box">
                    <?php
                    $lines = explode("\n", $syarat_only);
                    foreach ($lines as $line):
                        $line = trim($line);
                        if (empty($line)) continue;
                        // Skip line yang mengandung kata Syarat
                        if (preg_match('/^Syarat/i', $line)) continue;
                        
                        if (preg_match('/^\d+\./', $line)):
                            $content = preg_replace('/^\d+\.\s*/', '', $line);
                    ?>
                        <div class="syarat-item">
                            <span class="syarat-number"><?= preg_replace('/\..*/', '', $line) ?></span>
                            <span class="syarat-text"><?= htmlspecialchars($content) ?></span>
                        </div>
                    <?php
                        elseif (preg_match('/^[-•]/', $line)):
                            $content = ltrim($line, '-• ');
                    ?>
                        <div class="syarat-item sub">
                            <span class="syarat-dot"></span>
                            <span class="syarat-text"><?= htmlspecialchars($content) ?></span>
                        </div>
                    <?php
                        else:
                    ?>
                        <div class="syarat-item info">
                            <span class="syarat-text"><?= htmlspecialchars($line) ?></span>
                        </div>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($catatan_only)): ?>
                <div class="catatan-box">
                    <?php
                    // Hapus kata "Catatan" dari awal
                    $catatan_content = preg_replace('/^Catatan\s*:\s*/i', '', $catatan_only);
                    $note_lines = explode("\n", $catatan_content);
                    foreach ($note_lines as $line):
                        $line = trim($line);
                        if (empty($line)) continue;
                        
                        if (preg_match('/^[-•]/', $line)):
                            $content = ltrim($line, '-• ');
                    ?>
                        <div class="catatan-item">
                            <span class="catatan-bullet"></span>
                            <span class="catatan-text"><?= htmlspecialchars($content) ?></span>
                        </div>
                    <?php
                        else:
                    ?>
                        <div class="catatan-item">
                            <span class="catatan-text"><?= htmlspecialchars($line) ?></span>
                        </div>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sarana Prasarana -->
        <?php if (!empty($fasilitas)): ?>
        <div class="fasilitas-wrapper">
            <div class="section-title">
                <h2>Sarana Prasarana</h2>
                <p>Sarana dan prasarana pendukung kegiatan belajar mengajar di MI Muhammadiyah Bodaskarangjati</p>
            </div>
            <div class="fasilitas-grid">
                <?php foreach ($fasilitas as $f): ?>
                <div class="fasilitas-card">
                    <div class="fasilitas-icon"><i class="fas <?= getFasilitasIcon($f) ?>"></i></div>
                    <h4><?= getFasilitasNama($f) ?></h4>
                    <!-- DESKRIPSI DIHAPUS -->
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Mengapa Memilih Kami -->
        <div class="mengapa-wrapper">
            <div class="section-title-top">
                <h2>Mengapa Memilih Kami?</h2>
                <p>Kegiatan pembiasaan positif setiap pagi di MI Muhammadiyah Bodaskarangjati</p>
            </div>
            <div class="mengapa-grid">
                <?php if (!empty($pembiasaan_list)): ?>
                    <?php foreach ($pembiasaan_list as $pembiasaan): ?>
                    <div class="mengapa-card">
                        <div class="mengapa-icon"><i class="fas <?= getPembiasaanIcon($pembiasaan) ?>"></i></div>
                        <h4><?= htmlspecialchars($pembiasaan['nama_kegiatan'] ?? '') ?></h4>
                        <p><?= getPembiasaanDeskripsi($pembiasaan) ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="mengapa-card"><div class="mengapa-icon"><i class="fas fa-mosque"></i></div><h4>Pembiasaan Sholat Dhuha</h4><p>Membiasakan sholat dhuha berjamaah setiap pagi</p></div>
                    <div class="mengapa-card"><div class="mengapa-icon"><i class="fas fa-hand-peace"></i></div><h4>Senyum Sapa Salam</h4><p>Membudayakan senyum, sapa, dan salam</p></div>
                    <div class="mengapa-card"><div class="mengapa-icon"><i class="fas fa-book-open"></i></div><h4>Literasi Al-Qur'an</h4><p>Membaca Al-Qur'an sebelum pelajaran</p></div>
                    <div class="mengapa-card"><div class="mengapa-icon"><i class="fas fa-trash-alt"></i></div><h4>Peduli Lingkungan</h4><p>Menjaga kebersihan madrasah</p></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pendaftaran Section -->
        <?php if (!empty($ppdb['link_pendaftaran']) && ($ppdb['status'] ?? '') == 'aktif'): ?>
        <div class="pendaftaran-wrapper">
            <div class="pendaftaran-content">
                <div class="pendaftaran-left">
                    <h3>Daftar Sekarang!</h3>
                    <p>Bergabunglah bersama kami untuk pendidikan yang berkualitas</p>
                    <a href="<?= htmlspecialchars($ppdb['link_pendaftaran']) ?>" target="_blank" class="btn-daftar">
                        Daftar Online Sekarang
                    </a>
                </div>
                <div class="pendaftaran-right">
                    <div class="qr-code">
                        <?php if (!empty($ppdb['qr_code']) && file_exists('uploads/qr/' . $ppdb['qr_code'])): ?>
                            <img src="uploads/qr/<?= htmlspecialchars($ppdb['qr_code']) ?>" alt="QR Code">
                        <?php else: ?>
                            <i class="fas fa-qrcode"></i>
                        <?php endif; ?>
                    </div>
                    <p>Scan QR Code untuk mendaftar</p>
                </div>
            </div>
        </div>
        <?php elseif (($ppdb['status'] ?? '') != 'aktif'): ?>
        <div class="pendaftaran-wrapper" style="background: linear-gradient(135deg, #64748b, #475569);">
            <div class="pendaftaran-content">
                <div class="pendaftaran-left">
                    <h3>Pendaftaran Ditutup</h3>
                    <p>Pendaftaran siswa baru sedang ditutup. Silakan cek kembali di lain waktu.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Kontak Section -->
        <div class="kontak-wrapper">
            <div class="kontak-content">
                <div class="kontak-icon"><i class="fas fa-headset"></i></div>
                <div class="kontak-info">
                    <h3>Info Lebih Lanjut</h3>
                    <?php if (!empty($ppdb['kontak_telepon']) || !empty($ppdb['kontak_nama'])): ?>
                        <p class="kontak-phone"><?= htmlspecialchars($ppdb['kontak_telepon'] ?? '') ?></p>
                        <p class="kontak-person"><?= htmlspecialchars($ppdb['kontak_nama'] ?? '') ?></p>
                        <?php if (!empty($ppdb['kontak_keterangan'])): ?>
                            <p class="kontak-keterangan"><?= htmlspecialchars($ppdb['kontak_keterangan']) ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="kontak-phone">0812-3456-7890</p>
                        <p class="kontak-person">Panitia PPDB MI Muhammadiyah Bodaskarangjati</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Catatan Penting -->
        <?php if (!empty($ppdb['catatan'])): ?>
        <div class="catatan-wrapper">
            <div class="catatan-content">
                <i class="fas fa-info-circle"></i>
                <p><?= nl2br(htmlspecialchars($ppdb['catatan'])) ?></p>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php include('includes/footer.php'); ?>