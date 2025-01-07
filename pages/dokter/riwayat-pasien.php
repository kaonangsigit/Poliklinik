<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

date_default_timezone_set('Asia/Jakarta');

$id_dokter = $_SESSION['user_id'];

// Set tanggal default ke hari ini jika tidak ada tanggal yang dipilih
$selected_date = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Modifikasi query untuk menampilkan waktu yang tepat
$query = "SELECT 
            p.nama as nama_pasien,
            p.no_rm,
            p.id as id_pasien,
            COUNT(DISTINCT pr.id) as jumlah_kunjungan,
            MAX(dp.created_at) as terakhir_periksa,
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
                                    <?php 
                                        if(!empty($row['terakhir_periksa'])) {
                                            $waktu = new DateTime($row['terakhir_periksa']);
                                            echo $waktu->format('d/m/Y');
                                        } else {
                                            echo "-";
                                        }
                                    ?>
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
<div class="modal fade" id="editRiwayatModal" tabindex="-1" role="dialog" aria-labelledby="editRiwayatModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRiwayatModalLabel">Edit Riwayat Pemeriksaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditRiwayat">
                    <input type="hidden" id="edit_id_periksa" name="id_periksa">
                    <input type="hidden" id="edit_id_pasien" name="id_pasien">
                    <div class="form-group">
                        <label for="edit_catatan">Catatan/Diagnosis</label>
                        <textarea class="form-control" id="edit_catatan" name="catatan" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="tanpa_obat" name="tanpa_obat">
                            <label class="custom-control-label" for="tanpa_obat">Tidak membutuhkan obat</label>
                        </div>
                        <label for="edit_obat">Obat</label>
                        <select class="form-control select2" id="edit_obat" name="obat[]" multiple="multiple">
                            <?php
                            $query_obat = "SELECT * FROM obat ORDER BY nama_obat";
                            $result_obat = mysqli_query($koneksi, $query_obat);
                            while($obat = mysqli_fetch_assoc($result_obat)) {
                                echo "<option value='".$obat['id']."'>".$obat['nama_obat']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnUpdateRiwayat">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
// Pastikan document ready
$(document).ready(function() {
    // Destroy Select2 jika sudah ada
    if ($('#edit_obat').hasClass('select2-hidden-accessible')) {
        $('#edit_obat').select2('destroy');
    }
    
    // Inisialisasi ulang Select2
    $('#edit_obat').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih obat',
        width: '100%',
        dropdownParent: $('#editRiwayatModal'),
        allowClear: true
    });
    
    // Handle modal hidden
    $('#editRiwayatModal').on('hidden.bs.modal', function() {
        $('#edit_obat').val(null).trigger('change');
    });
});

// Fungsi untuk edit riwayat
function editRiwayat(id_periksa, catatan, obat_ids, id_pasien) {
    // Reset form
    $('#formEditRiwayat')[0].reset();
    $('#edit_obat').val(null).trigger('change');
    
    // Set nilai form
    $('#edit_id_periksa').val(id_periksa);
    $('#edit_id_pasien').val(id_pasien);
    $('#edit_catatan').val(catatan);
    
    // Set nilai obat jika ada
    if (obat_ids && obat_ids !== 'null') {
        let obatArray = obat_ids.split(',')
            .map(id => id.trim())
            .filter(id => id !== '');
        $('#edit_obat').val(obatArray).trigger('change');
    }
    
    // Reset checkbox tanpa obat
    $('#tanpa_obat').prop('checked', false);
    $('#edit_obat').prop('disabled', false);
    
    // Tampilkan modal
    $('#editRiwayatModal').modal('show');
}

// Fungsi untuk refresh data riwayat
function refreshRiwayatData() {
    $.ajax({
        url: window.location.href,
        type: 'GET',
        success: function(response) {
            // Update tabel riwayat
            let newContent = $(response).find('#tabelRiwayat').html();
            $('#tabelRiwayat').html(newContent);
            
            // Jika modal detail sedang terbuka, refresh juga detailnya
            if ($('#detailModal').hasClass('show')) {
                let id_pasien = $('#edit_id_pasien').val();
                if (id_pasien) {
                    showRiwayatDetail(id_pasien);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error refreshing data:', error);
        }
    });
}

// Handle klik tombol simpan
$('#btnUpdateRiwayat').on('click', function(e) {
    e.preventDefault();
    
    let catatan = $('#edit_catatan').val().trim();
    let id_periksa = $('#edit_id_periksa').val();
    let id_pasien = $('#edit_id_pasien').val();
    let obat = $('#edit_obat').val() || [];
    let tanpa_obat = $('#tanpa_obat').is(':checked');
    
    if (!catatan) {
        alert('Catatan tidak boleh kosong!');
        return;
    }
    
    // Jika checkbox tanpa obat dicentang, kosongkan array obat
    if (tanpa_obat) {
        obat = [];
    }
    
    // Kirim data ke server
    $.ajax({
        url: 'update_periksa.php',
        type: 'POST',
        data: {
            id_periksa: id_periksa,
            catatan: catatan,
            obat: obat,
            tanpa_obat: tanpa_obat
        },
        success: function(response) {
            if(response.success) {
                $('#editRiwayatModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + (response.message || 'Terjadi kesalahan!'));
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax Error:', error);
            alert('Terjadi kesalahan pada server: ' + error);
        }
    });
});

// Tambahkan event handler untuk checkbox tanpa obat
$('#tanpa_obat').on('change', function() {
    let isChecked = $(this).is(':checked');
    if (isChecked) {
        // Disable dan kosongkan select obat
        $('#edit_obat').prop('disabled', true).val(null).trigger('change');
    } else {
        // Enable kembali select obat
        $('#edit_obat').prop('disabled', false);
    }
});

// Event handler untuk modal hidden
$('#editRiwayatModal').on('hidden.bs.modal', function () {
    // Reset form dan select2
    $('#formEditRiwayat')[0].reset();
    $('#edit_obat').val(null).trigger('change');
});

// Fungsi untuk menampilkan detail riwayat
function showRiwayatDetail(id_pasien) {
    if (!id_pasien) return;
    
    $('#riwayatDetail').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><div class="mt-2">Memuat data...</div></div>');
    
    $.ajax({
        url: 'get_riwayat_detail.php',
        type: 'POST',
        data: { id_pasien: id_pasien },
        success: function(response) {
            $('#riwayatDetail').html(response);
            $('#detailModal').modal('show');
        },
        error: function() {
            alert('Gagal memuat data riwayat!');
        }
    });
}
</script>

<?php include_once("layouts/footer.php"); ?> 