<?php
session_start();

// Security: hanya admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

include "../config/koneksi.php";

date_default_timezone_set('Asia/Jakarta');
$awal = $_GET['awal'] ?? '';
$akhir = $_GET['akhir'] ?? '';
$query = "SELECT l.*, u.username 
          FROM tb_log_aktivitas l
          JOIN tb_user u ON l.id_user = u.id_user
          WHERE 1=1";

if ($awal && $akhir) {
    $query .= " AND DATE(l.waktu_aktivitas) BETWEEN '$awal' AND '$akhir'";
}

$query .= " ORDER BY l.waktu_aktivitas DESC";

$data = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Log Aktivitas</title>

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

.table th {
    background-color: #f1f5f9;
}

.btn-back {
    text-decoration: none;
    color: #1E3A8A;
    font-weight: 500;
}

.btn-back:hover {
    color: #3B82F6;
}
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
</head>

<body>

<div class="container py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Log Aktivitas Sistem</h3>

        <a href="../dashboard/admin.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card card-custom mb-4 no-print">
    <div class="card-body">

        <form method="GET" class="row g-3 align-items-end">

            <div class="col-md-4">
                <label class="form-label">Dari</label>
                <input type="date" name="awal" class="form-control" value="<?= $awal ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Sampai</label>
                <input type="date" name="akhir" class="form-control" value="<?= $akhir ?>">
            </div>

            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary w-50">
                    <i class="bi bi-funnel"></i> Filter
                </button>

                <a href="log.php" class="btn btn-secondary w-50">
                    Reset
                </a>
            </div>

        </form>

    </div>
</div>

    <!-- CARD -->
    <div class="card card-custom">
        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Aktivitas</th>
                            <th>Waktu</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (mysqli_num_rows($data) > 0) { ?>
                        <?php while ($d = mysqli_fetch_assoc($data)) { ?>
                            <tr>
                                <td>
                                    <strong><?= $d['username'] ?></strong>
                                </td>

                                <td>
                                    <?= $d['aktivitas'] ?>
                                </td>

                                <td>
                                    <?php 
                                    $waktu = $d['waktu_aktivitas']; 
                                    if ($waktu && $waktu != '0000-00-00 00:00:00') {
                                        echo date('d-m-Y H:i', strtotime($waktu));
                                    } else {
                                        echo "-";
                                    }
                                    ?>
                                </td>

                                <td>
                                    <span class="badge bg-success">Recorded</span>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                Belum ada catatan aktivitas
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

</body>
</html>