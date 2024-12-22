<?php
include_once("../../config/koneksi.php");
session_start();

header('Content-Type: application/json');

if(isset($_POST['id_daftar'])) {
    $id_daftar = $_POST['id_daftar'];
    $id_pasien = $_SESSION['user_id'];
    
    try {
        // Gunakan prepared statement untuk keamanan
        $query = "SELECT dp.status, p.nama_poli 
                 FROM daftar_poli dp
                 JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                 JOIN dokter d ON jp.id_dokter = d.id
                 JOIN poli p ON d.id_poli = p.id
                 WHERE dp.id = ? AND dp.id_pasien = ?";
                 
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ii", $id_daftar, $id_pasien);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($row = mysqli_fetch_assoc($result)) {
            echo json_encode([
                'success' => true,
                'status' => $row['status'],
                'poli' => $row['nama_poli']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID pendaftaran tidak ditemukan'
    ]);
} 