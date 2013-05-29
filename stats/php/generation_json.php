<?php

require_once 'codes.php';

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
	
	$query1 = 'SELECT a.id_lieu, a.ref, la.lib, la.lft, la.rgt, COUNT( DISTINCT s.id_stat ) AS size, s.Type_financement AS name
FROM gevu_antennes a
INNER JOIN gevu_lieux la ON la.id_lieu = a.id_lieu
INNER JOIN gevu_lieux lg ON lg.lft
BETWEEN la.lft
AND la.rgt
INNER JOIN gevu_stats s ON s.id_lieu = lg.id_lieu
WHERE a.ref !=  ""
GROUP BY a.id_lieu, s.Type_financement
ORDER BY ref';
	$nom1 = "Types de financement";
	financement($query1, $nom1);
	
	$query2 = 'SELECT a.id_lieu, a.ref, la.lib, la.lft, la.rgt, COUNT( DISTINCT s.id_stat ) AS size, s.Occupation AS name
FROM gevu_antennes a
INNER JOIN gevu_lieux la ON la.id_lieu = a.id_lieu
INNER JOIN gevu_lieux lg ON lg.lft
BETWEEN la.lft
AND la.rgt
INNER JOIN gevu_stats s ON s.id_lieu = lg.id_lieu
WHERE a.ref !=  ""
GROUP BY a.id_lieu, s.Occupation
ORDER BY ref';
	$nom2 = "Occupation";
	occupation($query2, $nom2);
	
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
			//on ajoute les données dans le tableau
			$data["children"][] = array("name"=>$row["name"],"size"=>$row["size"],"total"=>$row["ref"].$row["size"]+$row["size"]+$row["size"]+$row["size"]+$row["size"]);
			//on calcule la somme de la caractéristique
			if(isset($rsTotal[$row["name"]]))$rsTotal[$row["name"]] += $row["size"];
			else $rsTotal[$row["name"]] = $row["size"];
		}
		//on ajoute le tableau dans le tableau global
		for ($i = 0; $i < count($rs["children"]); $i++) {
			if($rs["children"][$i]["ref"]==$refA){
				$rs["children"][$i]["children"] = $data;
			}
		}
		//on ajoute la somme dans le tableau
			//voir ajout des données dans tableau
	}
	
	function financement($query1, $nom1){
		global $rs;
		$rsTotal1 = array();		
		$result1 = mysql_query($query1);
		if (!$result1)
		{
			die('Invalid query: ' . mysql_error());
		}
	
		$refB = "";
		while ($row1 = @mysql_fetch_assoc($result1))
		{
			if($refB != $row1["ref"]){
				if($refB != ""){
					//on ajoute le tableau dans le tableau global
					for ($i = 0; $i < count($rs["children"]); $i++) {
						if($rs["children"][$i]["ref"]==$refB){
							$rs["children"][$i]["children"] = $data;
						}						
					}
				}
				//on crée le tableau correspondant à l'antenne
				$data = array("name"=>$nom1, "children"=>array());
				$refB = $row1["ref"];
			}	
			//on ajoute les donnée dans le tableau
			$data["children"][] = array("name"=>$row1["name"],"size"=>$row1["size"],"total"=>$row["size"]+$row["size"]+$row["size"]+$row["size"]+$row["size"]);
			//on calcule la somme de la caractéristique
			if(isset($rsTotal1[$row1["name"]]))$rsTotal1[$row1["name"]] += $row1["size"];
			else $rsTotal1[$row1["name"]] = $row1["size"];
		}
		//on ajoute le tableau dans le tableau global
		for ($i = 0; $i < count($rs["children"]); $i++) {
			if($rs["children"][$i]["ref"]==$refB){
				$rs["children"][$i]["children"] = $data;
			}
		}
		//on ajoute la somme dans le tableau
	
	}

	function occupation($query2, $nom2){
		global $rs;
		$rsTotal2 = array();		
		$result2 = mysql_query($query2);
		if (!$result2)
		{
			die('Invalid query: ' . mysql_error());
		}
	
		$refC = "";
		while ($row2 = @mysql_fetch_assoc($result2))
		{
			if($refC != $row2["ref"]){
				if($refC != ""){
					//on ajoute le tableau dans le tableau global
					for ($i = 0; $i < count($rs["children"]); $i++) {
						if($rs["children"][$i]["ref"]==$refC){
							$rs["children"][$i]["children"] = $data;
						}						
					}
				}
				//on crée le tableau correspondant à l'antenne
				$data = array("name"=>$nom2, "children"=>array());
				$refC = $row2["ref"];
			}	
			//on ajoute les donnée dans le tableau
			$data["children"][] = array("name"=>$row2["name"],"size"=>$row2["size"],"total"=>$row["size"]+$row["size"]+$row["size"]+$row["size"]+$row["size"]);
			//on calcule la somme de la caractéristique
			if(isset($rsTotal2[$row2["name"]]))$rsTotal2[$row2["name"]] += $row2["size"];
			else $rsTotal2[$row2["name"]] = $row2["size"];
		}
		//on ajoute le tableau dans le tableau global
		for ($i = 0; $i < count($rs["children"]); $i++) {
			if($rs["children"][$i]["ref"]==$refC){
				$rs["children"][$i]["children"] = $data;
			}
		}
		//on ajoute la somme dans le tableau
	
	}	
	
	function document(){
	$doc = array (getData+financement+occupation);
	}
	document();
?>