<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_dokter = $_SESSION['user_id'];

// Query untuk mendapatkan daftar konsultasi
$query = "SELECT k.*, p.nama as nama_pasien, k.tgl_konsultasi,
          k.subject, k.pertanyaan, k.jawaban
          FROM konsultasi k
          JOIN pasien p ON k.id_pasien = p.id
          WHERE k.id_dokter = ?
          ORDER BY k.tgl_konsultasi DESC";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_dokter);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Daftar Konsultasi Medis Dokter</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr class="bg-primary">
                                <th>Tanggal Konsultasi</th>
                                <th>Nama Pasien</th>
                                <th>Subject</th>
                                <th>Pertanyaan</th>
                                <th>Tanggapan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($row['tgl_konsultasi'])); ?></td>
                                    <td><?php echo $row['nama_pasien']; ?></td>
                                    <td><?php echo $row['subject']; ?></td>
                                    <td><?php echo $row['pertanyaan']; ?></td>
                                    <td><?php echo $row['jawaban'] ?: '-'; ?></td>
                                    <td>
                                        <?php if (empty($row['jawaban'])) { ?>
                                            <button type="button" class="btn btn-primary btn-sm" 
                                                    onclick="tanggapiKonsultasi(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-reply"></i> Tanggapi
                                            </button>
                                        <?php } else { ?>
                                            <button type="button" class="btn btn-info btn-sm"
                                                    onclick="editTanggapan(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        <?php } ?>
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

<!-- Modal Tanggapan -->
<div class="modal fade" id="modalTanggapan">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">Tanggapan Konsultasi</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formTanggapan">
                <div class="modal-body">
                    <input type="hidden" id="id_konsultasi" name="id_konsultasi">
                    <div class="form-group">
                        <label>Tanggapan:</label>
                        <textarea class="form-control" id="jawaban" name="jawaban" 
                                rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#example1").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[0, 'desc']]
    });
});

function tanggapiKonsultasi(id) {
    $('#id_konsultasi').val(id);
    $('#jawaban').val('');
    $('#modalTanggapan').modal('show');
}

function editTanggapan(id) {
    $.ajax({
        url: 'konsultasi_handler.php',
        type: 'POST',
        data: {
            action: 'get_tanggapan',
            id_konsultasi: id
        },
        success: function(response) {
            if(response.success) {
                $('#id_konsultasi').val(id);
                $('#jawaban').val(response.data.jawaban);
                $('#modalTanggapan').modal('show');
            }
        }
    });
}

$('#formTanggapan').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'konsultasi_handler.php',
        type: 'POST',
        data: {
            action: 'save_tanggapan',
            id_konsultasi: $('#id_konsultasi').val(),
            jawaban: $('#jawaban').val()
        },
        success: function(response) {
            if(response.success) {
                $('#modalTanggapan').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Tanggapan berhasil disimpan'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message
                });
            }
        }
    });
});
</script>

<?php include_once("layouts/footer.php"); ?> 