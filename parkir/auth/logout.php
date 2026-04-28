<?php
session_start();
include "../config/koneksi.php";
include "../config/log.php";

logAktivitas($conn, $_SESSION['id_user'], "Logout dari sistem");

session_destroy();
header("Location: login.php");
?>