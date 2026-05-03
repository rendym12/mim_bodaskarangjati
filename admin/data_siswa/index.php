<?php
// File: admin/data_siswa/index.php

include "../includes/auth.php";

// ========== PROSES SIMPAN DATA ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $tahun_ajaran = mysqli_real_escape_string($conn, $_POST['tahun_ajaran']);
    
    $kelas_list = ['1', '2', '3', '4', '5', '6'];
    $perubahan_terjadi = false;
    
    foreach ($kelas_list as $kelas) {
        $laki_baru = (int) ($_POST['laki_' . $kelas] ?? 0);
        $perempuan_baru = (int) ($_POST['perempuan_' . $kelas] ?? 0);
        $total_baru = $laki_baru + $perempuan_baru;
        
        $check = mysqli_query($conn, "SELECT id FROM data_siswa WHERE kelas = '$kelas' AND tahun_ajaran = '$tahun_ajaran'");
        
        if (mysqli_num_rows($check) > 0) {
            mysqli_query($conn, "UPDATE data_siswa SET laki_laki = $laki_baru, perempuan = $perempuan_baru, total = $total_baru WHERE kelas = '$kelas' AND tahun_ajaran = '$tahun_ajaran'");
            $perubahan_terjadi = true;
        } else {
            if ($laki_baru > 0 || $perempuan_baru > 0) {
                mysqli_query($conn, "INSERT INTO data_siswa (kelas, laki_laki, perempuan, total, tahun_ajaran) VALUES ('$kelas', $laki_baru, $perempuan_baru, $total_baru, '$tahun_ajaran')");
                $perubahan_terjadi = true;
            }
        }
    }
    
    if ($perubahan_terjadi) {
        $_SESSION['success'] = "✅ Data siswa berhasil disimpan!";
    } else {
        $_SESSION['info'] = "ℹ️ Tidak ada perubahan data yang disimpan.";
    }
    
    header("Location: index.php?tahun=" . urlencode($tahun_ajaran));
    exit;
}

// ========== PROSES HAPUS ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $check = mysqli_query($conn, "SELECT * FROM data_siswa WHERE id = $id");
    
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $kelas = $row['kelas'];
        $tahun = $row['tahun_ajaran'];
        
        mysqli_query($conn, "DELETE FROM data_siswa WHERE id = $id");
        $_SESSION['success'] = "🗑️ Data siswa kelas $kelas tahun $tahun berhasil dihapus";
        
        header("Location: index.php?tahun=" . urlencode($tahun));
        exit;
    } else {
        $_SESSION['error'] = "❌ Data siswa tidak ditemukan";
    }
    header("Location: index.php");
    exit;
}

// ========== AMBIL SEMUA TAHUN AJARAN ==========
$tahun_list = [];
$result_tahun = mysqli_query($conn, "SELECT DISTINCT tahun_ajaran FROM data_siswa ORDER BY tahun_ajaran DESC");
if ($result_tahun) {
    while ($row = mysqli_fetch_assoc($result_tahun)) {
        $tahun_list[] = $row['tahun_ajaran'];
    }
}

// ========== TENTUKAN TAHUN AKTIF ==========
$tahun_ajaran_aktif = $_GET['tahun'] ?? null;

if (!$tahun_ajaran_aktif && !empty($tahun_list)) {
    $tahun_ajaran_aktif = $tahun_list[0];
}

if (!$tahun_ajaran_aktif) {
    $tahun_ajaran_aktif = date('Y') . '/' . (date('Y') + 1);
}

// ========== AMBIL DATA ==========
$data_siswa = [];
$result = mysqli_query($conn, "SELECT * FROM data_siswa WHERE tahun_ajaran = '$tahun_ajaran_aktif' ORDER BY CAST(kelas AS UNSIGNED) ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data_siswa[$row['kelas']] = $row;
    }
}

// ========== HITUNG TOTAL ==========
$total_laki_semua = 0;
$total_perempuan_semua = 0;
for ($i = 1; $i <= 6; $i++) {
    $kelas = (string)$i;
    if (isset($data_siswa[$kelas])) {
        $total_laki_semua += $data_siswa[$kelas]['laki_laki'];
        $total_perempuan_semua += $data_siswa[$kelas]['perempuan'];
    }
}
$total_semua = $total_laki_semua + $total_perempuan_semua;

include "../includes/header.php";
?>

