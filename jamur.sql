-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Des 2023 pada 07.03
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jamur`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `deteksi`
--

CREATE TABLE `deteksi` (
  `deteksi_id` int(7) NOT NULL,
  `waktu_deteksi` timestamp NOT NULL DEFAULT current_timestamp(),
  `kategori` enum('Rusak','Sehat') NOT NULL,
  `akurasi` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `deteksi`
--

INSERT INTO `deteksi` (`deteksi_id`, `waktu_deteksi`, `kategori`, `akurasi`) VALUES
(32, '2023-11-24 03:58:03', 'Sehat', '82.85714285714286'),
(33, '2023-11-24 03:58:33', 'Sehat', '82.85714285714286'),
(34, '2023-11-24 03:59:04', 'Sehat', '82.85714285714286'),
(35, '2023-11-24 03:59:35', 'Sehat', '82.85714285714286'),
(36, '2023-11-24 04:00:06', 'Sehat', '82.85714285714286'),
(37, '2023-11-24 04:00:36', 'Sehat', '82.85714285714286'),
(38, '2023-11-24 04:01:06', 'Sehat', '82.85714285714286'),
(39, '2023-11-24 04:01:37', 'Sehat', '82.85714285714286'),
(40, '2023-11-24 04:02:07', 'Sehat', '82.85714285714286'),
(41, '2023-11-24 04:02:37', 'Sehat', '82.85714285714286'),
(42, '2023-11-24 04:03:07', 'Sehat', '82.85714285714286'),
(43, '2023-11-24 04:03:37', 'Sehat', '82.85714285714286'),
(44, '2023-11-24 04:04:07', 'Sehat', '82.85714285714286'),
(45, '2023-11-24 04:08:33', 'Sehat', '90.27777777777779'),
(46, '2023-11-24 04:09:03', 'Sehat', '90.27777777777779'),
(47, '2023-11-24 04:10:58', 'Sehat', '89.04109589041096'),
(48, '2023-11-24 04:11:29', 'Sehat', '89.04109589041096'),
(49, '2023-11-24 06:50:23', 'Sehat', '89.04109589041096'),
(50, '2023-11-24 06:50:53', 'Sehat', '89.04109589041096'),
(51, '2023-11-24 06:51:23', 'Sehat', '89.04109589041096'),
(52, '2023-12-06 10:36:28', 'Sehat', '88.17204301075269'),
(53, '2023-12-06 10:36:59', 'Sehat', '88.17204301075269');

-- --------------------------------------------------------

--
-- Struktur dari tabel `realtime_data`
--

CREATE TABLE `realtime_data` (
  `suhu_id` int(7) NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp(),
  `suhu` varchar(5) NOT NULL,
  `kelembapan` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `realtime_data`
--

INSERT INTO `realtime_data` (`suhu_id`, `waktu`, `suhu`, `kelembapan`) VALUES
(1, '2023-12-06 04:56:00', '24.06', '70.06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat`
--

CREATE TABLE `riwayat` (
  `jamur_id` int(8) NOT NULL,
  `jamur_tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `jamursuhu_max` varchar(5) NOT NULL,
  `kelembapan` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `riwayat`
--

INSERT INTO `riwayat` (`jamur_id`, `jamur_tanggal`, `jamursuhu_max`, `kelembapan`) VALUES
(3, '2023-12-06 05:49:42', '33.00', '50.06'),
(4, '2023-12-06 05:50:09', '30.06', '50.06'),
(5, '2023-12-06 05:50:21', '70.06', '50.06'),
(6, '2023-12-06 05:50:33', '36.06', '50.06'),
(7, '2023-12-06 05:50:52', '31.26', '50.06'),
(8, '2023-12-06 05:53:40', '34.06', '50.06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`) VALUES
(1, 'admin', '81dc9bdb52d04dc20036dbd8313ed055');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `deteksi`
--
ALTER TABLE `deteksi`
  ADD PRIMARY KEY (`deteksi_id`);

--
-- Indeks untuk tabel `realtime_data`
--
ALTER TABLE `realtime_data`
  ADD PRIMARY KEY (`suhu_id`);

--
-- Indeks untuk tabel `riwayat`
--
ALTER TABLE `riwayat`
  ADD PRIMARY KEY (`jamur_id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `deteksi`
--
ALTER TABLE `deteksi`
  MODIFY `deteksi_id` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT untuk tabel `realtime_data`
--
ALTER TABLE `realtime_data`
  MODIFY `suhu_id` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `riwayat`
--
ALTER TABLE `riwayat`
  MODIFY `jamur_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
