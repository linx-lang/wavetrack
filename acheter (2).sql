-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2026 at 09:04 AM
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
-- Table structure for table `acheter`
--

CREATE TABLE `acheter` (
  `prixAct` decimal(10,2) DEFAULT NULL,
  `dateP` datetime DEFAULT NULL,
  `qant` int(11) NOT NULL,
  `idDev` int(11) NOT NULL,
  `idT` int(11) NOT NULL,
  `idSalle` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `acheter`
--

INSERT INTO `acheter` (`prixAct`, `dateP`, `qant`, `idDev`, `idT`, `idSalle`) VALUES
('219.96', '2026-05-03 00:00:00', 4, 1, 1, 24),
('219.96', '2025-01-10 00:00:00', 4, 1, 1, 30),
('159.96', '2026-05-04 09:09:14', 4, 1, 2, 30);

--
-- Triggers `acheter`
--
DELIMITER $$
CREATE TRIGGER `trg_insert_prixAcht` BEFORE INSERT ON `acheter` FOR EACH ROW SET New.prixAct =(
    SELECT t.prixT
    FROM type t
    WHERE t.idT = NEW.idT
) * NEW.qant
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acheter`
--
ALTER TABLE `acheter`
  ADD PRIMARY KEY (`idDev`,`idT`,`idSalle`),
  ADD KEY `fk_idT` (`idT`),
  ADD KEY `fk_idSalle` (`idSalle`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `acheter`
--
ALTER TABLE `acheter`
  ADD CONSTRAINT `fk_idDev` FOREIGN KEY (`idDev`) REFERENCES `devis` (`idDev`),
  ADD CONSTRAINT `fk_idSalle` FOREIGN KEY (`idSalle`) REFERENCES `salle` (`idSalle`),
  ADD CONSTRAINT `fk_idT` FOREIGN KEY (`idT`) REFERENCES `type` (`idT`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
