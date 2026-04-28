<?php
session_start();

// 1. Cek Login (Perbaiki urutan exit)
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Include koneksi HARUS di luar blok if agar bisa terbaca di bawah
include "../config/koneksi.php";

// --- AMBIL DATA STATISTIK ---

// 🔹 Kendaraan Masuk (hari ini)
$q1 = mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM tb_transaksi 
    WHERE DATE(waktu_masuk) = CURDATE()
");
$masuk = mysqli_fetch_assoc($q1)['total'] ?? 0;

// 🔹 Kendaraan Keluar (hari ini)
$q2 = mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM tb_transaksi 
    WHERE status='keluar' AND DATE(waktu_keluar) = CURDATE()
");
$keluar = mysqli_fetch_assoc($q2)['total'] ?? 0;

// 🔹 Area Penuh (Gunakan query yang lebih simpel sesuai tabel Anda)
$q3 = mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_area_parkir WHERE terisi >= kapasitas");
$area_penuh = mysqli_fetch_assoc($q3)['total'] ?? 0;

// 🔹 Pendapatan Hari Ini
$q4 = mysqli_query($conn, "
    SELECT SUM(biaya_total) as total 
    FROM tb_transaksi 
    WHERE status='keluar' AND DATE(waktu_keluar) = CURDATE()
");
$pendapatan = mysqli_fetch_assoc($q4)['total'] ?? 0;

// 🔹 Log Aktivitas Terbaru
$log = mysqli_query($conn, "
    SELECT l.*, u.username 
    FROM tb_log_aktivitas l
    JOIN tb_user u ON l.id_user = u.id_user
    ORDER BY l.waktu_aktivitas DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Parkir</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F9FAFB; color: #111827; }
        .sidebar { width: 250px; height: 100vh; position: fixed; background-color: #1E3A8A; padding-top: 20px; }
        .sidebar a { display: block; color: #fff; padding: 12px 20px; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover { background-color: #3B82F6; }
        .sidebar a.active { background-color: #3B82F6; font-weight: 500; }
        .content { margin-left: 250px; padding: 20px; }
        .navbar-custom { background-color: #fff; border-bottom: 1px solid #e5e7eb; }
        .card-custom { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: 0.3s; border: none; }
        .card-custom:hover { transform: translateY(-5px); }
        .stat-icon { font-size: 24px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="text-center mb-4">
    <img src="../assets/img/logo.png" 
         alt="Logo Sekolah" 
         style="width:70px; height:auto;">

    <h6 class="text-white mt-2 mb-0">Sistem Parkir</h6>
    <small class="text-white-50">SMK Siliwangi</small>
</div>
    <a href="#" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="../crud/user.php"><i class="bi bi-people"></i> User</a>
    <a href="../crud/kendaraan.php"><i class="bi bi-car-front"></i> Kendaraan</a>
    <a href="../crud/tarif.php"><i class="bi bi-cash"></i> Tarif</a>
    <a href="../crud/area.php"><i class="bi bi-geo-alt"></i> Area Parkir</a>
    <a href="../log/log.php"><i class="bi bi-activity"></i> Log Aktivitas</a>
    <hr class="text-white">
    <a href="../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="content">
    <nav class="navbar navbar-custom mb-4 px-3">
        <span class="navbar-brand mb-0 h5">Dashboard Admin</span>
        <span class="text-muted">Halo, <?= $_SESSION['role']; ?></span>
    </nav>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-custom p-3">
                <div class="d-flex justify-content-between">
                    <div><h6>Masuk Hari Ini</h6><h4><?= $masuk ?></h4></div>
                    <i class="bi bi-box-arrow-in-down text-primary stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-3">
                <div class="d-flex justify-content-between">
                    <div><h6>Keluar Hari Ini</h6><h4><?= $keluar ?></h4></div>
                    <i class="bi bi-box-arrow-up text-success stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-3">
                <div class="d-flex justify-content-between">
                    <div><h6>Area Penuh</h6><h4><?= $area_penuh ?></h4></div>
                    <i class="bi bi-exclamation-triangle text-warning stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-3">
                <div class="d-flex justify-content-between">
                    <div><h6>Pendapatan</h6><h4>Rp <?= number_format($pendapatan, 0, ',', '.') ?></h4></div>
                    <i class="bi bi-cash-stack text-danger stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom p-3">
        <h5 class="mb-3">Aktivitas Terbaru</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $no = 1;
                if (mysqli_num_rows($log) > 0) {
                    while ($l = mysqli_fetch_assoc($log)) { ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= $l['username'] ?></strong></td>
                        <td><?= $l['aktivitas'] ?></td>
                        <td><?= date('H:i', strtotime($l['waktu_aktivitas'])) ?> <small class="text-muted"><?= date('d/m/y', strtotime($l['waktu_aktivitas'])) ?></small></td>
                    </tr>
                <?php } } else { ?>
                    <tr><td colspan="4" class="text-center">Belum ada aktivitas</td></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
