<?php
include_once("../../config/koneksi.php");

header('Content-Type: application/json');

if(isset($_POST['id_periksa'])) {
    try {
        $id_periksa = $_POST['id_periksa'];
        $catatan = $_POST['catatan'];
        $obat = isset($_POST['obat']) ? $_POST['obat'] : [];
        
        mysqli_begin_transaction($koneksi);
        
        // Update catatan pemeriksaan
        $query_update = "UPDATE periksa SET catatan = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query_update);
        mysqli_stmt_bind_param($stmt, "si", $catatan, $id_periksa);
        mysqli_stmt_execute($stmt);
        
        // Hapus detail obat lama
        $query_delete = "DELETE FROM detail_periksa WHERE id_periksa = ?";
        $stmt = mysqli_prepare($koneksi, $query_delete);
        mysqli_stmt_bind_param($stmt, "i", $id_periksa);
        mysqli_stmt_execute($stmt);
        
        // Insert detail obat baru
        if(!empty($obat)) {
            $query_insert = "INSERT INTO detail_periksa (id_periksa, id_obat) VALUES (?, ?)";
            $stmt = mysqli_prepare($koneksi, $query_insert);
            
            foreach($obat as $id_obat) {
                mysqli_stmt_bind_param($stmt, "ii", $id_periksa, $id_obat);
                mysqli_stmt_execute($stmt);
            }
        }
        
        mysqli_commit($koneksi);
        
        echo json_encode([
            'success' => true,
            'message' => 'Data berhasil diupdate'
        ]);
        
    } catch(Exception $e) {
        mysqli_rollback($koneksi);
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Data tidak lengkap'
    ]);
} 