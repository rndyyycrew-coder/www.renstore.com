<?php
// Import koneksi database untuk mencari informasi order
include 'koneksi.php';

// Ambil kode order dari form
$kode = trim($_POST['kode'] ?? '');
$pageTitle = 'Hasil Transaksi - RENSTORE';
include 'header.php';

// Jika kode order kosong, tampilkan pesan error
if ($kode === '') {
    echo '<section class="section form-section"><h2 class="section-title">Hasil Transaksi</h2><div class="alert alert-error">Kode order tidak boleh kosong.</div><a href="cek.php" class="btn btn-secondary">Kembali</a></section>';
    include 'footer.php';
    exit;
}

// Query data order berdasarkan kode order
$stmt = $conn->prepare('SELECT o.kode_order, o.total_harga, o.status, o.user_id_game AS user_id, o.server_id AS server, o.nomor_kontak AS kontak, g.nama_game AS game, pr.nama_produk AS nominal, pm.nama_metode AS metode FROM orders o LEFT JOIN games g ON o.game_id = g.id LEFT JOIN products pr ON o.product_id = pr.id LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id WHERE o.kode_order = ? LIMIT 1');
$stmt->bind_param('s', $kode);
$stmt->execute();
$result = $stmt->get_result();

// Jika order tidak ditemukan, beritahu pengguna
if ($result->num_rows === 0) {
    echo '<section class="section form-section"><h2 class="section-title">Hasil Transaksi</h2><div class="alert alert-error">Transaksi dengan kode order tersebut tidak ditemukan.</div><a href="cek.php" class="btn btn-secondary">Kembali</a></section>';
    include 'footer.php';
    exit;
}

$d = $result->fetch_assoc();
?>

<section class="section form-section">
    <h2 class="section-title">Hasil Transaksi</h2>
    <div class="message-box">
        <!-- Tampilkan detail order yang ditemukan -->
        <ul>
            <li><strong>Kode Order:</strong> <?= htmlspecialchars($d['kode_order']) ?></li>
            <li><strong>Game:</strong> <?= htmlspecialchars($d['game']) ?></li>
            <li><strong>User ID:</strong> <?= htmlspecialchars($d['user_id']) ?></li>
            <?php if ($d['server']): ?>
                <li><strong>Server:</strong> <?= htmlspecialchars($d['server']) ?></li>
            <?php endif; ?>
            <li><strong>Nominal:</strong> <?= htmlspecialchars($d['nominal']) ?></li>
            <li><strong>Total Harga:</strong> Rp <?= number_format($d['total_harga'], 0, ',', '.') ?></li>
            <li><strong>Metode:</strong> <?= htmlspecialchars($d['metode']) ?></li>
            <li><strong>Nomor Kontak:</strong> <?= htmlspecialchars($d['kontak']) ?></li>
            <li><strong>Status:</strong> <?= htmlspecialchars($d['status']) ?></li>
        </ul>
        <a href="index.php" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
</section>

<?php include 'footer.php'; ?>
```````