<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "polidb";

$koneksi = mysqli_connect($host, $username, $password, $database);
// Fungsi untuk membersihkan input dari potensi SQL injection
function clean_input($data) {
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($koneksi, $data);
}

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?> 