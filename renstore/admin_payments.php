<?php
// Halaman manajemen metode pembayaran untuk admin
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';
$editData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $paymentId = (int)($_POST['id'] ?? 0);
    $namaMetode = trim($_POST['nama_metode'] ?? '');

    if ($action === 'add') {
        if ($namaMetode === '') {
            $message = 'Nama metode pembayaran tidak boleh kosong.';
        } else {
            $stmt = $conn->prepare('INSERT INTO payment_methods (nama_metode) VALUES (?)');
            $stmt->bind_param('s', $namaMetode);
            $stmt->execute();
            $message = 'Metode pembayaran berhasil ditambahkan.';
        }
    }

    if ($action === 'update' && $paymentId > 0) {
        if ($namaMetode === '') {
            $message = 'Nama metode pembayaran tidak boleh kosong.';
        } else {
            $stmt = $conn->prepare('UPDATE payment_methods SET nama_metode = ? WHERE id = ?');
            $stmt->bind_param('si', $namaMetode, $paymentId);
            $stmt->execute();
            $message = 'Metode pembayaran berhasil diperbarui.';
        }
    }

    if ($action === 'delete' && $paymentId > 0) {
        $stmt = $conn->prepare('DELETE FROM payment_methods WHERE id = ?');
        $stmt->bind_param('i', $paymentId);
        $stmt->execute();
        $message = 'Metode pembayaran berhasil dihapus.';
    }
}

if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    if ($editId > 0) {
        $stmt = $conn->prepare('SELECT * FROM payment_methods WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $editId);
        $stmt->execute();
        $result = $stmt->get_result();
        $editData = $result->fetch_assoc();
    }
}

$paymentsResult = $conn->query('SELECT * FROM payment_methods ORDER BY id ASC');
$paymentsList = $paymentsResult->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Kelola Payments - RENSTORE';
include 'admin_header.php';
?>

<section class="section">
    <?php if ($message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Form tambah atau edit metode pembayaran -->
    <div class="admin-form">
        <h3><i class="fas fa-credit-card"></i> <?= $editData ? 'Edit Metode Pembayaran' : 'Tambah Metode Pembayaran' ?></h3>
        <form method="POST">
            <input type="hidden" name="action" value="<?= $editData ? 'update' : 'add' ?>">
            <?php if ($editData): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Nama Metode</label>
                <input type="text" name="nama_metode" value="<?= htmlspecialchars($editData['nama_metode'] ?? '') ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $editData ? 'Simpan Perubahan' : 'Tambah Metode' ?>
            </button>
            <?php if ($editData): ?>
                <a href="admin_payments.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tabel daftar metode pembayaran -->
    <div class="admin-table">
        <div class="admin-table-header">
            <h2><i class="fas fa-list"></i> Daftar Metode Pembayaran</h2>
        </div>
        <div class="admin-table-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Metode</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paymentsList as $payment): ?>
                        <tr>
                            <td><?= htmlspecialchars($payment['id']) ?></td>
                            <td><?= htmlspecialchars($payment['nama_metode']) ?></td>
                            <td>
                                <a href="admin_payments.php?edit=<?= urlencode($payment['id']) ?>" class="btn btn-secondary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" style="display:inline-block; margin:0;" onsubmit="return confirm('Hapus metode pembayaran ini?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($payment['id']) ?>">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include 'admin_footer.php'; ?>