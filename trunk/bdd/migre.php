<?php
//connexion à l'ancienne base
$ldb = mysql_connect("localhost", "root", "")
    or die("Impossible de se connecter : " . mysql_error());    

   /*
//récupère les lieux à conserver
1. ceux qui sont uniques
INSERT INTO gevu_synchros(id_lieu)
SELECT ss.id_lieu
       FROM( SELECT count( * ) nb, id_rubrique, lib, id_parent, id_lieu
FROM `gevu_lieux`
GROUP BY id_rubrique, lib, id_parent
HAVING nb = 1) ss    

2. ceux qui ont un diagnostic, un probleme ou une remarque
INSERT INTO gevu_synchros(id_lieu)
SELECT ss.lieu
       FROM(
SELECT count(*) nb, l.id_lieu lieu, d.id_lieu l1, o.id_lieu l2, p.id_lieu l3
FROM gevu_lieux l
LEFT JOIN gevu_diagnostics d ON d.id_lieu = l.id_lieu
LEFT JOIN gevu_observations o ON o.id_lieu = l.id_lieu
LEFT JOIN gevu_problemes p ON p.id_lieu = l.id_lieu
GROUP BY l.id_lieu, d.id_lieu, o.id_lieu, p.id_lieu
HAVING d.id_lieu IS NOT NULL OR o.id_lieu IS NOT NULL OR p.id_lieu IS NOT NULL
) ss 
*/

mysql_select_db('gevu_solus');

//création d'un instant
/*
$sql = "INSERT INTO gevu_instants (`maintenant`, `ici`, `id_exi`, `nom`) VALUES
(now(), '-1', 1, 'ajusteBase')";
$result = mysql_query($sql);
if (!$result) die('Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>');
$idInstant = mysql_insert_id();
*/
$idInstant = 13;
echo "Instant : ".mysql_affected_rows()."<br/>";	
    
ajusteBase(0);    
    
$dbN = "gevu_solus";    
    
$dbO = "gevu_alceane";
$idInstant = 1;
migreBase($dbN, $dbO, $idInstant);
    
$dbO = "gevu_pc1";    
$idInstant = 2;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "gevu_pc2";    
$idInstant = 3;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "gevu_trouville";    
$idInstant = 4;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "gevutrouville";    
$idInstant = 5;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "gevutrouville1";    
$idInstant = 6;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "gevutrouville2";    
$idInstant = 7;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "gevutrouvillevoirie";    
$idInstant = 8;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "mundimuzgevu1";    
$idInstant = 9;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "mundimuzgevu2";    
$idInstant = 10;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "mundimuzgevu3";    
$idInstant = 11;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "mundimuzgevu4";    
$idInstant = 12;    
migreBase($dbN, $dbO, $idInstant);


