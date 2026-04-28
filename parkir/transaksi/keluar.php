<?php
include "../config/koneksi.php";

$data = mysqli_query($conn, "
    SELECT t.*, k.jenis_kendaraan, k.plat_nomor
    FROM tb_transaksi t
    LEFT JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    WHERE t.status='masuk'
");
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
        <h3>Transaksi Kendaraan Keluar</h3>
        <a href="../dashboard/petugas.php" class="btn btn-outline-dark">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card card-custom">
        <div class="card-body">

            <!-- SEARCH -->
            <div class="mb-3">
                <label class="form-label">Cari Plat Nomor</label>
                <input type="text" id="search" class="form-control" placeholder="Ketik plat nomor...">
            </div>

            <!-- LIST HASIL -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="tableData">
                    <thead>
                        <tr>
                            <th>Plat</th>
                            <th>Jenis</th>
                            <th>Waktu Masuk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php while ($d = mysqli_fetch_assoc($data)) { ?>
                        <tr>
                            <td class="plat"><?= $d['plat_nomor'] ?? '-' ?></td>
                            <td><?= $d['jenis_kendaraan'] ?? '-' ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($d['waktu_masuk'])) ?></td>
                            <td>
                                <button 
    class="btn btn-primary btn-sm btnKeluar"
    data-id="<?= $d['id_parkir'] ?>"
    data-plat="<?= $d['plat_nomor'] ?>"
    data-bs-toggle="modal"
    data-bs-target="#modalKeluar">

    <i class="bi bi-box-arrow-up"></i>
</button>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

<!-- SCRIPT SEARCH -->
<script>
document.getElementById("search").addEventListener("keyup", function() {
    let keyword = this.value.toLowerCase();
    let rows = document.querySelectorAll("#tableData tbody tr");

    rows.forEach(row => {
        let plat = row.querySelector(".plat").textContent.toLowerCase();
        row.style.display = plat.includes(keyword) ? "" : "none";
    });
});
</script>
<!-- MODAL KONFIRMASI -->
<div class="modal fade" id="modalKeluar" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="proses.php" id="formKeluar">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Kendaraan Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" name="id" id="idParkir">

                <p>Masukkan ulang plat nomor untuk konfirmasi:</p>

                <input type="text" id="inputPlat" class="form-control" placeholder="Contoh: B 1234 ABC" required>

                <div class="text-danger mt-2 d-none" id="errorPlat">
                    Plat nomor tidak sesuai!
                </div>

            </div>

            <div class="modal-footer">
                <button type="submit" name="keluar" class="btn btn-primary">
                    Konfirmasi Keluar
                </button>
            </div>

        </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
// ambil data dari tombol
let buttons = document.querySelectorAll(".btnKeluar");

buttons.forEach(btn => {
    btn.addEventListener("click", function() {
        let id = this.getAttribute("data-id");
        let plat = this.getAttribute("data-plat");

        document.getElementById("idParkir").value = id;
        document.getElementById("formKeluar").setAttribute("data-plat", plat);
    });
});

// validasi sebelum submit
document.getElementById("formKeluar").addEventListener("submit", function(e) {
    let input = document.getElementById("inputPlat").value.toLowerCase().trim();
    let platAsli = this.getAttribute("data-plat").toLowerCase().trim();

    if (input !== platAsli) {
        e.preventDefault();
        document.getElementById("errorPlat").classList.remove("d-none");
    }
});
</script>
</html>