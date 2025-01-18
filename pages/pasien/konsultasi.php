<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_pasien = $_SESSION['user_id'];

// Query untuk mengambil riwayat konsultasi
$query = "SELECT k.*, d.nama as nama_dokter, k.tgl_konsultasi,
          k.subject, k.pertanyaan, k.tanggapan
          FROM konsultasi k
          JOIN dokter d ON k.id_dokter = d.id
          WHERE k.id_pasien = ?
          ORDER BY k.tgl_konsultasi DESC";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_pasien);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Daftar Konsultasi Medis Pasien</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary float-right" 
                            data-toggle="modal" data-target="#modalTambahKonsultasi">
                        Tambah
                    </button>
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th>Tanggal Konsultasi</th>
                                <th>Nama Dokter</th>
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
                                    <td><?php echo htmlspecialchars($row['nama_dokter']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($row['pertanyaan']); ?></td>
                                    <td><?php echo $row['tanggapan'] ? htmlspecialchars($row['tanggapan']) : '-'; ?></td>
                                    <td>
                                        <?php if (!$row['tanggapan']) { ?>
                                            <button class="btn btn-warning btn-sm" onclick="editKonsultasi(<?php echo $row['id']; ?>)">
                                                Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="hapusKonsultasi(<?php echo $row['id']; ?>)">
                                                Hapus
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

<!-- Modal Tambah Konsultasi -->
<div class="modal fade" id="modalTambahKonsultasi">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">Tambah Konsultasi</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="formTambahKonsultasi">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Dokter</label>
                        <select class="form-control select2" id="id_dokter" name="id_dokter" required>
                            <option value="">-- Pilih Dokter --</option>
                            <?php 
                            $query_dokter = "SELECT d.*, p.nama_poli FROM dokter d 
                                           JOIN poli p ON d.id_poli = p.id 
                                           ORDER BY p.nama_poli, d.nama";
                            $result_dokter = mysqli_query($koneksi, $query_dokter);
                            while($dokter = mysqli_fetch_assoc($result_dokter)) { 
                            ?>
                                <option value="<?php echo $dokter['id']; ?>">
                                    <?php echo $dokter['nama'] . ' - ' . $dokter['nama_poli']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label>Pertanyaan</label>
                        <textarea class="form-control" id="pertanyaan" name="pertanyaan" rows="4" required></textarea>
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
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    $('#example1').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[0, 'desc']]
    });

    $('#formTambahKonsultasi').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'konsultasi_handler.php',
            type: 'POST',
            data: {
                action: 'tambah_konsultasi',
                id_dokter: $('#id_dokter').val(),
                subject: $('#subject').val(),
                pertanyaan: $('#pertanyaan').val()
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Konsultasi berhasil ditambahkan'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            }
        });
    });
});

function hapusKonsultasi(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data konsultasi akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'konsultasi_handler.php',
                type: 'POST',
                data: {
                    action: 'hapus_konsultasi',
                    id: id
                },
                success: function(response) {
                    if(response.success) {
                        Swal.fire('Terhapus!', 'Data konsultasi berhasil dihapus', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                }
            });
        }
    });
}
</script>

<?php include_once("layouts/footer.php"); ?>