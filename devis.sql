-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 31, 2026 at 10:19 PM
-- Server version: 5.7.17
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projetsql`
--

-- --------------------------------------------------------

--
-- Table structure for table `devis`
--

CREATE TABLE `devis` (
  `idDev` int(11) NOT NULL,
  `prixTot` decimal(10,2) DEFAULT NULL,
  `dateP` datetime DEFAULT NULL,
  `idPro` int(11) DEFAULT NULL,
  `prixClient` decimal(10,0) DEFAULT NULL,
  `prixMois` decimal(11,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `devis`
--

INSERT INTO `devis` (`idDev`, `prixTot`, `dateP`, `idPro`, `prixClient`, `prixMois`) VALUES
(1, NULL, '2026-05-18 11:38:27', 1, '0', NULL),
(2, NULL, '2026-05-18 11:50:05', 2, '0', NULL),
(3, NULL, '2026-05-20 09:13:17', 3, '0', NULL),
(4, NULL, '2026-05-20 09:23:49', 4, '0', NULL),
(5, NULL, '2026-05-20 09:33:08', 6, '0', NULL),
(6, NULL, '2026-05-28 15:42:26', 8, '0', NULL),
(7, '11755.60', '2026-05-30 16:46:17', 9, '14107', NULL),
(8, '11632.98', '2026-05-30 17:19:53', 10, '13960', NULL),
(9, '11718.97', '2026-05-31 21:53:34', 11, '14063', '1172');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `devis`
--
ALTER TABLE `devis`
  ADD PRIMARY KEY (`idDev`),
  ADD KEY `fk_dev_idPro` (`idPro`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `devis`
--
ALTER TABLE `devis`
  MODIFY `idDev` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `devis`
--
ALTER TABLE `devis`
  ADD CONSTRAINT `fk_dev_idPro` FOREIGN KEY (`idPro`) REFERENCES `proprietaire` (`idPro`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
