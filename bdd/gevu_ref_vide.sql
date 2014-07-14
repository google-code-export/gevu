-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 14 Juillet 2014 à 06:11
-- Version du serveur: 5.1.44
-- Version de PHP: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `gevu_ref`
--

-- --------------------------------------------------------

--
-- Structure de la table `gevu_antennes`
--

DROP TABLE IF EXISTS `gevu_antennes`;
CREATE TABLE IF NOT EXISTS `gevu_antennes` (
  `id_antenne` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `ref` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_antenne`),
  UNIQUE KEY `id_lieu` (`id_lieu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_batiments`
--

DROP TABLE IF EXISTS `gevu_batiments`;
CREATE TABLE IF NOT EXISTS `gevu_batiments` (
  `id_batiment` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `ref` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_proprietaire` int(11) DEFAULT NULL,
  `contact_delegataire` int(11) DEFAULT NULL,
  `contact_gardien` int(11) DEFAULT NULL,
  `horaires_gardien` text COLLATE utf8_unicode_ci NOT NULL,
  `horaires_batiment` text COLLATE utf8_unicode_ci NOT NULL,
  `superficie_parcelle` int(11) NOT NULL,
  `superficie_batiment` int(11) NOT NULL,
  `date_achevement` date NOT NULL,
  `date_depot_permis` date NOT NULL,
  `date_reha` date NOT NULL,
  `reponse_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_3` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_4` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_5` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_6` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_7` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_8` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_9` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_10` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_11` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_12` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_13` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_14` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_15` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_batiment`),
  UNIQUE KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_chainesdeplacements`
--

DROP TABLE IF EXISTS `gevu_chainesdeplacements`;
CREATE TABLE IF NOT EXISTS `gevu_chainesdeplacements` (
  `id_chainedepla` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `params` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_chainedepla`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_contacts`
--

DROP TABLE IF EXISTS `gevu_contacts`;
CREATE TABLE IF NOT EXISTS `gevu_contacts` (
  `id_contact` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_bin NOT NULL,
  `prenom` varchar(255) COLLATE utf8_bin NOT NULL,
  `fixe` varchar(32) COLLATE utf8_bin NOT NULL,
  `mobile` varchar(32) COLLATE utf8_bin NOT NULL,
  `fax` varchar(32) COLLATE utf8_bin NOT NULL,
  `mail` varchar(32) COLLATE utf8_bin NOT NULL,
  `civilite` varchar(10) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_contact`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_contactsxantennes`
--

DROP TABLE IF EXISTS `gevu_contactsxantennes`;
CREATE TABLE IF NOT EXISTS `gevu_contactsxantennes` (
  `id_contact` int(11) NOT NULL,
  `id_antenne` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_contactsxentreprises`
--

DROP TABLE IF EXISTS `gevu_contactsxentreprises`;
CREATE TABLE IF NOT EXISTS `gevu_contactsxentreprises` (
  `id_contact` int(11) NOT NULL,
  `id_entreprise` int(11) NOT NULL,
  PRIMARY KEY (`id_contact`,`id_entreprise`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_couts`
--

DROP TABLE IF EXISTS `gevu_couts`;
CREATE TABLE IF NOT EXISTS `gevu_couts` (
  `id_cout` int(11) NOT NULL AUTO_INCREMENT,
  `id_instant` int(11) NOT NULL,
  `unite` int(11) NOT NULL,
  `metre_lineaire` int(11) NOT NULL,
  `metre_carre` int(11) NOT NULL,
  `achat` int(11) NOT NULL,
  `pose` int(11) NOT NULL,
  PRIMARY KEY (`id_cout`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED AUTO_INCREMENT=819 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_criteres`
--

DROP TABLE IF EXISTS `gevu_criteres`;
CREATE TABLE IF NOT EXISTS `gevu_criteres` (
  `id_critere` int(11) NOT NULL AUTO_INCREMENT,
  `id_type_controle` int(11) NOT NULL,
  `ref` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `handicateur_moteur` int(11) NOT NULL,
  `handicateur_auditif` int(11) NOT NULL,
  `handicateur_visuel` int(11) NOT NULL,
  `handicateur_cognitif` int(11) NOT NULL,
  `criteres` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `affirmation` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_critere`),
  KEY `id_type_controle` (`id_type_controle`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT AUTO_INCREMENT=2037 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_criteresxtypesxcriteres`
--

DROP TABLE IF EXISTS `gevu_criteresxtypesxcriteres`;
CREATE TABLE IF NOT EXISTS `gevu_criteresxtypesxcriteres` (
  `id_type_critere` int(11) NOT NULL AUTO_INCREMENT,
  `id_critere` int(11) NOT NULL,
  PRIMARY KEY (`id_critere`,`id_type_critere`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_criteresxtypesxdeficiences`
--

DROP TABLE IF EXISTS `gevu_criteresxtypesxdeficiences`;
CREATE TABLE IF NOT EXISTS `gevu_criteresxtypesxdeficiences` (
  `id_type_deficience` int(11) NOT NULL AUTO_INCREMENT,
  `id_critere` int(11) NOT NULL,
  PRIMARY KEY (`id_critere`,`id_type_deficience`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_criteresxtypesxdroits`
--

DROP TABLE IF EXISTS `gevu_criteresxtypesxdroits`;
CREATE TABLE IF NOT EXISTS `gevu_criteresxtypesxdroits` (
  `id_type_droit` int(11) NOT NULL AUTO_INCREMENT,
  `id_critere` int(11) NOT NULL,
  PRIMARY KEY (`id_critere`,`id_type_droit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_diagext`
--

DROP TABLE IF EXISTS `gevu_diagext`;
CREATE TABLE IF NOT EXISTS `gevu_diagext` (
  `id_diagext` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `id_entreprise` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  `source` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auditif` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `cognitif` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `moteur` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `visuel` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `general` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `cmt_general` text COLLATE utf8_unicode_ci NOT NULL,
  `cmt_auditif` text COLLATE utf8_unicode_ci NOT NULL,
  `cmt_cognitif` text COLLATE utf8_unicode_ci NOT NULL,
  `cmt_moteur` text COLLATE utf8_unicode_ci NOT NULL,
  `cmt_visuel` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_diagext`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_diagnostics`
--

DROP TABLE IF EXISTS `gevu_diagnostics`;
CREATE TABLE IF NOT EXISTS `gevu_diagnostics` (
  `id_diag` int(11) NOT NULL AUTO_INCREMENT,
  `id_critere` int(11) NOT NULL,
  `id_reponse` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `id_lieu` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  `last` bit(1) DEFAULT NULL,
  PRIMARY KEY (`id_diag`),
  KEY `id_critere` (`id_critere`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`),
  KEY `id_reponse` (`id_reponse`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_diagnosticsxsolutions`
--

DROP TABLE IF EXISTS `gevu_diagnosticsxsolutions`;
CREATE TABLE IF NOT EXISTS `gevu_diagnosticsxsolutions` (
  `id_diagsolus` int(11) NOT NULL AUTO_INCREMENT,
  `id_diag` int(11) NOT NULL,
  `id_solution` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `id_cout` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `unite` int(11) NOT NULL,
  `pose` int(11) NOT NULL,
  `metre_lineaire` int(11) NOT NULL,
  `metre_carre` int(11) NOT NULL,
  `achat` int(11) NOT NULL,
  `cout` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_diagsolus`),
  KEY `id_diag` (`id_diag`),
  KEY `id_cout` (`id_cout`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_diagnosticsxvoirie`
--

DROP TABLE IF EXISTS `gevu_diagnosticsxvoirie`;
CREATE TABLE IF NOT EXISTS `gevu_diagnosticsxvoirie` (
  `id_diag_voirie` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_diag_voirie`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_docs`
--

DROP TABLE IF EXISTS `gevu_docs`;
CREATE TABLE IF NOT EXISTS `gevu_docs` (
  `id_doc` int(11) NOT NULL AUTO_INCREMENT,
  `id_instant` int(11) NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `titre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `branche` int(11) NOT NULL,
  `tronc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path_source` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_doc`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_docsxlieux`
--

DROP TABLE IF EXISTS `gevu_docsxlieux`;
CREATE TABLE IF NOT EXISTS `gevu_docsxlieux` (
  `id_doc` int(11) NOT NULL,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  PRIMARY KEY (`id_doc`,`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_docsxproblemes`
--

DROP TABLE IF EXISTS `gevu_docsxproblemes`;
CREATE TABLE IF NOT EXISTS `gevu_docsxproblemes` (
  `id_doc` int(11) NOT NULL,
  `id_probleme` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  PRIMARY KEY (`id_doc`,`id_probleme`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_docsxproduits`
--

DROP TABLE IF EXISTS `gevu_docsxproduits`;
CREATE TABLE IF NOT EXISTS `gevu_docsxproduits` (
  `id_doc` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  PRIMARY KEY (`id_doc`,`id_produit`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_docsxrapports`
--

DROP TABLE IF EXISTS `gevu_docsxrapports`;
CREATE TABLE IF NOT EXISTS `gevu_docsxrapports` (
  `id_doc` int(11) NOT NULL,
  `id_rapport` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  PRIMARY KEY (`id_doc`,`id_rapport`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_docsxsolutions`
--

DROP TABLE IF EXISTS `gevu_docsxsolutions`;
CREATE TABLE IF NOT EXISTS `gevu_docsxsolutions` (
  `id_doc` int(11) NOT NULL,
  `id_solution` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  PRIMARY KEY (`id_doc`,`id_solution`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_droits`
--

DROP TABLE IF EXISTS `gevu_droits`;
CREATE TABLE IF NOT EXISTS `gevu_droits` (
  `id_droit` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `params` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_droit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_entreprises`
--

DROP TABLE IF EXISTS `gevu_entreprises`;
CREATE TABLE IF NOT EXISTS `gevu_entreprises` (
  `id_entreprise` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `num` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `voie` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `code_postal` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ville` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pays` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `observations` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_entreprise`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_espaces`
--

DROP TABLE IF EXISTS `gevu_espaces`;
CREATE TABLE IF NOT EXISTS `gevu_espaces` (
  `id_espace` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_controle` int(11) NOT NULL,
  `reponse_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_specifique_int` int(11) NOT NULL,
  `id_type_specifique_ext` int(11) NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  `surface` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_espace`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_espacesxexterieurs`
--

DROP TABLE IF EXISTS `gevu_espacesxexterieurs`;
CREATE TABLE IF NOT EXISTS `gevu_espacesxexterieurs` (
  `id_espace_ext` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fonction` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_espace` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_specifique_ext` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_espace_ext`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_espacesxinterieurs`
--

DROP TABLE IF EXISTS `gevu_espacesxinterieurs`;
CREATE TABLE IF NOT EXISTS `gevu_espacesxinterieurs` (
  `id_espace_int` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fonction` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_specifique_int` int(11) NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_espace_int`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_etablissements`
--

DROP TABLE IF EXISTS `gevu_etablissements`;
CREATE TABLE IF NOT EXISTS `gevu_etablissements` (
  `id_etablissement` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `commune` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pays` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code_postal` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_proprietaire` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_delegataire` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_3` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_4` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_5` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  `catequip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gestionnaire` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_etablissement`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_exis`
--

DROP TABLE IF EXISTS `gevu_exis`;
CREATE TABLE IF NOT EXISTS `gevu_exis` (
  `id_exi` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_bin NOT NULL,
  `url` varchar(255) COLLATE utf8_bin NOT NULL,
  `mail` varchar(255) COLLATE utf8_bin NOT NULL,
  `mdp` varchar(32) COLLATE utf8_bin NOT NULL,
  `mdp_sel` varchar(32) COLLATE utf8_bin NOT NULL,
  `role` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_exi`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_exisxcontacts`
--

DROP TABLE IF EXISTS `gevu_exisxcontacts`;
CREATE TABLE IF NOT EXISTS `gevu_exisxcontacts` (
  `id_exi` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  PRIMARY KEY (`id_exi`,`id_contact`),
  KEY `id_exi` (`id_exi`),
  KEY `id_contact` (`id_contact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_exisxdroits`
--

DROP TABLE IF EXISTS `gevu_exisxdroits`;
CREATE TABLE IF NOT EXISTS `gevu_exisxdroits` (
  `id_exi` int(11) NOT NULL,
  `id_droit` int(11) NOT NULL,
  `params` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_exi`,`id_droit`),
  KEY `id_exi` (`id_exi`),
  KEY `id_droit` (`id_droit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_georss`
--

DROP TABLE IF EXISTS `gevu_georss`;
CREATE TABLE IF NOT EXISTS `gevu_georss` (
  `id_georss` int(11) NOT NULL AUTO_INCREMENT,
  `id_instant` int(11) NOT NULL,
  `id_lieu` int(11) NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_georss`),
  KEY `id_instant` (`id_instant`),
  KEY `id_lieu` (`id_lieu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_geos`
--

DROP TABLE IF EXISTS `gevu_geos`;
CREATE TABLE IF NOT EXISTS `gevu_geos` (
  `id_geo` int(11) NOT NULL AUTO_INCREMENT,
  `id_instant` int(11) NOT NULL,
  `id_lieu` int(11) NOT NULL,
  `lat` decimal(12,8) NOT NULL,
  `lng` decimal(12,8) NOT NULL,
  `latlng` point NOT NULL,
  `sw` point NOT NULL,
  `ne` point NOT NULL,
  `zoom_min` int(11) NOT NULL,
  `zoom_max` int(11) NOT NULL,
  `adresse` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `codepostal` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ville` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pays` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `kml` longtext COLLATE utf8_unicode_ci NOT NULL,
  `type_carte` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci NOT NULL,
  `heading` decimal(12,8) NOT NULL,
  `pitch` decimal(12,8) NOT NULL,
  `zoom_sv` decimal(2,2) NOT NULL,
  `lat_sv` decimal(12,8) NOT NULL,
  `lng_sv` decimal(12,8) NOT NULL,
  `insee` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `id_ext` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_geo`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=130 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_groupes`
--

DROP TABLE IF EXISTS `gevu_groupes`;
CREATE TABLE IF NOT EXISTS `gevu_groupes` (
  `id_groupe` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `ref` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_groupe`),
  UNIQUE KEY `id_lieu` (`id_lieu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_instants`
--

DROP TABLE IF EXISTS `gevu_instants`;
CREATE TABLE IF NOT EXISTS `gevu_instants` (
  `id_instant` int(11) NOT NULL AUTO_INCREMENT,
  `maintenant` datetime NOT NULL,
  `ici` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_exi` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `commentaires` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=186 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_interventions`
--

DROP TABLE IF EXISTS `gevu_interventions`;
CREATE TABLE IF NOT EXISTS `gevu_interventions` (
  `id_interv` int(11) NOT NULL AUTO_INCREMENT,
  `id_produit` int(11) NOT NULL,
  `interv` int(11) NOT NULL,
  `unite` int(11) NOT NULL,
  `frequence` int(11) NOT NULL,
  `cout` decimal(65,2) NOT NULL,
  PRIMARY KEY (`id_interv`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=279 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_lieux`
--

DROP TABLE IF EXISTS `gevu_lieux`;
CREATE TABLE IF NOT EXISTS `gevu_lieux` (
  `id_lieu` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_instant` int(11) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `niv` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  `lieu_parent` int(11) NOT NULL,
  `id_type_controle` int(11) NOT NULL,
  `lock_diag` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `lieu_copie` int(11) NOT NULL,
  PRIMARY KEY (`id_lieu`),
  KEY `id_instant` (`id_instant`),
  KEY `lieu_parent` (`lieu_parent`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `id_type_controle` (`id_type_controle`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=131 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_lieuxchainedeplacements`
--

DROP TABLE IF EXISTS `gevu_lieuxchainedeplacements`;
CREATE TABLE IF NOT EXISTS `gevu_lieuxchainedeplacements` (
  `id_chainedepla` int(11) NOT NULL,
  `id_lieu` int(11) NOT NULL,
  `ordre` int(11) NOT NULL,
  `id_lieuchainedepla` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id_lieuchainedepla`),
  KEY `id_chainedepla` (`id_chainedepla`,`id_lieu`),
  KEY `id_lieu` (`id_lieu`),
  KEY `ordre` (`ordre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_lieuxinterventions`
--

DROP TABLE IF EXISTS `gevu_lieuxinterventions`;
CREATE TABLE IF NOT EXISTS `gevu_lieuxinterventions` (
  `id_lieuinterv` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_interv` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `fait` datetime NOT NULL,
  `afaire` datetime NOT NULL,
  `cout` decimal(65,2) NOT NULL,
  PRIMARY KEY (`id_lieuinterv`),
  KEY `id_lieu` (`id_lieu`,`id_interv`,`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_locaux`
--

DROP TABLE IF EXISTS `gevu_locaux`;
CREATE TABLE IF NOT EXISTS `gevu_locaux` (
  `id_local` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `activite` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_local`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_logements`
--

DROP TABLE IF EXISTS `gevu_logements`;
CREATE TABLE IF NOT EXISTS `gevu_logements` (
  `id_logement` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `num_porte` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL,
  `Type_Logement` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Code_Escalier` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Etage` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Exposition` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Nombre_pieces` int(11) NOT NULL,
  `Surface_Reelle` decimal(10,2) NOT NULL,
  `Surface_Appliquee` decimal(10,2) NOT NULL,
  `Type_Reception_TV` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Categorie_Module` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `CREP_Date` datetime NOT NULL,
  `CREP_presence_Plomb` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `CREP_Seuil_Plomb_depasse` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `DTA_Date` datetime NOT NULL,
  `DTA_Date_Travaux` datetime NOT NULL,
  `DTA_Presence_Amiante` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `DTA_Presence_Amiante_Degradee` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `DTA_Mesure_Conservatoire` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `DPE_Date` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `DPE_consommation_reelle` int(11) NOT NULL,
  `DPE_Categorie_Consommation` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `DPE_emissions_GES` int(11) NOT NULL,
  `DPE_Categorie_Emissions_GES` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_logement`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_metiers`
--

DROP TABLE IF EXISTS `gevu_metiers`;
CREATE TABLE IF NOT EXISTS `gevu_metiers` (
  `id_metier` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_metier`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_motsclefs`
--

DROP TABLE IF EXISTS `gevu_motsclefs`;
CREATE TABLE IF NOT EXISTS `gevu_motsclefs` (
  `id_motclef` bigint(21) NOT NULL,
  `titre` varchar(255) CHARACTER SET utf8 NOT NULL,
  `type` int(11) NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_motclef`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_niveaux`
--

DROP TABLE IF EXISTS `gevu_niveaux`;
CREATE TABLE IF NOT EXISTS `gevu_niveaux` (
  `id_niveau` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_1` int(11) NOT NULL,
  `reponse_2` int(11) NOT NULL,
  `reponse_3` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_niveau`),
  KEY `id_lieu` (`id_lieu`,`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_objetsxexterieurs`
--

DROP TABLE IF EXISTS `gevu_objetsxexterieurs`;
CREATE TABLE IF NOT EXISTS `gevu_objetsxexterieurs` (
  `id_objet_ext` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fonctions` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_objet` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_objet_ext` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_objet_ext`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_objetsxinterieurs`
--

DROP TABLE IF EXISTS `gevu_objetsxinterieurs`;
CREATE TABLE IF NOT EXISTS `gevu_objetsxinterieurs` (
  `id_objet_int` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fonctions` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_objet` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_objet_int`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_objetsxvoiries`
--

DROP TABLE IF EXISTS `gevu_objetsxvoiries`;
CREATE TABLE IF NOT EXISTS `gevu_objetsxvoiries` (
  `id_objet_voirie` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_objet_voirie` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_objet_voirie`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_observations`
--

DROP TABLE IF EXISTS `gevu_observations`;
CREATE TABLE IF NOT EXISTS `gevu_observations` (
  `id_observations` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `id_reponse` int(11) NOT NULL,
  `num_marker` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_critere` int(11) NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  `id_diag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_observations`),
  KEY `id_lieu` (`id_lieu`,`id_instant`,`id_reponse`,`id_critere`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_paramximport`
--

DROP TABLE IF EXISTS `gevu_paramximport`;
CREATE TABLE IF NOT EXISTS `gevu_paramximport` (
  `id_paramximport` int(11) NOT NULL AUTO_INCREMENT,
  `colSource` varchar(255) COLLATE utf8_bin NOT NULL,
  `colChamp` varchar(32) COLLATE utf8_bin NOT NULL,
  `objDest` varchar(255) COLLATE utf8_bin NOT NULL,
  `ordre` int(11) NOT NULL,
  `type_import` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_paramximport`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_parcelles`
--

DROP TABLE IF EXISTS `gevu_parcelles`;
CREATE TABLE IF NOT EXISTS `gevu_parcelles` (
  `id_parcelle` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `contact_proprietaire` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `superficie` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cloture` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  `ref_cadastre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_parcelle`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_partiescommunes`
--

DROP TABLE IF EXISTS `gevu_partiescommunes`;
CREATE TABLE IF NOT EXISTS `gevu_partiescommunes` (
  `id_part_commu` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_part_commu`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_problemes`
--

DROP TABLE IF EXISTS `gevu_problemes`;
CREATE TABLE IF NOT EXISTS `gevu_problemes` (
  `id_probleme` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_critere` int(11) NOT NULL,
  `num_marker` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mesure` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `observations` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fichier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `doc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_instant` int(11) NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  `id_diag` int(11) NOT NULL,
  PRIMARY KEY (`id_probleme`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_critere` (`id_critere`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_produits`
--

DROP TABLE IF EXISTS `gevu_produits`;
CREATE TABLE IF NOT EXISTS `gevu_produits` (
  `id_produit` int(11) NOT NULL AUTO_INCREMENT,
  `id_entreprise` int(11) NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `technique` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `preconisation` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `marque` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `modele` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_produit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=313 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_produitsxcouts`
--

DROP TABLE IF EXISTS `gevu_produitsxcouts`;
CREATE TABLE IF NOT EXISTS `gevu_produitsxcouts` (
  `id_produit` int(11) NOT NULL,
  `id_cout` int(11) NOT NULL,
  PRIMARY KEY (`id_produit`,`id_cout`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_produitsxexperimentations`
--

DROP TABLE IF EXISTS `gevu_produitsxexperimentations`;
CREATE TABLE IF NOT EXISTS `gevu_produitsxexperimentations` (
  `id_produit` int(11) NOT NULL,
  `id_experimentation` int(11) NOT NULL,
  PRIMARY KEY (`id_produit`,`id_experimentation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_rapports`
--

DROP TABLE IF EXISTS `gevu_rapports`;
CREATE TABLE IF NOT EXISTS `gevu_rapports` (
  `id_rapport` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `site` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_exi` int(11) NOT NULL,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL,
  `selection` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_rapport`),
  KEY `id_exi` (`id_exi`),
  KEY `id_lieu` (`id_lieu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_roles`
--

DROP TABLE IF EXISTS `gevu_roles`;
CREATE TABLE IF NOT EXISTS `gevu_roles` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `inherit` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `params` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_scenario`
--

DROP TABLE IF EXISTS `gevu_scenario`;
CREATE TABLE IF NOT EXISTS `gevu_scenario` (
  `id_scenario` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL,
  `params` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_scenario`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_scenes`
--

DROP TABLE IF EXISTS `gevu_scenes`;
CREATE TABLE IF NOT EXISTS `gevu_scenes` (
  `id_scene` int(11) NOT NULL AUTO_INCREMENT,
  `id_scenario` int(11) NOT NULL,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `paramsCrit` longtext COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `paramsCtrl` longtext COLLATE utf8_unicode_ci NOT NULL,
  `paramsForm` longtext COLLATE utf8_unicode_ci NOT NULL,
  `paramsProd` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_scene`),
  KEY `id_scenario` (`id_scenario`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1569 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_sites`
--

DROP TABLE IF EXISTS `gevu_sites`;
CREATE TABLE IF NOT EXISTS `gevu_sites` (
  `id_site` int(11) NOT NULL AUTO_INCREMENT,
  `site_spip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_site`),
  UNIQUE KEY `site_spip` (`site_spip`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_solutions`
--

DROP TABLE IF EXISTS `gevu_solutions`;
CREATE TABLE IF NOT EXISTS `gevu_solutions` (
  `id_solution` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lib` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_solution` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_solution`),
  KEY `id_type_solution` (`id_type_solution`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=505 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_solutionsxcouts`
--

DROP TABLE IF EXISTS `gevu_solutionsxcouts`;
CREATE TABLE IF NOT EXISTS `gevu_solutionsxcouts` (
  `id_solution` int(11) NOT NULL,
  `id_cout` int(11) NOT NULL,
  PRIMARY KEY (`id_solution`,`id_cout`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_solutionsxcriteres`
--

DROP TABLE IF EXISTS `gevu_solutionsxcriteres`;
CREATE TABLE IF NOT EXISTS `gevu_solutionsxcriteres` (
  `id_solution` int(11) NOT NULL,
  `id_critere` int(11) NOT NULL,
  PRIMARY KEY (`id_solution`,`id_critere`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_solutionsxmetiers`
--

DROP TABLE IF EXISTS `gevu_solutionsxmetiers`;
CREATE TABLE IF NOT EXISTS `gevu_solutionsxmetiers` (
  `id_solution` int(11) NOT NULL,
  `id_metier` int(11) NOT NULL,
  PRIMARY KEY (`id_solution`,`id_metier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_solutionsxproduits`
--

DROP TABLE IF EXISTS `gevu_solutionsxproduits`;
CREATE TABLE IF NOT EXISTS `gevu_solutionsxproduits` (
  `id_solution` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  PRIMARY KEY (`id_solution`,`id_produit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_stats`
--

DROP TABLE IF EXISTS `gevu_stats`;
CREATE TABLE IF NOT EXISTS `gevu_stats` (
  `id_stat` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `Antenne_rattachement` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Code_groupe` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Code_Batiment` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Tranche` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Code_Escalier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Indicateur_Zus` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Code_Logement` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Categorie_Module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Logement_Individuel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Type_Logement` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Nombre_pieces` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Etage` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Surface_Reelle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Surface_Appliquee` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Type_financement` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Annee_Construction` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Contrat` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Type_Reception_TV` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Occupation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Motif_Vacance` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Copropriete` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `DPE_Date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `DPE_consommation_reelle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `DPE_Categorie_Consommation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `DPE_emissions_GES` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `DPE_Categorie_Emissions_GES` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `CREP_Date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `CREP_presence_Plomb` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `CREP_Seuil_Plomb_depasse` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `DTA_Date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `DTA_Presence_Amiante` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `DTA_Presence_Amiante_Degradee` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `DTA_Mesure_Conservatoire` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `DTA_Date_Travaux` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Gardien` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_CSP` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_AHH` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_Famille_mono_parentale` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_Famille_Nombreuse` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_Celibataire` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_Foyer_0_2Enf` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_Nb_Occupants` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_Age_Signataire_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_Age_Signataire_2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_nb_enfants` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_nb_enfants_0_10_ans` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_nb_enfants_11_17_ans` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_nb_enfants_sup18_ans` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_Provenance` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_Anciennete` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Peupl_Surpeuplement` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Montant_Impaye` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Montant_Quittance` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_stat`),
  UNIQUE KEY `Code_Logement` (`Code_Logement`),
  KEY `id_instant` (`id_instant`),
  KEY `id_lieu` (`id_lieu`),
  KEY `Antenne_rattachement` (`Antenne_rattachement`,`Code_groupe`),
  KEY `Code_Batiment` (`Code_Batiment`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_synchros`
--

DROP TABLE IF EXISTS `gevu_synchros`;
CREATE TABLE IF NOT EXISTS `gevu_synchros` (
  `id_lieu` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_test`
--

DROP TABLE IF EXISTS `gevu_test`;
CREATE TABLE IF NOT EXISTS `gevu_test` (
  `id_droit` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `params` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_droit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_tree`
--

DROP TABLE IF EXISTS `gevu_tree`;
CREATE TABLE IF NOT EXISTS `gevu_tree` (
  `top` smallint(6) DEFAULT NULL,
  `nodeID` smallint(6) DEFAULT NULL,
  `leftedge` smallint(6) DEFAULT NULL,
  `rightedge` smallint(6) DEFAULT NULL,
  KEY `nodeID` (`nodeID`,`leftedge`,`rightedge`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_typesxcontroles`
--

DROP TABLE IF EXISTS `gevu_typesxcontroles`;
CREATE TABLE IF NOT EXISTS `gevu_typesxcontroles` (
  `id_type_controle` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `zend_obj` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `aide` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_type_controle`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=141 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_typesxcriteres`
--

DROP TABLE IF EXISTS `gevu_typesxcriteres`;
CREATE TABLE IF NOT EXISTS `gevu_typesxcriteres` (
  `id_type_critere` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_type_critere`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_typesxdeficiences`
--

DROP TABLE IF EXISTS `gevu_typesxdeficiences`;
CREATE TABLE IF NOT EXISTS `gevu_typesxdeficiences` (
  `id_type_deficience` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_type_deficience`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_typesxdroits`
--

DROP TABLE IF EXISTS `gevu_typesxdroits`;
CREATE TABLE IF NOT EXISTS `gevu_typesxdroits` (
  `id_type_droit` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_type_droit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_typesxsolutions`
--

DROP TABLE IF EXISTS `gevu_typesxsolutions`;
CREATE TABLE IF NOT EXISTS `gevu_typesxsolutions` (
  `id_type_solution` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_type_solution`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_typexmotsclefs`
--

DROP TABLE IF EXISTS `gevu_typexmotsclefs`;
CREATE TABLE IF NOT EXISTS `gevu_typexmotsclefs` (
  `id_type_motsclefs` bigint(21) NOT NULL AUTO_INCREMENT,
  `id_parent` bigint(20) NOT NULL DEFAULT '0',
  `titre` text COLLATE utf8_unicode_ci NOT NULL,
  `descriptif` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_type_motsclefs`),
  KEY `id_parent` (`id_parent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=56 ;
