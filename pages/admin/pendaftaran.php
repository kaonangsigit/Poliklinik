<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Tambahkan fungsi untuk cek pendaftaran hari ini
function cekPendaftaranHariIni($koneksi, $id_pasien) {
    $query = "SELECT COUNT(*) as total FROM daftar_poli 
             WHERE id_pasien = ? 
             AND DATE(created_at) = CURDATE()";
    
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_pasien);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'] > 0;
}

// Proses tambah data
if (isset($_POST['tambah'])) {
    try {
        $id_pasien = $_POST['id_pasien'];
        $id_jadwal = $_POST['id_jadwal'];
        $keluhan = $_POST['keluhan'];
        $is_admin = true; // Karena ini di halaman admin

        // Cek apakah sudah mendaftar hari ini
        if (!$is_admin && cekPendaftaranHariIni($koneksi, $id_pasien)) {
            throw new Exception("Pasien sudah melakukan pendaftaran hari ini. Silakan kembali besok.");
        }

        // Generate nomor antrian
        $query_antrian = "SELECT MAX(no_antrian) as last_antrian 
                         FROM daftar_poli dp 
                         JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                         WHERE jp.id = ? 
                         AND DATE(dp.created_at) = CURDATE()";
        
        $stmt = $koneksi->prepare($query_antrian);
        $stmt->bind_param("i", $id_jadwal);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $no_antrian = ($row['last_antrian'] ?? 0) + 1;

        // Insert data pendaftaran
        $query = "INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian) 
                 VALUES (?, ?, ?, ?)";
        
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("iisi", $id_pasien, $id_jadwal, $keluhan, $no_antrian);
        
        if (!$stmt->execute()) {
            throw new Exception("Gagal mendaftarkan pasien");
        }

        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Pendaftaran berhasil dengan nomor antrian " . $no_antrian . "',
                showConfirmButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'pendaftaran.php';
                }
            });
        </script>";
    } catch (Exception $e) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '" . $e->getMessage() . "',
                showConfirmButton: true
            });
        </script>";
    }
}

// Proses hapus data
if (isset($_GET['hapus'])) {
    try {
        $id = $_GET['hapus'];
        
        // Cek apakah data pendaftaran masih bisa dihapus (status masih menunggu)
        $check_query = "SELECT status FROM daftar_poli WHERE id = ?";
        $stmt = $koneksi->prepare($check_query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if ($data['status'] != 'menunggu') {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak dapat dihapus',
                    text: 'Pendaftaran yang sudah diproses tidak dapat dihapus!',
                    confirmButtonColor: '#d33'
                });
            </script>";
            exit;
        }
        
        // Proses hapus jika status masih menunggu
        $query = "DELETE FROM daftar_poli WHERE id = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data pendaftaran berhasil dihapus',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                position: 'top-end',
                toast: true
            }).then(function() {
                window.location = 'pendaftaran.php';
            });
        </script>";
    } catch (Exception $e) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Gagal menghapus data: " . $e->getMessage() . "',
                confirmButtonColor: '#d33'
            });
        </script>";
    }
}

// Proses edit data
if (isset($_POST['edit'])) {
    try {
        $id = $_POST['id'];
        $id_pasien = $_POST['id_pasien'];
        $id_jadwal = $_POST['id_jadwal'];
        $keluhan = $_POST['keluhan'];
        
        // Cek apakah data pendaftaran masih bisa diedit (status masih menunggu)
        $check_query = "SELECT status FROM daftar_poli WHERE id = ?";
        $stmt = $koneksi->prepare($check_query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if ($data['status'] != 'menunggu') {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak dapat diedit',
                    text: 'Pendaftaran yang sudah diproses tidak dapat diedit!',
                    confirmButtonColor: '#d33'
                    }).then(function() {
                        window.location = 'pendaftaran.php';
                    });
            </script>";
            exit;
        }
        
        // Update data pendaftaran
        $query = "UPDATE daftar_poli SET 
                 id_pasien = ?, 
                 id_jadwal = ?, 
                 keluhan = ?
                 WHERE id = ?";
                 
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("iisi", $id_pasien, $id_jadwal, $keluhan, $id);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data pendaftaran berhasil diperbarui',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                position: 'top-end',
                toast: true
            }).then(function() {
                window.location = 'pendaftaran.php';
            });
        </script>";
    } catch (Exception $e) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Gagal memperbarui data: " . $e->getMessage() . "',
                confirmButtonColor: '#d33'
            });
        </script>";
    }
}

// Modifikasi query untuk menampilkan data
$query = "SELECT dp.*, p.nama as nama_pasien, p.no_rm, d.nama as nama_dokter, pol.nama_poli 
          FROM daftar_poli dp
          JOIN pasien p ON dp.id_pasien = p.id
          JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
          JOIN dokter d ON jp.id_dokter = d.id
          JOIN poli pol ON d.id_poli = pol.id
          ORDER BY dp.status ASC, dp.no_antrian ASC";  // Urutkan berdasarkan status dan nomor antrian
