<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
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

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
   

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="index.php" class="brand-link">
            <img src="../../assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">Poliklinik</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="dokter.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dokter.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-user-md"></i>
                            <p>Dokter</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="pasien.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'pasien.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Pasien</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="poli.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'poli.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-hospital"></i>
                            <p>Poli</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="obat.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'obat.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-pills"></i>
                            <p>Obat</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="pendaftaran.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'pendaftaran.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-notes-medical"></i>
                            <p>Pendaftaran</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="jadwal.php" class="nav-link">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Jadwal Dokter</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="monitoring-poli.php" class="nav-link">
                            <i class="nav-icon fas fa-desktop"></i>
                            <p>Monitoring Poli</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <?php include_once("layouts/footer.php"); ?>
</div>
</body>
</html> 