<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Poliklinik UDINUS</title>

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
            overflow-x: hidden;
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

        /* Hero Section dengan Background UDINUS */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('assets/img/udinus.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            color: white;
            position: relative;
            padding: 120px 0 220px;
            margin-bottom: -100px;
        }

        .hero-content {
            padding-top: 15vh;
            text-align: center;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            animation: fadeInDown 1s ease;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 300;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease 0.5s;
            animation-fill-mode: both;
        }

        /* Login Cards */
        .login-section {
            position: relative;
            z-index: 10;
            padding: 50px 0 100px;
        }

        .login-card {
            height: 100%;
            margin-bottom: 30px;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            text-align: center;
        }

        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .login-icon {
            font-size: 3rem;
            color: #0d6efd;
            margin-bottom: 20px;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .login-btn {
            background: #0d6efd;
            color: white;
            padding: 10px 30px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin-top: 15px;
        }

        .login-btn:hover {
            background: #0b5ed7;
            transform: scale(1.05);
            color: white;
        }

        /* Footer */
        footer {
            background: #0d6efd;
            color: white;
            padding: 20px 0;
            margin-top: 100px;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
            
            .login-section {
                padding: 30px 0 60px;
            }
            
            .login-card {
                margin-bottom: 20px;
            }
        }

        .layanan-section {
            padding: 100px 0;
            margin-top: 50px;
            background: #f8f9fa;
            position: relative;
            z-index: 1;
        }

        .layanan-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(13, 110, 253, 0.05) 100%);
            z-index: 1;
        }

        .section-title {
            color: #0d6efd;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
        }

        .section-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 3rem;
        }

        .layanan-card {
            height: 100%;
            margin-bottom: 30px;
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .layanan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .layanan-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #0d6efd, #0043a8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            transition: all 0.3s ease;
        }

        .layanan-icon i {
            font-size: 2.5rem;
            color: white;
        }

        .layanan-card:hover .layanan-icon {
            transform: rotateY(360deg);
        }

        .layanan-card h3 {
            color: #333;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .layanan-card p {
            color: #666;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .layanan-features {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: left;
        }

        .layanan-features li {
            color: #666;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .layanan-features li i {
            color: #0d6efd;
            margin-right: 10px;
        }

        @media (max-width: 768px) {
            .layanan-section {
                padding: 60px 0;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .layanan-card {
                margin-bottom: 30px;
            }
        }

        /* Tambahkan animasi hover untuk cards */
        .login-card:hover .login-icon,
        .layanan-card:hover .layanan-icon {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Container Spacing */
        .container {
            position: relative;
            z-index: 2;
        }

        /* Section Spacing */
        section {
            position: relative;
            overflow: hidden;
        }

        /* Perbaikan CSS untuk mengatasi tumpang tindih */
        body {
            padding-top: 76px;
            overflow-x: hidden;
        }

        .hero-section {
            padding: 120px 0 220px;
            margin-bottom: -100px;
        }

        .login-section {
            position: relative;
            z-index: 10;
            padding: 50px 0 100px;
        }

        .layanan-section {
            padding: 100px 0;
            margin-top: 50px;
            background: #f8f9fa;
            position: relative;
            z-index: 1;
        }

        .login-card {
            height: 100%;
            margin-bottom: 30px;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .layanan-card {
            height: 100%;
            margin-bottom: 30px;
            padding: 40px 30px;
        }

        .container {
            position: relative;
            z-index: 2;
        }

        section {
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 80px 0 180px;
            }

            .login-section {
                padding: 30px 0 60px;
            }

            .layanan-section {
                padding: 60px 0;
                margin-top: 30px;
            }

            .login-card,
            .layanan-card {
                margin-bottom: 20px;
            }
        }

        .highlight-section {
            background: white;
            position: relative;
            z-index: 10;
            margin-top: -50px;
        }

        .highlight-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }

        .highlight-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .highlight-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #0d6efd, #0043a8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .highlight-icon i {
            font-size: 1.8rem;
            color: white;
        }

        .highlight-card h4 {
            color: #333;
            font-size: 1.3rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .highlight-card p {
            color: #666;
            margin: 0;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .highlight-section {
                margin-top: -30px;
                padding: 30px 0;
            }
            
            .highlight-card {
                margin-bottom: 20px;
            }
        }

        .footer {
            background: #0d6efd;
            color: white;
            padding: 50px 0 30px;
            margin-top: 100px;
        }

        .footer h5 {
            font-weight: 600;
            margin-bottom: 20px;
        }

        .footer ul li {
            margin-bottom: 10px;
        }

        .footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer a:hover {
            color: white;
            text-decoration: none;
        }

        .footer i {
            margin-right: 10px;
        }

        @media (max-width: 768px) {
            .footer {
                text-align: center;
            }
            
            .footer .col-md-3 {
                margin-top: 30px;
            }
        }

        /* Responsive Design untuk Mobile */
        @media (max-width: 768px) {
            /* Navbar */
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .navbar-nav {
                margin-top: 1rem;
            }
            
            /* Hero Section */
            .hero-section {
                padding: 4rem 0;
            }
            
            .hero-title {
                font-size: 2rem;
                margin-bottom: 1rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            /* Cards */
            .login-card,
            .layanan-card {
                margin-bottom: 2rem;
                padding: 1.5rem;
            }
            
            .login-icon,
            .layanan-icon {
                font-size: 2.5rem;
                margin-bottom: 1rem;
            }
            
            .login-title,
            .layanan-title {
                font-size: 1.3rem;
            }
            
            /* Sections */
            .section-title {
                font-size: 1.8rem;
                margin-bottom: 1rem;
            }
            
            .section-subtitle {
                font-size: 0.9rem;
                margin-bottom: 2rem;
            }
            
            /* Buttons */
            .login-btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            
            /* Footer */
            .footer {
                text-align: center;
                padding: 2rem 0;
            }
            
            .footer .col-md-3 {
                margin-bottom: 2rem;
            }
            
            .footer-title {
                font-size: 1.2rem;
                margin-bottom: 1rem;
            }
            
            /* Spacing */
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            section {
                padding: 3rem 0;
            }
            
            /* Table Responsive */
            .table-responsive {
                overflow-x: auto;
            }
            
            /* Modal */
            .modal-dialog {
                margin: 1rem;
            }
        }

        /* Tambahan untuk perangkat sangat kecil */
        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.8rem;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
            
            .login-card,
            .layanan-card {
                padding: 1rem;
            }
            
            .navbar-brand {
                font-size: 1rem;
            }
        }

        /* Optimasi untuk orientasi landscape pada mobile */
        @media (max-height: 500px) and (orientation: landscape) {
            .hero-section {
                padding: 2rem 0;
            }
            
            .hero-title {
                font-size: 1.5rem;
            }
            
            .section {
                padding: 2rem 0;
            }
        }
    </style>
</head>
<body>
    <!-- 1. Navbar (tetap di posisi fixed-top) -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="assets/img/logo-udinus.png" height="40" alt="Logo UDINUS">
                Poliklinik UDINUS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#layanan">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="antrian.php">Antrian</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 2. Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title" data-aos="fade-up">Poliklinik UDINUS</h1>
                <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">
                    Melayani dengan Sepenuh Hati untuk Kesehatan Anda
                </p>
                <a href="#layanan" class="btn btn-light btn-lg" data-aos="fade-up" data-aos-delay="200">
                    Lihat Layanan Kami
                </a>
            </div>
        </div>
    </section>

    <!-- 3. Highlight Info Section (Jam Operasional, Kontak, Lokasi) -->
    <section class="highlight-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up">
                    <div class="highlight-card">
                        <div class="highlight-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4>Jam Operasional</h4>
                        <p>Senin - Jumat: 08:00 - 20:00<br>Sabtu: 08:00 - 17:00</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="highlight-card">
                        <div class="highlight-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h4>Kontak Darurat</h4>
                        <p>Telepon: (024) 3517261<br>Emergency: 0800-1234-5678</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="highlight-card">
                        <div class="highlight-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4>Lokasi</h4>
                        <p>Jl. Imam Bonjol No.207, Pendrikan Kidul<br>Semarang, Jawa Tengah</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Layanan Section -->
    <section class="layanan-section" id="layanan">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center mb-5">
                    <h2 class="section-title" data-aos="fade-up">Layanan Kami</h2>
                    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">
                        Berbagai layanan kesehatan yang kami sediakan dengan standar pelayanan terbaik
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="layanan-card">
                        <div class="layanan-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <h3>Pemeriksaan Umum</h3>
                        <p>Layanan pemeriksaan kesehatan umum dengan dokter berpengalaman dan peralatan modern</p>
                        <ul class="layanan-features">
                            <li><i class="fas fa-check-circle"></i> Konsultasi Dokter</li>
                            <li><i class="fas fa-check-circle"></i> Pemeriksaan Fisik</li>
                            <li><i class="fas fa-check-circle"></i> Pemeriksaan Dasar</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="layanan-card">
                        <div class="layanan-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h3>Spesialis</h3>
                        <p>Konsultasi dengan dokter spesialis berpengalaman sesuai kebutuhan Anda</p>
                        <ul class="layanan-features">
                            <li><i class="fas fa-check-circle"></i> Penyakit Dalam</li>
                            <li><i class="fas fa-check-circle"></i> Anak</li>
                            <li><i class="fas fa-check-circle"></i> Kandungan</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="layanan-card">
                        <div class="layanan-icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <h3>Farmasi</h3>
                        <p>Layanan farmasi lengkap dengan apoteker profesional dan obat berkualitas</p>
                        <ul class="layanan-features">
                            <li><i class="fas fa-check-circle"></i> Obat Resep</li>
                            <li><i class="fas fa-check-circle"></i> Konsultasi Obat</li>
                            <li><i class="fas fa-check-circle"></i> Informasi Dosis</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Login Section -->
    <section class="login-section" id="login">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center mb-5">
                    <h2 class="section-title" data-aos="fade-up">Area Login</h2>
                    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">
                        Silakan login sesuai dengan akses yang Anda miliki
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="login-card">
                        <i class="fas fa-user-shield login-icon"></i>
                        <h3 class="login-title">Admin</h3>
                        <p>Login sebagai administrator sistem</p>
                        <a href="auth/login.php?role=admin" class="login-btn">
                            <i class="fas fa-sign-in-alt me-2"></i>Login Admin
                        </a>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="login-card">
                        <i class="fas fa-user-md login-icon"></i>
                        <h3 class="login-title">Dokter</h3>
                        <p>Login sebagai dokter poliklinik</p>
                        <a href="auth/login.php?role=dokter" class="login-btn">
                            <i class="fas fa-sign-in-alt me-2"></i>Login Dokter
                        </a>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="login-card">
                        <i class="fas fa-user login-icon"></i>
                        <h3 class="login-title">Pasien</h3>
                        <p>Login sebagai pasien</p>
                        <a href="auth/login.php?role=pasien" class="login-btn">
                            <i class="fas fa-sign-in-alt me-2"></i>Login Pasien
                        </a>
                        <a href="auth/register.php" class="login-btn mt-2" style="background: #28a745;">
                            <i class="fas fa-user-plus me-2"></i>Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Poliklinik UDINUS</h5>
                    <p>Memberikan pelayanan kesehatan terbaik untuk civitas akademika UDINUS dan masyarakat umum.</p>
                </div>
                <div class="col-md-3">
                    <h5>Link Cepat</h5>
                    <ul class="list-unstyled">
                        <li><a href="#home">Beranda</a></li>
                        <li><a href="#layanan">Layanan</a></li>
                        <li><a href="antrian.php">Antrian</a></li>
                        <li><a href="#login">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone"></i> (024) 3517261</li>
                        <li><i class="fas fa-envelope"></i> info@poliklinik.dinus.ac.id</li>
                    </ul>
                </div>
            </div>
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