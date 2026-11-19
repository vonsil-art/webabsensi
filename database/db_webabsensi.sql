-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Nov 2026 pada 11.30
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_webabsensi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_absensi`
--

CREATE TABLE `tbl_absensi` (
  `id_absensi` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Izin','Sakit','Alpa') NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_absensi`
--

INSERT INTO `tbl_absensi` (`id_absensi`, `id_siswa`, `id_guru`, `tanggal`, `status`, `keterangan`) VALUES
(4, 1, 6, '2026-10-15', 'Hadir', ''),
(5, 4, 6, '2026-10-15', 'Hadir', ''),
(6, 8, 6, '2026-10-15', 'Hadir', ''),
(7, 7, 6, '2026-10-15', 'Hadir', ''),
(8, 6, 6, '2026-10-15', 'Hadir', ''),
(9, 4, 6, '2026-10-16', 'Hadir', NULL),
(10, 8, 6, '2026-10-16', 'Hadir', NULL),
(11, 6, 6, '2026-10-16', 'Sakit', NULL),
(12, 1, 6, '2026-10-16', 'Izin', NULL),
(13, 7, 6, '2026-10-16', 'Hadir', NULL),
(14, 4, 6, '2026-11-10', 'Sakit', NULL),
(15, 8, 6, '2026-11-10', 'Sakit', NULL),
(16, 6, 6, '2026-11-10', 'Sakit', NULL),
(17, 1, 6, '2026-11-10', 'Alpa', NULL),
(18, 7, 6, '2026-11-10', 'Izin', NULL),
(19, 5, 8, '2026-11-10', 'Hadir', NULL),
(20, 10, 8, '2026-11-10', 'Hadir', NULL),
(21, 3, 8, '2026-11-10', 'Hadir', NULL),
(22, 9, 8, '2026-11-10', 'Hadir', NULL),
(23, 11, 8, '2026-11-10', 'Hadir', NULL),
(24, 2, 8, '2026-11-10', 'Hadir', NULL),
(25, 4, 6, '2026-11-13', 'Hadir', NULL),
(26, 8, 6, '2026-11-13', 'Izin', NULL),
(27, 6, 6, '2026-11-13', 'Izin', NULL),
(28, 1, 6, '2026-11-13', 'Hadir', NULL),
(29, 7, 6, '2026-11-13', 'Hadir', NULL),
(30, 4, 6, '2026-11-18', 'Hadir', NULL),
(31, 8, 6, '2026-11-18', 'Hadir', NULL),
(32, 6, 6, '2026-11-18', 'Hadir', NULL),
(33, 1, 6, '2026-11-18', 'Hadir', NULL),
(34, 7, 6, '2026-11-18', 'Hadir', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_admin`
--

INSERT INTO `tbl_admin` (`id_admin`, `username`, `password`) VALUES
(1, 'admin1', '$2a$12$dxnmybjr/pPBrB.JM/syY.aC1wXffg6rZdrAj0N/5q7j7bAFGhO5m');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_guru`
--

CREATE TABLE `tbl_guru` (
  `id_guru` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `nama_guru` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_guru`
--

INSERT INTO `tbl_guru` (`id_guru`, `nip`, `nama_guru`, `username`, `password`) VALUES
(4, '2222222', 'achmad', 'mad', '$2a$12$K1EpoOuKdzYRL1BmLmp30.enD1KBvpO0FyFElaW/p9fvth2wq6HtC'),
(6, '1111111', 'wawan', 'wan', '$2a$12$XWtsSU7wn.jb8Yf84L7Wu.pb6KxjT1tBhYV5mXMnCNePuuIpHu7I6'),
(8, '3333333', 'Karim', 'kar', '$2y$10$0bywNWZsFVP7C4SbO8x99e5oMTN3KngwR1Tk8nlPi2V/cntV5RA2m'),
(9, '0987654321', 'Wahyu Siswanto', 'wahyusiswanto', '$2y$10$yHdMhb4QA6FAAMBi9CqBU.VS/hvmvj/JVt8n1L4d.vTmBpXCxfHWy');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_kelas`
--

CREATE TABLE `tbl_kelas` (
  `id_kelas` int(11) NOT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `id_wali_guru` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_kelas`
--

INSERT INTO `tbl_kelas` (`id_kelas`, `nama_kelas`, `id_wali_guru`) VALUES
(1, 'Banat 1', 6),
(2, 'Banin 1', 8),
(4, 'Banin 4', 9);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_siswa`
--

CREATE TABLE `tbl_siswa` (
  `id_siswa` int(11) NOT NULL,
  `nis` varchar(30) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `id_kelas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_siswa`
--

INSERT INTO `tbl_siswa` (`id_siswa`, `nis`, `nama_siswa`, `id_kelas`) VALUES
(1, '1122334455', 'kevin', 1),
(2, '11223344', 'vina', 2),
(3, '112233', 'hainab', 2),
(4, '412123', 'aiman', 1),
(5, '123123132', 'aisyah', 2),
(6, '66666666', 'joko', 1),
(7, '0909000', 'mamad', 1),
(8, '9898988989', 'danil', 1),
(9, '65656665', 'hanna', 2),
(10, '5454454545', 'ayu', 2),
(11, '1845375', 'Laila', 2),
(12, '87654321445', 'Irafan', 4),
(13, '7364364356', 'Salman Alfarizi', 4),
(14, '743534754345', 'Arshaka El Pram', 4);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tbl_absensi`
--
ALTER TABLE `tbl_absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD UNIQUE KEY `unik_absensi_harian` (`id_siswa`,`tanggal`),
  ADD KEY `id_guru` (`id_guru`);

--
-- Indeks untuk tabel `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `tbl_guru`
--
ALTER TABLE `tbl_guru`
  ADD PRIMARY KEY (`id_guru`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `tbl_kelas`
--
ALTER TABLE `tbl_kelas`
  ADD PRIMARY KEY (`id_kelas`),
  ADD UNIQUE KEY `nama_kelas` (`nama_kelas`),
  ADD KEY `id_wali_guru` (`id_wali_guru`);

--
-- Indeks untuk tabel `tbl_siswa`
--
ALTER TABLE `tbl_siswa`
  ADD PRIMARY KEY (`id_siswa`),
  ADD UNIQUE KEY `nis` (`nis`),
  ADD KEY `id_kelas` (`id_kelas`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tbl_absensi`
--
ALTER TABLE `tbl_absensi`
  MODIFY `id_absensi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT untuk tabel `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tbl_guru`
--
ALTER TABLE `tbl_guru`
  MODIFY `id_guru` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `tbl_kelas`
--
ALTER TABLE `tbl_kelas`
  MODIFY `id_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tbl_siswa`
--
ALTER TABLE `tbl_siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tbl_absensi`
--
ALTER TABLE `tbl_absensi`
  ADD CONSTRAINT `fk_absensi_guru` FOREIGN KEY (`id_guru`) REFERENCES `tbl_guru` (`id_guru`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_absensi_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `tbl_siswa` (`id_siswa`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_kelas`
--
ALTER TABLE `tbl_kelas`
  ADD CONSTRAINT `fk_kelas_wali` FOREIGN KEY (`id_wali_guru`) REFERENCES `tbl_guru` (`id_guru`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_siswa`
--
ALTER TABLE `tbl_siswa`
  ADD CONSTRAINT `fk_siswa_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `tbl_kelas` (`id_kelas`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
