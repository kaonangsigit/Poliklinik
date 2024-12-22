<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_pasien = $_SESSION['user_id'];

// Ambil data pasien
$query = "SELECT * FROM pasien WHERE id = '$id_pasien'";
$result = mysqli_query($koneksi, $query);
$pasien = mysqli_fetch_assoc($result);

// Ambil data pendaftaran hari ini
$query_daftar = "SELECT dp.*, p.nama_poli, d.nama as nama_dokter,
                 DATE_FORMAT(jp.jam_mulai, '%H:%i') as jam_mulai,
                 DATE_FORMAT(jp.jam_selesai, '%H:%i') as jam_selesai,
                 jp.hari
                 FROM daftar_poli dp
                 JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                 JOIN dokter d ON jp.id_dokter = d.id
                 JOIN poli p ON d.id_poli = p.id
                 WHERE dp.id_pasien = '$id_pasien'
                 AND DATE(dp.created_at) = CURDATE()
                 ORDER BY dp.created_at DESC LIMIT 1";
$result_daftar = mysqli_query($koneksi, $query_daftar);
$pendaftaran = mysqli_fetch_assoc($result_daftar);

// Proses pembatalan dengan SweetAlert2
if(isset($_POST['batalkan'])) {
    $id_daftar = $_POST['id_daftar'];
    
    // Cek status pendaftaran terlebih dahulu
    $check_status = "SELECT status FROM daftar_poli WHERE id = '$id_daftar' AND id_pasien = '$id_pasien'";
    $result_status = mysqli_query($koneksi, $check_status);
    $status_data = mysqli_fetch_assoc($result_status);
    
    if($status_data['status'] != 'menunggu') {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Tidak Dapat Dibatalkan',
                text: 'Pendaftaran tidak dapat dibatalkan karena status sudah " . $status_data['status'] . "',
                confirmButtonColor: '#d33'
            });
        </script>";
    } else {
        // Jika status masih 'menunggu', proses pembatalan
        $query_batal = "DELETE FROM daftar_poli 
                        WHERE id = '$id_daftar' 
                        AND id_pasien = '$id_pasien' 
                        AND status = 'menunggu'";
        
        if(mysqli_query($koneksi, $query_batal)) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pendaftaran berhasil dibatalkan',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat membatalkan pendaftaran',
                    confirmButtonColor: '#d33'
                });
            </script>";
        }
    }
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard Pasien</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Informasi Pendaftaran Hari Ini -->
            <?php if ($pendaftaran) { ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <div class="alert alert-success mb-4">
                                <h5 class="mb-0"><i class="fas fa-check-circle"></i> Anda telah terdaftar untuk pemeriksaan hari ini</h5>
                            </div>
                            
                            <h4 class="text-primary mb-4">Nomor Antrian Anda</h4>
                            <div class="antrian-number mb-4">
                                <span class="display-1 text-primary font-weight-bold" style="font-size: 100px;"><?php echo $pendaftaran['no_antrian']; ?></span>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div class="card bg-light shadow-sm">
                                        <div class="card-body text-center">
                                            <div class="icon-box mb-3">
                                                <i class="fas fa-hospital-alt fa-3x text-primary"></i>
                                            </div>
                                            <h6 class="text-muted mb-2">Poli</h6>
                                            <h5 class="mb-0 font-weight-bold"><?php echo $pendaftaran['nama_poli']; ?></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light shadow-sm">
                                        <div class="card-body text-center">
                                            <div class="icon-box mb-3">
                                                <i class="fas fa-user-md fa-3x text-success"></i>
                                            </div>
                                            <h6 class="text-muted mb-2">Dokter</h6>
                                            <h5 class="mb-0 font-weight-bold"><?php echo $pendaftaran['nama_dokter']; ?></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light shadow-sm">
                                        <div class="card-body text-center">
                                            <div class="icon-box mb-3">
                                                <i class="fas fa-clock fa-3x text-warning"></i>
                                            </div>
                                            <h6 class="text-muted mb-2">Jadwal</h6>
                                            <h5 class="mb-0 font-weight-bold">
                                                <?php echo $pendaftaran['hari']; ?><br>
                                                <small><?php echo $pendaftaran['jam_mulai'] . ' - ' . $pendaftaran['jam_selesai']; ?></small>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <span class="badge badge-pill px-4 py-2" style="font-size: 1rem;" id="status-badge">
                                    <i class="fas fa-hourglass-half mr-2"></i> Status: 
                                    <span id="status-text"><?php echo ucfirst($pendaftaran['status']); ?></span>
                                </span>
                                <button onclick="konfirmasiBatal(<?php echo $pendaftaran['id']; ?>)" 
                                        class="btn btn-danger btn-lg ml-3"
                                        id="btn-batal"
                                        style="display: <?php echo $pendaftaran['status'] != 'menunggu' ? 'none' : 'inline-block'; ?>">
                                    <i class="fas fa-times-circle"></i> Batalkan Pendaftaran
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <!-- Informasi Pasien -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h3 class="card-title">
                                <i class="fas fa-user-circle mr-2"></i>
                                Data Pasien
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="130px"><strong>Nama</strong></td>
                                            <td width="20px">:</td>
                                            <td><?php echo $pasien['nama']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>No. RM</strong></td>
                                            <td>:</td>
                                            <td><?php echo $pasien['no_rm']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>No. KTP</strong></td>
                                            <td>:</td>
                                            <td><?php echo $pasien['no_ktp']; ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="130px"><strong>Alamat</strong></td>
                                            <td width="20px">:</td>
                                            <td><?php echo $pasien['alamat']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>No. HP</strong></td>
                                            <td>:</td>
                                            <td><?php echo $pasien['no_hp']; ?></td>
                                        </tr>
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

<style>
.antrian-number {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 15px;
    display: inline-block;
}

.icon-box {
    height: 80px;
    width: 80px;
    line-height: 80px;
    border-radius: 50%;
    background: #fff;
    margin: 0 auto;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.card {
    border: none;
    border-radius: 15px;
}

.shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
}

.badge-pill {
    border-radius: 50rem;
}

.badge, .btn {
    transition: all 0.3s ease-in-out;
}

#btn-batal {
    transition: opacity 0.3s ease-in-out, display 0.3s ease-in-out;
}

