<?php

// Koneksi ke database
require_once 'config.php';

// Ambil data dari formulir login
$username = $_POST['username'];
$password = $_POST['password'];

// Cari pengguna berdasarkan username
$query = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($query);

// hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);


if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verifikasi kata sandi (gunakan metode keamanan yang lebih baik dalam produksi)
    if (password_verify($password, $hashedPassword)) {
        // Login berhasil
        session_start();

        // Set data sesi
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect ke halaman yang sesuai dengan peran pengguna
        if ($user['role'] === 'admin') {
            header('Location: admin.php');
        } elseif ($user['role'] === 'teacher') {
            header('Location: teacher.php');
        } else {
            // Peran tidak dikenali, tangani sesuai kebutuhan
            echo "Peran tidak valid";
        }
    } else {
        // Kata sandi tidak cocok
        echo "Kata sandi tidak valid";
    }
} else {
    // Pengguna tidak ditemukan
    echo "Pengguna tidak ditemukan";
}

$conn->close();
?>
