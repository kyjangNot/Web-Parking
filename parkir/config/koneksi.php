<?php
// Mencegah error reporting yang mengganggu tampilan di aplikasi produksi (opsional)
error_reporting(E_ALL); 

$host = "localhost";
$user = "root";
$pass = "";
$db   = "parkir";

$conn = mysqli_connect($host, $user, $pass, $db);

// Atur zona waktu agar sinkron antara PHP dan database
date_default_timezone_set('Asia/Jakarta');

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Set charset ke utf8 agar pembacaan karakter khusus lebih stabil
mysqli_set_charset($conn, "utf8");
?>
