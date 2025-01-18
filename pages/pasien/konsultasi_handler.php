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
        case 'tambah_konsultasi':
            if(!isset($_POST['id_dokter']) || !isset($_POST['subject']) || !isset($_POST['pertanyaan'])) {
                echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
                exit;
            }
            
            $id_dokter = mysqli_real_escape_string($koneksi, $_POST['id_dokter']);
            $subject = mysqli_real_escape_string($koneksi, $_POST['subject']);
            $pertanyaan = mysqli_real_escape_string($koneksi, $_POST['pertanyaan']);
            
            $query = "INSERT INTO konsultasi (id_pasien, id_dokter, subject, pertanyaan, tgl_konsultasi) 
                     VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "iiss", $id_pasien, $id_dokter, $subject, $pertanyaan);
            
            if(mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menambah konsultasi']);
            }
            break;
            
        case 'hapus_konsultasi':
            if(!isset($_POST['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID konsultasi tidak valid']);
                exit;
            }
            
            $id_konsultasi = mysqli_real_escape_string($koneksi, $_POST['id']);
            
            // Pastikan konsultasi milik pasien yang bersangkutan dan belum dijawab
            $query = "DELETE FROM konsultasi 
                     WHERE id = ? AND id_pasien = ? AND jawaban IS NULL";
            
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "ii", $id_konsultasi, $id_pasien);
            
            if(mysqli_stmt_execute($stmt)) {
                if(mysqli_affected_rows($koneksi) > 0) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Konsultasi tidak dapat dihapus karena sudah dijawab'
                    ]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus konsultasi']);
            }
            break;
    }
}
?> 