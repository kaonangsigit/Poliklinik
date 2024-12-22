<?php
include_once("../../config/koneksi.php");

header('Content-Type: application/json');

if (isset($_POST['id'])) {
    try {
        $id = $_POST['id'];
        
        // Query untuk mendapatkan data pasien saja
        $query = "SELECT * FROM pasien WHERE id = ?";
                 
        $stmt = $koneksi->prepare($query);
        if ($stmt === false) {
            throw new Exception('Prepare statement failed: ' . $koneksi->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Format data sebelum dikirim
            $response = [
                'id' => $row['id'],
                'nama' => $row['nama'],
                'no_rm' => $row['no_rm'],
                'no_ktp' => $row['no_ktp'],
                'alamat' => $row['alamat'],
                'no_hp' => $row['no_hp'],
                'username' => $row['username']
            ];
            
            echo json_encode($response);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Data pasien tidak ditemukan']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'ID pasien tidak diberikan']);
}
?> 