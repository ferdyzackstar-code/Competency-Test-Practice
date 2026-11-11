<?php
// index.php
session_start();
include 'config/koneksi.php';

// Pastikan user sudah login
if ($_SESSION['status_login'] != true) {
    echo "<script>alert('Anda belum login!'); window.location='login.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Beranda - Galeri Foto</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <a href="index.php">Beranda (Galeri)</a>
        <a href="album.php">Kelola Album</a>
        <a href="foto.php">Kelola Foto</a>
        <a href="logout.php" class="right">Logout</a>
    </div>

    <div class="container" style="max-width: 1000px;">
        <h2>Galeri Foto Terbaru</h2>
        <p>Selamat datang, <b><?php echo $_SESSION['username']; ?></b>!</p>

        <div class="gallery-grid">
            <?php
            // Mengambil semua data foto beserta nama pengunggahnya (JOIN ke tabel user)
            $sql = mysqli_query($conn, "SELECT gallery_foto.*, gallery_user.NamaLengkap FROM gallery_foto INNER JOIN gallery_user ON gallery_foto.UserID = gallery_user.UserID ORDER BY gallery_foto.FotoID DESC");
            
            while ($data = mysqli_fetch_array($sql)) {
                $fotoid = $data['FotoID'];
                
                // Menghitung jumlah Like
                $sql_like = mysqli_query($conn, "SELECT * FROM gallery_likefoto WHERE FotoID='$fotoid'");
                $jumlah_like = mysqli_num_rows($sql_like);

                // Menghitung jumlah Komentar
                $sql_komen = mysqli_query($conn, "SELECT * FROM gallery_komentarfoto WHERE FotoID='$fotoid'");
                $jumlah_komen = mysqli_num_rows($sql_komen);
            ?>
            <div class="gallery-item">
                <img src="assets/img/<?php echo $data['LokasiFile']; ?>" alt="<?php echo $data['JudulFoto']; ?>">
                <h4><?php echo $data['JudulFoto']; ?></h4>
                <p style="font-size: 12px; color: #666;">Oleh: <?php echo $data['NamaLengkap']; ?></p>
                
                <div class="action-links">
                    <!-- Menggunakan HTML Entity &#10084; untuk icon Love tanpa butuh internet -->
                    <a href="like.php?id=<?php echo $fotoid; ?>" style="color: red;">&#10084; <?php echo $jumlah_like; ?> Suka</a>
                    <a href="komentar.php?id=<?php echo $fotoid; ?>" style="color: blue;">&#128172; <?php echo $jumlah_komen; ?> Komentar</a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>