<?php 
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

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

    <div class="layout">
        <aside class="sidebar">
            <h2>Monitoring Gizi</h2>
            <ul>
                <li class="active"><a href="index.php">Riwayat Gizi</a></li>
                <li><a href="users.php">User</a></li>
            </ul>
            <div class="sidebar-logout">
                <a href="logout.php" class="btn btn-danger btn-logout">🚪 Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="header">
                <div>
                    <h1>Riwayat Gizi</h1>
                    <small style="color: #999;">User: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></small>
                </div>
                <div class="header-actions">
                    <a href="import.php" class="btn btn-secondary btn-sm">📥 Impor Excel</a>
                    <a href="tambah.php" class="btn btn-primary btn-sm">+ Tambah Data</a>
                </div>
            </div>
            <?php
            // Hitung jumlah kategori
            $qGiziBaik = mysqli_query($conn, "SELECT COUNT(*) as total FROM riwayat_gizi WHERE kategori='Gizi Baik'");
            $giziBaik = mysqli_fetch_assoc($qGiziBaik)['total'];

            $qStunting = mysqli_query($conn, "SELECT COUNT(*) as total FROM riwayat_gizi WHERE kategori='Stunting'");
            $stunting = mysqli_fetch_assoc($qStunting)['total'];

            $qObesitas = mysqli_query($conn, "SELECT COUNT(*) as total FROM riwayat_gizi WHERE kategori='Obesitas'");
            $obesitas = mysqli_fetch_assoc($qObesitas)['total'];
            ?>

            <div class="stats">
                <div class="stat-card">
                    <p>Gizi Baik</p>
                    <h2><?= $giziBaik ?></h2>
                </div>

                <div class="stat-card">
                    <p>Stunting</p>
                    <h2><?= $stunting ?></h2>
                </div>

                <div class="stat-card">
                    <p>Obesitas</p>
                    <h2><?= $obesitas ?></h2>
                </div>
            </div>

            <div class="card">
                <div class="toolbar">
                    <a href="export.php" class="btn btn-success btn-sm">Ekspor ke Excel</a>
                    <form method="GET" class="filter-section">
                        <input type="text" name="cari" placeholder="Search..." value="<?= htmlspecialchars($_GET['cari'] ?? '') ?>">

                        <select name="kategori">
                            <option value="" <?= ($_GET['kategori'] ?? '') === '' ? 'selected' : '' ?>>Semua</option>
                            <option value="Stunting" <?= ($_GET['kategori'] ?? '') === 'Stunting' ? 'selected' : '' ?>>Stunting</option>
                            <option value="Gizi Baik" <?= ($_GET['kategori'] ?? '') === 'Gizi Baik' ? 'selected' : '' ?>>Gizi Baik</option>
                            <option value="Obesitas" <?= ($_GET['kategori'] ?? '') === 'Obesitas' ? 'selected' : '' ?>>Obesitas</option>
                        </select>

                        <select name="mode">
                            <option value="FIFO" <?= ($_GET['mode'] ?? 'FIFO') === 'FIFO' ? 'selected' : '' ?>>FIFO</option>
                            <option value="LIFO" <?= ($_GET['mode'] ?? 'FIFO') === 'LIFO' ? 'selected' : '' ?>>LIFO</option>
                        </select>

                        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                    </form>
                </div>

                <table class="modern-table">
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
                            <td>{$row['berat']} kg</td>
                            <td>{$row['tinggi']} cm</td>
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