DROP PROCEDURE IF EXISTS gevu_solus.SPIPtoGEVU;
CREATE PROCEDURE gevu_solus.`SPIPtoGEVU`( IN dbn VARCHAR(100),  IN dbo VARCHAR(100), IN instant SMALLINT )
BEGIN
	
  -- DIAGNOSTICS 
	-- on ne conserve que les réponses 'non' et 'sous réserve'
	-- pour les questions qui ne sont pas interméfiaire
  
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_diagnostics "
  ," (id_critere, id_reponse, id_instant, id_lieu, id_donnee, maj)"
  ," SELECT c.id_critere, fdc1.valeur, ",instant,", l.id_lieu, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN ",dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = 'mot_1' "
  ," INNER JOIN ",dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee	AND fdc2.champ = 'ligne_1' "
  ," INNER JOIN ",dbo,".spip_forms_donnees_articles fda ON fda.id_donnee = fd.id_donnee "
  ," INNER JOIN ",dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant
  ," LEFT JOIN ",dbn,".gevu_criteres c ON c.ref = fdc2.valeur "
  ," WHERE fd.id_form =59 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  
  
  -- PROBLEMES
	-- on ne conserve que le champ photo
	-- les champs 'fichier' et 'doc' devront être ajouter à la table gevu_doc
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_problemes "
  ," (id_lieu, id_critere, num_marker, mesure, observations, fichier, doc, id_instant, id_donnee, maj) "
  ," SELECT l.id_lieu, c.id_critere, fdc1.valeur, fdc11.valeur, fdc3.valeur, fdc4.valeur, fdc6.valeur, ",instant,", fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = 'ligne_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc11 ON fdc11.id_donnee = fd.id_donnee	AND fdc11.champ = 'ligne_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc ON fdc.id_donnee = fd.id_donnee	AND fdc.champ = 'ligne_3' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee	AND fdc3.champ = 'texte_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee	AND fdc2.champ = 'mot_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee	AND fdc4.champ = 'fichier_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee	AND fdc5.champ = 'ligne_5' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee	AND fdc6.champ = 'ligne_4' "
  ," LEFT JOIN ",dbn,".gevu_criteres c ON c.ref = fdc.valeur "
  ," WHERE fd.id_form =60 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;

	-- GEOGRAPHIE
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_geos "
  ," (id_lieu, id_instant, lat, lng, zoom_min, zoom_max, adresse, id_type_carte, kml, id_donnee, maj) "
  ," SELECT l.id_lieu, ",instant,", fdc1.valeur, fdc2.valeur, fdc3.valeur, fdc4.valeur, fdc5.valeur, fdc6.valeur, fdc7.valeur, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant 
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = 'ligne_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee AND fdc2.champ = 'ligne_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee	AND fdc3.champ = 'ligne_3' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee	AND fdc4.champ = 'ligne_4' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee	AND fdc5.champ = 'ligne_7' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee	AND fdc6.champ = 'mot_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc7 ON fdc7.id_donnee = fd.id_donnee	AND fdc7.champ = 'texte_1' "
  ," WHERE fd.id_form = 1 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  
	-- GEORSS
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_georss "
  ," (id_lieu, id_instant, url, id_donnee, maj) "
  ," SELECT l.id_lieu ,",instant,", fdc1.valeur, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = 'url_1' "
  ," WHERE fd.id_form = 81 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;

	-- NIVEAU
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_niveaux "
  ," (id_lieu, id_instant, nom, ref, id_reponse_1, id_reponse_2, id_reponse_3, id_donnee, maj) "
  ," SELECT l.id_lieu, ",instant,", fdc1.valeur, fdc2.valeur, fdc3.valeur, fdc4.valeur, fdc5.valeur, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = 'ligne_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee AND fdc2.champ = 'ligne_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee AND fdc3.champ = 'mot_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee AND fdc4.champ = 'mot_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee AND fdc5.champ = 'mot_3' "
  ," WHERE fd.id_form = 35 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  
	-- BATIMENT
	-- les contacts devront être retraités
	-- (53, 'ligne_10', 12, 'Coordonnées du gardien'
	-- les horaires devront être retraité
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_batiments "
  ," (id_lieu, id_instant, nom, ref, adresse, commune, pays, code_postal "
  ," , contact_proprietaire, contact_delegataire, contact_gardien "
  ," , horaires_gardien, horaires_batiment "
  ," , superficie_parcelle, superficie_batiment "
  ," , date_achevement, date_depot_permis, date_reha "
  ," , reponse_1 , reponse_2, reponse_3, reponse_4, reponse_5, reponse_6, reponse_7, reponse_8, reponse_9 "
  ," , reponse_10, reponse_11, reponse_12, reponse_13, reponse_14, reponse_15, id_donnee, maj) "
  ," SELECT l.id_lieu, ",instant,", fdc1.valeur, fdc2.valeur, fdc3.valeur, fdc4.valeur, fdc5.valeur, fdc6.valeur, fdc7.valeur, fdc8.valeur, fdc9.valeur "
  ," , fdc10.valeur, fdc11.valeur, fdc12.valeur, fdc13.valeur, fdc14.valeur, fdc15.valeur, fdc16.valeur, fdc17.valeur, fdc18.valeur, fdc19.valeur "
  ," , fdc20.valeur, fdc21.valeur, fdc22.valeur, fdc23.valeur, fdc24.valeur, fdc25.valeur, fdc26.valeur, fdc27.valeur, fdc28.valeur, fdc29.valeur "
  ," , fdc30.valeur, fdc31.valeur, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = 'ligne_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee AND fdc2.champ = 'ligne_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee	AND fdc3.champ = 'ligne_3' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee	AND fdc4.champ = 'ligne_4' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee	AND fdc5.champ = 'ligne_5' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee	AND fdc6.champ = 'code_postal_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc7 ON fdc7.id_donnee = fd.id_donnee	AND fdc7.champ = 'ligne_6' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc8 ON fdc8.id_donnee = fd.id_donnee	AND fdc8.champ = 'ligne_7' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc9 ON fdc9.id_donnee = fd.id_donnee	AND fdc9.champ = 'ligne_10' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc10 ON fdc10.id_donnee = fd.id_donnee	AND fdc10.champ = 'ligne_9' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc11 ON fdc11.id_donnee = fd.id_donnee	AND fdc11.champ = 'ligne_11' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc12 ON fdc12.id_donnee = fd.id_donnee 	AND fdc12.champ = 'ligne_13' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc13 ON fdc13.id_donnee = fd.id_donnee	AND fdc13.champ = 'ligne_12' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc14 ON fdc14.id_donnee = fd.id_donnee	AND fdc14.champ = 'ligne_14' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc15 ON fdc15.id_donnee = fd.id_donnee	AND fdc15.champ = 'ligne_15' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc16 ON fdc16.id_donnee = fd.id_donnee	AND fdc16.champ = 'ligne_16' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee	AND fdc17.champ = 'mot_4' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee	AND fdc18.champ = 'mot_5' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc19 ON fdc19.id_donnee = fd.id_donnee	AND fdc19.champ = 'mot_6' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc20 ON fdc20.id_donnee = fd.id_donnee	AND fdc20.champ = 'mot_7' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc21 ON fdc21.id_donnee = fd.id_donnee	AND fdc21.champ = 'mot_9' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc22 ON fdc22.id_donnee = fd.id_donnee	AND fdc22.champ = 'mot_10' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc23 ON fdc23.id_donnee = fd.id_donnee	AND fdc23.champ = 'mot_12' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc24 ON fdc24.id_donnee = fd.id_donnee	AND fdc24.champ = 'mot_13' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc25 ON fdc25.id_donnee = fd.id_donnee	AND fdc25.champ = 'mot_14' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc26 ON fdc26.id_donnee = fd.id_donnee	AND fdc26.champ = 'mot_15' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc27 ON fdc27.id_donnee = fd.id_donnee	AND fdc27.champ = 'mot_16' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc28 ON fdc28.id_donnee = fd.id_donnee	AND fdc28.champ = 'mot_17' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc29 ON fdc29.id_donnee = fd.id_donnee	AND fdc29.champ = 'mot_25' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc30 ON fdc30.id_donnee = fd.id_donnee	AND fdc30.champ = 'mot_27' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc31 ON fdc31.id_donnee = fd.id_donnee	AND fdc31.champ = 'mot_28' "
  ," WHERE fd.id_form = 53 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  
	-- ETABLISSEMENT
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_etablissements "
  ,"(id_lieu, id_instant, nom, ref, adresse, commune, pays, code_postal "
  ," , contact_proprietaire, contact_delegataire "
  ,", reponse_1, reponse_2, reponse_3, reponse_4, reponse_5, id_donnee, maj) "
  ," SELECT l.id_lieu, ",instant,", fdc1.valeur, fdc2.valeur, fdc3.valeur, fdc4.valeur, fdc5.valeur, fdc6.valeur, fdc7.valeur, fdc8.valeur, fdc17.valeur, fdc18.valeur, fdc19.valeur "
  ," , fdc20.valeur, fdc21.valeur, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = 'ligne_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee	AND fdc2.champ = 'ligne_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee	AND fdc3.champ = 'ligne_3' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee	AND fdc4.champ = 'ligne_4' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee	AND fdc5.champ = 'ligne_5' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee	AND fdc6.champ = 'code_postal_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc7 ON fdc7.id_donnee = fd.id_donnee	AND fdc7.champ = 'ligne_6' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc8 ON fdc8.id_donnee = fd.id_donnee	AND fdc8.champ = 'ligne_7' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee	AND fdc17.champ = 'mot_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee	AND fdc18.champ = 'mot_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc19 ON fdc19.id_donnee = fd.id_donnee	AND fdc19.champ = 'mot_3' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc20 ON fdc20.id_donnee = fd.id_donnee	AND fdc20.champ = 'select_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc21 ON fdc21.id_donnee = fd.id_donnee	AND fdc21.champ = 'select_2' "
  ," WHERE fd.id_form = 55 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  
	-- ESPACES
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_espaces "
  ," (id_lieu, id_instant, ref, id_type_espace "
  ," , reponse_1, reponse_2, id_type_specifique_int, id_type_specifique_ext, id_donnee, maj) "
  ," SELECT l.id_lieu, ",instant,", fdc2.valeur, fdc17.valeur, fdc18.valeur, fdc19.valeur, fdc20.valeur, fdc21.valeur, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant," "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee	AND fdc2.champ = 'ligne_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee	AND fdc17.champ = 'mot_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee	AND fdc18.champ = 'mot_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc19 ON fdc19.id_donnee = fd.id_donnee	AND fdc19.champ = 'mot_3' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc20 ON fdc20.id_donnee = fd.id_donnee	AND fdc20.champ = 'mot_4' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc21 ON fdc21.id_donnee = fd.id_donnee	AND fdc21.champ = 'mot_5' "
  ," WHERE fd.id_form = 56 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  
	-- ESPACES INTERIEURS
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_espacesxinterieurs "
  ," (id_lieu, id_instant, nom, ref, fonction, id_type_specifique_int, id_donnee, maj) "
  ," SELECT l.id_lieu, ",instant,", fdc1.valeur, fdc2.valeur, fdc3.valeur, fdc4.valeur, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = 'ligne_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee	AND fdc2.champ = 'ligne_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee	AND fdc3.champ = 'ligne_3' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee	AND fdc4.champ = 'select_2' "
  ," WHERE fd.id_form = 57 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;

  -- PARCELLES
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_parcelles "
  ," (id_lieu, id_instant, nom, ref, adresse, commune, pays, code_postal, contact_proprietaire, reponse_1, reponse_2, id_donnee, maj) "
  ," SELECT l.id_lieu, ",instant,", fdc1.valeur, fdc2.valeur, fdc3.valeur, fdc4.valeur, fdc5.valeur, '', fdc6.valeur, fdc7.valeur, fdc17.valeur, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant 
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = 'ligne_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee	AND fdc2.champ = 'ligne_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee	AND fdc3.champ = 'ligne_3' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee	AND fdc4.champ = 'ligne_4' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee	AND fdc5.champ = 'ligne_5' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee	AND fdc6.champ = 'code_postal_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc7 ON fdc7.id_donnee = fd.id_donnee	AND fdc7.champ = 'ligne_6' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc8 ON fdc8.id_donnee = fd.id_donnee AND fdc8.champ = 'ligne_7'	 "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee	AND fdc17.champ = 'mot_1' "
  ," WHERE fd.id_form = 58 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  
	-- ESPACES EXTERIEURS
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_espacesxexterieurs "
  ," (id_lieu, id_instant, nom, ref, fonction, id_type_espace, id_type_specifique_ext, id_donnee, maj) "
  ," SELECT l.id_lieu, ",instant,", fdc1.valeur, fdc2.valeur, fdc3.valeur, fdc4.valeur, fdc4.valeur, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = 'ligne_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee	AND fdc2.champ = 'ligne_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee	AND fdc3.champ = 'ligne_3' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee	AND fdc4.champ = 'select_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee	AND fdc5.champ = 'select_2' "
  ," WHERE fd.id_form = 61 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;

  -- DIAGNOSTICS VOIRIE
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_diagnosticsxvoirie "
  ," (id_lieu, id_instant, nom, ref, id_donnee, maj) "
  ," SELECT l.id_lieu, ",instant,", fdc1.valeur, fdc3.valeur, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = 'ligne_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee	AND fdc3.champ = 'ligne_3' "
  ," WHERE fd.id_form = 62 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  
	-- OBJETS INTERIEURS
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_objetsxinterieurs "
  ," (id_lieu, id_instant, nom, ref, fonctions, reponse_1, reponse_2, id_type_objet, id_donnee, maj) "
  ," SELECT l.id_lieu, ",instant,", fdc1.valeur, fdc2.valeur, fdc3.valeur, fdc17.valeur, fdc18.valeur, fdc19.valeur, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant," "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee	AND fdc1.champ = 'ligne_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee	AND fdc2.champ = 'ligne_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee	AND fdc3.champ = 'ligne_3' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee	AND fdc17.champ = 'mot_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee	AND fdc18.champ = 'mot_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc19 ON fdc19.id_donnee = fd.id_donnee	AND fdc19.champ = 'mot_3' "
  ," WHERE fd.id_form = 63 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;

  -- OBJETS EXTERIEURS
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_objetsxexterieurs "
  ," (id_lieu, id_instant, nom, ref, fonctions, id_type_objet, id_type_objet_ext, id_donnee, maj) "
  ," SELECT l.id_lieu, ",instant,", fdc1.valeur, fdc2.valeur, fdc3.valeur, fdc17.valeur, fdc18.valeur, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant," "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee	AND fdc1.champ = 'ligne_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee	AND fdc2.champ = 'ligne_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee	AND fdc3.champ = 'ligne_3' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee	AND fdc17.champ = 'select_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee	AND fdc18.champ = 'select_2' "
  ," WHERE fd.id_form = 64 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
	
  -- ELEMENTS VOIRIE
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_objetsxvoiries "
  ," (id_lieu, id_instant, nom, ref, id_type_objet_voirie, id_donnee, maj) "
  ," SELECT l.id_lieu, ",instant,", fdc1.valeur, fdc2.valeur, fdc18.valeur, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant," "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = 'ligne_1' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee	AND fdc2.champ = 'ligne_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee	AND fdc18.champ = 'mot_1' "
  ," WHERE fd.id_form = 69 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  
	-- DOCUMENTS
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_docs "
  ," (tronc, id_instant, path_source, titre, content_type, maj) "
  ," SELECT id_document, ",instant,", fichier, d.titre, mime_type, d.date "
  ," FROM "  ,dbo,".spip_documents d "
  ," INNER JOIN "  ,dbo,".spip_types_documents td ON td.id_type = d.id_type");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  
  -- DOCUMENTS LIEUX
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_docsxlieux "
  ," (id_doc, id_lieu, id_instant) "
  ," SELECT gd.id_doc, l.id_lieu, ",instant
  ," FROM ",dbn,".gevu_docs gd "
  ," INNER JOIN "  ,dbo,".spip_documents d ON d.id_document = gd.tronc AND gd.id_instant = ",instant 
  ," INNER JOIN "  ,dbo,".spip_documents_rubriques dr ON dr.id_document = d.id_document "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = dr.id_rubrique  AND l.id_instant = ",instant);
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_docsxlieux "
  ," (id_doc, id_lieu, id_instant) "
  ," SELECT gd.id_doc, l.id_lieu, ",instant 
  ," FROM ",dbn,".gevu_docs gd "
  ," INNER JOIN "  ,dbo,".spip_documents d ON d.id_document = gd.tronc AND gd.id_instant = ",instant 
  ," INNER JOIN "  ,dbo,".spip_documents_articles da ON da.id_document = d.id_document "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = da.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant);
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  
	-- OBSERVATIONS
	-- on ne conserve que le champ photo
	-- les champs 'fichier' et 'doc' devront être ajouter à la table gevu_doc
	SET @sql = Concat("INSERT INTO ",dbn,".gevu_observations "
  ," (id_lieu, id_instant, id_reponse, num_marker, lib, id_critere, id_donnee, maj) "
  ," SELECT l.id_lieu, ",instant,", m.id_mot, fdc2.valeur, fdc3.valeur, c.id_critere, fd.id_donnee, fd.date "
  ," FROM "  ,dbo,".spip_forms_donnees fd "
  ," INNER JOIN "  ,dbo,".spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee "
  ," INNER JOIN "  ,dbo,".spip_articles a ON a.id_article = fda.id_article "
  ," INNER JOIN ",dbn,".gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = ",instant 
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = 'ligne_1' "
  ," INNER JOIN "  ,dbo,".spip_mots m ON m.titre = fdc1.valeur "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee	AND fdc2.champ = 'ligne_2' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee	AND fdc3.champ = 'ligne_3' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee	AND fdc4.champ = 'ligne_4' "
  ," LEFT JOIN "  ,dbo,".spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee	AND fdc5.champ = 'ligne_5' "
  ," LEFT JOIN ",dbn,".gevu_criteres c ON c.ref = fdc5.valeur "
  ," WHERE fd.id_form =67 "
  ," GROUP BY fd.id_donnee");
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  
END;
