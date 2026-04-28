<?php
include "../config/koneksi.php";
include "../config/log.php";
session_start();

// TAMBAH
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    mysqli_query($conn, "INSERT INTO tb_user (nama_lengkap, username, password, role, status_aktif)
        VALUES ('$nama', '$username', '$password', '$role', '$status')");
    
    logAktivitas($conn, $_SESSION['id_user'], "Menambah user baru: $username");
    header("Location: user.php");
    exit;
}

// TOGGLE STATUS
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT username, status_aktif FROM tb_user WHERE id_user='$id'"));
    
    $status_baru = $user['status_aktif'] == 1 ? 0 : 1;
    $label = $status_baru == 1 ? "Mengaktifkan" : "Menonaktifkan";

    mysqli_query($conn, "UPDATE tb_user SET status_aktif='$status_baru' WHERE id_user='$id'");
    
    logAktivitas($conn, $_SESSION['id_user'], "$label user: " . $user['username']);
    header("Location: user.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];

    mysqli_query($conn, "UPDATE tb_user SET
        nama_lengkap='$_POST[nama]',
        username='$username',
        password='$_POST[password]',
        role='$_POST[role]',
        status_aktif='$_POST[status]'
        WHERE id_user='$id'");
    
    logAktivitas($conn, $_SESSION['id_user'], "Update data user: $username");
    header("Location: user.php");
    exit;
}

// HAPUS
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tb_user WHERE id_user='$id'");
    
    logAktivitas($conn, $_SESSION['id_user'], "Menghapus user ID: $id");
    header("Location: user.php");
    exit;
}

// EDIT
$edit = null;
if (isset($_GET['edit'])) {
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_user WHERE id_user='$_GET[edit]'"));
}

$data = mysqli_query($conn, "SELECT * FROM tb_user");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manajemen User</title>


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

    <h3 class="mb-4">Manajemen User</h3>
    
<a href="../dashboard/admin.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- FORM -->
    <div class="card card-custom mb-4">
        <div class="card-body">

            <h5 class="mb-3">
                <?= $edit ? "Edit User" : "Tambah User" ?>
            </h5>

            <form method="POST">
                <input type="hidden" name="id" value="<?= $edit['id_user'] ?? '' ?>">

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control"
                        value="<?= $edit['nama_lengkap'] ?? '' ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control"
                        value="<?= $edit['username'] ?? '' ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="text" name="password" class="form-control"
                        value="<?= $edit['password'] ?? '' ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="admin" <?= ($edit['role'] ?? '')=='admin'?'selected':'' ?>>Admin</option>
                        <option value="petugas" <?= ($edit['role'] ?? '')=='petugas'?'selected':'' ?>>Petugas</option>
                        <option value="owner" <?= ($edit['role'] ?? '')=='owner'?'selected':'' ?>>Owner</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= ($edit['status_aktif'] ?? '')==1?'selected':'' ?>>Aktif</option>
                        <option value="0" <?= ($edit['status_aktif'] ?? '')==0?'selected':'' ?>>Nonaktif</option>
                    </select>
                </div>

                <button class="btn btn-primary" name="<?= $edit ? 'update' : 'tambah' ?>">
                    <i class="bi <?= $edit ? 'bi-pencil' : 'bi-plus-lg' ?>"></i>
                    <?= $edit ? "Update" : "Tambah" ?>
                </button>

            </form>

        </div>
    </div>

    <!-- TABLE -->
    <div class="card card-custom">
        <div class="card-body">

            <h5 class="mb-3">Data User</h5>

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php while ($d = mysqli_fetch_assoc($data)) { ?>
                        <tr>
                            <td><?= $d['id_user'] ?></td>
                            <td><?= $d['nama_lengkap'] ?></td>
                            <td><?= $d['username'] ?></td>

                            <td>
                                <span class="badge bg-primary">
                                    <?= $d['role'] ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($d['status_aktif'] == 1) { ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger">Nonaktif</span>
                                <?php } ?>
                            </td>

                            <td>
                                <a href="?edit=<?= $d['id_user'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <a href="?hapus=<?= $d['id_user'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Hapus user ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>

                                <a href="?toggle=<?= $d['id_user'] ?>" class="btn btn-secondary btn-sm">
                                    <i class="bi bi-power"></i>
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