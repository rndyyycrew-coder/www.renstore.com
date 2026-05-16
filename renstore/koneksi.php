<?php
// Aktifkan pelaporan kesalahan MySQLi supaya error tampil dan memudahkan debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Konfigurasi koneksi database
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'renstore';

// Buat koneksi baru ke database
$conn = new mysqli($host, $user, $pass, $db);
// Pastikan karakter encoding menggunakan UTF-8 agar data tersimpan dengan baik
$conn->set_charset('utf8mb4');
?>