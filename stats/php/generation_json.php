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
	$rs["children"][] = array("name"=>"Antenne - BL","ref"=>"BL","children"=>array());
	$rs["children"][] = array("name"=>"Antenne - CA","ref"=>"CA","children"=>array());
	$rs["children"][] = array("name"=>"Antenne - CV","ref"=>"CV","children"=>array());
	$rs["children"][] = array("name"=>"Antenne - MR","ref"=>"MR","children"=>array());
	$rs["children"][] = array("name"=>"Antenne - QS","ref"=>"QS","children"=>array());
	

	$query = 'SELECT a.id_lieu, a.ref
		, la.lib, la.lft, la.rgt
		, COUNT(DISTINCT s.id_stat) as size, s.Type_Logement as name 
		FROM gevu_antennes a
		  INNER JOIN gevu_lieux la ON la.id_lieu = a.id_lieu
		  INNER JOIN gevu_lieux lg ON lg.lft BETWEEN la.lft AND la.rgt
		  INNER JOIN gevu_stats s ON s.id_lieu = lg.id_lieu 
		WHERE a.ref != ""
		GROUP BY a.id_lieu, s.Type_Logement
		ORDER BY ref';
	$nom = "Types et nombres de logements";
	getData($query, $nom);
	
	/*
	$query = 'SELECT Type_financement as "Name : Type Financement", count(Type_financement) as size
FROM gevu_stats
GROUP BY Type_financement';
	getData($query);
	
	$query = 'SELECT Categorie_Module, Occupation AS  "Name : Occupation", COUNT( Occupation ) AS size
FROM gevu_stats
GROUP BY Occupation, Categorie_Module';
	getData($query);
	*/
	
	$jsonOutput = json_encode($rs); //JSON_FORCE_OBJECT pour tableau non associatif sous forme d'objet.

	header('Content-type: application/json');
	echo $jsonOutput; //Pour sortir le fichier en json

	
	function getData($query, $nom){
		global $rs;
		$rsTotal = array();		
		$result = mysql_query($query);
		if (!$result)
		{
			die('Invalid query: ' . mysql_error());
		}
	
		$refA = "";
		while ($row = @mysql_fetch_assoc($result))
		{
			if($refA != $row["ref"]){
				if($refA != ""){
					//on ajoute le tableau dans le tableau global
					for ($i = 0; $i < count($rs["children"]); $i++) {
						if($rs["children"][$i]["ref"]==$refA){
							$rs["children"][$i]["children"] = $data;
						}						
					}
				}
				//on crée le tableau correspondant à l'antenne
				$data = array("name"=>$nom, "children"=>array());
				$refA = $row["ref"];
			}	
			//on ajoute les donnée dans le tableau
			$data["children"][] = array("name"=>$row["name"],"size"=>$row["size"]);
			//on calcule la somme de la caractéristique
			if(isset($row["name"]))$rsTotal[$row["name"]] += $row["size"];
			else $rsTotal[$row["name"]] = $row["size"];
		}
		//on ajoute le tableau dans le tableau global
		for ($i = 0; $i < count($rs["children"]); $i++) {
			if($rs["children"][$i]["ref"]==$refA){
				$rs["children"][$i]["children"] = $data;
			}
		}
		//on ajoute la somme dans le tableau
	
	}
	
	
?>