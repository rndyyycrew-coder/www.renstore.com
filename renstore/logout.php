<?php
// Hapus semua session saat logout dan kirim kembali ke halaman login
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit;
?>