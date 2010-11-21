<?php
//connexion à l'ancienne base
$ldb = mysql_connect("localhost", "root", "")
    or die("Impossible de se connecter : " . mysql_error());    
mysql_select_db('gevu_solus');

$pdo = new PDO("mysql:host=localhost;port='3306';dbname=gevu_solus", 'root', '');

$dbN = "gevu_solus";    


$dbO = "gevu_alceane";
//migreBase($dbN, $dbO);
//ajusteBase(1);

$dbO = "gevu_pc1";    
//migreBase($dbN, $dbO);
ajusteBase(1);


$dbO = "gevu_pc2";    
migreBase($dbN, $dbO);
ajusteBase(1);

$dbO = "gevu_trouville";    
$idInstant = 4;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "gevutrouville";    
$idInstant = 5;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "gevutrouville1";    
$idInstant = 6;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "gevutrouville2";    
$idInstant = 7;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "gevutrouvillevoirie";    
$idInstant = 8;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "mundimuzgevu1";    
$idInstant = 9;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "mundimuzgevu2";    
$idInstant = 10;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "mundimuzgevu3";    
$idInstant = 11;    
migreBase($dbN, $dbO, $idInstant);

$dbO = "mundimuzgevu4";    
$idInstant = 12;    
migreBase($dbN, $dbO, $idInstant);


function migreBase($dbN, $dbO){
	
	//création d'un instant
	$sql = "INSERT INTO $dbN.gevu_instants (`maintenant`, `ici`, `id_exi`, `nom`) VALUES
	(now(), '-1', 1, 'Migre_GEVU $dbO')";
	$result = mysql_query($sql);
	if (!$result) die('Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>');
	$idInstant = mysql_insert_id();
	echo "$dbO Instant : ".mysql_affected_rows()."<br/>";	

	//mise à jour de l'instant de l'univers
	//récupère les lieux
	$sql = "UPDATE gevu_lieux SET id_instant=".$idInstant." WHERE id_lieu = 1";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	
	//récupère les propriété de l'univers
	$sql = "SELECT id_lieu, lft, rgt FROM $dbN.gevu_lieux WHERE id_lieu = 1";
	$result = mysql_query($sql);
	if (!$result) die('Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>');
	$r=mysql_fetch_array($result);
	echo "$dbO univers : ".$r['lft']." - ".$r['rgt']."<br/>";		
	
	//LIEUX
	setLieuxHierarchie($r['id_lieu'], $dbN, $dbO, $idInstant, 5479, $r['lft'], $r['rgt']);
	/*pour vérifier
	SELECT CONCAT( REPEAT(' ', COUNT(pl.lib) - 1), l.lib) AS name
	FROM gevu_lieux AS l,
	gevu_lieux AS pl
	WHERE l.lft BETWEEN pl.lft AND pl.rgt
	GROUP BY l.lib
	ORDER BY l.lft
	*/
	
	//on met à jour le droite de l'univers
	$sql = "SELECT MAX(rgt)+1 m FROM gevu_lieux";
	$result = mysql_query($sql);
	$r=mysql_fetch_array($result);
	$sql = "UPDATE gevu_lieux SET rgt=".$r['m']." WHERE id_lieu = 1";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	
	global $pdo;
	
	$stmt = $pdo->prepare("CALL SPIPtoGEVU(p1,p2,p3)");
	$stmt->bindParam('p1', $dbN, PDO::PARAM_STR);
	$stmt->bindParam('p2', $dbO, PDO::PARAM_STR);
	$stmt->bindParam('p3', $idInstant, PDO::PARAM_INT);
	$stmt->execute();
	
	echo "FIN IMPORT --------------------<br/>--------------<br/>";	
	
}
	

