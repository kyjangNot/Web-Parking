<?php
include "../config/koneksi.php";
include "../config/log.php";
session_start();

$awal = $_GET['awal'] ?? '';
$akhir = $_GET['akhir'] ?? '';

if ($awal && $akhir) {
    logAktivitas($conn, $_SESSION['id_user'], "Melihat laporan $awal - $akhir");
}

$query = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan
          FROM tb_transaksi t
          JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
          WHERE t.status='keluar'";

if ($awal && $akhir) {
    $query .= " AND DATE(t.waktu_keluar) BETWEEN '$awal' AND '$akhir'";
}

$query .= " ORDER BY t.waktu_keluar DESC";

$data = mysqli_query($conn, $query);

$total_uang = 0;
?>
<?php if (isset($_GET['cetak'])) { ?>
<script>
    window.onload = function() {
        window.print();
    }
</script>
<?php } ?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Laporan Parkir</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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

.table th {
    background-color: #f1f5f9;
}

.btn-primary {
    background-color: #1E3A8A;
    border: none;
}

.btn-primary:hover {
    background-color: #3B82F6;
}

@media print {
    .no-print {
        display: none !important;
        visibility: hidden !important;
    }

    body {
        background: white;
    }

    .card {
        box-shadow: none !important;
        border: none !important;
    }
    @media print {

    table {
        font-size: 12px;
    }

    th {
        background: #ddd !important;
    }

    h3 {
        text-align: center;
        margin-bottom: 20px;
    }

}
}</style>
</head>

<body>

<div class="container py-4">

    <!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h3 class="mb-0">Laporan Pendapatan Parkir</h3>
    
    <div class="d-flex align-items-center">
        <a href="../dashboard/owner.php" class="btn btn-link text-decoration-none text-muted me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-outline-dark">
            <i class="bi bi-printer"></i> Cetak Laporan
        </button>
    </div>
</div>


    <!-- FILTER -->
    <div class="card card-custom mb-4 no-print">
        <div class="card-body">

            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label">Dari</label>
                    <input type="date" name="awal" class="form-control" value="<?= $awal ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sampai</label>
                    <input type="date" name="akhir" class="form-control" value="<?= $akhir ?>" required>
                </div>

                <div class="col-md-4">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- TABLE -->
    <div class="card card-custom">
        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Plat</th>
                            <th>Jenis</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Durasi</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if ($data && mysqli_num_rows($data) > 0) { ?>
                        <?php while ($d = mysqli_fetch_assoc($data)) { 
                            $total_uang += $d['biaya_total'];
                        ?>
                        <tr>
                            <td><?= $d['id_parkir'] ?></td>
                            <td><strong><?= $d['plat_nomor'] ?></strong></td>

                            <td>
                                <?php if ($d['jenis_kendaraan'] == 'mobil') { ?>
                                    <span class="badge bg-primary">Mobil</span>
                                <?php } else { ?>
                                    <span class="badge bg-secondary">Motor</span>
                                <?php } ?>
                            </td>

                            <td><?= date('d-m-Y H:i', strtotime($d['waktu_masuk'])) ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($d['waktu_keluar'])) ?></td>
                            <td><?= $d['durasi_jam'] ?> jam</td>

                            <td>
                                <strong class="text-success">
                                    Rp <?= number_format($d['biaya_total'], 0, ',', '.') ?>
                                </strong>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                Data tidak ditemukan
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>

                    <!-- TOTAL -->
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-end">TOTAL PENDAPATAN</th>
                            <th class="text-success">
                                Rp <?= number_format($total_uang, 0, ',', '.') ?>
                            </th>
                        </tr>
                    </tfoot>

                </table>

            </div>

        </div>
    </div>

</div>

</body>
</html>