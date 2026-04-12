<?php
require 'vendor/autoload.php';
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

use PhpOffice\PhpSpreadsheet\IOFactory;

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file']['tmp_name'];
    $fileName = $_FILES['excel_file']['name'];
    $fileType = $_FILES['excel_file']['type'];

    // Validasi file berdasarkan ekstensi
    $allowedExtensions = ['xlsx', 'xls'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedExtensions)) {
        $message = 'File harus berupa Excel (.xlsx atau .xls)';
    } elseif (!file_exists($file) || !is_readable($file)) {
        $message = 'File tidak dapat dibaca.';
    } else {
        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $successCount = 0;
            $errorCount = 0;
            $debugInfo = [];

            // Asumsikan baris 1 adalah header, mulai dari baris 2
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Skip baris kosong
                if (empty($row[0]) && empty($row[1]) && empty($row[2]) && empty($row[3]) && empty($row[4])) {
                    continue;
                }
                $nisn = trim($row[0] ?? '');
                $nama = trim($row[1] ?? '');
                $tanggal_raw = $row[2] ?? '';
                $berat = floatval($row[3] ?? 0);
                $tinggi = floatval($row[4] ?? 0);

                // Validasi NISN
                if (empty($nisn) || !is_numeric($nisn) || intval($nisn) <= 0) {
                    $errorCount++;
                    $debugInfo[] = "Baris " . ($i + 1) . ": NISN tidak valid";
                    continue;
                }
                $nisn = intval($nisn);

                // Validasi nama
                if (empty($nama)) {
                    $errorCount++;
                    $debugInfo[] = "Baris " . ($i + 1) . ": Nama kosong";
                    continue;
                }

                // Validasi berat dan tinggi
                if ($berat <= 0 || $tinggi <= 0) {
                    $errorCount++;
                    $debugInfo[] = "Baris " . ($i + 1) . ": Berat atau tinggi tidak valid";
                    continue;
                }

                // Parsing tanggal
                if (is_numeric($tanggal_raw)) {
                    // Jika tanggal berupa angka, anggap sebagai Excel date serial
                    $tanggal = DateTime::createFromFormat('U', (($tanggal_raw - 25569) * 86400))->format('Y-m-d');
                } else {
                    // Coba beberapa format tanggal
                    $tanggal = trim($tanggal_raw);
                    $date = DateTime::createFromFormat('d-m-Y', $tanggal);
                    if (!$date) {
                        $date = DateTime::createFromFormat('d/m/Y', $tanggal);
                    }
                    if (!$date) {
                        $date = DateTime::createFromFormat('Y-m-d', $tanggal);
                    }
                    if (!$date) {
                        $date = DateTime::createFromFormat('Y/m/d', $tanggal);
                    }
                    if (!$date) {
                        $date = DateTime::createFromFormat('d-m-y', $tanggal);
                    }
                    
                    if (!$date) {
                        $errorCount++;
                        $debugInfo[] = "Baris " . ($i + 1) . ": Format tanggal tidak dikenali: " . $tanggal;
                        continue;
                    }
                    
                    $tanggal = $date->format('Y-m-d');
                }
                
                // Validasi tanggal (pastikan valid)
                $validateDate = DateTime::createFromFormat('Y-m-d', $tanggal);
                if (!$validateDate) {
                    $errorCount++;
                    $debugInfo[] = "Baris " . ($i + 1) . ": Tanggal tidak valid";
                    continue;
                }

                // Hitung BMI
                $bmi = $berat / (($tinggi / 100) * ($tinggi / 100));
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
                $query = "INSERT INTO riwayat_gizi (nisn, nama, tanggal, berat, tinggi, bmi, kategori) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                
                if (!$stmt) {
                    $errorCount++;
                    $debugInfo[] = "Baris " . ($i + 1) . ": Gagal membuat statement";
                    continue;
                }

                mysqli_stmt_bind_param($stmt, 'issddds', $nisn, $nama, $tanggal, $berat, $tinggi, $bmi, $kategori);
                
                if (mysqli_stmt_execute($stmt)) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $debugInfo[] = "Baris " . ($i + 1) . ": Gagal menyimpan data - " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            }

            $message = "Import selesai. Berhasil: $successCount, Gagal: $errorCount";
            
            if (!empty($debugInfo)) {
                $message .= "<br><br><strong>Detail Kesalahan:</strong><br>" . implode("<br>", array_slice($debugInfo, 0, 10));
                if (count($debugInfo) > 10) {
                    $message .= "<br>... dan " . (count($debugInfo) - 10) . " lagi";
                }
            }
        } catch (Exception $e) {
            $message = 'Error membaca file: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Data dari Excel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Import Data dari Excel</h1>
    </header>

    <div class="container" style="max-width: 600px;">
        <div class="form-container">
            <?php if ($message): ?>
                <div class="toast" style="position: static; margin-bottom: 20px;">
                    <?php echo nl2br($message); ?>
                </div>
            <?php endif; ?>

            <h2>Pilih File Excel untuk Diimpor</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="excel_file">File Excel (.xlsx atau .xls)</label>
                    <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                </div>
                <button type="submit" class="btn-primary">Import Data</button>
            </form>

            <div style="margin-top: 20px; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #34495e; font-size: 13px;">
                <strong>Format File Excel:</strong>
                <ul style="margin-top: 10px; margin-left: 20px;">
                    <li>Kolom A: NISN</li>
                    <li>Kolom B: Nama</li>
                    <li>Kolom C: Tanggal Lahir (dd-mm-yyyy, dd/mm/yyyy, atau yyyy-mm-dd)</li>
                    <li>Kolom D: Berat (kg)</li>
                    <li>Kolom E: Tinggi (cm)</li>
                    <li>Baris pertama = Header (NISN, Nama, Tanggal, Berat, Tinggi)</li>
                </ul>
            </div>

            <a href="index.php" style="display: inline-block; margin-top: 20px; color: #34495e; text-decoration: none; font-weight: 500;">← Kembali ke Halaman Utama</a>
        </div>
    </div>
</body>
</html>
