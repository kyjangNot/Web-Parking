<?php
include "../config/koneksi.php";

$id = $_GET['id'];

$data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT t.*, k.plat_nomor, k.jenis_kendaraan
    FROM tb_transaksi t
    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    WHERE t.id_parkir='$id'
"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Karcis Parkir</title>

    <style>
        body {
            font-family: monospace;
            text-align: center;
        }

        .karcis {
            width: 280px;
            margin: auto;
            border: 1px dashed black;
            padding: 10px;
        }

        h2 {
            margin: 5px 0;
        }

        hr {
            border: 1px dashed black;
        }

        .left {
            text-align: left;
        }

        .footer {
            margin-top: 10px;
            font-size: 12px;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">

<div class="karcis">

    <h2>PARKIR APP</h2>
    <small>Jl. Contoh No.123</small>

    <hr>

    <div class="left">
        <b>ID Parkir:</b> <?= $data['id_parkir'] ?><br>
        <b>Plat:</b> <?= $data['plat_nomor'] ?><br>
        <b>Jenis:</b> <?= strtoupper($data['jenis_kendaraan']) ?><br>
        <b>Masuk:</b><br>
        <?= date('d-m-Y H:i', strtotime($data['waktu_masuk'])) ?><br>
    </div>

    <hr>

    <div class="footer">
        Simpan karcis ini<br>
        Hilang = denda
    </div>

</div>

</body>
</html>