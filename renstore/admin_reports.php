<?php
// Halaman laporan dashboard admin
session_start();
include 'koneksi.php';

// Pastikan hanya admin yang bisa mengakses laporan
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Laporan Admin - RENSTORE';
include 'admin_header.php';

$totalOrders = $conn->query('SELECT COUNT(*) as total FROM orders')->fetch_assoc()['total'];
$totalRevenue = $conn->query('SELECT COALESCE(SUM(total_harga), 0) as total FROM orders')->fetch_assoc()['total'];
$ordersByStatus = $conn->query('SELECT status, COUNT(*) as jumlah FROM orders GROUP BY status')->fetch_all(MYSQLI_ASSOC);
$revenueByGame = $conn->query('SELECT g.nama_game, COALESCE(SUM(o.total_harga), 0) as revenue FROM orders o JOIN games g ON o.game_id = g.id GROUP BY g.nama_game')->fetch_all(MYSQLI_ASSOC);
$ordersByPayment = $conn->query('SELECT pm.nama_metode, COUNT(*) as jumlah FROM orders o JOIN payment_methods pm ON o.payment_method_id = pm.id GROUP BY pm.nama_metode')->fetch_all(MYSQLI_ASSOC);
$recentOrders = $conn->query('SELECT o.kode_order, g.nama_game, pr.nama_produk, o.user_id_game, o.server_id, o.total_harga, pm.nama_metode, o.nomor_kontak, o.status FROM orders o LEFT JOIN games g ON o.game_id = g.id LEFT JOIN products pr ON o.product_id = pr.id LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id ORDER BY o.kode_order DESC LIMIT 10')->fetch_all(MYSQLI_ASSOC);
?>

<section class="section">
    <div class="admin-stats">
        <div class="admin-card">
            <h3><i class="fas fa-list"></i> Total Order</h3>
            <div class="stat-number"><?= htmlspecialchars($totalOrders) ?></div>
            <div class="stat-label">Semua order</div>
        </div>
        <div class="admin-card">
            <h3><i class="fas fa-dollar-sign"></i> Total Pendapatan</h3>
            <div class="stat-number">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></div>
            <div class="stat-label">Jumlah pendapatan</div>
        </div>
    </div>

    <div class="admin-table">
        <div class="admin-table-header">
            <h2><i class="fas fa-chart-pie"></i> Ringkasan Status Order</h2>
        </div>
        <div class="admin-table-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Jumlah Order</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ordersByStatus as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['jumlah']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-table">
        <div class="admin-table-header">
            <h2><i class="fas fa-gamepad"></i> Pendapatan per Game</h2>
        </div>
        <div class="admin-table-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($revenueByGame as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama_game']) ?></td>
                            <td>Rp <?= number_format($row['revenue'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-table">
        <div class="admin-table-header">
            <h2><i class="fas fa-credit-card"></i> Order per Metode Pembayaran</h2>
        </div>
        <div class="admin-table-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Metode Pembayaran</th>
                        <th>Jumlah Order</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ordersByPayment as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama_metode']) ?></td>
                            <td><?= htmlspecialchars($row['jumlah']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-table">
        <div class="admin-table-header">
            <h2><i class="fas fa-clock"></i> 10 Order Terbaru</h2>
        </div>
        <div class="admin-table-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode Order</th>
                        <th>Game</th>
                        <th>Produk</th>
                        <th>User ID</th>
                        <th>Server ID</th>
                        <th>Harga</th>
                        <th>Metode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['kode_order']) ?></td>
                            <td><?= htmlspecialchars($order['nama_game']) ?></td>
                            <td><?= htmlspecialchars($order['nama_produk']) ?></td>
                            <td><?= htmlspecialchars($order['user_id_game']) ?></td>
                            <td><?= htmlspecialchars($order['server_id']) ?></td>
                            <td>Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($order['nama_metode']) ?></td>
                            <td><?= htmlspecialchars($order['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include 'admin_footer.php'; ?>