-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  jeu. 28 mai 2026 à 16:05
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
-- Structure de la table `creneaux`
--

CREATE TABLE `creneaux` (
  `idC` int(11) NOT NULL,
  `heureDeb` time DEFAULT NULL,
  `heureFin` time DEFAULT NULL,
  `date` date DEFAULT NULL,
  `idArt` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `creneaux`
--

INSERT INTO `creneaux` (`idC`, `heureDeb`, `heureFin`, `date`, `idArt`) VALUES
(1, '19:00:00', '22:00:00', '2025-03-01', 1),
(18, '00:00:00', '02:00:00', '2026-05-25', 4),
(11, '05:00:00', '07:00:00', '2026-05-24', 2),
(14, '22:00:00', '24:00:00', '2026-05-26', 8),
(5, '00:00:00', '02:00:00', '2026-05-21', 4),
(12, '22:00:00', '24:00:00', '2026-05-18', 3),
(8, '00:00:00', '02:00:00', '2026-05-22', 5),
(15, '23:00:00', '25:00:00', '2026-05-22', 1),
(16, '02:00:00', '04:00:00', '2026-05-21', 2),
(17, '23:00:00', '25:00:00', '2026-05-19', 5),
(19, '00:00:00', '02:00:00', '2026-05-26', 1),
(20, '02:00:00', '04:00:00', '2026-05-25', 5),
(21, '00:00:00', '02:00:00', '2026-05-27', 3);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `creneaux`
--
ALTER TABLE `creneaux`
  ADD PRIMARY KEY (`idC`),
  ADD KEY `idArt` (`idArt`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `creneaux`
--
ALTER TABLE `creneaux`
  MODIFY `idC` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
