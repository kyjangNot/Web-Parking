<?php
session_start();
include "../config/koneksi.php";
include "../config/log.php"; // Pindahkan ke atas

$username = mysqli_real_escape_string($conn, $_POST['username']); // Tambahkan pengamanan
$password = $_POST['password'];

$data = mysqli_query($conn, "SELECT * FROM tb_user WHERE username='$username'");
$user = mysqli_fetch_assoc($data);

if ($user) {
    // 1. CEK STATUS AKTIF
    if ($user['status_aktif'] == 0) {
        echo "Akun tidak aktif!";
        exit;
    }

    // 2. CEK PASSWORD
    if ($password == $user['password']) {
        // Set Session
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['role'] = $user['role'];

        // 3. CATAT LOG DULU (Penting: harus sebelum header)
        logAktivitas($conn, $user['id_user'], "Login ke sistem");

        // 4. BARU REDIRECT
        if ($user['role'] == "admin") {
            header("Location: ../dashboard/admin.php");
        } elseif ($user['role'] == "petugas") {
            header("Location: ../dashboard/petugas.php");
        } else {
            header("Location: ../dashboard/owner.php");
        }
        exit; // Tambahkan exit agar kode di bawahnya tidak jalan

    } else {
        echo "Password salah!";
    }
} else {
    echo "User tidak ditemukan!";
}
?>
