<?php
session_start();
include("../config/koneksi.php");

if(isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_pass'])) {
    $saved_username = $_COOKIE['remember_user'];
    $saved_password = $_COOKIE['remember_pass'];
} else {
    $saved_username = '';
    $saved_password = '';
}

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
            
            if(isset($_POST['remember'])) {
                setcookie('remember_user', $username, time() + (86400 * 30), "/"); // 30 hari
                setcookie('remember_pass', $_POST['password'], time() + (86400 * 30), "/");
            } else {
                setcookie('remember_user', '', time() - 3600, "/");
                setcookie('remember_pass', '', time() - 3600, "/");
            }
            
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
                
                if(isset($_POST['remember'])) {
                    setcookie('remember_user', $username, time() + (86400 * 30), "/");
                    setcookie('remember_pass', $_POST['password'], time() + (86400 * 30), "/");
                } else {
                    setcookie('remember_user', '', time() - 3600, "/");
                    setcookie('remember_pass', '', time() - 3600, "/");
                }
                
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
                
                if(isset($_POST['remember'])) {
                    setcookie('remember_user', $username, time() + (86400 * 30), "/");
                    setcookie('remember_pass', $_POST['password'], time() + (86400 * 30), "/");
                } else {
                    setcookie('remember_user', '', time() - 3600, "/");
                    setcookie('remember_pass', '', time() - 3600, "/");
                }
                
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
    <style>
        .login-page {
            background: linear-gradient(135deg, #1e88e5 0%, #0d47a1 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            width: 400px;
            margin: 0 auto;
        }
        .card {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-radius: 15px;
            border: none;
            border-top: 5px solid #1e88e5;
        }
        .login-logo {
            margin-bottom: 20px;
        }
        .login-logo a {
            color: white;
            font-size: 24px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .login-box-msg {
            color: #1e88e5;
            font-weight: 600;
            font-size: 1.1em;
        }
        .input-group {
            margin-bottom: 1.5rem !important;
        }
        .input-group-text {
            border: none;
            background: transparent;
            color: #1e88e5;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 2px solid #e3f2fd;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #1e88e5;
            box-shadow: 0 0 0 0.2rem rgba(30,136,229,0.15);
        }
        .btn-primary {
            background: #1e88e5;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #1976d2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30,136,229,0.3);
        }
        .toggle-password {
            cursor: pointer;
            padding: 8px;
            color: #1e88e5;
            transition: all 0.3s ease;
        }
        .toggle-password:hover {
            color: #1976d2;
        }
        .icheck-primary label {
            color: #555;
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
        }
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .register-link a {
            color: #1e88e5;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .register-link a:hover {
            color: #1976d2;
            text-decoration: underline;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="../index.php">Login <b><?php echo $roleTitle; ?></b></a>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Masuk untuk memulai sesi Anda</p>
            
            <?php if(isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']); 
                    ?>
                </div>
            <?php endif; ?>

            <?php if(isset($error)) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <form action="" method="post">
                <div class="input-group">
                    <input type="text" class="form-control" name="username" 
                           placeholder="Username" required 
                           value="<?php echo isset($saved_username) ? $saved_username : ''; ?>">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group">
                    <input type="password" class="form-control" name="password" 
                           id="password" placeholder="Password" required
                           value="<?php echo isset($saved_password) ? $saved_password : ''; ?>">
                    <div class="input-group-append">
                        <div class="input-group-text toggle-password" onclick="togglePassword()">
                            <span class="fas fa-eye" id="toggleIcon"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">
                                Ingat Saya
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" name="login" class="btn btn-primary btn-block">
                            Masuk
                        </button>
                    </div>
                </div>
            </form>

            <?php if($role == 'pasien'): ?>
                <div class="register-link">
                    Belum punya akun? 
                    <a href="../auth/register.php">
                        Daftar Sekarang
                    </a>
                </div>
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
<script>
function togglePassword() {
    const passwordField = document.getElementById("password");
    const toggleIcon = document.getElementById("toggleIcon");
    
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
</body>
</html> 