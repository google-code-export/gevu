-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 21 Novembre 2011 à 11:15
-- Version du serveur: 5.5.9
-- Version de PHP: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `gevu_new_alceane`
--

-- --------------------------------------------------------

--
-- Structure de la table `gevu_batiments`
--

CREATE TABLE `gevu_batiments` (
  `id_batiment` int(11) NOT NULL AUTO_INCREMENT,
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
  `contact_gardien` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `horaires_gardien` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `horaires_batiment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
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
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_batiment`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=561 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_contacts`
--

CREATE TABLE `gevu_contacts` (
  `id_contact` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fixe` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_contact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_contactsxentreprises`
--

CREATE TABLE `gevu_contactsxentreprises` (
  `id_contact` int(11) NOT NULL,
  `id_entreprise` int(11) NOT NULL,
  PRIMARY KEY (`id_contact`,`id_entreprise`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_couts`
--

CREATE TABLE `gevu_couts` (
  `id_cout` int(11) NOT NULL AUTO_INCREMENT,
  `id_instant` int(11) NOT NULL,
  `unite` int(11) NOT NULL,
  `metre_lineaire` int(11) NOT NULL,
  `metre_carre` int(11) NOT NULL,
  `achat` int(11) NOT NULL,
  `pose` int(11) NOT NULL,
  PRIMARY KEY (`id_cout`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_criteres`
--

CREATE TABLE `gevu_criteres` (
  `id_critere` int(11) NOT NULL AUTO_INCREMENT,
  `id_type_controle` int(11) NOT NULL,
  `ref` varchar(100) COLLATE utf8_bin NOT NULL,
  `handicateur_moteur` int(11) NOT NULL,
  `handicateur_auditif` int(11) NOT NULL,
  `handicateur_visuel` int(11) NOT NULL,
  `handicateur_cognitif` int(11) NOT NULL,
  `criteres` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `affirmation` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id_critere`),
  KEY `id_type_controle` (`id_type_controle`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT AUTO_INCREMENT=675 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_criteresxtypesxcriteres`
--

CREATE TABLE `gevu_criteresxtypesxcriteres` (
  `id_type_critere` int(11) NOT NULL AUTO_INCREMENT,
  `id_critere` int(11) NOT NULL,
  PRIMARY KEY (`id_critere`,`id_type_critere`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_criteresxtypesxdeficiences`
--

CREATE TABLE `gevu_criteresxtypesxdeficiences` (
  `id_type_deficience` int(11) NOT NULL AUTO_INCREMENT,
  `id_critere` int(11) NOT NULL,
  PRIMARY KEY (`id_critere`,`id_type_deficience`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_criteresxtypesxdroits`
--

CREATE TABLE `gevu_criteresxtypesxdroits` (
  `id_type_droit` int(11) NOT NULL AUTO_INCREMENT,
  `id_critere` int(11) NOT NULL,
  PRIMARY KEY (`id_critere`,`id_type_droit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_diagnostics`
--

CREATE TABLE `gevu_diagnostics` (
  `id_diag` int(11) NOT NULL AUTO_INCREMENT,
  `id_critere` int(11) NOT NULL,
  `id_reponse` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `id_lieu` int(11) NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_diag`),
  KEY `id_critere` (`id_critere`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`),
  KEY `id_reponse` (`id_reponse`),
  KEY `id_donnee` (`id_donnee`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED AUTO_INCREMENT=5907 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_diagnosticsxvoirie`
--

CREATE TABLE `gevu_diagnosticsxvoirie` (
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

CREATE TABLE `gevu_docs` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=603 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_docsxlieux`
--

CREATE TABLE `gevu_docsxlieux` (
  `id_doc` int(11) NOT NULL,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  PRIMARY KEY (`id_doc`,`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_docsxproduits`
--

CREATE TABLE `gevu_docsxproduits` (
  `id_doc` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  PRIMARY KEY (`id_doc`,`id_produit`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_docsxsolutions`
--

CREATE TABLE `gevu_docsxsolutions` (
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

CREATE TABLE `gevu_droits` (
  `id_droit` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `params` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_droit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_entreprises`
--

CREATE TABLE `gevu_entreprises` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_espaces`
--

CREATE TABLE `gevu_espaces` (
  `id_espace` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_espace` int(11) NOT NULL,
  `reponse_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_specifique_int` int(11) NOT NULL,
  `id_type_specifique_ext` int(11) NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_espace`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_espacesxexterieurs`
--

CREATE TABLE `gevu_espacesxexterieurs` (
  `id_espace_ext` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fonction` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_espace` int(11) NOT NULL,
  `id_type_specifique_ext` int(11) NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_espace_ext`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_espacesxinterieurs`
--

CREATE TABLE `gevu_espacesxinterieurs` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=14747 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_etablissements`
--

CREATE TABLE `gevu_etablissements` (
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
  PRIMARY KEY (`id_etablissement`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=561 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_exis`
--

CREATE TABLE `gevu_exis` (
  `id_exi` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_bin NOT NULL,
  `url` varchar(255) COLLATE utf8_bin NOT NULL,
  `mail` varchar(255) COLLATE utf8_bin NOT NULL,
  `mdp` varchar(32) COLLATE utf8_bin NOT NULL,
  `mdp_sel` varchar(32) COLLATE utf8_bin NOT NULL,
  `role` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_exi`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_exisxcontacts`
--

CREATE TABLE `gevu_exisxcontacts` (
  `id_exi` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  PRIMARY KEY (`id_exi`,`id_contact`),
  KEY `id_exi` (`id_exi`),
  KEY `id_contact` (`id_contact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_exisxdroits`
--

CREATE TABLE `gevu_exisxdroits` (
  `id_exi` int(11) NOT NULL,
  `id_droit` int(11) NOT NULL,
  `params` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_exi`,`id_droit`),
  KEY `id_exi` (`id_exi`),
  KEY `id_droit` (`id_droit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_georss`
--

CREATE TABLE `gevu_georss` (
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

CREATE TABLE `gevu_geos` (
  `id_geo` int(11) NOT NULL AUTO_INCREMENT,
  `id_instant` int(11) NOT NULL,
  `id_lieu` int(11) NOT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lng` decimal(10,8) NOT NULL,
  `zoom_min` int(11) NOT NULL,
  `zoom_max` int(11) NOT NULL,
  `adresse` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `kml` text COLLATE utf8_unicode_ci NOT NULL,
  `id_type_carte` int(11) NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_geo`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=564 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_instants`
--

CREATE TABLE `gevu_instants` (
  `id_instant` int(11) NOT NULL AUTO_INCREMENT,
  `maintenant` datetime NOT NULL,
  `ici` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_exi` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_instantsxdocs`
--

CREATE TABLE `gevu_instantsxdocs` (
  `id_doc` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  PRIMARY KEY (`id_doc`,`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_lieux`
--

CREATE TABLE `gevu_lieux` (
  `id_lieu` int(11) NOT NULL AUTO_INCREMENT,
  `id_rubrique` int(11) NOT NULL,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_parent` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `niv` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  `lieu_parent` int(11) NOT NULL,
  PRIMARY KEY (`id_lieu`),
  KEY `id_parent` (`id_parent`),
  KEY `id_rubrique` (`id_rubrique`),
  KEY `id_instant` (`id_instant`),
  KEY `arbre` (`lft`,`rgt`,`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=22487 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_metiers`
--

CREATE TABLE `gevu_metiers` (
  `id_metier` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_metier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_motsclefs`
--

CREATE TABLE `gevu_motsclefs` (
  `id_motclef` bigint(21) NOT NULL,
  `titre` varchar(255) CHARACTER SET utf8 NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id_motclef`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_niveaux`
--

CREATE TABLE `gevu_niveaux` (
  `id_niveau` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `id_reponse_1` int(11) NOT NULL,
  `id_reponse_2` int(11) NOT NULL,
  `id_reponse_3` int(11) NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_niveau`),
  KEY `id_lieu` (`id_lieu`,`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=6228 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_objetsxexterieurs`
--

CREATE TABLE `gevu_objetsxexterieurs` (
  `id_objet_ext` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fonctions` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_objet` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_objet_ext` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_objet_ext`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_objetsxinterieurs`
--

CREATE TABLE `gevu_objetsxinterieurs` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_objetsxvoiries`
--

CREATE TABLE `gevu_objetsxvoiries` (
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

CREATE TABLE `gevu_observations` (
  `id_observations` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `id_reponse` int(11) NOT NULL,
  `num_marker` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_critere` int(11) NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_observations`),
  KEY `id_lieu` (`id_lieu`,`id_instant`,`id_reponse`,`id_critere`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=187 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_paramximport`
--

CREATE TABLE `gevu_paramximport` (
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

CREATE TABLE `gevu_parcelles` (
  `id_parcelle` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `commune` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pays` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code_postal` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_proprietaire` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reponse_2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_donnee` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_parcelle`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_problemes`
--

CREATE TABLE `gevu_problemes` (
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
  PRIMARY KEY (`id_probleme`),
  KEY `id_lieu` (`id_lieu`),
  KEY `id_critere` (`id_critere`),
  KEY `id_instant` (`id_instant`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=329 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_produits`
--

CREATE TABLE `gevu_produits` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_produitsxcouts`
--

CREATE TABLE `gevu_produitsxcouts` (
  `id_produit` int(11) NOT NULL,
  `id_cout` int(11) NOT NULL,
  PRIMARY KEY (`id_produit`,`id_cout`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_produitsxexperimentations`
--

CREATE TABLE `gevu_produitsxexperimentations` (
  `id_produit` int(11) NOT NULL,
  `id_experimentation` int(11) NOT NULL,
  PRIMARY KEY (`id_produit`,`id_experimentation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_rapports`
--

CREATE TABLE `gevu_rapports` (
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

CREATE TABLE `gevu_roles` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `inherit` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `params` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_scenario`
--

CREATE TABLE `gevu_scenario` (
  `id_scenario` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL,
  `params` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_scenario`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_scenes`
--

CREATE TABLE `gevu_scenes` (
  `id_scene` int(11) NOT NULL AUTO_INCREMENT,
  `id_scenario` int(11) NOT NULL,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `params` longtext COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `xml` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_scene`),
  KEY `id_scenario` (`id_scenario`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=55 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_sites`
--

CREATE TABLE `gevu_sites` (
  `id_site` int(11) NOT NULL AUTO_INCREMENT,
  `site_spip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lib` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_site`),
  UNIQUE KEY `site_spip` (`site_spip`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_solutions`
--

CREATE TABLE `gevu_solutions` (
  `id_solution` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lib` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_solution` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  PRIMARY KEY (`id_solution`),
  KEY `id_type_solution` (`id_type_solution`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_solutionsxcouts`
--

CREATE TABLE `gevu_solutionsxcouts` (
  `id_solution` int(11) NOT NULL,
  `id_cout` int(11) NOT NULL,
  PRIMARY KEY (`id_solution`,`id_cout`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_solutionsxcriteres`
--

CREATE TABLE `gevu_solutionsxcriteres` (
  `id_solution` int(11) NOT NULL,
  `id_critere` int(11) NOT NULL,
  PRIMARY KEY (`id_solution`,`id_critere`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_solutionsxmetiers`
--

CREATE TABLE `gevu_solutionsxmetiers` (
  `id_solution` int(11) NOT NULL,
  `id_metier` int(11) NOT NULL,
  PRIMARY KEY (`id_solution`,`id_metier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_solutionsxproduits`
--

CREATE TABLE `gevu_solutionsxproduits` (
  `id_solution` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  PRIMARY KEY (`id_solution`,`id_produit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_synchros`
--

CREATE TABLE `gevu_synchros` (
  `id_lieu` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_tree`
--

CREATE TABLE `gevu_tree` (
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

CREATE TABLE `gevu_typesxcontroles` (
  `id_type_controle` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `icone` varchar(255) COLLATE utf8_bin NOT NULL,
  `zend_obj` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id_type_controle`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=60 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_typesxcriteres`
--

CREATE TABLE `gevu_typesxcriteres` (
  `id_type_critere` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_type_critere`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_typesxdeficiences`
--

CREATE TABLE `gevu_typesxdeficiences` (
  `id_type_deficience` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_type_deficience`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_typesxdroits`
--

CREATE TABLE `gevu_typesxdroits` (
  `id_type_droit` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_type_droit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_typesxsolutions`
--

CREATE TABLE `gevu_typesxsolutions` (
  `id_type_solution` int(11) NOT NULL AUTO_INCREMENT,
  `lib` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_type_solution`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Structure de la table `gevu_typexmotsclefs`
--

CREATE TABLE `gevu_typexmotsclefs` (
  `id_type_motsclefs` bigint(21) NOT NULL AUTO_INCREMENT,
  `id_parent` bigint(20) NOT NULL DEFAULT '0',
  `titre` text COLLATE utf8_unicode_ci NOT NULL,
  `descriptif` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_type_motsclefs`),
  KEY `id_parent` (`id_parent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=54 ;
