<?php
include_once("../../config/koneksi.php");
session_start();

header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pasien') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id_pasien = $_SESSION['user_id'];

if(isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'get_chat':
            if(!isset($_POST['id_dokter'])) {
                echo json_encode(['success' => false, 'message' => 'ID dokter tidak valid']);
                exit;
            }
            
            $id_dokter = mysqli_real_escape_string($koneksi, $_POST['id_dokter']);
            
            $query = "SELECT * FROM konsultasi 
                     WHERE id_pasien = ? AND id_dokter = ?
                     ORDER BY waktu ASC";
            
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "ii", $id_pasien, $id_dokter);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $messages = [];
            while($row = mysqli_fetch_assoc($result)) {
                $messages[] = $row;
            }
            
            echo json_encode(['success' => true, 'messages' => $messages]);
            break;
            
        case 'send_message':
            if(!isset($_POST['id_dokter']) || !isset($_POST['pesan'])) {
                echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
                exit;
            }
            
            $id_dokter = mysqli_real_escape_string($koneksi, $_POST['id_dokter']);
            $pesan = mysqli_real_escape_string($koneksi, $_POST['pesan']);
            
            $query = "INSERT INTO konsultasi (id_pasien, id_dokter, pesan, pengirim) 
                     VALUES (?, ?, ?, 'pasien')";
            
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "iis", $id_pasien, $id_dokter, $pesan);
            
            if(mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mengirim pesan']);
            }
            break;
            
        case 'mulai_konsultasi':
            if(!isset($_POST['id_dokter']) || !isset($_POST['pesan'])) {
                echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
                exit;
            }
            
            $id_dokter = mysqli_real_escape_string($koneksi, $_POST['id_dokter']);
            $pesan = mysqli_real_escape_string($koneksi, $_POST['pesan']);
            
            $query = "INSERT INTO konsultasi (id_pasien, id_dokter, pesan, pengirim) 
                     VALUES (?, ?, ?, 'pasien')";
            
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "iis", $id_pasien, $id_dokter, $pesan);
            
            if(mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal memulai konsultasi']);
            }
            break;
    }
}
?> 