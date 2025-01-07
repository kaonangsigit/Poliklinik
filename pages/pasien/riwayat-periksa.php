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
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="tabelRiwayat">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center">No</th>
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
                                                $id_periksa = $row['id'];
                                                $query_obat = "SELECT o.nama_obat, o.harga
                                                             FROM detail_periksa dp
                                                             JOIN obat o ON dp.id_obat = o.id
                                                             WHERE dp.id_periksa = '$id_periksa'";
                                                $obat_result = mysqli_query($koneksi, $query_obat);
                                                $obat_list = [];
                                                while($obat = mysqli_fetch_assoc($obat_result)) {
                                                    $obat_list[] = $obat['nama_obat'] . " (Rp " . number_format($obat['harga'], 0, ',', '.') . ")";
                                                }
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($row['tgl_periksa'])); ?></td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?php echo $row['nama_poli']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $row['nama_dokter']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-link p-0" 
                                                        data-toggle="popover" 
                                                        data-trigger="hover"
                                                        title="Keluhan" 
                                                        data-content="<?php echo htmlspecialchars($row['keluhan']); ?>">
                                                    Lihat Keluhan
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-link p-0" 
                                                        data-toggle="popover" 
                                                        data-trigger="hover"
                                                        title="Catatan Dokter" 
                                                        data-content="<?php echo htmlspecialchars($row['catatan']); ?>">
                                                    Lihat Catatan
                                                </button>
                                            </td>
                                            <td>
                                                <?php if(!empty($obat_list)): ?>
                                                    <button type="button" class="btn btn-sm btn-link p-0" 
                                                            data-toggle="popover" 
                                                            data-trigger="hover"
                                                            data-html="true"
                                                            title="Daftar Obat" 
                                                            data-content="<?php echo htmlspecialchars(implode('<br>', $obat_list)); ?>">
                                                        Lihat Obat
                                                    </button>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
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
        </div>
    </section>
</div>

<style>
/* Custom styles untuk tabel responsif */
.table-responsive {
    border-radius: .5rem;
    box-shadow: 0 0 15px rgba(0,0,0,.1);
}

.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle !important;
}

/* Responsif text */
@media (max-width: 768px) {
    .table {
        font-size: 0.875rem;
    }
    
    .badge {
        font-size: 0.75rem;
    }
}

/* Hover effect */
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,.05);
}

/* Custom badge */
.badge-info {
    background-color: #17a2b8;
    padding: 0.5em 0.8em;
}

/* Popover custom */
.popover {
    max-width: 300px;
}

.popover-body {
    padding: 1rem;
    font-size: 0.875rem;
}
</style>

<script>
$(document).ready(function() {
    // Inisialisasi DataTables dengan konfigurasi responsif
    $('#tabelRiwayat').DataTable({
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        order: [[1, 'desc']], // Urutkan berdasarkan tanggal
        columnDefs: [
            { responsivePriority: 1, targets: [1,2,7] }, // Kolom yang prioritas ditampilkan
            { responsivePriority: 2, targets: [3,4] },
            { responsivePriority: 3, targets: [5,6] }
        ]
    });

    // Inisialisasi Popover
    $('[data-toggle="popover"]').popover({
        container: 'body',
        placement: 'auto'
    });

    // Tutup popover saat klik di luar
    $('body').on('click', function (e) {
        if ($(e.target).data('toggle') !== 'popover' 
            && $(e.target).parents('.popover.in').length === 0) { 
            $('[data-toggle="popover"]').popover('hide');
        }
    });
});
</script>

<?php include_once("layouts/footer.php"); ?> 