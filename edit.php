<?php
include 'koneksi.php';

$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM riwayat_gizi WHERE id=$id");
$row = mysqli_fetch_assoc($data);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-6 rounded-xl shadow-xl w-96">
        <h2 class="text-xl font-bold mb-4">Edit Data</h2>

        <form action="update.php" method="POST" class="space-y-3">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">

            <input type="text" name="nama" value="<?= $row['nama'] ?>" class="w-full p-2 border rounded">

            <input type="date" name="tanggal" value="<?= $row['tanggal'] ?>" class="w-full p-2 border rounded">

            <input type="number" step="0.1" name="berat" value="<?= $row['berat'] ?>" class="w-full p-2 border rounded">

            <input type="number" step="0.1" name="tinggi" value="<?= $row['tinggi'] ?>"
                class="w-full p-2 border rounded">

            <button class="bg-green-500 text-white w-full p-2 rounded hover:bg-green-600">
                Update
            </button>
            <button class="bg-green-500 text-white w-full p-2 rounded hover:bg-green-600">
                Kembali
            </button>
        </form>
    </div>

</body>

</html>