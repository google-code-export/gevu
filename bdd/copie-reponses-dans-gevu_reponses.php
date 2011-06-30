<?php
	$ldb = mysql_connect("localhost", "root", "") or die("Impossible de se connecter : " . mysql_error());    
	mysql_select_db("gevu");
	
	// clear thr table
	$sql = "TRUNCATE TABLE gevu_reponses";
	$result = mysql_query($sql);
	if (!$result) die('Requête invalide : ' . mysql_error().'<br />'.$sql.'<br />');
	
	
	// finds words types
	$sql = "INSERT INTO gevu.motsclefs(id_motclef, titre, type)
				SELECT s.id_mot, s.titre, s.id_groupe FROM gevu_trouville_voirie1.spip_mots s
				WHERE s.id_groupe in (SELECT s.extra_info e FROM gevu_trouville_voirie1.spip_forms_champs s
									  WHERE s.type=\"mot\"
									  GROUP BY extra_info)";
	$result = mysql_query($sql);
	
	mysql_close($ldb);		
?>