<?php
// Import koneksi database untuk mengambil detail order
include 'koneksi.php';

if (!isset($_GET['kode_order'])) {
    header('Location: index.php');
    exit;
}

$kode_order = trim($_GET['kode_order']);

// Ambil data order lengkap dari database
$stmt = $conn->prepare('SELECT o.*, g.nama_game, pr.nama_produk, pm.nama_metode FROM orders o JOIN games g ON o.game_id = g.id JOIN products pr ON o.product_id = pr.id JOIN payment_methods pm ON o.payment_method_id = pm.id WHERE o.kode_order = ? LIMIT 1');
$stmt->bind_param('s', $kode_order);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$order = $result->fetch_assoc();

$pageTitle = 'Transaksi Berhasil - RENSTORE';
include 'header.php';
?>

<section class="section form-section">
    <h2 class="section-title">Transaksi Berhasil</h2>
    <div class="message-box">
        <p>Bukti pembayaran Anda telah diterima dan akan segera diproses.</p>
        <p>Status pesanan dapat dicek menggunakan Kode Order di halaman <a href="cek.php">Cek Transaksi</a>.</p>
        <ul>
            <li><strong>Kode Order:</strong> <?= htmlspecialchars($order['kode_order']) ?></li>
            <li><strong>Game:</strong> <?= htmlspecialchars($order['nama_game']) ?></li>
            <li><strong>Produk:</strong> <?= htmlspecialchars($order['nama_produk']) ?></li>
            <li><strong>User ID Game:</strong> <?= htmlspecialchars($order['user_id_game']) ?></li>
            <?php if ($order['server_id']): ?>
                <li><strong>Server ID:</strong> <?= htmlspecialchars($order['server_id']) ?></li>
            <?php endif; ?>
            <li><strong>Total Harga:</strong> Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></li>
            <li><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($order['nama_metode']) ?></li>
            <li><strong>Nomor Kontak:</strong> <?= htmlspecialchars($order['nomor_kontak']) ?></li>
            <li><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></li>
        </ul>
        <a href="index.php" class="btn btn-primary">Kembali ke Beranda</a>
        <a href="cek.php" class="btn btn-secondary">Cek Status</a>
    </div>
</section>

<?php include 'footer.php'; ?>