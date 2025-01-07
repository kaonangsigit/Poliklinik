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
    <title>Poliklinik | Dokter</title>
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
            font-size: 16px;
        }
        
        /* Responsive Sidebar */
        @media (max-width: 768px) {
            .sidebar-mini.sidebar-collapse .main-sidebar {
                width: 4.6rem !important;
            }
            
            .sidebar-mini.sidebar-collapse .content-wrapper {
                margin-left: 4.6rem !important;
            }
            
            .sidebar-mini.sidebar-collapse .main-sidebar .nav-sidebar .nav-link {
                padding: 10px 15px;
            }
            
            .sidebar-mini.sidebar-collapse .main-sidebar .brand-text,
            .sidebar-mini.sidebar-collapse .main-sidebar .user-panel .info {
                display: none;
            }
        }
        
        @media (min-width: 768px) {
            .sidebar-mini.sidebar-collapse .main-sidebar:hover {
                width: 250px !important;
            }
            
            .sidebar-mini.sidebar-collapse .main-sidebar:hover .brand-text,
            .sidebar-mini.sidebar-collapse .main-sidebar:hover .user-panel .info {
                display: inline-block;
            }
        }
        
        /* Sidebar Styling */
        .main-sidebar {
            background-color: #343a40;
            transition: width 0.3s ease-in-out;
        }
        
        .nav-sidebar .nav-link {
            color: #c2c7d0 !important;
            padding: 12px 20px;
            margin: 4px 0;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .nav-sidebar .nav-link.active {
            background-color: #007bff !important;
            color: #fff !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .nav-sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: #fff !important;
            transform: translateX(5px);
        }
        
        /* Content Styling */
        .content-wrapper {
            background: #f4f6f9;
            min-height: 100vh;
            transition: margin-left 0.3s ease-in-out;
        }
        
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        
        .small-box {
            border-radius: 0.5rem;
            transition: transform 0.3s ease;
        }
        
        .small-box:hover {
            transform: translateY(-5px);
        }
        
        /* Navbar Styling */
        .main-header {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-nav .nav-link {
            padding: 0.5rem 1rem;
            transition: color 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: #007bff !important;
        }

        /* Table Styling */
        .table {
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 0.5rem;
            border: none;
        }

        .modal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        /* Form Styling */
        .form-control {
            border-radius: 0.25rem;
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
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

<!-- Scripts -->
<script src="../../assets/plugins/jquery/jquery.min.js"></script>
<script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/dist/js/adminlte.min.js"></script>
<script src="../../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- SweetAlert2 -->
<link rel="stylesheet" href="../../plugins/sweetalert2/sweetalert2.min.css">
<script src="../../plugins/sweetalert2/sweetalert2.all.min.js"></script>

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

    // Load saved sidebar state
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

    // Initialize DataTables
    $('.table').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true
    });
});
</script>
</body>
</html> 