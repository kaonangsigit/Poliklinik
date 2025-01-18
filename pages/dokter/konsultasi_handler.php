<?php
include_once("../../config/koneksi.php");
session_start();

header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dokter') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id_dokter = $_SESSION['user_id'];

if(isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'tanggapi':
            if(!isset($_POST['id_konsultasi']) || !isset($_POST['tanggapan'])) {
                echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
                exit;
            }
            
            $id_konsultasi = mysqli_real_escape_string($koneksi, $_POST['id_konsultasi']);
            $tanggapan = mysqli_real_escape_string($koneksi, $_POST['tanggapan']);
            
            // Pastikan konsultasi milik dokter yang bersangkutan
            $query = "UPDATE konsultasi 
                     SET tanggapan = ? 
                     WHERE id = ? AND id_dokter = ?";
            
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "sii", $tanggapan, $id_konsultasi, $id_dokter);
            
            if(mysqli_stmt_execute($stmt)) {
                if(mysqli_affected_rows($koneksi) > 0) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Konsultasi tidak ditemukan atau bukan milik Anda'
                    ]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan tanggapan']);
            }
            break;
    }
}
?> 