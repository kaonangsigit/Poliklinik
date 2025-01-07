<?php
include_once("../../config/koneksi.php");

if (!isset($_POST['id_periksa'])) {
    echo '<div class="alert alert-danger">ID Periksa tidak ditemukan!</div>';
    exit;
}

$id_periksa = $_POST['id_periksa'];
if (!is_numeric($id_periksa)) {
    echo '<div class="alert alert-danger">ID Periksa tidak valid!</div>';
    exit;
}

$query_detail = "SELECT 
    p.*,
    dp.keluhan,
    dp.no_antrian,
    pr.tgl_periksa,
    pr.catatan,
    pr.biaya_periksa,
    d.nama as nama_dokter,
    pol.nama_poli,
    GROUP_CONCAT(DISTINCT o.nama_obat SEPARATOR ', ') as obat_diberikan,
    COALESCE(SUM(o.harga), 0) as total_obat
FROM daftar_poli dp
JOIN pasien p ON dp.id_pasien = p.id
JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
JOIN dokter d ON jp.id_dokter = d.id
JOIN poli pol ON d.id_poli = pol.id
LEFT JOIN periksa pr ON dp.id = pr.id_daftar_poli
LEFT JOIN detail_periksa dpr ON pr.id = dpr.id_periksa
LEFT JOIN obat o ON dpr.id_obat = o.id
WHERE pr.id = ?
GROUP BY pr.id";

$stmt_detail = mysqli_prepare($koneksi, $query_detail);
mysqli_stmt_bind_param($stmt_detail, "i", $id_periksa);
mysqli_stmt_execute($stmt_detail);
$result_detail = mysqli_stmt_get_result($stmt_detail);

if ($detail = mysqli_fetch_assoc($result_detail)) {
    ?>
    <div class="row">
        <div class="col-md-6">
            <h5 class="mb-3">Informasi Pasien</h5>
            <table class="table table-borderless">
                <tr>
                    <td width="35%">No. RM</td>
                    <td>: <?php echo $detail['no_rm']; ?></td>
                </tr>
                <tr>
                    <td>Nama Pasien</td>
                    <td>: <?php echo $detail['nama']; ?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>: <?php echo $detail['alamat']; ?></td>
                </tr>
                <tr>
                    <td>No. KTP</td>
                    <td>: <?php echo $detail['no_ktp']; ?></td>
                </tr>
                <tr>
                    <td>No. HP</td>
                    <td>: <?php echo $detail['no_hp']; ?></td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h5 class="mb-3">Detail Pemeriksaan</h5>
            <table class="table table-borderless">
                <tr>
                    <td width="35%">Tanggal Periksa</td>
                    <td>: <?php 
                        $waktu_daftar = new DateTime($detail['tgl_daftar']); 
                        echo $waktu_daftar->format('d/m/Y H:i'); 
                    ?></td>
                </tr>
                <tr>
                    <td>Poli</td>
                    <td>: <?php echo $detail['nama_poli']; ?></td>
                </tr>
                <tr>
                    <td>Dokter</td>
                    <td>: <?php echo $detail['nama_dokter']; ?></td>
                </tr>
                <tr>
                    <td>Keluhan</td>
                    <td>: <?php echo $detail['keluhan']; ?></td>
                </tr>
                <tr>
                    <td>Catatan</td>
                    <td>: <?php echo $detail['catatan']; ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col-12">
            <h5 class="mb-3">Rincian Biaya</h5>
            <table class="table table-bordered">
                <tr>
                    <td width="35%">Biaya Pemeriksaan</td>
                    <td>: Rp <?php echo number_format($detail['biaya_periksa'], 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td>Obat yang diberikan</td>
                    <td>: <?php echo $detail['obat_diberikan'] ?: '-'; ?></td>
                </tr>
                <tr>
                    <td>Total Biaya Obat</td>
                    <td>: Rp <?php echo number_format($detail['total_obat'], 0, ',', '.'); ?></td>
                </tr>
                <tr class="table-primary">
                    <td><strong>Total Pembayaran</strong></td>
                    <td><strong>: Rp <?php echo number_format($detail['biaya_periksa'] + $detail['total_obat'], 0, ',', '.'); ?></strong></td>
                </tr>
            </table>
        </div>
    </div>
    <?php
} else {
    echo '<div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Data detail pemeriksaan tidak ditemukan.
          </div>';
}
?> 