<?php
// Set judul halaman untuk ditampilkan di bagian header
$pageTitle = 'Top Up Game - RENSTORE';

// Sertakan file koneksi dan header umum situs
include 'koneksi.php';
include 'header.php';

// Ambil daftar metode pembayaran dari database
$payments = [];
$paymentResult = $conn->query('SELECT nama_metode FROM payment_methods ORDER BY id ASC');
if ($paymentResult) {
    while ($row = $paymentResult->fetch_assoc()) {
        $payments[] = $row['nama_metode'];
    }
}

// Ambil parameter game dari URL, lalu ubah menjadi lowercase dan hilangkan spasi kosong di depan/belakang
$gameKey = strtolower(trim($_GET['game'] ?? 'ml'));

// Daftar game yang tersedia untuk top up
$availableGames = ['ml', 'ff', 'pubg'];

// Jika parameter game tidak valid, gunakan default 'ml'
if (!in_array($gameKey, $availableGames, true)) {
    $gameKey = 'ml';
}
?>

<section class="section form-section">
    <h2 class="section-title">Top Up Game</h2>

    <!-- Form top up akan mengirim data ke proses.php melalui metode POST -->
    <form action="proses.php" method="POST" class="form-box" onsubmit="return validateForm()">
        <!-- Field tersembunyi untuk menyimpan game yang dipilih -->
        <input type="hidden" name="game" id="game" value="">
        <!-- Field tersembunyi untuk menyimpan nominal paket yang dipilih -->
        <input type="hidden" name="nominal" id="nominal" value="">

        <!-- Kontainer untuk input dinamis yang di-generate oleh topup.js -->
        <div id="dynamic-fields"></div>

        <label>Pilih Paket</label>
        <!-- Tombol paket nominal akan diisi oleh JavaScript -->
        <div class="nominal-options" id="nominal-options"></div>

        <label>Metode Pembayaran</label>
        <select name="metode" required>
            <option value="">Pilih Metode</option>
            <?php if (!empty($payments)): ?>
                <?php foreach ($payments as $payment): ?>
                    <option value="<?= htmlspecialchars($payment) ?>"><?= htmlspecialchars($payment) ?></option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="">Belum ada metode pembayaran tersedia</option>
            <?php endif; ?>
        </select>

        <label>Nomor Kontak</label>
        <!-- Input nomor kontak pengguna untuk konfirmasi dan pengiriman data top up -->
        <input type="tel" name="kontak" placeholder="08xxxxxx" required>

        <button type="submit" class="btn btn-primary">Pesan Sekarang</button>
    </form>
</section>

<!-- Memuat file JavaScript yang mengatur logika paket top up -->
<script src="topup.js"></script>

<?php include 'footer.php'; // Sertakan footer umum situs ?>