.badge i {
    transition: all 0.3s ease-in-out;
}
</style>

<!-- Script untuk konfirmasi pembatalan -->
<script>
function konfirmasiBatal(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Pendaftaran yang dibatalkan tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, batalkan!',
        cancelButtonText: 'Tidak',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            let form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="id_daftar" value="${id}">
                <input type="hidden" name="batalkan" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Pastikan SweetAlert tidak mempengaruhi layout sidebar
document.addEventListener('DOMContentLoaded', function() {
    const swalOverlay = document.querySelector('.swal2-container');
    if (swalOverlay) {
        swalOverlay.style.zIndex = '9999';
    }
});

// Tampilkan SweetAlert2 jika ada pesan sukses dari pendaftaran
<?php if(isset($_SESSION['daftar_sukses'])) { ?>
    Swal.fire({
        icon: 'success',
        title: 'Pendaftaran Berhasil!',
        text: 'Silahkan lihat detail pendaftaran Anda di bawah ini',
        timer: 2000,
        showConfirmButton: false
    });
    <?php unset($_SESSION['daftar_sukses']); ?>
<?php } ?>

function updateStatusBadge(status) {
    const statusBadge = $('#status-badge');
    const statusText = $('#status-text');
    const btnBatal = $('#btn-batal');
    
    // Update text dan kapitalisasi huruf pertama
    statusText.text(status.charAt(0).toUpperCase() + status.slice(1));
    
    // Update warna badge dan icon
    statusBadge.removeClass('badge-warning badge-success badge-danger badge-info');
    let iconClass = 'fas ';
    
    switch(status) {
        case 'menunggu':
            statusBadge.addClass('badge-warning');
            iconClass += 'fa-hourglass-half';
            btnBatal.fadeIn(); // Tampilkan tombol dengan animasi
            break;
        case 'selesai':
            statusBadge.addClass('badge-success');
            iconClass += 'fa-check-circle';
            btnBatal.fadeOut(); // Sembunyikan tombol dengan animasi
            break;
        case 'batal':
            statusBadge.addClass('badge-danger');
            iconClass += 'fa-times-circle';
            btnBatal.fadeOut();
            break;
        case 'dalam_pemeriksaan':
            statusBadge.addClass('badge-info');
            iconClass += 'fa-stethoscope';
            btnBatal.fadeOut();
            break;
        default:
            statusBadge.addClass('badge-secondary');
            iconClass += 'fa-question-circle';
            btnBatal.fadeOut();
    }
    
    // Update icon dengan animasi
    statusBadge.find('i')
        .fadeOut(200, function() {
            $(this).attr('class', iconClass + ' mr-2').fadeIn(200);
        });
}

function updateStatus() {
    const pendaftaranId = <?php echo $pendaftaran['id']; ?>;
    
    $.ajax({
        url: 'check-status.php',
        type: 'POST',
        data: { id_daftar: pendaftaranId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                updateStatusBadge(response.status);
                
                // Jika status sudah selesai atau batal, hentikan interval
                if(response.status === 'selesai' || response.status === 'batal') {
                    clearInterval(statusInterval);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating status:', error);
        }
    });
}

// Jalankan update pertama kali
$(document).ready(function() {
    updateStatus();
    // Update setiap 5 detik
    const statusInterval = setInterval(updateStatus, 5000);
});
</script>

<?php include_once("layouts/footer.php"); ?> 