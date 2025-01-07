<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_dokter = $_SESSION['user_id'];

// Proses tambah jadwal
if(isset($_POST['submit'])) {
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $id_dokter = $_SESSION['user_id'];

    // Cek jadwal yang sama untuk dokter tersebut di hari yang sama
    $query_cek = "SELECT * FROM jadwal_periksa 
                  WHERE id_dokter = ? 
                  AND hari = ? 
                  AND status = 'aktif'";
    
    $stmt_cek = mysqli_prepare($koneksi, $query_cek);
    mysqli_stmt_bind_param($stmt_cek, "is", $id_dokter, $hari);
    mysqli_stmt_execute($stmt_cek);
    $result_cek = mysqli_stmt_get_result($stmt_cek);

    if(mysqli_num_rows($result_cek) > 0) {
        // Jika ditemukan jadwal yang sama
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Jadwal untuk hari " . $hari . " sudah ada!',
                showConfirmButton: true
            });
        </script>";
    } else {
        // Jika tidak ada jadwal yang sama, cek jam
        if($jam_mulai >= $jam_selesai) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Jam selesai harus lebih besar dari jam mulai!',
                    showConfirmButton: true
                });
            </script>";
        } else {
            // Insert jadwal baru
            $query_insert = "INSERT INTO jadwal_periksa 
                           (id_dokter, hari, jam_mulai, jam_selesai, status) 
                           VALUES (?, ?, ?, ?, 'aktif')";
            
            $stmt_insert = mysqli_prepare($koneksi, $query_insert);
            mysqli_stmt_bind_param($stmt_insert, "isss", $id_dokter, $hari, $jam_mulai, $jam_selesai);
            
            if(mysqli_stmt_execute($stmt_insert)) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Jadwal berhasil ditambahkan!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.href = 'jadwal-periksa.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal menambahkan jadwal!',
                        showConfirmButton: true
                    });
                </script>";
            }
        }
    }
}

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

// Proses edit jadwal
if(isset($_POST['edit_jadwal'])) {
    $id_jadwal = $_POST['id_jadwal'];
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $id_dokter = $_SESSION['user_id'];
    
    try {
        // Validasi jam
        if($jam_mulai >= $jam_selesai) {
            throw new Exception("Jam selesai harus lebih besar dari jam mulai!");
        }
        
        // Update jadwal
        $query = "UPDATE jadwal_periksa 
                 SET hari = ?, 
                     jam_mulai = ?, 
                     jam_selesai = ? 
                 WHERE id = ? 
                 AND id_dokter = ?";
                 
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "sssii", 
                             $hari, $jam_mulai, $jam_selesai, 
                             $id_jadwal, $id_dokter);
        
        if(mysqli_stmt_execute($stmt)) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Jadwal berhasil diperbarui!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    window.location.href = 'jadwal-periksa.php';
                });
            </script>";
        } else {
            throw new Exception("Gagal mengupdate jadwal!");
        }
        
    } catch (Exception $e) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '" . $e->getMessage() . "',
                showConfirmButton: true
            });
        </script>";
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

<!-- HTML Modal Form -->
<div class="modal fade" id="modalJadwal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="">
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
$(document).ready(function() {
    // Reset form saat modal dibuka
    $('.btn-tambah').click(function() {
        $('#modalJadwal').modal('show');
        $('#formJadwal')[0].reset();
    });

    // Validasi form sebelum submit
    $('form').on('submit', function(e) {
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

// Fungsi untuk edit jadwal
function editJadwal(jadwal) {
    // Ubah judul modal
    $('.modal-title').text('Edit Jadwal Periksa');
    
    // Isi form dengan data jadwal
    $('select[name="hari"]').val(jadwal.hari);
    $('input[name="jam_mulai"]').val(jadwal.jam_mulai.substr(0, 5));
    $('input[name="jam_selesai"]').val(jadwal.jam_selesai.substr(0, 5));
    
    // Tambahkan input hidden untuk id jadwal
    $('#modalJadwal form').append('<input type="hidden" name="id_jadwal" value="' + jadwal.id + '">');
    
    // Ubah nama submit button
    $('button[name="submit"]').attr('name', 'edit_jadwal');
    
    // Tampilkan modal
    $('#modalJadwal').modal('show');
}

// Reset form ketika modal ditutup
$('#modalJadwal').on('hidden.bs.modal', function () {
    $('.modal-title').text('Tambah Jadwal Periksa');
    $('#modalJadwal form')[0].reset();
    $('input[name="id_jadwal"]').remove();
    $('button[name="edit_jadwal"]').attr('name', 'submit');
});

// Tambahkan event listener untuk tombol edit
$(document).ready(function() {
    $('.btn-tambah').click(function() {
        $('.modal-title').text('Tambah Jadwal Periksa');
        $('button[name="edit_jadwal"]').attr('name', 'submit');
        $('input[name="id_jadwal"]').remove();
        $('#modalJadwal form')[0].reset();
    });
});
</script>

<?php include_once("layouts/footer.php"); ?> 