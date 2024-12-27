<?php
include_once("../../config/koneksi.php");

// Set header JSON
header('Content-Type: application/json');

// Debug: log received data
error_log("Received POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validasi input
        if (!isset($_POST['id_periksa']) || !isset($_POST['catatan'])) {
            throw new Exception('Data tidak lengkap');
        }

        $id_periksa = $_POST['id_periksa'];
        $catatan = $_POST['catatan'];
        $obat = isset($_POST['obat']) ? (array)$_POST['obat'] : [];

        // Debug: log processed data
        error_log("Processing data - ID Periksa: $id_periksa, Catatan: $catatan");
        error_log("Obat: " . print_r($obat, true));

        mysqli_begin_transaction($koneksi);

        // Update catatan
        $query_update = "UPDATE periksa SET catatan = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query_update);
        mysqli_stmt_bind_param($stmt, "si", $catatan, $id_periksa);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal update catatan");
        }

        // Hapus detail obat yang lama
        $query_delete = "DELETE FROM detail_periksa WHERE id_periksa = ?";
        $stmt = mysqli_prepare($koneksi, $query_delete);
        mysqli_stmt_bind_param($stmt, "i", $id_periksa);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal hapus obat lama");
        }

        // Hitung total biaya
        $biaya_periksa = 150000; // Biaya dasar periksa
        $total_biaya_obat = 0;

        // Hitung total biaya obat jika ada
        if (!empty($obat)) {
            $obat_ids = implode(',', array_map('intval', $obat));
            $query_obat = "SELECT SUM(harga) as total FROM obat WHERE id IN ($obat_ids)";
            $result_obat = mysqli_query($koneksi, $query_obat);
            $row_obat = mysqli_fetch_assoc($result_obat);
            $total_biaya_obat = $row_obat['total'] ?? 0;

            // Insert obat baru
            $query_insert = "INSERT INTO detail_periksa (id_periksa, id_obat) VALUES (?, ?)";
            $stmt = mysqli_prepare($koneksi, $query_insert);
            
            foreach ($obat as $id_obat) {
                mysqli_stmt_bind_param($stmt, "ii", $id_periksa, $id_obat);
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Gagal tambah obat");
                }
            }
        }

        // Update total biaya
        $total_biaya = $biaya_periksa + $total_biaya_obat;
        $query_biaya = "UPDATE periksa SET biaya_periksa = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query_biaya);
        mysqli_stmt_bind_param($stmt, "di", $total_biaya, $id_periksa);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal update biaya");
        }

        mysqli_commit($koneksi);
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Data berhasil diupdate',
            'biaya' => $total_biaya
        ]);

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        error_log("Error in update_periksa.php: " . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?> 