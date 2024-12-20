<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Proses tambah data
if (isset($_POST['tambah'])) {
    try {
        $id_pasien = $_POST['id_pasien'];
        $id_jadwal = $_POST['id_jadwal'];
        $keluhan = $_POST['keluhan'];
        
        // Query untuk menambahkan data pendaftaran
        $query = "INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian, status) 
                  VALUES (?, ?, ?, ?, 'menunggu')";
        $stmt = $koneksi->prepare($query);
        
        // Hitung nomor antrian
        $query_antrian = "SELECT MAX(no_antrian) as max_antrian FROM daftar_poli WHERE id_jadwal = ?";
        $stmt_antrian = $koneksi->prepare($query_antrian);
        $stmt_antrian->bind_param("i", $id_jadwal);
        $stmt_antrian->execute();
        $result_antrian = $stmt_antrian->get_result();
        $row_antrian = $result_antrian->fetch_assoc();
        $no_antrian = $row_antrian['max_antrian'] + 1;
        
        $stmt->bind_param("iisi", $id_pasien, $id_jadwal, $keluhan, $no_antrian);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data pendaftaran berhasil ditambahkan',
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
                text: 'Gagal menambahkan data: " . $e->getMessage() . "',
                confirmButtonColor: '#d33'
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

// Query untuk menampilkan data
$query = "SELECT dp.*, p.nama as nama_pasien, p.no_rm, 
          d.nama as nama_dokter, pl.nama_poli,
          jp.hari, jp.jam_mulai, jp.jam_selesai, jp.status
          FROM daftar_poli dp
          JOIN pasien p ON dp.id_pasien = p.id
          JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
          JOIN dokter d ON jp.id_dokter = d.id
          JOIN poli pl ON d.id_poli = pl.id
          ORDER BY dp.id DESC";
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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Pendaftaran</h3>
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-tambah">
                                <i class="fas fa-plus"></i> Tambah Pendaftaran
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No. RM</th>
                                        <th>Nama Pasien</th>
                                        <th>Poli</th>
                                        <th>Dokter</th>
                                        <th>Hari/Jam</th>
                                        <th>No. Antrian</th>
                                        <th>Keluhan</th>
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
                                        <td><?php echo $row['no_rm']; ?></td>
                                        <td><?php echo $row['nama_pasien']; ?></td>
                                        <td><?php echo $row['nama_poli']; ?></td>
                                        <td><?php echo $row['nama_dokter']; ?></td>
                                        <td><?php echo $row['hari'] . ', ' . substr($row['jam_mulai'],0,5) . '-' . substr($row['jam_selesai'],0,5); ?></td>
                                        <td><?php echo $row['no_antrian']; ?></td>
                                        <td><?php echo $row['keluhan']; ?></td>
                                        <td><?php echo $row['status']; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-edit-<?php echo $row['id']; ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm btn-hapus">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit untuk setiap baris -->
                                    <div class="modal fade" id="modal-edit-<?php echo $row['id']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Edit Pendaftaran</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form action="" method="post">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Pasien</label>
                                                            <select class="form-control select2" name="id_pasien" required>
                                                                <?php 
                                                                mysqli_data_seek($result_pasien, 0);
                                                                while($pasien = mysqli_fetch_assoc($result_pasien)) { 
                                                                ?>
                                                                <option value="<?php echo $pasien['id']; ?>" 
                                                                    <?php echo ($pasien['id'] == $row['id_pasien']) ? 'selected' : ''; ?>>
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
                                                                <option value="<?php echo $jadwal['id']; ?>" 
                                                                    <?php echo ($jadwal['id'] == $row['id_jadwal']) ? 'selected' : ''; ?>>
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
                                                            <textarea class="form-control" name="keluhan" required><?php echo $row['keluhan']; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                                                        <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
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
<?php include_once("layouts/footer.php"); ?> 