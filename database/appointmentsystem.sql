-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 02, 2025 at 05:01 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `appointmentsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` enum('admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `role`) VALUES
(1, 'admin', 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `daftar_poli`
--

CREATE TABLE `daftar_poli` (
  `id_daftar` int NOT NULL,
  `id_pasien` int DEFAULT NULL,
  `id_jadwal` int DEFAULT NULL,
  `keluhan` text NOT NULL,
  `no_antrian` int NOT NULL,
  `status` enum('belum','selesai') DEFAULT 'belum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `daftar_poli`
--

INSERT INTO `daftar_poli` (`id_daftar`, `id_pasien`, `id_jadwal`, `keluhan`, `no_antrian`, `status`) VALUES
(1, 1, 7, 'gigi bolong dok', 1, 'selesai'),
(2, 6, 8, 'sakit demam', 1, 'selesai'),
(3, 1, 8, 'Panas', 1, 'selesai'),
(4, 4, 8, 'sakit ini dok', 1, 'selesai'),
(5, 6, 7, 'sakit dok', 1, 'selesai'),
(6, 8, 8, 'pusing', 1, 'selesai'),
(7, 9, 8, 'halo dok', 1, 'selesai'),
(8, 4, 9, 'dok', 1, 'selesai'),
(9, 9, 9, 'in', 1, 'selesai'),
(10, 6, 8, 'sakit dok ini', 1, 'selesai'),
(11, 8, 8, 'sakit lagi dok', 1, 'selesai'),
(12, 10, 8, 'sakit', 1, 'selesai'),
(13, 11, 8, 'sakit', 1, 'selesai'),
(14, 11, 7, 'bolong', 1, 'belum'),
(15, 10, 7, 'ngilu', 1, 'belum'),
(16, 9, 7, 'aduh', 1, 'belum'),
(17, 9, 9, 'sakit', 2, 'belum'),
(18, 12, 7, 'sakit', 2, 'selesai');

-- --------------------------------------------------------

--
-- Table structure for table `detail_periksa`
--

CREATE TABLE `detail_periksa` (
  `id_detail` int NOT NULL,
  `id_periksa` int DEFAULT NULL,
  `id_obat` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_periksa`
--

INSERT INTO `detail_periksa` (`id_detail`, `id_periksa`, `id_obat`) VALUES
(4, 4, 6),
(5, 4, 11),
(6, 4, 16),
(7, 4, 17),
(8, 10, 16),
(9, 10, 17),
(10, 10, 18),
(14, 11, 4),
(15, 11, 7),
(16, 11, 9),
(17, 11, 11),
(18, 12, 4),
(19, 12, 7),
(20, 13, 1),
(21, 13, 4),
(22, 14, 7),
(23, 14, 16),
(24, 15, 15),
(25, 15, 16);

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id_dokter` int NOT NULL,
  `nama_dokter` varchar(255) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_hp` varchar(15) NOT NULL,
  `id_poli` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dokter`
--

INSERT INTO `dokter` (`id_dokter`, `nama_dokter`, `alamat`, `no_hp`, `id_poli`) VALUES
(1, 'Imanuel', 'Semarang', '089503666973', 2),
(3, 'dimas', 'ngaliyan', '3333', 2),
(5, 'Cahyo', 'Semarang', '0821998', 1);

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_periksa`
--

CREATE TABLE `jadwal_periksa` (
  `id_jadwal` int NOT NULL,
  `id_dokter` int DEFAULT NULL,
  `hari` varchar(20) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `aktif` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jadwal_periksa`
--

INSERT INTO `jadwal_periksa` (`id_jadwal`, `id_dokter`, `hari`, `jam_mulai`, `jam_selesai`, `aktif`) VALUES
(6, 1, 'Senin', '17:02:00', '20:05:00', 0),
(7, 1, 'Selasa', '18:04:00', '20:07:00', 1),
(8, 5, 'Jumat', '15:00:00', '20:00:00', 1),
(9, 3, 'Jumat', '15:30:00', '20:30:00', 1),
(10, 5, 'Kamis', '11:15:00', '15:15:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `obat`
--

CREATE TABLE `obat` (
  `id_obat` int NOT NULL,
  `nama_obat` varchar(255) NOT NULL,
  `kemasan` varchar(35) DEFAULT NULL,
  `harga` int UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `obat`
--

INSERT INTO `obat` (`id_obat`, `nama_obat`, `kemasan`, `harga`) VALUES
(1, 'ACT (Artesunate tablet 50 mg + Amodiaquine anhydrida tablet 200 mg)', '2 blister @ 12 tablet / kotak', 440000),
(4, 'Paracetamol', 'Strip', 5000),
(6, 'Ibuprofen', 'Tablet 200mg', 15000),
(7, 'Amoxicillin', 'Kapsul 500mg', 25000),
(8, 'Vitamin C', 'Tablet Effervescent', 75000),
(9, 'Cetirizine', 'Tablet 10mg', 12000),
(10, 'Omeprazole', 'Kapsul 20mg', 18000),
(11, 'Antasida Doen', 'Tablet Kunyah', 5000),
(12, 'Cefixime', 'Kapsul 100mg', 35000),
(13, 'Metformin', 'Tablet 500mg', 20000),
(14, 'Simvastatin', 'Tablet 10mg', 25000),
(15, 'Azithromycin', 'Tablet 250mg', 45000),
(16, 'Hydroxychloroquine', 'Tablet 200mg', 75000),
(17, 'Chlorpheniramine Maleate', 'Tablet 4mg', 3000),
(18, 'Ranitidine', 'Tablet 150mg', 12000);

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` int NOT NULL,
  `nama_pasien` varchar(255) NOT NULL,
  `alamat` text,
  `no_ktp` varchar(16) NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `no_rm` char(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pasien`
--

INSERT INTO `pasien` (`id_pasien`, `nama_pasien`, `alamat`, `no_ktp`, `no_hp`, `no_rm`) VALUES
(1, 'Noel', 'Semarang', '33333', '00000', '202412-0001'),
(4, 'test', 'jawa', '337415', '08950366', '202412-0003'),
(6, 'riyan', 'semarang', '9090989', '989078', '202412-0005'),
(8, 'Henok', 'bsb', '9090909', '089787', '202412-0004'),
(9, 'qq', 'bsb', '898989', '080808', '202412-0005'),
(10, 'tata', 'bsb', '989898', '918786', '202412-0006'),
(11, 'regita', 'bsb', '887878', '098787', '202412-0007'),
(12, 'farel', 'udinus', '8787878', '64265472', '202501-0001');

-- --------------------------------------------------------

--
-- Table structure for table `periksa`
--

CREATE TABLE `periksa` (
  `id_periksa` int NOT NULL,
  `id_daftar` int DEFAULT NULL,
  `tgl_periksa` date NOT NULL,
  `catatan` text NOT NULL,
  `biaya_periksa` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `periksa`
--

INSERT INTO `periksa` (`id_periksa`, `id_daftar`, `tgl_periksa`, `catatan`, `biaya_periksa`) VALUES
(1, 2, '2024-12-18', 'istirahat', 590000),
(2, 4, '2024-12-18', 'halo', 155000),
(3, 3, '2024-12-23', 'istirahat', 590000),
(4, 6, '2024-12-30', 'lekas sembuh', 248000),
(5, 1, '2024-12-29', 'sikat giti', 198000),
(6, 1, '2024-12-29', 'sikat gigi', 623000),
(7, 5, '2024-12-29', 'tidur', 180000),
(8, 7, '2024-12-29', 'hai', 210000),
(9, 10, '2024-12-30', 'tidur', 198000),
(10, 11, '2024-12-30', 'tidur aja udah', 240000),
(11, 12, '2024-12-31', 'tidur ya', 197000),
(12, 13, '2024-12-31', 'istirahat', 180000),
(13, 8, '2025-01-01', 'tidur', 595000),
(14, 9, '2025-01-01', 'aaa', 250000),
(15, 18, '2025-01-01', 'oke', 270000);

-- --------------------------------------------------------

--
-- Table structure for table `poli`
--

CREATE TABLE `poli` (
  `id_poli` int NOT NULL,
  `nama_poli` varchar(255) NOT NULL,
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `poli`
--

INSERT INTO `poli` (`id_poli`, `nama_poli`, `keterangan`) VALUES
(1, 'Umum', 'Dokter Umum'),
(2, 'Gigi', 'Dokter Gigi'),
(6, 'jajal', 'halo');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `daftar_poli`
--
ALTER TABLE `daftar_poli`
  ADD PRIMARY KEY (`id_daftar`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `id_jadwal` (`id_jadwal`);

--
-- Indexes for table `detail_periksa`
--
ALTER TABLE `detail_periksa`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_periksa` (`id_periksa`),
  ADD KEY `id_obat` (`id_obat`);

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id_dokter`),
  ADD KEY `id_poli` (`id_poli`);

--
-- Indexes for table `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indexes for table `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`id_obat`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id_pasien`),
  ADD UNIQUE KEY `no_ktp` (`no_ktp`);

--
-- Indexes for table `periksa`
--
ALTER TABLE `periksa`
  ADD PRIMARY KEY (`id_periksa`),
  ADD KEY `id_daftar` (`id_daftar`);

--
-- Indexes for table `poli`
--
ALTER TABLE `poli`
  ADD PRIMARY KEY (`id_poli`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `daftar_poli`
--
ALTER TABLE `daftar_poli`
  MODIFY `id_daftar` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `detail_periksa`
--
ALTER TABLE `detail_periksa`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id_dokter` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  MODIFY `id_jadwal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `obat`
--
ALTER TABLE `obat`
  MODIFY `id_obat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `periksa`
--
ALTER TABLE `periksa`
  MODIFY `id_periksa` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `poli`
--
ALTER TABLE `poli`
  MODIFY `id_poli` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daftar_poli`
--
ALTER TABLE `daftar_poli`
  ADD CONSTRAINT `daftar_poli_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`),
  ADD CONSTRAINT `daftar_poli_ibfk_2` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_periksa` (`id_jadwal`);

--
-- Constraints for table `detail_periksa`
--
ALTER TABLE `detail_periksa`
  ADD CONSTRAINT `detail_periksa_ibfk_1` FOREIGN KEY (`id_periksa`) REFERENCES `periksa` (`id_periksa`),
  ADD CONSTRAINT `detail_periksa_ibfk_2` FOREIGN KEY (`id_obat`) REFERENCES `obat` (`id_obat`);

--
-- Constraints for table `dokter`
--
ALTER TABLE `dokter`
  ADD CONSTRAINT `dokter_ibfk_1` FOREIGN KEY (`id_poli`) REFERENCES `poli` (`id_poli`);

--
-- Constraints for table `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  ADD CONSTRAINT `jadwal_periksa_ibfk_1` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`);

--
-- Constraints for table `periksa`
--
ALTER TABLE `periksa`
  ADD CONSTRAINT `periksa_ibfk_1` FOREIGN KEY (`id_daftar`) REFERENCES `daftar_poli` (`id_daftar`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
