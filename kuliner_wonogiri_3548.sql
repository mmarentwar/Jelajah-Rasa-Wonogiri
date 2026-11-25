-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 25, 2026 at 06:05 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kuliner_wonogiri_3548`
--
CREATE DATABASE IF NOT EXISTS `kuliner_wonogiri_3548` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `kuliner_wonogiri_3548`;

-- --------------------------------------------------------

--
-- Table structure for table `jenis_makanan`
--

DROP TABLE IF EXISTS `jenis_makanan`;
CREATE TABLE `jenis_makanan` (
  `id_jenis` int NOT NULL,
  `nama_jenis` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jenis_makanan`
--

INSERT INTO `jenis_makanan` (`id_jenis`, `nama_jenis`) VALUES
(1, 'Makanan Berat'),
(2, 'Camilan'),
(3, 'Minuman'),
(4, 'Oleh-oleh');

-- --------------------------------------------------------

--
-- Table structure for table `kuliner`
--

DROP TABLE IF EXISTS `kuliner`;
CREATE TABLE `kuliner` (
  `id_kuliner` int NOT NULL,
  `id_jenis` int NOT NULL,
  `nama_makanan` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci NOT NULL,
  `kisaran_harga` enum('murah','sedang','mahal') COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_dicoba` date NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kuliner`
--

INSERT INTO `kuliner` (`id_kuliner`, `id_jenis`, `nama_makanan`, `deskripsi`, `kisaran_harga`, `tanggal_dicoba`, `foto`) VALUES
(1, 1, 'Nasi Tiwul', 'Tiwul dari singkong yang disajikan seperti nasi, dimakan bersama sayur dan lauk sederhana khas pedesaan Wonogiri.', 'murah', '2026-10-05', 'kuliner_20261125_223032_4d728e88.jpeg'),
(2, 1, 'Sate Kelinci', 'Sate daging kelinci dengan bumbu kacang atau kecap, dagingnya empuk dan tidak berbau.', 'sedang', '2026-10-06', 'kuliner_20261125_223023_b0067bf9.jpg'),
(3, 1, 'Mie Ayam Wonogiri', 'Mie ayam dengan topping ayam berbumbu manis gurih dan kuah kaldu yang hangat. Kuliner perantauan Wonogiri yang terkenal di banyak kota.', 'murah', '2026-10-07', 'kuliner_20261125_223014_bf91741c.jpeg'),
(4, 1, 'Bakso Wonogiri', 'Bakso dengan kuah kaldu sapi yang gurih, biasanya disajikan bersama mie, tahu, dan pangsit.', 'murah', '2026-10-08', 'kuliner_20261125_222110_9de53260.jpeg'),
(6, 2, 'Gaplek', 'Singkong yang dikupas lalu dijemur sampai kering. Bisa dimakan sebagai camilan dan juga jadi bahan dasar tiwul.', 'murah', '2026-10-10', 'kuliner_20261125_222958_417b0c58.jpg'),
(7, 2, 'Cemplon', 'Jajanan tradisional berbahan singkong parut dengan isian gula merah, dibentuk bulat lalu digoreng.', 'murah', '2026-10-11', 'kuliner_20261125_222943_48564fdc.jpg'),
(8, 2, 'Getuk Lindri', 'Getuk dari singkong yang digiling berbentuk mirip mie, berwarna-warni, dan ditaburi kelapa parut.', 'murah', '2026-10-12', 'kuliner_20261125_222930_eb9e980f.jpg'),
(9, 4, 'Keripik Singkong', 'Singkong yang diiris tipis lalu digoreng renyah, tersedia dengan rasa original, balado, dan lainnya.', 'sedang', '2026-10-13', 'kuliner_20261125_222921_94e3a1a5.jpg'),
(10, 4, 'Emping Mlinjo', 'Keripik tipis dari biji melinjo yang dipipihkan lalu digoreng, rasanya gurih dengan sedikit pahit.', 'sedang', '2026-10-14', 'kuliner_20261125_222911_3493cec9.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`) VALUES
(1, 'admin', '$2b$12$xztyM5.U879BAFobhYGlf.SlnF.T6w86BRZ9UtPtwdK5AiaTfEzLi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jenis_makanan`
--
ALTER TABLE `jenis_makanan`
  ADD PRIMARY KEY (`id_jenis`);

--
-- Indexes for table `kuliner`
--
ALTER TABLE `kuliner`
  ADD PRIMARY KEY (`id_kuliner`),
  ADD KEY `fk_kuliner_jenis` (`id_jenis`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jenis_makanan`
--
ALTER TABLE `jenis_makanan`
  MODIFY `id_jenis` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kuliner`
--
ALTER TABLE `kuliner`
  MODIFY `id_kuliner` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kuliner`
--
ALTER TABLE `kuliner`
  ADD CONSTRAINT `fk_kuliner_jenis` FOREIGN KEY (`id_jenis`) REFERENCES `jenis_makanan` (`id_jenis`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
