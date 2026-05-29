-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2026 at 06:45 PM
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
-- Table structure for table `creneaux`
--

CREATE TABLE `creneaux` (
  `idC` int(11) NOT NULL,
  `heureDeb` time DEFAULT NULL,
  `heureFin` time DEFAULT NULL,
  `date` date DEFAULT NULL,
  `idArt` int(11) DEFAULT NULL,
  `idSalle` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `creneaux`
--
ALTER TABLE `creneaux`
  ADD PRIMARY KEY (`idC`),
  ADD KEY `idArt` (`idArt`),
  ADD KEY `fk_idSalle` (`idSalle`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `creneaux`
--
ALTER TABLE `creneaux`
  MODIFY `idC` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
