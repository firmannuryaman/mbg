<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM riwayat_gizi WHERE id=$id");
$row = mysqli_fetch_assoc($data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Edit Data</h1>
    </header>

    <div class="container" style="max-width: 600px;">
        <div class="form-container">
            <form action="update.php" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']); ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nisn">NISN</label>
                        <input type="number" id="nisn" name="nisn" value="<?= htmlspecialchars($row['nisn']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($row['nama']); ?>" required>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label for="tanggal">Tanggal Lahir</label>
                        <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($row['tanggal']); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="berat">Berat (kg)</label>
                        <input type="number" id="berat" name="berat" step="0.1" value="<?= htmlspecialchars($row['berat']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tinggi">Tinggi (cm)</label>
                        <input type="number" id="tinggi" name="tinggi" step="0.1" value="<?= htmlspecialchars($row['tinggi']); ?>" required>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-success" style="flex: 1;">Simpan Perubahan</button>
                    <a href="index.php" class="btn btn-info" style="flex: 1; text-align: center;">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>