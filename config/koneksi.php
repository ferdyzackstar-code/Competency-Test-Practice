<?php

$host = "localhost";
$name = "root";
$pass = "";
$dbname = "db_galeri";

$dsn = "mysql:host=$host;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $name, $pass);
    //echo "Koneksi Database Berhasil!";
} catch (\PDOException $e) {
    die("Koneksi Gagal: " . $e->getMessage());
}

?>