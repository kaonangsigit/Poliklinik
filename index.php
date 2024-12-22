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
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Navbar Styles */
        .navbar {
            background: rgba(13, 110, 253, 0.95) !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            padding: 10px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        /* Hero Section Styles */
        .hero-section {
            background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.5)), 
                        url('assets/img/hospital.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-text {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .hero-btn {
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .hero-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        /* Service Cards Styles */
        .service-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s ease;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .service-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .service-icon {
            transition: all 0.4s ease;
        }

        .service-card:hover .service-icon {
            transform: scale(1.2);
            color: #0d6efd;
        }

        /* Login Section Styles */
        .login-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 100px 0;
        }

        .login-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s ease;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .login-icon {
            transition: all 0.4s ease;
        }

        .login-card:hover .login-icon {
            transform: scale(1.2) rotate(10deg);
        }

        .login-btn {
            border-radius: 50px;
            padding: 10px 30px;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
        }

        /* Footer Styles */
        footer {
            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
            padding: 30px 0;
        }

        /* Animations */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <!-- Navbar dengan efek scroll -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
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
                        <a class="nav-link" href="antrian.php">Antrian</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section dengan parallax dan animasi -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                <h1 class="hero-title">Selamat Datang di Poliklinik</h1>
                <p class="hero-text">Memberikan pelayanan kesehatan terbaik untuk Anda dan keluarga</p>
                <a href="#login" class="btn btn-primary hero-btn">
                    <i class="fas fa-sign-in-alt me-2"></i>Login Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Layanan Section dengan animasi -->
    <section class="py-5" id="layanan">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Layanan Kami</h2>
            <div class="row">
                <!-- Service cards dengan animasi -->
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card service-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Dokter Profesional</h5>
                            <p class="card-text">Ditangani oleh dokter-dokter profesional dan berpengalaman</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card service-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-heartbeat fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Pelayanan 24 Jam</h5>
                            <p class="card-text">Siap melayani kebutuhan kesehatan Anda selama 24 jam</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
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

    <!-- Login Section dengan animasi -->
    <section class="login-section" id="login">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Login</h2>
            <div class="row justify-content-center">
                <!-- Login cards dengan animasi -->
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card login-card">
                        <div class="card-body text-center">
                            <i class="fas fa-user-shield fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Admin</h5>
                            <p class="card-text">Login sebagai administrator</p>
                            <a href="auth/login.php?role=admin" class="btn btn-primary">Login Admin</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card login-card">
                        <div class="card-body text-center">
                            <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Dokter</h5>
                            <p class="card-text">Login sebagai dokter</p>
                            <a href="auth/login.php?role=dokter" class="btn btn-primary">Login Dokter</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card login-card">
                        <div class="card-body text-center">
                            <i class="fas fa-user fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Pasien</h5>
                            <p class="card-text">Login sebagai pasien</p>
                            <a href="auth/login.php?role=pasien" class="btn btn-primary mb-2">Login Pasien</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 Poliklinik. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Inisialisasi AOS
        AOS.init();

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').classList.add('scrolled');
            } else {
                document.querySelector('.navbar').classList.remove('scrolled');
            }
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html> 