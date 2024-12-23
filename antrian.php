<?php
include_once("config/koneksi.php");

// Query untuk mengambil data poli
$query_poli = "SELECT DISTINCT p.id, p.nama_poli,
              (SELECT COUNT(*) FROM daftar_poli dp 
               JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id 
               JOIN dokter d ON jp.id_dokter = d.id 
               WHERE d.id_poli = p.id 
               AND DATE(dp.created_at) = CURDATE() 
               AND dp.status = 'menunggu') as jml_menunggu,
              (SELECT COUNT(*) FROM daftar_poli dp 
               JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id 
               JOIN dokter d ON jp.id_dokter = d.id 
               WHERE d.id_poli = p.id 
               AND DATE(dp.created_at) = CURDATE() 
               AND dp.status = 'diperiksa') as jml_diperiksa,
              (SELECT COUNT(*) FROM daftar_poli dp 
               JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id 
               JOIN dokter d ON jp.id_dokter = d.id 
               WHERE d.id_poli = p.id 
               AND DATE(dp.created_at) = CURDATE() 
               AND dp.status = 'selesai') as jml_selesai
              FROM poli p
              ORDER BY p.nama_poli";

$result_poli = mysqli_query($koneksi, $query_poli);
$poli_data = [];
while($row = mysqli_fetch_assoc($result_poli)) {
    $poli_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Antrian Poliklinik UDINUS</title>
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding-top: 76px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        /* Navbar Styles */
        .navbar {
            background: rgba(13, 110, 253, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('assets/img/udinus.jpg');
            background-size: cover;
            background-position: center;
            padding: 100px 0;
            color: white;
            text-align: center;
            margin-bottom: 50px;
        }

        /* Antrian Card Styles tetap sama seperti sebelumnya */
        .antrian-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }

        .antrian-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        /* ... sisanya tetap sama ... */

        .current-time {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .time-display {
            font-size: 2rem;
            font-weight: 600;
            color: #0d6efd;
        }

        .antrian-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .antrian-card .card-header {
            background: linear-gradient(135deg, #0d6efd, #0043a8);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .antrian-card .card-body {
            padding: 30px;
        }

        .antrian-status {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .status-item:hover {
            transform: translateX(5px);
            background: #e9ecef;
        }

        .status-icon {
            width: 50px;
            height: 50px;
            background: #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-icon i {
            font-size: 1.5rem;
            color: white;
        }

        .status-info {
            flex-grow: 1;
        }

        .status-info h5 {
            margin: 0;
            font-size: 1rem;
            color: #495057;
        }

        .badge {
            font-size: 1rem;
            padding: 8px 15px;
        }

        @media (max-width: 768px) {
            .time-display {
                font-size: 1.5rem;
            }
            
            .status-item {
                padding: 10px;
            }
            
            .status-icon {
                width: 40px;
                height: 40px;
            }
            
            .status-icon i {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="assets/img/logo-udinus.png" height="40" alt="Logo UDINUS">
                Poliklinik UDINUS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="antrian.php">Antrian</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="display-4 fw-bold" data-aos="fade-up">Informasi Antrian</h1>
            <p class="lead" data-aos="fade-up" data-aos-delay="100">
                Pantau antrian poliklinik secara real-time
            </p>
        </div>
    </div>

    <!-- Antrian Content -->
    <div class="container mb-5">
        <div class="row justify-content-center mb-5">
            <div class="col-md-8 text-center">
                <div class="current-time mb-4" data-aos="fade-up">
                    <h3 class="mb-2">Waktu Saat Ini</h3>
                    <div class="time-display" id="currentTime">
                        <!-- Waktu akan diupdate via JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <?php foreach($poli_data as $poli): ?>
            <div class="col-md-4 mb-4" data-aos="fade-up">
                <div class="antrian-card">
                    <div class="card-header">
                        <h4 class="mb-0"><?= $poli['nama_poli'] ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="antrian-status">
                            <div class="status-item">
                                <div class="status-icon">
                                    <i class="fas fa-user-clock"></i>
                                </div>
                                <div class="status-info">
                                    <h5>Menunggu</h5>
                                    <span class="badge bg-warning"><?= $poli['jml_menunggu'] ?></span>
                                </div>
                            </div>
                            <div class="status-item">
                                <div class="status-icon">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <div class="status-info">
                                    <h5>Diperiksa</h5>
                                    <span class="badge bg-primary"><?= $poli['jml_diperiksa'] ?></span>
                                </div>
                            </div>
                            <div class="status-item">
                                <div class="status-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="status-info">
                                    <h5>Selesai</h5>
                                    <span class="badge bg-success"><?= $poli['jml_selesai'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();

        function updateTime() {
            const timeDisplay = document.getElementById('currentTime');
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            timeDisplay.textContent = now.toLocaleDateString('id-ID', options);
        }

        // Update setiap detik
        setInterval(updateTime, 1000);
        updateTime(); // Initial call

        // Auto refresh halaman setiap 30 detik
        setInterval(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>