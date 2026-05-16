<?php
// Set judul halaman untuk ditampilkan pada tag <title>
$pageTitle = 'RENSTORE - Top Up Game';
// Sertakan file header umum yang berisi navigasi dan pembukaan <main>
include 'header.php';
?>

<!-- Banner promosi dengan penawaran khusus -->
<!-- PROMO SLIDER -->
<section class="promo-slider">

    <div class="slides">

        <div class="slide">
            <img src="assets/banner_ml.png" alt="Mobile Legends">
        </div>

        <div class="slide">
            <img src="assets/banner_ff.png" alt="Free Fire">
        </div>

        <div class="slide">
            <img src="assets/banner_pubg.png" alt="PUBG Mobile">
        </div>

    </div>

    <!-- Tombol -->
    <button class="prev">&#10094;</button>
    <button class="next">&#10095;</button>

</section>

<!-- Pilihan game yang tersedia untuk top up -->
<section class="section" id="pilih-game">
    <h2 class="section-title">Pilih Game</h2>
    <div class="container">
        <div class="card">
            <img src="assets/mobile legends.jpg" alt="Mobile Legends">
            <h3>Mobile Legends</h3>
            <p>Top up diamond resmi untuk akun Mobile Legends.</p>
            <a href="topup.php?game=ml" class="btn">Topup</a>
        </div>
        <div class="card">
            <img src="assets/free fire.jpg" alt="Free Fire">
            <h3>Free Fire</h3>
            <p>Top up diamond Free Fire dengan pilihan pembayaran mudah.</p>
            <a href="topup.php?game=ff" class="btn">Topup</a>
        </div>
        <div class="card">
            <img src="assets/pubg.jpg" alt="PUBG Mobile">
            <h3>PUBG Mobile</h3>
            <p>Top up UC PUBG Mobile dengan proses cepat.</p>
            <a href="topup.php?game=pubg" class="btn">Topup</a>
        </div>
    </div>
</section>

<!-- Keunggulan layanan RENSTORE -->
<section class="section features">
    <h2 class="section-title">Kenapa renstore?</h2>
    <div class="feature-grid">
        <div class="feature-item">
            <h4>Proses Cepat</h4>
            <p>Transaksi diproses segera setelah pembayaran dikonfirmasi.</p>
        </div>
        <div class="feature-item">
            <h4>Pembayaran Mudah</h4>
            <p>Terima DANA, OVO, dan GoPay tanpa ribet.</p>
        </div>
        <div class="feature-item">
            <h4>Jaminan Aman</h4>
            <p>Data transaksi tersimpan aman dan rahasia.</p>
        </div>
    </div>
</section>

<!-- Tentang RENSTORE dan komitmen layanan -->
<section id="tentang-kami" class="section about">
    <h2 class="section-title">Tentang Kami</h2>
    <div class="about-content">
        <p>RENSTORE adalah platform top up game yang menyediakan layanan pembelian diamond, UC, dan kebutuhan game lainnya secara cepat, aman, dan terpercaya. Kami hadir untuk memudahkan para gamers melakukan top up tanpa ribet dengan proses instan, harga terjangkau, serta berbagai metode pembayaran yang mudah digunakan. Saat ini RENSTORE melayani berbagai game populer seperti Mobile Legends, Free Fire, dan PUBG Mobile dengan mengutamakan kenyamanan pelanggan, keamanan transaksi, dan pelayanan terbaik agar pengalaman top up menjadi lebih mudah, nyaman, dan menyenangkan.</p>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="banner.js"></script>