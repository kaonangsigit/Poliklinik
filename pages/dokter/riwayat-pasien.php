<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_dokter = $_SESSION['user_id'];

// Set tanggal default ke hari ini jika tidak ada tanggal yang dipilih
$selected_date = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Query untuk mengambil riwayat pasien berdasarkan tanggal
$query = "SELECT 
            p.nama as nama_pasien,
            p.no_rm,
            dp.keluhan,
            dp.no_antrian,
            dp.status,
            pr.id as id_periksa,
            pr.tgl_periksa,
            pr.catatan,
            pr.biaya_periksa,
            150000 as jasa_dokter,
            COALESCE(SUM(o.harga), 0) as total_obat
          FROM daftar_poli dp
          JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
          JOIN pasien p ON dp.id_pasien = p.id
          LEFT JOIN periksa pr ON dp.id = pr.id_daftar_poli
          LEFT JOIN detail_periksa dpr ON pr.id = dpr.id_periksa
          LEFT JOIN obat o ON dpr.id_obat = o.id
          WHERE jp.id_dokter = ?
          AND DATE(COALESCE(pr.tgl_periksa, dp.created_at)) = ?
          GROUP BY dp.id
          ORDER BY dp.status ASC, pr.tgl_periksa DESC";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "is", $id_dokter, $selected_date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Riwayat Pasien</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Form Filter Tanggal -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="" class="form-inline">
                        <div class="form-group mr-2">
                            <label for="tanggal" class="mr-2">Pilih Tanggal:</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                   value="<?php echo $selected_date; ?>" max="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabel Riwayat -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Pasien Tanggal: <?php echo date('d/m/Y', strtotime($selected_date)); ?></h3>
                </div>
                <div class="card-body">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                    <table id="tabelRiwayat" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">No RM</th>
                                <th width="45%">Nama Pasien</th>
                                <th width="15%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td><?php echo $row['no_rm']; ?></td>
                                <td><?php echo $row['nama_pasien']; ?></td>
                                <td class="text-center">
                                    <span class="badge badge-<?php 
                                        echo $row['status'] == 'menunggu' ? 'warning' : 
                                            ($row['status'] == 'diperiksa' ? 'primary' : 'success'); 
                                    ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-info btn-sm" 
                                            onclick="showDetail(<?php echo $row['id_periksa']; ?>)">
                                        <i class="fas fa-info-circle"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Tidak ada riwayat pasien untuk tanggal yang dipilih.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Container -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="detailModalLabel">
                    <i class="fas fa-info-circle mr-2"></i>Detail Pasien
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Data detail akan dimuat di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#tabelRiwayat')) {
        $('#tabelRiwayat').DataTable().destroy();
    }
    
    $('#tabelRiwayat').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "order": [[1, 'asc']], // Urutkan berdasarkan waktu
        "columnDefs": [
            {
                "targets": [4, 5, 6],
                "orderable": false
            }
        ]
    });
});

function showDetail(id_periksa) {
    $.ajax({
        url: 'get_detail_pasien.php',
        type: 'POST',
        data: {id_periksa: id_periksa},
        success: function(response) {
            $('#detailModal .modal-body').html(response);
            $('#detailModal').modal('show');
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan saat mengambil data!'
            });
        }
    });
}

// Tambahkan ini untuk membersihkan modal saat ditutup
$('#detailModal').on('hidden.bs.modal', function () {
    $(this).find('.modal-body').html('');
});
</script>

<?php include_once("layouts/footer.php"); ?> 