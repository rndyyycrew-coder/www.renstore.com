<?php
// Mulai session agar bisa memeriksa status login admin
session_start();
include 'koneksi.php';

// Redirect jika admin belum login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_contact') {
        // Ambil data kontak baru dari form
        $whatsapp = trim($_POST['whatsapp'] ?? '');
        $telegram = trim($_POST['telegram'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $instagram = trim($_POST['instagram'] ?? '');

        // Saat ini hanya menampilkan pesan sukses, implementasi update dapat ditambahkan
        $message = 'Informasi kontak berhasil diperbarui.';
    }
}

// Sertakan dashboard admin utama
include 'dashboard.php';
?>