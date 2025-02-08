<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db = "midproject_bncc";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = mysqli_prepare($koneksi, "SELECT photo FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($data) {
        $photoPath = __DIR__ . '/' . $data['photo'];
        if (!empty($data['photo']) && file_exists($photoPath)) {
            unlink($photoPath);
        }

        $stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('User berhasil dihapus!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('User tidak ditemukan!'); window.location.href='dashboard.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location.href='dashboard.php';</script>";
}

mysqli_close($koneksi);
?>