function migreBase($dbN, $dbO, $idInstant){
	
	//création d'un instant
	$sql = "INSERT INTO $dbN.gevu_instants (`maintenant`, `ici`, `id_exi`, `nom`) VALUES
	(now(), '-1', 1, 'Migre_GEVU $dbO')";
	$result = mysql_query($sql);
	if (!$result) die('Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>');
	$idInstant = mysql_insert_id();
	echo "$dbO Instant : ".mysql_affected_rows()."<br/>";	
	
	//LIEUX
	setLieuxHierarchie($dbN, $dbO, $idInstant, 5479, 0, 1);
	/*pour vérifier
	SELECT CONCAT( REPEAT(' ', COUNT(pl.lib) - 1), l.lib) AS name
	FROM gevu_lieux AS l,
	gevu_lieux AS pl
	WHERE l.lft BETWEEN pl.lft AND pl.rgt
	GROUP BY l.lib
	ORDER BY l.lft
	*/
	
	
	//DIAGNOSTICS 
	//on ne conserve que les réponses 'non' et 'sous réserve'
	// pour les questions qui ne sont pas interméfiaire
	$sql = "INSERT INTO $dbN.gevu_diagnostics
	(id_critere, id_reponse, id_instant, id_lieu, id_donnee, maj)
	SELECT c.id_critere, fdc1.valeur, $idInstant, l.id_lieu, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'mot_1' 
		INNER JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'ligne_1' 
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fda.id_donnee = fd.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbN.gevu_criteres c ON c.ref = fdc2.valeur
	WHERE fd.id_form =59
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO DIAGNOSTICS : ".mysql_affected_rows()."<br/>";	
	
	
	//PROBLEMES
	//on ne conserve que le champ photo
	//les champs 'fichier' et 'doc' devront être ajouter à la table gevu_doc
	$sql = "INSERT INTO $dbN.gevu_problemes
	(id_lieu, id_critere, num_marker, mesure, observations, fichier, doc, id_instant, id_donnee, maj)
	SELECT l.id_lieu
	    	, c.id_critere
			, fdc1.valeur
			, fdc11.valeur
			, fdc3.valeur
			, fdc4.valeur
			, fdc6.valeur
			, $idInstant
			, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'ligne_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc11 ON fdc11.id_donnee = fd.id_donnee
			AND fdc11.champ = 'ligne_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc ON fdc.id_donnee = fd.id_donnee
			AND fdc.champ = 'ligne_3'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
			AND fdc3.champ = 'texte_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'mot_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
			AND fdc4.champ = 'fichier_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
		 	AND fdc5.champ = 'ligne_5'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee
		 	AND fdc6.champ = 'ligne_4'	 					 						 
		LEFT JOIN $dbN.gevu_criteres c ON c.ref = fdc.valeur
	WHERE fd.id_form =60
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO PROBLEMES : ".mysql_affected_rows()."<br/>";	
	
	
	//GEOGRAPHIE
	$sql = "INSERT INTO $dbN.gevu_geos
	(id_lieu, id_instant, lat, lng, zoom_min, zoom_max, adresse, id_type_carte, kml, id_donnee, maj)
	SELECT l.id_lieu
			, $idInstant
			, fdc1.valeur
			, fdc2.valeur
			, fdc3.valeur
			, fdc4.valeur
			, fdc5.valeur
			, fdc6.valeur
			, fdc7.valeur
			, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'ligne_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'ligne_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
			AND fdc3.champ = 'ligne_3'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
			AND fdc4.champ = 'ligne_4'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
			AND fdc5.champ = 'ligne_7'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee
			AND fdc6.champ = 'mot_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc7 ON fdc7.id_donnee = fd.id_donnee
			AND fdc7.champ = 'texte_1'
	WHERE fd.id_form = 1
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO GEOGRAPHIE : ".mysql_affected_rows()."<br/>";	
	
	//GEORSS
	$sql = "INSERT INTO $dbN.gevu_georss
	(id_lieu, id_instant, url, id_donnee, maj)
	SELECT l.id_lieu
			, $idInstant
			, fdc1.valeur
			, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'url_1'
	WHERE fd.id_form = 81
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO GEORSS : ".mysql_affected_rows()."<br/>";	
	
	//NIVEAU
	$sql = "INSERT INTO $dbN.gevu_niveaux
	(id_lieu, id_instant, nom, ref, id_reponse_1, id_reponse_2, id_reponse_3, id_donnee, maj)
	SELECT l.id_lieu
			, $idInstant
			, fdc1.valeur
			, fdc2.valeur
			, fdc3.valeur
			, fdc4.valeur
			, fdc5.valeur
			, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'ligne_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'ligne_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
			AND fdc3.champ = 'mot_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
			AND fdc4.champ = 'mot_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
			AND fdc5.champ = 'mot_3'
	WHERE fd.id_form = 35
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO NIVEAU : ".mysql_affected_rows()."<br/>";	
	
	//BATIMENT
	//les contacts devront être retraités
	//(53, 'ligne_10', 12, 'Coordonnées du gardien'
	//les horaires devront être retraité
	$sql = "INSERT INTO $dbN.gevu_batiments
	(id_lieu, id_instant, nom, ref, adresse, commune, pays, code_postal
		, contact_proprietaire, contact_delegataire, contact_gardien
		, horaires_gardien, horaires_batiment
		, superficie_parcelle, superficie_batiment
		, date_achevement, date_depot_permis, date_reha
		, reponse_1
		, reponse_2
		, reponse_3
		, reponse_4
		, reponse_5
		, reponse_6
		, reponse_7
		, reponse_8
		, reponse_9
		, reponse_10
		, reponse_11
		, reponse_12
		, reponse_13
		, reponse_14
		, reponse_15
		, id_donnee, maj)
	SELECT l.id_lieu
		, $idInstant
		, fdc1.valeur
		, fdc2.valeur
		, fdc3.valeur
		, fdc4.valeur
		, fdc5.valeur
		, fdc6.valeur
		, fdc7.valeur
		, fdc8.valeur
		, fdc9.valeur
		, fdc10.valeur
		, fdc11.valeur
		, fdc12.valeur
		, fdc13.valeur
		, fdc14.valeur
		, fdc15.valeur
		, fdc16.valeur
		, fdc17.valeur
		, fdc18.valeur
		, fdc19.valeur
		, fdc20.valeur
		, fdc21.valeur
		, fdc22.valeur
		, fdc23.valeur
		, fdc24.valeur
		, fdc25.valeur
		, fdc26.valeur
		, fdc27.valeur
		, fdc28.valeur
		, fdc29.valeur
		, fdc30.valeur
		, fdc31.valeur
		, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'ligne_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'ligne_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
			AND fdc3.champ = 'ligne_3'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
			AND fdc4.champ = 'ligne_4'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
			AND fdc5.champ = 'ligne_5'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee
			AND fdc6.champ = 'code_postal_1'
	
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc7 ON fdc7.id_donnee = fd.id_donnee
			AND fdc7.champ = 'ligne_6'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc8 ON fdc8.id_donnee = fd.id_donnee
			AND fdc8.champ = 'ligne_7'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc9 ON fdc9.id_donnee = fd.id_donnee
			AND fdc9.champ = 'ligne_10'
			
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc10 ON fdc10.id_donnee = fd.id_donnee
			AND fdc10.champ = 'ligne_9'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc11 ON fdc11.id_donnee = fd.id_donnee
			AND fdc11.champ = 'ligne_11'
	
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc12 ON fdc12.id_donnee = fd.id_donnee
			AND fdc12.champ = 'ligne_13'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc13 ON fdc13.id_donnee = fd.id_donnee
			AND fdc13.champ = 'ligne_12'
	
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc14 ON fdc14.id_donnee = fd.id_donnee
			AND fdc14.champ = 'ligne_14'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc15 ON fdc15.id_donnee = fd.id_donnee
			AND fdc15.champ = 'ligne_15'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc16 ON fdc16.id_donnee = fd.id_donnee
			AND fdc16.champ = 'ligne_16'
			
			
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee
			AND fdc17.champ = 'mot_4'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee
			AND fdc18.champ = 'mot_5'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc19 ON fdc19.id_donnee = fd.id_donnee
			AND fdc19.champ = 'mot_6'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc20 ON fdc20.id_donnee = fd.id_donnee
			AND fdc20.champ = 'mot_7'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc21 ON fdc21.id_donnee = fd.id_donnee
			AND fdc21.champ = 'mot_9'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc22 ON fdc22.id_donnee = fd.id_donnee
			AND fdc22.champ = 'mot_10'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc23 ON fdc23.id_donnee = fd.id_donnee
			AND fdc23.champ = 'mot_12'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc24 ON fdc24.id_donnee = fd.id_donnee
			AND fdc24.champ = 'mot_13'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc25 ON fdc25.id_donnee = fd.id_donnee
			AND fdc25.champ = 'mot_14'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc26 ON fdc26.id_donnee = fd.id_donnee
			AND fdc26.champ = 'mot_15'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc27 ON fdc27.id_donnee = fd.id_donnee
			AND fdc27.champ = 'mot_16'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc28 ON fdc28.id_donnee = fd.id_donnee
			AND fdc28.champ = 'mot_17'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc29 ON fdc29.id_donnee = fd.id_donnee
			AND fdc29.champ = 'mot_25'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc30 ON fdc30.id_donnee = fd.id_donnee
			AND fdc30.champ = 'mot_27'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc31 ON fdc31.id_donnee = fd.id_donnee
			AND fdc31.champ = 'mot_28'		
	WHERE fd.id_form = 53
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO BATIMENT : ".mysql_affected_rows()."<br/>";	
	
	//ETABLISSEMENT
	$sql = "INSERT INTO $dbN.gevu_etablissements
	(id_lieu, id_instant, nom, ref, adresse, commune, pays, code_postal
		, contact_proprietaire, contact_delegataire
		, reponse_1
		, reponse_2
		, reponse_3
		, reponse_4
		, reponse_5
	, id_donnee, maj)
	SELECT l.id_lieu
		, $idInstant
		, fdc1.valeur
		, fdc2.valeur
		, fdc3.valeur
		, fdc4.valeur
		, fdc5.valeur
		, fdc6.valeur
		, fdc7.valeur
		, fdc8.valeur
		, fdc17.valeur
		, fdc18.valeur
		, fdc19.valeur
		, fdc20.valeur
		, fdc21.valeur
		, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'ligne_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'ligne_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
			AND fdc3.champ = 'ligne_3'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
			AND fdc4.champ = 'ligne_4'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
			AND fdc5.champ = 'ligne_5'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee
			AND fdc6.champ = 'code_postal_1'
	
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc7 ON fdc7.id_donnee = fd.id_donnee
			AND fdc7.champ = 'ligne_6'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc8 ON fdc8.id_donnee = fd.id_donnee
			AND fdc8.champ = 'ligne_7'	
			
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee
			AND fdc17.champ = 'mot_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee
			AND fdc18.champ = 'mot_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc19 ON fdc19.id_donnee = fd.id_donnee
			AND fdc19.champ = 'mot_3'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc20 ON fdc20.id_donnee = fd.id_donnee
			AND fdc20.champ = 'select_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc21 ON fdc21.id_donnee = fd.id_donnee
			AND fdc21.champ = 'select_2'
	
			WHERE fd.id_form = 55
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO ETABLISSEMENT : ".mysql_affected_rows()."<br/>";	
	
	//ESPACES
	$sql = "INSERT INTO $dbN.gevu_espaces
	(id_lieu, id_instant, ref
		, id_type_espace
		, reponse_1
		, reponse_2
		, id_type_specifique_int
		, id_type_specifique_ext
		, id_donnee, maj)
	SELECT l.id_lieu
		, $idInstant
		, fdc2.valeur
		, fdc17.valeur
		, fdc18.valeur
		, fdc19.valeur
		, fdc20.valeur
		, fdc21.valeur
		, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'ligne_2'		
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee
			AND fdc17.champ = 'mot_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee
			AND fdc18.champ = 'mot_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc19 ON fdc19.id_donnee = fd.id_donnee
			AND fdc19.champ = 'mot_3'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc20 ON fdc20.id_donnee = fd.id_donnee
			AND fdc20.champ = 'mot_4'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc21 ON fdc21.id_donnee = fd.id_donnee
			AND fdc21.champ = 'mot_5'
	WHERE fd.id_form = 56
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO ESPACES : ".mysql_affected_rows()."<br/>";	
	
	//ESPACES INTERIEURS
	$sql = "INSERT INTO $dbN.gevu_espacesxinterieurs
	(id_lieu, id_instant, nom, ref
		, fonction
		, id_type_specifique_int
		, id_donnee, maj)
	SELECT l.id_lieu
		, $idInstant
		, fdc1.valeur
		, fdc2.valeur
		, fdc3.valeur
		, fdc4.valeur
		, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'ligne_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'ligne_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
			AND fdc3.champ = 'ligne_3'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
			AND fdc4.champ = 'select_2'
	WHERE fd.id_form = 57
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO ESPACES INTERIEURS : ".mysql_affected_rows()."<br/>";	
	
	//PARCELLES
	$sql = "INSERT INTO $dbN.gevu_parcelles
	(id_lieu, id_instant, nom, ref, adresse, commune, pays, code_postal
		, contact_proprietaire
		, reponse_1
		, reponse_2
	, id_donnee, maj)
	SELECT l.id_lieu
		, $idInstant
		, fdc1.valeur
		, fdc2.valeur
		, fdc3.valeur
		, fdc4.valeur
		, fdc5.valeur
		, ''
		, fdc6.valeur
		, fdc7.valeur
		, fdc17.valeur
		, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'ligne_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'ligne_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
			AND fdc3.champ = 'ligne_3'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
			AND fdc4.champ = 'ligne_4'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
			AND fdc5.champ = 'ligne_5'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee
			AND fdc6.champ = 'code_postal_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc7 ON fdc7.id_donnee = fd.id_donnee
			AND fdc7.champ = 'ligne_6'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc8 ON fdc8.id_donnee = fd.id_donnee
			AND fdc8.champ = 'ligne_7'	
			
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee
			AND fdc17.champ = 'mot_1'
	
	WHERE fd.id_form = 58
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO PARCELLES : ".mysql_affected_rows()."<br/>";	
	
	//ESPACES EXTERIEURS
	$sql = "INSERT INTO $dbN.gevu_espacesxexterieurs
	(id_lieu, id_instant, nom, ref
		, fonction
		, id_type_espace
		, id_type_specifique_ext
		, id_donnee, maj)
	SELECT l.id_lieu
		, $idInstant
		, fdc1.valeur
		, fdc2.valeur
		, fdc3.valeur
		, fdc4.valeur
		, fdc4.valeur
		, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'ligne_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'ligne_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
			AND fdc3.champ = 'ligne_3'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
			AND fdc4.champ = 'select_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
			AND fdc5.champ = 'select_2'
	WHERE fd.id_form = 61
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO ESPACES EXTERIEURS : ".mysql_affected_rows()."<br/>";	
	
	//DIAGNOSTICS VOIRIE
	$sql = "INSERT INTO $dbN.gevu_diagnosticsxvoirie
	(id_lieu, id_instant, nom, ref, id_donnee, maj)
	SELECT l.id_lieu
		, $idInstant
		, fdc1.valeur
		, fdc3.valeur
		, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'ligne_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
			AND fdc3.champ = 'ligne_3'
	WHERE fd.id_form = 62
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO DIAGNOSTICS VOIRIE : ".mysql_affected_rows()."<br/>";	
	
	//OBJETS INTERIEURS
	$sql = "INSERT INTO $dbN.gevu_objetsxinterieurs
	(id_lieu, id_instant, nom, ref
		, fonctions
		, reponse_1
		, reponse_2
		, id_type_objet
	, id_donnee, maj)
	SELECT l.id_lieu
		, $idInstant
		, fdc1.valeur
		, fdc2.valeur
		, fdc3.valeur
		, fdc17.valeur
		, fdc18.valeur
		, fdc19.valeur
		, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'ligne_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'ligne_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
			AND fdc3.champ = 'ligne_3'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee
			AND fdc17.champ = 'mot_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee
			AND fdc18.champ = 'mot_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc19 ON fdc19.id_donnee = fd.id_donnee
			AND fdc19.champ = 'mot_3'
	WHERE fd.id_form = 63
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO OBJETS INTERIEURS : ".mysql_affected_rows()."<br/>";	
	
	//OBJETS EXTERIEURS
	$sql = "INSERT INTO $dbN.gevu_objetsxexterieurs
	(id_lieu, id_instant, nom, ref
		, fonctions
		, id_type_objet
		, id_type_objet_ext
	, id_donnee, maj)
	SELECT l.id_lieu
		, $idInstant
		, fdc1.valeur
		, fdc2.valeur
		, fdc3.valeur
		, fdc17.valeur
		, fdc18.valeur
		, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'ligne_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'ligne_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
			AND fdc3.champ = 'ligne_3'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee
			AND fdc17.champ = 'select_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee
			AND fdc18.champ = 'select_2'
	WHERE fd.id_form = 64
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO OBJETS EXTERIEURS : ".mysql_affected_rows()."<br/>";	
	
	//ELEMENTS VOIRIE
	$sql = "INSERT INTO $dbN.gevu_objetsxvoiries
	(id_lieu, id_instant, nom, ref
		, id_type_objet_voirie, id_donnee, maj
	)
	SELECT l.id_lieu
		, $idInstant
		, fdc1.valeur
		, fdc2.valeur
		, fdc18.valeur
		, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'ligne_1'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'ligne_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee
			AND fdc18.champ = 'mot_1'
	WHERE fd.id_form = 69
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO ELEMENTS VOIRIE : ".mysql_affected_rows()."<br/>";	
	
	//DOCUMENTS
	$sql = "INSERT INTO $dbN.gevu_docs
	(tronc, id_instant, path_source, titre, content_type, maj)
	SELECT id_document
		, $idInstant
		, fichier
		, d.titre
		, mime_type
		, d.date 
	FROM $dbO.spip_documents d
		INNER JOIN $dbO.spip_types_documents td ON td.id_type = d.id_type
	";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO DOCUMENTS : ".mysql_affected_rows()."<br/>";	
	
	//DOCUMENTS LIEUX
	$sql = "INSERT INTO $dbN.gevu_docsxlieux
	(id_doc, id_lieu, id_instant)
	SELECT gd.id_doc
		, l.id_lieu
		, $idInstant
	FROM $dbN.gevu_docs gd
		INNER JOIN $dbO.spip_documents d ON d.id_document = gd.tronc AND gd.id_instant = $idInstant
		INNER JOIN $dbO.spip_documents_rubriques dr ON dr.id_document = d.id_document
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = dr.id_rubrique  AND l.id_instant = $idInstant
	";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO DOCUMENTS LIEUX rubrique: ".mysql_affected_rows()."<br/>";	
	
	$sql = "INSERT INTO $dbN.gevu_docsxlieux
	(id_doc, id_lieu, id_instant)
	SELECT gd.id_doc
		, l.id_lieu
		, $idInstant
	FROM $dbN.gevu_docs gd
		INNER JOIN $dbO.spip_documents d ON d.id_document = gd.tronc AND gd.id_instant = $idInstant
		INNER JOIN $dbO.spip_documents_articles da ON da.id_document = d.id_document
		INNER JOIN $dbO.spip_articles a ON a.id_article = da.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
	";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO DOCUMENTS LIEUX article: ".mysql_affected_rows()."<br/>";	
	
	
	//OBSERVATIONS
	//on ne conserve que le champ photo
	//les champs 'fichier' et 'doc' devront être ajouter à la table gevu_doc
	$sql = "INSERT INTO $dbN.gevu_observations
	(id_lieu, id_instant, id_reponse, num_marker, lib, id_critere, id_donnee, maj)
	SELECT l.id_lieu
			, $idInstant
			, m.id_mot
			, fdc2.valeur
			, fdc3.valeur
			, c.id_critere
			, fd.id_donnee, fd.date 
	FROM $dbO.spip_forms_donnees fd
		INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
		INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
		INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
			AND fdc1.champ = 'ligne_1'
		INNER JOIN $dbO.spip_mots m ON m.titre = fdc1.valeur
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
			AND fdc2.champ = 'ligne_2'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
			AND fdc3.champ = 'ligne_3'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
			AND fdc4.champ = 'ligne_4'
		LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
			AND fdc5.champ = 'ligne_5'
		LEFT JOIN $dbN.gevu_criteres c ON c.ref = fdc5.valeur
	WHERE fd.id_form =67
	GROUP BY fd.id_donnee";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "$dbO OBSERVATIONS: ".mysql_affected_rows()."<br/>";	

	echo "FIN IMPORT --------------------<br/>--------------<br/>";	
	
}
	

