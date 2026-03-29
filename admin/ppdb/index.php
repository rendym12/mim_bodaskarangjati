<?php
ob_start();
include "../includes/auth.php";

// Ambil data PPDB
$query = mysqli_query($conn, "SELECT * FROM ppdb LIMIT 1");
$ppdb = mysqli_fetch_assoc($query);

if (!$ppdb) {
    // Buat data default jika belum ada - Format JSON untuk syarat
    $default = [
        'status' => 'nonaktif',
        'judul' => 'PMBM',
        'sub_judul' => 'PENERIMAAN MURID BARU MADRASAH',
        'tahun_ajaran' => date('Y') . '/' . (date('Y')+1),
        'tanggal_mulai' => date('Y-m-d'),
        'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
        'tanggal_pemetaan' => '',
        'info_tambahan' => '',
        'syarat' => json_encode([
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
        ], JSON_UNESCAPED_UNICODE),
        'link_pendaftaran' => '',
        'qr_code' => '',
        'kontak_telepon' => '',
        'kontak_nama' => '',
        'catatan' => ''
    ];
    $ppdb = $default;
}

// Decode data syarat dari JSON untuk ditampilkan di form
$syarat_data = json_decode($ppdb['syarat'] ?? '[]', true);
if (!is_array($syarat_data)) {
    $syarat_data = [];
}

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $sub_judul = mysqli_real_escape_string($conn, $_POST['sub_judul']);
    $tahun_ajaran = mysqli_real_escape_string($conn, $_POST['tahun_ajaran']);
    $tanggal_mulai = mysqli_real_escape_string($conn, $_POST['tanggal_mulai']);
    $tanggal_selesai = mysqli_real_escape_string($conn, $_POST['tanggal_selesai']);
    $tanggal_pemetaan = mysqli_real_escape_string($conn, $_POST['tanggal_pemetaan']);
    $info_tambahan = mysqli_real_escape_string($conn, $_POST['info_tambahan']);
    $link_pendaftaran = mysqli_real_escape_string($conn, $_POST['link_pendaftaran']);
    $kontak_telepon = mysqli_real_escape_string($conn, $_POST['kontak_telepon']);
    $kontak_nama = mysqli_real_escape_string($conn, $_POST['kontak_nama']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    
    // Proses data syarat dari form
    $syarat_array = [];
    if (!empty($_POST['syarat_icon'])) {
        foreach ($_POST['syarat_icon'] as $index => $icon) {
            $item = [
                'icon' => $icon,
                'title' => $_POST['syarat_title'][$index] ?? '',
                'description' => $_POST['syarat_desc'][$index] ?? '',
                'is_required' => isset($_POST['syarat_required'][$index]) ? true : false
            ];
            
            // Options (online/offline)
            if (!empty($_POST['syarat_options'][$index])) {
                $item['options'] = $_POST['syarat_options'][$index];
            }
            
            // Files
            if (!empty($_POST['syarat_files'][$index]['name'])) {
                $files = [];
                foreach ($_POST['syarat_files'][$index]['name'] as $fkey => $fname) {
                    if (empty($fname)) continue;
                    $files[] = [
                        'icon' => $_POST['syarat_files'][$index]['icon'][$fkey] ?? 'fa-file-alt',
                        'name' => $fname
                    ];
                }
                if (!empty($files)) {
                    $item['files'] = $files;
                }
            }
            
            // Note
            if (!empty($_POST['syarat_note'][$index])) {
                $item['note'] = $_POST['syarat_note'][$index];
            }
            
            if (!empty($item['title'])) {
                $syarat_array[] = $item;
            }
        }
    }
    
    $syarat_json = json_encode($syarat_array, JSON_UNESCAPED_UNICODE);
    
    $errors = [];
    
    // Upload QR Code jika ada
    $qr_code = $ppdb['qr_code'] ?? null;
    if (isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['qr_code']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['qr_code']['size'];
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file QR Code harus JPG, JPEG, PNG, atau GIF";
        } elseif ($size > 1 * 1024 * 1024) {
            $errors[] = "Ukuran file QR Code maksimal 1MB";
        } else {
            // Buat folder jika belum ada
            if (!file_exists('../../uploads/qr')) {
                mkdir('../../uploads/qr', 0777, true);
            }
            
            // Hapus QR lama jika ada
            if (!empty($ppdb['qr_code']) && file_exists("../../uploads/qr/" . $ppdb['qr_code'])) {
                unlink("../../uploads/qr/" . $ppdb['qr_code']);
            }
            
            $qr_code = 'qr_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['qr_code']['tmp_name'], '../../uploads/qr/' . $qr_code);
        }
    }
    
    if (empty($errors)) {
        if (isset($ppdb['id'])) {
            // Update
            $sql = "UPDATE ppdb SET 
                    status='$status',
                    judul='$judul',
                    sub_judul='$sub_judul',
                    tahun_ajaran='$tahun_ajaran',
                    tanggal_mulai=" . ($tanggal_mulai ? "'$tanggal_mulai'" : "NULL") . ",
                    tanggal_selesai=" . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ",
                    tanggal_pemetaan=" . ($tanggal_pemetaan ? "'$tanggal_pemetaan'" : "NULL") . ",
                    info_tambahan=" . ($info_tambahan ? "'$info_tambahan'" : "NULL") . ",
                    syarat=" . ($syarat_json ? "'" . mysqli_real_escape_string($conn, $syarat_json) . "'" : "NULL") . ",
                    link_pendaftaran=" . ($link_pendaftaran ? "'$link_pendaftaran'" : "NULL") . ",
                    qr_code=" . ($qr_code ? "'$qr_code'" : "NULL") . ",
                    kontak_telepon=" . ($kontak_telepon ? "'$kontak_telepon'" : "NULL") . ",
                    kontak_nama=" . ($kontak_nama ? "'$kontak_nama'" : "NULL") . ",
                    catatan=" . ($catatan ? "'$catatan'" : "NULL") . "
                    WHERE id={$ppdb['id']}";
        } else {
            // Insert
            $sql = "INSERT INTO ppdb (status, judul, sub_judul, tahun_ajaran, tanggal_mulai, tanggal_selesai, tanggal_pemetaan, info_tambahan, syarat, link_pendaftaran, qr_code, kontak_telepon, kontak_nama, catatan) 
                    VALUES ('$status', '$judul', '$sub_judul', '$tahun_ajaran', " . ($tanggal_mulai ? "'$tanggal_mulai'" : "NULL") . ", " . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", " . ($tanggal_pemetaan ? "'$tanggal_pemetaan'" : "NULL") . ", " . ($info_tambahan ? "'$info_tambahan'" : "NULL") . ", '" . mysqli_real_escape_string($conn, $syarat_json) . "', " . ($link_pendaftaran ? "'$link_pendaftaran'" : "NULL") . ", " . ($qr_code ? "'$qr_code'" : "NULL") . ", " . ($kontak_telepon ? "'$kontak_telepon'" : "NULL") . ", " . ($kontak_nama ? "'$kontak_nama'" : "NULL") . ", " . ($catatan ? "'$catatan'" : "NULL") . ")";
        }
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success'] = "Data PPDB berhasil diperbarui!";
            ob_end_clean();
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
}

