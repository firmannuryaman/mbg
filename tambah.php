<?php
include 'koneksi.php';

$nisn = $_POST['nisn'];
$nama = $_POST['nama'];
$tanggal = $_POST['tanggal'];
$berat = $_POST['berat'];
$tinggi = $_POST['tinggi'];

// Hitung BMI
$bmi = $berat / (($tinggi/100) * ($tinggi/100));
$bmi = round($bmi, 2);

// Kategori BMI
if ($bmi < 18.5) {
    $kategori = "Stunting";
} elseif ($bmi < 25) {
    $kategori = "Gizi Baik";
} else {
    $kategori = "Obesitas";
}

// Simpan ke database
$query = "INSERT INTO riwayat_gizi 
(nisn, nama, tanggal, berat, tinggi, bmi, kategori)
VALUES ('$nisn', '$nama', '$tanggal', '$berat', '$tinggi', '$bmi', '$kategori')";

mysqli_query($conn, $query);

header("Location: index.php");
?>