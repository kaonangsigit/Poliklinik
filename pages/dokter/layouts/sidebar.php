<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
        <span class="brand-text font-weight-light">Poliklinik</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="index.php" class="d-block">Dr. <?php echo $_SESSION['username']; ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="jadwal-periksa.php" class="nav-link">
                        <i class="nav-icon fas fa-calendar"></i>
                        <p>Jadwal Periksa</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="periksa-pasien.php" class="nav-link">
                        <i class="nav-icon fas fa-stethoscope"></i>
                        <p>Periksa Pasien</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="riwayat-pasien.php" class="nav-link">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Riwayat Pasien</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside> 