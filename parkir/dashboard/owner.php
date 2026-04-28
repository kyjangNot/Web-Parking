<?php
session_start();
include "../config/koneksi.php";

// TOTAL PENDAPATAN
$q1 = mysqli_query($conn, "
    SELECT SUM(biaya_total) as total 
    FROM tb_transaksi 
    WHERE status='keluar'
");
$d1 = mysqli_fetch_assoc($q1);
$total_pendapatan = $d1['total'] ?? 0;

// TOTAL TRANSAKSI
$q2 = mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM tb_transaksi
");
$d2 = mysqli_fetch_assoc($q2);
$total_transaksi = $d2['total'] ?? 0;

// KENDARAAN MASIH PARKIR
$q3 = mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM tb_transaksi 
    WHERE status='masuk'
");
$d3 = mysqli_fetch_assoc($q3);
$kendaraan_aktif = $d3['total'] ?? 0;

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'owner') {
    echo "Akses ditolak!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Owner</title>

<!-- Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background-color: #F9FAFB;
}

/* Sidebar */
.sidebar {
    width: 240px;
    height: 100vh;
    position: fixed;
    background: #1E3A8A;
    padding-top: 20px;
}

.sidebar a {
    display: block;
    color: white;
    padding: 12px 18px;
    text-decoration: none;
    transition: 0.3s;
}

.sidebar a:hover {
    background: #3B82F6;
}

.content {
    margin-left: 240px;
    padding: 20px;
}

.card-custom {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: 0.2s;
}

.card-custom:hover {
    transform: scale(1.02);
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="text-center mb-4">
    <img src="../assets/img/logo.png" 
         alt="owner panel" 
         style="width:70px; height:auto;">

    <h6 class="text-white mt-2 mb-0">Sistem Parkir</h6>
    <small class="text-white-50">SMK Siliwangi</small>
</div>

    <a href="#" class="fw-bold">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <a href="../laporan/laporan.php">
        <i class="bi bi-file-earmark-bar-graph"></i> Laporan
    </a>

    <hr class="text-white">

    <a href="../auth/logout.php">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>

</div>

<!-- CONTENT -->
<div class="content">

    <h3 class="mb-4">Dashboard Owner</h3>

    <!-- STAT CARDS -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card card-custom p-3">
                <h6>Total Pendapatan</h6>
                <h4 class="text-success">
    Rp <?= number_format($total_pendapatan, 0, ',', '.') ?>
</h4>
                <small class="text-muted">Seluruh transaksi</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-custom p-3">
                <h6>Total Transaksi</h6>
                <h4 class="text-primary">
    <?= $total_transaksi ?>
</h4>
                <small class="text-muted">Masuk & keluar</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-custom p-3">
                <h6>Kendaraan Aktif</h6>
                <h4 class="text-warning">
    <?= $kendaraan_aktif ?>
</h4>
                <small class="text-muted">Sedang parkir</small>
            </div>
        </div>

    </div>

    <!-- MENU LAPORAN -->
    <div class="row g-3">

        <div class="col-md-6">
            <a href="../laporan/laporan.php" style="text-decoration:none;">
                <div class="card card-custom p-4">
                    <h5>Laporan Parkir</h5>
                    <p class="text-muted mb-0">
                        Lihat rekap transaksi, pendapatan, dan aktivitas sistem
                    </p>
                </div>
            </a>
        </div>

        <div class="col-md-6">
    <a href="../laporan/laporan.php?cetak=1" style="text-decoration:none;">
        <div class="card card-custom p-4">
            <h5><i class="bi bi-printer"></i> Cetak Laporan</h5>
            <p class="text-muted mb-0">
                Cetak laporan transaksi dan pendapatan parkir
            </p>
        </div>
    </a>
</div>

    </div>

</div>

</body>
</html>