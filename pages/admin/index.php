<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

// Hitung total data untuk dashboard
$query_dokter = "SELECT COUNT(*) as total FROM dokter";
$result_dokter = mysqli_query($koneksi, $query_dokter);
$total_dokter = mysqli_fetch_assoc($result_dokter)['total'];

$query_pasien = "SELECT COUNT(*) as total FROM pasien";
$result_pasien = mysqli_query($koneksi, $query_pasien);
$total_pasien = mysqli_fetch_assoc($result_pasien)['total'];

$query_poli = "SELECT COUNT(*) as total FROM poli";
$result_poli = mysqli_query($koneksi, $query_poli);
$total_poli = mysqli_fetch_assoc($result_poli)['total'];

$query_obat = "SELECT COUNT(*) as total FROM obat";
$result_obat = mysqli_query($koneksi, $query_obat);
$total_obat = mysqli_fetch_assoc($result_obat)['total'];
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Dokter Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $total_dokter; ?></h3>
                            <p>Total Dokter</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <a href="dokter.php" class="small-box-footer">
                            Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Pasien Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $total_pasien; ?></h3>
                            <p>Total Pasien</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="pasien.php" class="small-box-footer">
                            Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Poli Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo $total_poli; ?></h3>
                            <p>Total Poli</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <a href="poli.php" class="small-box-footer">
                            Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Obat Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo $total_obat; ?></h3>
                            <p>Total Obat</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <a href="obat.php" class="small-box-footer">
                            Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Aktivitas Terbaru</h3>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <!-- Timeline items will be added here -->
                                <div>Selamat datang di Dashboard Admin Poliklinik</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once("layouts/footer.php"); ?> 