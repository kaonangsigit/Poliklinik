<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_pasien = $_SESSION['user_id'];

// Ambil riwayat pemeriksaan dengan JOIN yang benar
$query = "SELECT pr.id, pr.tgl_periksa, pr.catatan, pr.biaya_periksa,
          dp.keluhan, d.nama as nama_dokter, pol.nama_poli
          FROM daftar_poli dp
          JOIN periksa pr ON dp.id = pr.id_daftar_poli
          JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
          JOIN dokter d ON jp.id_dokter = d.id
          JOIN poli pol ON d.id_poli = pol.id
          WHERE dp.id_pasien = '$id_pasien'
          ORDER BY pr.tgl_periksa DESC";

$result = mysqli_query($koneksi, $query);

// Debug
if (!$result) {
    echo "Error: " . mysqli_error($koneksi);
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Riwayat Pemeriksaan</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Poli</th>
                                        <th>Dokter</th>
                                        <th>Keluhan</th>
                                        <th>Catatan</th>
                                        <th>Obat</th>
                                        <th>Biaya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (mysqli_num_rows($result) > 0) {
                                        $no = 1;
                                        while($row = mysqli_fetch_assoc($result)) {
                                            // Ambil daftar obat untuk setiap pemeriksaan
                                            $id_periksa = $row['id'];
                                            $query_obat = "SELECT o.nama_obat, o.harga
                                                         FROM detail_periksa dp
                                                         JOIN obat o ON dp.id_obat = o.id
                                                         WHERE dp.id_periksa = '$id_periksa'";
                                            $obat_result = mysqli_query($koneksi, $query_obat);
                                            
                                            // Debug obat
                                            if (!$obat_result) {
                                                echo "Error obat: " . mysqli_error($koneksi);
                                            }
                                            
                                            $obat_list = [];
                                            while($obat = mysqli_fetch_assoc($obat_result)) {
                                                $obat_list[] = $obat['nama_obat'] . 
                                                              " (Rp " . number_format($obat['harga'], 0, ',', '.') . ")";
                                            }
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['tgl_periksa'])); ?></td>
                                        <td><?php echo $row['nama_poli']; ?></td>
                                        <td><?php echo $row['nama_dokter']; ?></td>
                                        <td><?php echo $row['keluhan']; ?></td>
                                        <td><?php echo $row['catatan']; ?></td>
                                        <td><?php echo !empty($obat_list) ? implode("<br>", $obat_list) : '-'; ?></td>
                                        <td>Rp <?php echo number_format($row['biaya_periksa'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center'>Belum ada riwayat pemeriksaan</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once("layouts/footer.php"); ?> 