<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_dokter = $_SESSION['user_id'];

// Set tanggal default ke hari ini jika tidak ada tanggal yang dipilih
$selected_date = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Modifikasi query untuk mengelompokkan berdasarkan pasien
$query = "SELECT 
            p.nama as nama_pasien,
            p.no_rm,
            p.id as id_pasien,
            COUNT(DISTINCT pr.id) as jumlah_kunjungan,
            MAX(COALESCE(pr.tgl_periksa, dp.created_at)) as terakhir_periksa,
            dp.status
          FROM daftar_poli dp
          JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
          JOIN pasien p ON dp.id_pasien = p.id
          LEFT JOIN periksa pr ON dp.id = pr.id_daftar_poli
          WHERE jp.id_dokter = ?
          GROUP BY p.id
          ORDER BY terakhir_periksa DESC";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_dokter);
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
                    <h3 class="card-title">Riwayat Pasien</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover" id="tabelRiwayat">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">No RM</th>
                                <th width="30%">Nama Pasien</th>
                                <th width="15%">Jumlah Kunjungan</th>
                                <th width="20%">Terakhir Periksa</th>
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
                                <td class="text-center"><?php echo $row['jumlah_kunjungan']; ?> kali</td>
                                <td class="text-center">
                                    <?php echo date('d/m/Y', strtotime($row['terakhir_periksa'])); ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-info btn-sm" 
                                            onclick="showRiwayatDetail(<?php echo $row['id_pasien']; ?>)">
                                        <i class="fas fa-history"></i> Riwayat
                                    </button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Detail Riwayat -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">
                    <i class="fas fa-history mr-2"></i>Riwayat Kunjungan Pasien
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="riwayatDetail"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Riwayat -->
<div class="modal fade" id="editRiwayatModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Edit Riwayat Pemeriksaan
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formEditRiwayat">
                <div class="modal-body">
                    <input type="hidden" name="id_periksa" id="edit_id_periksa">
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea class="form-control" name="catatan" id="edit_catatan" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Obat</label>
                        <select class="form-control select2" name="obat[]" id="edit_obat" multiple required>
                            <?php
                            $query_obat = "SELECT * FROM obat ORDER BY nama_obat";
                            $result_obat = mysqli_query($koneksi, $query_obat);
                            while($obat = mysqli_fetch_assoc($result_obat)) {
                                echo "<option value='".$obat['id']."'>".$obat['nama_obat']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRiwayatDetail(id_pasien) {
    $.ajax({
        url: 'get_riwayat_detail.php',
        type: 'POST',
        data: {id_pasien: id_pasien},
        success: function(response) {
            $('#riwayatDetail').html(response);
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

// Perbaikan inisialisasi DataTable
var tabelRiwayat;
$(document).ready(function() {
    // Destroy jika sudah ada
    if ($.fn.DataTable.isDataTable('#tabelRiwayat')) {
        $('#tabelRiwayat').DataTable().destroy();
    }
    
    // Inisialisasi baru
    tabelRiwayat = $('#tabelRiwayat').DataTable({
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "order": [[4, "desc"]], // Urutkan berdasarkan tanggal terakhir periksa
        "columnDefs": [
            {
                "targets": [0, 3, 4, 5], // Kolom nomor, jumlah kunjungan, tanggal, dan aksi
                "orderable": false
            }
        ]
    });
});

// Refresh DataTable saat modal ditutup (jika diperlukan)
$('#detailModal').on('hidden.bs.modal', function () {
    if (tabelRiwayat) {
        tabelRiwayat.ajax.reload(null, false);
    }
});

// Fungsi untuk menampilkan modal edit
function editRiwayat(id_periksa) {
    $.ajax({
        url: 'get_periksa_detail.php',
        type: 'POST',
        data: {id_periksa: id_periksa},
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#edit_id_periksa').val(response.data.id);
                $('#edit_catatan').val(response.data.catatan);
                
                // Set nilai obat yang dipilih
                $('#edit_obat').val(response.data.obat).trigger('change');
                
                $('#editRiwayatModal').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat mengambil data!'
            });
        }
    });
}

// Handle submit form edit
$('#formEditRiwayat').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'update_periksa.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data pemeriksaan berhasil diupdate!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    $('#editRiwayatModal').modal('hide');
                    // Refresh detail riwayat
                    showRiwayatDetail($('#edit_id_pasien').val());
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menyimpan data!'
            });
        }
    });
});

// Inisialisasi Select2 untuk multiple select obat
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih obat',
        width: '100%'
    });
});
</script>

<?php include_once("layouts/footer.php"); ?> 