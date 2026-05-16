<?php
// Mulai session untuk memastikan user dapat melihat order yang benar
session_start();
include 'koneksi.php';

// Pastikan kode order tersedia di query string
if (!isset($_GET['kode_order'])) {
    header('Location: index.php');
    exit;
}

$kode_order = trim($_GET['kode_order']);

// Ambil detail order dari database untuk ditampilkan di halaman upload bukti pembayaran
$stmt = $conn->prepare('SELECT o.*, g.nama_game, pr.nama_produk, pm.nama_metode FROM orders o JOIN games g ON o.game_id = g.id JOIN products pr ON o.product_id = pr.id JOIN payment_methods pm ON o.payment_method_id = pm.id WHERE o.kode_order = ? LIMIT 1');
$stmt->bind_param('s', $kode_order);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$order = $result->fetch_assoc();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bukti_pembayaran'])) {
    $file = $_FILES['bukti_pembayaran'];

    // Validasi file upload
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = 'Error saat upload file.';
    } elseif (!in_array($file['type'], $allowed_types)) {
        $message = 'Format file tidak didukung. Gunakan JPG, PNG.';
    } elseif ($file['size'] > $max_size) {
        $message = 'Ukuran file terlalu besar. Maksimal 5MB.';
    } else {
        // Buat nama file unik dan path penyimpanan
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'bukti_' . $kode_order . '_' . time() . '.' . $ext;
        $upload_path = 'uploads/bukti/' . $filename;

        // Pastikan direktori tujuan ada
        if (!is_dir('uploads/bukti')) {
            mkdir('uploads/bukti', 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Update status order menjadi dibayar dan simpan path bukti
            $stmt = $conn->prepare('UPDATE orders SET status = ?, bukti_pembayaran = ? WHERE kode_order = ?');
            $status = 'dibayar';
            $stmt->bind_param('sss', $status, $upload_path, $kode_order);
            $stmt->execute();

            // Arahkan ke halaman sukses setelah upload berhasil
            header('Location: transaksi_berhasil.php?kode_order=' . urlencode($kode_order));
            exit;
        } else {
            $message = 'Gagal menyimpan file.';
        }
    }
}

$pageTitle = 'Upload Bukti Pembayaran - RENSTORE';
include 'header.php';
?>

<section class="section form-section">
    <h2 class="section-title">Upload Bukti Pembayaran</h2>

    <!-- Ringkasan pesanan yang harus dikonfirmasi pembayaran -->
    <div class="order-summary">
        <h3>Ringkasan Pesanan</h3>
        <ul>
            <li><strong>Kode Order:</strong> <span><?= htmlspecialchars($order['kode_order']) ?></span></li>
            <li><strong>Game:</strong> <span><?= htmlspecialchars($order['nama_game']) ?></span></li>
            <li><strong>Produk:</strong> <span><?= htmlspecialchars($order['nama_produk']) ?></span></li>
            <li><strong>User ID Game:</strong> <span><?= htmlspecialchars($order['user_id_game']) ?></span></li>
            <?php if ($order['server_id']): ?>
                <li><strong>Server ID:</strong> <span><?= htmlspecialchars($order['server_id']) ?></span></li>
            <?php endif; ?>
            <li class="total-price"><strong>Total Harga:</strong> <span>Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></span></li>
            <li><strong>Metode Pembayaran:</strong> <span><?= htmlspecialchars($order['nama_metode']) ?></span></li>
            <li><strong>Nomor Kontak:</strong> <span><?= htmlspecialchars($order['nomor_kontak']) ?></span></li>
        </ul>
    </div>

    <?php if ($message): ?>
        <!-- Tampilkan pesan error jika upload gagal -->
        <div class="alert alert-error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form-box">
        <label>Upload Bukti Pembayaran</label>
        <input type="file" name="bukti_pembayaran" accept="image/*" required>
        <small>Format: JPG, PNG. Maksimal 5MB</small>

        <button type="submit" class="btn btn-primary">Upload & Konfirmasi</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</section>

<?php include 'footer.php'; ?>