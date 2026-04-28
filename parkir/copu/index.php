<?php
session_start();

// Kalau BELUM login → ke login
if (!isset($_SESSION['id_user'])) {
    header("Location: auth/login.php");
    exit;
}

// Kalau SUDAH login → arahkan sesuai role
if ($_SESSION['role'] == "admin") {
    header("Location: dashboard/admin.php");
} elseif ($_SESSION['role'] == "petugas") {
    header("Location: dashboard/petugas.php");
} else {
    header("Location: dashboard/owner.php");
}
exit;
?>