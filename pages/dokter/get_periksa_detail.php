<?php
include_once("../../config/koneksi.php");

if(isset($_POST['id_periksa'])) {
    $id_periksa = $_POST['id_periksa'];
    
    // Query untuk mendapatkan data pemeriksaan
    $query = "SELECT p.*, GROUP_CONCAT(dp.id_obat) as obat_ids 
             FROM periksa p
             LEFT JOIN detail_periksa dp ON p.id = dp.id_periksa
             WHERE p.id = ?
             GROUP BY p.id";
             
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_periksa);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($data = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'success' => true,
            'data' => [
                'id' => $data['id'],
                'catatan' => $data['catatan'],
                'obat' => explode(',', $data['obat_ids'])
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Data tidak ditemukan'
        ]);
    }
}
?> 