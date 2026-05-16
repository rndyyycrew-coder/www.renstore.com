<?php
// Halaman manajemen produk untuk panel admin
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
    $productId = (int)($_POST['id'] ?? 0);
    $namaProduk = trim($_POST['nama_produk'] ?? '');
    $harga = (int)($_POST['harga'] ?? 0);
    $gameId = (int)($_POST['game_id'] ?? 0);

    if ($action === 'add') {
        if ($namaProduk === '' || $harga <= 0 || $gameId <= 0) {
            $message = 'Lengkapi nama game, nama produk, dan harga.';
        } else {
            $stmt = $conn->prepare('INSERT INTO products (game_id, nama_produk, harga) VALUES (?, ?, ?)');
            $stmt->bind_param('isi', $gameId, $namaProduk, $harga);
            $stmt->execute();
            $message = 'Produk berhasil ditambahkan.';
        }
    }

    if ($action === 'update' && $productId > 0) {
        if ($namaProduk === '' || $harga <= 0 || $gameId <= 0) {
            $message = 'Lengkapi nama game, nama produk, dan harga.';
        } else {
            $stmt = $conn->prepare('UPDATE products SET game_id = ?, nama_produk = ?, harga = ? WHERE id = ?');
            $stmt->bind_param('isii', $gameId, $namaProduk, $harga, $productId);
            $stmt->execute();
            $message = 'Produk berhasil diperbarui.';
        }
    }

    if ($action === 'delete' && $productId > 0) {
        $stmt = $conn->prepare('DELETE FROM products WHERE id = ?');
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $message = 'Produk berhasil dihapus.';
    }
}

if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    if ($editId > 0) {
        $stmt = $conn->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $editId);
        $stmt->execute();
        $result = $stmt->get_result();
        $editData = $result->fetch_assoc();
    }
}

// Ambil daftar game untuk dropdown dan daftar produk untuk tabel
$gamesResult = $conn->query('SELECT * FROM games ORDER BY nama_game ASC');
$gamesList = $gamesResult->fetch_all(MYSQLI_ASSOC);
$productsResult = $conn->query('SELECT p.*, g.nama_game FROM products p LEFT JOIN games g ON p.game_id = g.id ORDER BY p.id ASC');
$productsList = $productsResult->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Kelola Products - RENSTORE';
include 'admin_header.php';
?>

<section class="section">
    <?php if ($message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Form tambah atau edit produk -->
    <div class="admin-form">
        <h3><i class="fas fa-box"></i> <?= $editData ? 'Edit Product' : 'Tambah Product' ?></h3>
        <form method="POST">
            <input type="hidden" name="action" value="<?= $editData ? 'update' : 'add' ?>">
            <?php if ($editData): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Game</label>
                <select name="game_id" required>
                    <option value="">Pilih game</option>
                    <?php foreach ($gamesList as $game): ?>
                        <option value="<?= $game['id'] ?>" <?= $editData && $game['id'] == $editData['game_id'] ? 'selected' : '' ?>><?= htmlspecialchars($game['nama_game']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" value="<?= htmlspecialchars($editData['nama_produk'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" value="<?= htmlspecialchars($editData['harga'] ?? '') ?>" min="1" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $editData ? 'Simpan Perubahan' : 'Tambah Product' ?>
            </button>
            <?php if ($editData): ?>
                <a href="admin_products.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tabel daftar produk -->
    <div class="admin-table">
        <div class="admin-table-header">
            <h2><i class="fas fa-list"></i> Daftar Produk</h2>
        </div>
        <div class="admin-table-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Game</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productsList as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['id']) ?></td>
                            <td><?= htmlspecialchars($product['nama_game'] ?? 'Tidak ditentukan') ?></td>
                            <td><?= htmlspecialchars($product['nama_produk']) ?></td>
                            <td>Rp <?= number_format($product['harga'], 0, ',', '.') ?></td>
                            <td>
                                <a href="admin_products.php?edit=<?= urlencode($product['id']) ?>" class="btn btn-secondary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" style="display:inline-block; margin:0;" onsubmit="return confirm('Hapus produk ini?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
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