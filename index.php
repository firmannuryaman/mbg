<?php include 'koneksi.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Riwayat Gizi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php if (isset($_GET['status'])): ?>
    <div class="toast">
        Data berhasil <?= htmlspecialchars($_GET['status']); ?>!
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.toast').style.display = 'none';
        }, 3000);
    </script>
    <?php endif; ?>

    <header>
        <h1>Sistem Riwayat Gizi</h1>
    </header>

    <div class="container">
        <!-- FORM INPUT -->
        <div class="form-container">
            <h2>Tambah Data Baru</h2>
            <form action="tambah.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nisn">NISN</label>
                        <input type="number" id="nisn" name="nisn" required>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" id="nama" name="nama" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tanggal">Tanggal Lahir</label>
                        <input type="date" id="tanggal" name="tanggal" required>
                    </div>
                    <div class="form-group">
                        <label for="berat">Berat (kg)</label>
                        <input type="number" id="berat" name="berat" step="0.1" required>
                    </div>
                </div>
                <div class="form-row full">
                    <div class="form-group">
                        <label for="tinggi">Tinggi (cm)</label>
                        <input type="number" id="tinggi" name="tinggi" step="0.1" required>
                    </div>
                </div>
                <button type="submit" class="btn-primary">Simpan Data</button>
            </form>
        </div>

        <!-- DATA TABLE -->
        <div class="table-container">
            <h2>Data Riwayat Gizi</h2>
            
            <!-- Buttons -->
            <div class="btn-group">
                <a href="export.php" class="btn btn-success">Ekspor ke Excel</a>
                <a href="import.php" class="btn btn-info">Impor dari Excel</a>
            </div>

            <!-- Filter Section -->
            <form method="GET" class="filter-section">
                <input type="text" name="cari" value="<?= htmlspecialchars($_GET['cari'] ?? '') ?>" placeholder="Cari nama...">
                
                <select name="kategori">
                    <option value="">Semua Kategori</option>
                    <option value="Stunting" <?= ($_GET['kategori'] ?? '') === 'Stunting' ? 'selected' : '' ?>>Stunting</option>
                    <option value="Gizi Baik" <?= ($_GET['kategori'] ?? '') === 'Gizi Baik' ? 'selected' : '' ?>>Gizi Baik</option>
                    <option value="Obesitas" <?= ($_GET['kategori'] ?? '') === 'Obesitas' ? 'selected' : '' ?>>Obesitas</option>
                </select>

                <select name="mode">
                    <option value="FIFO" <?= ($_GET['mode'] ?? 'FIFO') === 'FIFO' ? 'selected' : '' ?>>FIFO</option>
                    <option value="LIFO" <?= ($_GET['mode'] ?? 'FIFO') === 'LIFO' ? 'selected' : '' ?>>LIFO</option>
                </select>

                <button type="submit" class="btn btn-primary">Cari</button>
            </form>

            <!-- Data Table -->
            <table>
                <thead>
                    <tr>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Umur</th>
                        <th>Berat (kg)</th>
                        <th>Tinggi (cm)</th>
                        <th>BMI</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cari = $_GET['cari'] ?? '';
                    $kategori = $_GET['kategori'] ?? '';
                    $mode = $_GET['mode'] ?? 'FIFO';

                    $query = "SELECT * FROM riwayat_gizi WHERE 1=1";

                    if (!empty($cari)) {
                        $cari = mysqli_real_escape_string($conn, $cari);
                        $query .= " AND nama LIKE '%$cari%'";
                    }

                    if (!empty($kategori)) {
                        $kategori = mysqli_real_escape_string($conn, $kategori);
                        $query .= " AND kategori = '$kategori'";
                    }

                    if ($mode == "LIFO") {
                        $query .= " ORDER BY id DESC";
                    } else {
                        $query .= " ORDER BY id ASC";
                    }

                    $result = mysqli_query($conn, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $badgeClass = 'badge-primary';
                        if ($row['kategori'] == "Stunting") $badgeClass = 'badge-primary';
                        elseif ($row['kategori'] == "Gizi Baik") $badgeClass = 'badge-success';
                        elseif ($row['kategori'] == "Obesitas") $badgeClass = 'badge-danger';

                        // Hitung umur
                        $birthDate = new DateTime($row['tanggal']);
                        $today = new DateTime();
                        $age = $today->diff($birthDate)->y;

                        echo "<tr>
                            <td>{$row['nisn']}</td>
                            <td>{$row['nama']}</td>
                            <td>{$age} tahun</td>
                            <td>{$row['berat']}</td>
                            <td>{$row['tinggi']}</td>
                            <td><strong>{$row['bmi']}</strong></td>
                            <td><span class='badge {$badgeClass}'>{$row['kategori']}</span></td>
                            <td>
                                <div class='action-buttons'>
                                    <a href='edit.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='hapus.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">Hapus</a>
                                </div>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>