//pour créer la hiérarchie des lieux
function setLieuxHierarchie($idParent, $dbN, $dbO, $idInstant, $idRub, $lft, $rgt, $niv=1){
	//http://dev.mysql.com/tech-resources/articles/hierarchical-data.html
	
	//récupère les enfants de la rubrique
	$sql = "SELECT r.id_rubrique, r.titre, r.id_parent
		FROM $dbO.spip_rubriques r
		WHERE id_parent = $idRub";
	$res = mysql_query($sql);
	
   while($row=mysql_fetch_array($res)) {
   	$lft++;
   	$rgt++;
   	
	//on insert un lieu pour récupérer l'identifiant
   	$sql = "INSERT INTO $dbN.gevu_lieux
	(id_instant, lieu_parent, id_rubrique, lib, id_parent, lft, rgt, niv, maj)
	SELECT $idInstant, $idParent, r.id_rubrique, r.titre, r.id_parent, $lft, $rgt, $niv, r.maj 
	FROM $dbO.spip_rubriques r
	WHERE id_rubrique = ".$row['id_rubrique'];
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
   	$idLieu = mysql_insert_id();
   	
   	echo str_repeat("- ", $niv).$row['id_rubrique'].' -> lft: '.$lft.' rgt: '.$rgt.' niv: '.$niv.'<br/>';
	$arr= setLieuxHierarchie($idLieu, $dbN, $dbO, $idInstant, $row['id_rubrique'], $lft, $rgt, $niv+1);
	if($lft<$arr[0]){
		$rgt=$arr[1];	
	}else{
		$lft=$arr[0];	
		$rgt=$arr[1];		
	}
   	echo str_repeat("- ", $niv).$row['id_rubrique'].' <-> lft: '.$lft.' rgt: '.$rgt.' niv: '.$niv.'<br/>';
	
   	//on met à jour le gauche droite
   	$sql = "UPDATE $dbN.gevu_lieux
	SET lft = $lft, rgt = $rgt
	WHERE id_lieu = ".$idLieu;
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	$lft = $rgt;
	$rgt= $lft+1;	
   }
	return array($lft,$rgt);		
}

function ajusteBase($idLieu){

	//création d'un instant
	$sql = "INSERT INTO gevu_instants (`maintenant`, `ici`, `id_exi`, `nom`) VALUES
	(now(), '-1', 1, 'ajusteBase')";
	$result = mysql_query($sql);
	if (!$result) die('Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>');
	$idInstant = mysql_insert_id();
	echo "Instant : ".$idInstant."<br/>";		
	
	//mise à jour de l'instant de l'univers
	//récupère les lieux
	$sql = "UPDATE gevu_lieux SET id_instant=".$idInstant." WHERE id_lieu = $idLieu";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';

	//récupère les lieux avec le même nom, niveau, id_parent, id_rubrique
	$sql = "SELECT count(*) nb, GROUP_CONCAT(l.id_lieu) ids
		FROM gevu_lieux l
		WHERE l.lieu_parent = $idLieu
		GROUP BY l.id_rubrique, l.lib, l.id_parent, l.niv
		ORDER BY l.maj DESC";
	$rs = mysql_query($sql);
	if (!$rs) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	
	while($r=mysql_fetch_array($rs)) {
		CompareBranche($r['ids'], $idInstant, $idLieu);
	}

	//récupère les lieux du niveau inférieur pour changer leur instant et récalculer les gauche droite
	$sql = "SELECT e.id_lieu, e.niv, p.lft
		FROM gevu_lieux e
			INNER JOIN gevu_lieux p ON p.id_lieu = e.lieu_parent
		WHERE p.id_lieu = $idLieu";
	$rs = mysql_query($sql);
	if (!$rs) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	while($r=mysql_fetch_array($rs)) {
		ChangeBranche($r['id_lieu'], $r['lft'], $idInstant, $r['niv']);
	}
	
}


