-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 18 Jan 2025 pada 05.15
-- Versi server: 10.4.21-MariaDB
-- Versi PHP: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `polidb`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `daftar_poli`
--

CREATE TABLE `daftar_poli` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_pasien` int(10) UNSIGNED NOT NULL,
  `id_jadwal` int(10) UNSIGNED NOT NULL,
  `keluhan` text NOT NULL,
  `no_antrian` int(11) NOT NULL,
  `status` enum('menunggu','diperiksa','selesai') DEFAULT 'menunggu',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `daftar_poli`
--

INSERT INTO `daftar_poli` (`id`, `id_pasien`, `id_jadwal`, `keluhan`, `no_antrian`, `status`, `created_at`, `updated_at`) VALUES
(40, 21, 7, 'sakit', 1, 'menunggu', '2024-12-05 20:56:12', '2024-12-22 15:36:23'),
(42, 20, 7, 'sakit', 2, 'menunggu', '2024-12-05 21:33:57', '2024-12-22 15:36:23'),
(43, 19, 9, 'sakit', 1, 'selesai', '2024-12-05 21:42:57', '2024-12-22 15:36:23'),
(44, 20, 9, 'sakit', 2, 'selesai', '2024-12-05 21:56:28', '2024-12-22 15:36:23'),
(45, 19, 9, 'sakit', 3, 'selesai', '2024-12-05 22:19:18', '2024-12-22 15:36:23'),
(54, 19, 11, 'Sakit', 1, 'selesai', '2024-12-08 20:14:23', '2024-12-22 16:04:01'),
(55, 33, 9, 'Sakit', 1, 'selesai', '2024-12-08 20:40:57', '2024-12-22 15:36:23'),
(70, 30, 11, 'sakit', 1, 'selesai', '2024-12-22 15:23:12', '2024-12-22 15:36:23'),
(76, 30, 11, 'oy', 4, 'selesai', '2024-12-22 16:47:09', '2024-12-22 16:55:19'),
(78, 36, 11, 'x', 5, 'selesai', '2024-12-22 17:01:10', '2024-12-22 17:01:56'),
(99, 39, 11, 'sakit', 6, 'selesai', '2024-12-23 06:12:10', '2024-12-23 06:13:05'),
(100, 30, 11, 'sakit', 7, 'selesai', '2024-12-23 06:14:15', '2024-12-23 06:14:34'),
(101, 40, 11, 'sakit', 8, 'selesai', '2024-12-23 06:30:10', '2024-12-23 06:30:55'),
(103, 30, 13, 'kotoran banyak', 1, 'selesai', '2024-12-26 12:24:25', '2024-12-26 12:25:07'),
(104, 30, 20, 'sakit', 1, 'selesai', '2024-12-27 13:26:46', '2024-12-27 13:27:18'),
(105, 40, 20, 'sakit', 2, 'selesai', '2024-12-27 13:29:31', '2024-12-27 13:39:08'),
(106, 38, 20, 'sakit', 3, 'selesai', '2024-12-27 13:40:29', '2024-12-27 13:56:42'),
(107, 39, 20, 'sakit\r\n', 4, 'selesai', '2024-12-27 13:58:18', '2024-12-27 13:59:06'),
(108, 31, 20, 'kotoran', 1, 'selesai', '2024-12-27 17:11:53', '2024-12-27 17:13:08'),
(109, 38, 20, 'x', 1, 'selesai', '2024-12-29 11:44:53', '2024-12-29 11:45:27'),
(110, 41, 20, 'k', 2, 'selesai', '2024-12-29 11:49:48', '2024-12-29 11:50:10'),
(114, 38, 42, 'X', 1, 'selesai', '2025-01-07 17:04:18', '2025-01-07 17:44:14'),
(115, 39, 42, 'Y', 2, 'selesai', '2025-01-07 17:06:02', '2025-01-07 17:07:46'),
(116, 39, 10, 'panas', 1, 'selesai', '2025-01-07 17:16:44', '2025-01-07 17:21:27'),
(117, 39, 9, 'x', 1, 'selesai', '2025-01-07 17:22:33', '2025-01-07 17:25:09'),
(118, 39, 8, 'sakit', 1, 'selesai', '2025-01-07 17:29:03', '2025-01-07 17:34:38'),
(119, 38, 13, 'sakit', 1, 'selesai', '2025-01-07 17:46:49', '2025-01-07 17:47:22'),
(120, 38, 19, 'karang gigi', 1, 'selesai', '2025-01-07 17:48:03', '2025-01-07 17:52:55'),
(122, 38, 42, 'karang', 3, 'selesai', '2025-01-07 17:53:57', '2025-01-07 17:54:43'),
(130, 38, 42, 'x', 4, 'selesai', '2025-01-07 18:42:12', '2025-01-07 18:46:47'),
(131, 38, 42, 'karang', 5, 'selesai', '2025-01-07 18:45:09', '2025-01-07 18:46:54'),
(132, 38, 42, 'karang', 6, 'selesai', '2025-01-07 18:46:00', '2025-01-07 18:47:03'),
(133, 38, 42, 'x\r\n', 7, 'selesai', '2025-01-07 18:49:44', '2025-01-07 18:51:12'),
(135, 38, 19, 'x', 2, 'selesai', '2025-01-07 19:10:27', '2025-01-07 19:13:40'),
(136, 38, 42, 'x', 8, 'selesai', '2025-01-07 19:15:38', '2025-01-07 19:15:56'),
(137, 38, 42, 'x', 9, 'selesai', '2025-01-07 19:28:39', '2025-01-07 19:28:53'),
(138, 38, 42, 'c', 10, 'selesai', '2025-01-07 19:31:40', '2025-01-07 19:31:53'),
(139, 38, 42, 'b', 11, 'selesai', '2025-01-07 19:43:59', '2025-01-07 19:44:19'),
(140, 38, 42, 'q', 12, 'selesai', '2025-01-07 20:41:22', '2025-01-07 20:41:55'),
(141, 38, 43, 'Kotoran', 1, 'selesai', '2025-01-08 18:44:40', '2025-01-08 18:46:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_periksa`
--

