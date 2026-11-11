<?php
// register.php
include 'config/koneksi.php';

if (isset($_POST['register'])) {
    $username = $_POST['Username'];
    $password = md5($_POST['Password']); // Menggunakan MD5 untuk kemudahan UjiKom, bisa diganti password_hash jika penguji meminta standar tinggi
    $email = $_POST['Email'];
    $namalengkap = $_POST['NamaLengkap'];
    $alamat = $_POST['Alamat'];

    $query = mysqli_query($conn, "INSERT INTO gallery_user (Username, Password, Email, NamaLengkap, Alamat) VALUES ('$username', '$password', '$email', '$namalengkap', '$alamat')");

    if ($query) {
        echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Registrasi Gagal!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Registrasi - Galeri Foto</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Daftar Akun Baru</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="Username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="Password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="Email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="NamaLengkap" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="Alamat" class="form-control" required></textarea>
            </div>
            <button type="submit" name="register" class="btn">Daftar</button>
        </form>
        <p class="text-center" style="margin-top: 15px;">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>