<?php
if (!function_exists('logAktivitas')) {

function logAktivitas($conn, $id_user, $aktivitas) {
    date_default_timezone_set('Asia/Jakarta');

    $id_user   = mysqli_real_escape_string($conn, $id_user);
    $aktivitas = mysqli_real_escape_string($conn, $aktivitas);

    $sql = "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) 
            VALUES ('$id_user', '$aktivitas', NOW())";

    if (!mysqli_query($conn, $sql)) {
        echo "Error log aktivitas: " . mysqli_error($conn);
    }
}

}
?>