include "../includes/header.php";
?>

<div class="content-wrapper ppdb-admin">
    <div class="content-header">
        <h1><i class="fas fa-graduation-cap"></i> Kelola PPDB</h1>
        <a href="../dashboard.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>

    <!-- NOTIFICATION CONTAINER -->
    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible">
                <i class="fas fa-exclamation-circle"></i>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data" id="ppdbForm">
            <!-- Status PPDB -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-toggle-on"></i>
                    <h3>Status PPDB</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Status Pendaftaran</label>
                        <select name="status" class="form-control">
                            <option value="aktif" <?= ($ppdb['status'] ?? '') == 'aktif' ? 'selected' : '' ?>>Aktif (Dibuka)</option>
                            <option value="nonaktif" <?= ($ppdb['status'] ?? '') == 'nonaktif' ? 'selected' : '' ?>>Nonaktif (Ditutup)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Header Poster -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-heading"></i>
                    <h3>Header Poster</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Judul Utama (H1)</label>
                        <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($ppdb['judul'] ?? 'PMBM') ?>" placeholder="Contoh: PMBM">
                    </div>

                    <div class="form-group">
                        <label>Sub Judul (H2)</label>
                        <input type="text" name="sub_judul" class="form-control" value="<?= htmlspecialchars($ppdb['sub_judul'] ?? 'PENERIMAAN MURID BARU MADRASAH') ?>" placeholder="Contoh: PENERIMAAN MURID BARU MADRASAH">
                    </div>

                    <div class="form-group">
                        <label>Tahun Ajaran (H3)</label>
                        <input type="text" name="tahun_ajaran" class="form-control" value="<?= htmlspecialchars($ppdb['tahun_ajaran'] ?? date('Y') . '/' . (date('Y')+1)) ?>" placeholder="Contoh: 2026/2027">
                    </div>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i>
                    <h3>Info Cards</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="<?= $ppdb['tanggal_mulai'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" value="<?= $ppdb['tanggal_selesai'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-map"></i> Tanggal Pemetaan</label>
                        <input type="text" name="tanggal_pemetaan" class="form-control" value="<?= htmlspecialchars($ppdb['tanggal_pemetaan'] ?? '') ?>" placeholder="Contoh: 20-22 Juni 2026">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-info"></i> Info Tambahan</label>
                        <input type="text" name="info_tambahan" class="form-control" value="<?= htmlspecialchars($ppdb['info_tambahan'] ?? '') ?>" placeholder="Contoh: Kuota Terbatas">
                    </div>
                </div>
            </div>

            <!-- Syarat Pendaftaran - Dynamic Form -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Syarat Pendaftaran</h3>
                    <button type="button" class="btn-add-syarat" id="addSyaratBtn">
                        <i class="fas fa-plus"></i> Tambah Syarat
                    </button>
                </div>
                <div class="card-body">
                    <div id="syaratContainer">
                        <?php if (!empty($syarat_data)): ?>
                            <?php foreach ($syarat_data as $index => $item): ?>
                            <div class="syarat-item" data-index="<?= $index ?>">
                                <div class="syarat-header">
                                    <h4><i class="fas fa-edit"></i> Syarat <?= $index + 1 ?></h4>
                                    <button type="button" class="btn-remove-syarat" data-index="<?= $index ?>">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </div>
                                
                                <div class="syarat-fields">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Icon (FontAwesome)</label>
                                            <input type="text" name="syarat_icon[]" class="form-control" value="<?= htmlspecialchars($item['icon'] ?? 'fa-check') ?>" placeholder="fa-edit">
                                            <small>Contoh: fa-edit, fa-folder-open, fa-file-alt</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Judul</label>
                                            <input type="text" name="syarat_title[]" class="form-control" value="<?= htmlspecialchars($item['title'] ?? '') ?>" placeholder="Judul syarat" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea name="syarat_desc[]" class="form-control" rows="2" placeholder="Deskripsi syarat"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Options (Online/Offline)</label>
                                            <select name="syarat_options[<?= $index ?>][]" class="form-control" multiple>
                                                <option value="online" <?= in_array('online', $item['options'] ?? []) ? 'selected' : '' ?>>Online</option>
                                                <option value="offline" <?= in_array('offline', $item['options'] ?? []) ? 'selected' : '' ?>>Offline</option>
                                            </select>
                                            <small>Ctrl+klik untuk pilih lebih dari satu</small>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" name="syarat_required[<?= $index ?>]" value="1" <?= ($item['is_required'] ?? true) ? 'checked' : '' ?>>
                                                Wajib
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Catatan (opsional)</label>
                                        <input type="text" name="syarat_note[]" class="form-control" value="<?= htmlspecialchars($item['note'] ?? '') ?>" placeholder="Catatan tambahan">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Daftar Berkas</label>
                                        <div class="files-container" data-index="<?= $index ?>">
                                            <?php if (!empty($item['files'])): ?>
                                                <?php foreach ($item['files'] as $findex => $file): ?>
                                                <div class="file-item">
                                                    <input type="text" name="syarat_files[<?= $index ?>][icon][]" class="form-control file-icon" value="<?= htmlspecialchars($file['icon'] ?? 'fa-file-alt') ?>" placeholder="Icon" style="width: 100px;">
                                                    <input type="text" name="syarat_files[<?= $index ?>][name][]" class="form-control file-name" value="<?= htmlspecialchars($file['name'] ?? '') ?>" placeholder="Nama berkas" style="flex: 1;">
                                                    <button type="button" class="btn-remove-file">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        <button type="button" class="btn-add-file" data-index="<?= $index ?>">
                                            <i class="fas fa-plus"></i> Tambah Berkas
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Template default 2 syarat -->
                            <div class="syarat-item" data-index="0">
                                <div class="syarat-header">
                                    <h4><i class="fas fa-edit"></i> Syarat 1</h4>
                                    <button type="button" class="btn-remove-syarat" data-index="0">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </div>
                                <div class="syarat-fields">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Icon (FontAwesome)</label>
                                            <input type="text" name="syarat_icon[]" class="form-control" value="fa-edit" placeholder="fa-edit">
                                        </div>
                                        <div class="form-group">
                                            <label>Judul</label>
                                            <input type="text" name="syarat_title[]" class="form-control" value="Mengisi Formulir Pendaftaran" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea name="syarat_desc[]" class="form-control" rows="2">Pendaftaran dapat dilakukan secara ONLINE melalui website resmi madrasah atau OFFLINE dengan datang langsung ke kantor madrasah.</textarea>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Options</label>
                                            <select name="syarat_options[0][]" class="form-control" multiple>
                                                <option value="online" selected>Online</option>
                                                <option value="offline" selected>Offline</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label><input type="checkbox" name="syarat_required[0]" value="1" checked> Wajib</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Catatan</label>
                                        <input type="text" name="syarat_note[]" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label>Daftar Berkas</label>
                                        <div class="files-container" data-index="0"></div>
                                        <button type="button" class="btn-add-file" data-index="0"><i class="fas fa-plus"></i> Tambah Berkas</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="syarat-item" data-index="1">
                                <div class="syarat-header">
                                    <h4><i class="fas fa-edit"></i> Syarat 2</h4>
                                    <button type="button" class="btn-remove-syarat" data-index="1">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </div>
                                <div class="syarat-fields">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Icon (FontAwesome)</label>
                                            <input type="text" name="syarat_icon[]" class="form-control" value="fa-folder-open" placeholder="fa-folder-open">
                                        </div>
                                        <div class="form-group">
                                            <label>Judul</label>
                                            <input type="text" name="syarat_title[]" class="form-control" value="Menyerahkan Berkas Pendaftaran" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea name="syarat_desc[]" class="form-control" rows="2">Berkas yang diperlukan untuk discan/difoto:</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Catatan</label>
                                        <input type="text" name="syarat_note[]" class="form-control" value="Berkas difoto/discan dalam kondisi jelas">
                                    </div>
                                    <div class="form-group">
                                        <label>Daftar Berkas</label>
                                        <div class="files-container" data-index="1">
                                            <div class="file-item">
                                                <input type="text" name="syarat_files[1][icon][]" class="form-control file-icon" value="fa-file-alt" style="width: 100px;">
                                                <input type="text" name="syarat_files[1][name][]" class="form-control file-name" value="Kartu Keluarga (KK)" style="flex: 1;">
                                                <button type="button" class="btn-remove-file"><i class="fas fa-times"></i></button>
                                            </div>
                                            <div class="file-item">
                                                <input type="text" name="syarat_files[1][icon][]" class="form-control file-icon" value="fa-baby-carriage" style="width: 100px;">
                                                <input type="text" name="syarat_files[1][name][]" class="form-control file-name" value="Akta Kelahiran" style="flex: 1;">
                                                <button type="button" class="btn-remove-file"><i class="fas fa-times"></i></button>
                                            </div>
                                            <div class="file-item">
                                                <input type="text" name="syarat_files[1][icon][]" class="form-control file-icon" value="fa-id-card" style="width: 100px;">
                                                <input type="text" name="syarat_files[1][name][]" class="form-control file-name" value="KTP (salah satu orang tua/wali)" style="flex: 1;">
                                                <button type="button" class="btn-remove-file"><i class="fas fa-times"></i></button>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-add-file" data-index="1"><i class="fas fa-plus"></i> Tambah Berkas</button>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><input type="checkbox" name="syarat_required[1]" value="1" checked> Wajib</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Link Pendaftaran & QR Code -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-link"></i>
                    <h3>Link Pendaftaran & QR Code</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Link Pendaftaran Online</label>
                        <input type="url" name="link_pendaftaran" class="form-control" value="<?= htmlspecialchars($ppdb['link_pendaftaran'] ?? '') ?>" placeholder="https://forms.google.com/...">
                    </div>

                    <div class="form-group">
                        <label>QR Code</label>
                        
                        <?php if (!empty($ppdb['qr_code'])): ?>
                        <div style="margin-bottom: 15px; padding: 10px; background: #f8fafc; border-radius: 8px;">
                            <img src="../../uploads/qr/<?= $ppdb['qr_code'] ?>" alt="QR Code" style="max-width: 100px; border-radius: 5px;">
                            <p style="margin-top: 5px;"><?= $ppdb['qr_code'] ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="file-upload" id="qrUploadArea">
                            <i class="fas fa-qrcode"></i>
                            <p>Klik untuk upload QR Code</p>
                            <small>Format: JPG, PNG (Maks. 1MB)</small>
                            <input type="file" name="qr_code" id="qr_code" accept="image/*" style="display: none;">
                        </div>
                        <div id="previewQrContainer" class="preview-container" style="display: none; margin-top: 15px;">
                            <img id="previewQr" src="#" alt="Preview QR" style="max-width: 150px; border-radius: 5px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kontak -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-phone-alt"></i>
                    <h3>Kontak Panitia</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nomor Telepon/WA</label>
                            <input type="text" name="kontak_telepon" class="form-control" value="<?= htmlspecialchars($ppdb['kontak_telepon'] ?? '') ?>" placeholder="Contoh: 0812-3456-7890">
                        </div>
                        <div class="form-group">
                            <label>Nama Kontak Person</label>
                            <input type="text" name="kontak_nama" class="form-control" value="<?= htmlspecialchars($ppdb['kontak_nama'] ?? '') ?>" placeholder="Contoh: Bpk. Ahmad">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan Penting -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i>
                    <h3>Catatan Penting</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan untuk informasi penting"><?= htmlspecialchars($ppdb['catatan'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-primary" id="btnSubmit">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="../dashboard.php" class="btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>