//pour créer la hiérarchie des lieux
function setLieuxHierarchie($dbN, $dbO, $idInstant, $idRub, $lft, $rgt, $niv=0){
	//http://dev.mysql.com/tech-resources/articles/hierarchical-data.html
	
	//récupère les enfants de la rubrique
	$sql = "SELECT r.id_rubrique, r.titre, r.id_parent
		FROM $dbO.spip_rubriques r
		WHERE id_parent = $idRub";
	$res = mysql_query($sql);
	
   while($row=mysql_fetch_array($res)) {
   	$lft++;
   	$rgt++;
	$arr= setLieuxHierarchie($dbN, $dbO, $idInstant, $row['id_rubrique'], $lft, $rgt, $niv+1);
	if($lft<$arr[0]){
		$rgt=$arr[1]+1;	
	}else{
		$lft=$arr[0];	
		$rgt=$arr[1];		
	}
	$sql = "INSERT INTO $dbN.gevu_lieux
	(id_instant, id_rubrique, lib, id_parent, lft, rgt, niv, maj)
	SELECT $idInstant, r.id_rubrique, r.titre, r.id_parent, $lft, $rgt, $niv, r.maj 
	FROM $dbO.spip_rubriques r
	WHERE id_rubrique = ".$row['id_rubrique'];
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	$lft = $rgt;
	$rgt= $lft+1;	
   }
	return array($lft,$rgt);		
}

