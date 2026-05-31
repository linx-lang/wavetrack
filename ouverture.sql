-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  Dim 31 mai 2026 à 21:31
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
-- Structure de la table `ouverture`
--

CREATE TABLE `ouverture` (
  `idJ` int(11) NOT NULL,
  `IdSalle` int(11) NOT NULL,
  `horaireDeb` time DEFAULT NULL,
  `horaireFin` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `ouverture`
--

INSERT INTO `ouverture` (`idJ`, `IdSalle`, `horaireDeb`, `horaireFin`) VALUES
(1, 1, '08:00:00', '23:00:00'),
(2, 4, '08:00:00', '23:00:00'),
(3, 4, '12:00:00', '23:15:00'),
(4, 4, '20:00:00', '23:15:00'),
(5, 4, '20:00:00', '23:15:00'),
(6, 0, '20:00:00', '23:15:00'),
(7, 3, '20:00:00', '23:15:00'),
(8, 6, '08:00:00', '09:00:00'),
(9, 7, '08:00:00', '09:00:00'),
(10, 8, '07:00:00', '15:00:00');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `ouverture`
--
ALTER TABLE `ouverture`
  ADD PRIMARY KEY (`idJ`,`IdSalle`),
  ADD KEY `fk_salle` (`IdSalle`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
