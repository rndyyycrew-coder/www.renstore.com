<?php
// Mulai session untuk login admin
session_start();
include 'koneksi.php';

// Judul halaman login admin
$pageTitle = 'Login Admin - RENSTORE';
include 'header.php';

$error = '';
if (isset($_POST['login'])) {
    // Ambil input dari form
    $user = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi input dasar
    if ($user === '' || $password === '') {
        $error = 'Silakan isi username dan password.';
    } else {
        // Ambil hash password dari database berdasarkan username
        $stmt = $conn->prepare('SELECT password FROM admins WHERE username = ?');
        $stmt->bind_param('s', $user);
        $stmt->execute();
        $stmt->bind_result($hash);

        if ($stmt->fetch()) {
            $valid = false;
            // Cek beberapa format password yang mungkin tersimpan di database
            if ($password === $hash) {
                $valid = true;
            } elseif (password_verify($password, $hash)) {
                $valid = true;
            } elseif (md5($password) === $hash) {
                $valid = true;
            }

            // Jika password valid, set session dan arahkan ke panel admin
            if ($valid) {
                $_SESSION['login'] = true;
                header('Location: admin.php');
                exit;
            }
        }

        $error = 'Username atau password tidak cocok.';
    }
}
?>

<section class="section form-section">
    <h2 class="section-title">Login Admin</h2>
    <?php if ($error): ?>
        <!-- Tampilkan pesan error jika login gagal -->
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" class="form-box admin-login-box">
        <label>Username</label>
        <input type="text" name="username" placeholder="Masukkan Username" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="Masukkan Password">
        <button type="submit" name="login" class="btn btn-primary">Masuk</button>
    </form>
</section>

<?php include 'footer.php'; ?>
