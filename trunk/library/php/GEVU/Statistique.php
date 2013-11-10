<?php

class GEVU_Statistique extends GEVU_Site{
        
	/**
	* constructeur de la class
	*
    * @param string $idBase
    * @param boolean $cache
    * 
    */
	public function __construct($idBase=false, $cache=true)
    {
    	parent::__construct($idBase, $cache);
		
    }

	/**
	* récupère les impayés par antenne
    * @param string $idBase
    * @return string
    */
	public function getAntennesImpayes($idBase=false){
	   $c = str_replace("::", "_", __METHOD__)."_".$idBase; 
	   $s = $this->cache->load($c);
       if(!$s){
    		//connexion à la base
    		$db = $this->getDb($idBase);
			$s = json_encode($arrD);
    		$this->cache->save($s, $c);
        }
        return $s;
    }
    
    
	/**
	* récupère les diagnostics énergétique et carbonne du patrimoine
    * @param string $idBase
    * @return string
    */
	public function getPatrimoineDiag($idBase=false){
	   $c = str_replace("::", "_", __METHOD__)."_".$idBase; 
	   $rs = false;//$this->cache->load($c);
       if(!$rs){
    		//connexion à la base
    		$db = $this->getDb($idBase);
    		
        	//récupère les données GES
    		$sql = "SELECT COUNT(*) nbLog
				, s.DPE_Categorie_Emissions_GES
				, SUM(DATEDIFF(CURDATE(), STR_TO_DATE(DPE_Date, '%d/%m/%Y %H:%i')))/COUNT(DISTINCT s.id_stat) moyAgeDiag
				, SUM(YEAR(CURRENT_DATE())-s.Annee_Construction)/COUNT(DISTINCT s.id_stat) moyAge
				FROM gevu_stats s 
				WHERE DPE_Date != ''
				GROUP BY s.DPE_Categorie_Emissions_GES";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
	    	$rType="";
	    	foreach ($arr as $type) {
	    		$geos = $this->getGeoStat($db, "DPE_Categorie_Emissions_GES", $type["DPE_Categorie_Emissions_GES"]);
				$rType[] = array("name"=>$type["DPE_Categorie_Emissions_GES"],"diag"=>"GES","nb"=>$type["nbLog"],"moyAgeDiag"=>$type["moyAgeDiag"],"moyAge"=>$type["moyAge"],"geos"=>$geos);			
	    	}
			$rStat[] = array("name"=>"Bilan carbone","visible"=>true,"diag"=>"GES","children"=>$rType);			
	    	
	    	//récupère les données DPE
    		$sql = "SELECT COUNT(*) nbLog
				, s.DPE_Categorie_Consommation
				, SUM(DATEDIFF(CURDATE(), STR_TO_DATE(DPE_Date, '%d/%m/%Y %H:%i')))/COUNT(DISTINCT s.id_stat) moyAgeDiag
				, SUM(YEAR(CURRENT_DATE())-s.Annee_Construction)/COUNT(DISTINCT s.id_stat) moyAge
				FROM gevu_stats s 
				WHERE DPE_Date != ''
				GROUP BY s.DPE_Categorie_Consommation";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
	    	$rType="";
	    	foreach ($arr as $type) {
	    		$geos = $this->getGeoStat($db, "DPE_Categorie_Consommation", $type["DPE_Categorie_Consommation"]);
		    	$rType[] = array("name"=>$type["DPE_Categorie_Consommation"],"diag"=>"DPE","nb"=>$type["nbLog"],"moyAgeDiag"=>$type["moyAgeDiag"],"moyAge"=>$type["moyAge"],"geos"=>$geos);
	    	}
			$rStat[] = array("name"=>"Diagnostic de performance énergétique","visible"=>true,"diag"=>"DPE","children"=>$rType);			
    		//compilation du tableau total
			$rs = array("name"=>"Diagnostics","visible"=>true,"children"=>$rStat);        	
        	$this->cache->save($rs, $c);
        }
        return $rs;
			
	}
    
