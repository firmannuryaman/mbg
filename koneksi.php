<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "gizi_db");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Buat tabel users jika belum ada
$create_users_table = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $create_users_table);

// Cek dan insert default admin jika belum ada
$check_admin = mysqli_query($conn, "SELECT * FROM users WHERE username='admin'");
if (mysqli_num_rows($check_admin) == 0) {
    $default_password = password_hash('admin123', PASSWORD_BCRYPT);
    mysqli_query($conn, "INSERT INTO users (username, password) VALUES ('admin', '$default_password')");
}
?>