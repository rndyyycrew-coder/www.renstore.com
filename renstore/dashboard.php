<?php
// Halaman dashboard admin utama
session_start();
include 'koneksi.php';

// Jika belum login sebagai admin, arahkan ke halaman login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';
$editData = null;

// Ambil data master untuk ditampilkan di form edit order
$games = $conn->query('SELECT * FROM games');
$payments_methods = $conn->query('SELECT * FROM payment_methods');
$products = $conn->query('SELECT * FROM products');
$gamesArray = $games->fetch_all(MYSQLI_ASSOC);
$payments_methodsArray = $payments_methods->fetch_all(MYSQLI_ASSOC);
$productsArray = $products->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $kode_order = trim($_POST['kode_order'] ?? '');
        if ($kode_order !== '') {
            $stmt = $conn->prepare('DELETE FROM orders WHERE kode_order = ?');
            $stmt->bind_param('s', $kode_order);
            $stmt->execute();
            $message = 'Order berhasil dihapus.';
        }
    }

    if ($action === 'update') {
        $game_id = (int)($_POST['game_id'] ?? 0);
        $user_id_game = trim($_POST['user_id_game'] ?? '');
        $server_id = trim($_POST['server_id'] ?? '');
        $product_id = (int)($_POST['product_id'] ?? 0);
        $total_harga = (int)($_POST['total_harga'] ?? 0);
        $payment_method_id = (int)($_POST['payment_method_id'] ?? 0);
        $nomor_kontak = trim($_POST['nomor_kontak'] ?? '');
        $status = trim($_POST['status'] ?? 'pending');

        $original_kode_order = trim($_POST['original_kode_order'] ?? '');
        $stmt = $conn->prepare('UPDATE orders SET game_id = ?, user_id_game = ?, server_id = ?, product_id = ?, total_harga = ?, payment_method_id = ?, nomor_kontak = ?, status = ? WHERE kode_order = ?');
        $stmt->bind_param('issiiisss', $game_id, $user_id_game, $server_id, $product_id, $total_harga, $payment_method_id, $nomor_kontak, $status, $original_kode_order);
        $stmt->execute();
        $message = 'Data order berhasil diperbarui.';
    }
}

