<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

// Proses tambah data
if (isset($_POST['tambah'])) {
    try {
        $nama_poli = $_POST['nama_poli'];
        $keterangan = $_POST['keterangan'];
        
        // Cek apakah nama poli sudah ada
        $check_query = "SELECT * FROM poli WHERE nama_poli = ?";
        $stmt = $koneksi->prepare($check_query);
        $stmt->bind_param("s", $nama_poli);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception('Nama Poli sudah ada!');
        }
        
        $query = "INSERT INTO poli (nama_poli, keterangan) VALUES (?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("ss", $nama_poli, $keterangan);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data poli berhasil ditambahkan',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                position: 'top-end',
                toast: true
            }).then(function() {
                window.location = 'poli.php';
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

// Proses edit data
if (isset($_POST['edit'])) {
    try {
        $id = $_POST['id'];
        $nama_poli = $_POST['nama_poli'];
        $keterangan = $_POST['keterangan'];
        
        // Cek apakah nama poli sudah ada (kecuali untuk poli yang sedang diedit)
        $check_query = "SELECT * FROM poli WHERE nama_poli = ? AND id != ?";
        $stmt = $koneksi->prepare($check_query);
        $stmt->bind_param("si", $nama_poli, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception('Nama Poli sudah ada!');
        }
        
        $query = "UPDATE poli SET nama_poli=?, keterangan=? WHERE id=?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("ssi", $nama_poli, $keterangan, $id);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data poli berhasil diperbarui',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                position: 'top-end',
                toast: true
            }).then(function() {
                window.location = 'poli.php';
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
        
        // Cek apakah poli masih digunakan di tabel dokter
        $check_query = "SELECT * FROM dokter WHERE id_poli = ?";
        $stmt = $koneksi->prepare($check_query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception('Poli tidak dapat dihapus karena masih digunakan oleh dokter!');
        }
        
        $query = "DELETE FROM poli WHERE id = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        
        if ($stmt->affected_rows > 0) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data poli berhasil dihapus',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                    position: 'top-end',
                    toast: true
                }).then(function() {
                    window.location = 'poli.php';
                });
            </script>";
        } else {
            throw new Exception('Data poli tidak ditemukan');
        }
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

// Ambil data poli
$query = "SELECT * FROM poli ORDER BY nama_poli";
$result = mysqli_query($koneksi, $query);
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Kelola Poli</h1>
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
                            <h3 class="card-title">Data Poli</h3>
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-tambah">
                                <i class="fas fa-plus"></i> Tambah Poli
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Poli</th>
                                        <th>Keterangan</th>
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
                                        <td><?php echo $row['nama_poli']; ?></td>
                                        <td><?php echo $row['keterangan']; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-edit-<?php echo $row['id']; ?>">
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
                                                    <h4 class="modal-title">Edit Poli</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form action="" method="post">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Nama Poli</label>
                                                            <input type="text" class="form-control" name="nama_poli" value="<?php echo $row['nama_poli']; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Keterangan</label>
                                                            <textarea class="form-control" name="keterangan" required><?php echo $row['keterangan']; ?></textarea>
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
                <h4 class="modal-title">Tambah Poli</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Poli</label>
                        <input type="text" class="form-control" name="nama_poli" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea class="form-control" name="keterangan" required></textarea>
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
    
    // Konfirmasi sebelum menghapus
    $('.btn-hapus').click(function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data poli akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
});
</script>

<?php include_once("layouts/footer.php"); ?> 