<div class="content-wrapper">
    
    <div class="content-header">
        <h1><i class="fas fa-users"></i> Data Siswa</h1>
    </div>

    <div class="notification-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['info'])): ?>
            <div class="alert alert-info alert-dismissible">
                <i class="fas fa-info-circle"></i> <?= htmlspecialchars($_SESSION['info']) ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
            <?php unset($_SESSION['info']); ?>
        <?php endif; ?>
    </div>

    <!-- FILTER TAHUN AJARAN -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calendar-alt"></i> Filter Tahun Ajaran</h3>
        </div>
        <div class="card-body">
            <div class="tahun-filter">
                <form method="GET" action="" id="filterForm">
                    <select name="tahun" class="tahun-select" id="tahunSelect">
                        <?php if (empty($tahun_list)): ?>
                            <option value="<?= $tahun_ajaran_aktif ?>"><?= $tahun_ajaran_aktif ?></option>
                        <?php else: ?>
                            <?php foreach ($tahun_list as $tahun): ?>
                                <option value="<?= $tahun ?>" <?= $tahun == $tahun_ajaran_aktif ? 'selected' : '' ?>><?= $tahun ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tampilkan</button>
                    <?php if (!empty($tahun_list)): ?>
                        <span class="tahun-tersedia"><i class="fas fa-database"></i> <?= count($tahun_list) ?> tahun tersimpan</span>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- FORM INPUT -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-edit"></i> Input Data Siswa per Kelas</h3>
        </div>
        <div class="card-body">
            
            <form method="POST" action="" id="siswaForm">
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" id="tahunAjaranInput" value="<?= htmlspecialchars($tahun_ajaran_aktif) ?>" required placeholder="Contoh: 2025/2026">
                    <small>Isi tahun ajaran baru jika ingin membuat data baru</small>
                </div>
                
                <div class="table-wrapper">
                    <table class="data-table" id="dataSiswaTable">
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Laki-laki</th>
                                <th>Perempuan</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 1; $i <= 6; $i++): 
                                $kelas = (string)$i;
                                $laki = isset($data_siswa[$kelas]['laki_laki']) ? $data_siswa[$kelas]['laki_laki'] : 0;
                                $perempuan = isset($data_siswa[$kelas]['perempuan']) ? $data_siswa[$kelas]['perempuan'] : 0;
                                $data_id = isset($data_siswa[$kelas]['id']) ? $data_siswa[$kelas]['id'] : 0;
                            ?>
                            <tr data-kelas="<?= $kelas ?>">
                                <td class="kelas-label"><strong>Kelas <?= $kelas ?></strong></td>
                                <td class="input-cell">
                                    <input type="number" 
                                           name="laki_<?= $kelas ?>" 
                                           id="laki_<?= $kelas ?>" 
                                           class="input-siswa laki-input auto-calc-input" 
                                           value="<?= $laki ?>" 
                                           min="0" 
                                           step="1">
                                </td>
                                <td class="input-cell">
                                    <input type="number" 
                                           name="perempuan_<?= $kelas ?>" 
                                           id="perempuan_<?= $kelas ?>" 
                                           class="input-siswa perempuan-input auto-calc-input" 
                                           value="<?= $perempuan ?>" 
                                           min="0" 
                                           step="1">
                                </td>
                                <td class="total-kelas" id="total_<?= $kelas ?>"><strong><?= number_format($laki + $perempuan) ?></strong></td>
                                <td class="aksi-cell">
                                    <?php if($data_id): ?>
                                        <a href="?delete=<?= $data_id ?>" class="btn-delete" data-id="<?= $data_id ?>" data-kelas="<?= $kelas ?>" data-tahun="<?= $tahun_ajaran_aktif ?>"><i class="fas fa-trash"></i></a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background:#e6f0ff;font-weight:700;">
                                <td><strong>TOTAL SEMUA</strong></td>
                                <td><strong id="total_laki_semua"><?= number_format($total_laki_semua) ?></strong></td>
                                <td><strong id="total_perempuan_semua"><?= number_format($total_perempuan_semua) ?></strong></td>
                                <td><strong id="total_semua"><?= number_format($total_semua) ?></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="text-right">
                    <button type="button" id="previewBtn" class="btn-preview"><i class="fas fa-eye"></i> Preview Perubahan</button>
                    <button type="button" id="saveBtn" class="btn-simpan"><i class="fas fa-save"></i> Simpan Data</button>
                </div>
            </form>
            
            <div class="summary-card">
                <h3><i class="fas fa-school"></i> Total Seluruh Siswa</h3>
                <div class="total" id="summary_total"><?= number_format($total_semua) ?> Siswa</div>
                <small>Laki-laki: <span id="summary_laki"><?= number_format($total_laki_semua) ?></span> | Perempuan: <span id="summary_perempuan"><?= number_format($total_perempuan_semua) ?></span></small>
            </div>
            
        </div>
    </div>
</div>

<!-- MODAL PREVIEW -->
<div id="previewModal" class="modal-confirm" style="display: none;">
    <div class="modal-confirm-content">
        <div class="modal-confirm-header">
            <i class="fas fa-eye" style="font-size: 24px; color: #0B3D91;"></i>
            <h3>Preview Perubahan Data</h3>
        </div>
        <div class="modal-confirm-body">
            <div id="previewContent"></div>
        </div>
        <div class="modal-confirm-footer">
            <button type="button" class="btn-batal" onclick="window.closePreviewModal()"><i class="fas fa-times"></i> Tutup Preview</button>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI SIMPAN -->
<div id="confirmSaveModal" class="modal-confirm" style="display: none;">
    <div class="modal-confirm-content">
        <div class="modal-confirm-header">
            <i class="fas fa-question-circle" style="font-size: 24px; color: #FFD700;"></i>
            <h3>Konfirmasi Simpan Data</h3>
        </div>
        <div class="modal-confirm-body">
            <div id="confirmSaveContent"></div>
        </div>
        <div class="modal-confirm-footer">
            <button type="button" class="btn-batal" onclick="window.closeConfirmSaveModal()"><i class="fas fa-times"></i> Batal</button>
            <button type="button" class="btn-confirm" onclick="window.submitForm()"><i class="fas fa-check"></i> Ya, Simpan Data</button>
        </div>
    </div>
</div>

<script>
// Data awal untuk perbandingan
window.dataAwal = {
    <?php for ($i = 1; $i <= 6; $i++): 
        $kelas = (string)$i;
        $laki_awal = isset($data_siswa[$kelas]['laki_laki']) ? $data_siswa[$kelas]['laki_laki'] : 0;
        $perempuan_awal = isset($data_siswa[$kelas]['perempuan']) ? $data_siswa[$kelas]['perempuan'] : 0;
    ?>
    <?= $i ?>: { laki: <?= $laki_awal ?>, perempuan: <?= $perempuan_awal ?> },
    <?php endfor; ?>
};
console.log('✅ dataAwal loaded:', window.dataAwal);
</script>

<?php include "../includes/footer.php"; ?>