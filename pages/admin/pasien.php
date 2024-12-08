<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

// Fungsi untuk mengeksekusi query dengan prepared statement
function executeQuery($koneksi, $query, $params, $types) {
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt;
}

// Proses tambah pasien
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $no_ktp = $_POST['no_ktp'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        // Cek username sudah digunakan atau belum
        $check_username = "SELECT id FROM pasien WHERE username = ?";
        $stmt_check = $koneksi->prepare($check_username);
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            throw new Exception("Username sudah digunakan!");
        }
        
        // Generate nomor RM dengan format YYYYMM-XXX
        $tahun_bulan = date('Ym'); // Format: 202412
        
        // Cek nomor urut tertinggi untuk bulan ini
        $query_rm = "SELECT MAX(CAST(SUBSTRING_INDEX(no_rm, '-', -1) AS UNSIGNED)) as max_urut 
                     FROM pasien 
                     WHERE no_rm LIKE '$tahun_bulan-%'";
        $result_rm = mysqli_query($koneksi, $query_rm);
        $row_rm = mysqli_fetch_assoc($result_rm);
        $urut = ($row_rm['max_urut'] ?? 0) + 1;
        
        // Format nomor RM: YYYYMM-XXX (contoh: 202412-001)
        $no_rm = $tahun_bulan . '-' . str_pad($urut, 3, '0', STR_PAD_LEFT);
        
        // Cek apakah no KTP sudah terdaftar
        $check_ktp = "SELECT id FROM pasien WHERE no_ktp = ?";
        $stmt_check = $koneksi->prepare($check_ktp);
        $stmt_check->bind_param("s", $no_ktp);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            throw new Exception("Nomor KTP sudah terdaftar!");
        }
        
        // Insert data pasien baru
        $query = "INSERT INTO pasien (nama, no_ktp, alamat, no_hp, no_rm, username, password) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("sssssss", $nama, $no_ktp, $alamat, $no_hp, $no_rm, $username, $password);
        
        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data pasien berhasil ditambahkan dengan Nomor RM: $no_rm',
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'pasien.php';
                        }
                    });
                  </script>";
        } else {
            throw new Exception("Gagal menambahkan data pasien");
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

// Proses edit pasien
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $no_ktp = $_POST['no_ktp'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $username = $_POST['username'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';
    
    try {
        // Cek username sudah digunakan oleh pasien lain
        $check_username = "SELECT id FROM pasien WHERE username = ? AND id != ?";
        $stmt_check = $koneksi->prepare($check_username);
        $stmt_check->bind_param("si", $username, $id);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            throw new Exception("Username sudah digunakan oleh pasien lain!");
        }
        
        // Update query berdasarkan ada tidaknya perubahan password
        if (!empty($password)) {
            $query = "UPDATE pasien SET nama=?, no_ktp=?, alamat=?, no_hp=?, username=?, password=? WHERE id=?";
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("ssssssi", $nama, $no_ktp, $alamat, $no_hp, $username, $password, $id);
        } else {
            $query = "UPDATE pasien SET nama=?, no_ktp=?, alamat=?, no_hp=?, username=? WHERE id=?";
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("sssssi", $nama, $no_ktp, $alamat, $no_hp, $username, $id);
        }
        
        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data pasien berhasil diperbarui',
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'pasien.php';
                        }
                    });
                  </script>";
        } else {
            throw new Exception("Gagal memperbarui data pasien");
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
        
        $query = "DELETE FROM pasien WHERE id = ?";
        executeQuery($koneksi, $query, [$id], 'i');

        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data pasien berhasil dihapus',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    window.location = 'pasien.php';
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

// Handler untuk request AJAX get pasien
if (isset($_POST['id']) && isset($_POST['action']) && $_POST['action'] == 'get_pasien') {
    header('Content-Type: application/json');
    
    try {
        $id = $_POST['id'];
        
        $query = "SELECT * FROM pasien WHERE id = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode($row);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// Ambil data pasien
$query = "SELECT * FROM pasien ORDER BY nama";
$result = mysqli_query($koneksi, $query);

// Tambahkan query untuk mengambil jadwal yang aktif
$query_jadwal = "SELECT jp.*, d.nama as nama_dokter, p.nama_poli
                 FROM jadwal_periksa jp
                 JOIN dokter d ON jp.id_dokter = d.id
                 JOIN poli p ON d.id_poli = p.id
                 WHERE jp.status = 'aktif'
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
                    <h1>Data Pasien</h1>
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
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-tambah">
                                <i class="fas fa-plus"></i> Tambah Pasien
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="container mt-4">
                                <h2>Data Pasien</h2>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>No. KTP</th>
                                            <th>Alamat</th>
                                            <th>No. HP</th>
                                            <th>No. RM</th>
                                            <th>Username</th>
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
                                            <td><?php echo $row['no_ktp']; ?></td>
                                            <td><?php echo $row['alamat']; ?></td>
                                            <td><?php echo $row['no_hp']; ?></td>
                                            <td><?php echo $row['no_rm']; ?></td>
                                            <td><?php echo $row['username']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-warning btn-sm btn-edit" data-toggle="modal" data-target="#modal-edit" data-id="<?php echo $row['id']; ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm btn-hapus">
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
                <h4 class="modal-title">Tambah Pasien</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label>No. KTP</label>
                        <input type="text" class="form-control" name="no_ktp" 
                               pattern="[0-9]{16}" title="Nomor KTP harus 16 digit" required>
                        <small class="text-muted">Masukkan 16 digit nomor KTP</small>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea class="form-control" name="alamat" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>No. HP</label>
                        <input type="text" class="form-control" name="no_hp" 
                               pattern="[0-9]{10,13}" title="Nomor HP harus 10-13 digit" required>
                        <small class="text-muted">Masukkan 10-13 digit nomor HP</small>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" required>
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

<!-- Modal Edit -->
<div class="modal fade" id="modal-edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Pasien</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="form-edit" action="" method="post">
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" class="form-control" name="nama" id="edit-nama" required>
                    </div>
                    <div class="form-group">
                        <label>No. KTP</label>
                        <input type="text" class="form-control" name="no_ktp" id="edit-no_ktp" 
                               pattern="[0-9]{16}" title="Nomor KTP harus 16 digit" required>
                        <small class="text-muted">Masukkan 16 digit nomor KTP</small>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea class="form-control" name="alamat" id="edit-alamat" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>No. HP</label>
                        <input type="text" class="form-control" name="no_hp" id="edit-no_hp" 
                               pattern="[0-9]{10,13}" title="Nomor HP harus 10-13 digit" required>
                        <small class="text-muted">Masukkan 10-13 digit nomor HP</small>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" id="edit-username" required>
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



<script>
    $(document).ready(function() {
    // Inisialisasi DataTable
    $('#tablePasien').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
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
        },
        "columns": [
            { "width": "5%" },  // No
            { "width": "15%" }, // Nama
            { "width": "15%" }, // No. KTP
            { "width": "20%" }, // Alamat
            { "width": "10%" }, // No. HP
            { "width": "10%" }, // No. RM
            { "width": "10%" }, // Username
            { "width": "15%" }  // Aksi
            ]
        });

        // Tangani klik tombol edit
    $('.btn-edit').click(function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        // Reset form sebelum mengisi data baru
        $('#form-edit')[0].reset();
        
        // Ambil data pasien dengan AJAX
        $.ajax({
            url: 'get_pasien.php',
            type: 'POST',
            data: {
                id: id,
                action: 'get_pasien'
            },
            dataType: 'json',
            success: function(response) {
                if (response) {
                    // Isi form dengan data yang diterima
                    $('#edit-id').val(response.id);
                    $('#edit-nama').val(response.nama);
                    $('#edit-no_ktp').val(response.no_ktp);
                    $('#edit-alamat').val(response.alamat);
                    $('#edit-no_hp').val(response.no_hp);
                    $('#edit-username').val(response.username);
                    
                    // Tampilkan modal
                    $('#modal-edit').modal('show');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal mengambil data pasien: ' + error,
                    showConfirmButton: true
                });
            }
        });
    });

    // Konfirmasi sebelum menghapus
    $('.btn-hapus').click(function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data pasien akan dihapus permanen!",
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