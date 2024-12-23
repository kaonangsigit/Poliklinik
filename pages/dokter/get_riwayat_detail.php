<?php
include_once("../../config/koneksi.php");

if(isset($_POST['id_pasien'])) {
    $id_pasien = $_POST['id_pasien'];
    
    // Query untuk mengambil detail riwayat kunjungan
    $query = "SELECT 
                pr.tgl_periksa,
                dp.keluhan,
                pr.catatan,
                pr.biaya_periksa,
                GROUP_CONCAT(o.nama_obat SEPARATOR ', ') as obat_diberikan
              FROM daftar_poli dp
              JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
              LEFT JOIN periksa pr ON dp.id = pr.id_daftar_poli
              LEFT JOIN detail_periksa dpr ON pr.id = dpr.id_periksa
              LEFT JOIN obat o ON dpr.id_obat = o.id
              WHERE dp.id_pasien = ?
              GROUP BY pr.id
              ORDER BY pr.tgl_periksa DESC";
              
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_pasien);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // Ambil data pasien
    $query_pasien = "SELECT nama, no_rm FROM pasien WHERE id = ?";
    $stmt_pasien = mysqli_prepare($koneksi, $query_pasien);
    mysqli_stmt_bind_param($stmt_pasien, "i", $id_pasien);
    mysqli_stmt_execute($stmt_pasien);
    $pasien = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_pasien));
    ?>
    
    <div class="mb-3">
        <h5>Data Pasien:</h5>
        <p class="mb-1"><strong>Nama:</strong> <?php echo $pasien['nama']; ?></p>
        <p><strong>No. RM:</strong> <?php echo $pasien['no_rm']; ?></p>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tanggal Periksa</th>
                    <th>Keluhan</th>
                    <th>Diagnosis/Catatan</th>
                    <th>Obat</th>
                    <th>Biaya</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($row['tgl_periksa'])); ?></td>
                    <td><?php echo $row['keluhan']; ?></td>
                    <td><?php echo $row['catatan']; ?></td>
                    <td><?php echo $row['obat_diberikan'] ?: '-'; ?></td>
                    <td>Rp <?php echo number_format($row['biaya_periksa'], 0, ',', '.'); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
}
?> 