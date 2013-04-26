DROP TABLE IF EXISTS `gevu_diagnosticsxsolutions`;
CREATE TABLE IF NOT EXISTS `gevu_diagnosticsxsolutions` (
  `id_diagsolus` int(11) NOT NULL,
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
  UNIQUE KEY `id_cout` (`id_cout`),
  KEY `id_diag` (`id_diag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `gevu_docsxrapports` (
  `id_doc` int(11) NOT NULL,
  `id_rapport` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  PRIMARY KEY (`id_doc`,`id_rapport`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;