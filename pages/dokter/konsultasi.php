<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_dokter = $_SESSION['user_id'];

// Query untuk mendapatkan daftar konsultasi
$query = "SELECT k.*, p.nama as nama_pasien, k.tgl_konsultasi,
          k.subject, k.pertanyaan, k.tanggapan
          FROM konsultasi k
          JOIN pasien p ON k.id_pasien = p.id
          WHERE k.id_dokter = ?
          ORDER BY k.tgl_konsultasi DESC";

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
                            <tr class="bg-primary text-white">
                                <th>Tanggal Konsultasi</th>
                                <th>Nama Pasien</th>
                                <th>Subject</th>
                                <th>Pertanyaan</th>
                                <th>Tanggapan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($row['tgl_konsultasi'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_pasien']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($row['pertanyaan']); ?></td>
                                    <td><?php echo $row['tanggapan'] ? htmlspecialchars($row['tanggapan']) : '-'; ?></td>
                                    <td>
                                        <?php if (!$row['tanggapan']) { ?>
                                            <button type="button" class="btn btn-primary btn-sm" 
                                                    onclick="showTanggapanModal(<?php echo $row['id']; ?>, 
                                                    '<?php echo addslashes($row['pertanyaan']); ?>')">
                                                <i class="fas fa-reply"></i> Tanggapi
                                            </button>
                                        <?php } else { ?>
                                            <button type="button" class="btn btn-warning btn-sm" 
                                                    onclick="showEditModal(<?php echo $row['id']; ?>, 
                                                    '<?php echo addslashes($row['tanggapan']); ?>')">
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
<div class="modal fade" id="modalTanggapan" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Berikan Tanggapan</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="formTanggapan">
                <div class="modal-body">
                    <input type="hidden" id="id_konsultasi" name="id_konsultasi">
                    <div class="form-group">
                        <label>Pertanyaan Pasien:</label>
                        <p id="pertanyaanText" class="font-weight-bold"></p>
                    </div>
                    <div class="form-group">
                        <label>Tanggapan:</label>
                        <textarea class="form-control" id="tanggapan" name="tanggapan" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showTanggapanModal(id, pertanyaan) {
    $('#id_konsultasi').val(id);
    $('#pertanyaanText').text(pertanyaan);
    $('#tanggapan').val('');
    $('#modalTanggapan').modal('show');
}

function showEditModal(id, tanggapan) {
    $('#id_konsultasi').val(id);
    $('#tanggapan').val(tanggapan);
    $('#modalTanggapan').modal('show');
}

$('#formTanggapan').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'konsultasi_handler.php',
        type: 'POST',
        data: {
            action: 'tanggapi',
            id_konsultasi: $('#id_konsultasi').val(),
            tanggapan: $('#tanggapan').val()
        },
        success: function(response) {
            if(response.success) {
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