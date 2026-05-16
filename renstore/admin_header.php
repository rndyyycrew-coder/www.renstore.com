<?php
// Mulai session jika belum, untuk menjaga status login admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Judul halaman dashboard admin
$pageTitle = $pageTitle ?? 'Dashboard Admin - RENSTORE';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header dashboard admin -->
    <div class="admin-header">
        <div class="admin-header-content">
            <div class="admin-logo">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard Admin</span>
            </div>
            <div class="admin-user-info">
                <span>Admin RENSTORE</span>
                <a href="logout.php" class="admin-logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Sidebar navigasi untuk halaman admin -->
    <div class="admin-sidebar">
        <nav class="admin-nav">
            <a href="dashboard.php" class="admin-nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="admin_games.php" class="admin-nav-link">
                <i class="fas fa-gamepad"></i> Kelola Games
            </a>
            <a href="admin_products.php" class="admin-nav-link">
                <i class="fas fa-box"></i> Kelola Products
            </a>
            <a href="admin_payments.php" class="admin-nav-link">
                <i class="fas fa-credit-card"></i> Kelola Payments
            </a>
            <a href="admin_reports.php" class="admin-nav-link">
                <i class="fas fa-chart-bar"></i> Laporan
            </a>
        </nav>
    </div>

    <div class="admin-main-content">