<?php
include('includes/header.php');
include('includes/db.php');

// ==============================================
// AMBIL DATA PPDB
// ==============================================
$ppdb = getActivePpdb($conn);

// ==============================================
// AMBIL DATA FASILITAS
// ==============================================
$fasilitas = getFasilitasList($conn);

// ==============================================
// AMBIL DATA PEMBIASAAN
// ==============================================
$pembiasaan_list = getPembiasaanList($conn);

// ==============================================
// AMBIL DATA SYARAT PENDAFTARAN
// ==============================================
$syarat_data = getSyaratData($ppdb);

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
    
    return $ppdb ?: [];
}

function getFasilitasList($conn) {
    $query = mysqli_query($conn, "SELECT * FROM sarana ORDER BY urutan ASC LIMIT 6");
    $fasilitas = [];
    
    while ($row = mysqli_fetch_assoc($query)) {
        $fasilitas[] = $row;
    }
    
    if (count($fasilitas) < 6) {
        return getDefaultFasilitas();
    }
    
    return $fasilitas;
}

function getDefaultFasilitas() {
    return [
        ['icon' => 'fa-chalkboard', 'nama' => 'Ruang Kelas Ber-AC'],
        ['icon' => 'fa-laptop', 'nama' => 'Lab Komputer'],
        ['icon' => 'fa-book', 'nama' => 'Perpustakaan Digital'],
        ['icon' => 'fa-mosque', 'nama' => 'Musholla Nyaman'],
        ['icon' => 'fa-futbol', 'nama' => 'Lapangan Olahraga'],
        ['icon' => 'fa-utensils', 'nama' => 'Kantin Sehat & Bersih']
    ];
}

function getPembiasaanList($conn) {
    $query = mysqli_query($conn, "SELECT * FROM pembiasaan ORDER BY urutan ASC LIMIT 4");
    $list = [];
    
    while ($row = mysqli_fetch_assoc($query)) {
        $list[] = $row;
    }
    
    return $list;
}

function parseSyaratData($data) {
    if (empty($data)) return null;
    
    $decoded = json_decode($data, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        return $decoded;
    }
    
    $raw = str_replace(['<br>', '<br/>', '<br />'], "\n", $data);
    $raw = strip_tags($raw);
    $items = array_filter(array_map('trim', explode("\n", $raw)));
    
    if (empty($items)) return null;
    
    return [
        [
            'icon' => 'fa-edit',
            'title' => 'Mengisi Formulir Pendaftaran',
            'description' => 'Pendaftaran dapat dilakukan secara ONLINE melalui website resmi madrasah atau OFFLINE dengan datang langsung ke kantor madrasah.',
            'options' => ['online', 'offline'],
            'is_required' => true
        ],
        [
            'icon' => 'fa-folder-open',
            'title' => 'Menyerahkan Berkas Pendaftaran',
            'description' => 'Berkas yang diperlukan untuk discan/difoto:',
            'files' => array_map(function($item) {
                $icon = 'fa-file-alt';
                if (stripos($item, 'KK') !== false || stripos($item, 'Kartu') !== false) {
                    $icon = 'fa-file-alt';
                } elseif (stripos($item, 'Akta') !== false) {
                    $icon = 'fa-baby-carriage';
                } elseif (stripos($item, 'KTP') !== false) {
                    $icon = 'fa-id-card';
                }
                return ['icon' => $icon, 'name' => $item];
            }, $items),
            'note' => 'Berkas difoto/discan dalam kondisi jelas',
            'is_required' => true
        ]
    ];
}

function getSyaratData($ppdb) {
    $data = parseSyaratData($ppdb['syarat'] ?? '');
    
    if (empty($data)) {
        return getDefaultSyarat();
    }
    
    return $data;
}

function getDefaultSyarat() {
    return [
        [
            'icon' => 'fa-edit',
            'title' => 'Mengisi Formulir Pendaftaran',
            'description' => 'Pendaftaran dapat dilakukan secara ONLINE melalui website resmi madrasah atau OFFLINE dengan datang langsung ke kantor madrasah.',
            'options' => ['online', 'offline'],
            'is_required' => true
        ],
        [
            'icon' => 'fa-folder-open',
            'title' => 'Menyerahkan Berkas Pendaftaran',
            'description' => 'Berkas yang diperlukan untuk discan/difoto:',
            'files' => [
                ['icon' => 'fa-file-alt', 'name' => 'Kartu Keluarga (KK)'],
                ['icon' => 'fa-baby-carriage', 'name' => 'Akta Kelahiran'],
                ['icon' => 'fa-id-card', 'name' => 'KTP (salah satu orang tua/wali)']
            ],
            'note' => 'Berkas difoto/discan dalam kondisi jelas',
            'is_required' => true
        ]
    ];
}

function getFasilitasIcon($fasilitas) {
    $icon = $fasilitas['ikon'] ?? $fasilitas['icon'] ?? 'fa-school';
    return htmlspecialchars($icon);
}

