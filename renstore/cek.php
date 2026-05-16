<?php
// Judul halaman untuk melakukan pengecekan transaksi berdasarkan kode order
$pageTitle = 'Cek Transaksi - RENSTORE';
include 'header.php';
?>

<section class="section form-section">
    <h2 class="section-title">Cek Transaksi</h2>
    <!-- Form untuk memasukkan kode order yang diterima setelah checkout -->
    <form action="hasil.php" method="POST" class="form-box transaction-check-box">
        <label>Kode Order</label>
        <input type="text" name="kode" placeholder="ORD-20250510..." required>
        <button type="submit" class="btn btn-primary">Cek</button>
    </form>
</section>

<?php include 'footer.php'; ?>
