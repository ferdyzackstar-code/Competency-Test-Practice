<?php
// album.php
session_start();
include 'config/koneksi.php';

// Proteksi agar user yang belum login tidak bisa masuk
if ($_SESSION['status_login'] != true) {
    echo "<script>alert('Anda belum login!'); window.location='login.php';</script>";
}

// Logika Tambah Album
if (isset($_POST['tambah_album'])) {
    $nama_album = $_POST['NamaAlbum'];
    $deskripsi = $_POST['Deskripsi'];
    $tanggal = date('Y-m-d');
    $userid = $_SESSION['userid'];

    $query = mysqli_query($conn, "INSERT INTO gallery_album (NamaAlbum, Deskripsi, TanggalDibuat, UserID) VALUES ('$nama_album', '$deskripsi', '$tanggal', '$userid')");

    if ($query) {
        echo "<script>alert('Album berhasil ditambahkan!'); window.location='album.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah album!');</script>";
    }
}

// Logika Hapus Album
if (isset($_GET['hapus'])) {
    $albumid = $_GET['hapus'];
    // Hapus album sesuai ID. Karena di DB kita set CASCADE, foto di dalamnya otomatis ikut terhapus!
    $query = mysqli_query($conn, "DELETE FROM gallery_album WHERE AlbumID='$albumid'");
    
    if ($query) {
        echo "<script>alert('Album berhasil dihapus!'); window.location='album.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Album - Galeri Foto</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="index.php">Beranda (Galeri)</a>
        <a href="album.php">Kelola Album</a>
        <a href="foto.php">Kelola Foto</a>
        <a href="logout.php" class="right">Logout</a>
    </div>

    <div class="container" style="max-width: 800px;">
        <h2>Kelola Album</h2>
        
        <!-- Form Tambah Album -->
        <form method="POST" action="">
            <div class="form-group">
                <label>Nama Album</label>
                <input type="text" name="NamaAlbum" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Deskripsi Album</label>
                <textarea name="Deskripsi" class="form-control" required></textarea>
            </div>
            <button type="submit" name="tambah_album" class="btn">Tambah Album</button>
        </form>

        <hr style="margin: 30px 0;">

        <!-- Tabel Tampil Album -->
        <h3>Daftar Album Saya</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Album</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $userid = $_SESSION['userid'];
                $no = 1;
                $sql = mysqli_query($conn, "SELECT * FROM gallery_album WHERE UserID='$userid'");
                while ($data = mysqli_fetch_array($sql)) {
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $data['NamaAlbum']; ?></td>
                    <td><?php echo $data['Deskripsi']; ?></td>
                    <td><?php echo $data['TanggalDibuat']; ?></td>
                    <td>
                        <!-- Tombol Hapus dengan konfirmasi -->
                        <a href="?hapus=<?php echo $data['AlbumID']; ?>" onclick="return confirm('Yakin ingin menghapus album ini? Semua foto di dalamnya akan ikut terhapus!')" style="color: red;">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>