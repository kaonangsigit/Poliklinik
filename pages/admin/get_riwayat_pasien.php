<?php
include_once("../../config/koneksi.php");
date_default_timezone_set('Asia/Jakarta');

header('Content-Type: application/json');

if (isset($_POST['id'])) {
    try {
        $id_pasien = $_POST['id'];
        
        // Query untuk mendapatkan semua riwayat tanpa batasan waktu
        $query = "SELECT 
                    p.id,
                    dp.created_at as tgl_periksa,
                    pol.nama_poli,
                    d.nama as nama_dokter,
                    dp.keluhan,
                    p.catatan,
                    p.biaya_periksa,
                    GROUP_CONCAT(DISTINCT o.nama_obat SEPARATOR ', ') as obat
                 FROM daftar_poli dp
                 LEFT JOIN periksa p ON dp.id = p.id_daftar_poli
                 JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                 JOIN dokter d ON jp.id_dokter = d.id
                 JOIN poli pol ON d.id_poli = pol.id
                 LEFT JOIN detail_periksa dp2 ON p.id = dp2.id_periksa
                 LEFT JOIN obat o ON dp2.id_obat = o.id
                 WHERE dp.id_pasien = ?
                 GROUP BY dp.id, p.id, dp.created_at, pol.nama_poli, d.nama, dp.keluhan, p.catatan, p.biaya_periksa
                 ORDER BY dp.created_at DESC";
                 
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("i", $id_pasien);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $riwayat = [];
        while ($row = $result->fetch_assoc()) {
            $tanggal = new DateTime($row['tgl_periksa']);
            $riwayat[] = [
                'id' => $row['id'],
                'tgl_periksa' => $tanggal->format('d/m/Y'),
                'nama_poli' => $row['nama_poli'],
                'nama_dokter' => $row['nama_dokter'],
                'keluhan' => $row['keluhan'],
                'catatan' => $row['catatan'] ?: '-',
                'biaya_periksa' => $row['biaya_periksa'] ? 'Rp ' . number_format($row['biaya_periksa'], 0, ',', '.') : '-',
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