<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'petugas') {
    echo "Akses ditolak!";
    exit;
}
?>
<?php
include "../config/koneksi.php";

$parkir = mysqli_query($conn, "
    SELECT t.*, k.plat_nomor, k.jenis_kendaraan
    FROM tb_transaksi t
    LEFT JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    WHERE t.status='masuk'
    ORDER BY t.waktu_masuk DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Petugas</title>

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
         alt="Logo Sekolah" 
         style="width:70px; height:auto;">

    <h6 class="text-white mt-2 mb-0">Sistem Parkir</h6>
    <small class="text-white-50">SMK Siliwangi</small>
</div>

    <a href="#" class="fw-bold">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <a href="../transaksi/masuk.php">
        <i class="bi bi-box-arrow-in-down"></i> Transaksi Masuk
    </a>

    <a href="../transaksi/keluar.php">
        <i class="bi bi-box-arrow-up"></i> Kendaraan Keluar
    </a>

    <hr class="text-white">

    <a href="../auth/logout.php">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
</div>

<!-- CONTENT -->
<div class="content">

    <h3 class="mb-4">Dashboard Petugas</h3>

    <!-- CARD MENU -->
    <div class="row g-3">

        <div class="col-md-6">
            <a href="../transaksi/masuk.php" style="text-decoration:none;">
                <div class="card card-custom p-4">
                    <h5>Transaksi Masuk</h5>
                    <p class="text-muted mb-0">Input kendaraan masuk & cetak karcis</p>
                </div>
            </a>
        </div>

        <div class="col-md-6">
            <a href="../transaksi/keluar.php" style="text-decoration:none;">
                <div class="card card-custom p-4">
                    <h5>Transaksi Keluar</h5>
                    <p class="text-muted mb-0">Hitung biaya & proses kendaraan keluar</p>
                </div>
            </a>
        </div>

    </div>
    <!-- DATA PARKIR AKTIF -->
<div class="card card-custom mt-4">
    <div class="card-body">

        <h5 class="mb-3">
            <i class="bi bi-car-front"></i> Kendaraan Sedang Parkir
        </h5>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Plat</th>
                        <th>Jenis</th>
                        <th>Waktu Masuk</th>
                        <th>Durasi</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (mysqli_num_rows($parkir) > 0) { ?>
                    <?php while ($p = mysqli_fetch_assoc($parkir)) { 
                        $masuk = strtotime($p['waktu_masuk']);
                        $sekarang = time();
                        $durasi = ceil(($sekarang - $masuk) / 3600);
                    ?>
                    <tr>
                        <td><strong><?= $p['plat_nomor'] ?? '-' ?></strong></td>

                        <td>
                            <span class="badge bg-primary">
                                <?= $p['jenis_kendaraan'] ?? '-' ?>
                            </span>
                        </td>

                        <td>
                            <?= date('d-m-Y H:i', strtotime($p['waktu_masuk'])) ?>
                        </td>

                        <td class="durasi" data-masuk="<?= $p['waktu_masuk'] ?>">
    0 jam
</td>

                        <td>
                            <span class="badge bg-success">Parkir</span>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="text-center py-3">
                            Tidak ada kendaraan di parkiran
                        </td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>

    </div>
</div>

</div>
<script>
function updateDurasi() {
    let rows = document.querySelectorAll(".durasi");

    rows.forEach(row => {
        let waktuMasuk = row.getAttribute("data-masuk");
        let masuk = new Date(waktuMasuk);
        let sekarang = new Date();

        let diffMs = sekarang - masuk;
        let jam = Math.floor(diffMs / (1000 * 60 * 60));
        let menit = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

        row.innerHTML = jam + " jam " + menit + " menit";

        // 🔥 BONUS: warna jika lama
        if (jam >= 5) {
            row.style.color = "#EF4444"; // merah
            row.style.fontWeight = "bold";
        }
    });
}

// update tiap 1 detik
setInterval(updateDurasi, 1000);

// jalankan pertama kali
updateDurasi();
</script>
</body>
</html>