CREATE TABLE `detail_periksa` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_periksa` int(10) UNSIGNED NOT NULL,
  `id_obat` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `detail_periksa`
--

INSERT INTO `detail_periksa` (`id`, `id_periksa`, `id_obat`) VALUES
(2, 15, 3),
(3, 16, 3),
(4, 17, 3),
(5, 19, 5),
(6, 20, 3),
(89, 10, 3),
(93, 28, 3),
(96, 29, 5),
(97, 27, 5),
(100, 32, 3),
(101, 33, 1),
(102, 34, 3),
(103, 42, 3),
(104, 41, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokter`
--

CREATE TABLE `dokter` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `no_hp` varchar(50) NOT NULL,
  `id_poli` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `dokter`
--

INSERT INTO `dokter` (`id`, `nama`, `alamat`, `no_hp`, `id_poli`, `username`, `password`) VALUES
(15, 'Amel', 'semarang', '0812347454545', 10, 'Amel', '$2y$10$OzOz5jwy4dxbQBzVrx/uxemxaFbP1V.tmMekE2dRYWcGDQH.tXaty'),
(16, 'Konang', 'semarang', '02312313123', 6, 'Konang', '$2y$10$Gm4XxwFue3jPp2AiMapeXeG3JfNW74S7/l0VbVdOeUDVWJ8r3PQTC'),
(21, 'Ifan', 'Semarang', '0812346567550', 8, 'Ifan', '$2y$10$jx/qQiPXS7DwyHjUV2UNyeFdh7SSQsABLm9tlTLfsSsp8vXCBi5au'),
(23, 'adrian', 'Semarang', '0812346567567', 12, 'adrian', '$2y$10$0vniLFFAqbv4qqxdBpSYcOZ9wGXL5L1AZxoecUgOBA4.agNGjDqI6'),
(24, 'abdi', 'Semarang', '082132323123', 8, 'abdi', '$2y$10$36TueeWQkDOwsroGVMTW6ewgdiw7Y9etHZZyL8ZOgguLJa1r4rVZO');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_periksa`
--

CREATE TABLE `jadwal_periksa` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_dokter` int(10) UNSIGNED NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `status` enum('aktif','tidak aktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `jadwal_periksa`
--

INSERT INTO `jadwal_periksa` (`id`, `id_dokter`, `hari`, `jam_mulai`, `jam_selesai`, `status`) VALUES
(8, 15, 'Selasa', '08:00:00', '12:00:00', 'aktif'),
(9, 16, 'Senin', '08:00:00', '12:00:00', 'aktif'),
(10, 23, 'Kamis', '08:00:00', '12:00:00', 'aktif'),
(22, 16, 'Selasa', '08:00:00', '12:00:00', 'aktif'),
(24, 16, 'Kamis', '07:00:00', '08:00:00', 'tidak aktif'),
(42, 21, 'Senin', '07:00:00', '08:00:00', 'aktif'),
(43, 21, 'Rabu', '10:00:00', '14:00:00', 'tidak aktif'),
(44, 21, 'Selasa', '07:00:00', '08:00:00', 'tidak aktif'),
(45, 24, 'Senin', '07:00:00', '12:00:00', 'aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `konsultasi`
--

CREATE TABLE `konsultasi` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_pasien` int(10) UNSIGNED NOT NULL,
  `id_dokter` int(10) UNSIGNED NOT NULL,
  `subject` varchar(50) NOT NULL,
  `pertanyaan` text NOT NULL,
  `jawaban` text DEFAULT NULL,
  `tgl_konsultasi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `obat`
--

CREATE TABLE `obat` (
  `id` int(11) NOT NULL,
  `nama_obat` varchar(50) NOT NULL,
  `kemasan` varchar(35) DEFAULT NULL,
  `harga` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `obat`
--

INSERT INTO `obat` (`id`, `nama_obat`, `kemasan`, `harga`) VALUES
(1, 'Abacavir', 'tablet', 698000),
(3, 'Amoxicillin', 'Kapsul', 25000),
(4, 'Omeprazole', 'Tablet', 15000),
(5, 'Laserin', 'cair', 20000),
(13, 'Paracetamol', 'tablet', 5000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pasien`
--

CREATE TABLE `pasien` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `no_ktp` varchar(255) NOT NULL,
  `no_hp` varchar(50) NOT NULL,
  `no_rm` varchar(25) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pasien`
--

INSERT INTO `pasien` (`id`, `nama`, `alamat`, `no_ktp`, `no_hp`, `no_rm`, `username`, `password`) VALUES
(30, 'Adi', 'Semarang', '1111111111111114', '083424234234', '202412-005', 'Adi', '$2y$10$xuAD.jd/X2gK287DPd5qRO9WS0E3w8Jg6XP6hO6mh1kh5EZ/KA9DS'),
(31, 'Ali', 'Semarang', '1111111111111115', '034234324342', '202412-006', 'Ali', '$2y$10$E0GsSHroE6vUXrFRtkS2tuhBt8KGntiDaXJvI9d39slJlAaCLAF0K'),
(33, 'Aldin', 'Semarang ', '1111111111111116', '081343244533', '202412-007', 'Aldin ', '$2y$10$88GoEBKhz1fhfz7eX/Cxvu1GeGKd9DiJTnZOS0r0.t2o8SeJApSOq'),
(36, 'Aska', 'Semaratng', '1111111111111120', '08231232323', '202412-008', 'Aska', '$2y$10$TFCNN0DrCRbvncLNUAsqee1N.E3JmmXA.25zXA5b1n0DX30jR9tGy'),
(38, 'x', 'x', '1111111111111222', '0832321323123', '202412-009', 'x', '$2y$10$P3HsndRiv0kufqbJ1RB5sOMxR4KJvilrj5ujyudnYTeEBb0qBlhYC'),
(39, 'y', 'semarang', '1111111111111123', '0812123321321', '202412-010', 'y', '$2y$10$/QnjUTdvRHEoSRt/Vg/PLeHRsPe439tnFstAVHpT6af56hFm2hTW6'),
(40, 'nando', 'semarang', '1111111111111456', '0983213213213', '202412-011', 'nando', '$2y$10$GkLCGKuwHeUt/Xe/hkk.8.xb6m7fv8IIjR4t1zwveV5nA8snrA/jK'),
(41, 'k', 'semarang', '1111111111111203', '0821323213213', '202412-012', 'k', '$2y$10$WgA761a91KLec/ZEKpmMjezXCBKcdXIjDUAKImwuV5MbjKWgXiLRG');

-- --------------------------------------------------------

--
-- Struktur dari tabel `periksa`
--

CREATE TABLE `periksa` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_daftar_poli` int(10) UNSIGNED NOT NULL,
  `tgl_periksa` datetime NOT NULL,
  `catatan` text DEFAULT NULL,
  `biaya_periksa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `periksa`
--

INSERT INTO `periksa` (`id`, `id_daftar_poli`, `tgl_periksa`, `catatan`, `biaya_periksa`) VALUES
(7, 43, '2024-12-05 22:54:49', 'sips', 150000),
(8, 44, '2024-12-05 23:06:53', 'sips', 150000),
(9, 45, '2024-12-06 00:26:38', 'sipa', 150000),
(10, 55, '2024-12-08 21:54:48', 'diminum 2 kali sehari', 175000),
(11, 70, '2024-12-22 16:24:00', 'kotoran', 150000),
(12, 54, '2024-12-22 17:04:01', 'kotoran', 150000),
(13, 76, '2024-12-22 17:55:47', 'kotoran', 150000),
(14, 78, '2024-12-22 18:01:56', 'x', 150000),
(15, 99, '2024-12-23 07:13:05', 'contoh', 175000),
(16, 100, '2024-12-23 07:14:34', 'sakit', 175000),
(17, 101, '2024-12-23 07:30:55', 'sakit', 175000),
(18, 103, '2024-12-26 13:25:07', 'telinga kanan', 150000),
(19, 104, '2024-12-27 14:27:18', 'sakit', 170000),
(20, 105, '2024-12-27 14:39:08', 'sakit', 175000),
(21, 106, '2024-12-27 14:55:01', 'sudah bersih', 150000),
(22, 106, '2024-12-27 14:55:20', 'sudah bersih', 150000),
(23, 106, '2024-12-27 14:55:39', 'sudah bersih', 150000),
(24, 106, '2024-12-27 14:55:52', 'sudah bersih\r\n', 175000),
(25, 106, '2024-12-27 14:56:16', 'sudah bersih', 150000),
(26, 106, '2024-12-27 14:56:42', 'sudah bersih', 150000),
(27, 107, '2024-12-27 14:59:06', 'sehat', 170000),
(28, 108, '2024-12-27 18:13:08', 'top\r\n', 175000),
(29, 109, '2024-12-29 12:45:27', 'aman', 170000),
(30, 110, '2024-12-29 12:50:09', 'istirahat', 150000),
(31, 115, '2025-01-07 18:07:46', 'sudah bersih', 150000),
(32, 116, '2025-01-07 18:21:27', 'sehat ya makan buah yang banyak sayur dan sebagainya', 175000),
(33, 117, '2025-01-07 18:25:09', 'sips', 848000),
(34, 118, '2025-01-07 18:34:38', 'sips', 175000),
(35, 114, '2025-01-07 18:44:14', 'sips', 150000),
(36, 119, '2025-01-07 18:47:22', 'sips', 150000),
(37, 120, '2025-01-07 18:52:55', 'sips', 150000),
(38, 122, '2025-01-07 18:54:43', 'sips', 150000),
(39, 130, '2025-01-07 19:46:47', 'sips', 150000),
(40, 131, '2025-01-07 19:46:54', 'sips', 150000),
(41, 132, '2025-01-07 19:47:03', 'sipd', 175000),
(42, 133, '2025-01-07 19:51:12', 'sips', 175000),
(43, 135, '2025-01-07 20:13:40', 'x', 150000),
(44, 136, '2025-01-07 20:15:56', 'x', 150000),
(45, 137, '2025-01-07 20:28:53', 'x', 150000),
(46, 138, '2025-01-07 20:31:53', 'c', 150000),
(47, 139, '2025-01-07 20:44:19', 'b', 150000),
(48, 140, '2025-01-07 21:41:55', 'q', 150000),
(49, 141, '2025-01-08 19:46:06', 'sips', 150000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `poli`
--

CREATE TABLE `poli` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_poli` varchar(25) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `poli`
--

INSERT INTO `poli` (`id`, `nama_poli`, `keterangan`) VALUES
(6, 'JANTUNG', 'jantung'),
(8, 'THT', 'Tht'),
(10, 'GIGI', 'GIGI'),
(12, 'ANAK', 'Anak');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin') NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(4, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `daftar_poli`
--
ALTER TABLE `daftar_poli`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `id_jadwal` (`id_jadwal`);

--
-- Indeks untuk tabel `detail_periksa`
--
ALTER TABLE `detail_periksa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_periksa` (`id_periksa`),
  ADD KEY `id_obat` (`id_obat`);

--
-- Indeks untuk tabel `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_poli` (`id_poli`);

--
-- Indeks untuk tabel `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indeks untuk tabel `konsultasi`
--
ALTER TABLE `konsultasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indeks untuk tabel `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `periksa`
--
ALTER TABLE `periksa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_daftar_poli` (`id_daftar_poli`);

--
-- Indeks untuk tabel `poli`
--
ALTER TABLE `poli`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `daftar_poli`
--
ALTER TABLE `daftar_poli`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT untuk tabel `detail_periksa`
--
ALTER TABLE `detail_periksa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT untuk tabel `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT untuk tabel `konsultasi`
--
ALTER TABLE `konsultasi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `obat`
--
ALTER TABLE `obat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `periksa`
--
ALTER TABLE `periksa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `poli`
--
ALTER TABLE `poli`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_periksa`
--
ALTER TABLE `detail_periksa`
  ADD CONSTRAINT `detail_periksa_ibfk_1` FOREIGN KEY (`id_periksa`) REFERENCES `periksa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_periksa_ibfk_2` FOREIGN KEY (`id_obat`) REFERENCES `obat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `dokter`
--
ALTER TABLE `dokter`
  ADD CONSTRAINT `dokter_ibfk_1` FOREIGN KEY (`id_poli`) REFERENCES `poli` (`id`);

--
-- Ketidakleluasaan untuk tabel `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  ADD CONSTRAINT `jadwal_periksa_ibfk_1` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `konsultasi`
--
ALTER TABLE `konsultasi`
  ADD CONSTRAINT `konsultasi_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `konsultasi_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
