<?php

require_once 'codes.php';

	/*$query = 'SELECT id_lieu, Type_Logement, COUNT(Type_Logement) as size, Type_financement, count(Type_financement) as size,Occupation, count(Occupation) as size
FROM gevu_stats
GROUP BY Type_Logement, Type_financement, Occupation';*/
//WHERE id_lieu in (3,3520,8404,13311,17064)

	$query = 'SELECT id_lieu, Type_Logement as "Name : Type Logement", COUNT(Type_Logement) as size
FROM gevu_stats
GROUP BY Type_Logement';

	$query1 = 'SELECT id_lieu, Type_financement as "Name : Type Financement", count(Type_financement) as size
FROM gevu_stats
GROUP BY Type_financement';

	$query2 = 'SELECT id_lieu Occupation as "Name : Occupation", count(Occupation) as size
FROM gevu_stats
GROUP BY Occupation';

	$result = mysql_query($query);
		if (!$result) 
		{
			die('Invalid query: ' . mysql_error());
		}
		
	$result1 = mysql_query($query1);
		if (!$result) 
		{
			die('Invalid query: ' . mysql_error());
		}
		
	$result2 = mysql_query($query2);
		if (!$result) 
		{
			die('Invalid query: ' . mysql_error());
		}
		
	//function getAlceane(){
	$rs["name"] = "Alcéane";
	//}
	
	//$alceane = getAlceane();
	
	
	while ($row = @mysql_fetch_assoc($result))
	{
	
		$rs["children"][] = array("name"=>"Antenne - BL","ref"=>"BL","children"=>$row);		
		$rs["children"][] = array("name"=>"Antenne - CA","ref"=>"CA","children"=>$row);
		$rs["children"][] = array("name"=>"Antenne - CV","ref"=>"CV","children"=>$row);	
		$rs["children"][] = array("name"=>"Antenne - MR","ref"=>"MR","children"=>$row);	
		$rs["children"][] = array("name"=>"Antenne - QS","ref"=>"QS","children"=>$row);			
	}
	
	while ($row = @mysql_fetch_assoc($result1))
	{
	
		$rs["children"][] = array("name"=>"Antenne - BL","ref"=>"BL","children"=>$row);		
		$rs["children"][] = array("name"=>"Antenne - CA","ref"=>"CA","children"=>$row);
		$rs["children"][] = array("name"=>"Antenne - CV","ref"=>"CV","children"=>$row);	
		$rs["children"][] = array("name"=>"Antenne - MR","ref"=>"MR","children"=>$row);	
		$rs["children"][] = array("name"=>"Antenne - QS","ref"=>"QS","children"=>$row);			
	}
		
	while ($row = @mysql_fetch_assoc($result2))
	{
	
		$rs["children"][] = array("name"=>"Antenne - BL","ref"=>"BL","children"=>$row);		
		$rs["children"][] = array("name"=>"Antenne - CA","ref"=>"CA","children"=>$row);
		$rs["children"][] = array("name"=>"Antenne - CV","ref"=>"CV","children"=>$row);	
		$rs["children"][] = array("name"=>"Antenne - MR","ref"=>"MR","children"=>$row);	
		$rs["children"][] = array("name"=>"Antenne - QS","ref"=>"QS","children"=>$row);			
	}
	
	$jsonOutput = json_encode($rs); //JSON_FORCE_OBJECT pour tableau non associatif sous forme d'objet.

	header('Content-type: application/json');
	echo $jsonOutput; //Pour sortir le fichier en json

?>