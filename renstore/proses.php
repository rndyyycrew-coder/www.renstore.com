<?php
// Import koneksi database
include 'koneksi.php';

// Pastikan request berasal dari form POST agar tidak bisa diakses langsung lewat URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Ambil data dari form topup
$game = trim($_POST['game'] ?? '');
$user_id_game = trim($_POST['user_id'] ?? '');
$server_id = trim($_POST['server'] ?? '');
$paket = trim($_POST['nominal'] ?? '');
$nomor_kontak = trim($_POST['kontak'] ?? '');
$metode = trim($_POST['metode'] ?? '');

// Cari id game berdasarkan nama game yang dipilih
$game_id = 0;
$gameStmt = $conn->prepare("SELECT id FROM games WHERE nama_game = ?");
$gameStmt->bind_param('s', $game);
$gameStmt->execute();
$gameResult = $gameStmt->get_result();
if ($gameResult && $gameResult->num_rows > 0) {
    $gameRow = $gameResult->fetch_assoc();
    $game_id = $gameRow['id'];
}

// Cari id metode pembayaran berdasarkan nama metode
$payment_method_id = 0;
$paymentStmt = $conn->prepare("SELECT id FROM payment_methods WHERE nama_metode = ?");
$paymentStmt->bind_param('s', $metode);
$paymentStmt->execute();
$paymentResult = $paymentStmt->get_result();
if ($paymentResult && $paymentResult->num_rows > 0) {
    $paymentRow = $paymentResult->fetch_assoc();
    $payment_method_id = $paymentRow['id'];
}

// Ambil harga dari paket yang dipilih, hapus format text seperti "Rp." dan tanda titik
$parts = explode(' - ', $paket);
$total_harga = isset($parts[1]) ? (int) str_replace(['Rp. ', '.', ','], '', $parts[1]) : 0;

// Cari product_id berdasarkan game dan total harga paket
$product_id = 0;
$productStmt = $conn->prepare("SELECT id FROM products WHERE game_id = ? AND harga = ?");
$productStmt->bind_param('ii', $game_id, $total_harga);
$productStmt->execute();
$productResult = $productStmt->get_result();
if ($productResult && $productResult->num_rows > 0) {
    $productRow = $productResult->fetch_assoc();
    $product_id = $productRow['id'];
}

// Validasi input sebelum menyimpan order ke database
$errors = [];
if ($game_id === 0) {
    $errors[] = 'Jenis game tidak valid.';
}
if ($user_id_game === '') {
    $errors[] = 'User ID harus diisi.';
}
if ($paket === '' || $total_harga === 0) {
    $errors[] = 'Paket nominal harus dipilih.';
}
if ($product_id === 0) {
    $errors[] = 'Paket tidak ditemukan.';
}
if ($nomor_kontak === '') {
    $errors[] = 'Nomor kontak harus diisi.';
}
if ($payment_method_id === 0) {
    $errors[] = 'Metode pembayaran tidak valid.';
}

if ($errors) {
    // Tampilkan halaman error jika ada masalah input
    $pageTitle = 'Error Transaksi - RENSTORE';
    include 'header.php';
    echo '<section class="section form-section"><h2 class="section-title">Transaksi Gagal</h2>';
    echo '<div class="alert alert-error"><ul>';
    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul></div>';
    echo '<a href="javascript:history.back()" class="btn btn-secondary">Kembali</a></section>';
    include 'footer.php';
    exit;
}

// Buat kode order unik untuk transaksi ini
$kode_order = '';
do {
    $kode_order = 'ORD-' . sprintf('%05d', random_int(0, 99999));
    $checkStmt = $conn->prepare('SELECT COUNT(*) AS total FROM orders WHERE kode_order = ?');
    $checkStmt->bind_param('s', $kode_order);
    $checkStmt->execute();
    $exists = $checkStmt->get_result()->fetch_assoc()['total'] > 0;
} while ($exists);

// Simpan order ke tabel orders
$stmt = $conn->prepare('INSERT INTO orders (kode_order, game_id, product_id, user_id_game, server_id, payment_method_id, nomor_kontak, total_harga, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
$status = 'pending';
$stmt->bind_param('siiisiiis', $kode_order, $game_id, $product_id, $user_id_game, $server_id, $payment_method_id, $nomor_kontak, $total_harga, $status);
$stmt->execute();

// Arahkan pengguna ke halaman upload bukti pembayaran
header('Location: upload_bukti.php?kode_order=' . urlencode($kode_order));
exit;
