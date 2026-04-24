<?php
// File: admin/data_siswa/index.php

include "../includes/auth.php";

// ========== PROSES SIMPAN DATA ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $tahun_ajaran = mysqli_real_escape_string($conn, $_POST['tahun_ajaran']);
    
    $kelas_list = ['1', '2', '3', '4', '5', '6'];
    
    foreach ($kelas_list as $kelas) {
        $laki = (int) ($_POST['laki_' . $kelas] ?? 0);
        $perempuan = (int) ($_POST['perempuan_' . $kelas] ?? 0);
        $total = $laki + $perempuan;
        
        $check = mysqli_query($conn, "SELECT id FROM data_siswa WHERE kelas = '$kelas' AND tahun_ajaran = '$tahun_ajaran'");
        
        if (mysqli_num_rows($check) > 0) {
            mysqli_query($conn, "UPDATE data_siswa SET laki_laki = $laki, perempuan = $perempuan, total = $total WHERE kelas = '$kelas' AND tahun_ajaran = '$tahun_ajaran'");
        } else {
            mysqli_query($conn, "INSERT INTO data_siswa (kelas, laki_laki, perempuan, total, tahun_ajaran) VALUES ('$kelas', $laki, $perempuan, $total, '$tahun_ajaran')");
        }
    }
    
    $_SESSION['success'] = "Data siswa berhasil disimpan!";
    
    // 🔥 REDIRECT KE TAHUN YANG SAMA (bukan pindah ke tahun lain)
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
        $_SESSION['success'] = "Data siswa kelas $kelas tahun $tahun berhasil dihapus";
        
        // Redirect kembali ke tahun yang sama
        header("Location: index.php?tahun=" . urlencode($tahun));
        exit;
    } else {
        $_SESSION['error'] = "Data siswa tidak ditemukan";
    }
    header("Location: index.php");
    exit;
}

// ========== AMBIL SEMUA TAHUN AJARAN UNTUK DROPDOWN ==========
$tahun_list = [];
$result_tahun = mysqli_query($conn, "SELECT DISTINCT tahun_ajaran FROM data_siswa ORDER BY tahun_ajaran DESC");
if ($result_tahun) {
    while ($row = mysqli_fetch_assoc($result_tahun)) {
        $tahun_list[] = $row['tahun_ajaran'];
    }
}

// ========== TENTUKAN TAHUN AKTIF ==========
// Prioritas: 1. GET['tahun']  2. Tahun terbaru dari DB  3. Tahun default
$tahun_ajaran_aktif = $_GET['tahun'] ?? null;

// Jika tidak ada parameter tahun, ambil tahun terbaru dari database
if (!$tahun_ajaran_aktif && !empty($tahun_list)) {
    $tahun_ajaran_aktif = $tahun_list[0];
}

// Jika masih kosong, gunakan tahun default
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
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
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

    <!-- FILTER TAHUN AJARAN DENGAN DROPDOWN -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calendar-alt"></i> Filter Tahun Ajaran</h3>
        </div>
        <div class="card-body">
            <div class="tahun-filter">
                <form method="GET" action="" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <select name="tahun" class="tahun-select" id="tahunSelect" style="padding: 10px 15px; border-radius: 30px; border: 1px solid #e2e8f0; background: white;">
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
                                $laki = $data_siswa[$kelas]['laki_laki'] ?? 0;
                                $perempuan = $data_siswa[$kelas]['perempuan'] ?? 0;
                                $data_id = $data_siswa[$kelas]['id'] ?? 0;
                            ?>
                            <tr data-kelas="<?= $kelas ?>">
                                <td class="kelas-label"><strong>Kelas <?= $kelas ?></strong></td>
                                <td>
                                    <input type="number" name="laki_<?= $kelas ?>" id="laki_<?= $kelas ?>" class="input-siswa laki-input" data-kelas="<?= $kelas ?>" value="<?= $laki ?>" min="0">
                                 </td>
                                <td>
                                    <input type="number" name="perempuan_<?= $kelas ?>" id="perempuan_<?= $kelas ?>" class="input-siswa perempuan-input" data-kelas="<?= $kelas ?>" value="<?= $perempuan ?>" min="0">
                                 </td>
                                <td class="total-kelas" id="total_<?= $kelas ?>"><strong><?= number_format($laki + $perempuan) ?></strong></td>
                                <td>
                                    <?php if($data_id): ?>
                                        <a href="?delete=<?= $data_id ?>" class="btn-delete" data-id="<?= $data_id ?>" data-kelas="<?= $kelas ?>" data-tahun="<?= $tahun_ajaran_aktif ?>" onclick="return confirm('Hapus data kelas <?= $kelas ?>?')"><i class="fas fa-trash"></i></a>
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
                    <button type="submit" name="simpan" class="btn-simpan"><i class="fas fa-save"></i> Simpan Data</button>
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

