<?php
session_start();
include("../config/koneksi.php");

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    
    // Ambil role dari URL
    $role = $_GET['role'];
    
    if ($role == 'admin') {
        // Login admin
        $query = "SELECT * FROM users WHERE username='$username' AND password='$password' AND role='admin'";
        $result = mysqli_query($koneksi, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'admin';
            
            header("Location: ../pages/admin/index.php");
            exit();
        }
        $error = "Username atau Password salah!";
    } elseif ($role == 'dokter') {
        // Login dokter dengan username dan password
        $query = "SELECT * FROM dokter WHERE username=?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            // Verifikasi password menggunakan password_verify karena menggunakan bcrypt
            if (password_verify($_POST['password'], $data['password'])) {
                $_SESSION['user_id'] = $data['id'];
                $_SESSION['username'] = $data['nama'];
                $_SESSION['role'] = 'dokter';
                $_SESSION['id_poli'] = $data['id_poli'];
                
                header("Location: ../pages/dokter/index.php");
                exit();
            }
        }
        $error = "Username atau Password salah!";
    } else {
        // Login pasien menggunakan username dan password
        $query = "SELECT * FROM pasien WHERE username=?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            // Verifikasi password menggunakan password_verify
            if (password_verify($_POST['password'], $data['password'])) {
                $_SESSION['user_id'] = $data['id'];
                $_SESSION['username'] = $data['nama'];
                $_SESSION['no_rm'] = $data['no_rm'];
                $_SESSION['role'] = 'pasien';
                
                header("Location: ../pages/pasien/index.php");
                exit();
            }
        }
        $error = "Username atau Password salah!";
    }
    

}

$role = isset($_GET['role']) ? $_GET['role'] : '';
$roleTitle = ucfirst($role);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Poliklinik | Login <?php echo $roleTitle; ?></title>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="../index.php">Login <b><?php echo $roleTitle; ?></b></a>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Masuk untuk memulai sesi Anda</p>
            
            <!-- Pesan sukses dari registrasi -->
            <?php if(isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']); 
                    ?>
                </div>
            <?php endif; ?>

            <!-- Pesan error login jika ada -->
            <?php if(isset($error)) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <?php if($role == 'pasien'): ?>
            <form action="" method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="username" 
                           placeholder="Username" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" 
                           id="password" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text" style="cursor: pointer;" 
                             onclick="togglePassword()">
                            <span class="fas fa-eye" id="toggleIcon"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <a href="register.php" class="text-center">Daftar Pasien Baru</a>
                    </div>
                    <div class="col-4">
                        <button type="submit" name="login" class="btn btn-primary btn-block">Masuk</button>
                    </div>
                </div>
            </form>

            <!-- Tambahkan script JavaScript ini sebelum closing body tag -->
            <script>
            function togglePassword() {
                var passwordField = document.getElementById("password");
                var toggleIcon = document.getElementById("toggleIcon");
                
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    toggleIcon.classList.remove("fa-eye");
                    toggleIcon.classList.add("fa-eye-slash");
                } else {
                    passwordField.type = "password";
                    toggleIcon.classList.remove("fa-eye-slash");
                    toggleIcon.classList.add("fa-eye");
                }
            }
            </script>
            <?php else: ?>
            <form action="" method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">
                                Ingat Saya
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" name="login" class="btn btn-primary btn-block">Masuk</button>
                    </div>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.min.js"></script>
</body>
</html> 