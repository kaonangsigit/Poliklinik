<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Poliklinik</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/img/hospital.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            text-align: center;
            color: white;
        }
        .service-card {
            transition: transform 0.3s;
            cursor: pointer;
        }
        .service-card:hover {
            transform: translateY(-10px);
        }
        .login-section {
            background-color: #f8f9fa;
            padding: 80px 0;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .login-card-body {
            padding: 40px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-hospital-alt me-2"></i>
                Poliklinik
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#layanan">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#antrian">Antrian</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h1 class="display-4 mb-4">Selamat Datang di Poliklinik</h1>
                    <p class="lead mb-5">Memberikan pelayanan kesehatan terbaik untuk Anda dan keluarga</p>
                    <a href="#login" class="btn btn-primary btn-lg">Login Sekarang</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Section -->
    <section class="py-5" id="layanan">
        <div class="container">
            <h2 class="text-center mb-5">Layanan Kami</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card service-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Dokter Profesional</h5>
                            <p class="card-text">Ditangani oleh dokter-dokter profesional dan berpengalaman</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card service-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-heartbeat fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Pelayanan 24 Jam</h5>
                            <p class="card-text">Siap melayani kebutuhan kesehatan Anda selama 24 jam</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card service-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-hospital-user fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Fasilitas Modern</h5>
                            <p class="card-text">Dilengkapi dengan fasilitas dan peralatan medis modern</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Section -->
    <section class="login-section" id="login">
        <div class="container">
            <h2 class="text-center mb-5">Login</h2>
            <div class="row justify-content-center">
                <div class="col-md-4 mb-4">
                    <div class="card login-card">
                        <div class="card-body text-center">
                            <i class="fas fa-user-shield fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Admin</h5>
                            <p class="card-text">Login sebagai administrator</p>
                            <a href="auth/login.php?role=admin" class="btn btn-primary">Login Admin</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card login-card">
                        <div class="card-body text-center">
                            <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Dokter</h5>
                            <p class="card-text">Login sebagai dokter</p>
                            <a href="auth/login.php?role=dokter" class="btn btn-primary">Login Dokter</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card login-card">
                        <div class="card-body text-center">
                            <i class="fas fa-user fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Pasien</h5>
                            <p class="card-text">Login sebagai pasien</p>
                            <a href="auth/login.php?role=pasien" class="btn btn-primary mb-2">Login Pasien</a>
                            <div class="mt-2">
                                <small class="text-muted">Belum punya akun?</small>
                                <a href="auth/register.php" class="d-block btn btn-outline-primary mt-2">Daftar Pasien Baru</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Antrian Section -->
    <?php
    // Koneksi database
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

    <section class="py-5 bg-light" id="antrian">
        <div class="container">
            <h2 class="text-center mb-5">Status Antrian Hari Ini</h2>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div id="antrianCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach($poli_data as $index => $poli) { ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h4 class="card-title text-center mb-0"><?php echo $poli['nama_poli']; ?></h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="alert alert-warning text-center h-100">
                                                        <h6 class="mb-2">Menunggu</h6>
                                                        <div class="display-4 font-weight-bold">
                                                            <?php echo $poli['jml_menunggu']; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="alert alert-primary text-center h-100">
                                                        <h6 class="mb-2">Diperiksa</h6>
                                                        <div class="display-4 font-weight-bold">
                                                            <?php echo $poli['jml_diperiksa']; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="alert alert-success text-center h-100">
                                                        <h6 class="mb-2">Selesai</h6>
                                                        <div class="display-4 font-weight-bold">
                                                            <?php echo $poli['jml_selesai']; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CSS untuk animasi -->
    <style>
    .carousel-item {
        transition: transform 0.6s ease-in-out;
    }

    .carousel-inner {
        overflow: hidden;
    }

    .card {
        margin: 0 auto;
        border: none;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 30px rgba(0,0,0,0.2);
    }

    .alert {
        transition: all 0.3s ease;
    }

    .alert:hover {
        transform: scale(1.02);
    }
    </style>

    <!-- Script untuk carousel -->
    <script>
    $(document).ready(function() {
        // Inisialisasi carousel
        var carousel = new bootstrap.Carousel(document.getElementById('antrianCarousel'), {
            interval: 10000, // Waktu pergantian slide (10 detik)
            wrap: true,      // Berputar terus
            ride: 'carousel' // Mulai otomatis
        });

        // Auto refresh data setiap 30 detik
        setInterval(function() {
            $.ajax({
                url: window.location.href,
                success: function(data) {
                    var newDoc = new DOMParser().parseFromString(data, 'text/html');
                    var newCarousel = newDoc.querySelector('#antrianCarousel .carousel-inner').innerHTML;
                    $('#antrianCarousel .carousel-inner').html(newCarousel);
                }
            });
        }, 30000);
    });
    </script>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 Poliklinik. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 