$result = mysqli_query($koneksi, $query);

// Ambil data pasien untuk dropdown
$query_pasien = "SELECT * FROM pasien ORDER BY nama";
$result_pasien = mysqli_query($koneksi, $query_pasien);

// Ambil data jadwal aktif untuk dropdown
$query_jadwal = "SELECT jp.*, d.nama as nama_dokter, p.nama_poli
                 FROM jadwal_periksa jp
                 JOIN dokter d ON jp.id_dokter = d.id
                 JOIN poli p ON d.id_poli = p.id
                 WHERE jp.status = 'aktif'  -- Hanya ambil jadwal yang aktif
                 ORDER BY FIELD(jp.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), 
                 jp.jam_mulai";
$result_jadwal = mysqli_query($koneksi, $query_jadwal);
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pendaftaran Poli</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#pendaftaran-aktif" data-toggle="tab">Pendaftaran Aktif</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#riwayat-selesai" data-toggle="tab">Riwayat Selesai</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Tab Pendaftaran Aktif -->
                        <div class="tab-pane active" id="pendaftaran-aktif">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Data Pendaftaran Aktif</h3>
                                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-tambah">
                                        <i class="fas fa-plus"></i> Tambah Pendaftaran
                                    </button>
                                </div>
                                <div class="card-body">
                                    <table id="tabel-aktif" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>No. RM</th>
                                                <th>Nama Pasien</th>
                                                <th>Poli</th>
                                                <th>Dokter</th>
                                                <th>No. Antrian</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            mysqli_data_seek($result, 0);
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                if ($row['status'] != 'selesai') { // Hanya tampilkan yang belum selesai
                                            ?>
                                                <tr>
                                                    <td><?php echo $no++; ?></td>
                                                    <td><?php echo $row['no_rm']; ?></td>
                                                    <td><?php echo $row['nama_pasien']; ?></td>
                                                    <td><?php echo $row['nama_poli']; ?></td>
                                                    <td><?php echo $row['nama_dokter']; ?></td>
                                                    <td><?php echo $row['no_antrian']; ?></td>
                                                    <td>
                                                        <span id="status-<?php echo $row['id']; ?>" class="badge badge-<?php echo $status_class; ?>">
                                                            <i class="fas fa-<?php 
                                                                echo $row['status'] == 'selesai' ? 'check-circle' : 
                                                                    ($row['status'] == 'diperiksa' ? 'stethoscope' : 'clock');
                                                            ?>"></i>
                                                            <?php echo ucfirst($row['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td id="action-<?php echo $row['id']; ?>">
                                                        <?php if($row['status'] == 'menunggu'): ?>
                                                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" 
                                                                    data-target="#modal-edit-<?php echo $row['id']; ?>">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                            <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm btn-hapus">
                                                                <i class="fas fa-trash"></i> Hapus
                                                            </a>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-secondary btn-sm" disabled>
                                                                <i class="fas fa-lock"></i> Terkunci
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php 
                                                }
                                            } 
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Riwayat Selesai -->
                        <div class="tab-pane" id="riwayat-selesai">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Riwayat Pendaftaran Selesai</h3>
                                </div>
                                <div class="card-body">
                                    <table id="tabel-selesai" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>No. RM</th>
                                                <th>Nama Pasien</th>
                                                <th>Poli</th>
                                                <th>Dokter</th>
                                                <th>No. Antrian</th>
                                                <th>Tanggal Selesai</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            mysqli_data_seek($result, 0);
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                if ($row['status'] == 'selesai') {
                                            ?>
                                                <tr>
                                                    <td><?php echo $no++; ?></td>
                                                    <td><?php echo $row['no_rm']; ?></td>
                                                    <td><?php echo $row['nama_pasien']; ?></td>
                                                    <td><?php echo $row['nama_poli']; ?></td>
                                                    <td><?php echo $row['nama_dokter']; ?></td>
                                                    <td><?php echo $row['no_antrian']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($row['updated_at'])); ?></td>
                                                    <td>
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check-circle"></i> Selesai
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php 
                                                }
                                            } 
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
                <h4 class="modal-title">Tambah Pendaftaran</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pasien</label>
                        <select class="form-control select2" name="id_pasien" required>
                            <option value="">Pilih Pasien</option>
                            <?php 
                            mysqli_data_seek($result_pasien, 0);
                            while($pasien = mysqli_fetch_assoc($result_pasien)) { 
                            ?>
                            <option value="<?php echo $pasien['id']; ?>">
                                <?php echo $pasien['no_rm'] . ' - ' . $pasien['nama']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jadwal</label>
                        <select class="form-control select2" name="id_jadwal" required>
                            <option value="">Pilih Jadwal</option>
                            <?php 
                            mysqli_data_seek($result_jadwal, 0);
                            while($jadwal = mysqli_fetch_assoc($result_jadwal)) { 
                            ?>
                            <option value="<?php echo $jadwal['id']; ?>">
                                <?php echo $jadwal['nama_poli'] . ' - ' . 
                                          $jadwal['nama_dokter'] . ' (' . 
                                          $jadwal['hari'] . ', ' . 
                                          substr($jadwal['jam_mulai'],0,5) . '-' . 
                                          substr($jadwal['jam_selesai'],0,5) . ')'; ?>
                            </option>
                            <?php } ?>
                        </select>
                        <small class="text-muted">Hanya menampilkan jadwal yang aktif</small>
                    </div>
                    <div class="form-group">
                        <label>Keluhan</label>
                        <textarea class="form-control" name="keluhan" required></textarea>
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

<!-- Modal Edit untuk setiap baris data -->
<?php
mysqli_data_seek($result, 0); // Reset pointer hasil query
while ($row = mysqli_fetch_assoc($result)) {
?>
<div class="modal fade" id="modal-edit-<?php echo $row['id']; ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Pendaftaran</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <div class="form-group">
                        <label>Pasien</label>
                        <select class="form-control select2" name="id_pasien" required>
                            <?php 
                            mysqli_data_seek($result_pasien, 0);
                            while($pasien = mysqli_fetch_assoc($result_pasien)) { 
                            ?>
                            <option value="<?php echo $pasien['id']; ?>" <?php echo ($pasien['id'] == $row['id_pasien']) ? 'selected' : ''; ?>>
                                <?php echo $pasien['no_rm'] . ' - ' . $pasien['nama']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jadwal</label>
                        <select class="form-control select2" name="id_jadwal" required>
                            <?php 
                            mysqli_data_seek($result_jadwal, 0);
                            while($jadwal = mysqli_fetch_assoc($result_jadwal)) { 
                            ?>
                            <option value="<?php echo $jadwal['id']; ?>" <?php echo ($jadwal['id'] == $row['id_jadwal']) ? 'selected' : ''; ?>>
                                <?php echo $jadwal['nama_poli'] . ' - ' . 
                                          $jadwal['nama_dokter'] . ' (' . 
                                          $jadwal['hari'] . ', ' . 
                                          substr($jadwal['jam_mulai'],0,5) . '-' . 
                                          substr($jadwal['jam_selesai'],0,5) . ')'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keluhan</label>
                        <textarea class="form-control" name="keluhan" required><?php echo $row['keluhan']; ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<!-- Tambahkan script untuk Select2 -->
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk semua modal
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        dropdownParent: $('.modal') // Ini penting agar select2 bekerja dalam modal
    });
});
</script>

