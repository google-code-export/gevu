<?php

require_once 'codes.php';

	$query = 'SELECT Type_Logement, COUNT(Type_Logement) 
FROM gevu_stats
GROUP BY Type_Logement';

	$result = mysql_query($query);
		if (!$result) 
		{
			die('Invalid query: ' . mysql_error());
		}
	$rs = @mysql_fetch_assoc($result);
		
	$jsonOutput = json_encode($rs,JSON_FORCE_OBJECT); //JSON_FORCE_OBJECT pour tableau non associatif sous forme d'objet.

	header('Content-type: application/json');
	echo $jsonOutput; //Pour sortir le fichier en json

?>