function CompareBranche($idsLieux, $idInstant, $LieuParent){

	//récupère les liens du lieu ayant la même rubrique et le même parent 
	$sql = "SELECT l.id_lieu, l.lieu_parent, l.lib, l.maj, l.lft, l.rgt, l.id_instant, l.niv
				,count(enfant.id_lieu) nbLieux
          		,count(t1.id_lieu) nbBat
          		,count(t2.id_lieu) nbDiag
          		,count(t3.id_lieu) nbDiagVoi
				,count(t4.id_lieu) nbDoc
				,count(t5.id_lieu) nbEsp
				,count(t6.id_lieu) nbEspEx
				,count(t7.id_lieu) nbEspIn
				,count(t8.id_lieu) nbEtab
				,count(t9.id_lieu) nbGeoR
				,count(t10.id_lieu) nbGeo
				,count(t11.id_lieu) nbNiv
				,count(t12.id_lieu) nbObjExt
				,count(t13.id_lieu) nbObjInt
				,count(t14.id_lieu) nbObjVoi
				,count(t15.id_lieu) nbObs
				,count(t16.id_lieu) nbPar
				,count(t17.id_lieu) nbPro
				,(count(enfant.id_lieu)
		             + count(t1.id_lieu)
		             + count(t2.id_lieu)
		             + count(t3.id_lieu)
		             + count(t4.id_lieu)
		             + count(t5.id_lieu)
		             + count(t6.id_lieu)
		             + count(t7.id_lieu)
		             + count(t8.id_lieu)
		             + count(t9.id_lieu)
		             + count(t10.id_lieu)
		             + count(t11.id_lieu)
		             + count(t12.id_lieu)
		             + count(t13.id_lieu)
		             + count(t14.id_lieu)
		             + count(t15.id_lieu)
		             + count(t16.id_lieu)
		             + count(t17.id_lieu)
		             ) nbTot
					,CONCAT_WS(':',count(enfant.id_lieu)
		             ,count(t1.id_lieu)
		             ,count(t2.id_lieu)
		             ,count(t3.id_lieu)
		             ,count(t4.id_lieu)
		             ,count(t5.id_lieu)
		             ,count(t6.id_lieu)
		             ,count(t7.id_lieu)
		             ,count(t8.id_lieu)
		             ,count(t9.id_lieu)
		             ,count(t10.id_lieu)
		             ,count(t11.id_lieu)
		             ,count(t12.id_lieu)
		             ,count(t13.id_lieu)
		             ,count(t14.id_lieu)
		             ,count(t15.id_lieu)
		             ,count(t16.id_lieu)
		             ,count(t17.id_lieu)
		             ) k
			FROM gevu_lieux l
				LEFT JOIN gevu_lieux AS enfant ON enfant.lft > l.lft AND enfant.rgt < l.rgt 
					AND enfant.niv = l.niv+1 AND enfant.id_instant = l.id_instant
		        LEFT JOIN gevu_batiments t1 ON t1.id_lieu = l.id_lieu
				LEFT JOIN gevu_diagnostics t2 ON t2.id_lieu = l.id_lieu
				LEFT JOIN gevu_diagnosticsxvoirie t3 ON t3.id_lieu = l.id_lieu
				LEFT JOIN gevu_docsxlieux t4 ON t4.id_lieu = l.id_lieu
				LEFT JOIN gevu_espaces t5 ON t5.id_lieu = l.id_lieu
				LEFT JOIN gevu_espacesxexterieurs t6 ON t6.id_lieu = l.id_lieu
				LEFT JOIN gevu_espacesxinterieurs t7 ON t7.id_lieu = l.id_lieu
				LEFT JOIN gevu_etablissements t8 ON t8.id_lieu = l.id_lieu
				LEFT JOIN gevu_georss t9 ON t9.id_lieu = l.id_lieu
				LEFT JOIN gevu_geos t10 ON t10.id_lieu = l.id_lieu
				LEFT JOIN gevu_niveaux t11 ON t11.id_lieu = l.id_lieu
				LEFT JOIN gevu_objetsxexterieurs t12 ON t12.id_lieu = l.id_lieu
				LEFT JOIN gevu_objetsxinterieurs t13 ON t13.id_lieu = l.id_lieu
				LEFT JOIN gevu_objetsxvoiries t14 ON t14.id_lieu = l.id_lieu
				LEFT JOIN gevu_observations t15 ON t15.id_lieu = l.id_lieu
				LEFT JOIN gevu_parcelles t16 ON t16.id_lieu = l.id_lieu
				LEFT JOIN gevu_problemes t17 ON t17.id_lieu = l.id_lieu
			WHERE l.id_lieu IN (".$idsLieux.")
			GROUP BY l.id_lieu 
			ORDER BY  l.lib, l.maj DESC"; 
	$rs1 = mysql_query($sql);
	$oLib="";
	$oDate="";
	$oLieu="";
	$oRgt="";
	$oK="";
	while($r1=mysql_fetch_array($rs1)) {
		if($oLib!=$r1['lib']){
			/*
			if($Plft==-1){
				//récupère la branche du parent
				$sql="SELECT parent.lft
				FROM gevu_lieux AS parent
				WHERE parent.id_lieu = ".$r1['lieu_parent'];
				$rs2 = mysql_query($sql);
				$r2=mysql_fetch_array($rs2);
				$Plft = $r2['lft'];
			}
			*/	
			//le lieux le plus récent avec un nom différent sert de référence
			//on met à jour l'instant de la référence et de ses enfants
			//ChangeBranche($r1['id_lieu'], $r1['lft'], $r1['rgt'], $r1['id_instant'], $Plft, $idInstant, $r1['niv']);							
			$oLieu = $r1['id_lieu'];
			$oLft = $r1['lft'];
			$oK = $r1['k'];
			echo "ajusteBase: ".str_repeat("- ", $r1['niv'])." : ".$r1['lib']."<br/>";								
		}else{
			//on vérifie si les lieux sont identiques
			if($oDate==$r1['maj'] && $oK==$r1['k'] ){
				//on supprime le lieu et tout ces élements
				DeleteLieu($r1['id_lieu']);
			//on vérifie s'il y a des éléments liés
			}elseif($r1['nbTot']==0){
				//on supprime la donnée
				DeleteLieu($r1['id_lieu']);
			}else{
				ModifBranche($r1, $oLieu, $oLft, $idInstant);
				//on met à jour le parent
				$sql="UPDATE gevu_batiments SET lieu_parent=".$LieuParent." WHERE id_lieu = ".$r1['id_lieu'];			          			
				mysql_query($sql);
			}
		}
		$oDate=$r1['maj'];
		$oLib=$r1['lib'];
	}
}

