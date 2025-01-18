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
        case 'get_tanggapan':
            if(!isset($_POST['id_konsultasi'])) {
                echo json_encode(['success' => false, 'message' => 'ID konsultasi tidak valid']);
                exit;
            }
            
            $id_konsultasi = mysqli_real_escape_string($koneksi, $_POST['id_konsultasi']);
            
            $query = "SELECT jawaban FROM konsultasi WHERE id = ? AND id_dokter = ?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "ii", $id_konsultasi, $id_dokter);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if($row = mysqli_fetch_assoc($result)) {
                echo json_encode(['success' => true, 'data' => $row]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            break;
            
        case 'save_tanggapan':
            if(!isset($_POST['id_konsultasi']) || !isset($_POST['jawaban'])) {
                echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
                exit;
            }
            
            $id_konsultasi = mysqli_real_escape_string($koneksi, $_POST['id_konsultasi']);
            $jawaban = mysqli_real_escape_string($koneksi, $_POST['jawaban']);
            
            $query = "UPDATE konsultasi SET jawaban = ? WHERE id = ? AND id_dokter = ?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "sii", $jawaban, $id_konsultasi, $id_dokter);
            
            if(mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan tanggapan']);
            }
            break;
    }
}
?> 