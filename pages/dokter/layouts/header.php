<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dokter') {
    header("Location: ../../auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Poliklinik | Admin</title>
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
        }
        
        /* Sidebar Styling */
        .main-sidebar {
            background-color: #343a40;
            transition: all 0.3s ease;
        }
        
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
            background-color: #007bff;
            color: #fff;
        }
        
        .nav-sidebar .nav-link p {
            font-size: 14px;
        }
        
        .nav-sidebar .nav-link {
            padding: 12px;
            margin: 4px 8px;
            border-radius: 8px;
        }
        
        .nav-sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }
        
        /* Brand Logo */
        .brand-link {
            border-bottom: 1px solid #4b545c;
            padding: 15px;
        }
        
        .brand-link .brand-image {
            margin-top: -3px;
        }
        
        /* Content Styling */
        .content-wrapper {
            background: #f4f6f9;
        }
        
        .content-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        /* Card Styling */
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,.125);
            padding: 1.25rem;
        }
        
        /* Button Styling */
        .btn {
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            font-size: 14px;
        }
        
        /* Table Styling */
        .table {
            font-size: 14px;
        }
        
        .table thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }
        
        /* Responsive Sidebar */
        @media (max-width: 768px) {
            .main-sidebar {
                transform: translateX(-250px);
            }
            
            .sidebar-open .main-sidebar {
                transform: translateX(0);
            }
            
            .content-wrapper {
                margin-left: 0 !important;
            }
        }
        
        @media (min-width: 768px) {
            body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper,
            body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer,
            body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header {
                transition: margin-left 0.3s ease-in-out;
                margin-left: 250px;
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button" id="pushMenuButton">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="../../auth/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="index.php" class="brand-link">
            <img src="../../assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" 
                 class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">Poliklinik</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <!-- Add icons to the links using the .nav-icon class -->
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Scripts -->
    <script src="../../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../assets/dist/js/adminlte.min.js"></script>
    <script>
    $(document).ready(function() {
        // Toggle sidebar
        $('#pushMenuButton').on('click', function(e) {
            e.preventDefault();
            if (window.innerWidth > 768) {
                $('body').toggleClass('sidebar-collapse');
                localStorage.setItem('sidebar-state', $('body').hasClass('sidebar-collapse'));
            } else {
                $('body').toggleClass('sidebar-open');
            }
        });

        // Load saved state
        if (localStorage.getItem('sidebar-state') === 'true' && window.innerWidth > 768) {
            $('body').addClass('sidebar-collapse');
        }

        // Close sidebar when clicking outside on mobile
        $(document).on('click', function(e) {
            if (window.innerWidth <= 768) {
                if (!$(e.target).closest('.main-sidebar').length && 
                    !$(e.target).closest('#pushMenuButton').length) {
                    $('body').removeClass('sidebar-open');
                }
            }
        });

        // Handle window resize
        $(window).resize(function() {
            if (window.innerWidth > 768) {
                $('body').removeClass('sidebar-open');
                if (localStorage.getItem('sidebar-state') === 'true') {
                    $('body').addClass('sidebar-collapse');
                }
            }
        });
    });
    </script>
</body>
</html> 