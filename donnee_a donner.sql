-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  jeu. 28 mai 2026 à 16:06
-- Version du serveur :  5.7.17
-- Version de PHP :  5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `projetsql`
--

-- --------------------------------------------------------

--
-- Structure de la table `donnee`
--

CREATE TABLE `donnee` (
  `idD` int(11) NOT NULL,
  `valeur` decimal(10,2) NOT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `IdCap` int(11) DEFAULT NULL,
  `Nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `donnee`
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
(10, '20.00', '2026-05-06 17:00:00', 1, 'Temperature'),
(100, '220.50', '2026-05-06 08:00:00', 2, 'Voltage'),
(101, '221.00', '2026-05-06 09:00:00', 2, 'Voltage'),
(102, '219.80', '2026-05-06 10:00:00', 2, 'Voltage'),
(103, '222.10', '2026-05-06 11:00:00', 2, 'Voltage');

--
-- Déclencheurs `donnee`
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

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `donnee`
--
ALTER TABLE `donnee`
  ADD PRIMARY KEY (`idD`,`valeur`),
  ADD KEY `fk_donnee_capteur` (`IdCap`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `donnee`
--
ALTER TABLE `donnee`
  ADD CONSTRAINT `fk_donnee_capteur` FOREIGN KEY (`IdCap`) REFERENCES `capteurs` (`idCap`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
