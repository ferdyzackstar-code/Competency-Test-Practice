<?php
// login.php
session_start();
include 'config/koneksi.php';

if (isset($_POST['login'])) {
    $username = $_POST['Username'];
    $password = md5($_POST['Password']);

    $query = mysqli_query($conn, "SELECT * FROM gallery_user WHERE Username='$username' AND Password='$password'");
    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $data = mysqli_fetch_array($query);
        $_SESSION['userid'] = $data['UserID'];
        $_SESSION['username'] = $data['Username'];
        $_SESSION['status_login'] = true;
        
        echo "<script>alert('Login Berhasil!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Username atau Password salah!'); window.location='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - Galeri Foto</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Login Galeri</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="Username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="Password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
        </form>
        <p class="text-center" style="margin-top: 15px;">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</body>
</html>