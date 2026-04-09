-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 09 Apr 2026 pada 10.51
-- Versi server: 8.0.30
-- Versi PHP: 8.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `gizi_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_gizi`
--

CREATE TABLE `riwayat_gizi` (
  `id` int NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `berat` float DEFAULT NULL,
  `tinggi` float DEFAULT NULL,
  `bmi` float DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `riwayat_gizi`
--

INSERT INTO `riwayat_gizi` (`id`, `nama`, `tanggal`, `berat`, `tinggi`, `bmi`, `kategori`) VALUES
(3, 'Firman', '2026-04-09', 54, 171, 18.47, 'Kurus'),
(4, 'ilham', '2026-04-09', 54, 169, 18.91, 'Normal'),
(5, 'aulia', '2026-04-09', 65, 178, 20.52, 'Normal'),
(6, 'aul', '2026-04-09', 54, 176, 17.43, 'Kurus');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `riwayat_gizi`
--
ALTER TABLE `riwayat_gizi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `riwayat_gizi`
--
ALTER TABLE `riwayat_gizi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
