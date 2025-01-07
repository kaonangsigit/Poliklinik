<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

// Proses tambah jadwal
if (isset($_POST['tambah'])) {
    $id_dokter = $_POST['id_dokter'];
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    
    // Cek apakah jadwal sudah ada
    $check_jadwal = mysqli_query($koneksi, 
        "SELECT * FROM jadwal_periksa 
         WHERE id_dokter = $id_dokter 
         AND hari = '$hari' 
         AND ((jam_mulai BETWEEN '$jam_mulai' AND '$jam_selesai') 
         OR (jam_selesai BETWEEN '$jam_mulai' AND '$jam_selesai'))"
    );
    
    if (mysqli_num_rows($check_jadwal) > 0) {
        $error = "Jadwal dokter sudah ada di waktu tersebut!";
    } else {
        $query = "INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai) 
                 VALUES ($id_dokter, '$hari', '$jam_mulai', '$jam_selesai')";
        mysqli_query($koneksi, $query);
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Jadwal berhasil ditambahkan!',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                window.location.href = 'jadwal.php';
            });
        </script>";
    }
}

// Proses hapus jadwal
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM jadwal_periksa WHERE id=$id");
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Jadwal berhasil dihapus!',
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            window.location.href = 'jadwal.php';
        });
    </script>";
}

// Proses ubah status jadwal
if (isset($_GET['ubah_status'])) {
    $id = $_GET['ubah_status'];
    $status = $_GET['status'] == 'aktif' ? 'tidak aktif' : 'aktif';
    
    $query = "UPDATE jadwal_periksa SET status = ? WHERE id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Status jadwal berhasil diubah!',
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            window.location.href = 'jadwal.php';
        });
    </script>";
}

// Tambahkan proses edit jadwal setelah proses ubah status
if (isset($_POST['edit_jadwal'])) {
    $id = $_POST['id_jadwal'];
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
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'jadwal.php';
                }
            });
        </script>";
    } else {
        // Jika validasi berhasil, lakukan update
        $query = "UPDATE jadwal_periksa SET jam_mulai = ?, jam_selesai = ? WHERE id = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("ssi", $jam_mulai, $jam_selesai, $id);
        
        if($stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Jadwal berhasil diubah!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    window.location.href = 'jadwal.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengubah jadwal!',
                    showConfirmButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'jadwal.php';
                    }
                });
            </script>";
        }
    }
}

// Ambil data jadwal yang aktif
$query = "SELECT jp.*, d.nama as nama_dokter, p.nama_poli
          FROM jadwal_periksa jp
          JOIN dokter d ON jp.id_dokter = d.id
          JOIN poli p ON d.id_poli = p.id
          WHERE jp.status = 'aktif'
          ORDER BY FIELD(jp.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), 
          jp.jam_mulai";
$result = mysqli_query($koneksi, $query);

// Ambil semua data jadwal untuk ditampilkan di tabel
$query_all = "SELECT jp.*, d.nama as nama_dokter, p.nama_poli
              FROM jadwal_periksa jp
              JOIN dokter d ON jp.id_dokter = d.id
              JOIN poli p ON d.id_poli = p.id
              ORDER BY FIELD(jp.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), 
              jp.jam_mulai";
$result_all = mysqli_query($koneksi, $query_all);

// Ambil data dokter untuk dropdown
$query_dokter = "SELECT d.*, p.nama_poli 
                 FROM dokter d 
                 JOIN poli p ON d.id_poli = p.id 
                 ORDER BY p.nama_poli, d.nama";