function ajusteBase($niv){
	global $idInstant;

	//récupère les lieux du niveau
	$sql = "SELECT l.id_lieu, l.id_rubrique, l.id_parent, l.niv 
		FROM gevu_lieux l
		ORDER BY maj DESC";
	$rs = mysql_query($sql);
	
	while($r=mysql_fetch_array($rs)) {
		//récupère les liens du lieu ayant la même rubrique et le même parent 
		$sql = "SELECT l.id_lieu, l.lib, l.maj, l.lft, l.rgt, l.id_instant
					,count(enfant.id_lieu) nbLieux
          			,count(t1.id_lieu) nbBat
          			,count(t2.id_lieu) nbDiag
          			,count(t3.id_lieu) nbDiagVoi
					,count(t4.id_lieu) nbDoc
					,count(t5.id_lieu) nbEsp
					,count(t6.id_lieu) nbEspEx
					,count(t7.id_lieu) nbEspIn
					,count(t8.id_lieu) nbEtab
					,count(t9.id_lieu) nbGeoR
					,count(t10.id_lieu) nbGeo
					,count(t11.id_lieu) nbNiv
					,count(t12.id_lieu) nbObjExt
					,count(t13.id_lieu) nbObjInt
					,count(t14.id_lieu) nbObjVoi
					,count(t15.id_lieu) nbObs
					,count(t16.id_lieu) nbPar
					,count(t17.id_lieu) nbPro
					,(count(enfant.id_lieu)
			             + count(t1.id_lieu)
			             + count(t2.id_lieu)
			             + count(t3.id_lieu)
			             + count(t4.id_lieu)
			             + count(t5.id_lieu)
			             + count(t6.id_lieu)
			             + count(t7.id_lieu)
			             + count(t8.id_lieu)
			             + count(t9.id_lieu)
			             + count(t10.id_lieu)
			             + count(t11.id_lieu)
			             + count(t12.id_lieu)
			             + count(t13.id_lieu)
			             + count(t14.id_lieu)
			             + count(t15.id_lieu)
			             + count(t16.id_lieu)
			             + count(t17.id_lieu)
			             ) nbTot
						,CONCAT_WS(':',count(enfant.id_lieu)
			             ,count(t1.id_lieu)
			             ,count(t2.id_lieu)
			             ,count(t3.id_lieu)
			             ,count(t4.id_lieu)
			             ,count(t5.id_lieu)
			             ,count(t6.id_lieu)
			             ,count(t7.id_lieu)
			             ,count(t8.id_lieu)
			             ,count(t9.id_lieu)
			             ,count(t10.id_lieu)
			             ,count(t11.id_lieu)
			             ,count(t12.id_lieu)
			             ,count(t13.id_lieu)
			             ,count(t14.id_lieu)
			             ,count(t15.id_lieu)
			             ,count(t16.id_lieu)
			             ,count(t17.id_lieu)
			             ) k
				FROM gevu_lieux l
					LEFT JOIN gevu_lieux AS enfant ON enfant.lft > l.lft AND enfant.rgt < l.rgt 
						AND enfant.niv = l.niv+1 AND enfant.id_instant = l.id_instant
			        LEFT JOIN gevu_batiments t1 ON t1.id_lieu = l.id_lieu
					LEFT JOIN gevu_diagnostics t2 ON t2.id_lieu = l.id_lieu
					LEFT JOIN gevu_diagnosticsxvoirie t3 ON t3.id_lieu = l.id_lieu
					LEFT JOIN gevu_docsxlieux t4 ON t4.id_lieu = l.id_lieu
					LEFT JOIN gevu_espaces t5 ON t5.id_lieu = l.id_lieu
					LEFT JOIN gevu_espacesxexterieurs t6 ON t6.id_lieu = l.id_lieu
					LEFT JOIN gevu_espacesxinterieurs t7 ON t7.id_lieu = l.id_lieu
					LEFT JOIN gevu_etablissements t8 ON t8.id_lieu = l.id_lieu
					LEFT JOIN gevu_georss t9 ON t9.id_lieu = l.id_lieu
					LEFT JOIN gevu_geos t10 ON t10.id_lieu = l.id_lieu
					LEFT JOIN gevu_niveaux t11 ON t11.id_lieu = l.id_lieu
					LEFT JOIN gevu_objetsxexterieurs t12 ON t12.id_lieu = l.id_lieu
					LEFT JOIN gevu_objetsxinterieurs t13 ON t13.id_lieu = l.id_lieu
					LEFT JOIN gevu_objetsxvoiries t14 ON t14.id_lieu = l.id_lieu
					LEFT JOIN gevu_observations t15 ON t15.id_lieu = l.id_lieu
					LEFT JOIN gevu_parcelles t16 ON t16.id_lieu = l.id_lieu
					LEFT JOIN gevu_problemes t17 ON t17.id_lieu = l.id_lieu
				WHERE l.id_lieu IN (
					SELECT id_lieu
					FROM gevu_lieux
					WHERE l.id_rubrique = ".$r['id_rubrique']." AND l.id_parent = ".$r['id_parent']."
					)
				GROUP BY l.id_lieu 
				ORDER BY  l.lib, l.maj DESC"; 
		$rs1 = mysql_query($sql);
		$oLib="";
		$oDate="";
		$oLieu="";
		$oRgt="";
		$oK="";
		while($r1=mysql_fetch_array($rs1)) {
			if($oLib!=$r1['lib']){
				//le lieux le plus récent avec un nom différent sert de référence
				$sql="UPDATE gevu_lieux SET id_instant=".$idInstant." WHERE id_lieu=".$r1['id_lieu'];
				mysql_query($sql);
				//on met à jour les instants de tout les lieux enfants
				$sql="UPDATE gevu_lieux SET id_instant=".$idInstant." 
					WHERE id_lieu IN (
						SELECT id_lieu
						FROM gevu_lieux
						WHERE lft BETWEEN ".$r1['lft']." and ".$r1['rgt']." AND id_instant = ".$r1['id_instant'];			
				$oLieu = $r1['id_lieu'];
				$oRgt = $r1['rgt'];
				$oK = $r1['k'];
			}else{
				//on vérifie si les lieux sont identiques
				if($oDate==$r1['maj'] && $oK==$r1['k'] ){
					//on supprime le lieu et tout ces élements
				}else{
					//on ajoute à la référence
					//les éléments lié au lieu ayant le même nom que la référence
					if($r1['nbLieux']>0){
						//récupèration des éléments liés
						$sql="SELECT enfant.* 
							FROM lieux l
							LEFT JOIN gevu_lieux AS enfant ON enfant.lft > l.lft AND enfant.rgt < l.rgt 
								AND enfant.niv = l.niv+1 AND enfant.id_instant = l.id_instant
							WHERE l.id_lieux = ".$r1['id_lieu'];
						$rs2 = mysql_query($sql);
						while($r2=mysql_fetch_array($rs2)){
							//on ajoute la branche à la référence
							ChangeBranche($r1['id_lieu'], $oRgt, $r1['id_lieu'], $idInstant); 
						}
					}
          			if($r1['nbBat']>0){
						$sql="UPDATE gevu_batiments SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
          			}
          			if($r1['nbDiag']>0){
			        	$sql="UPDATE gevu_diagnostics SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
					}
          			if($r1['nbDiagVoi']>0){
						$sql="UPDATE gevu_diagnosticsxvoirie SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
					}
          			if($r1['nbDoc']>0){
			        	$sql="UPDATE gevu_docsxlieux SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
					}
          			if($r1['nbEsp']>0){
						$sql="UPDATE gevu_espaces SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			          								
					}
          			if($r1['nbEspEx']>0){
			        	$sql="UPDATE gevu_espacesxexterieurs SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			       			
					}
          			if($r1['nbEspIn']>0){
			        	$sql="UPDATE gevu_espacesxinterieurs SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
					}
          			if($r1['nbEtab']>0){
			        	$sql="UPDATE gevu_etablissements SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
					}
          			if($r1['nbGeoR']>0){
						$sql="UPDATE gevu_georss SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			         								
					}
          			if($r1['nbGeo']>0){
			        	$sql="UPDATE gevu_geos SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			          			
					}
          			if($r1['nbNiv']>0){
			        	$sql="UPDATE gevu_niveaux SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			          			
					}
          			if($r1['nbObjExt']>0){
			        	$sql="UPDATE gevu_objetsxexterieurs SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			         			
					}
          			if($r1['nbObjInt']>0){
			        	$sql="UPDATE gevu_objetsxinterieurs SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
          			
					}
          			if($r1['nbObjVoi']>0){
			        	$sql="UPDATE gevu_objetsxvoiries SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
			        
					}
          			if($r1['nbObs']>0){
						$sql="UPDATE gevu_observations SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
          								
					}
          			if($r1['nbPar']>0){
			        	$sql="UPDATE gevu_parcelles SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
          			
					}
          			if($r1['nbPro']>0){
			        	$sql="UPDATE gevu_problemes SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
          			
					}			
				}else{
					if($r1['nbTot']==0){
						//on supprime la donnée
						$sql="UPDATE gevu_lieux SET id_instant=-1 WHERE id_lieu=".$r1['id_lieu'];
						mysql_query($sql);									
					}
				}
			}
			$oLib=$r1['lib'];
			$oDate=$r1['maj'];
		}
	}
}

function ChangeBranche($id_lieu, $rgt, $idInstantSource, $idInstantDest){

	$sql="UPDATE gevu_lieux SET rgt = rgt + 2, id_instant=".$idInstantDest." WHERE rgt > ".$rgt." AND id_instant=".$idInstantSource;
	mysql_query($sql);
	$sql="UPDATE gevu_lieux SET lft = lft + 2, id_instant=".$idInstantDest." WHERE lft > ".$rgt." AND id_instant=".$idInstantSource;
	mysql_query($sql);
	$sql="UPDATE gevu_lieux SET lft = ".$rgt." + 1, rgt = ".$rgt." + 1, id_instant=".$idInstantDest." WHERE id_lieu > ".$id_lieu."";
	mysql_query($sql);

}

function DeleteLieu($id_lieu){

	$sql="UPDATE gevu_lieux SET rgt = rgt + 2, id_instant=".$idInstantDest." WHERE rgt > ".$rgt." AND id_instant=".$idInstantSource;
	mysql_query($sql);
	$sql="UPDATE gevu_lieux SET lft = lft + 2, id_instant=".$idInstantDest." WHERE lft > ".$rgt." AND id_instant=".$idInstantSource;
	mysql_query($sql);
	$sql="UPDATE gevu_lieux SET lft = ".$rgt." + 1, rgt = ".$rgt." + 1, id_instant=".$idInstantDest." WHERE id_lieu > ".$id_lieu."";
	mysql_query($sql);

}

//fermeture de la connexion    
mysql_close($ldb);
?>