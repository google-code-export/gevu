-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Ven 22 Octobre 2010 à 18:26
-- Version du serveur: 5.1.30
-- Version de PHP: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `gevu_solus`
--

-- --------------------------------------------------------

--
-- Structure de la table `gevu_metiers`
--

DROP TABLE IF EXISTS `gevu_metiers`;
CREATE TABLE IF NOT EXISTS `gevu_metiers` (
  `id_metier` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_metier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Contenu de la table `gevu_metiers`
--


-- --------------------------------------------------------

--
-- Structure de la table `gevu_metiersxsolutions`
--

DROP TABLE IF EXISTS `gevu_metiersxsolutions`;
CREATE TABLE IF NOT EXISTS `gevu_metiersxsolutions` (
  `id_solution` int(11) NOT NULL,
  `id_metier` int(11) NOT NULL,
  PRIMARY KEY (`id_solution`,`id_metier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=FIXED;

--
-- Contenu de la table `gevu_metiersxsolutions`
--


-- --------------------------------------------------------

--
-- Structure de la table `gevu_solutions`
--

DROP TABLE IF EXISTS `gevu_solutions`;
CREATE TABLE IF NOT EXISTS `gevu_solutions` (
  `id_solution` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(255) COLLATE utf8_bin NOT NULL,
  `lib` varchar(300) COLLATE utf8_bin NOT NULL,
  `id_type_solution` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_solution`),
  KEY `id_type_solution` (`id_type_solution`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Contenu de la table `gevu_solutions`
--

INSERT INTO `gevu_solutions` (`id_solution`, `ref`, `lib`, `id_type_solution`, `maj`) VALUES
(1, '', 'bidule', 1, '0000-00-00 00:00:00'),
(2, 'qvqd', 'bidule', 2, '2010-10-18 11:55:43');

-- --------------------------------------------------------

--
-- Structure de la table `gevu_typesxsolutions`
--

DROP TABLE IF EXISTS `gevu_typesxsolutions`;
CREATE TABLE IF NOT EXISTS `gevu_typesxsolutions` (
  `id_type_solution` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_type_solution`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=4 ;

--
-- Contenu de la table `gevu_typesxsolutions`
--

INSERT INTO `gevu_typesxsolutions` (`id_type_solution`, `lib`) VALUES
(1, 'Humaine'),
(2, 'Opérationnelle'),
(3, 'Technique');
