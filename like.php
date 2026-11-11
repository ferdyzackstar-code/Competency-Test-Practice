<?php
// like.php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['userid'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

$fotoid = $_GET['id'];
$userid = $_SESSION['userid'];
$tanggal = date('Y-m-d');

// Cek apakah user sudah pernah like foto ini
$cek = mysqli_query($conn, "SELECT * FROM gallery_likefoto WHERE FotoID='$fotoid' AND UserID='$userid'");

if (mysqli_num_rows($cek) > 0) {
    // Jika sudah like, maka batalkan (Unlike) dengan menghapus datanya
    mysqli_query($conn, "DELETE FROM gallery_likefoto WHERE FotoID='$fotoid' AND UserID='$userid'");
} else {
    // Jika belum like, maka tambahkan ke database
    // Perhatikan urutan value: LikeID (kosongkan agar auto_increment jalan), FotoID, UserID, Tanggal
    mysqli_query($conn, "INSERT INTO gallery_likefoto (FotoID, UserID, TanggalLike) VALUES ('$fotoid', '$userid', '$tanggal')");
}

// Kembalikan user ke halaman sebelumnya (entah dari index atau dari detail komentar)
header("Location: " . $_SERVER['HTTP_REFERER']);
?>