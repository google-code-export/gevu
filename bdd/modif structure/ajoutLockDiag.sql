ALTER TABLE  `gevu_lieux` ADD  `lock_diag` VARCHAR( 10 ) NOT NULL ,
ADD INDEX (  `lock_diag` );

ALTER TABLE gevu_alceane.gevu_lieux ADD lieu_copie INT NOT NULL;
ALTER TABLE gevu_new.gevu_lieux ADD lieu_copie INT NOT NULL;
ALTER TABLE gevu_trouville.gevu_lieux ADD lieu_copie INT NOT NULL;
ALTER TABLE gevu_valdemarne.gevu_lieux ADD lieu_copie INT NOT NULL;
ALTER TABLE gevu_clrp.gevu_lieux ADD lieu_copie INT NOT NULL;
ALTER TABLE gevu_pro_administratif.gevu_lieux ADD lieu_copie INT NOT NULL;
ALTER TABLE gevu_pro_cafetaria.gevu_lieux ADD lieu_copie INT NOT NULL;
ALTER TABLE gevu_pro_formation.gevu_lieux ADD lieu_copie INT NOT NULL;
ALTER TABLE gevu_pro_prospective.gevu_lieux ADD lieu_copie INT NOT NULL;
