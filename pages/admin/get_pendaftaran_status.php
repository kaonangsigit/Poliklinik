<?php
include_once("../../config/koneksi.php");

header('Content-Type: application/json');

try {
    $query = "SELECT id, status FROM daftar_poli WHERE DATE(created_at) = CURDATE()";
    $result = mysqli_query($koneksi, $query);
    
    $data = [];
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'id' => $row['id'],
            'status' => $row['status']
        ];
    }
    
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}