<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_dokter = $_SESSION['user_id'];

// Proses update status jadwal
if(isset($_POST['update_status'])) {
    $jadwal_id = $_POST['jadwal_id'];
    $status = $_POST['status'];
    
    $query_update = "UPDATE jadwal_periksa SET status = ? WHERE id = ? AND id_dokter = ?";
    $stmt = mysqli_prepare($koneksi, $query_update);
    mysqli_stmt_bind_param($stmt, "sii", $status, $jadwal_id, $id_dokter);
    
    if(mysqli_stmt_execute($stmt)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Status jadwal berhasil diperbarui!',
                showConfirmButton: false,
                timer: 1500
            });
        </script>";
    }
}

// Proses tambah jadwal
if(isset($_POST['submit'])) {
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    
    // Validasi jam
    if($jam_mulai >= $jam_selesai) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Jam selesai harus lebih besar dari jam mulai!',
                showConfirmButton: true
            });
        </script>";
        exit;
    }
    
    // Cek overlap jadwal
    $query_check = "SELECT * FROM jadwal_periksa 
                   WHERE id_dokter = ? 
                   AND hari = ? 
                   AND status = 'aktif'
                   AND ((jam_mulai BETWEEN ? AND ?) 
                   OR (jam_selesai BETWEEN ? AND ?))";
    
    $stmt_check = mysqli_prepare($koneksi, $query_check);
    mysqli_stmt_bind_param($stmt_check, "isssss", 
                         $id_dokter, $hari, $jam_mulai, $jam_selesai, 
                         $jam_mulai, $jam_selesai);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    
    if(mysqli_num_rows($result_check) > 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Jadwal bertabrakan dengan jadwal aktif yang sudah ada!',
                showConfirmButton: true
            });
        </script>";
    } else {
        try {
            // Insert jadwal baru
            $query = "INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai, status) 
                     VALUES (?, ?, ?, ?, 'aktif')";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "isss", $id_dokter, $hari, $jam_mulai, $jam_selesai);
            
            if(mysqli_stmt_execute($stmt)) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Jadwal berhasil ditambahkan!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = 'jadwal-periksa.php';
                    });
                </script>";
            } else {
                throw new Exception(mysqli_error($koneksi));
            }
        } catch (Exception $e) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menambahkan jadwal: " . $e->getMessage() . "',
                    showConfirmButton: true
                });
            </script>";
        }
    }
}

// Ambil jadwal dokter
$query = "SELECT * FROM jadwal_periksa 
          WHERE id_dokter = ? 
          ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_dokter);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!-- Tampilan tabel -->
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
            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary btn-tambah">
                        <i class="fas fa-plus"></i> Tambah Jadwal
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
                                    <select class="form-control status-jadwal" 
                                            data-id="<?php echo $row['id']; ?>">
                                        <option value="aktif" <?php echo $row['status'] == 'aktif' ? 'selected' : ''; ?>>
                                            Aktif
                                        </option>
                                        <option value="tidak aktif" <?php echo $row['status'] == 'tidak aktif' ? 'selected' : ''; ?>>
                                            Tidak Aktif
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm" 
                                            onclick='editJadwal(<?php echo json_encode($row); ?>)'>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="hapus-jadwal.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
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

<!-- Form Modal -->
<div class="modal fade" id="modalJadwal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="formJadwal">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Jadwal Periksa</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Hari</label>
                        <select class="form-control" name="hari" required>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jam Mulai</label>
                        <input type="time" class="form-control" name="jam_mulai" required>
                    </div>
                    <div class="form-group">
                        <label>Jam Selesai</label>
                        <input type="time" class="form-control" name="jam_selesai" required>
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
// Update status jadwal
$('.status-jadwal').change(function() {
    const jadwal_id = $(this).data('id');
    const status = $(this).val();
    
    $.ajax({
        url: 'jadwal-periksa.php',
        type: 'POST',
        data: {
            update_status: true,
            jadwal_id: jadwal_id,
            status: status
        },
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Status jadwal berhasil diperbarui!',
                showConfirmButton: false,
                timer: 1500
            });
        }
    });
});

// Fungsi edit jadwal
function editJadwal(data) {
    $('#modalJadwal').modal('show');
    $('#jadwal_id').val(data.id);
    $('#hari').val(data.hari);
    $('#jam_mulai').val(data.jam_mulai);
    $('#jam_selesai').val(data.jam_selesai);
    $('.modal-title').text('Edit Jadwal Periksa');
}

$(document).ready(function() {
    // Reset form saat modal dibuka
    $('.btn-tambah').click(function() {
        $('#modalJadwal').modal('show');
        $('#formJadwal')[0].reset();
    });

    // Validasi form sebelum submit
    $('#formJadwal').on('submit', function(e) {
        const jamMulai = $('input[name="jam_mulai"]').val();
        const jamSelesai = $('input[name="jam_selesai"]').val();
        
        if (jamMulai >= jamSelesai) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Jam selesai harus lebih besar dari jam mulai!',
                showConfirmButton: true
            });
            return false;
        }
    });
});
</script>

<?php include_once("layouts/footer.php"); ?> 