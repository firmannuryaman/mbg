<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// header file
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=riwayat_gizi.csv");

// judul kolom
echo "NISN,Nama,Tanggal,Berat,Tinggi,BMI,Kategori\n";

// 🔥 QUERY WAJIB ADA
$data = mysqli_query($conn, "SELECT * FROM riwayat_gizi");

// cek kalau query gagal
if (!$data) {
    die("Query error: " . mysqli_error($conn));
}

// looping data
while ($row = mysqli_fetch_assoc($data)) {
    echo "{$row['nisn']},{$row['nama']},{$row['tanggal']},{$row['berat']},{$row['tinggi']},{$row['bmi']},{$row['kategori']}\n";
}
?>