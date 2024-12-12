<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_pasien = $_SESSION['user_id'];

// Ambil data poli
$query_poli = "SELECT * FROM poli ORDER BY nama_poli";
$result_poli = mysqli_query($koneksi, $query_poli);

// Proses pendaftaran poli
if (isset($_POST['daftar'])) {
    $id_jadwal = $_POST['id_jadwal'];
    $keluhan = mysqli_real_escape_string($koneksi, $_POST['keluhan']);
    
    // Cek status jadwal
    $check_jadwal = "SELECT status FROM jadwal_periksa WHERE id = '$id_jadwal'";
    $result_jadwal = mysqli_query($koneksi, $check_jadwal);
    $jadwal = mysqli_fetch_assoc($result_jadwal);
    
    if (!$jadwal || $jadwal['status'] !== 'aktif') {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Jadwal Tidak Tersedia',
                text: 'Jadwal yang Anda pilih tidak aktif atau tidak tersedia.',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href='daftar-poli.php';
                }
            });
        </script>";
        exit;
    }
    
    // Cek apakah pasien sudah mendaftar di poli manapun hari ini
    $check_today_query = "SELECT dp.*, p.nama_poli 
                         FROM daftar_poli dp
                         JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                         JOIN dokter d ON jp.id_dokter = d.id
                         JOIN poli p ON d.id_poli = p.id
                         WHERE dp.id_pasien = '$id_pasien' 
                         AND DATE(dp.created_at) = CURDATE()";
    $check_today_result = mysqli_query($koneksi, $check_today_query);
    
    if (mysqli_num_rows($check_today_result) > 0) {
        $poli_data = mysqli_fetch_assoc($check_today_result);
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Dapat Mendaftar',
                text: 'Anda sudah mendaftar di poli " . $poli_data['nama_poli'] . " untuk hari ini! Tidak dapat mendaftar di poli lain pada hari yang sama.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href='daftar-poli.php';
                }
            });
        </script>";
        exit;
    }
    
    // Cek apakah sudah pernah mendaftar di jadwal yang sama
    $check_query = "SELECT * FROM daftar_poli 
                   WHERE id_pasien = '$id_pasien' 
                   AND id_jadwal = '$id_jadwal'
                   AND DATE(created_at) = CURDATE()";
    $check_result = mysqli_query($koneksi, $check_query);
    
    // Cek status pendaftaran terakhir
    $status_query = "SELECT status FROM daftar_poli 
                    WHERE id_pasien = '$id_pasien' 
                    AND id_jadwal = '$id_jadwal'
                    AND status = 'menunggu'
                    ORDER BY created_at DESC LIMIT 1";
    $status_result = mysqli_query($koneksi, $status_query);
    $status_data = mysqli_fetch_assoc($status_result);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>
            Swal.fire({
                icon: 'info',
                title: 'Sudah Terdaftar',
                text: 'Anda sudah mendaftar pada poli ini untuk hari ini!',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href='daftar-poli.php';
                }
            });
        </script>";
    } else if ($status_data && $status_data['status'] == 'menunggu') {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Pendaftaran Belum Selesai',
                text: 'Anda masih memiliki pendaftaran yang belum selesai di poli ini!',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href='daftar-poli.php';
                }
            });
        </script>";
    }
    
    // Ambil nomor antrian terakhir untuk hari ini
    $query_antrian = "SELECT MAX(no_antrian) as max_antrian 
                     FROM daftar_poli 
                     WHERE id_jadwal = '$id_jadwal'
                     AND DATE(created_at) = CURDATE()";
    $result_antrian = mysqli_query($koneksi, $query_antrian);
    $data_antrian = mysqli_fetch_assoc($result_antrian);
    $no_antrian = ($data_antrian['max_antrian'] ?? 0) + 1;
    
    // Insert data pendaftaran baru
    $query = "INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian, status) 
             VALUES ('$id_pasien', '$id_jadwal', '$keluhan', '$no_antrian', 'menunggu')";
             
    if(mysqli_query($koneksi, $query)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Pendaftaran Berhasil!',
                text: 'Anda telah berhasil mendaftar ke poli.',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href='index.php';
                }
            });
        </script>";
        exit();
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href='daftar-poli.php';
                }
            });
        </script>";
    }
}

