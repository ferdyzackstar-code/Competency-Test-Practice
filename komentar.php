<?php
// komentar.php
session_start();
include 'config/koneksi.php';

if (empty($_SESSION['userid'])) {
    header('Location: login.php');
    exit;
}

$fotoid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$fotoid) {
    header('Location: index.php');
    exit;
}

// Logika Tambah Komentar
if (isset($_POST['kirim_komentar'])) {
    $isi_komentar = $_POST['IsiKomentar'];
    $userid = $_SESSION['userid'];
    $tanggal = date('Y-m-d');

    $query = $pdo->prepare(
        "INSERT INTO gallery_komentarfoto (FotoID, UserID, IsiKomentar, TanggalKomentar) VALUES (?, ?, ?, ?)"
    );    
    if ($query->execute([$fotoid, $userid, $isi_komentar, $tanggal])) {
        // Refresh halaman agar komentar baru langsung muncul
        echo "<script>window.location='komentar.php?id=$fotoid';</script>";
    }
}

// Ambil detail foto
$sql_foto = $pdo->prepare(
    "SELECT * FROM gallery_foto INNER JOIN gallery_user ON gallery_foto.UserID = gallery_user.UserID WHERE FotoID = ?"
);
$sql_foto->execute([$fotoid]);
$detail = $sql_foto->fetch();
if (!$detail) {
    header('Location: index.php');
    exit;
}
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
        $sql_komen = $pdo->prepare(
            "SELECT gallery_komentarfoto.*, gallery_user.NamaLengkap FROM gallery_komentarfoto INNER JOIN gallery_user ON gallery_komentarfoto.UserID = gallery_user.UserID WHERE FotoID = ? ORDER BY KomentarID DESC"
        );
        $sql_komen->execute([$fotoid]);
        $ada_komentar = false;
        while ($komen = $sql_komen->fetch()) {
            $ada_komentar = true;
        ?>
            <div class="komentar-box" style="background: #f9f9f9; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
                <b><?php echo htmlspecialchars($komen['NamaLengkap']); ?></b> 
                <span style="font-size: 12px; color: #777;">(<?php echo $komen['TanggalKomentar']; ?>)</span>
                <p style="margin: 5px 0 0 0;"><?php echo nl2br(htmlspecialchars($komen['IsiKomentar'])); ?></p>
            </div>
        <?php 
        }

        if (!$ada_komentar) {
            echo "<p style='color: #777;'>Belum ada komentar. Jadilah yang pertama berkomentar!</p>";
        }
        ?>
    </div>
</body>
</html>