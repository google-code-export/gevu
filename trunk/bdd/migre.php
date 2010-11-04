<?php
//connexion à l'ancienne base
$lO = mysql_connect("localhost", "root", "")
    or die("Impossible de se connecter : " . mysql_error());

//création d'un instant
$sql = "INSERT INTO gevu_solus.gevu_instants (`maintenant`, `ici`, `id_exi`, `nom`) VALUES
(now(), '-1', 1, 'Migre_Diagnostics')";
$result = mysql_query($sql);
$idInstant = mysql_insert_id();

//DIAGNOSTICS
//on ne conserve que les réponses 'non' et 'sous réserve'
// pour les questions qui ne sont pas interméfiaire
$sql = "INSERT INTO gevu_solus.gevu_diagnostics
(id_critere, id_reponse, id_instant, id_lieu)
SELECT c.id_critere, fdc1.valeur, $idInstant, fdc3.id_article 
FROM gevutrouville1.spip_forms_donnees fd
INNER JOIN gevutrouville1.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
	AND fdc1.champ = 'mot_1' AND fdc1.valeur IN ( 141, 2 )
INNER JOIN gevutrouville1.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
	AND fdc2.champ = 'ligne_1' AND INSTR( fdc2.valeur, '_qi_' ) =0
INNER JOIN gevutrouville1.spip_forms_donnees_articles fdc3 ON fdc3.id_donnee = fd.id_donnee
LEFT JOIN gevu_solus.gevu_criteres c ON c.ref = fdc2.valeur
WHERE fd.id_form =59";
$result = mysql_query($sql);

//PROBLEMES
//on ne conserve que les réponses 'non' et 'sous réserve'
// pour les questions qui ne sont pas interméfiaire
$sql = "INSERT INTO gevu_solus.gevu_diagnostics
(id_critere, id_reponse, id_instant, id_lieu)



SELECT fda.id_article
		, fd.id_donnee idDon
    	, fdc.valeur idCont
		, fdc1.valeur idPbPlan
		, fdc11.valeur mesure
		, fdc2.valeur photo
		, fdc3.valeur obs
		, fdc4.valeur fichier
		, fdc5.valeur RepProb
		, fdc6.valeur doc
FROM spip_forms_donnees fd
	INNER JOIN spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
	LEFT JOIN spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
		AND fdc1.champ = 'ligne_1'
	LEFT JOIN spip_forms_donnees_champs fdc11 ON fdc11.id_donnee = fd.id_donnee
		AND fdc11.champ = 'ligne_2'
	LEFT JOIN spip_forms_donnees_champs fdc ON fdc.id_donnee = fd.id_donnee
		AND fdc.champ = 'ligne_3'
	LEFT JOIN spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
		AND fdc3.champ = 'texte_1'
	LEFT JOIN spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
		AND fdc2.champ = 'mot_1'
	LEFT JOIN spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
		AND fdc4.champ = 'fichier_1'
	LEFT JOIN spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
	 	AND fdc5.champ = 'ligne_5'
	LEFT JOIN spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee
	 	AND fdc6.champ = 'ligne_4'	 					 						 
WHERE fd.id_form =60
GROUP BY fd.id_donnee
$result = mysql_query($sql);


//fermeture de la connexion    
mysql_close($lO);
?>