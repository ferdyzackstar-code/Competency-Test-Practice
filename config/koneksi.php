<?php

$host = "localhost";
$name = "root";
$pass = "";
$db = "db_galeri";

$conn = mysqli_connect($host, $name, $pass, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

?>