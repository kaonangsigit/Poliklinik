<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_dokter = $_SESSION['user_id'];

// Proses tambah/edit jadwal
if(isset($_POST['submit'])) {
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    
    if(isset($_POST['id'])) {
        // Update
        $id = $_POST['id'];
        $query = "UPDATE jadwal_periksa 
                 SET hari = '$hari', jam_mulai = '$jam_mulai', jam_selesai = '$jam_selesai' 
                 WHERE id = '$id'";
    } else {
        // Insert
        $query = "INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai, status) 
                 VALUES ('$id_dokter', '$hari', '$jam_mulai', '$jam_selesai', 'aktif')";
    }
    
    if(mysqli_query($koneksi, $query)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Jadwal berhasil disimpan!',
                showConfirmButton: false,
                timer: 1500
            });
        </script>";
    }
}

// Ambil jadwal dokter
$query = "SELECT * FROM jadwal_periksa WHERE id_dokter = '$id_dokter'";
$result = mysqli_query($koneksi, $query);
?>

<!-- Modal Tambah/Edit Jadwal -->
<div class="modal fade" id="modalJadwal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Jadwal Periksa</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="jadwal_id">
                    <div class="form-group">
                        <label>Hari</label>
                        <select class="form-control" name="hari" id="hari" required>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jam Mulai</label>
                        <input type="time" class="form-control" name="jam_mulai" id="jam_mulai" required>
                    </div>
                    <div class="form-group">
                        <label>Jam Selesai</label>
                        <input type="time" class="form-control" name="jam_selesai" id="jam_selesai" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fungsi untuk menampilkan data di modal edit
function editJadwal(data) {
    $('#modalJadwal').modal('show');
    $('#jadwal_id').val(data.id);
    $('#hari').val(data.hari);
    $('#jam_mulai').val(data.jam_mulai);
    $('#jam_selesai').val(data.jam_selesai);
    $('.modal-title').text('Edit Jadwal Periksa');
}

// Reset form saat modal tambah dibuka
$(document).ready(function() {
    $('.btn-tambah').click(function() {
        $('#modalJadwal').modal('show');
        $('#jadwal_id').val('');
        $('#hari').val('Senin');
        $('#jam_mulai').val('');
        $('#jam_selesai').val('');
        $('.modal-title').text('Tambah Jadwal Periksa');
    });
});
</script>

<!-- Tabel Jadwal -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Jadwal Periksa</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary btn-tambah">
                                Tambah Jadwal
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Hari</th>
                                        <th>Jam Mulai</th>
                                        <th>Jam Selesai</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while($row = mysqli_fetch_assoc($result)) { 
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['hari']; ?></td>
                                        <td><?php echo substr($row['jam_mulai'], 0, 5); ?></td>
                                        <td><?php echo substr($row['jam_selesai'], 0, 5); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $row['status'] == 'aktif' ? 'success' : 'danger'; ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" 
                                                    onclick='editJadwal(<?php echo json_encode($row); ?>)'>
                                                Edit
                                            </button>
                                            <a href="hapus-jadwal.php?id=<?php echo $row['id']; ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Yakin hapus jadwal ini?')">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
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