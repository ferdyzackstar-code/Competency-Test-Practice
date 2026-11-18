<?php
// index.php
session_start();
include 'config/koneksi.php';

// Pastikan user sudah login
if (empty($_SESSION['userid'])) {
    header('Location: login.php');
    exit;
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
            $sql = $pdo->prepare("SELECT gallery_foto.*, gallery_user.NamaLengkap FROM gallery_foto INNER JOIN gallery_user ON gallery_foto.UserID = gallery_user.UserID ORDER BY gallery_foto.FotoID DESC");
            $sql->execute();
            while ($data = $sql->fetch()) {
                $fotoid = $data['FotoID'];
                
                // Menghitung jumlah Like
                $sql_like = $pdo->prepare("SELECT COUNT(*) AS total FROM gallery_likefoto WHERE FotoID = ?");
                $sql_like->execute([$fotoid]);
                $jumlah_like = $sql_like->fetch()['total'];

                // Menghitung jumlah Komentar
                $sql_komen = $pdo->prepare("SELECT COUNT(*) AS total FROM gallery_komentarfoto WHERE FotoID = ?");
                $sql_komen->execute([$fotoid]);
                $jumlah_komen = $sql_komen->fetch()['total'];
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