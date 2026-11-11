<?php
// foto.php
session_start();
include 'config/koneksi.php';

if ($_SESSION['status_login'] != true) {
    echo "<script>alert('Anda belum login!'); window.location='login.php';</script>";
}

// Logika Tambah Foto
if (isset($_POST['tambah_foto'])) {
    $judul = $_POST['JudulFoto'];
    $deskripsi = $_POST['DeskripsiFoto'];
    $albumid = $_POST['AlbumID'];
    $tanggal = date('Y-m-d');
    $userid = $_SESSION['userid'];

    // Menangkap data file gambar
    $foto = $_FILES['LokasiFile']['name'];
    $tmp = $_FILES['LokasiFile']['tmp_name'];
    
    // Generate nama file acak agar tidak ada nama file yang bentrok/tertumpuk
    $nama_foto = rand() . '-' . $foto;
    $lokasi = 'assets/img/';

    // Pindahkan file dari penyimpanan sementara (tmp) ke folder assets/img/
    if (move_uploaded_file($tmp, $lokasi . $nama_foto)) {
        $query = mysqli_query($conn, "INSERT INTO gallery_foto (JudulFoto, DeskripsiFoto, TanggalUnggah, LokasiFile, AlbumID, UserID) VALUES ('$judul', '$deskripsi', '$tanggal', '$nama_foto', '$albumid', '$userid')");
        
        if ($query) {
            echo "<script>alert('Foto berhasil diunggah!'); window.location='foto.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan data ke database!');</script>";
        }
    } else {
        echo "<script>alert('Gagal mengunggah file gambar!');</script>";
    }
}

// Logika Hapus Foto
if (isset($_GET['hapus'])) {
    $fotoid = $_GET['hapus'];
    
    // Cari nama file foto di database untuk dihapus dari folder fisik
    $query = mysqli_query($conn, "SELECT LokasiFile FROM gallery_foto WHERE FotoID='$fotoid'");
    $data = mysqli_fetch_array($query);
    $nama_file = $data['LokasiFile'];
    
    // Hapus file fisik dari folder assets/img/ jika file tersebut ada
    if (is_file('assets/img/' . $nama_file)) {
        unlink('assets/img/' . $nama_file);
    }

    // Hapus data dari database
    $hapus_db = mysqli_query($conn, "DELETE FROM gallery_foto WHERE FotoID='$fotoid'");
    
    if ($hapus_db) {
        echo "<script>alert('Foto berhasil dihapus!'); window.location='foto.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Foto - Galeri Foto</title>
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
        <h2>Kelola Foto</h2>
        
        <!-- Form Tambah Foto. WAJIB pakai enctype="multipart/form-data" -->
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>Judul Foto</label>
                <input type="text" name="JudulFoto" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Deskripsi Foto</label>
                <textarea name="DeskripsiFoto" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label>Pilih Album</label>
                <select name="AlbumID" class="form-control" required>
                    <option value="">-- Pilih Album --</option>
                    <?php
                    $userid = $_SESSION['userid'];
                    // Menampilkan daftar album milik user yang sedang login
                    $sql_album = mysqli_query($conn, "SELECT * FROM gallery_album WHERE UserID='$userid'");
                    while ($album = mysqli_fetch_array($sql_album)) {
                        echo "<option value='".$album['AlbumID']."'>".$album['NamaAlbum']."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>File Foto</label>
                <input type="file" name="LokasiFile" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" name="tambah_foto" class="btn">Unggah Foto</button>
        </form>

        <hr style="margin: 30px 0;">

        <!-- Tabel Tampil Foto -->
        <h3>Daftar Foto Saya</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Album</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                // Query JOIN untuk mengambil nama album dari tabel gallery_album
                $sql = mysqli_query($conn, "SELECT gallery_foto.*, gallery_album.NamaAlbum FROM gallery_foto INNER JOIN gallery_album ON gallery_foto.AlbumID = gallery_album.AlbumID WHERE gallery_foto.UserID='$userid'");
                while ($data = mysqli_fetch_array($sql)) {
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td>
                        <!-- Menampilkan Thumbnail Foto -->
                        <img src="assets/img/<?php echo $data['LokasiFile']; ?>" width="100" style="border-radius: 4px;">
                    </td>
                    <td><?php echo $data['JudulFoto']; ?></td>
                    <td><?php echo $data['DeskripsiFoto']; ?></td>
                    <td><?php echo $data['NamaAlbum']; ?></td>
                    <td><?php echo $data['TanggalUnggah']; ?></td>
                    <td>
                        <a href="?hapus=<?php echo $data['FotoID']; ?>" onclick="return confirm('Yakin ingin menghapus foto ini?')" style="color: red;">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>