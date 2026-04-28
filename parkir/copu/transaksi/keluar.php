<?php 
include "../config/koneksi.php";

// 🔥 PERBAIKAN: Join transaksi dengan kendaraan agar dapat Plat Nomor dari mobil yang sedang parkir
$query_aktif = mysqli_query($conn, "
    SELECT t.id_parkir, k.plat_nomor, k.jenis_kendaraan 
    FROM tb_transaksi t
    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    WHERE t.status='masuk'
");

// Tetap ambil ini untuk statistik di atas
$kendaraan = mysqli_query($conn, "SELECT * FROM tb_kendaraan");
$area = mysqli_query($conn, "SELECT * FROM tb_area_parkir");
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Transaksi Keluar</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background-color: #F9FAFB;
}

.card-custom {
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    border: none;
}

.btn-danger {
    background-color: #DC2626;
    border: none;
}

.btn-danger:hover {
    background-color: #EF4444;
}
.btn-back {
    text-decoration: none;
    color: #4B5563;
    font-weight: 500;
    transition: 0.3s;
}

.btn-back:hover {
    color: #111827;
}

</style>
</head>

<body>

<div class="container py-4">
<div class="d-flex justify-content-between align-items-center mb-4">
    <!-- 🔥 UBAH JUDUL -->
    <h3 class="mb-4">Transaksi Kendaraan Keluar</h3>
    <a href="../dashboard/petugas.php" class="btn-back">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

     <!-- INFO CARD -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card card-custom p-3">
                <h6>Kendaraan Sedang Parkir</h6>
                <!-- Mengambil jumlah dari $query_aktif -->
                <h4><?= mysqli_num_rows($query_aktif) ?></h4>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-custom p-3">
                <h6>Total Area Parkir</h6>
                <h4><?= mysqli_num_rows($area) ?></h4>
            </div>
        </div>
    </div>


    <!-- FORM -->
    <div class="card card-custom">
        <div class="card-body">

            <!-- Pastikan action sesuai dengan file proses keluar Anda -->
<form method="POST" action="proses.php"> 

    <div class="mb-3">
        <label class="form-label">Pilih Kendaraan yang Sedang Parkir</label>
        <select name="id" class="form-select" required>
            <option value="">-- Pilih Plat Nomor --</option>

            <?php while ($t = mysqli_fetch_assoc($query_aktif)) { ?>
                <option value="<?= $t['id_parkir'] ?>">
                    <?= $t['plat_nomor'] ?> - <?= ucfirst($t['jenis_kendaraan']) ?> (Sedang Parkir)
                </option>
            <?php } ?>

        </select>
    </div>

    <button class="btn btn-danger w-100" name="keluar">
        <i class="bi bi-box-arrow-up"></i> Proses Kendaraan Keluar
    </button>
</form>


        </div>
    </div>

</div>

</body>
</html>