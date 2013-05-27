<?php

require_once 'codes.php';

	/*$query = 'SELECT id_lieu, Type_Logement, COUNT(Type_Logement) as size, Type_financement, count(Type_financement) as size,Occupation, count(Occupation) as size
FROM gevu_stats
GROUP BY Type_Logement, Type_financement, Occupation';*/
//WHERE id_lieu in (3,3520,8404,13311,17064)

/*SELECT ref, Type_Logement as "Name : Type Logement", COUNT(Type_Logement) as size
FROM gevu_stats s inner join gevu_antennes a on a.id_lieu = s.id_lieu
GROUP BY Type_Logement*/


	$rs["name"] = "Alcéane";
			

	$query = 'SELECT Type_Logement as "Name : Type Logement", COUNT(Type_Logement) as size
FROM gevu_stats
GROUP BY Type_Logement';
	getData($query);
	
	$query = 'SELECT Type_financement as "Name : Type Financement", count(Type_financement) as size
FROM gevu_stats
GROUP BY Type_financement';
	getData($query);
	
	$query = 'SELECT Categorie_Module, Occupation AS  "Name : Occupation", COUNT( Occupation ) AS size
FROM gevu_stats
GROUP BY Occupation, Categorie_Module';
	getData($query);
	
	$jsonOutput = json_encode($rs); //JSON_FORCE_OBJECT pour tableau non associatif sous forme d'objet.

	header('Content-type: application/json');
	echo $jsonOutput; //Pour sortir le fichier en json

	
	function getData($query){
		global $rs;
		
		$result = mysql_query($query);
		if (!$result)
		{
			die('Invalid query: ' . mysql_error());
		}
	
		while ($row = @mysql_fetch_assoc($result))
		{
	
			$rs["children"][] = array("name"=>"Antenne - BL","ref"=>"BL","children"=>$row);
			$rs["children"][] = array("name"=>"Antenne - CA","ref"=>"CA","children"=>$row);
			$rs["children"][] = array("name"=>"Antenne - CV","ref"=>"CV","children"=>$row);
			$rs["children"][] = array("name"=>"Antenne - MR","ref"=>"MR","children"=>$row);
			$rs["children"][] = array("name"=>"Antenne - QS","ref"=>"QS","children"=>$row);
		}
	
	
	}
	
	
?>