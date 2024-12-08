<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_dokter = $_SESSION['user_id'];

// Ambil statistik
$query_total_pasien = "SELECT COUNT(DISTINCT dp.id_pasien) as total 
                       FROM daftar_poli dp 
                       JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id 
                       WHERE jp.id_dokter = '$id_dokter'";
$total_pasien = mysqli_fetch_assoc(mysqli_query($koneksi, $query_total_pasien))['total'];

$query_hari_ini = "SELECT COUNT(*) as total 
                   FROM daftar_poli dp 
                   JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id 
                   WHERE jp.id_dokter = '$id_dokter' 
                   AND DATE(dp.created_at) = CURDATE()";
$pasien_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi, $query_hari_ini))['total'];
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $total_pasien; ?></h3>
                            <p>Total Pasien</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $pasien_hari_ini; ?></h3>
                            <p>Pasien Hari Ini</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Tampilkan welcome notification
    showNotification('Selamat datang kembali, Dr. <?php echo $_SESSION["username"]; ?>', 'info');
    
    // Refresh data setiap 30 detik
    setInterval(function() {
        $.ajax({
            url: 'get_dashboard_data.php',
            success: function(response) {
                // Update statistik
                updateDashboardStats(response);
            }
        });
    }, 30000);
});

function updateDashboardStats(data) {
    // Update statistik dengan animasi
    $('.small-box h3').each(function(index) {
        $(this).prop('Counter', 0).animate({
            Counter: data.stats[index]
        }, {
            duration: 1000,
            step: function(now) {
                $(this).text(Math.ceil(now));
            }
        });
    });
}
</script>

<?php include_once("layouts/footer.php"); ?> 