<?php
session_start();
include("../config/koneksi.php");

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_ktp = mysqli_real_escape_string($koneksi, $_POST['no_ktp']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    
    // Cek apakah username sudah digunakan
    $check_username = mysqli_query($koneksi, "SELECT * FROM pasien WHERE username = '$username'");
    if (mysqli_num_rows($check_username) > 0) {
        $error = "Username sudah digunakan! Silakan pilih username lain.";
    } else {
        // Cek apakah no KTP sudah terdaftar
        $check_ktp = mysqli_query($koneksi, "SELECT * FROM pasien WHERE no_ktp = '$no_ktp'");
        if (mysqli_num_rows($check_ktp) > 0) {
            $error = "No KTP sudah terdaftar! Silakan login menggunakan username dan password Anda.";
        } else {
            // Generate nomor rekam medis (RM)
            $tahun_bulan = date('Ym');
            $query_rm = "SELECT MAX(CAST(SUBSTRING_INDEX(no_rm, '-', -1) AS UNSIGNED)) as max_urut 
                        FROM pasien WHERE no_rm LIKE '$tahun_bulan%'";
            $result_rm = mysqli_query($koneksi, $query_rm);
            $data_rm = mysqli_fetch_assoc($result_rm);
            $nomor_urut = ($data_rm['max_urut'] ?? 0) + 1;
            $no_rm = $tahun_bulan . "-" . str_pad($nomor_urut, 3, "0", STR_PAD_LEFT);
            
            // Insert data pasien baru
            $query = "INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm, username, password) 
                     VALUES ('$nama', '$alamat', '$no_ktp', '$no_hp', '$no_rm', '$username', '$password')";
            
            if (mysqli_query($koneksi, $query)) {
                $_SESSION['success_message'] = "Pendaftaran berhasil! <br>
                       Silakan login menggunakan:<br>
                       Username: <strong>$username</strong>";
                header("Location: login.php?role=pasien");
                exit();
            } else {
                $error = "Pendaftaran gagal! Silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Poliklinik | Registrasi Pasien</title>
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <style>
        .register-page {
            background: linear-gradient(135deg, #1e88e5 0%, #0d47a1 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-box {
            width: 420px;
            margin: 0 auto;
        }
        .card {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-radius: 15px;
            border: none;
            border-top: 5px solid #1e88e5;
        }
        .register-logo a {
            color: white;
            font-size: 24px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .login-box-msg {
            color: #1e88e5;
            font-weight: 600;
            font-size: 1.1em;
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
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #1e88e5;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .login-link a:hover {
            color: #1976d2;
            text-decoration: underline;
        }
    </style>
</head>
<body class="hold-transition register-page">
<div class="register-box">
    <div class="register-logo">
        <a href="../index.php"><b>Registrasi</b> Pasien</a>
    </div>
    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg">Daftar sebagai pasien baru</p>

            <?php if(isset($success)) { ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php } ?>

            <?php if(isset($error)) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <form action="" method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="nama" placeholder="Nama Lengkap" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <textarea class="form-control" name="alamat" placeholder="Alamat Lengkap" required></textarea>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-home"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="no_ktp" 
                           placeholder="Nomor KTP" pattern="[0-9]{16}" 
                           title="Nomor KTP harus 16 digit" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-id-card"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="no_hp" 
                           placeholder="Nomor HP" pattern="[0-9]{10,13}" 
                           title="Nomor HP harus 10-13 digit" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-phone"></span>
                        </div>
                    </div>
                </div>
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
                    <input type="text" class="form-control" name="password" 
                           id="password" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text" style="cursor: pointer;" 
                             onclick="togglePassword()">
                            <span class="fas fa-eye-slash" id="toggleIcon"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <a href="login.php?role=pasien" class="text-center">Sudah punya akun?</a>
                    </div>
                    <div class="col-4">
                        <button type="submit" name="register" class="btn btn-primary btn-block">Daftar</button>
                    </div>
                </div>
            </form>
            
            <div class="login-link">
                Sudah punya akun? <a href="login.php?role=pasien">Login</a>
            </div>
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
</body>
</html> 