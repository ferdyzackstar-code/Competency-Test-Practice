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
$cek = $pdo->prepare("SELECT 1 FROM gallery_likefoto WHERE FotoID = ? AND UserID = ?");
$cek->execute([$fotoid, $userid]);
$sudah_like = $cek->fetch();

if ($sudah_like) {
    // Jika sudah like, maka batalkan (Unlike) dengan menghapus datanya
    $query = $pdo->prepare("DELETE FROM gallery_likefoto WHERE FotoID = ? AND UserID = ?");
    $query->execute([$fotoid, $userid]);
} else {
    // Jika belum like, maka tambahkan ke database
    // Perhatikan urutan value: LikeID (kosongkan agar auto_increment jalan), FotoID, UserID, Tanggal
    $query = $pdo->prepare("INSERT INTO gallery_likefoto (FotoID, UserID, TanggalLike) VALUES (?, ?, ?)");
    $query->execute([$fotoid, $userid, $tanggal]);
}

header('Location: index.php');
exit;
?>