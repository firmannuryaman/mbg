<?php
include 'koneksi.php';

// header file
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=riwayat_gizi.csv");

// judul kolom
echo "Nama,Tanggal,Berat,Tinggi,BMI,Kategori\n";

// 🔥 QUERY WAJIB ADA
$data = mysqli_query($conn, "SELECT * FROM riwayat_gizi");

// cek kalau query gagal
if (!$data) {
    die("Query error: " . mysqli_error($conn));
}

// looping data
while ($row = mysqli_fetch_assoc($data)) {
    echo "{$row['nama']},{$row['tanggal']},{$row['berat']},{$row['tinggi']},{$row['bmi']},{$row['kategori']}\n";
}
?>