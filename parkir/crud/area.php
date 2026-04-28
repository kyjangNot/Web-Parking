<?php
include "../config/koneksi.php";

if (isset($_POST['tambah'])) {
    mysqli_query($conn, "INSERT INTO tb_area_parkir 
    (nama_area, kapasitas, terisi)
    VALUES ('$_POST[nama]', '$_POST[kapasitas]', 0)");
}

if (isset($_POST['update'])) {
    mysqli_query($conn, "UPDATE tb_area_parkir SET
        nama_area='$_POST[nama]',
        kapasitas='$_POST[kapasitas]'
        WHERE id_area='$_POST[id]'
    ");
}

if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM tb_area_parkir WHERE id_area='$_GET[hapus]'");
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT * FROM tb_area_parkir WHERE id_area='$_GET[edit]'"));
}

$data = mysqli_query($conn, "SELECT * FROM tb_area_parkir");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Area Parkir</title>

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

.table th {
    background-color: #f1f5f9;
}
</style>
</head>

<body>

<div class="container py-4">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-4">Manajemen Area Parkir</h3>
    <a href="../dashboard/admin.php" class="btn-back">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

    <!-- FORM -->
    <div class="card card-custom mb-4">
        <div class="card-body">

            <h5 class="mb-3">
                <?= $edit ? "Edit Area" : "Tambah Area" ?>
            </h5>

            <form method="POST">
                <input type="hidden" name="id" value="<?= $edit['id_area'] ?? '' ?>">

                <div class="mb-3">
                    <label class="form-label">Nama Area</label>
                    <input type="text" name="nama" class="form-control"
                        value="<?= $edit['nama_area'] ?? '' ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kapasitas</label>
                    <input type="number" name="kapasitas" class="form-control"
                        value="<?= $edit['kapasitas'] ?? '' ?>" required>
                </div>

                <button class="btn btn-primary" name="<?= $edit ? 'update' : 'tambah' ?>">
                    <i class="bi <?= $edit ? 'bi-pencil' : 'bi-plus-lg' ?>"></i>
                    <?= $edit ? "Update" : "Tambah" ?>
                </button>

            </form>

        </div>
    </div>

    <!-- TABEL -->
    <div class="card card-custom">
        <div class="card-body">

            <h5 class="mb-3">Data Area Parkir</h5>

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Area</th>
                            <th>Kapasitas</th>
                            <th>Terisi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php while ($d = mysqli_fetch_assoc($data)) { 
                        $status = ($d['terisi'] >= $d['kapasitas']) ? "Penuh" : "Tersedia";
                    ?>
                        <tr>
                            <td><?= $d['id_area'] ?></td>
                            <td><?= $d['nama_area'] ?></td>
                            <td><?= $d['kapasitas'] ?></td>
                            <td><?= $d['terisi'] ?></td>

                            <td>
                                <?php if ($status == "Penuh") { ?>
                                    <span class="badge bg-danger">Penuh</span>
                                <?php } else { ?>
                                    <span class="badge bg-success">Tersedia</span>
                                <?php } ?>
                            </td>

                            <td>
                                <a href="?edit=<?= $d['id_area'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <a href="?hapus=<?= $d['id_area'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus?')">
                                    <i class="bi bi-trash"></i>
                                </a>
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