// Tampilkan detail pendaftaran jika ada
if (isset($_GET['success']) && isset($_SESSION['pendaftaran_sukses'])) {
    $detail = $_SESSION['detail_pendaftaran'];
?>
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Pendaftaran Berhasil!</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-4">
                        <h4>Nomor Antrian Anda</h4>
                        <h1 class="display-1 text-primary"><?php echo $detail['no_antrian']; ?></h1>
                    </div>
                    <div class="alert alert-info">
                        <h5>Detail Pendaftaran:</h5>
                        <p><strong>Poli:</strong> <?php echo $detail['nama_poli']; ?></p>
                        <p><strong>Dokter:</strong> <?php echo $detail['nama_dokter']; ?></p>
                        <p><strong>Jadwal:</strong> <?php echo $detail['hari']; ?>, 
                           <?php echo $detail['jam_mulai']; ?> - 
                           <?php echo $detail['jam_selesai']; ?></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#successModal').modal('show');
        });
    </script>
<?php
    // Hapus session setelah ditampilkan
    unset($_SESSION['pendaftaran_sukses']);
    unset($_SESSION['detail_pendaftaran']);
}
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
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form Pendaftaran Poli</h3>
                        </div>
                        <form action="" method="post">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Pilih Poli</label>
                                    <select class="form-control" id="poli" name="id_poli" required>
                                        <option value="">Pilih Poli</option>
                                        <?php while($poli = mysqli_fetch_assoc($result_poli)) { ?>
                                            <option value="<?php echo $poli['id']; ?>">
                                                <?php echo $poli['nama_poli']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Pilih Jadwal Dokter</label>
                                    <select class="form-control" name="id_jadwal" id="jadwal" required>
                                        <option value="">Pilih Jadwal</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Keluhan</label>
                                    <textarea class="form-control" name="keluhan" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="daftar" class="btn btn-primary">Daftar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Script untuk mengambil jadwal dokter -->
<script>
$(document).ready(function() {
    $('#poli').change(function() {
        var id_poli = $(this).val();
        console.log('ID Poli:', id_poli); // Debug

        if(id_poli != '') {
            $.ajax({
                url: 'get-jadwal.php',
                type: 'POST',
                dataType: 'html',
                data: {id_poli: id_poli},
                beforeSend: function() {
                    $('#jadwal').html('<option value="">Loading...</option>');
                },
                success: function(response) {
                    console.log('Response:', response); // Debug
                    $('#jadwal').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Ajax Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    $('#jadwal').html('<option value="">Error loading jadwal</option>');
                }
            });
        } else {
            $('#jadwal').html('<option value="">Pilih Jadwal</option>');
        }
    });
});
</script>

<!-- Tambahkan di bagian atas setelah navbar -->
<div id="loading-animation" style="display: none;">
    <div class="loading-content">
        <div class="checkmark-circle">
            <div class="checkmark draw"></div>
        </div>
        <h3>Sedang Memproses...</h3>
        <p>Mohon tunggu sebentar</p>
    </div>
</div>

<!-- CSS untuk animasi -->
<style>
#loading-animation {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-content {
    text-align: center;
}

.checkmark-circle {
    width: 100px;
    height: 100px;
    position: relative;
    display: inline-block;
    vertical-align: top;
    margin: 20px;
}

.checkmark {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    stroke-width: 6;
    stroke: #4CAF50;
    stroke-miterlimit: 10;
    box-shadow: inset 0px 0px 0px #4CAF50;
    animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
}

.checkmark.draw {
    animation-delay: 0s;
}

@keyframes fill {
    100% { box-shadow: inset 0px 0px 0px 50px #4CAF50; }
}

@keyframes scale {
    0%, 100% { transform: none; }
    50% { transform: scale3d(1.1, 1.1, 1); }
}

.loading-content h3 {
    color: #333;
    margin: 10px 0;
    font-size: 24px;
}

.loading-content p {
    color: #666;
    margin: 0;
}
</style>

<!-- Modifikasi script submit form -->
<script>
$(document).ready(function() {
    $('#form-daftar').on('submit', function(e) {
        e.preventDefault();
        
        // Tampilkan animasi loading
        $('#loading-animation').fadeIn();
        
        // Kirim form dengan AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                setTimeout(function() {
                    // Sembunyikan loading
                    $('#loading-animation').fadeOut();
                    
                    // Tampilkan animasi sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Pendaftaran Berhasil!',
                        text: 'Silahkan cek nomor antrian Anda di dashboard',
                        showConfirmButton: true,
                        confirmButtonText: 'Lihat Dashboard',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'dashboard.php';
                        }
                    }); 
                }, 1500); // Delay untuk menampilkan animasi
            },
            error: function() {
                $('#loading-animation').fadeOut();
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan! Silahkan coba lagi.'
                });
            }
        });
    });
});
</script>

<?php include_once("layouts/footer.php"); ?> 