	/**
	* récupère les données géographique pour une stat
    * @param  db $db
    * @param  string $stat
    * @param  string $val
    * @return array
    */
	public function getGeoStat($db, $stat, $val){
	   $c = str_replace("::", "_", __METHOD__)."_".$this->idBase."_".$stat."_".$val; 
	   $rs = $this->cache->load($c);
       if(!$rs){
			//récupère les géolocalisations
    		$sql = "SELECT COUNT(*) nbLog
				, g.lat, g.lng
				FROM gevu_stats s 
				INNER JOIN gevu_geos g ON s.id_lieu = g.id_lieu
				WHERE s.".$stat." = '".$val."'
				GROUP BY g.lat, g.lng";
    		$stmt = $db->query($sql);
	    	$arrG = $stmt->fetchAll();
			$geos = "";
	    	foreach ($arrG as $geo){
				$rs[] = array("poids"=>$geo["nbLog"],"lat"=>$geo["lat"],"lng"=>$geo["lng"]);
			}
        	$this->cache->save($rs, $c);
       }
       return $rs;
	}
	/**
	* récupère les données générale du patrimoine
    * @param string $idBase
    * @return string
    */
	public function getPatrimoineDonGen($idBase=false){
	   $c = str_replace("::", "_", __METHOD__)."_".$idBase; 
	   $rs = $this->cache->load($c);
       if(!$rs){
    		//connexion à la base
    		$db = $this->getDb($idBase);
    		
        	//récupère les données Nb de bâtiments d'habitation
    		$sql = "SELECT COUNT(*) nbLog
					, s.Logement_Individuel
					, COUNT(DISTINCT s.code_batiment)
				FROM gevu_stats s 
				WHERE Categorie_Module = 'L'
				GROUP BY s.Logement_Individuel";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
	    	$nbLogCol = $arr[0]['nbLog'];
	    	$nbLogInd = $arr[1]['nbLog'];
	    	/*
	    	$r = array(
						array("name"=>'Collectif',"nb"=>$arr[0]['nbBat'])
						,array("name"=>'Individuel',"nb"=>$arr[1]['nbBat'])
						);
			$rStat[] = array("name"=>"Nb de bâtiments d'habitation","nb"=>($arr[0]['nbBat']+$arr[1]['nbBat']),"children"=>$r);
			*/			
        	//récupère les données Nb de Foyer
    		$sql = "SELECT COUNT(*)
					, s.Categorie_Module
					, COUNT(DISTINCT s.code_batiment) nbBat
				FROM gevu_stats s 
				WHERE s.Categorie_Module = 'F'";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
			$rStat[] = array("name"=>"Nb de foyers","nb"=>$arr[0]['nbBat']);			
        	//récupère les données Nb de Commerce
    		$sql = "SELECT COUNT(*)
					, s.Categorie_Module
					, COUNT(DISTINCT s.code_batiment) nbBat
				FROM gevu_stats s 
				WHERE s.Categorie_Module = 'C'";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
			$rStat[] = array("name"=>"Nb de commerces","nb"=>$arr[0]['nbBat']);			
        	//récupère les données Nb d'associations
    		$sql = "SELECT COUNT(*)
					, s.Categorie_Module
					, COUNT(DISTINCT s.code_batiment) nbBat
				FROM gevu_stats s 
				WHERE s.Categorie_Module = 'A'";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
			$rStat[] = array("name"=>"Nb d'associations","nb"=>$arr[0]['nbBat']);			
        	//récupère les données Nb de RPA
    		$sql = "SELECT COUNT(*)
					, s.Categorie_Module
					, COUNT(DISTINCT s.code_batiment) nbBat
				FROM gevu_stats s 
				WHERE s.Categorie_Module = 'F'";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
			$rStat[] = array("name"=>"Nb de RPA","nb"=>$arr[0]['nbBat']);			
			
	    	//récupère les données Nb de bâtiments en copropriété
    		$sql = "SELECT COUNT(*) nbLog
					, COUNT(DISTINCT s.code_batiment) nbBat
				FROM gevu_stats s 
				WHERE s.Copropriete != ''";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
	    	$nbLogCopro = $arr[0]['nbLog'];
	    	$rStat[] = array("name"=>"Nb de bâtiments en copropriété","nb"=>$arr[0]['nbBat']);			

	    	/*récupère les données Nb de bâtiments administratifs
    		$sql = "SELECT COUNT(*)
					, s.Categorie_Module
					, COUNT(DISTINCT s.code_batiment) nbBat
				FROM gevu_stats s 
				WHERE s.Categorie_Module = 'A'";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
			$rStat[] = array("name"=>"Nb de bâtiments administratifs","nb"=>$arr[0]['nbBat']);			
        	*/
        	/*récupère les données Nb de locaux d'activité
    		$sql = "SELECT COUNT(*)
					, s.Categorie_Module
					, COUNT(DISTINCT s.code_batiment) nbBat
				FROM gevu_stats s 
				WHERE s.Categorie_Module = 'M'";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
			$rStat[] = array("name"=>"Nb de locaux administatifs","nb"=>$arr[0]['nbBat']);
			*/
        	/*récupère les données Nb de Hall d'entrée
    		$sql = "SELECT SUM(sHall.nbHallBat) nbHall
				FROM (
					SELECT COUNT(*)
						, s.code_batiment
						, COUNT(DISTINCT Code_Escalier) nbHallBat
					FROM gevu_stats s 
					WHERE Categorie_Module = 'L'
					GROUP BY s.code_batiment) sHall";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
			$rStat[] = array("name"=>"Nb de Hall d'entrée","nb"=>$arr[0]['nbBat']);			
			*/
        	//récupère les données Nb de pavillon
    		$sql = "SELECT SUM(sPav.nbBat) nbPav
				FROM (SELECT COUNT(*)
						, s.Type_Logement
						, COUNT(DISTINCT s.code_batiment) nbBat
					FROM gevu_stats s 
					WHERE s.Type_Logement LIKE 'P%'
					GROUP BY s.code_batiment) sPav";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
			$rStat[] = array("name"=>"Nb de pavillon","nb"=>$arr[0]['nbPav']);			
	    	
			//rassemble les stats pour les bâtiments
			$rs[] = array("name"=>"Nombres de bâtiments","children"=>$rStat);
        	
			//traitement des stats logement
			$rStat = "";
			//récupère les données Nb de logement collectif et individuel
			$rStat[] = array("name"=>"Collectifs","nb"=>$nbLogCol);
			$rStat[] = array("name"=>"Individuels","nb"=>$nbLogInd);
			//récupère les données Réservataires
			//$rStat[] = array("name"=>"Réservataires","nb"=>0);
			//récupère les données En commercialisation
			//$rStat[] = array("name"=>"En commercialisation (co-propriété)","nb"=>$nbLogCopro);
        	//récupère les données Nb de logement ZUS
    		$sql = "SELECT COUNT(*) nbLog
				FROM gevu_stats s 
				WHERE s.Indicateur_Zus = '2'";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
			$rStat[] = array("name"=>"Nb de logement ZUS","nb"=>$arr[0]['nbLog']);			
        	//récupère les données Répartition des logements par typologie
    		$sql = "SELECT COUNT(*) nbLog, s.Type_Logement
				FROM gevu_stats s 
				WHERE Categorie_Module = 'L'
				GROUP BY s.Type_Logement
				ORDER BY s.Type_Logement";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
	    	$rType="";
	    	foreach ($arr as $type) {
				$rType[] = array("name"=>$type["Type_Logement"],"nb"=>$type["nbLog"]);			
	    	}
			$rStat[] = array("name"=>"Répartition des logements par typologie","children"=>$rType);			
			//rassemble les stats pour les logements				
        	$rs[] = array("name"=>"Nombres de logements","children"=>$rStat);

			//traitement des stats stationnement
			$rStat = "";
        	//récupère les données par type de stationnement
    		$sql = "SELECT COUNT(*) nbLog, s.Contrat
				FROM gevu_stats s 
				WHERE s.Type_Logement IN ('GA','GD','GP','TO')
				GROUP BY s.Contrat
				ORDER BY s.Contrat";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
	    	$rType="";
	    	foreach ($arr as $type) {
				$rType[] = array("name"=>$type["Contrat"],"nb"=>$type["nbLog"]);			
	    	}
	    	//rassemble les stats pour les stationnement					    	
        	$rs[] = array("name"=>"Nombres de stationnement","children"=>$rType);

			//traitement des stats Vacances locatives
			$rStat = "";
        	//récupère les données Nb de vacance
    		$sql = "SELECT COUNT(*) nbLog, s.Categorie_Module, s.Occupation
				FROM gevu_stats s 
				WHERE s.Categorie_Module IN ('L','G','C')
				GROUP BY s.Categorie_Module, s.Occupation
				ORDER BY s.Categorie_Module, s.Occupation";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
			$rStat[] = array("name"=>"Logements"
				,"children"=>array(
					array("name"=>$arr[4]["Occupation"],"nb"=>$arr[4]["nbLog"])
					,array("name"=>$arr[5]["Occupation"],"nb"=>$arr[5]["nbLog"])
					));			
			$rStat[] = array("name"=>"Garages"
				,"children"=>array(
					array("name"=>$arr[2]["Occupation"],"nb"=>$arr[2]["nbLog"])
					,array("name"=>$arr[3]["Occupation"],"nb"=>$arr[3]["nbLog"])
					));			
			$rStat[] = array("name"=>"Commerces"
				,"children"=>array(
					array("name"=>$arr[0]["Occupation"],"nb"=>$arr[0]["nbLog"])
					,array("name"=>$arr[1]["Occupation"],"nb"=>$arr[1]["nbLog"])
					));								
        	$rs[] = array("name"=>"Vacances locatives","children"=>$rStat);
        	
			//traitement des stats Répartition par type de financement
			$rStat = "";
        	//récupère les données de type de financement
    		$sql = "SELECT COUNT(*) nbLog, s.Type_financement
				FROM gevu_stats s 
				GROUP BY s.Type_financement
				ORDER BY s.Type_financement";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
	    	foreach ($arr as $type) {
				$rStat[] = array("name"=>$type["Type_financement"],"nb"=>$type["nbLog"]);			
	    	}
        	$rs[] = array("name"=>"Répartition par type de financement","children"=>$rStat);
        	
			//traitement des stats Répartition par age du patrimoine
			$rStat = "";
        	//récupère les données d'age du patrimoine
    		$sql = "SELECT COUNT(*) nbLog, s.Annee_Construction, (YEAR(CURRENT_DATE()) - s.Annee_Construction) as age
				FROM gevu_stats s 
				WHERE s.Annee_Construction != ''
				GROUP BY s.Annee_Construction";
    		$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();
	    	foreach ($arr as $type) {
	    		$geos = $this->getGeoStat($db, "Annee_Construction", $type["Annee_Construction"]);
				$rStat[] = array("name"=>$type["age"],"annee"=>$type["Annee_Construction"],"nb"=>$type["nbLog"],"geos"=>$geos);			
	    	}
        	$rs[] = array("name"=>"Répartition par âge","children"=>$rStat);
        	
    		//compilation du tableau total
			$rs = array("name"=>"Patrimoine","children"=>$rs);        	
        	$this->cache->save($rs, $c);
        }
        return $rs;
    }
    
    
	/**
	 * récupère la liste des réponses d'une base
    * @param string $idBase
    * @return string
    */
	public function getListeReponse($idBase=false){
	   $c = str_replace("::", "_", __METHOD__)."_".$idBase; 
	   $s = $this->cache->load($c);
       if(!$s){
    		//connexion à la base
    		$db = $this->getDb($idBase);

    		//création des tables
        	$dbD = new Models_DbTable_Gevu_diagnostics($db);
        	$dbG = new Models_DbTable_Gevu_geos($db);
        	$dbL = new Models_DbTable_Gevu_lieux($db);
        	
        	$arrD = $dbD->getReponses();
        	
        	//complète les informations avec des données géographiques
        	$nbI = count($arrD);
        	for ($i = 0; $i < $nbI; $i++) {
        		//récupère le fil d'ariane de la réponse
        		$arrL = $dbL->getFullPath($arrD[$i]['id_lieu'],'rgt');
        		//recherche les coordonnées les plus précises
        		$nbJ = count($arrL);
        		for ($j = 0; $j < $nbJ; $j++){
        			$arrG = $dbG->findById_lieu($arrL[$j]['id_lieu']);
        			if(count($arrG)>0){
        				$arrD[$i]['lat'] = $arrG[0]['lat']; 
        				$arrD[$i]['lng'] = $arrG[0]['lng'];
        				$j = $nbJ; 
        			}
        		}
        	}
        	
        	
			$s = json_encode($arrD);
    		$this->cache->save($s, $c);
        }
        return $s;
    }

