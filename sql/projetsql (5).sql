-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 10:19 AM
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
  `ValeurA` decimal(11,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `artiste`
--

CREATE TABLE `artiste` (
  `idArt` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Table structure for table `devis`
--

CREATE TABLE `devis` (
  `idDev` int(11) NOT NULL,
  `prixTot` decimal(10,2) DEFAULT NULL,
  `dateP` datetime DEFAULT NULL,
  `idPro` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
-- Triggers `donnee`
--
DELIMITER $$
CREATE TRIGGER `trg_insert_alerte` AFTER INSERT ON `donnee` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1 FROM machine m
        JOIN capteurs c ON c.idM = m.idM
        JOIN donnee d ON d.idCap=d.IdCap
        WHERE d.idD = NEW.idD
        AND (
            (NEW.nom = 'Temperature' AND NEW.valeur >= m.valeurMaxTemp)
            OR
            (NEW.nom = 'Voltage' AND NEW.valeur >= m.valeurMaxWatt)
        )
    ) THEN
        INSERT INTO alerte (ValeurA, idD, idM, date)
        SELECT NEW.valeur, NEW.idD, m.idM, NEW.date
        FROM machine m
        INNER JOIN capteurs c ON c.idM = m.idM
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
-- Triggers `proprietaire`
--
DELIMITER $$
CREATE TRIGGER `trg_update_prixAbon` BEFORE INSERT ON `proprietaire` FOR EACH ROW IF NEW.nomAb = 'classique' AND NEW.nb_salle = 1 THEN
SET NEW.prixAbon = 30;
    
ELSEIF NEW.nomAb = 'classique' AND NEW.nb_salle = 2 THEN
SET NEW.prixAbon = 45;
    
ELSEIF NEW.nomAb = 'classique' AND NEW.nb_salle > 2 THEN
SET NEW.prixAbon = 60;

ELSEIF NEW.nomAB='complet' AND NEW.nb_salle= 1 THEN 
SET NEW.prixAbon= 40;

ELSEIF NEW.nomAB='complet' AND NEW.nb_salle= 2 THEN 
SET NEW.prixAbon= 55 ;   

ELSEIF NEW.nomAB='complet' AND NEW.nb_salle > 2 THEN 
SET NEW.prixAbon= 70;

ELSEIF NEW.nomAB='expert' AND NEW.nb_salle= 1 THEN 
SET NEW.prixAbon= 50;

ELSEIF NEW.nomAB='expert' AND NEW.nb_salle= 2 THEN 
SET NEW.prixAbon= 65;

ELSEIF NEW.nomAB='expert' AND NEW.nb_salle > 2 THEN 
SET NEW.prixAbon= 80;



    
END IF
$$
DELIMITER ;

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
-- Triggers `salle`
--
DELIMITER $$
CREATE TRIGGER `trg_insert_nb_salle` BEFORE INSERT ON `salle` FOR EACH ROW BEGIN
    UPDATE proprietaire
    SET nb_salle = nb_salle + 1
    WHERE idPro = NEW.idPro;
END
$$
DELIMITER ;

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
  MODIFY `idDev` int(11) NOT NULL AUTO_INCREMENT;
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
  MODIFY `idPro` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `salle`
--
ALTER TABLE `salle`
  MODIFY `idSalle` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `type`
--
ALTER TABLE `type`
  MODIFY `idT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `zone`
--
ALTER TABLE `zone`
  MODIFY `idZone` int(11) NOT NULL AUTO_INCREMENT;
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
