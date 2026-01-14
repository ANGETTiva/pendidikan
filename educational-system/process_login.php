<?php
require_once 'config.php';
require_once 'functions.php';

// Cek method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setMessage('error', 'Akses tidak valid');
    redirect('login.php');
}

// Ambil data
$username = sanitize($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi
if (empty($username) || empty($password)) {
    setMessage('error', 'Username dan password harus diisi');
    redirect('login.php');
}

// Cari user di database
$user = getUserByUsername($username);

if (!$user) {
    setMessage('error', 'Username atau password salah');
    redirect('login.php');
}

// Verifikasi password
if (!password_verify($password, $user['password'])) {
    setMessage('error', 'Username atau password salah');
    redirect('login.php');
}

// Cek status aktif
if (!$user['is_active']) {
    setMessage('error', 'Akun tidak aktif');
    redirect('login.php');
}

// Login sukses
loginUser($user['id'], $user['username'], $user['user_role']);

// Update last login
$conn = getDB();
$stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$stmt->close();
$conn->close();

setMessage('success', 'Login berhasil! Selamat datang ' . $user['full_name']);
redirect('dashboard.php');
?>