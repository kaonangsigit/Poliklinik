<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_pasien = $_SESSION['user_id'];

// Ambil data poli
$query_poli = "SELECT * FROM poli ORDER BY nama_poli";
$result_poli = mysqli_query($koneksi, $query_poli);

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

// Proses pendaftaran poli
if (isset($_POST['daftar'])) {
    try {
        $id_pasien = $_SESSION['user_id']; // ID pasien yang sedang login
        $id_jadwal = $_POST['id_jadwal'];
        $keluhan = $_POST['keluhan'];
        $is_admin = false; // Karena ini di halaman pasien

        // Cek apakah sudah mendaftar hari ini
        if (cekPendaftaranHariIni($koneksi, $id_pasien)) {
            throw new Exception("Anda sudah melakukan pendaftaran hari ini. Silakan kembali besok atau hubungi admin untuk bantuan.");
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
            throw new Exception("Gagal melakukan pendaftaran");
        }

        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Pendaftaran berhasil dengan nomor antrian " . $no_antrian . "',
                showConfirmButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php';
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
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card card-primary shadow">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-hospital-user mr-2"></i>Form Pendaftaran Poli
                            </h3>
                        </div>
                        <form action="" method="post">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="poli">
                                        <i class="fas fa-hospital mr-1"></i> Pilih Poli
                                    </label>
                                    <select class="form-control select2bs4" id="poli" name="id_poli" required>
                                        <option value="">-- Pilih Poli --</option>
                                        <?php while($poli = mysqli_fetch_assoc($result_poli)) { ?>
                                            <option value="<?php echo $poli['id']; ?>">
                                                <?php echo $poli['nama_poli']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="jadwal">
                                        <i class="fas fa-calendar-alt mr-1"></i> Pilih Jadwal Dokter
                                    </label>
                                    <select class="form-control select2bs4" name="id_jadwal" id="jadwal" required>
                                        <option value="">-- Pilih Jadwal --</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="keluhan">
                                        <i class="fas fa-notes-medical mr-1"></i> Keluhan
                                    </label>
                                    <textarea class="form-control" id="keluhan" name="keluhan" rows="4" 
                                              placeholder="Tuliskan keluhan Anda di sini..." required></textarea>
                                </div>
                            </div>
                            <div class="card-footer text-center">
                                <button type="submit" name="daftar" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-check-circle mr-2"></i>Daftar
                                </button>
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

<!-- CSS tambahan -->
<style>
/* Reset dan base styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Card styles */
.card {
    border-radius: 15px;
    border: none;
    margin-bottom: 30px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    background: linear-gradient(45deg, #007bff, #0056b3);
    padding: 15px 20px;
}

.card-body {
    padding: 25px;
}

/* Form styles */
.form-group {
    margin-bottom: 1.5rem;
}

.form-control {
    height: auto;
    padding: 0.75rem 1rem;
    font-size: 14px;
    border-radius: 8px;
}

/* Select2 fixes */
.select2-container--bootstrap4 {
    display: block;
    width: 100% !important;
}

.select2-container--bootstrap4 .select2-selection {
    height: 45px !important;
    border-radius: 8px !important;
    border: 1px solid #ced4da !important;
}

.select2-container--bootstrap4 .select2-selection--single {
    padding: 0.375rem 0.75rem !important;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    padding: 5px 0 !important;
    line-height: 1.5 !important;
    height: auto !important;
    margin-top: 0 !important;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
    height: 43px !important;
    width: 30px !important;
    right: 3px !important;
}

.select2-container--bootstrap4 .select2-results__option {
    padding: 8px 12px;
    font-size: 14px;
}

.select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {
    padding: 8px;
    border-radius: 4px;
}

.select2-container--bootstrap4 .select2-dropdown {
    border-color: #80bdff;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Textarea */
textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

/* Button */
.btn-primary {
    padding: 12px 30px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 50px;
    box-shadow: 0 4px 6px rgba(50,50,93,.11);
    transition: all 0.3s ease;
}

/* Responsive fixes */
@media (max-width: 768px) {
    .card-body {
        padding: 15px;
    }
    
    .btn-primary {
        width: 100%;
        padding: 10px 20px;
    }
    
    .select2-container--bootstrap4 .select2-selection {
        height: 40px !important;
    }
    
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }
}

/* Fix untuk dropdown yang terpotong */
.select2-container--open .select2-dropdown {
    margin-top: 3px;
}

.select2-container--bootstrap4.select2-container--focus .select2-selection {
    border-color: #80bdff !important;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25) !important;
}

/* Fix untuk placeholder */
.select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
    color: #6c757d;
    line-height: 2;
}
</style>

<!-- Tambahkan ini di bagian bawah sebelum closing body -->
<script>
$(document).ready(function() {
    $('.select2bs4').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Pilih opsi',
        allowClear: true,
        dropdownAutoWidth: true
    });
});
</script>

<?php include_once("layouts/footer.php"); ?> 