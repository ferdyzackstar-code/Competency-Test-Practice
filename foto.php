<?php
// foto.php
session_start();
include 'config/koneksi.php';

if (empty($_SESSION['userid'])) {
    header('Location: login.php');
    exit;
}

$userid = $_SESSION['userid'];

// 1. LOGIKA TAMBAH FOTO
if (isset($_POST['tambah_foto'])) {
    $judul = $_POST['JudulFoto'];
    $deskripsi = $_POST['DeskripsiFoto'];
    $albumid = $_POST['AlbumID'];
    $tanggal = date('Y-m-d');

    $foto = $_FILES['LokasiFile']['name'];
    $tmp = $_FILES['LokasiFile']['tmp_name'];
    $nama_foto = rand() . '-' . $foto;
    $lokasi = 'assets/img/';

    $albumCheck = $pdo->prepare("SELECT AlbumID FROM gallery_album WHERE AlbumID = ? AND UserID = ?");
    $albumCheck->execute([$albumid, $userid]);

    $albumExists = $albumCheck->fetch();


    if ($albumExists && move_uploaded_file($tmp, $lokasi . $nama_foto)) {
        $query = $pdo->prepare(
            "INSERT INTO gallery_foto (JudulFoto, DeskripsiFoto, TanggalUnggah, LokasiFile, AlbumID, UserID) VALUES (?, ?, ?, ?, ?, ?)"
        );
        if ($query->execute([$judul, $deskripsi, $tanggal, $nama_foto, $albumid, $userid])) {
            echo "<script>alert('Foto berhasil diunggah!'); window.location='foto.php';</script>";
        } else {
            unlink($lokasi . $nama_foto);
            echo "<script>alert('Gagal menyimpan data foto!');</script>";
        }
    }
}

// 2. LOGIKA EDIT FOTO (Hanya update teks & kategori album)
if (isset($_POST['edit_foto'])) {
    $fotoid = $_POST['FotoID'];
    $judul = $_POST['JudulFoto'];
    $deskripsi = $_POST['DeskripsiFoto'];
    $albumid = $_POST['AlbumID'];

    $albumCheck = $pdo->prepare("SELECT AlbumID FROM gallery_album WHERE AlbumID = ? AND UserID = ?");
    $albumCheck->execute([$albumid,$userid]);

    $albumResult = $albumCheck->fetch();
    if ($albumExists) {
        $query = $pdo->prepare(
            "UPDATE gallery_foto SET JudulFoto = ?, DeskripsiFoto = ?, AlbumID = ? WHERE FotoID = ? AND UserID = ?"
        );

        if ($query->execute([$judul, $deskripsi, $albumid, $fotoid, $userid])) {
            echo "<script>alert('Data foto berhasil diperbarui!'); window.location='foto.php';</script>";
        }
    }
}

// 3. LOGIKA HAPUS FOTO
if (isset($_GET['hapus'])) {
    $fotoid = $_GET['hapus'];
    $query = $pdo->prepare("SELECT LokasiFile FROM gallery_foto WHERE FotoID = ? AND UserID = ?");
    $query->execute([$fotoid,$userid]);
    $result = $query->fetch();

    if ($result) {
        $nama_file = $result['LokasiFile'];
        if (is_file('assets/img/' . $nama_file)) {
            unlink('assets/img/' . $nama_file);
        }

        $delete = $pdo->prepare("DELETE FROM gallery_foto WHERE FotoID = ? AND UserID = ?");
        $delete->execute([$fotoid,$userid]);
        echo "<script>alert('Foto berhasil dihapus!'); window.location='foto.php';</script>";
    }
}

if (isset($_GET['edit'])) {
    $fotoid = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if (!$fotoid) {
        header('Location: foto.php');
        exit;
    }

    $query_edit = $pdo->prepare("SELECT * FROM gallery_foto WHERE FotoID = ? AND UserID = ?");
    $query_edit->execute([$fotoid, $userid]);
    $editResult = $query_edit->fetch();

    if (!$editResult) {
        header('Location: foto.php');
        exit;
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
    <div class="navbar">
        <a href="index.php">Beranda (Galeri)</a>
        <a href="album.php">Kelola Album</a>
        <a href="foto.php">Kelola Foto</a>
        <a href="logout.php" class="right">Logout</a>
    </div>

    <div class="container" style="max-width: 800px;">
        <h2>Kelola Foto</h2>
        
        <?php
        // CEK MODE: Apakah user sedang klik tombol Edit?
        if (isset($_GET['edit'])) {
        ?>
            <!-- TAMPILAN FORM EDIT -->
            <form method="POST" action="">
                <input type="hidden" name="FotoID" value="<?php echo $editResult['FotoID']; ?>">
                <div class="form-group">
                    <label>Judul Foto</label>
                    <input type="text" name="JudulFoto" class="form-control" value="<?php echo $editResult['JudulFoto']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi Foto</label>
                    <textarea name="DeskripsiFoto" class="form-control" required><?php echo $editResult['DeskripsiFoto']; ?></textarea>
                </div>
                <div class="form-group">
                    <label>Pindah Album</label>
                    <select name="AlbumID" class="form-control" required>
                        <?php
                        $sql_album = $pdo->prepare("SELECT * FROM gallery_album WHERE UserID = ?");
                        $sql_album->execute([$userid]);
                        while ($album = $sql_album->fetch()) {
                            // Cek jika album ini adalah album foto tersebut, maka berikan atribut 'selected'
                            $selected = ($album['AlbumID'] == $editResult['AlbumID']) ? 'selected' : '';
                            echo "<option value='".$album['AlbumID']."' $selected>".$album['NamaAlbum']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <!-- Perhatikan: Tidak ada input file gambar di sini! -->
                <button type="submit" name="edit_foto" class="btn" style="background-color: #ff9800;">Simpan Perubahan</button>
                <a href="foto.php" class="btn" style="background-color: #6c757d; display: inline-block; text-align: center; margin-top: 10px; box-sizing: border-box;">Batal Edit</a>
            </form>

        <?php } else { ?>
            <!-- TAMPILAN FORM TAMBAH (Original) -->
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
                        $sql_album = $pdo->prepare("SELECT * FROM gallery_album WHERE UserID = ?");
                        $sql_album->execute([$userid]);
                        while ($album = $sql_album->fetch()) {
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

        <?php } ?>

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
                $sql = $pdo->prepare("SELECT gallery_foto.*, gallery_album.NamaAlbum FROM gallery_foto INNER JOIN gallery_album ON gallery_foto.AlbumID = gallery_album.AlbumID WHERE gallery_foto.UserID = ?");
                $sql->execute([$userid]);
                while ($data = $sql->fetch()) {
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><img src="assets/img/<?php echo $data['LokasiFile']; ?>" width="100" style="border-radius: 4px;"></td>
                    <td><?php echo $data['JudulFoto']; ?></td>
                    <td><?php echo $data['DeskripsiFoto']; ?></td>
                    <td><?php echo $data['NamaAlbum']; ?></td>
                    <td><?php echo $data['TanggalUnggah']; ?></td>
                    <td>
                        <!-- Tambahan Tombol Edit di sini -->
                        <a href="?edit=<?php echo $data['FotoID']; ?>" style="color: #ff9800; font-weight: bold; text-decoration: none;">Edit</a> |
                        <a href="?hapus=<?php echo $data['FotoID']; ?>" onclick="return confirm('Yakin ingin menghapus foto ini?')" style="color: red; text-decoration: none;">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>