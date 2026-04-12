<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM riwayat_gizi WHERE id=$id");

header("Location: index.php");
?>