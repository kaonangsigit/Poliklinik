<?php
include_once("../../config/koneksi.php");

// Set timezone untuk Indonesia
date_default_timezone_set('Asia/Jakarta');

if(isset($_POST['id_pasien'])) {
    $id_pasien = $_POST['id_pasien'];
    
    // Query untuk data pasien
    $query_pasien = "SELECT nama, no_rm FROM pasien WHERE id = ?";
    $stmt_pasien = mysqli_prepare($koneksi, $query_pasien);
    mysqli_stmt_bind_param($stmt_pasien, "i", $id_pasien);
    mysqli_stmt_execute($stmt_pasien);
    $result_pasien = mysqli_stmt_get_result($stmt_pasien);
    $pasien = mysqli_fetch_assoc($result_pasien);
    
    // Query untuk riwayat kunjungan
    $query_riwayat = "SELECT 
        dp.id,
        dp.created_at,
        dp.keluhan,
        pr.catatan,
        pr.biaya_periksa,
        GROUP_CONCAT(o.nama_obat) as obat_diberikan,
        pr.id as id_periksa,
        pr.id_daftar_poli,
        GROUP_CONCAT(o.id) as obat_ids
        FROM daftar_poli dp
        LEFT JOIN periksa pr ON dp.id = pr.id_daftar_poli
        LEFT JOIN detail_periksa det ON pr.id = det.id_periksa
        LEFT JOIN obat o ON det.id_obat = o.id
        WHERE dp.id_pasien = ?
        GROUP BY dp.id
        ORDER BY dp.created_at DESC";

    $stmt_riwayat = mysqli_prepare($koneksi, $query_riwayat);
    mysqli_stmt_bind_param($stmt_riwayat, "i", $id_pasien);
    mysqli_stmt_execute($stmt_riwayat);
    $result_riwayat = mysqli_stmt_get_result($stmt_riwayat);
?>
    <!-- Tampilan Modal -->
    <div class="mb-3">
        <h5>Data Pasien:</h5>
        <p class="mb-1">Nama: <?php echo $pasien['nama']; ?></p>
        <p>No. RM: <?php echo $pasien['no_rm']; ?></p>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="bg-light">
                <tr>
                    <th>Tanggal Periksa</th>
                    <th>Keluhan</th>
                    <th>Diagnosis/Catatan</th>
                    <th>Obat</th>
                    <th>Biaya</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result_riwayat)) { ?>
                <tr>
                    <td>
                        <?php 
                            $tanggal = new DateTime($row['created_at']);
                            echo $tanggal->format('d/m/Y');
                        ?>
                    </td>
                    <td><?php echo $row['keluhan']; ?></td>
                    <td><?php echo $row['catatan'] ?? '-'; ?></td>
                    <td><?php echo $row['obat_diberikan'] ?? '-'; ?></td>
                    <td>Rp <?php echo number_format($row['biaya_periksa'] ?? 0, 0, ',', '.'); ?></td>
                    <td>
                        <?php if($row['id_periksa']) { ?>
                            <button type="button" class="btn btn-warning btn-sm" 
                                    onclick="editRiwayat(<?php echo $row['id_periksa']; ?>, 
                                                       '<?php echo addslashes($row['catatan']); ?>', 
                                                       '<?php echo $row['obat_ids']; ?>',
                                                       <?php echo $id_pasien; ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php
}
?> 