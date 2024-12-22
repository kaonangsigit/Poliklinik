<?php
session_start();
include_once("../../config/koneksi.php");

$last_update = $_GET['last_update'];
$current_time = time();

// Cek perubahan dalam database
$query = "SELECT * FROM daftar_poli 
          WHERE updated_at > FROM_UNIXTIME(?)";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $last_update);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$patients = [];
while ($row = mysqli_fetch_assoc($result)) {
    $patients[] = $row;
}

$response = array(
    'needsUpdate' => count($patients) > 0,
    'patients' => $patients
);

$_SESSION['last_update'] = $current_time;
header('Content-Type: application/json');
echo json_encode($response);
?> 