function ModifBranche($r1, $oLieu, $oLft, $idInstant){

	//on ajoute à la référence
	//les éléments lié au lieu ayant le même nom que la référence
	if($r1['nbLieux']>0){
		//récupèration des éléments liés
		$sql="SELECT CONCAT(e.ids, eR.ids) ids
		FROM (SELECT GROUP_CONCAT(enfant.id_lieu) ids
			FROM gevu_lieux enfant
			WHERE l.lieu_parent = ".$r1['id_lieu'].") e
		, (SELECT GROUP_CONCAT(enfant.id_lieu) ids
			FROM gevu_lieux enfant
			WHERE l.lieu_parent = ".$oLieu.") eR";
		//on récupère les enfants de reférence et ceux à comparer pour le niveau suivant
		$rs2 = mysql_query($sql);
		$r2=mysql_fetch_array($rs2);
		CompareBranche($r2["ids"], $idInstant, $oLieu);
		
	}
	//on met à jour les éléments liés 
    if($r1['nbBat']>0){
		$sql="UPDATE gevu_batiments SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
		mysql_query($sql);
    }
    
    if($r1['nbDiag']>0){
        $sql="UPDATE gevu_diagnostics SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
		mysql_query($sql);
    }

    if($r1['nbDiagVoi']>0){
		$sql="UPDATE gevu_diagnosticsxvoirie SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
		mysql_query($sql);
    }
    
    if($r1['nbDoc']>0){
        $sql="UPDATE gevu_docsxlieux SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
		mysql_query($sql);
    }

    if($r1['nbEsp']>0){
		$sql="UPDATE gevu_espaces SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			          								
		mysql_query($sql);
    }

    if($r1['nbEspEx']>0){
        $sql="UPDATE gevu_espacesxexterieurs SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			       			
		mysql_query($sql);
    }

    if($r1['nbEspIn']>0){
        $sql="UPDATE gevu_espacesxinterieurs SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
		mysql_query($sql);
    }

    if($r1['nbEtab']>0){
        $sql="UPDATE gevu_etablissements SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
		mysql_query($sql);
    }

    if($r1['nbGeoR']>0){
		$sql="UPDATE gevu_georss SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			         								
		mysql_query($sql);
    }

    if($r1['nbGeo']>0){
        $sql="UPDATE gevu_geos SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			          			
		mysql_query($sql);
    }

    if($r1['nbNiv']>0){
        $sql="UPDATE gevu_niveaux SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			          			
		mysql_query($sql);
    }
    
    if($r1['nbObjExt']>0){
        $sql="UPDATE gevu_objetsxexterieurs SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			         			
		mysql_query($sql);
    }

    if($r1['nbObjInt']>0){
        $sql="UPDATE gevu_objetsxinterieurs SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
		mysql_query($sql);
	}

	if($r1['nbObjVoi']>0){
        $sql="UPDATE gevu_objetsxvoiries SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
		mysql_query($sql);
	}

	if($r1['nbObs']>0){
		$sql="UPDATE gevu_observations SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
		mysql_query($sql);
	}

	if($r1['nbPar']>0){
        $sql="UPDATE gevu_parcelles SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			
		mysql_query($sql);
	}

	if($r1['nbPro']>0){
        $sql="UPDATE gevu_problemes SET id_lieu=".$oLieu." WHERE id_lieu = ".$r1['id_lieu'];			          			          			
		mysql_query($sql);
	}
    
	//on supprime le lieu
	DeleteLieu($r1['id_lieu']);

}

