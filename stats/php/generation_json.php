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
		
$dom = new DOMDocument('1.0', 'UTF-8'); //On créer un nouveau document en utf8

echo json_encode($query,JSON_FORCE_OBJECT); //JSON_FORCE_OBJECT pour tableau non associatif sous forme d'objet.

$jsonOutput = $dom->saveJSON(); 
header('Content-type: application/json');
echo $jsonOutput; //Pour sortir le fichier en json
?>