$result_dokter = mysqli_query($koneksi, $query_dokter);
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Jadwal Dokter</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Jadwal</h3>
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-tambah">
                                <i class="fas fa-plus"></i> Tambah Jadwal
                            </button>
                        </div>
                        <div class="card-body">
                            <?php if(isset($error)) { ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php } ?>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Poli</th>
                                        <th>Dokter</th>
                                        <th>Hari</th>
                                        <th>Jam Praktik</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while($row = mysqli_fetch_assoc($result_all)) { 
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['nama_poli']; ?></td>
                                        <td><?php echo $row['nama_dokter']; ?></td>
                                        <td><?php echo $row['hari']; ?></td>
                                        <td><?php echo substr($row['jam_mulai'],0,5) . ' - ' . substr($row['jam_selesai'],0,5); ?></td>
                                        <td>
                                            <?php if($row['status'] == 'aktif'): ?>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-times-circle"></i> Tidak Aktif
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button onclick="toggleStatus(<?php echo $row['id']; ?>, '<?php echo $row['status']; ?>')" 
                                                    class="btn <?php echo $row['status'] == 'aktif' ? 'btn-warning' : 'btn-success'; ?> btn-sm">
                                                <?php if($row['status'] == 'aktif'): ?>
                                                    <i class="fas fa-toggle-off"></i> Nonaktifkan
                                                <?php else: ?>
                                                    <i class="fas fa-toggle-on"></i> Aktifkan
                                                <?php endif; ?>
                                            </button>
                                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit<?php echo $row['id']; ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm btn-hapus" 
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit untuk setiap baris -->
                                    <div class="modal fade" id="modalEdit<?php echo $row['id']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Edit Jadwal</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form action="" method="post">
                                                    <input type="hidden" name="id_jadwal" value="<?php echo $row['id']; ?>">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Hari</label>
                                                            <input type="text" class="form-control" value="<?php echo $row['hari']; ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Dokter</label>
                                                            <input type="text" class="form-control" value="<?php echo $row['nama_dokter']; ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Jam Mulai</label>
                                                            <input type="time" class="form-control" name="jam_mulai" value="<?php echo substr($row['jam_mulai'], 0, 5); ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Jam Selesai</label>
                                                            <input type="time" class="form-control" name="jam_selesai" value="<?php echo substr($row['jam_selesai'], 0, 5); ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                        <button type="submit" name="edit_jadwal" class="btn btn-primary">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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

<!-- Modal Tambah -->
<div class="modal fade" id="modal-tambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Jadwal</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Dokter</label>
                        <select class="form-control select2" name="id_dokter" required>
                            <option value="">Pilih Dokter</option>
                            <?php while($dokter = mysqli_fetch_assoc($result_dokter)) { ?>
                            <option value="<?php echo $dokter['id']; ?>">
                                <?php echo $dokter['nama_poli'] . ' - ' . $dokter['nama']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hari</label>
                        <select class="form-control" name="hari" required>
                            <option value="">Pilih Hari</option>
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
                        <input type="time" class="form-control" name="jam_mulai" required>
                    </div>
                    <div class="form-group">
                        <label>Jam Selesai</label>
                        <input type="time" class="form-control" name="jam_selesai" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once("layouts/footer.php"); ?> 

<script>
function toggleStatus(id, currentStatus) {
    Swal.fire({
        title: 'Konfirmasi',
        text: `Apakah Anda yakin ingin ${currentStatus == 'aktif' ? 'menonaktifkan' : 'mengaktifkan'} jadwal ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `?ubah_status=${id}&status=${currentStatus}`;
        }
    });
}

// Tambahkan animasi hover untuk tombol
$(document).ready(function() {
    $('.btn').hover(
        function() {
            $(this).addClass('animated pulse');
        },
        function() {
            $(this).removeClass('animated pulse');
        }
    );
});

$(document).ready(function() {
    // Inisialisasi SweetAlert untuk tombol hapus
    $(document).on('click', '.btn-hapus', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data jadwal akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
});
</script>

<style>
/* CSS untuk animasi dan styling tambahan */
.badge {
    font-size: 14px;
    padding: 8px 12px;
}

.badge i {
    margin-right: 5px;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.btn {
    transition: all 0.3s ease;
    margin: 0 2px;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.btn i {
    margin-right: 5px;
}

/* Animasi pulse */
@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.animated {
    animation-duration: 1s;
    animation-fill-mode: both;
}

.pulse {
    animation-name: pulse;
}

/* Styling untuk tabel */
.table td {
    vertical-align: middle !important;
}

/* Efek hover pada baris tabel */
.table tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}
</style> 