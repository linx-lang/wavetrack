-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2026 at 06:37 PM
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
('122.62', NULL, 2, 1, 1, 1),
('171.98', NULL, 2, 1, 2, 1),
('50.90', NULL, 2, 1, 3, 1),
('23.38', NULL, 2, 1, 4, 1),
('122.62', NULL, 2, 2, 1, 2),
('171.98', NULL, 2, 2, 2, 2),
('25.45', NULL, 1, 2, 3, 2),
('23.38', NULL, 2, 2, 4, 2),
('245.24', NULL, 4, 5, 1, 3),
('343.96', NULL, 4, 5, 2, 3),
('171.98', NULL, 2, 6, 2, 6),
('23.38', NULL, 2, 6, 4, 6),
('122.62', NULL, 2, 7, 1, 7),
('171.98', NULL, 2, 7, 2, 7),
('171.98', NULL, 2, 8, 2, 8);

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
DELIMITER $$
CREATE TRIGGER `trg_update_prixtot` AFTER INSERT ON `acheter` FOR EACH ROW UPDATE devis
SET prixTot= (SELECT SUM(prixTotal) FROM v_prixsalle WHERE v_prixsalle.idDev=NEW.idDev)
WHERE `idDev` = NEW.`idDev`
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `alerte`
--

CREATE TABLE `alerte` (
  `idA` int(11) NOT NULL,
  `date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `idD` int(11) DEFAULT NULL,
  `idM` int(11) DEFAULT NULL,
  `ValeurA` decimal(11,0) NOT NULL,
  `resolu` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `alerte`
--

INSERT INTO `alerte` (`idA`, `date`, `idD`, `idM`, `ValeurA`, `resolu`) VALUES
(1, '2026-05-01 08:12:00', 3, 1, '28', NULL),
(2, '2026-05-01 09:45:00', 3, 1, '29', NULL),
(3, '2026-05-01 11:20:00', 3, 1, '30', NULL),
(4, '2026-05-02 07:55:00', 3, 1, '28', NULL),
(5, '2026-05-02 10:10:00', 3, 1, '31', NULL),
(6, '2026-05-02 14:22:00', 3, 1, '33', NULL),
(7, '2026-05-03 09:00:00', 3, 1, '29', NULL),
(8, '2026-05-03 12:30:00', 3, 1, '30', NULL),
(9, '2026-05-01 08:00:00', 4, 2, '65', NULL),
(10, '2026-05-01 13:15:00', 4, 2, '70', NULL),
(11, '2026-05-02 09:40:00', 4, 2, '72', NULL),
(12, '2026-05-02 16:10:00', 4, 2, '75', NULL),
(13, '2026-05-03 10:05:00', 4, 2, '69', NULL),
(14, '2026-05-01 07:30:00', 5, 3, '40', NULL),
(15, '2026-05-01 11:50:00', 5, 3, '42', NULL),
(16, '2026-05-02 08:20:00', 5, 3, '45', NULL),
(17, '2026-05-02 15:00:00', 5, 3, '48', NULL),
(18, '2026-05-03 09:10:00', 5, 3, '44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `artiste`
--

CREATE TABLE `artiste` (
  `idArt` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `artiste`
--

INSERT INTO `artiste` (`idArt`, `nom`) VALUES
(1, 'Malice Mizer'),
(2, 'The Smiths'),
(3, 'Alex G'),
(4, 'Mars Argo'),
(5, 'SALES'),
(8, 'Jack Stauber');

-- --------------------------------------------------------

--
-- Table structure for table `bouton`
--

CREATE TABLE `bouton` (
  `idB` int(11) NOT NULL,
  `etatb` varchar(50) DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `idSalle` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bouton`
--

INSERT INTO `bouton` (`idB`, `etatb`, `prix`, `idSalle`) VALUES
(1, '0', '0.00', 7),
(2, '0', '0.00', 7);

-- --------------------------------------------------------

--
-- Table structure for table `capteurs`
--

CREATE TABLE `capteurs` (
  `idCap` int(11) NOT NULL,
  `lieuCAP` varchar(255) DEFAULT NULL,
  `idT` int(11) DEFAULT NULL,
  `idM` int(11) DEFAULT NULL,
  `idZone` int(11) DEFAULT NULL,
  `etatC` tinyint(1) DEFAULT NULL,
  `idSalle` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `capteurs`
--

INSERT INTO `capteurs` (`idCap`, `lieuCAP`, `idT`, `idM`, `idZone`, `etatC`, `idSalle`) VALUES
(1, 'Regie', 1, NULL, 1, NULL, 1),
(2, 'Regie', 1, NULL, 1, NULL, 1),
(3, 'Regie', 2, NULL, 1, NULL, 1),
(4, 'Regie', 2, NULL, 1, NULL, 1),
(5, 'Regie', 3, NULL, 1, NULL, 1),
(6, 'Regie', 3, NULL, 1, NULL, 1),
(7, 'Regie', 4, NULL, 1, NULL, 1),
(8, 'Regie', 4, NULL, 1, NULL, 1),
(9, 'Backstage', 1, NULL, 2, NULL, 2),
(10, 'Backstage', 2, NULL, 2, NULL, 2),
(11, 'Backstage', 3, NULL, 2, NULL, 2),
(12, 'Backstage', 4, NULL, 2, NULL, 2),
(13, 'Regie', 1, NULL, 3, NULL, 2),
(14, 'Regie', 2, NULL, 3, NULL, 2),
(15, 'Regie', 4, NULL, 3, NULL, 2),
(16, 'Backstage', 1, NULL, 5, NULL, 3),
(17, 'Backstage', 1, NULL, 5, NULL, 3),
(18, 'Backstage', 2, NULL, 5, NULL, 3),
(19, 'Backstage', 2, NULL, 5, NULL, 3),
(20, 'Regie', 1, NULL, 6, NULL, 3),
(21, 'Regie', 1, NULL, 6, NULL, 3),
(22, 'Regie', 2, NULL, 6, NULL, 3),
(23, 'Regie', 2, NULL, 6, NULL, 3),
(24, NULL, 2, 2, NULL, NULL, 4),
(25, NULL, 2, 1, NULL, 0, 5),
(26, 'Backstage', 2, NULL, 7, NULL, 6),
(27, 'Backstage', 2, NULL, 7, NULL, 6),
(28, 'Bar', 4, NULL, 8, NULL, 6),
(29, 'Bar', 4, NULL, 8, NULL, 6),
(30, NULL, 4, 2, NULL, NULL, 4),
(31, NULL, 4, 2, NULL, NULL, 4),
(32, 'Regie', 2, NULL, 9, NULL, 7),
(33, 'Regie', 2, NULL, 9, NULL, 7),
(34, 'Vestiaires', 2, NULL, 10, NULL, 8),
(35, 'Vestiaires', 2, NULL, 10, NULL, 8);

-- --------------------------------------------------------

--
-- Table structure for table `climatisation`
--

CREATE TABLE `climatisation` (
  `idClim` int(11) NOT NULL,
  `etat` varchar(50) DEFAULT NULL,
  `idB` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `creneaux`
--

CREATE TABLE `creneaux` (
  `idC` int(11) NOT NULL,
  `heureDeb` time DEFAULT NULL,
  `heureFin` time DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `idArt` int(11) DEFAULT NULL,
  `idSalle` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `creneaux`
--

INSERT INTO `creneaux` (`idC`, `heureDeb`, `heureFin`, `date`, `idArt`, `idSalle`) VALUES
(1, '19:00:00', '22:00:00', '2025-03-01 00:00:00', 1, NULL),
(5, '00:00:00', '02:00:00', '2026-05-21 00:00:00', 4, NULL),
(8, '00:00:00', '02:00:00', '2026-05-22 00:00:00', 5, NULL),
(11, '05:00:00', '07:00:00', '2026-05-24 00:00:00', 2, NULL),
(12, '22:00:00', '24:00:00', '2026-05-18 00:00:00', 3, NULL),
(14, '22:00:00', '24:00:00', '2026-05-26 00:00:00', 8, NULL),
(15, '23:00:00', '25:00:00', '2026-05-22 00:00:00', 1, NULL),
(16, '02:00:00', '04:00:00', '2026-05-21 00:00:00', 2, NULL),
(17, '23:00:00', '25:00:00', '2026-05-19 00:00:00', 5, NULL),
(18, '00:00:00', '02:00:00', '2026-05-25 00:00:00', 4, NULL),
(19, '00:00:00', '02:00:00', '2026-05-26 00:00:00', 1, NULL),
(20, '02:00:00', '04:00:00', '2026-05-25 00:00:00', 5, NULL),
(21, '00:00:00', '02:00:00', '2026-05-27 00:00:00', 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `devis`
--

CREATE TABLE `devis` (
  `idDev` int(11) NOT NULL,
  `prixTot` decimal(10,2) DEFAULT NULL,
  `dateP` datetime DEFAULT NULL,
  `idPro` int(11) DEFAULT NULL,
  `prixClient` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `devis`
--

INSERT INTO `devis` (`idDev`, `prixTot`, `dateP`, `idPro`, `prixClient`) VALUES
(1, NULL, '2026-05-18 11:38:27', 1, '0'),
(2, NULL, '2026-05-18 11:50:05', 2, '0'),
(3, NULL, '2026-05-20 09:13:17', 3, '0'),
(4, NULL, '2026-05-20 09:23:49', 4, '0'),
(5, NULL, '2026-05-20 09:33:08', 6, '0'),
(6, NULL, '2026-05-28 15:42:26', 8, '0'),
(7, '11755.60', '2026-05-30 16:46:17', 9, '14107'),
(8, '11632.98', '2026-05-30 17:19:53', 10, '13960');

-- --------------------------------------------------------

--
-- Table structure for table `donnee`
--

CREATE TABLE `donnee` (
  `idD` int(11) NOT NULL,
  `valeur` decimal(10,2) NOT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `IdCap` int(11) DEFAULT NULL,
  `Nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `donnee`
--

INSERT INTO `donnee` (`idD`, `valeur`, `date`, `IdCap`, `Nom`) VALUES
(1, '18.50', '2026-05-06 08:00:00', 1, 'Temperature'),
(2, '19.20', '2026-05-06 09:00:00', 1, 'Temperature'),
(3, '21.00', '2026-05-06 10:00:00', 1, 'Temperature'),
(4, '22.30', '2026-05-06 11:00:00', 1, 'Temperature'),
(5, '23.80', '2026-05-06 12:00:00', 1, 'Temperature'),
(6, '24.10', '2026-05-06 13:00:00', 1, 'Temperature'),
(7, '23.50', '2026-05-06 14:00:00', 1, 'Temperature'),
(8, '22.70', '2026-05-06 15:00:00', 1, 'Temperature'),
(9, '21.40', '2026-05-06 16:00:00', 1, 'Temperature'),
(9, '50.00', '2026-05-27 03:55:47', 24, 'Temperature\r\n'),
(10, '20.00', '2026-05-06 17:00:00', 1, 'Temperature'),
(10, '40.00', '2026-05-27 03:56:03', 25, 'Temperature\r\n'),
(11, '70.00', '2026-05-27 03:55:47', 24, 'Temperature\r\n'),
(12, '70.00', '2026-05-27 03:55:47', 24, 'Temperature\r\n'),
(13, '70.00', '2026-05-27 03:55:47', 24, 'Temperature\r\n'),
(14, '70.00', '2026-05-27 03:55:47', 24, 'Temperature\r\n'),
(15, '70.00', '2026-05-27 03:55:47', 24, 'Temperature\r\n'),
(16, '70.00', '2026-05-27 03:55:47', 24, 'Temperature\r\n'),
(17, '70.00', '2026-05-27 12:13:03', 24, 'Temperature'),
(18, '30.00', '2026-05-29 11:34:23', 24, 'Temperature'),
(18, '70.00', '2026-05-29 11:33:42', 24, 'Temperature'),
(19, '20.00', '2026-05-29 14:12:21', 24, 'Temperature'),
(19, '30.00', '2026-05-29 14:11:28', 24, 'Temperature'),
(20, '10.00', '2026-05-29 14:14:44', 24, 'Temperature'),
(20, '20.00', '2026-05-30 18:17:01', 24, 'Temperature'),
(21, '30.00', '2026-05-30 18:17:21', 24, 'Temperature'),
(22, '23.00', '2026-05-30 18:17:37', 24, 'Temperature'),
(25, '10.00', '2026-05-29 22:12:34', 30, 'Voltage'),
(26, '10.00', '2026-05-30 07:06:57', 30, 'Voltage'),
(27, '10.00', '2026-05-30 07:12:53', 30, 'Voltage'),
(27, '30.00', '2026-05-30 07:13:23', 30, 'Voltage'),
(27, '40.00', '2026-05-30 07:13:48', 30, 'Voltage'),
(28, '30.00', '2026-05-30 08:39:37', 30, 'Voltage'),
(29, '20.00', '2026-05-30 08:39:53', 30, 'Voltage'),
(100, '220.50', '2026-05-06 08:00:00', 2, 'Voltage'),
(101, '221.00', '2026-05-06 09:00:00', 2, 'Voltage'),
(102, '219.80', '2026-05-06 10:00:00', 2, 'Voltage'),
(103, '222.10', '2026-05-06 11:00:00', 2, 'Voltage');

--
-- Triggers `donnee`
--
DELIMITER $$
CREATE TRIGGER `trg_insert_alerte` AFTER INSERT ON `donnee` FOR EACH ROW BEGIN

    IF EXISTS (
        SELECT 1
        FROM capteurs c
        JOIN machine m ON m.idM = c.idM
        WHERE c.idCap = NEW.idCap
        AND (
            (NEW.Nom = 'Temperature' AND NEW.valeur >= m.valeurMaxTemp)
            OR
            (NEW.Nom = 'Voltage' AND NEW.valeur >= m.valeurMaxWatt)
        )

    ) THEN

        INSERT INTO alerte (ValeurA, idD, idM, date, resolu)

        SELECT
            NEW.valeur,
            NEW.idD,
            c.idM,
            NEW.date, 
            0

        FROM capteurs c
        WHERE c.idCap = NEW.idCap
        LIMIT 1;

    END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_insert_alerteM` AFTER INSERT ON `donnee` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1 FROM ouverture h
        JOIN jour j ON j.idJ = h.idJ
        JOIN salle s ON s.idSalle = h.idSalle
        JOIN capteurs c ON c.idSalle = s.idSalle
        JOIN donnee d ON d.idCap = c.idCap
        WHERE d.idD = NEW.idD
        AND NEW.valeur = 1
        AND FIELD(j.nom, 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 				'Jeudi', 'Vendredi', 'Samedi') = DAYOFWEEK(NEW.date)
        AND TIME(NEW.date) NOT BETWEEN h.horaireDeb AND h.horaireFin
    ) THEN
        INSERT INTO alerte (ValeurA, idD, idM, date)
        SELECT NEW.valeur, NEW.idD, c.idM, NEW.date
        FROM capteurs c
        JOIN donnee d ON d.idCap = c.idCap
        WHERE d.idD = NEW.idD
        LIMIT 1;

    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `jour`
--

CREATE TABLE `jour` (
  `idJ` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jour`
--

INSERT INTO `jour` (`idJ`, `nom`) VALUES
(1, 'Lundi'),
(2, 'Lundi'),
(3, 'Mercredi'),
(4, 'Mercredi'),
(5, 'Mercredi'),
(6, 'Mercredi'),
(7, 'Mercredi'),
(8, 'Lundi'),
(9, 'Lundi'),
(10, 'Mardi');

-- --------------------------------------------------------

--
-- Table structure for table `machine`
--

CREATE TABLE `machine` (
  `idM` int(11) NOT NULL,
  `valeurMaxTemp` decimal(10,2) DEFAULT NULL,
  `noM` varchar(100) DEFAULT NULL,
  `valeurMaxWatt` decimal(10,0) NOT NULL,
  `idZone` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `machine`
--

INSERT INTO `machine` (`idM`, `valeurMaxTemp`, `noM`, `valeurMaxWatt`, `idZone`) VALUES
(1, '40.00', 'guitare', '400', NULL),
(2, '40.00', 'basse', '400', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `idMess` int(11) NOT NULL,
  `texte` text,
  `idSalle` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ouverture`
--

CREATE TABLE `ouverture` (
  `idJ` int(11) NOT NULL,
  `IdSalle` int(11) NOT NULL,
  `horaireDeb` time DEFAULT NULL,
  `horaireFin` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ouverture`
--

INSERT INTO `ouverture` (`idJ`, `IdSalle`, `horaireDeb`, `horaireFin`) VALUES
(1, 1, '08:00:00', '23:00:00'),
(2, 2, '08:00:00', '23:00:00'),
(3, 0, '20:00:00', '23:15:00'),
(4, 0, '20:00:00', '23:15:00'),
(5, 0, '20:00:00', '23:15:00'),
(6, 0, '20:00:00', '23:15:00'),
(7, 3, '20:00:00', '23:15:00'),
(8, 6, '08:00:00', '09:00:00'),
(9, 7, '08:00:00', '09:00:00'),
(10, 8, '07:00:00', '15:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `porte`
--

CREATE TABLE `porte` (
  `idP` int(11) NOT NULL,
  `idSalle` int(11) DEFAULT NULL,
  `idCap` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `nomAb` varchar(25) DEFAULT NULL,
  `dateAb` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `nb_salle` int(11) DEFAULT NULL,
  `prixAbon` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `proprietaire`
--

INSERT INTO `proprietaire` (`idPro`, `nomP`, `prenomP`, `mailP`, `mdpP`, `numP`, `nomAb`, `dateAb`, `nb_salle`, `prixAbon`) VALUES
(1, 'Pascal', 'obispo', 'lep@gmail.com', 'essai', NULL, NULL, NULL, NULL, NULL),
(2, 'Ricard', 'Richard', 'leR@gmail.com', 'ravenclaw', NULL, NULL, NULL, NULL, NULL),
(3, 'Marguerite', 'Fleur', 'botanie@gmail.fr', 'pesticide', NULL, NULL, NULL, NULL, NULL),
(4, 'Marguerite', 'Fleur', 'botanie@gmail.fr', 'pesticide', NULL, NULL, NULL, NULL, NULL),
(5, 'Marguerite', 'Fleur', 'botanie@gmail.fr', 'pestocode', NULL, NULL, NULL, NULL, NULL),
(6, 'Marguerite', 'Fleur', 'botanie@gmail.fr', 'pestocode', NULL, 'classique', '2026-05-21 00:29:06', 3, 8302),
(7, 'Test', 'premier', 'test@gmail.com', 'pls', NULL, NULL, NULL, NULL, NULL),
(8, 'test', '2', 'test2@gmail.com', 'pls', NULL, 'classique', '2026-05-28 15:42:36', 1, 3256),
(9, 'test3', '3', 'test3@gmail.com', 'pls', NULL, 'complet', '2026-05-30 17:10:01', 1, 3256),
(10, 'test4', '4', 'test4@gmail.com', 'gmail.com', NULL, 'complet', '2026-05-30 17:20:55', 1, 3256);

-- --------------------------------------------------------

--
-- Table structure for table `salle`
--

CREATE TABLE `salle` (
  `idSalle` int(11) NOT NULL,
  `nomS` varchar(100) DEFAULT NULL,
  `lieus` varchar(255) DEFAULT NULL,
  `capacite` int(11) DEFAULT NULL,
  `codePoS` int(11) NOT NULL,
  `idPro` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `salle`
--

INSERT INTO `salle` (`idSalle`, `nomS`, `lieus`, `capacite`, `codePoS`, `idPro`) VALUES
(1, 'lo2lo2a', '140 chemin dela levrette', 5000, 32450, 1),
(2, 'el delulu', '140 chemin dela levrette', 5000, 32450, 2),
(3, 'coccica', '13 rue de labeille', 420, 31000, 6),
(4, 'test1', 'ramonville', 100, 3100, 7),
(5, 'test1.2', 'ramonville', 100, 3100, 7),
(6, 'jsp', '24 phhh', 100, 31500, 8),
(7, 'tes3', '24 phhh', 100, 31500, 9),
(8, 'tes4', '24 phhh', 100, 31500, 10);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `type`
--

INSERT INTO `type` (`idT`, `nomT`, `prixT`, `valeurT`, `etatT`) VALUES
(1, 'Bouton', '61.31', NULL, 0),
(2, 'Multi-Sensor', '85.99', 'luminosite et temperature', 0),
(3, 'Porte', '25.45', 'mouvement', 0),
(4, 'Prise Intelligente', '11.69', 'Voltage', 0);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_prixsalle`
-- (See below for the actual view)
--
CREATE TABLE `v_prixsalle` (
`idDev` int(11)
,`idSalle` int(11)
,`prixAchS` decimal(32,2)
,`prixInstallation` int(4)
,`prixAbon` int(11)
,`prixTotal` decimal(34,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `zone`
--

CREATE TABLE `zone` (
  `idZone` int(11) NOT NULL,
  `nomZone` varchar(100) NOT NULL,
  `valMaxtemp` int(11) DEFAULT NULL,
  `idSalle` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `zone`
--

INSERT INTO `zone` (`idZone`, `nomZone`, `valMaxtemp`, `idSalle`) VALUES
(1, 'Regie', NULL, 1),
(2, 'Backstage', NULL, 2),
(3, 'Regie', NULL, 2),
(5, 'Backstage', NULL, 3),
(6, 'Regie', NULL, 3),
(7, 'Backstage', NULL, 6),
(8, 'Bar', NULL, 6),
(9, 'Regie', NULL, 7),
(10, 'Vestiaires', NULL, 8);

-- --------------------------------------------------------

--
-- Structure for view `v_prixsalle`
--
DROP TABLE IF EXISTS `v_prixsalle`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_prixsalle`  AS  select `a`.`idDev` AS `idDev`,`a`.`idSalle` AS `idSalle`,sum(`a`.`prixAct`) AS `prixAchS`,8205 AS `prixInstallation`,`p`.`prixAbon` AS `prixAbon`,((sum(`a`.`prixAct`) + 8205) + `p`.`prixAbon`) AS `prixTotal` from ((`acheter` `a` join `salle` `s` on((`a`.`idSalle` = `s`.`idSalle`))) join `proprietaire` `p` on((`s`.`idPro` = `p`.`idPro`))) group by `a`.`idDev`,`a`.`idSalle` ;

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
-- Indexes for table `alerte`
--
ALTER TABLE `alerte`
  ADD PRIMARY KEY (`idA`),
  ADD KEY `fk_Al_idD` (`idD`),
  ADD KEY `fk_Al_idM` (`idM`);

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
  ADD KEY `fk_bout_idSalle` (`idSalle`);

--
-- Indexes for table `capteurs`
--
ALTER TABLE `capteurs`
  ADD PRIMARY KEY (`idCap`),
  ADD KEY `fk_cap_idT` (`idT`),
  ADD KEY `fk_cap_idZone` (`idZone`),
  ADD KEY `fk_cap_idM` (`idM`),
  ADD KEY `fk_cap_idSalle` (`idSalle`);

--
-- Indexes for table `climatisation`
--
ALTER TABLE `climatisation`
  ADD PRIMARY KEY (`idClim`),
  ADD KEY `fk_clim_bout` (`idB`);

--
-- Indexes for table `creneaux`
--
ALTER TABLE `creneaux`
  ADD PRIMARY KEY (`idC`),
  ADD KEY `fk_creneaux_art` (`idArt`),
  ADD KEY `fk_creneaux_idsalle` (`idSalle`);

--
-- Indexes for table `devis`
--
ALTER TABLE `devis`
  ADD PRIMARY KEY (`idDev`),
  ADD KEY `fk_dev_idPro` (`idPro`);

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
  ADD PRIMARY KEY (`idM`),
  ADD KEY `fk_idZone` (`idZone`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`idMess`),
  ADD KEY `fk_mSalle` (`idSalle`);

--
-- Indexes for table `ouverture`
--
ALTER TABLE `ouverture`
  ADD PRIMARY KEY (`idJ`,`IdSalle`),
  ADD KEY `fk_salle` (`IdSalle`);

--
-- Indexes for table `porte`
--
ALTER TABLE `porte`
  ADD PRIMARY KEY (`idP`),
  ADD KEY `fk_pidSalle` (`idSalle`),
  ADD KEY `fk_pidCap` (`idCap`);

--
-- Indexes for table `proprietaire`
--
ALTER TABLE `proprietaire`
  ADD PRIMARY KEY (`idPro`);

--
-- Indexes for table `salle`
--
ALTER TABLE `salle`
  ADD PRIMARY KEY (`idSalle`),
  ADD KEY `fk_idPro` (`idPro`);

--
-- Indexes for table `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`idT`);

--
-- Indexes for table `zone`
--
ALTER TABLE `zone`
  ADD PRIMARY KEY (`idZone`),
  ADD KEY `fk_zone_salle` (`idSalle`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alerte`
--
ALTER TABLE `alerte`
  MODIFY `idA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `artiste`
--
ALTER TABLE `artiste`
  MODIFY `idArt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `bouton`
--
ALTER TABLE `bouton`
  MODIFY `idB` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `capteurs`
--
ALTER TABLE `capteurs`
  MODIFY `idCap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- AUTO_INCREMENT for table `climatisation`
--
ALTER TABLE `climatisation`
  MODIFY `idClim` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `creneaux`
--
ALTER TABLE `creneaux`
  MODIFY `idC` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `devis`
--
ALTER TABLE `devis`
  MODIFY `idDev` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `jour`
--
ALTER TABLE `jour`
  MODIFY `idJ` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `machine`
--
ALTER TABLE `machine`
  MODIFY `idM` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
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
  MODIFY `idPro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `salle`
--
ALTER TABLE `salle`
  MODIFY `idSalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `type`
--
ALTER TABLE `type`
  MODIFY `idT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `zone`
--
ALTER TABLE `zone`
  MODIFY `idZone` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
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

--
-- Constraints for table `alerte`
--
ALTER TABLE `alerte`
  ADD CONSTRAINT `fk_Al_idD` FOREIGN KEY (`idD`) REFERENCES `donnee` (`idD`),
  ADD CONSTRAINT `fk_Al_idM` FOREIGN KEY (`idM`) REFERENCES `machine` (`idM`);

--
-- Constraints for table `bouton`
--
ALTER TABLE `bouton`
  ADD CONSTRAINT `fk_bout_idSalle` FOREIGN KEY (`idSalle`) REFERENCES `salle` (`idSalle`);

--
-- Constraints for table `capteurs`
--
ALTER TABLE `capteurs`
  ADD CONSTRAINT `fk_cap_idM` FOREIGN KEY (`idM`) REFERENCES `machine` (`idM`),
  ADD CONSTRAINT `fk_cap_idSalle` FOREIGN KEY (`idSalle`) REFERENCES `salle` (`idSalle`),
  ADD CONSTRAINT `fk_cap_idT` FOREIGN KEY (`idT`) REFERENCES `type` (`idT`),
  ADD CONSTRAINT `fk_cap_idZone` FOREIGN KEY (`idZone`) REFERENCES `zone` (`idZone`);

--
-- Constraints for table `climatisation`
--
ALTER TABLE `climatisation`
  ADD CONSTRAINT `fk_clim_bout` FOREIGN KEY (`idB`) REFERENCES `bouton` (`idB`);

--
-- Constraints for table `creneaux`
--
ALTER TABLE `creneaux`
  ADD CONSTRAINT `fk_creneaux_art` FOREIGN KEY (`idArt`) REFERENCES `artiste` (`idArt`),
  ADD CONSTRAINT `fk_creneaux_idsalle` FOREIGN KEY (`idSalle`) REFERENCES `salle` (`idSalle`);

--
-- Constraints for table `devis`
--
ALTER TABLE `devis`
  ADD CONSTRAINT `fk_dev_idPro` FOREIGN KEY (`idPro`) REFERENCES `proprietaire` (`idPro`);

--
-- Constraints for table `donnee`
--
ALTER TABLE `donnee`
  ADD CONSTRAINT `fk_donnee_capteur` FOREIGN KEY (`IdCap`) REFERENCES `capteurs` (`idCap`);

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fk_mSalle` FOREIGN KEY (`idSalle`) REFERENCES `salle` (`idSalle`);

--
-- Constraints for table `porte`
--
ALTER TABLE `porte`
  ADD CONSTRAINT `fk_pidCap` FOREIGN KEY (`idCap`) REFERENCES `capteurs` (`idCap`),
  ADD CONSTRAINT `fk_pidSalle` FOREIGN KEY (`idSalle`) REFERENCES `salle` (`idSalle`);

--
-- Constraints for table `salle`
--
ALTER TABLE `salle`
  ADD CONSTRAINT `fk_idPro` FOREIGN KEY (`idPro`) REFERENCES `proprietaire` (`idPro`);

--
-- Constraints for table `zone`
--
ALTER TABLE `zone`
  ADD CONSTRAINT `fk_zone_salle` FOREIGN KEY (`idSalle`) REFERENCES `salle` (`idSalle`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `ev_ajDevis` ON SCHEDULE EVERY 1 MINUTE STARTS '2026-05-13 10:13:00' ENDS '2026-05-31 00:00:00' ON COMPLETION PRESERVE ENABLE DO INSERT INTO devis (idPro, dateP)
SELECT p.idPro, DATE_FORMAT(NOW(), '%Y-%m-01')
FROM proprietaire p
WHERE EXISTS (
    SELECT 1 FROM devis d
    WHERE d.idPro = p.idPro
)
AND NOT EXISTS (
    SELECT 1 FROM devis d
    WHERE d.idPro = p.idPro
    AND YEAR(d.dateP) = YEAR(NOW())
    AND MONTH(d.dateP) = MONTH(NOW())
)$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
