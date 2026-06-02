-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2026 at 01:57 AM
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
('171.98', NULL, 2, 8, 2, 8),
('257.97', NULL, 3, 9, 2, 9),
('183.93', NULL, 3, 11, 1, 11),
('171.98', NULL, 2, 11, 2, 11),
('50.90', NULL, 2, 11, 3, 11),
('122.62', NULL, 2, 12, 1, 14),
('85.99', NULL, 1, 12, 2, 14),
('85.99', NULL, 1, 13, 2, 16),
('25.45', NULL, 1, 13, 3, 16),
('11.69', NULL, 1, 13, 4, 16),
('122.62', NULL, 2, 14, 1, 17),
('50.90', NULL, 2, 14, 3, 17),
('85.99', NULL, 1, 15, 2, 18);

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
(21, '2026-06-02 23:50:37', 3, 2, '100', 0),
(22, '2026-06-03 00:02:35', 4, 2, '80', 0),
(23, '2026-06-03 00:03:18', 7, 2, '80', 0);

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
(8, 'Jack Stauber'),
(9, 'Fairuz'),
(10, 'Polyphia'),
(11, 'Eminem');

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
(2, '0', '0.00', 7),
(3, '0', '0.00', 11),
(4, '0', '0.00', 11),
(5, '0', '0.00', 11),
(6, '0', '0.00', 14),
(7, '0', '0.00', 4),
(8, '0', '0.00', 17),
(9, '0', '0.00', 17);

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
(24, NULL, 2, 2, NULL, NULL, 4),
(25, NULL, 4, 2, NULL, 0, 5),
(28, 'Bar', 4, NULL, 8, NULL, 6),
(29, 'Bar', 4, 1, NULL, NULL, 4),
(54, 'Bar', 2, 1, NULL, NULL, 4),
(57, NULL, 3, 2, NULL, NULL, 4);

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
(1, '19:00:00', '22:00:00', '2025-03-01 00:00:00', 1, 4),
(5, '00:00:00', '02:00:00', '2026-05-21 00:00:00', 4, 4),
(8, '00:00:00', '02:00:00', '2026-05-22 00:00:00', 5, 4),
(11, '05:00:00', '07:00:00', '2026-05-24 00:00:00', 2, 4),
(12, '22:00:00', '24:00:00', '2026-05-18 00:00:00', 3, 4),
(14, '22:00:00', '24:00:00', '2026-05-26 00:00:00', 8, 4),
(15, '23:00:00', '25:00:00', '2026-05-22 00:00:00', 1, 4),
(16, '02:00:00', '04:00:00', '2026-05-21 00:00:00', 2, 4),
(17, '23:00:00', '25:00:00', '2026-05-19 00:00:00', 5, 4),
(18, '00:00:00', '02:00:00', '2026-05-25 00:00:00', 4, 4),
(19, '00:00:00', '02:00:00', '2026-05-26 00:00:00', 1, 4),
(20, '02:00:00', '04:00:00', '2026-05-25 00:00:00', 5, 4),
(21, '00:00:00', '02:00:00', '2026-05-27 00:00:00', 3, 4),
(22, '10:00:00', '12:00:00', '2026-06-01 00:00:00', 9, 4),
(24, '11:00:00', '13:00:00', '2026-06-02 00:00:00', 10, 4);

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
(9, '11718.97', '2026-05-31 21:53:34', 11, '14063', '1172'),
(10, NULL, '2026-06-01 11:06:35', 7, NULL, NULL),
(11, NULL, '2026-06-01 11:10:33', 13, '0', '0'),
(12, '11669.61', '2026-06-01 11:47:40', 14, '14004', '1167'),
(13, '11584.13', '2026-06-02 18:58:41', 15, '13901', '-2984'),
(14, '11634.52', '2026-06-02 19:24:53', 16, NULL, NULL),
(15, '11546.99', '2026-06-02 19:29:25', 17, '13856', '8562');

-- --------------------------------------------------------

--
-- Table structure for table `donnee`
--