function getFasilitasNama($fasilitas) {
    $nama = $fasilitas['nama_sarana'] ?? $fasilitas['nama'] ?? '';
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

function getDefaultPembiasaanCards() {
    $defaults = [
        ['icon' => 'fa-mosque', 'nama' => 'Pembiasaan Sholat Dhuha', 'deskripsi' => 'Membiasakan sholat dhuha berjamaah setiap pagi'],
        ['icon' => 'fa-hand-peace', 'nama' => 'Senyum Sapa Salam', 'deskripsi' => 'Membudayakan senyum, sapa, dan salam kepada guru dan teman'],
        ['icon' => 'fa-book-open', 'nama' => 'Literasi Al-Qur\'an', 'deskripsi' => 'Membaca Al-Qur\'an sebelum memulai pelajaran'],
        ['icon' => 'fa-trash-alt', 'nama' => 'Peduli Lingkungan', 'deskripsi' => 'Kegiatan menjaga kebersihan dan keindahan madrasah']
    ];
    
    $html = '';
    foreach ($defaults as $item) {
        $html .= '<div class="mengapa-card">
                    <div class="mengapa-icon">
                        <i class="fas ' . htmlspecialchars($item['icon']) . '"></i>
                    </div>
                    <h4>' . htmlspecialchars($item['nama']) . '</h4>
                    <p>' . htmlspecialchars($item['deskripsi']) . '</p>
                  </div>';
    }
    return $html;
}
?>

<!-- TAMBAHKAN DATA ATTRIBUTE UNTUK JS COUNTDOWN -->
<body data-ppdb-target-date="<?= !empty($ppdb['tanggal_selesai']) ? $ppdb['tanggal_selesai'] . ' 23:59:59' : '' ?>">

<!-- KONTEN PPDB -->
<div class="ppdb-page">
    
    <!-- Hero Section -->
    <div class="ppdb-hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <span class="hero-badge"><?= htmlspecialchars($ppdb['badge'] ?? 'Pendaftaran Dibuka') ?></span>
                    <h1><?= htmlspecialchars($ppdb['judul'] ?? 'PMBM') ?></h1>
                    <p><?= htmlspecialchars($ppdb['sub_judul'] ?? 'PENERIMAAN MURID BARU MADRASAH') ?></p>
                    <div class="hero-info">
                        <div class="hero-year">
                            <i class="fas fa-calendar-alt"></i>
                            <span>TAHUN PELAJARAN <?= htmlspecialchars($ppdb['tahun_ajaran'] ?? date('Y') . '/' . (date('Y')+1)) ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($ppdb['tanggal_selesai'])): ?>
                <div class="hero-countdown">
                    <div class="countdown-wrapper">
                        <div class="countdown-label">Masa Pendaftaran Berakhir Dalam</div>
                        <div class="countdown-timer" id="countdownTimer">
                            <div class="countdown-item">
                                <span class="countdown-value" id="days">00</span>
                                <span class="countdown-label">Hari</span>
                            </div>
                            <div class="countdown-item">
                                <span class="countdown-value" id="hours">00</span>
                                <span class="countdown-label">Jam</span>
                            </div>
                            <div class="countdown-item">
                                <span class="countdown-value" id="minutes">00</span>
                                <span class="countdown-label">Menit</span>
                            </div>
                            <div class="countdown-item">
                                <span class="countdown-value" id="seconds">00</span>
                                <span class="countdown-label">Detik</span>
                            </div>
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
                <div class="card-icon calendar">
                    <i class="fas fa-calendar-alt"></i>
                </div>
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
                <div class="card-icon mapping">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h3>Pemetaan</h3>
                <p><?= htmlspecialchars($ppdb['tanggal_pemetaan']) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($ppdb['info_tambahan'])): ?>
            <div class="info-card">
                <div class="card-icon info">
                    <i class="fas fa-info-circle"></i>
                </div>
                <h3>Informasi</h3>
                <p><?= htmlspecialchars($ppdb['info_tambahan']) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Syarat Pendaftaran -->
        <div class="syarat-wrapper">
            <div class="section-title-top">
                <h2>Syarat Pendaftaran</h2>
                <p>Lengkapi persyaratan berikut untuk proses pendaftaran calon siswa baru</p>
            </div>

            <div class="syarat-grid">
                <?php foreach ($syarat_data as $item): ?>
                <div class="syarat-card">
                    <div class="syarat-card-icon">
                        <i class="fas <?= htmlspecialchars($item['icon']) ?>"></i>
                    </div>
                    <div class="syarat-card-content">
                        <h4><?= htmlspecialchars($item['title']) ?></h4>
                        <p><?= htmlspecialchars($item['description']) ?></p>
                        
                        <?php if (!empty($item['options'])): ?>
                        <div class="syarat-options">
                            <?php foreach ($item['options'] as $opt): ?>
                                <span class="option-badge <?= htmlspecialchars($opt) ?>">
                                    <i class="fas <?= $opt == 'online' ? 'fa-globe' : 'fa-building' ?>"></i>
                                    <?= ucfirst(htmlspecialchars($opt)) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['files'])): ?>
                        <ul class="berkas-list">
                            <?php foreach ($item['files'] as $file): ?>
                            <li>
                                <i class="fas <?= htmlspecialchars($file['icon']) ?>"></i>
                                <span><?= htmlspecialchars($file['name']) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        
                        <?php if ($item['is_required'] ?? false): ?>
                        <div class="syarat-badge">
                            <i class="fas fa-check-circle"></i> Wajib
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['note'])): ?>
                        <div class="syarat-note-small">
                            <i class="fas fa-info-circle"></i>
                            <?= htmlspecialchars($item['note']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="syarat-note">
                <div class="note-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="note-content">
                    <strong>Informasi Penting:</strong>
                    <p>Bagi yang mendaftar secara offline, harap membawa berkas asli untuk diverifikasi. Pendaftaran online akan mendapatkan nomor pendaftaran yang digunakan untuk proses selanjutnya.</p>
                </div>
            </div>
        </div>

        <!-- Fasilitas Unggulan -->
        <div class="fasilitas-wrapper">
            <div class="section-title">
                <h2>Fasilitas Unggulan</h2>
                <p>Berbagai fasilitas pendukung kegiatan belajar mengajar</p>
            </div>
            
            <div class="fasilitas-grid">
                <?php foreach ($fasilitas as $f): ?>
                <div class="fasilitas-card">
                    <div class="fasilitas-icon">
                        <i class="fas <?= getFasilitasIcon($f) ?>"></i>
                    </div>
                    <h4><?= getFasilitasNama($f) ?></h4>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Keunggulan / Mengapa Memilih Kami -->
        <div class="mengapa-wrapper">
            <div class="section-title-top">
                <h2>Mengapa Memilih Kami?</h2>
                <p>Kegiatan pembiasaan positif setiap pagi di MI Muhammadiyah Bodaskarangjati</p>
            </div>
            
            <div class="mengapa-grid">
                <?php if (!empty($pembiasaan_list)): ?>
                    <?php foreach ($pembiasaan_list as $pembiasaan): ?>
                    <div class="mengapa-card">
                        <div class="mengapa-icon">
                            <i class="fas <?= getPembiasaanIcon($pembiasaan) ?>"></i>
                        </div>
                        <h4><?= htmlspecialchars($pembiasaan['nama_kegiatan'] ?? '') ?></h4>
                        <p><?= getPembiasaanDeskripsi($pembiasaan) ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?= getDefaultPembiasaanCards() ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pendaftaran Section -->
        <?php if (!empty($ppdb['link_pendaftaran'])): ?>
        <div class="pendaftaran-wrapper">
            <div class="pendaftaran-content">
                <div class="pendaftaran-left">
                    <h3>Daftar Sekarang!</h3>
                    <p>Bergabunglah bersama kami untuk pendidikan yang berkualitas</p>
                    <a href="<?= htmlspecialchars($ppdb['link_pendaftaran']) ?>" target="_blank" class="btn-daftar">
                        <i class="fas fa-external-link-alt"></i> Daftar Online Sekarang
                    </a>
                </div>
                <div class="pendaftaran-right">
                    <div class="qr-code">
                        <?php if (!empty($ppdb['qr_code'])): ?>
                            <img src="uploads/qr/<?= htmlspecialchars($ppdb['qr_code']) ?>" alt="QR Code">
                        <?php else: ?>
                            <i class="fas fa-qrcode"></i>
                        <?php endif; ?>
                    </div>
                    <p>Scan QR Code untuk mendaftar</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Kontak Section -->
        <div class="kontak-wrapper">
            <div class="kontak-content">
                <div class="kontak-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="kontak-info">
                    <h3>Info Lebih Lanjut</h3>
                    <?php if (!empty($ppdb['kontak_telepon']) || !empty($ppdb['kontak_nama'])): ?>
                        <p class="kontak-phone"><?= htmlspecialchars($ppdb['kontak_telepon'] ?? '') ?></p>
                        <p class="kontak-person"><?= htmlspecialchars($ppdb['kontak_nama'] ?? '') ?></p>
                    <?php else: ?>
                        <p class="kontak-phone">0812-3456-7890</p>
                        <p class="kontak-person">Panitia PPDB MI Muhammadiyah Bodaskarangjati</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Catatan Penting -->
        <div class="catatan-wrapper">
            <div class="catatan-content">
                <i class="fas fa-info-circle"></i>
                <p><?= htmlspecialchars($ppdb['catatan'] ?? 'Pendaftaran dilakukan secara online melalui link di atas. Calon siswa yang sudah mendaftar akan mendapatkan nomor pendaftaran untuk keperluan pemetaan. Kuota terbatas, segera daftarkan putra-putri Anda.') ?></p>
            </div>
        </div>

    </div>
</div>

<?php include('includes/footer.php'); ?>