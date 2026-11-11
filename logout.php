<?php
// logout.php
session_start();
session_destroy();
echo "<script>alert('Anda telah berhasil logout.'); window.location='login.php';</script>";
?>