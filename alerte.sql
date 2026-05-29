-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  ven. 29 mai 2026 à 16:04
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
-- Structure de la table `alerte`
--

CREATE TABLE `alerte` (
  `idA` int(11) NOT NULL,
  `date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `idD` int(11) DEFAULT NULL,
  `idM` int(11) DEFAULT NULL,
  `ValeurA` decimal(11,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `alerte`
--

INSERT INTO `alerte` (`idA`, `date`, `idD`, `idM`, `ValeurA`) VALUES
(1, '2026-05-01 08:12:00', 3, 1, '28'),
(2, '2026-05-01 09:45:00', 3, 1, '29'),
(3, '2026-05-01 11:20:00', 3, 1, '30'),
(4, '2026-05-02 07:55:00', 3, 1, '28'),
(5, '2026-05-02 10:10:00', 3, 1, '31'),
(6, '2026-05-02 14:22:00', 3, 1, '33'),
(7, '2026-05-03 09:00:00', 3, 1, '29'),
(8, '2026-05-03 12:30:00', 3, 1, '30'),
(9, '2026-05-01 08:00:00', 4, 2, '65'),
(10, '2026-05-01 13:15:00', 4, 2, '70'),
(11, '2026-05-02 09:40:00', 4, 2, '72'),
(12, '2026-05-02 16:10:00', 4, 2, '75'),
(13, '2026-05-03 10:05:00', 4, 2, '69'),
(14, '2026-05-01 07:30:00', 5, 3, '40'),
(15, '2026-05-01 11:50:00', 5, 3, '42'),
(16, '2026-05-02 08:20:00', 5, 3, '45'),
(17, '2026-05-02 15:00:00', 5, 3, '48'),
(18, '2026-05-03 09:10:00', 5, 3, '44');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `alerte`
--
ALTER TABLE `alerte`
  ADD PRIMARY KEY (`idA`),
  ADD KEY `fk_Al_idD` (`idD`),
  ADD KEY `fk_Al_idM` (`idM`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `alerte`
--
ALTER TABLE `alerte`
  MODIFY `idA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `alerte`
--
ALTER TABLE `alerte`
  ADD CONSTRAINT `fk_Al_idD` FOREIGN KEY (`idD`) REFERENCES `donnee` (`idD`),
  ADD CONSTRAINT `fk_Al_idM` FOREIGN KEY (`idM`) REFERENCES `machine` (`idM`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
