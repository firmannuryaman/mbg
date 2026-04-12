<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn = $_POST['nisn'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $berat = $_POST['berat'] ?? '';
    $tinggi = $_POST['tinggi'] ?? '';

    $bmi = 0;
    if ($tinggi > 0) {
        $bmi = $berat / (($tinggi / 100) * ($tinggi / 100));
        $bmi = round($bmi, 2);
    }

    if ($bmi < 18.5) {
        $kategori = "Stunting";
    } elseif ($bmi < 25) {
        $kategori = "Gizi Baik";
    } else {
        $kategori = "Obesitas";
    }

    $nisn = mysqli_real_escape_string($conn, $nisn);
    $nama = mysqli_real_escape_string($conn, $nama);
    $tanggal = mysqli_real_escape_string($conn, $tanggal);
    $berat = mysqli_real_escape_string($conn, $berat);
    $tinggi = mysqli_real_escape_string($conn, $tinggi);
    $kategori = mysqli_real_escape_string($conn, $kategori);

    $query = "INSERT INTO riwayat_gizi (nisn, nama, tanggal, berat, tinggi, bmi, kategori) VALUES ('$nisn', '$nama', '$tanggal', '$berat', '$tinggi', '$bmi', '$kategori')";
    mysqli_query($conn, $query);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Riwayat Gizi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Tambah Data Baru</h1>
    </header>

    <div class="container">
        <div class="form-container">
            <h2>Form Tambah Data</h2>
            <form action="tambah.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nisn">NISN</label>
                        <input type="number" id="nisn" name="nisn" value="<?= htmlspecialchars($_POST['nisn'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tanggal">Tanggal Lahir</label>
                        <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($_POST['tanggal'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="berat">Berat (kg)</label>
                        <input type="number" id="berat" name="berat" step="0.1" value="<?= htmlspecialchars($_POST['berat'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="form-row full">
                    <div class="form-group">
                        <label for="tinggi">Tinggi (cm)</label>
                        <input type="number" id="tinggi" name="tinggi" step="0.1" value="<?= htmlspecialchars($_POST['tinggi'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                    <a href="index.php" class="btn btn-info">Kembali ke Riwayat</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>