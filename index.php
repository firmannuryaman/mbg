<?php include 'koneksi.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Gizi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<?php if (isset($_GET['status'])): ?>
<div id="toast" class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow">
    Data berhasil <?= $_GET['status']; ?>!
</div>

<script>
setTimeout(() => {
    document.getElementById('toast').style.display = 'none';
}, 3000);
</script>
<?php endif; ?>

<body class="bg-gradient-to-br from-green-100 to-blue-100 min-h-screen p-6">

    <div class="max-w-5xl mx-auto">

        <!-- HEADER -->
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">
            📊 Sistem Riwayat Gizi
        </h1>

        <!-- FORM -->
        <div class="bg-white shadow-xl rounded-2xl p-6 mb-8 transition hover:scale-105">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Tambah Data</h2>

            <form action="tambah.php" method="POST" class="grid grid-cols-2 gap-4">
                <input type="text" name="nama" placeholder="Nama"
                    class="p-3 border rounded-xl focus:ring-2 focus:ring-green-400" required>

                <input type="date" name="tanggal" class="p-3 border rounded-xl focus:ring-2 focus:ring-green-400"
                    required>

                <input type="number" step="0.1" name="berat" placeholder="Berat (kg)"
                    class="p-3 border rounded-xl focus:ring-2 focus:ring-green-400" required>

                <input type="number" step="0.1" name="tinggi" placeholder="Tinggi (cm)"
                    class="p-3 border rounded-xl focus:ring-2 focus:ring-green-400" required>

                <button type="submit"
                    class="col-span-2 bg-green-500 hover:bg-green-600 text-white p-3 rounded-xl font-semibold transition">
                    Simpan Data
                </button>
            </form>
        </div>

        <!-- TABLE -->
        <div class="bg-white shadow-xl rounded-2xl p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">📋 Riwayat Data</h2>
            <!--  export excel -->
            <a href="export.php" class="bg-green-600 text-white px-4 py-2 rounded mb-4 inline-block">
                Export Excel
            </a>

            <!-- FIFO & LIFO & search -->
            <form method="GET" class="mb-4 flex gap-2">

                <input type="text" name="cari" value="<?= $_GET['cari'] ?? '' ?>" placeholder="Cari nama..."
                    class="p-2 border rounded">

                <select name="kategori" class="p-2 border rounded">
                    <option value="">Semua</option>
                    <option value="Kurus">Kurus</option>
                    <option value="Normal">Normal</option>
                    <option value="Gemuk">Gemuk</option>
                    <option value="Obesitas">Obesitas</option>
                </select>

                <!-- MODE -->
                <select name="mode" class="p-2 border rounded">
                    <option value="FIFO">FIFO</option>
                    <option value="LIFO">LIFO</option>
                </select>

                <button class="bg-blue-500 text-white px-4 rounded">Filter</button>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-center border-collapse">
                    <thead>
                        <tr class="bg-green-500 text-white">
                            <th class="p-3">Nama</th>
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Berat</th>
                            <th class="p-3">Tinggi</th>
                            <th class="p-3">BMI</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
$cari = $_GET['cari'] ?? '';
$kategori = $_GET['kategori'] ?? '';
$mode = $_GET['mode'] ?? 'FIFO';

// base query
$query = "SELECT * FROM riwayat_gizi WHERE 1=1";

// filter nama
if (!empty($cari)) {
    $query .= " AND nama LIKE '%$cari%'";
}

// filter kategori
if (!empty($kategori)) {
    $query .= " AND kategori = '$kategori'";
}

// 🔥 FIFO vs LIFO
if ($mode == "LIFO") {
    $query .= " ORDER BY id DESC";
} else {
    $query .= " ORDER BY id ASC";
}

// eksekusi
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {

    $warna = "bg-gray-200";
    if ($row['kategori'] == "Kurus") $warna = "bg-blue-300";
    elseif ($row['kategori'] == "Normal") $warna = "bg-green-300";
    elseif ($row['kategori'] == "Gemuk") $warna = "bg-yellow-300";
    elseif ($row['kategori'] == "Obesitas") $warna = "bg-red-300";

    echo "<tr class='hover:bg-gray-100 transition'>
        <td class='p-3'>{$row['nama']}</td>
        <td class='p-3'>{$row['tanggal']}</td>
        <td class='p-3'>{$row['berat']} kg</td>
        <td class='p-3'>{$row['tinggi']} cm</td>
        <td class='p-3 font-semibold'>{$row['bmi']}</td>
        <td class='p-3'>
            <span class='$warna px-3 py-1 rounded-full text-sm font-semibold'>
                {$row['kategori']}
            </span>
        </td>

        <!-- AKSI -->
        <td class='p-3 flex gap-2 justify-center'>
            <a href='edit.php?id={$row['id']}'
                class='bg-yellow-400 hover:bg-yellow-500 px-3 py-1 rounded text-white'>
                Edit
            </a>

            <a href='hapus.php?id={$row['id']}'
                onclick=\"return confirm('Yakin mau hapus?')\"
                class='bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white'>
                Hapus
            </a>
        </td>
    </tr>";
}
?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>

</html>