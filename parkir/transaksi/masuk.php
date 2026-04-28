<?php
include "../config/koneksi.php";

$area = mysqli_query($conn, "SELECT * FROM tb_area_parkir");
$tarif = mysqli_query($conn, "SELECT jenis_kendaraan FROM tb_tarif");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Transaksi Masuk</title>

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

.card-custom {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.btn-primary {
    background-color: #1E3A8A;
    border: none;
}

.btn-primary:hover {
    background-color: #3B82F6;
}
</style>
</head>

<body>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-4">Transaksi Kendaraan Masuk</h3>
    <a href="../dashboard/petugas.php" class="btn-back">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

    <div class="card card-custom">
        <div class="card-body">

            <form method="POST" action="proses.php">

                <div class="row">

                    <!-- PLAT -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Plat Nomor</label>
                        <input type="text" name="plat" class="form-control"
                               placeholder="Contoh: B 1234 ABC" required>
                    </div>

                    <!-- JENIS -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jenis Kendaraan</label>
                        <select name="jenis" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            <?php while ($t = mysqli_fetch_assoc($tarif)) { ?>
                                <option value="<?= $t['jenis_kendaraan'] ?>">
                                    <?= ucfirst($t['jenis_kendaraan']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- WARNA -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Warna</label>
                        <input type="text" name="warna" class="form-control" required>
                    </div>

                    <!-- PEMILIK -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pemilik</label>
                        <input type="text" name="pemilik" class="form-control" required>
                    </div>

                    <!-- AREA -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Area Parkir</label>
                        <select name="id_area" class="form-select" required>
                            <option value="">-- Pilih Area --</option>

                            <?php while ($a = mysqli_fetch_assoc($area)) { 
                                $penuh = ($a['terisi'] >= $a['kapasitas']);
                            ?>
                                <option value="<?= $a['id_area'] ?>"
                                    <?= $penuh ? 'disabled' : '' ?>>

                                    <?= $a['nama_area'] ?> 
                                    (<?= $a['terisi'] ?>/<?= $a['kapasitas'] ?>)
                                    <?= $penuh ? ' - PENUH' : '' ?>

                                </option>
                            <?php } ?>

                        </select>

                        <small class="text-muted">
                            Area penuh tidak dapat dipilih
                        </small>
                    </div>

                </div>

                <!-- BUTTON -->
                <button type="submit" name="masuk" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-down"></i>
                    Catat Kendaraan Masuk
                </button>

            </form>

        </div>
    </div>

</div>

</body>
</html>