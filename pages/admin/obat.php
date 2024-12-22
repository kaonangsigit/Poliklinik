<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

// Proses tambah data
if (isset($_POST['tambah'])) {
    try {
        $nama_obat = $_POST['nama_obat'];
        $kemasan = $_POST['kemasan'];
        $harga = $_POST['harga'];
        
        // Cek apakah nama obat sudah ada
        $check_query = "SELECT * FROM obat WHERE nama_obat = ?";
        $stmt = $koneksi->prepare($check_query);
        $stmt->bind_param("s", $nama_obat);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception('Nama Obat sudah ada!');
        }
        
        $query = "INSERT INTO obat (nama_obat, kemasan, harga) VALUES (?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("ssd", $nama_obat, $kemasan, $harga);
        
        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data obat berhasil ditambahkan',
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'obat.php';
                        }
                    });
                  </script>";
        } else {
            throw new Exception("Gagal menambahkan data obat");
        }
        
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

// Proses edit data
if (isset($_POST['edit'])) {
    try {
        $id = $_POST['id'];
        $nama_obat = $_POST['nama_obat'];
        $kemasan = $_POST['kemasan'];
        $harga = $_POST['harga'];
        
        // Cek apakah nama obat sudah ada (kecuali untuk obat yang sedang diedit)
        $check_query = "SELECT * FROM obat WHERE nama_obat = ? AND id != ?";
        $stmt = $koneksi->prepare($check_query);
        $stmt->bind_param("si", $nama_obat, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception('Nama Obat sudah ada!');
        }
        
        $query = "UPDATE obat SET nama_obat=?, kemasan=?, harga=? WHERE id=?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("ssdi", $nama_obat, $kemasan, $harga, $id);
        
        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data obat berhasil diperbarui',
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'obat.php';
                        }
                    });
                  </script>";
        } else {
            throw new Exception("Gagal memperbarui data obat");
        }
        
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
        
        $query = "DELETE FROM obat WHERE id = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        
        echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data obat berhasil dihapus',
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            window.location = 'obat.php';
        });
      </script>";
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

// Ambil data obat
$query = "SELECT * FROM obat ORDER BY nama_obat";
$result = mysqli_query($koneksi, $query);
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Kelola Obat</h1>
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
                            <h3 class="card-title">Data Obat</h3>
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-tambah">
                                <i class="fas fa-plus"></i> Tambah Obat
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Obat</th>
                                        <th>Kemasan</th>
                                        <th>Harga</th>
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
                                        <td><?php echo $row['nama_obat']; ?></td>
                                        <td><?php echo $row['kemasan']; ?></td>
                                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-edit-<?php echo $row['id']; ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm btn-hapus">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="modal-edit-<?php echo $row['id']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Edit Obat</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form action="" method="post">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Nama Obat</label>
                                                            <input type="text" class="form-control" name="nama_obat" value="<?php echo $row['nama_obat']; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Kemasan</label>
                                                            <input type="text" class="form-control" name="kemasan" value="<?php echo $row['kemasan']; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Harga</label>
                                                            <input type="number" class="form-control" name="harga" value="<?php echo $row['harga']; ?>" required>
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
                <h4 class="modal-title">Tambah Obat</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Obat</label>
                        <input type="text" class="form-control" name="nama_obat" required>
                    </div>
                    <div class="form-group">
                        <label>Kemasan</label>
                        <input type="text" class="form-control" name="kemasan" required>
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" class="form-control" name="harga" required>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Destroy DataTable jika sudah ada
    if ($.fn.DataTable.isDataTable('#example1')) {
        $('#example1').DataTable().destroy();
    }
    
    // Inisialisasi DataTable baru
    $('#example1').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
            "infoEmpty": "Tidak ada data yang tersedia",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        }
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    
    // Konfirmasi hapus dengan SweetAlert2
    $(document).on('click', '.btn-hapus', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data obat akan dihapus permanen!",
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