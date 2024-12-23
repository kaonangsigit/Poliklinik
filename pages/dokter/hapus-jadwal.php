<?php
include_once("../../config/koneksi.php");
session_start();

if(isset($_GET['id'])) {
    $id_jadwal = $_GET['id'];
    $id_dokter = $_SESSION['user_id'];
    
    // Cek apakah jadwal digunakan di daftar_poli
    $query_check = "SELECT dp.* FROM daftar_poli dp 
                   JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id 
                   WHERE jp.id = ? AND jp.id_dokter = ?";
    $stmt_check = mysqli_prepare($koneksi, $query_check);
    mysqli_stmt_bind_param($stmt_check, "ii", $id_jadwal, $id_dokter);
    mysqli_stmt_execute($stmt_check);
    $result = mysqli_stmt_get_result($stmt_check);
    
    if(mysqli_num_rows($result) > 0) {
        echo "<script>
            alert('Jadwal tidak dapat dihapus karena sudah digunakan!');
            window.location.href = 'jadwal-periksa.php';
        </script>";
    } else {
        // Hapus jadwal
        $query_delete = "DELETE FROM jadwal_periksa WHERE id = ? AND id_dokter = ?";
        $stmt_delete = mysqli_prepare($koneksi, $query_delete);
        mysqli_stmt_bind_param($stmt_delete, "ii", $id_jadwal, $id_dokter);
        
        if(mysqli_stmt_execute($stmt_delete)) {
            echo "<script>
                alert('Jadwal berhasil dihapus!');
                window.location.href = 'jadwal-periksa.php';
            </script>";
        } else {
            echo "<script>
                alert('Gagal menghapus jadwal!');
                window.location.href = 'jadwal-periksa.php';
            </script>";
        }
    }
} else {
    header("Location: jadwal-periksa.php");
}
?> 