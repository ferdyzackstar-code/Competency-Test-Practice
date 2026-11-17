<?php
// like.php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['userid'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

$fotoid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$fotoid) {
    header('Location: index.php');
    exit;
}

$userid = $_SESSION['userid'];
$tanggal = date('Y-m-d');

// Cek apakah user sudah pernah like foto ini
$cek = $conn->prepare("SELECT 1 FROM gallery_likefoto WHERE FotoID = ? AND UserID = ?");
$cek->bind_param("ii", $fotoid, $userid);
$cek->execute();

if ($cek->get_result()->num_rows > 0) {
    // Jika sudah like, maka batalkan (Unlike) dengan menghapus datanya
    $query = $conn->prepare("DELETE FROM gallery_likefoto WHERE FotoID = ? AND UserID = ?");
    $query->bind_param("ii", $fotoid, $userid);
    $query->execute();
} else {
    // Jika belum like, maka tambahkan ke database
    // Perhatikan urutan value: LikeID (kosongkan agar auto_increment jalan), FotoID, UserID, Tanggal
    $query = $conn->prepare("INSERT INTO gallery_likefoto (FotoID, UserID, TanggalLike) VALUES (?, ?, ?)");
    $query->bind_param("iis", $fotoid, $userid, $tanggal);
    $query->execute();
}

header('Location: index.php');
exit;
?>