<?php 
include "../config/koneksi.php";

// TAMBAH
if (isset($_POST['tambah'])) {
    mysqli_query($conn, "INSERT INTO tb_kendaraan (jenis_kendaraan)
        VALUES ('$_POST[jenis]')");
}

// UPDATE
if (isset($_POST['update'])) {
    mysqli_query($conn, "UPDATE tb_kendaraan SET
        jenis_kendaraan='$_POST[jenis]'
        WHERE id_kendaraan='$_POST[id]'
    ");
}

// HAPUS
if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM tb_kendaraan 
        WHERE id_kendaraan='$_GET[hapus]'");
}

// EDIT
$edit = null;
if (isset($_GET['edit'])) {
    $edit = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT * FROM tb_kendaraan WHERE id_kendaraan='$_GET[edit]'"));
}

// DATA
$data = mysqli_query($conn, "SELECT * FROM tb_kendaraan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Jenis Kendaraan</title>

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
    <h3 class="mb-4">Manajemen Kendaraan</h3>
    
    <a href="../dashboard/admin.php" class="btn-back">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    <!-- FORM -->
    <div class="card card-custom mb-4">
        <div class="card-body">

            <h5 class="mb-3">
                <?= $edit ? "Edit Jenis Kendaraan" : "Tambah Jenis Kendaraan" ?>
            </h5>

            <form method="POST">
                <input type="hidden" name="id" value="<?= $edit['id_kendaraan'] ?? '' ?>">

                <div class="mb-3">
                    <label class="form-label">Jenis Kendaraan</label>
                    <input type="text" name="jenis" class="form-control"
                        value="<?= $edit['jenis_kendaraan'] ?? '' ?>"
                        placeholder="Contoh: Motor / Mobil"
                        required>
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

            <h5 class="mb-3">Data Jenis Kendaraan</h5>

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Jenis Kendaraan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php while ($d = mysqli_fetch_assoc($data)) { ?>
                        <tr>
                            <td><?= $d['id_kendaraan'] ?></td>

                            <td>
                                <span class="badge bg-primary">
                                    <?= $d['jenis_kendaraan'] ?>
                                </span>
                            </td>

                            <td>
                                <a href="?edit=<?= $d['id_kendaraan'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <a href="?hapus=<?= $d['id_kendaraan'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin hapus?')">
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