<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

// Set tanggal default hari ini
$tanggal = date('Y-m-d');

// Query untuk data tabel
$query = "SELECT 
            dp.id,
            dp.no_antrian,
            dp.status,
            dp.created_at,
            p.nama as nama_pasien, 
            p.no_rm,
            d.nama as nama_dokter, 
            pol.nama_poli
          FROM daftar_poli dp
          JOIN pasien p ON dp.id_pasien = p.id
          JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
          JOIN dokter d ON jp.id_dokter = d.id
          JOIN poli pol ON d.id_poli = pol.id
          WHERE DATE(dp.created_at) = CURDATE()
          ORDER BY pol.nama_poli, dp.no_antrian";

$result = mysqli_query($koneksi, $query);

// Query statistik
$query_stats = "SELECT 
                COUNT(*) as total_pasien,
                SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as total_menunggu,
                SUM(CASE WHEN status = 'diperiksa' THEN 1 ELSE 0 END) as total_diperiksa,
                SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as total_selesai
                FROM daftar_poli 
                WHERE DATE(created_at) = CURDATE()";

$stats_result = mysqli_query($koneksi, $query_stats);
$stats = mysqli_fetch_assoc($stats_result);
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Monitoring Poli</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Tanggal dan Waktu -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="far fa-calendar-alt fa-2x text-primary mr-3"></i>
                            <div>
                                <h5 class="mb-0" id="tanggalHariIni"></h5>
                                <small class="text-muted">Data Monitoring Poli</small>
                            </div>
                        </div>
                        <div class="bg-light rounded p-2">
                            <i class="far fa-clock"></i>
                            <span id="waktuSekarang" class="ml-2"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $stats['total_pasien'] ?? 0; ?></h3>
                            <p>Total Pasien</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo $stats['total_menunggu'] ?? 0; ?></h3>
                            <p>Menunggu</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?php echo $stats['total_diperiksa'] ?? 0; ?></h3>
                            <p>Sedang Diperiksa</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $stats['total_selesai'] ?? 0; ?></h3>
                            <p>Selesai</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Monitoring -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Kunjungan Pasien Hari Ini</h3>
                </div>
                <div class="card-body">
                    <table id="tabelMonitoring" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Poli</th>
                                <th>No RM</th>
                                <th>Nama Pasien</th>
                                <th>No Antrian</th>
                                <th>Dokter</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($result && mysqli_num_rows($result) > 0) {
                                $no = 1;
                                while($row = mysqli_fetch_assoc($result)) { 
                                    $status_class = 
                                        $row['status'] == 'selesai' ? 'success' : 
                                        ($row['status'] == 'diperiksa' ? 'primary' : 'warning');
                            ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_poli']); ?></td>
                                    <td><?php echo htmlspecialchars($row['no_rm']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_pasien']); ?></td>
                                    <td><?php echo htmlspecialchars($row['no_antrian']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_dokter']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $status_class; ?>">
                                            <i class="fas fa-<?php 
                                                echo $row['status'] == 'selesai' ? 'check-circle' : 
                                                    ($row['status'] == 'diperiksa' ? 'stethoscope' : 'clock');
                                            ?>"></i>
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>Tidak ada data pasien hari ini</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
// Fungsi untuk memformat tanggal dalam bahasa Indonesia
function formatTanggal(date) {
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    return date.toLocaleDateString('id-ID', options);
}

// Fungsi untuk memformat waktu
function formatWaktu(date) {
    const options = { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit', 
        hour12: false 
    };
    return date.toLocaleTimeString('id-ID', options);
}

// Fungsi untuk update tanggal dan waktu
function updateDateTime() {
    const now = new Date();
    document.getElementById('tanggalHariIni').textContent = formatTanggal(now);
    document.getElementById('waktuSekarang').textContent = formatWaktu(now);
}

// Update setiap 1 detik
setInterval(updateDateTime, 1000);

// Update pertama kali saat halaman dimuat
updateDateTime();

// DataTable initialization yang sudah ada
$(document).ready(function() {
    $('#tabelMonitoring').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Auto refresh setiap 30 detik
    setInterval(function() {
        location.reload();
    }, 30000);
});

// Tambahkan animasi fade saat refresh
let fadeTimeout;
function showRefreshAnimation() {
    $('.card').addClass('fade');
    clearTimeout(fadeTimeout);
    fadeTimeout = setTimeout(() => {
        $('.card').removeClass('fade');
    }, 1000);
}

// CSS untuk animasi
const style = document.createElement('style');
style.textContent = `
    .card {
        transition: opacity 0.5s ease-in-out;
    }
    .card.fade {
        opacity: 0.6;
    }
    #waktuSekarang {
        font-family: monospace;
        font-size: 1.1em;
    }
    .bg-light {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 8px 15px;
    }
`;
document.head.appendChild(style);
</script>

<?php include_once("layouts/footer.php"); ?> 