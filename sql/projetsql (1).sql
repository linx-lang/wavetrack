-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2026 at 12:06 AM
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
  `dateP` date DEFAULT NULL,
  `qant` int(11) NOT NULL,
  `idDev` int(11) NOT NULL,
  `idT` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `acheter`
--

INSERT INTO `acheter` (`prixAct`, `dateP`, `qant`, `idDev`, `idT`) VALUES
('219.96', '2025-01-10', 4, 1, 1);

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

-- --------------------------------------------------------

--
-- Table structure for table `alerte`
--

CREATE TABLE `alerte` (
  `idA` int(11) NOT NULL,
  `date` datetime DEFAULT NULL,
  `idD` int(11) DEFAULT NULL,
  `idM` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `artiste`
--

CREATE TABLE `artiste` (
  `idArt` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bouton`
--

CREATE TABLE `bouton` (
  `idB` int(11) NOT NULL,
  `etatb` varchar(50) DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `idSalle` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `capteurs`
--

CREATE TABLE `capteurs` (
  `idCap` int(11) NOT NULL,
  `lieuCAP` varchar(255) DEFAULT NULL,
  `idT` int(11) DEFAULT NULL,
  `idM` int(11) DEFAULT NULL,
  `idD` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `climatisation`
--

CREATE TABLE `climatisation` (
  `idClim` int(11) NOT NULL,
  `etat` varchar(50) DEFAULT NULL,
  `idB` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `creneaux`
--

CREATE TABLE `creneaux` (
  `idC` int(11) NOT NULL,
  `heureDeb` time DEFAULT NULL,
  `heureFin` time DEFAULT NULL,
  `date` date DEFAULT NULL,
  `idArt` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `devis`
--

CREATE TABLE `devis` (
  `idDev` int(11) NOT NULL,
  `prixTot` decimal(10,2) DEFAULT NULL,
  `dateP` date DEFAULT NULL,
  `idPro` int(11) DEFAULT NULL,
  `idAbon` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `devis`
--

INSERT INTO `devis` (`idDev`, `prixTot`, `dateP`, `idPro`, `idAbon`) VALUES
(1, NULL, '2026-04-10', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `donnee`
--

CREATE TABLE `donnee` (
  `idD` int(11) NOT NULL,
  `valeur` decimal(10,2) NOT NULL,
  `valeurBis` decimal(10,2) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `IdCap` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `jour`
--

CREATE TABLE `jour` (
  `idJ` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `machine`
--

CREATE TABLE `machine` (
  `idM` int(11) NOT NULL,
  `valeurMax` decimal(10,2) DEFAULT NULL,
  `noM` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `idMess` int(11) NOT NULL,
  `texte` text,
  `idSalle` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ouverture`
--

CREATE TABLE `ouverture` (
  `Idj` int(11) NOT NULL,
  `IdSalle` int(11) NOT NULL,
  `horaireDeb` time NOT NULL,
  `horaireFin` time NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `porte`
--

CREATE TABLE `porte` (
  `idP` int(11) NOT NULL,
  `idSalle` int(11) DEFAULT NULL,
  `idCap` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `proprietaire`
--

CREATE TABLE `proprietaire` (
  `idPro` int(11) NOT NULL,
  `nomP` varchar(100) DEFAULT NULL,
  `prenomP` varchar(100) DEFAULT NULL,
  `mailP` varchar(150) DEFAULT NULL,
  `mdpP` varchar(255) DEFAULT NULL,
  `numP` varchar(20) DEFAULT NULL,
  `idSalle` int(11) DEFAULT NULL,
  `nomAb` varchar(25) NOT NULL,
  `dateAb` date NOT NULL,
  `nb_salle` int(11) DEFAULT NULL,
  `prixAbon` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `proprietaire`
--

INSERT INTO `proprietaire` (`idPro`, `nomP`, `prenomP`, `mailP`, `mdpP`, `numP`, `idSalle`, `nomAb`, `dateAb`, `nb_salle`, `prixAbon`) VALUES
(1, 'AM', 'Caine', 'caine.am@gmail.com', '1234', '0612345678', 1, '', '0000-00-00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `salle`
--

CREATE TABLE `salle` (
  `idSalle` int(11) NOT NULL,
  `nomS` varchar(100) DEFAULT NULL,
  `lieus` varchar(255) DEFAULT NULL,
  `capacite` int(11) DEFAULT NULL,
  `codePoS` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE `type` (
  `idT` int(11) NOT NULL,
  `nomT` varchar(100) DEFAULT NULL,
  `prixT` decimal(10,2) DEFAULT NULL,
  `valeurT` varchar(255) DEFAULT NULL,
  `etatT` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `type`
--

INSERT INTO `type` (`idT`, `nomT`, `prixT`, `valeurT`, `etatT`) VALUES
(1, 'Multisensor', '54.99', 'Temperature et luminosité', 1),
(2, 'Door Window Sensor', '39.99', 'Mouvement porte', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acheter`
--
ALTER TABLE `acheter`
  ADD PRIMARY KEY (`idDev`,`idT`);

--
-- Indexes for table `alerte`
--
ALTER TABLE `alerte`
  ADD PRIMARY KEY (`idA`),
  ADD KEY `idD` (`idD`),
  ADD KEY `idM` (`idM`);

--
-- Indexes for table `artiste`
--
ALTER TABLE `artiste`
  ADD PRIMARY KEY (`idArt`);

--
-- Indexes for table `bouton`
--
ALTER TABLE `bouton`
  ADD PRIMARY KEY (`idB`),
  ADD KEY `idSalle` (`idSalle`);

--
-- Indexes for table `capteurs`
--
ALTER TABLE `capteurs`
  ADD PRIMARY KEY (`idCap`),
  ADD KEY `idT` (`idT`),
  ADD KEY `idM` (`idM`),
  ADD KEY `idD` (`idD`);

--
-- Indexes for table `climatisation`
--
ALTER TABLE `climatisation`
  ADD PRIMARY KEY (`idClim`),
  ADD KEY `idB` (`idB`);

--
-- Indexes for table `creneaux`
--
ALTER TABLE `creneaux`
  ADD PRIMARY KEY (`idC`),
  ADD KEY `idArt` (`idArt`);

--
-- Indexes for table `devis`
--
ALTER TABLE `devis`
  ADD PRIMARY KEY (`idDev`),
  ADD KEY `idPro` (`idPro`),
  ADD KEY `idAbon` (`idAbon`);

--
-- Indexes for table `donnee`
--
ALTER TABLE `donnee`
  ADD PRIMARY KEY (`idD`,`valeur`),
  ADD KEY `fk_donnee_capteur` (`IdCap`);

--
-- Indexes for table `jour`
--
ALTER TABLE `jour`
  ADD PRIMARY KEY (`idJ`);

--
-- Indexes for table `machine`
--
ALTER TABLE `machine`
  ADD PRIMARY KEY (`idM`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`idMess`),
  ADD KEY `idSalle` (`idSalle`);

--
-- Indexes for table `ouverture`
--
ALTER TABLE `ouverture`
  ADD PRIMARY KEY (`Idj`,`IdSalle`),
  ADD KEY `fk_salle` (`IdSalle`);

--
-- Indexes for table `porte`
--
ALTER TABLE `porte`
  ADD PRIMARY KEY (`idP`),
  ADD KEY `idSalle` (`idSalle`),
  ADD KEY `idCap` (`idCap`);

--
-- Indexes for table `proprietaire`
--
ALTER TABLE `proprietaire`
  ADD PRIMARY KEY (`idPro`),
  ADD KEY `fk_idSalle` (`idSalle`);

--
-- Indexes for table `salle`
--
ALTER TABLE `salle`
  ADD PRIMARY KEY (`idSalle`);

--
-- Indexes for table `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`idT`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alerte`
--
ALTER TABLE `alerte`
  MODIFY `idA` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `artiste`
--
ALTER TABLE `artiste`
  MODIFY `idArt` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bouton`
--
ALTER TABLE `bouton`
  MODIFY `idB` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `capteurs`
--
ALTER TABLE `capteurs`
  MODIFY `idCap` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `climatisation`
--
ALTER TABLE `climatisation`
  MODIFY `idClim` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `creneaux`
--
ALTER TABLE `creneaux`
  MODIFY `idC` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `devis`
--
ALTER TABLE `devis`
  MODIFY `idDev` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `jour`
--
ALTER TABLE `jour`
  MODIFY `idJ` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `machine`
--
ALTER TABLE `machine`
  MODIFY `idM` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `idMess` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `porte`
--
ALTER TABLE `porte`
  MODIFY `idP` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `proprietaire`
--
ALTER TABLE `proprietaire`
  MODIFY `idPro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `salle`
--
ALTER TABLE `salle`
  MODIFY `idSalle` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `type`
--
ALTER TABLE `type`
  MODIFY `idT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
