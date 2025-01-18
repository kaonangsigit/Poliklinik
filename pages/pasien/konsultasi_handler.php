<?php
session_start();
include_once("../../config/koneksi.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pasien') {
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
            
            try {
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
                    throw new Exception(mysqli_error($koneksi));
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        case 'edit_konsultasi':
            if(!isset($_POST['id']) || !isset($_POST['subject']) || !isset($_POST['pertanyaan'])) {
                echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
                exit;
            }
            
            try {
                $id = mysqli_real_escape_string($koneksi, $_POST['id']);
                $subject = mysqli_real_escape_string($koneksi, $_POST['subject']);
                $pertanyaan = mysqli_real_escape_string($koneksi, $_POST['pertanyaan']);
                
                // Cek apakah konsultasi sudah ditanggapi
                $check_query = "SELECT tanggapan FROM konsultasi WHERE id = ? AND id_pasien = ?";
                $check_stmt = mysqli_prepare($koneksi, $check_query);
                mysqli_stmt_bind_param($check_stmt, "ii", $id, $id_pasien);
                mysqli_stmt_execute($check_stmt);
                $result = mysqli_stmt_get_result($check_stmt);
                $row = mysqli_fetch_assoc($result);
                
                if($row && $row['tanggapan'] !== null) {
                    echo json_encode(['success' => false, 'message' => 'Konsultasi yang sudah ditanggapi tidak dapat diedit']);
                    exit;
                }
                
                $query = "UPDATE konsultasi SET subject = ?, pertanyaan = ? WHERE id = ? AND id_pasien = ?";
                $stmt = mysqli_prepare($koneksi, $query);
                mysqli_stmt_bind_param($stmt, "ssii", $subject, $pertanyaan, $id, $id_pasien);
                
                if(mysqli_stmt_execute($stmt)) {
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception(mysqli_error($koneksi));
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        case 'hapus_konsultasi':
            if(!isset($_POST['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
                exit;
            }
            
            try {
                $id = mysqli_real_escape_string($koneksi, $_POST['id']);
                
                // Cek apakah konsultasi sudah ditanggapi
                $check_query = "SELECT tanggapan FROM konsultasi WHERE id = ? AND id_pasien = ?";
                $check_stmt = mysqli_prepare($koneksi, $check_query);
                mysqli_stmt_bind_param($check_stmt, "ii", $id, $id_pasien);
                mysqli_stmt_execute($check_stmt);
                $result = mysqli_stmt_get_result($check_stmt);
                $row = mysqli_fetch_assoc($result);
                
                if($row && $row['tanggapan'] !== null) {
                    echo json_encode(['success' => false, 'message' => 'Konsultasi yang sudah ditanggapi tidak dapat dihapus']);
                    exit;
                }
                
                $query = "DELETE FROM konsultasi WHERE id = ? AND id_pasien = ?";
                $stmt = mysqli_prepare($koneksi, $query);
                mysqli_stmt_bind_param($stmt, "ii", $id, $id_pasien);
                
                if(mysqli_stmt_execute($stmt)) {
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception(mysqli_error($koneksi));
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        case 'get_konsultasi':
            if(!isset($_POST['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
                exit;
            }
            
            try {
                $id = mysqli_real_escape_string($koneksi, $_POST['id']);
                
                $query = "SELECT * FROM konsultasi WHERE id = ? AND id_pasien = ?";
                $stmt = mysqli_prepare($koneksi, $query);
                mysqli_stmt_bind_param($stmt, "ii", $id, $id_pasien);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if($row = mysqli_fetch_assoc($result)) {
                    echo json_encode(['success' => true, 'data' => $row]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;
    }
}
?> 