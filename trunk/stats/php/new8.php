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
			

	$query = 'SELECT a.id_lieu, a.ref, la.lib, la.lft, la.rgt, Type_Logement AS TypeLogement, COUNT( DISTINCT s.id_stat ) AS total
FROM gevu_antennes a
INNER JOIN gevu_lieux la ON la.id_lieu = a.id_lieu
INNER JOIN gevu_lieux lg ON lg.lft
BETWEEN la.lft
AND la.rgt
INNER JOIN gevu_stats s ON s.id_lieu = lg.id_lieu
WHERE a.ref !=  ""
GROUP BY a.id_lieu, Type_Logement
ORDER BY ref';
	getData($query);
	
	$query = 'SELECT a.id_lieu, a.ref, la.lib, la.lft, la.rgt, Type_financement AS TypeFinancement, COUNT( DISTINCT s.id_stat ) AS total
FROM gevu_antennes a
INNER JOIN gevu_lieux la ON la.id_lieu = a.id_lieu
INNER JOIN gevu_lieux lg ON lg.lft
BETWEEN la.lft
AND la.rgt
INNER JOIN gevu_stats s ON s.id_lieu = lg.id_lieu
WHERE a.ref !=  ""
GROUP BY a.id_lieu, Type_Financement
ORDER BY ref';
	getData($query);
	
	$query = 'SELECT a.id_lieu, a.ref, la.lib, la.lft, la.rgt, Occupation AS Occupation, COUNT( DISTINCT s.id_stat ) AS total
FROM gevu_antennes a
INNER JOIN gevu_lieux la ON la.id_lieu = a.id_lieu
INNER JOIN gevu_lieux lg ON lg.lft
BETWEEN la.lft
AND la.rgt
INNER JOIN gevu_stats s ON s.id_lieu = lg.id_lieu
WHERE a.ref !=  ""
GROUP BY a.id_lieu, Occupation
ORDER BY ref';
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
	
			$rs["children"][] = array($row);
			$rs["antenne"][] = array($result);

		}
	
	
	}
	
	
?>