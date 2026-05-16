<?php
// Halaman manajemen daftar game di panel admin
session_start();
include 'koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';
$editData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $gameName = trim($_POST['nama_game'] ?? '');
    $gameId = (int)($_POST['id'] ?? 0);

    if ($action === 'add' && $gameName !== '') {
        // Tambah game baru
        $stmt = $conn->prepare('INSERT INTO games (nama_game) VALUES (?)');
        $stmt->bind_param('s', $gameName);
        $stmt->execute();
        $message = 'Game berhasil ditambahkan.';
    }

    if ($action === 'update' && $gameId > 0 && $gameName !== '') {
        // Update nama game yang sudah ada
        $stmt = $conn->prepare('UPDATE games SET nama_game = ? WHERE id = ?');
        $stmt->bind_param('si', $gameName, $gameId);
        $stmt->execute();
        $message = 'Game berhasil diperbarui.';
    }

    if ($action === 'delete' && $gameId > 0) {
        // Hapus game dari database
        $stmt = $conn->prepare('DELETE FROM games WHERE id = ?');
        $stmt->bind_param('i', $gameId);
        $stmt->execute();
        $message = 'Game berhasil dihapus.';
    }
}

if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    if ($editId > 0) {
        $stmt = $conn->prepare('SELECT * FROM games WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $editId);
        $stmt->execute();
        $result = $stmt->get_result();
        $editData = $result->fetch_assoc();
    }
}

// Ambil daftar game untuk ditampilkan di tabel
$gamesResult = $conn->query('SELECT * FROM games ORDER BY id ASC');
$gamesList = $gamesResult->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Kelola Games - RENSTORE';
include 'admin_header.php';
?>

<section class="section">
    <?php if ($message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Form tambah atau edit game -->
    <div class="admin-form">
        <h3><i class="fas fa-gamepad"></i> <?= $editData ? 'Edit Game' : 'Tambah Game' ?></h3>
        <form method="POST">
            <input type="hidden" name="action" value="<?= $editData ? 'update' : 'add' ?>">
            <?php if ($editData): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Nama Game</label>
                <input type="text" name="nama_game" value="<?= htmlspecialchars($editData['nama_game'] ?? '') ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $editData ? 'Simpan Perubahan' : 'Tambah Game' ?>
            </button>
            <?php if ($editData): ?>
                <a href="admin_games.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tabel daftar game -->
    <div class="admin-table">
        <div class="admin-table-header">
            <h2><i class="fas fa-list"></i> Daftar Games</h2>
        </div>
        <div class="admin-table-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Game</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gamesList as $game): ?>
                        <tr>
                            <td><?= htmlspecialchars($game['id']) ?></td>
                            <td><?= htmlspecialchars($game['nama_game']) ?></td>
                            <td>
                                <a href="admin_games.php?edit=<?= urlencode($game['id']) ?>" class="btn btn-secondary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" style="display:inline-block; margin:0;" onsubmit="return confirm('Hapus game ini?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($game['id']) ?>">
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