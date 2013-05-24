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
	$rs["name"] = "Alcéane";
	while ($row = @mysql_fetch_assoc($result))
	{
		$rs["children"][] = array("name"=>"Antenne - BL","ref"=>"BL","children"=>$row);		
	}
		
	$jsonOutput = json_encode($rs); //JSON_FORCE_OBJECT pour tableau non associatif sous forme d'objet.

	header('Content-type: application/json');
	echo $jsonOutput; //Pour sortir le fichier en json

?>