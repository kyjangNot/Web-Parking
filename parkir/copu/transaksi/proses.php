<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
session_start(); // Pastikan session aktif untuk mengambil ID User
include "../config/koneksi.php";
include "../config/log.php"; // Pindahkan ke paling atas

# =========================
# KENDARAAN MASUK
# =========================
if (isset($_POST['masuk'])) {
    $id_kendaraan = $_POST['id_kendaraan'];
    $id_area = $_POST['id_area'];

    // 🔴 CEK apakah kendaraan masih ada di dalam
    $cek = mysqli_query($conn, "
        SELECT * FROM tb_transaksi 
        WHERE id_kendaraan='$id_kendaraan' 
        AND status='masuk'
    ");

    if (mysqli_num_rows($cek) > 0) {
        die("<h3>⚠️ Kendaraan ini masih berada di dalam parkiran!</h3>
             <a href='masuk.php'>Kembali</a>");
    }

    // 1. Cek Kapasitas Area
    $query_area = mysqli_query($conn, "SELECT * FROM tb_area_parkir WHERE id_area='$id_area'");
    $area = mysqli_fetch_assoc($query_area);

    if ($area['terisi'] >= $area['kapasitas']) {
        die("<h3>⚠️ Area parkir penuh!</h3><a href='masuk.php'>Kembali</a>");
    }

    // 2. Simpan Transaksi
    $insert = mysqli_query($conn, "INSERT INTO tb_transaksi 
        (id_kendaraan, id_area, waktu_masuk, status) 
        VALUES ('$id_kendaraan', '$id_area', NOW(), 'masuk')");

    if ($insert) {
        $id_transaksi = mysqli_insert_id($conn);
        
        // 3. Tambah jumlah terisi di master area
        mysqli_query($conn, "UPDATE tb_area_parkir SET terisi = terisi + 1 WHERE id_area='$id_area'");
        
        // 4. Catat Log (Setelah ID transaksi didapat)
        logAktivitas($conn, $_SESSION['id_user'], "Input kendaraan masuk ID: $id_transaksi");
        
        header("Location: karcis.php?id=$id_transaksi");
        exit;
    }
}

# =========================
# KENDARAAN KELUAR
# =========================
if (isset($_POST['keluar'])) {

    include "../config/koneksi.php";
    include "../config/log.php";

    $id = $_POST['id'];

    // ambil data transaksi
    $data = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT t.*, k.jenis_kendaraan, k.plat_nomor
        FROM tb_transaksi t
        JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
        WHERE t.id_parkir='$id'
    "));

    if (!$data) {
        echo "Data transaksi tidak ditemukan";
        exit;
    }

    // waktu
    $masuk = strtotime($data['waktu_masuk']);
    $keluar = time();

    $durasi = ceil(($keluar - $masuk) / 3600);

    // tarif
    $tarif_data = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * FROM tb_tarif 
        WHERE jenis_kendaraan='{$data['jenis_kendaraan']}'
    "));

    if (!$tarif_data) {
        echo "Tarif tidak ditemukan";
        exit;
    }

    $tarif = $tarif_data['tarif_per_jam'];
    $total = $durasi * $tarif;

    // update transaksi
    mysqli_query($conn, "
        UPDATE tb_transaksi SET 
        waktu_keluar = NOW(),
        durasi_jam = '$durasi',
        biaya_total = '$total',
        status = 'keluar'
        WHERE id_parkir='$id'
    ") or die(mysqli_error($conn));

    // kurangi kapasitas area
    mysqli_query($conn, "
        UPDATE tb_area_parkir 
        SET terisi = terisi - 1
        WHERE id_area = (
            SELECT id_area FROM tb_transaksi WHERE id_parkir='$id'
        )
    ") or die(mysqli_error($conn));

    // log
    logAktivitas($conn, $_SESSION['id_user'], "Transaksi keluar ID $id");

    header("Location: struk.php?id=$id");
}
?>
