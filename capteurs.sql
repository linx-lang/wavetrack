-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2026 at 03:19 PM
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
-- Table structure for table `capteurs`
--

CREATE TABLE `capteurs` (
  `idCap` int(11) NOT NULL,
  `lieuCAP` varchar(255) DEFAULT NULL,
  `idT` int(11) DEFAULT NULL,
  `idM` int(11) DEFAULT NULL,
  `idD` int(11) DEFAULT NULL,
  `idZone` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `capteurs`
--
ALTER TABLE `capteurs`
  ADD PRIMARY KEY (`idCap`),
  ADD KEY `idT` (`idT`),
  ADD KEY `idM` (`idM`),
  ADD KEY `idD` (`idD`),
  ADD KEY `fk_idZone` (`idZone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `capteurs`
--
ALTER TABLE `capteurs`
  MODIFY `idCap` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
