<?php
// komentar.php
session_start();
include 'config/koneksi.php';

if ($_SESSION['status_login'] != true) {
    echo "<script>alert('Anda belum login!'); window.location='login.php';</script>";
}

$fotoid = $_GET['id'];

// Logika Tambah Komentar
if (isset($_POST['kirim_komentar'])) {
    $isi_komentar = $_POST['IsiKomentar'];
    $userid = $_SESSION['userid'];
    $tanggal = date('Y-m-d');

    $query = mysqli_query($conn, "INSERT INTO gallery_komentarfoto (FotoID, UserID, IsiKomentar, TanggalKomentar) VALUES ('$fotoid', '$userid', '$isi_komentar', '$tanggal')");
    
    if ($query) {
        // Refresh halaman agar komentar baru langsung muncul
        echo "<script>window.location='komentar.php?id=$fotoid';</script>";
    }
}

// Ambil detail foto
$sql_foto = mysqli_query($conn, "SELECT * FROM gallery_foto INNER JOIN gallery_user ON gallery_foto.UserID = gallery_user.UserID WHERE FotoID='$fotoid'");
$detail = mysqli_fetch_array($sql_foto);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Komentar Foto - Galeri</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <a href="index.php">Kembali ke Beranda</a>
    </div>

    <div class="container" style="max-width: 800px;">
        <h2 class="text-center"><?php echo $detail['JudulFoto']; ?></h2>
        <div class="text-center">
            <img src="assets/img/<?php echo $detail['LokasiFile']; ?>" style="max-width: 100%; border-radius: 8px;">
        </div>
        <p style="margin-top: 15px;"><b>Deskripsi:</b> <?php echo $detail['DeskripsiFoto']; ?></p>
        <p style="font-size: 14px; color: #555;">Diunggah oleh: <?php echo $detail['NamaLengkap']; ?> pada <?php echo $detail['TanggalUnggah']; ?></p>
        
        <hr style="margin: 20px 0;">

        <!-- Form Input Komentar -->
        <form method="POST" action="">
            <div class="form-group">
                <label>Tulis Komentar Anda:</label>
                <textarea name="IsiKomentar" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" name="kirim_komentar" class="btn">Kirim Komentar</button>
        </form>

        <h3 style="margin-top: 30px;">Kolom Komentar</h3>
        <?php
        // Ambil semua komentar untuk foto ini, urutkan dari yang terbaru
        $sql_komen = mysqli_query($conn, "SELECT gallery_komentarfoto.*, gallery_user.NamaLengkap FROM gallery_komentarfoto INNER JOIN gallery_user ON gallery_komentarfoto.UserID = gallery_user.UserID WHERE FotoID='$fotoid' ORDER BY KomentarID DESC");
        
        if (mysqli_num_rows($sql_komen) > 0) {
            while ($komen = mysqli_fetch_array($sql_komen)) {
        ?>
            <div class="komentar-box">
                <b><?php echo $komen['NamaLengkap']; ?></b> 
                <span style="font-size: 12px; color: #777;">(<?php echo $komen['TanggalKomentar']; ?>)</span>
                <p style="margin: 5px 0 0 0;"><?php echo $komen['IsiKomentar']; ?></p>
            </div>
        <?php 
            }
        } else {
            echo "<p style='color: #777;'>Belum ada komentar. Jadilah yang pertama berkomentar!</p>";
        }
        ?>
    </div>
</body>
</html>