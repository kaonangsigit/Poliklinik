<?php
include_once("../../config/koneksi.php");

// Debug mode
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_POST['id_poli'])) {
    $id_poli = mysqli_real_escape_string($koneksi, $_POST['id_poli']);
    
    // Query untuk mengambil jadwal dokter
    $query = "SELECT jp.*, d.nama as nama_dokter 
              FROM jadwal_periksa jp
              JOIN dokter d ON jp.id_dokter = d.id
              WHERE d.id_poli = '$id_poli'
              AND jp.status = 'aktif'
              ORDER BY FIELD(jp.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), 
                       jp.jam_mulai";
    
    $result = mysqli_query($koneksi, $query);
    
    if(!$result) {
        error_log("MySQL Error: " . mysqli_error($koneksi));
        echo "<option value=''>Error mengambil jadwal</option>";
        exit;
    }
    
    if(mysqli_num_rows($result) > 0) {
        echo "<option value=''>-- Pilih Jadwal --</option>";
        while($row = mysqli_fetch_assoc($result)) {
            // Format jam
            $jam_mulai = date('H:i', strtotime($row['jam_mulai']));
            $jam_selesai = date('H:i', strtotime($row['jam_selesai']));
            
            echo "<option value='" . $row['id'] . "'>" . 
                 htmlspecialchars($row['nama_dokter']) . " - " . 
                 htmlspecialchars($row['hari']) . " (" . 
                 $jam_mulai . " - " . 
                 $jam_selesai . ")" .
                 "</option>";
        }
    } else {
        echo "<option value=''>Tidak ada jadwal aktif untuk poli ini</option>";
    }
} else {
    echo "<option value=''>Pilih poli terlebih dahulu</option>";
}
?> 