<!-- SCRIPT AUTO-CALCULATE - VERSI AGGRESIF -->
<script>
// Fungsi untuk format angka
function formatAngka(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Fungsi hitung total
function hitungTotal() {
    console.log('🔄 hitungTotal dipanggil');
    
    let totalLaki = 0;
    let totalPerempuan = 0;
    
    for (let i = 1; i <= 6; i++) {
        let inputLaki = document.getElementById('laki_' + i);
        let inputPerempuan = document.getElementById('perempuan_' + i);
        let spanTotal = document.getElementById('total_' + i);
        
        if (inputLaki && inputPerempuan && spanTotal) {
            let nilaiLaki = parseInt(inputLaki.value) || 0;
            let nilaiPerempuan = parseInt(inputPerempuan.value) || 0;
            let jumlah = nilaiLaki + nilaiPerempuan;
            
            spanTotal.innerHTML = '<strong>' + formatAngka(jumlah) + '</strong>';
            
            totalLaki += nilaiLaki;
            totalPerempuan += nilaiPerempuan;
        }
    }
    
    let totalSemua = totalLaki + totalPerempuan;
    
    // Update footer
    let elTotalLaki = document.getElementById('total_laki_semua');
    let elTotalPerempuan = document.getElementById('total_perempuan_semua');
    let elTotalSemua = document.getElementById('total_semua');
    
    if (elTotalLaki) elTotalLaki.innerHTML = formatAngka(totalLaki);
    if (elTotalPerempuan) elTotalPerempuan.innerHTML = formatAngka(totalPerempuan);
    if (elTotalSemua) elTotalSemua.innerHTML = formatAngka(totalSemua);
    
    // Update summary card
    let elSummaryTotal = document.getElementById('summary_total');
    let elSummaryLaki = document.getElementById('summary_laki');
    let elSummaryPerempuan = document.getElementById('summary_perempuan');
    
    if (elSummaryTotal) elSummaryTotal.innerHTML = formatAngka(totalSemua) + ' Siswa';
    if (elSummaryLaki) elSummaryLaki.innerHTML = formatAngka(totalLaki);
    if (elSummaryPerempuan) elSummaryPerempuan.innerHTML = formatAngka(totalPerempuan);
    
    console.log('Total Laki: ' + totalLaki + ', Perempuan: ' + totalPerempuan + ', Total: ' + totalSemua);
}

// ========== CARA 1: Event Listener Langsung ==========
document.querySelectorAll('#dataSiswaTable input[type="number"]').forEach(function(input) {
    input.addEventListener('input', hitungTotal);
    input.addEventListener('change', hitungTotal);
    input.addEventListener('keyup', hitungTotal);
    input.addEventListener('click', hitungTotal);
});

// ========== CARA 2: Pantau perubahan setiap 0.3 detik (PASTI BERHASIL) ==========
// Simpan nilai sebelumnya
let nilaiSebelumnya = {};

for (let i = 1; i <= 6; i++) {
    let inputLaki = document.getElementById('laki_' + i);
    let inputPerempuan = document.getElementById('perempuan_' + i);
    if (inputLaki) nilaiSebelumnya['laki_' + i] = inputLaki.value;
    if (inputPerempuan) nilaiSebelumnya['perempuan_' + i] = inputPerempuan.value;
}

// Cek perubahan setiap 0.3 detik
setInterval(function() {
    let adaPerubahan = false;
    
    for (let i = 1; i <= 6; i++) {
        let inputLaki = document.getElementById('laki_' + i);
        let inputPerempuan = document.getElementById('perempuan_' + i);
        
        if (inputLaki && nilaiSebelumnya['laki_' + i] != inputLaki.value) {
            nilaiSebelumnya['laki_' + i] = inputLaki.value;
            adaPerubahan = true;
        }
        
        if (inputPerempuan && nilaiSebelumnya['perempuan_' + i] != inputPerempuan.value) {
            nilaiSebelumnya['perempuan_' + i] = inputPerempuan.value;
            adaPerubahan = true;
        }
    }
    
    if (adaPerubahan) {
        console.log('📊 Deteksi perubahan via interval');
        hitungTotal();
    }
}, 300);

// Panggil pertama kali
hitungTotal();

console.log('✅ Auto-calculate Data Siswa AKTIF (dengan interval monitor)');
</script>

<?php include "../includes/footer.php"; ?>