<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_dokter = $_SESSION['user_id'];

// Ambil data dokter
$query = "SELECT d.*, p.nama_poli 
          FROM dokter d 
          JOIN poli p ON d.id_poli = p.id 
          WHERE d.id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_dokter);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$dokter = mysqli_fetch_assoc($result);

// Proses update profile
if(isset($_POST['update_profile'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    
    try {
        // Cek jika ada update password
        $update_password = false;
        if(!empty($password_lama) && !empty($password_baru)) {
            // Verifikasi password lama
            if(!password_verify($password_lama, $dokter['password'])) {
                throw new Exception("Password lama tidak sesuai!");
            }
            
            // Validasi password baru
            if($password_baru !== $konfirmasi_password) {
                throw new Exception("Konfirmasi password baru tidak sesuai!");
            }
            
            $update_password = true;
            $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
        }
        
        // Update data dokter
        if($update_password) {
            $query_update = "UPDATE dokter 
                           SET nama = ?, 
                               alamat = ?, 
                               no_hp = ?,
                               password = ? 
                           WHERE id = ?";
            $stmt = mysqli_prepare($koneksi, $query_update);
            mysqli_stmt_bind_param($stmt, "ssssi", $nama, $alamat, $no_hp, $password_hash, $id_dokter);
        } else {
            $query_update = "UPDATE dokter 
                           SET nama = ?, 
                               alamat = ?, 
                               no_hp = ?
                           WHERE id = ?";
            $stmt = mysqli_prepare($koneksi, $query_update);
            mysqli_stmt_bind_param($stmt, "sssi", $nama, $alamat, $no_hp, $id_dokter);
        }
        
        if(mysqli_stmt_execute($stmt)) {
            // Refresh data
            $query_refresh = "SELECT d.*, p.nama_poli 
                            FROM dokter d 
                            JOIN poli p ON d.id_poli = p.id 
                            WHERE d.id = ?";
            $stmt_refresh = mysqli_prepare($koneksi, $query_refresh);
            mysqli_stmt_bind_param($stmt_refresh, "i", $id_dokter);
            mysqli_stmt_execute($stmt_refresh);
            $result_refresh = mysqli_stmt_get_result($stmt_refresh);
            $dokter = mysqli_fetch_assoc($result_refresh);
            
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data profil berhasil diperbarui',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    window.location.href = 'profile.php';
                });
            </script>";
        }
    } catch (Exception $e) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{$e->getMessage()}',
                confirmButtonColor: '#dc3545'
            });
        </script>";
    }
}
?>

<!-- Form HTML -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Profil Dokter</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="" id="profileForm">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" 
                                   value="<?php echo htmlspecialchars($dokter['nama']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Poli</label>
                            <input type="text" class="form-control" 
                                   value="<?php echo htmlspecialchars($dokter['nama_poli']); ?>" 
                                   readonly>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea class="form-control" name="alamat" rows="3" 
                                      required><?php echo htmlspecialchars($dokter['alamat']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>No. HP</label>
                            <input type="text" class="form-control" name="no_hp" 
                                   value="<?php echo htmlspecialchars($dokter['no_hp']); ?>" 
                                   pattern="[0-9]+" title="Hanya angka yang diperbolehkan" required>
                        </div>
                        
                        <hr>
                        <h5>Ubah Password</h5>
                        <div class="form-group">
                            <label>Password Lama</label>
                            <input type="password" class="form-control" name="password_lama">
                        </div>
                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" class="form-control" name="password_baru">
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" name="konfirmasi_password">
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Tambahkan SweetAlert2 CSS dan JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Simpan data awal form
    const formInitialData = {
        nama: $('input[name="nama"]').val(),
        alamat: $('textarea[name="alamat"]').val(),
        no_hp: $('input[name="no_hp"]').val()
    };

    // Validasi form sebelum submit
    $('#profileForm').on('submit', function(e) {
        e.preventDefault(); // Mencegah form submit default

        const noHp = $('input[name="no_hp"]').val();
        const passwordLama = $('input[name="password_lama"]').val();
        const passwordBaru = $('input[name="password_baru"]').val();
        const konfirmasiPassword = $('input[name="konfirmasi_password"]').val();

        // Validasi nomor HP
        if (!/^[0-9]+$/.test(noHp)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No. HP hanya boleh berisi angka!',
                confirmButtonColor: '#dc3545'
            });
            return false;
        }

        // Validasi password
        if (passwordLama || passwordBaru || konfirmasiPassword) {
            if (!passwordLama || !passwordBaru || !konfirmasiPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Semua field password harus diisi jika ingin mengubah password!',
                    confirmButtonColor: '#dc3545'
                });
                return false;
            }

            if (passwordBaru !== konfirmasiPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Konfirmasi password baru tidak sesuai!',
                    confirmButtonColor: '#dc3545'
                });
                return false;
            }
        }

        // Submit form jika validasi berhasil
        this.submit();
    });

    // Tampilkan SweetAlert jika ada pesan sukses atau error dari PHP
    <?php if(isset($_POST['update_profile'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data profil berhasil diperbarui',
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            window.location.href = 'profile.php';
        });
    <?php endif; ?>
});
</script>

<?php include_once("layouts/footer.php"); ?> 