if (isset($_GET['edit'])) {
    $editId = trim($_GET['edit']);
    $stmt = $conn->prepare('SELECT * FROM orders WHERE kode_order = ? LIMIT 1');
    $stmt->bind_param('s', $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    $editData = $result->fetch_assoc();
}

$pageTitle = 'Dashboard Admin - RENSTORE';
include 'admin_header.php';

$filter_date = trim($_GET['filter_date'] ?? '');
$filter_month = trim($_GET['filter_month'] ?? '');
$filter_year = trim($_GET['filter_year'] ?? '');

$whereClauses = [];
$params = [];
$paramTypes = '';

if ($filter_date !== '') {
    $whereClauses[] = 'DATE(o.created_at) = ?';
    $params[] = $filter_date;
    $paramTypes .= 's';
} else {
    if ($filter_month !== '' && ctype_digit($filter_month) && (int)$filter_month >= 1 && (int)$filter_month <= 12) {
        $whereClauses[] = 'MONTH(o.created_at) = ?';
        $params[] = (int)$filter_month;
        $paramTypes .= 'i';
    }

    if ($filter_year !== '' && ctype_digit($filter_year)) {
        $whereClauses[] = 'YEAR(o.created_at) = ?';
        $params[] = (int)$filter_year;
        $paramTypes .= 'i';
    }
}

$sql = 'SELECT o.*, g.nama_game, pr.nama_produk, pm.nama_metode FROM orders o JOIN games g ON o.game_id = g.id JOIN products pr ON o.product_id = pr.id JOIN payment_methods pm ON o.payment_method_id = pm.id';
if (!empty($whereClauses)) {
    $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
}
$sql .= ' ORDER BY o.kode_order DESC';

if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $bindNames = array_merge([$paramTypes], $params);
    $bindParams = [];
    foreach ($bindNames as $key => $value) {
        $bindParams[$key] = &$bindNames[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $bindParams);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

$years = $conn->query('SELECT DISTINCT YEAR(created_at) AS year FROM orders ORDER BY year DESC')->fetch_all(MYSQLI_ASSOC);
$monthNames = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];
?>

<section class="section">
    <div class="admin-stats">
        <div class="admin-card">
            <h3><i class="fas fa-shopping-cart"></i> Total Orders</h3>
            <div class="stat-number">
                <?php
                $totalOrders = $conn->query('SELECT COUNT(*) as total FROM orders')->fetch_assoc()['total'];
                echo $totalOrders;
                ?>
            </div>
            <div class="stat-label">Pesanan hari ini</div>
        </div>
        <div class="admin-card">
            <h3><i class="fas fa-clock"></i> Pending</h3>
            <div class="stat-number">
                <?php
                $pendingOrders = $conn->query('SELECT COUNT(*) as total FROM orders WHERE status = "pending"')->fetch_assoc()['total'];
                echo $pendingOrders;
                ?>
            </div>
            <div class="stat-label">Menunggu pembayaran</div>
        </div>
        <div class="admin-card">
            <h3><i class="fas fa-check-circle"></i> Completed</h3>
            <div class="stat-number">
                <?php
                $completedOrders = $conn->query('SELECT COUNT(*) as total FROM orders WHERE status = "selesai"')->fetch_assoc()['total'];
                echo $completedOrders;
                ?>
            </div>
            <div class="stat-label">Selesai diproses</div>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($editData): ?>
        <div class="admin-form">
            <h3><i class="fas fa-edit"></i> Edit Order</h3>
            <form method="POST" class="form-box">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="original_kode_order" value="<?= htmlspecialchars($editData['kode_order']) ?>">

                <div class="form-group">
                    <label>Game</label>
                    <select name="game_id" required>
                        <?php foreach ($gamesArray as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= $g['id'] == $editData['game_id'] ? 'selected' : '' ?>><?= htmlspecialchars($g['nama_game']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Product</label>
                    <select name="product_id" required>
                        <?php foreach ($productsArray as $pr): ?>
                            <option value="<?= $pr['id'] ?>" <?= $pr['id'] == $editData['product_id'] ? 'selected' : '' ?>><?= htmlspecialchars($pr['nama_produk']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>User ID Game</label>
                    <input type="text" name="user_id_game" value="<?= htmlspecialchars($editData['user_id_game']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Server ID</label>
                    <input type="text" name="server_id" value="<?= htmlspecialchars($editData['server_id']) ?>">
                </div>

                <div class="form-group">
                    <label>Total Harga</label>
                    <input type="number" name="total_harga" value="<?= htmlspecialchars($editData['total_harga']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <select name="payment_method_id" required>
                        <?php foreach ($payments_methodsArray as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $p['id'] == $editData['payment_method_id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nama_metode']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>KONTAK</label>
                    <input type="text" name="nomor_kontak" value="<?= htmlspecialchars($editData['nomor_kontak']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required>
                        <option value="dibayar" <?= $editData['status'] == 'dibayar' ? 'selected' : '' ?>>Dibayar</option>
                        <option value="selesai" <?= $editData['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        <option value="batal" <?= $editData['status'] == 'batal' ? 'selected' : '' ?>>Batal</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </form>
        </div>
    <?php endif; ?>

    <div class="admin-table">
        <div class="admin-table-header">
            <div class="admin-table-header-top">
                <h2><i class="fas fa-list"></i> Daftar Orders</h2>
                <form method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="filter_date">Tanggal</label>
                        <input type="date" id="filter_date" name="filter_date" value="<?= htmlspecialchars($filter_date) ?>">
                    </div>
                    <div class="form-group">
                        <label for="filter_month">Bulan</label>
                        <select id="filter_month" name="filter_month">
                            <option value="">Semua Bulan</option>
                            <?php foreach ($monthNames as $m => $monthLabel): ?>
                                <option value="<?= $m ?>" <?= $filter_month == $m ? 'selected' : '' ?>><?= htmlspecialchars($monthLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter_year">Tahun</label>
                        <select id="filter_year" name="filter_year">
                            <option value="">Semua Tahun</option>
                            <?php foreach ($years as $yearRow): ?>
                                <option value="<?= htmlspecialchars($yearRow['year']) ?>" <?= $filter_year == $yearRow['year'] ? 'selected' : '' ?>><?= htmlspecialchars($yearRow['year']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                    <a href="dashboard.php" class="btn btn-secondary">Reset</a>
                </form>
            </div>
        </div>
        <div class="admin-table-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode Order</th>
                        <th>Game</th>
                        <th>Product</th>
                        <th>User ID Game</th>
                        <th>Server ID</th>
                        <th>Total Harga</th>
                        <th>Tanggal</th>
                        <th>Metode</th>
                        <th>KONTAK</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($d = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['kode_order']) ?></td>
                            <td><?= htmlspecialchars($d['nama_game']) ?></td>
                            <td><?= htmlspecialchars($d['nama_produk']) ?></td>
                            <td><?= htmlspecialchars($d['user_id_game']) ?></td>
                            <td><?= htmlspecialchars($d['server_id']) ?></td>
                            <td>Rp <?= number_format($d['total_harga'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($d['created_at']))) ?></td>
                            <td><?= htmlspecialchars($d['nama_metode']) ?></td>
                            <td><?= htmlspecialchars($d['nomor_kontak']) ?></td>
                            <td>
                                <span class="status-badge status-<?= $d['status'] ?>">
                                    <?= htmlspecialchars($d['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="dashboard.php?edit=<?= urlencode($d['kode_order']) ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" style="display:inline-block; margin:0;" onsubmit="return confirmDelete();">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="kode_order" value="<?= htmlspecialchars($d['kode_order']) ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include 'admin_footer.php'; ?>