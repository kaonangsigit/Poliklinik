<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

// Proses tambah data
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $id_poli = $_POST['id_poli'];
    
    // Generate username otomatis: dr.nama (tanpa spasi)
    //$username = 'dr.' . strtolower(str_replace(' ', '', $nama));
    $username = $nama;
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        // Cek apakah username sudah ada
        $check_query = "SELECT * FROM dokter WHERE username = '$username'";
        $check_result = mysqli_query($koneksi, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            throw new Exception('Username sudah digunakan!');
        }
        
        $query = "INSERT INTO dokter (nama, alamat, no_hp, id_poli, username, password) 
                  VALUES ('$nama', '$alamat', '$no_hp', $id_poli, '$username', '$password')";
        
        if (!mysqli_query($koneksi, $query)) {
            throw new Exception(mysqli_error($koneksi));
        }
        
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data dokter berhasil ditambahkan',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            }).then(function() {
                window.location.href = 'dokter.php';
            });
        </script>";
    } catch (Exception $e) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Gagal menambahkan data: " . $e->getMessage() . "',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}

// Proses edit data
if (isset($_POST['edit'])) {
    try {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $alamat = $_POST['alamat'];
        $no_hp = $_POST['no_hp'];
        $id_poli = $_POST['id_poli'];
        $username = $nama;
        
        // Cek apakah username sudah ada (kecuali untuk dokter yang sedang diedit)
        $check_query = "SELECT * FROM dokter WHERE username = '$username' AND id != $id";
        $check_result = mysqli_query($koneksi, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            throw new Exception('Username sudah digunakan!');
        }
        
        // Perbaikan query update
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $query = "UPDATE dokter SET 
                     nama = '$nama', 
                     alamat = '$alamat', 
                     no_hp = '$no_hp', 
                     id_poli = $id_poli, 
                     username = '$username', 
                     password = '$password' 
                     WHERE id = $id";
        } else {
            $query = "UPDATE dokter SET 
                     nama = '$nama', 
                     alamat = '$alamat', 
                     no_hp = '$no_hp', 
                     id_poli = $id_poli, 
                     username = '$username' 
                     WHERE id = $id";
        }
        
        if (!mysqli_query($koneksi, $query)) {
            throw new Exception(mysqli_error($koneksi));
        }
        
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data dokter berhasil diperbarui',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                window.location.href = 'dokter.php';
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

// Proses hapus data
if (isset($_GET['hapus'])) {
    try {
        $id = $_GET['hapus'];
        
        // Gunakan prepared statement untuk menghapus data
        $query = "DELETE FROM dokter WHERE id = ?";
        $stmt = $koneksi->prepare($query);
        if ($stmt === false) {
            throw new Exception('Prepare statement failed: ' . $koneksi->error);
        }
        
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception('Gagal menghapus data: ' . $stmt->error);
        }
        
        if ($stmt->affected_rows > 0) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data dokter berhasil dihapus',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                }).then(function() {
                    window.location = 'dokter.php';
                });
            </script>";
        } else {
            throw new Exception('Data dokter tidak ditemukan');
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menghapus data: " . $e->getMessage() . "',
                showConfirmButton: true
            });
        </script>";
    }
}

// Ambil data dokter
$query = "SELECT d.*, p.nama_poli 
          FROM dokter d 
          JOIN poli p ON d.id_poli = p.id 
          ORDER BY d.nama";
$result = mysqli_query($koneksi, $query);

// Ambil data poli untuk dropdown
$query_poli = "SELECT * FROM poli ORDER BY nama_poli";
$result_poli = mysqli_query($koneksi, $query_poli);
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Kelola Dokter</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Dokter</h3>
                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-tambah">
                        <i class="fas fa-plus"></i> Tambah Dokter
                    </button>
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>No. HP</th>
                                <th>Poli</th>
                                <th>Username</th>
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
                                <td><?php echo $row['nama']; ?></td>
                                <td><?php echo $row['alamat']; ?></td>
                                <td><?php echo $row['no_hp']; ?></td>
                                <td><?php echo $row['nama_poli']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" 
                                            data-target="#modal-edit-<?php echo $row['id']; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="modal-edit-<?php echo $row['id']; ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Edit Dokter</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form action="" method="post">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Nama</label>
                                                    <input type="text" class="form-control" name="nama" 
                                                           value="<?php echo $row['nama']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Alamat</label>
                                                    <textarea class="form-control" name="alamat" required><?php echo $row['alamat']; ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>No. HP</label>
                                                    <input type="text" class="form-control" name="no_hp" 
                                                           value="<?php echo $row['no_hp']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Poli</label>
                                                    <select class="form-control" name="id_poli" required>
                                                        <?php 
                                                        mysqli_data_seek($result_poli, 0);
                                                        while($poli = mysqli_fetch_assoc($result_poli)) { 
                                                        ?>
                                                        <option value="<?php echo $poli['id']; ?>" 
                                                                <?php echo ($poli['id'] == $row['id_poli']) ? 'selected' : ''; ?>>
                                                            <?php echo $poli['nama_poli']; ?>
                                                        </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Username</label>
                                                    <input type="text" class="form-control" name="username" 
                                                           value="<?php echo $row['username']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Password</label>
                                                    <input type="text" class="form-control" name="password">
                                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
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
    </section>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modal-tambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Dokter</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea class="form-control" name="alamat" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>No. HP</label>
                        <input type="text" class="form-control" name="no_hp" required>
                    </div>
                    <div class="form-group">
                        <label>Poli</label>
                        <select class="form-control" name="id_poli" required>
                            <option value="">Pilih Poli</option>
                            <?php 
                            mysqli_data_seek($result_poli, 0);
                            while($poli = mysqli_fetch_assoc($result_poli)) { 
                            ?>
                            <option value="<?php echo $poli['id']; ?>"><?php echo $poli['nama_poli']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="text" class="form-control" name="password" required>
                        <small class="text-muted">Password ini akan digunakan dokter untuk login</small>
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

<script>
$(document).ready(function() {
    // Konfirmasi sebelum menghapus
    $('.btn-hapus').click(function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data dokter akan dihapus permanen!",
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