	/**
	 * calcule la hiérarchie des types de logement
    * @param string $typeLog
	 * 
    * @return array
    */
	public function getGeoTypeLog($typeLog=""){
	   $c = str_replace("::", "_", __METHOD__).$typeLog; 
	   $rs = $this->cache->load($c);
       if(!$rs){
       }
	}
	
	/**
	 * calcule la hiérarchie des types de logement
    * @param string $typeLog
	 * 
    * @return array
    */
	public function getArbreTypeLog($typeLog=""){
	   $c = str_replace("::", "_", __METHOD__).$typeLog; 
	   $rs = $this->cache->load($c);
       if(!$rs){

       		$wTypelog = "";
			if($typeLog!="")$wTypelog = " AND s.Categorie_Module ='".$typeLog."'";
       	
        	//récupère la hiérarchie des antennes - groupe - batiments
    		$sql = "SELECT a.ref, la.lib libA, la.id_lieu idA, lg.lib libG, lg.id_lieu idG, lb.lib libB, lb.id_lieu idB, COUNT(s.id_lieu) nbStat, m.titre 
				FROM gevu_antennes a
				    INNER JOIN gevu_lieux la on la.id_lieu = a.id_lieu
					INNER JOIN gevu_lieux lg on lg.lieu_parent = la.id_lieu
					INNER JOIN gevu_lieux lb on lb.lieu_parent = lg.id_lieu
					INNER JOIN gevu_lieux le on le.lft BETWEEN lb.lft AND lb.rgt
					LEFT JOIN gevu_stats s on s.id_lieu = le.id_lieu ".$wTypelog."
				    LEFT JOIN gevu_motsclefs m on m.code = s.Categorie_Module 
				GROUP BY la.id_lieu, lg.id_lieu, lb.id_lieu, m.id_motclef
				HAVING nbStat > 0";
       		
	        $db = Zend_Db_Table::getDefaultAdapter();
	    	$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();

	    	$idA = -1; $idG = -1; $idB = -1; $minNb = 1; $maxNb = 1; $nbTot;
	        $rs = array("name"=>"Alcéane","children"=>array());
	        foreach ($arr as $r) {
	        	if($idA != $r['idA']){
	        		if($idA != -1){
	        			//$strType = implode('-', array_keys($types));
	        			//$rsA["types"] = $strType;
	        			//$types = array();
	        			if($rsB)$rsG['children'][] = $rsB;
	        			$rsB = false;
	        			if($rsG)$rsA['children'][] = $rsG;
	        			$rsG = false;
	        			//met à jour le min/max
	        			$rsA['max'] = $maxNb;
	        			$rsA['min'] = $minNb;
	        			$rs['children'][] = $rsA; 
	        		} 
	        		$minNb = $r['nbStat']; $maxNb = $r['nbStat'];
	        		$rsA = array("ref"=>$r['ref'],"name"=>$r['libA'],"nb"=>1,"children"=>array());
	        		$idA = $r['idA'];
	        	}
	        	if($idG != $r['idG']){
	        		if($idG != -1){
	        			if($rsB)$rsG['children'][] = $rsB;
	        			$rsB = false;
	        			if($rsG)$rsA['children'][] = $rsG;
	        		} 
	        		$rsG = array("ref"=>$r['ref'],"name"=>$r['libG'],"nb"=>1,"children"=>array());
	        		$idG = $r['idG'];
	        	}
	        	if($idB != $r['idB']){
	        		if($idB != -1){
	        			if($rsB)$rsG['children'][] = $rsB;
	        		} 
	        		$rsB = array("ref"=>$r['ref'],"name"=>$r['libB'],"nb"=>1,"children"=>array());
	        		$idB = $r['idB'];
	        	}
	        	$rsA['nb'] = $rsA['nb']+$r['nbStat'];
	        	$rsG['nb'] = $rsG['nb']+$r['nbStat'];
	        	$rsB['nb'] = $rsB['nb']+$r['nbStat'];
	        	$rsL = array("ref"=>$r['ref'],"name"=>$r['titre'],"nb"=>$r['nbStat']);
	        	//enregistre le min/max
	        	if($maxNb<$r['nbStat'])$maxNb=$r['nbStat'];
	        	if($minNb>$r['nbStat'])$minNb=$r['nbStat'];
	        	//$types[$r['titre']] = "-";
	        	$rsB['children'][] = $rsL;
	        	//enregistre le nombre total
	        	$nbTot += $r['nbStat'];
	        }
			//$strType = implode('-', array_keys($types));
	        //$rsA["types"] = $strType;
			$rsG['children'][] = $rsB;
        	$rsA['children'][] = $rsG;
        	//met à jour le min/max
	        $rsA['max'] = $maxNb;
	        $rsA['min'] = $minNb;
        	$rs['children'][] = $rsA; 
        	$rs['nb'] = $nbTot; 
        	
	        $this->cache->save($rs, $c);
        }
        
        return $rs;
    }
    
}
