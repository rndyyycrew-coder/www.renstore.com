<?php
// Mulai session jika belum berjalan agar fungsi login dan tampilan dinamis dapat bekerja
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Judul halaman default jika tidak ditetapkan
$pageTitle = $pageTitle ?? 'RENSTORE';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigasi utama situs -->
    <div class="nav">
        <div class="logo">
            <img src="assets/logo.png" alt="Logo RENSTORE">
            <div>
                <span class="brand-name">RENSTORE</span>
                <small>Top Up Game Cepat & Aman</small>
            </div>
        </div>
        <div class="menu">
            <a href="index.php">Beranda</a>
            <a href="cek.php">Cek Transaksi</a>
            <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
                <!-- Link kontak tetap sama untuk pengguna yang sudah login -->
                <a href="#tentang-kami">Tentang kami</a>
            <?php else: ?>
                <a href="#tentang-kami">Tentang kami</a>
            <?php endif; ?>
        </div>
    </div>
    <main class="page">
