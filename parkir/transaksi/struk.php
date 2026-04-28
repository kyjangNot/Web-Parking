<?php
include "../config/koneksi.php";

$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

$query = mysqli_query($conn, "
    SELECT t.*, k.plat_nomor, k.jenis_kendaraan, tf.tarif_per_jam
    FROM tb_transaksi t
    LEFT JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    LEFT JOIN tb_tarif tf ON k.jenis_kendaraan = tf.jenis_kendaraan
    WHERE t.id_parkir='$id'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data transaksi tidak ditemukan!");
}

// Format angka rupiah
function rupiah($angka){
    return "Rp " . number_format($angka,0,',','.');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Struk Parkir</title>

    <style>
        body {
            font-family: monospace;
            font-size: 14px;
            text-align: center;
        }

        .struk {
            width: 300px;
            margin: auto;
            border: 1px dashed black;
            padding: 10px;
        }

        hr {
            border: 1px dashed black;
        }

        .left {
            text-align: left;
        }

        .right {
            float: right;
        }

        .clear {
            clear: both;
        }

        h2 {
            margin: 5px 0;
        }

        .footer {
            font-size: 12px;
            margin-top: 10px;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">

<div class="struk">

    <h2>PARKIR APP</h2>
    <small>Jl. Contoh No.123</small>

    <hr>

    <div class="left">
        ID     : <?= $data['id_parkir'] ?><br>
        Plat   : <?= $data['plat_nomor'] ?><br>
        Jenis  : <?= strtoupper($data['jenis_kendaraan']) ?><br>
    </div>

    <hr>

    <div class="left">
        Masuk  :<br>
        <?= date('d-m-Y H:i', strtotime($data['waktu_masuk'])) ?><br><br>

        Keluar :<br>
        <?= date('d-m-Y H:i', strtotime($data['waktu_keluar'])) ?><br>
    </div>

    <hr>

    <div class="left">
        <div>Durasi <span class="right"><?= $data['durasi_jam'] ?> jam</span></div>
        <div class="clear"></div>

        <div>Tarif/jam <span class="right"><?= rupiah($data['tarif_per_jam']) ?></span></div>
        <div class="clear"></div>
    </div>

    <hr>

    <div class="left">
        <b>Total <span class="right"><?= rupiah($data['biaya_total']) ?></span></b>
        <div class="clear"></div>
    </div>

    <hr>

    <!-- QR CODE -->
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=ParkirID-<?= $id ?>" />

    <div class="footer">
        Terima kasih<br>
        Simpan struk ini<br>
        Kehilangan tiket dikenakan denda
    </div>

</div>

</body>
</html>