CREATE TABLE `donnee` (
  `idD` int(11) NOT NULL,
  `valeur` decimal(10,2) NOT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `IdCap` int(11) DEFAULT NULL,
  `Nom` varchar(100) NOT NULL,
  `idB` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `donnee`
--

INSERT INTO `donnee` (`idD`, `valeur`, `date`, `IdCap`, `Nom`, `idB`) VALUES
(1, '100.00', '2026-06-02 23:50:37', 14, 'Temperature', NULL),
(2, '90.00', '2026-06-02 23:56:10', 14, 'Temperature', NULL),
(3, '100.00', '2026-06-02 23:50:37', 24, 'Temperature', NULL),
(4, '80.00', '2026-06-03 00:02:35', 24, 'Temperature', NULL),
(5, '30.00', '2026-06-03 00:03:30', 24, 'Temperature', NULL),
(5, '50.00', '2026-06-03 00:03:59', 25, 'Voltage', NULL),
(5, '70.00', '2026-06-03 00:04:38', 25, 'Voltage', NULL),
(7, '80.00', '2026-06-03 00:03:18', 24, 'Temperature', NULL);

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
        AND NEW.valeur = 1 AND NEW.Nom='Porte'
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
(2, 'Mardi'),
(3, 'Mercredi'),
(4, 'Jeudi'),
(5, 'Vendredi'),
(6, 'Samedi'),
(7, 'Dimanche');

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
(2, '40.00', 'basse', '400', NULL),
(3, '150.00', 'Multi-Sensor - guitar', '150', NULL);

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
(1, 11, '08:00:00', '20:00:00'),
(1, 12, '08:00:00', '20:00:00'),
(1, 13, '08:00:00', '20:00:00'),
(1, 14, '08:00:00', '20:00:00'),
(1, 15, '08:00:00', '20:00:00'),
(1, 16, '08:00:00', '20:00:00'),
(1, 17, '08:00:00', '20:00:00'),
(1, 18, '08:00:00', '20:00:00'),
(2, 4, '08:00:00', '20:00:00'),
(2, 10, '07:00:00', '15:00:00'),
(2, 16, '07:00:00', '15:00:00'),
(3, 4, '12:00:00', '23:15:00'),
(3, 10, '17:00:00', '23:00:00'),
(3, 16, '17:00:00', '23:00:00'),
(4, 4, '20:00:00', '23:15:00'),
(4, 10, '18:00:00', '23:00:00'),
(4, 16, '18:00:00', '23:00:00'),
(5, 4, '20:00:00', '23:15:00'),
(6, 0, '20:00:00', '23:15:00'),
(7, 3, '20:00:00', '23:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `porte`
--

CREATE TABLE `porte` (
  `idP` int(11) NOT NULL,
  `idSalle` int(11) DEFAULT NULL,
  `idCap` int(11) DEFAULT NULL,
  `idZone` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `porte`
--

INSERT INTO `porte` (`idP`, `idSalle`, `idCap`, `idZone`) VALUES
(2, 11, NULL, 16),
(3, 11, NULL, 17),
(4, 14, NULL, 18),
(7, 4, 57, 20);

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
(10, 'test4', '4', 'test4@gmail.com', 'gmail.com', NULL, 'complet', '2026-05-30 17:20:55', 1, 3256),
(11, 'test5', '5', 'test5@gmail.com', 'pls', NULL, 'complet', '2026-05-31 21:53:45', 1, 3256),
(12, 'test6', '6', 'test6@gmail.com', 'pls', NULL, NULL, NULL, NULL, NULL),
(13, 'bistro', 'pascal', 'pascal@gmail.com', 'pls', NULL, NULL, NULL, NULL, NULL),
(14, 'test', 'test7', 't@gmail.com', 'pls', NULL, 'classique', '2026-06-01 11:47:45', 1, 3256),
(15, 'test', '10', 'test10@gmail.com', 'pls', NULL, 'complet', '2026-06-02 19:04:39', 1, 3256),
(16, 'test', '11', 'test11@gmail.com', 'pls', NULL, 'classique', '2026-06-02 19:25:01', 1, 3256),
(17, 'bla', 'blas', 'blas@gmail.cpom', 'ajkdf', NULL, 'complet', '2026-06-02 19:34:22', 1, 3256);

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
(8, 'tes4', '24 phhh', 100, 31500, 10),
(9, 'tes5', '24 phhh', 100, 31500, 11),
(10, 'tes6', '24 phhh', 100, 31500, 12),
(11, 'caillous', '18jdka', 100, 31500, 13),
(12, 'merde', '18jdka', 100, 31500, 14),
(13, 'merde', '18jdka', 100, 31500, 14),
(14, 'merde', '18jdka', 100, 31500, 14),
(15, 'merde', '18jdka', 100, 31500, 15),
(16, 'tes6', '24 phhh', 100, 31500, 15),
(17, 'merde', '18jdka', 100, 31500, 16),
(18, 'merde', '18jdka', 100, 31500, 17);

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
,`prixMois` decimal(36,4)
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
(10, 'Vestiaires', NULL, 8),
(11, 'Regie', NULL, 9),
(12, 'Backstage', NULL, 10),
(13, 'Regie', NULL, 10),
(14, 'Scene', NULL, 11),
(15, 'Regie', NULL, 11),
(16, 'Regie', NULL, 11),
(17, 'Bar', NULL, 11),
(18, 'Scene', NULL, 14),
(19, 'Bar', NULL, 16),
(20, 'Bar', NULL, 17),
(21, 'Backstage', NULL, 18);

-- --------------------------------------------------------

--
-- Structure for view `v_prixsalle`
--
DROP TABLE IF EXISTS `v_prixsalle`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_prixsalle`  AS  select `a`.`idDev` AS `idDev`,`a`.`idSalle` AS `idSalle`,sum(`a`.`prixAct`) AS `prixAchS`,8205 AS `prixInstallation`,`p`.`prixAbon` AS `prixAbon`,((sum(`a`.`prixAct`) + 8205) + `p`.`prixAbon`) AS `prixTotal`,((sum(`a`.`prixAct`) + 8205) + (`p`.`prixAbon` / 12)) AS `prixMois` from ((`acheter` `a` join `salle` `s` on((`a`.`idSalle` = `s`.`idSalle`))) join `proprietaire` `p` on((`s`.`idPro` = `p`.`idPro`))) group by `a`.`idDev`,`a`.`idSalle` ;

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
  ADD KEY `fk_pidCap` (`idCap`),
  ADD KEY `fk_pidZone` (`idZone`);

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
  MODIFY `idA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `artiste`
--
ALTER TABLE `artiste`
  MODIFY `idArt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `bouton`
--
ALTER TABLE `bouton`
  MODIFY `idB` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `capteurs`
--
ALTER TABLE `capteurs`
  MODIFY `idCap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
--
-- AUTO_INCREMENT for table `climatisation`
--
ALTER TABLE `climatisation`
  MODIFY `idClim` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `creneaux`
--
ALTER TABLE `creneaux`
  MODIFY `idC` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `devis`
--
ALTER TABLE `devis`
  MODIFY `idDev` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `jour`
--
ALTER TABLE `jour`
  MODIFY `idJ` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `machine`
--
ALTER TABLE `machine`
  MODIFY `idM` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `idMess` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `porte`
--
ALTER TABLE `porte`
  MODIFY `idP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `proprietaire`
--
ALTER TABLE `proprietaire`
  MODIFY `idPro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `salle`
--
ALTER TABLE `salle`
  MODIFY `idSalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `type`
--
ALTER TABLE `type`
  MODIFY `idT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `zone`
--
ALTER TABLE `zone`
  MODIFY `idZone` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
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
  ADD CONSTRAINT `fk_pidSalle` FOREIGN KEY (`idSalle`) REFERENCES `salle` (`idSalle`),
  ADD CONSTRAINT `fk_pidZone` FOREIGN KEY (`idZone`) REFERENCES `zone` (`idZone`);

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
CREATE DEFINER=`root`@`localhost` EVENT `ev_ajDevis` ON SCHEDULE EVERY 1 MINUTE STARTS '2026-05-13 10:13:00' ENDS '2026-05-31 00:00:00' ON COMPLETION PRESERVE DISABLE DO INSERT INTO devis (idPro, dateP,prixMois)
SELECT p.idPro, DATE_FORMAT(NOW(), '%Y-%m-01'),p.prixAbon/12
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