<!-- Script untuk DataTables -->
<script>
$(document).ready(function() {
    // Inisialisasi DataTable untuk kedua tabel
    $('#tabel-aktif').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true
    });

    $('#tabel-selesai').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true
    });
});
</script>

<style>
.nav-pills .nav-link.active {
    background-color: #007bff;
    color: white;
}

.nav-pills .nav-link {
    color: #6c757d;
}

.tab-content {
    padding-top: 20px;
}

.badge {
    padding: 8px 12px;
}

.table td {
    vertical-align: middle;
}
</style>

<script>
$(document).ready(function() {
    // Fungsi untuk memperbarui status dan tombol aksi secara real-time
    function updateStatus() {
        $.ajax({
            url: 'get_pendaftaran_status.php',
            method: 'GET',
            success: function(response) {
                response.forEach(function(data) {
                    // Update badge status
                    let statusCell = $(`#status-${data.id}`);
                    let actionCell = $(`#action-${data.id}`); // Tambahkan cell aksi
                    let statusClass = '';
                    let statusIcon = '';
                    
                    switch(data.status) {
                        case 'menunggu':
                            statusClass = 'warning';
                            statusIcon = 'clock';
                            // Update tombol aksi untuk status menunggu
                            actionCell.html(`
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" 
                                        data-target="#modal-edit-${data.id}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="?hapus=${data.id}" class="btn btn-danger btn-sm btn-hapus">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            `);
                            break;
                        case 'diperiksa':
                            statusClass = 'primary';
                            statusIcon = 'stethoscope';
                            // Update tombol aksi untuk status diperiksa
                            actionCell.html(`
                                <button type="button" class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-lock"></i> Terkunci
                                </button>
                            `);
                            break;
                        case 'selesai':
                            statusClass = 'success';
                            statusIcon = 'check-circle';
                            // Update tombol aksi untuk status selesai
                            actionCell.html(`
                                <button type="button" class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-lock"></i> Terkunci
                                </button>
                            `);
                            break;
                    }
                    
                    // Update badge status dengan animasi
                    statusCell.fadeOut(200, function() {
                        $(this).html(`
                            <span class="badge badge-${statusClass}">
                                <i class="fas fa-${statusIcon}"></i> 
                                ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}
                            </span>
                        `).fadeIn(200);
                    });
                });
            }
        });
    }

    // Update status setiap 5 detik
    setInterval(updateStatus, 5000);

    // Inisialisasi SweetAlert untuk tombol hapus
    $(document).on('click', '.btn-hapus', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data pendaftaran akan dihapus!",
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

<?php include_once("layouts/footer.php"); ?> 