function ChangeBranche($id_lieuSrc, $lftDst, $idInstantDst, $niv=0){
	//http://www.artfulsoftware.com/mysqlbook/sampler/mysqled1ch20.html
	global $pdo;
	
	$stmt = $pdo->prepare("CALL MoveNode(p1,p2,p3)");
	$stmt->bindParam('p1', $id_lieuSrc, PDO::PARAM_INT);
	$stmt->bindParam('p2', $lftDst, PDO::PARAM_INT);
	$stmt->bindParam('p3', $idInstantDst, PDO::PARAM_INT);
	$stmt->execute();

	/*
	$sql="UPDATE gevu_lieux SET rgt = rgt + 2 WHERE rgt > ".$lftDst." AND id_instant=".$idInstantDst;
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	//echo "ChangeBranche rgt = rgt + 2: ".$id_lieuSrc." ".mysql_affected_rows()."<br/>";	
	
	$sql="UPDATE gevu_lieux SET lft = lft + 2 WHERE lft > ".$lftDst." AND id_instant=".$idInstantDst;
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	//echo "ChangeBranche lft = lft + 2 : ".$id_lieuSrc." ".mysql_affected_rows()."<br/>";	
	
	$sql="UPDATE gevu_lieux SET lft = ".($lftDst+1).", rgt = ".($lftDst+2).", id_instant=".$idInstantDst." WHERE id_lieu = ".$id_lieuSrc."";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
   	//echo "ChangeBranche: ".str_repeat("- ", $niv)." < ".$id_lieuSrc." < : ".mysql_affected_rows()."<br/>";	
	*/
	
	//Met à jour les enfants du lieu à ajouter
	$sql="SELECT e.id_lieu, e.niv
		FROM gevu_lieux e
		WHERE e.lieu_parent = $id_lieuSrc";
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
   	//echo "ChangeBranche: ".str_repeat("- ", $niv)." > ".$id_lieuSrc." > : ".mysql_affected_rows()."<br/>";	
	while($r=mysql_fetch_array($result)){
		//on ajoute la branche à la référence
		ChangeBranche($r['id_lieu'], $lftDst+1, $idInstantDst, $r['niv']);							
	}

	
}

function DeleteLieu($id_lieu){

	$sql="DELETE FROM gevu_batiments WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_diagnostics WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_diagnosticsxvoirie WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_docsxlieux WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_espaces WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_espacesxexterieurs WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_espacesxinterieurs WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_etablissements WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_georss WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_geos WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_niveaux WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_objetsxexterieurs WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_objetsxinterieurs WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_objetsxvoiries WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_observations WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_parcelles WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_problemes WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
	
	$sql="DELETE FROM gevu_lieux WHERE id_lieu = ".$id_lieu;			          			
	$result = mysql_query($sql);
	if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
	echo "DeleteLieu: ".$id_lieu." ".mysql_affected_rows()."<br/>";	
		
}

//fermeture de la connexion    
mysql_close($ldb);
?>