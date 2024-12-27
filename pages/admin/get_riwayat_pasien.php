<?php
include_once("../../config/koneksi.php");

header('Content-Type: application/json');

if (isset($_POST['id'])) {
    try {
        $id_pasien = $_POST['id'];
        
        // Query untuk mendapatkan data riwayat periksa
        $query = "SELECT 
                    p.id,
                    p.tgl_periksa,
                    pol.nama_poli,
                    d.nama as nama_dokter,
                    p.catatan,
                    GROUP_CONCAT(o.nama_obat SEPARATOR ', ') as obat
                 FROM periksa p
                 JOIN daftar_poli dp ON p.id_daftar_poli = dp.id
                 JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                 JOIN dokter d ON jp.id_dokter = d.id
                 JOIN poli pol ON d.id_poli = pol.id
                 LEFT JOIN detail_periksa dp2 ON p.id = dp2.id_periksa
                 LEFT JOIN obat o ON dp2.id_obat = o.id
                 WHERE dp.id_pasien = ?
                 GROUP BY p.id
                 ORDER BY p.tgl_periksa DESC";
                 
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("i", $id_pasien);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $riwayat = [];
        while ($row = $result->fetch_assoc()) {
            $riwayat[] = [
                'id' => $row['id'],
                'tgl_periksa' => date('d/m/Y H:i', strtotime($row['tgl_periksa'])),
                'nama_poli' => $row['nama_poli'],
                'nama_dokter' => $row['nama_dokter'],
                'catatan' => $row['catatan'],
                'obat' => $row['obat'] ?: '-'
            ];
        }
        
        echo json_encode(['success' => true, 'data' => $riwayat]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID pasien tidak diberikan']);
} 