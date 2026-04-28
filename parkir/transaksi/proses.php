<?php
session_start();
include "../config/koneksi.php";
include "../config/log.php";

# =========================
# KENDARAAN MASUK
# =========================
if (isset($_POST['masuk'])) {

    // 1. Ambil dan amankan data dari form
    $plat    = mysqli_real_escape_string($conn, $_POST['plat']);
    $warna   = mysqli_real_escape_string($conn, $_POST['warna']);
    $pemilik = mysqli_real_escape_string($conn, $_POST['pemilik']);
    $jenis   = mysqli_real_escape_string($conn, $_POST['jenis']);
    $id_area = mysqli_real_escape_string($conn, $_POST['id_area']);

    // 2. Cek apakah kendaraan sudah pernah terdaftar di tb_kendaraan
    $cek_kendaraan = mysqli_query($conn, "SELECT id_kendaraan FROM tb_kendaraan WHERE plat_nomor = '$plat'");
    
    if (mysqli_num_rows($cek_kendaraan) > 0) {
        // Jika sudah ada, ambil ID-nya dan update info terbaru (warna/pemilik)
        $data_k = mysqli_fetch_assoc($cek_kendaraan);
        $id_kendaraan = $data_k['id_kendaraan'];

        mysqli_query($conn, "UPDATE tb_kendaraan 
            SET warna='$warna', pemilik='$pemilik' 
            WHERE id_kendaraan='$id_kendaraan'");
    } else {
        // Jika belum ada, simpan sebagai kendaraan baru
        mysqli_query($conn, "INSERT INTO tb_kendaraan 
            (plat_nomor, warna, pemilik, jenis_kendaraan) 
            VALUES ('$plat', '$warna', '$pemilik', '$jenis')");
        $id_kendaraan = mysqli_insert_id($conn);
    }

    // 3. Cek apakah kendaraan tersebut statusnya masih 'masuk' (belum keluar)
    $cek_parkir = mysqli_query($conn, "
        SELECT * FROM tb_transaksi 
        WHERE id_kendaraan='$id_kendaraan' 
        AND status='masuk'
    ");

    if (mysqli_num_rows($cek_parkir) > 0) {
        die("<h3>⚠️ Kendaraan dengan plat $plat ini sudah berada di dalam parkiran!</h3><a href='masuk.php'>Kembali</a>");
    }

    // 4. Input ke tabel transaksi
    $insert = mysqli_query($conn, "
        INSERT INTO tb_transaksi (id_kendaraan, id_area, waktu_masuk, status) 
        VALUES ('$id_kendaraan', '$id_area', NOW(), 'masuk')
    ");

    if ($insert) {
        $id_transaksi = mysqli_insert_id($conn);

        // 5. Update kuota terisi di tb_area_parkir
        mysqli_query($conn, "
            UPDATE tb_area_parkir 
            SET terisi = terisi + 1 
            WHERE id_area='$id_area'
        ");

        // 6. Catat aktivitas petugas ke log
        logAktivitas($conn, $_SESSION['id_user'], 
            "Input kendaraan masuk Plat: $plat (ID Transaksi: $id_transaksi)");

        // 7. Arahkan ke cetak karcis
        header("Location: karcis.php?id=$id_transaksi");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

# =========================
# KENDARAAN KELUAR
# =========================
if (isset($_POST['keluar'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']); 

    // Ambil data transaksi
    $query_data = mysqli_query($conn, "
        SELECT t.*, k.jenis_kendaraan 
        FROM tb_transaksi t
        JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
        WHERE t.id_parkir='$id' AND t.status='masuk'
    ");
    $data = mysqli_fetch_assoc($query_data);

    if ($data) {
        // Hitung durasi dan biaya
        $masuk = strtotime($data['waktu_masuk']);
        $keluar = time();
        $durasi = ceil(($keluar - $masuk) / 3600);
        if ($durasi <= 0) $durasi = 1;

        $tarif_res = mysqli_query($conn, "SELECT tarif_per_jam FROM tb_tarif WHERE jenis_kendaraan='{$data['jenis_kendaraan']}'");
        $tarif_data = mysqli_fetch_assoc($tarif_res);
        $tarif = $tarif_data['tarif_per_jam'] ?? 2000;

        $total = $durasi * $tarif;

        // Update transaksi jadi keluar
        $update = mysqli_query($conn, "UPDATE tb_transaksi SET 
            waktu_keluar = NOW(),
            durasi_jam = '$durasi',
            biaya_total = '$total',
            status = 'keluar'
            WHERE id_parkir='$id'
        ");

        if ($update) {
            // Kurangi kuota area
            $id_area = $data['id_area'];
            mysqli_query($conn, "UPDATE tb_area_parkir SET terisi = terisi - 1 WHERE id_area='$id_area'");
            
            logAktivitas($conn, $_SESSION['id_user'], "Kendaraan keluar ID: $id, Total: $total");

            header("Location: struk.php?id=$id");
            exit;
        }
    } else {
        echo "<script>alert('Data tidak ditemukan!'); window.location='keluar.php';</script>";
    }
}



# =========================
# KENDARAAN KELUAR
# =========================
if (isset($_POST['keluar'])) {

    $id = mysqli_real_escape_string($conn, $_POST['id']);

    $query_data = mysqli_query($conn, "
        SELECT t.*, k.jenis_kendaraan 
        FROM tb_transaksi t
        JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
        WHERE t.id_parkir='$id'
    ");

    $data = mysqli_fetch_assoc($query_data);

    if (!$data) {
        die("Data transaksi tidak ditemukan!");
    }

    // hitung waktu
    $masuk = strtotime($data['waktu_masuk']);
    $keluar = time();
    $durasi = ceil(($keluar - $masuk) / 3600);
    if ($durasi <= 0) $durasi = 1;

    // ambil tarif
    $tarif_res = mysqli_query($conn, "
        SELECT * FROM tb_tarif 
        WHERE jenis_kendaraan='{$data['jenis_kendaraan']}'
    ");

    $tarif_data = mysqli_fetch_assoc($tarif_res);

    if (!$tarif_data) {
        die("Tarif tidak ditemukan!");
    }

    $tarif = $tarif_data['tarif_per_jam'];
    $total = $durasi * $tarif;

    // update transaksi
    $update = mysqli_query($conn, "
        UPDATE tb_transaksi SET 
        waktu_keluar = NOW(),
        durasi_jam = '$durasi',
        biaya_total = '$total',
        status = 'keluar'
        WHERE id_parkir='$id'
    ");

    if ($update) {

        // update area
        mysqli_query($conn, "
            UPDATE tb_area_parkir 
            SET terisi = terisi - 1 
            WHERE id_area='{$data['id_area']}'
        ");

        // log
        logAktivitas($conn, $_SESSION['id_user'], 
            "Kendaraan keluar ID: $id");

        header("Location: struk.php?id=$id");
        exit;
    } else {
        die(mysqli_error($conn));
    }
}
?>