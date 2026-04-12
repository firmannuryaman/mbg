<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_POST['id'];
$nama = $_POST['nama'];
$tanggal = $_POST['tanggal'];
$berat = $_POST['berat'];
$tinggi = $_POST['tinggi'];

// Hitung ulang BMI
$bmi = $berat / (($tinggi/100) * ($tinggi/100));
$bmi = round($bmi, 2);

// Kategori
if ($bmi < 18.5) {
    $kategori = "Kurus";
} elseif ($bmi < 25) {
    $kategori = "Normal";
} elseif ($bmi < 30) {
    $kategori = "Gemuk";
} else {
    $kategori = "Obesitas";
}

// Update data
$query = "UPDATE riwayat_gizi SET
    nama='$nama',
    tanggal='$tanggal',
    berat='$berat',
    tinggi='$tinggi',
    bmi='$bmi',
    kategori='$kategori'
    WHERE id=$id";

mysqli_query($conn, $query);

header("Location: index.php");
?>