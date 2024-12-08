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
    
    // Hapus pendaftaran
    mysqli_query($koneksi, "DELETE FROM daftar_poli WHERE id = '$id_daftar' AND id_pasien = '$id_pasien' AND status = 'menunggu'");
    
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Pendaftaran berhasil dibatalkan',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = 'index.php';
        });
    </script>";
    exit();
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
                                <span class="badge badge-pill badge-warning px-4 py-2" style="font-size: 1rem;">
                                    <i class="fas fa-hourglass-half mr-2"></i> Status: Menunggu
                                </span>
                                <button onclick="konfirmasiBatal(<?php echo $pendaftaran['id']; ?>)" 
                                        class="btn btn-danger btn-lg ml-3">
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
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form pembatalan
            let form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `<input type="hidden" name="id_daftar" value="${id}">
                            <input type="hidden" name="batalkan" value="1">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

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
</script>

<?php include_once("layouts/footer.php"); ?> 