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
    <title>Antrian Poliklinik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('assets/img/pattern.png') repeat;
            opacity: 0.1;
            z-index: -1;
        }

        .page-title {
            color: #2d3436;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            position: relative;
            display: inline-block;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50%;
            height: 4px;
            background: linear-gradient(90deg, #4a90e2, #67b26f);
            border-radius: 2px;
        }

        .card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .card-header {
            background: linear-gradient(135deg, #4a90e2, #67b26f);
            padding: 1.5rem;
            border: none;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }

        .alert {
            border: none;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .alert-warning {
            background: linear-gradient(135deg, #ffd3a5 0%, #fd6585 100%);
            color: white;
        }

        .alert-primary {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
        }

        .alert-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        .alert:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .display-4 {
            font-weight: 700;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        /* Carousel Controls */
        .carousel-control-prev,
        .carousel-control-next {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.9);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.8;
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            opacity: 1;
            background: white;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            filter: invert(1) grayscale(100);
        }

        /* Animasi Loading */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .loading {
            animation: pulse 2s infinite;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card {
                margin: 1rem;
            }
            
            .display-4 {
                font-size: 2rem;
            }
            
            .alert {
                margin-bottom: 1rem;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #4a90e2, #67b26f);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #357abd, #4e995c);
        }
    </style>
</head>
<body>
    <section class="py-5" id="antrian">
        <div class="container">
            <h2 class="text-center page-title" data-aos="fade-down">Status Antrian Hari Ini</h2>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div id="antrianCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                        <div class="carousel-inner">
                            <?php foreach($poli_data as $index => $poli) { ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" data-aos="fade-up">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title text-center">
                                                <i class="fas fa-hospital-alt me-2"></i>
                                                <?php echo $poli['nama_poli']; ?>
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="alert alert-warning text-center h-100">
                                                        <i class="fas fa-clock mb-2 fa-2x"></i>
                                                        <h6 class="mb-2">Menunggu</h6>
                                                        <div class="display-4">
                                                            <?php echo $poli['jml_menunggu']; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="alert alert-primary text-center h-100">
                                                        <i class="fas fa-stethoscope mb-2 fa-2x"></i>
                                                        <h6 class="mb-2">Diperiksa</h6>
                                                        <div class="display-4">
                                                            <?php echo $poli['jml_diperiksa']; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="alert alert-success text-center h-100">
                                                        <i class="fas fa-check-circle mb-2 fa-2x"></i>
                                                        <h6 class="mb-2">Selesai</h6>
                                                        <div class="display-4">
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
                        <button class="carousel-control-prev" type="button" data-bs-target="#antrianCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#antrianCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
        
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
    </script>
</body>
</html>