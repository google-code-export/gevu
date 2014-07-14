ALTER TABLE  `gevu_geos` ADD  `insee` VARCHAR( 10 ) NOT NULL;
ALTER TABLE  `gevu_geos` ADD  `id_ext` VARCHAR( 10 ) NOT NULL;

ALTER TABLE  `gevu_etablissements` ADD  `catequip` VARCHAR(255) NOT NULL;
ALTER TABLE  `gevu_etablissements` ADD  `gestionnaire` VARCHAR(255) NOT NULL;

ALTER TABLE `gevu_diagext` ADD `general` VARCHAR( 10 ) NOT NULL; 
ALTER TABLE `gevu_diagext` ADD `cmt_general` TEXT NOT NULL; 
ALTER TABLE `gevu_diagext` ADD `cmt_auditif` TEXT NOT NULL; 
ALTER TABLE `gevu_diagext` ADD `cmt_cognitif` TEXT NOT NULL; 
ALTER TABLE `gevu_diagext` ADD `cmt_moteur` TEXT NOT NULL; 
ALTER TABLE `gevu_diagext` ADD `cmt_visuel` TEXT NOT NULL; 