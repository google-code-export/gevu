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
	   $c = str_replace("::", "_", __METHOD__)."_".$idBase."_".$idLieu; 
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
	* récupère les statistiques générales d'une antenne
    * @param string $idBase
    * @param int 	$idLieu
    * @return 		string
    */
	public function getAntenneDonGen($idBase=false, $idLieu=-1){

		$c = str_replace("::", "_", __METHOD__)."_".$idBase."_".$idLieu; 
	   	$rs = $this->cache->load($c);
       	if(!$rs){
    		//connexion à la base
    		$db = $this->getDb($idBase);
    		
    	
    		$rStat[] = $this->batNbType("Nb de foyers", "Categorie_Module", "F", false, $idLieu);
    		$rStat[] = $this->batNbType("Nb de commerces", "Categorie_Module", "C", false, $idLieu);
    		$rStat[] = $this->batNbType("Nb d'associations", "Categorie_Module", "A", false, $idLieu);
    		$rStat[] = $this->batNbType("Nb RPA", "Categorie_Module", "R", false, $idLieu);
    		$rStat[] = $this->batNbType("Nb de bât. en copropriété", "Copropriete", "non vide", false, $idLieu);
    		$rStat[] = $this->batNbType("Nb de pavillon", "Type_Logement", "P%", "LIKE", $idLieu);
    		
			//rassemble les stats pour les bâtiments
			$rs[] = array("name"=>"Nombres de bâtiments","visible"=>true, "children"=>$rStat);
        	
			//traitement des stats logement
			$rStat = "";
			//récupère les données Nb de logement collectif et individuel, ZUS
    		$rStat[] = $this->logNbType("Nb de logement collectifs", array("Categorie_Module","Logement_Individuel"), array("L","C"), false, $idLieu);
			$rStat[] = $this->logNbType("Nb de logement individuels", array("Categorie_Module","Logement_Individuel"), array("L","I"), false, $idLieu);
    		$rStat[] = $this->logNbType("Nb de logement ZUS", "Indicateur_Zus", "2", false, $idLieu);
			$rs[] = array("name"=>"Nombres de logements","visible"=>true, "children"=>$rStat);
    		
    		//récupère la typologie des logement
    		$rs[] = $this->typeLogement($idLieu);
    		
    		//traitement des stats stationnement
    		$rs[] = $this->typeSationnement($idLieu);
    		
			//traitement des stats Vacances locatives
    		$rs[] = $this->typeVacances($idLieu);
        	
			//traitement des stats Répartition par type de financement
			$rs[] = $this->typeFinancement($idLieu);
        	
			//traitement des stats Répartition par age du patrimoine
			$rs[] = $this->typeAge($idLieu);
			        	
    		//compilation du tableau total
			$rs = array("name"=>"Antenne", "idLieu"=>$idLieu,"children"=>$rs);        	
        	$this->cache->save($rs, $c);
        }
        return $rs;		
	}

	/**
	* récupère les statistiques générales d'un batiment
    * @param string $idBase
    * @param int 	$idLieu
    * @return 		string
    */
	public function getBatimentDonGen($idBase=false, $idLieu=-1){

		$c = str_replace("::", "_", __METHOD__)."_".$idBase."_".$idLieu; 
	   	$rs = $this->cache->load($c);
       	if(!$rs){
    		//connexion à la base
    		$db = $this->getDb($idBase);
    		
			//traitement des stats logement
			$rStat = "";
			//récupère les données Nb de logement collectif et individuel, ZUS
    		$rStat[] = $this->logNbType("Nb de log. en copropriété", "Copropriete", "non vide", false, $idLieu);
			$rStat[] = $this->logNbType("Nb de logement collectifs", array("Categorie_Module","Logement_Individuel"), array("L","C"), false, $idLieu);
			$rStat[] = $this->logNbType("Nb de logement individuels", array("Categorie_Module","Logement_Individuel"), array("L","I"), false, $idLieu);
			$rs[] = array("name"=>"Nombres de logements","visible"=>true, "children"=>$rStat);
    		
    		//récupère la typologie des logement
    		$rs[] = $this->typeLogement($idLieu);
    		    		
			//traitement des stats Vacances locatives
    		$rs[] = $this->typeVacances($idLieu);
        	
			//traitement des stats Répartition par type de financement
			$rs[] = $this->typeFinancement($idLieu);
        				        	
    		//compilation du tableau total
			$rs = array("name"=>"Bâtiment", "idLieu"=>$idLieu,"children"=>$rs);        	
        	$this->cache->save($rs, $c);
        }
        return $rs;		
	}	
	/**
	* récupère les statistiques générales d'un groupe
    * @param string $idBase
    * @param int 	$idLieu
    * @return 		array
    */
	public function getGroupeDonGen($idBase=false, $idLieu=-1){

		$c = str_replace("::", "_", __METHOD__)."_".$idBase."_".$idLieu; 
	   	$rs = false;//$this->cache->load($c);
       	if(!$rs){
    		//connexion à la base
    		$db = $this->getDb($idBase);
    		
    	
    		$rStat[] = $this->batNbType("Nb de foyers", "Categorie_Module", "F", false, $idLieu);
    		$rStat[] = $this->batNbType("Nb de commerces", "Categorie_Module", "C", false, $idLieu);
    		$rStat[] = $this->batNbType("Nb d'associations", "Categorie_Module", "A", false, $idLieu);
    		$rStat[] = $this->batNbType("Nb RPA", "Categorie_Module", "R", false, $idLieu);
    		$rStat[] = $this->batNbType("Nb de bât. en copropriété", "Copropriete", "non vide", false, $idLieu);
    		$rStat[] = $this->batNbType("Nb de pavillon", "Type_Logement", "P%", "LIKE", $idLieu);
    		
			//rassemble les stats pour les bâtiments
			$dbL = new Models_DbTable_Gevu_lieux($db);
			$nbBat = $dbL->getChildForTypeControle($idLieu, 45);
			$rs[] = array("name"=>"Nombres de bâtiments", "nb"=>count($nbBat),"visible"=>true, "children"=>$rStat);
        	
			//traitement des stats logement
			$rStat = "";
			//récupère les données Nb de logement collectif et individuel, ZUS
    		$rStat[] = $this->logNbType("Nb de logement collectifs", array("Categorie_Module","Logement_Individuel"), array("L","C"), false, $idLieu);
			$rStat[] = $this->logNbType("Nb de logement individuels", array("Categorie_Module","Logement_Individuel"), array("L","I"), false, $idLieu);
    		$rs[] = array("name"=>"Nombres de logements","visible"=>true, "children"=>$rStat);
    		
    		//traitement des stats stationnement
    		$rs[] = $this->typeSationnement($idLieu);
    		
			//traitement des stats Vacances locatives
    		$rs[] = $this->typeVacances($idLieu);
        	
			//traitement des stats Répartition par type de financement
			$rs[] = $this->typeFinancement($idLieu);
			        	
    		//compilation du tableau total
			$rs = array("name"=>"Groupe", "idLieu"=>$idLieu,"children"=>$rs);        	
        	$this->cache->save($rs, $c);
        }
        return $rs;		
	}	
	
	/**
	* récupère les diagnostics énergétique et carbonne du patrimoine
    * @param string $idBase
    * @return string
    */
	public function getPatrimoineDiag($idBase=false){
	   $c = str_replace("::", "_", __METHOD__)."_".$idBase; 
	   $rs = $this->cache->load($c);
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
    * @param  db 		$db
    * @param  array 	$stat
    * @param  array 	$val
    * @param  string 	$op
    * @param  int		$idParent
    * @return array
    */
	public function getGeoStat($db, $stat, $val, $op=false, $idParent=false){
		
		if(is_array($stat))
	    	$idStat = implode($stat,"_")."-".implode($val,"_");
	    else 
	    	$idStat = $stat."-".$val;
		
	   $c = str_replace("::", "_", __METHOD__)."_".$this->idBase."_".md5($idStat)."_".$idParent; 
	   $rs = $this->cache->load($c);
       if(!$rs){
       		//création du where
	       	$w = $this->getWhere($stat, $val, $op);
      		
      		$join = "";
      		if($idParent){
      			$join = "
      			INNER JOIN gevu_lieux le ON le.id_lieu = s.id_lieu 
				INNER JOIN gevu_lieux l ON le.lft BETWEEN l.lft AND l.rgt AND l.id_lieu = ".$idParent."
      			";
      		}
      		
			//récupère les géolocalisations
    		$sql = "SELECT COUNT(*) nbLog
				, g.lat, g.lng
				FROM gevu_stats s 
				INNER JOIN gevu_geos g ON s.id_lieu = g.id_lieu
				".$join."
				".$w."
				GROUP BY g.lat, g.lng";
    		//if($op=="LIKE")echo $sql;
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
	   $rs = false;//$this->cache->load($c);
       if(!$rs){
    		//connexion à la base
    		$db = $this->getDb($idBase);
    		
    		$rStat[] = $this->batNbType("Nb de foyers", "Categorie_Module", "F");
    		$rStat[] = $this->batNbType("Nb de commerces", "Categorie_Module", "C");
    		$rStat[] = $this->batNbType("Nb d'associations", "Categorie_Module", "A");
    		$rStat[] = $this->batNbType("Nb RPA", "Categorie_Module", "R");
    		$rStat[] = $this->batNbType("Nb de bât. en copropriété", "Copropriete", "non vide");
    		$rStat[] = $this->batNbType("Nb de pavillon", "Type_Logement", "P%", "LIKE");
    		
			//rassemble les stats pour les bâtiments
			$rs[] = array("name"=>"Nombres de bâtiments","visible"=>true, "children"=>$rStat);
        	
			//traitement des stats logement
			$rStat = "";
			//récupère les données Nb de logement collectif et individuel, ZUS
    		$rStat[] = $this->logNbType("Nb de logement collectifs", array("Categorie_Module","Logement_Individuel"), array("L","C"));
			$rStat[] = $this->logNbType("Nb de logement individuels", array("Categorie_Module","Logement_Individuel"), array("L","I"));
    		$rStat[] = $this->logNbType("Nb de logement ZUS", "Indicateur_Zus", "2");
			$rs[] = array("name"=>"Nombres de logements","visible"=>true, "children"=>$rStat);
    		
    		//récupère la typologie des logement
    		$rs[] = $this->typeLogement();
    		
    		//traitement des stats stationnement
    		$rs[] = $this->typeSationnement();
    		
			//traitement des stats Vacances locatives
    		$rs[] = $this->typeVacances();
        	
			//traitement des stats Répartition par type de financement
			$rs[] = $this->typeFinancement();
        	
			//traitement des stats Répartition par age du patrimoine
			$rs[] = $this->typeAge();
			        	
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
    
	/**
	* calcule la répartition des types de financement
    * @param int $idLieu
	* 
    * @return array
    */
    function typeFinancement($idLieu=false){

    	$rStat = "";

    	if($idLieu){
    		$join = "INNER JOIN gevu_lieux le ON le.id_lieu = s.id_lieu 
				INNER JOIN gevu_lieux l ON le.lft BETWEEN l.lft AND l.rgt AND l.id_lieu = ".$idLieu;
    	}
    	
        //récupère les données de type de financement
    	$sql = "SELECT COUNT(*) nbLog, s.Type_financement
			FROM gevu_stats s 
			".$join."
			WHERE s.Type_financement != ''
			GROUP BY s.Type_financement
			ORDER BY s.Type_financement";
    	$stmt = $this->db->query($sql);
    	$arr = $stmt->fetchAll();
    	$min=$arr[0]["nbLog"];
    	$max=$min;
    	foreach ($arr as $type) {
    		$geos = $this->getGeoStat($this->db, "Type_financement", $type["Type_financement"], false, $idLieu);
			$rStat[] = array("name"=>$type["Type_financement"],"stat"=>"finance","nb"=>$type["nbLog"],"geos"=>$geos);			
    		if($min > $type["nbLog"])$min = $type["nbLog"];
    		if($max < $type["nbLog"])$max = $type["nbLog"];
    	}
        return array("name"=>"Répartition par type de financement", "min"=>$min, "max"=>$max,"visible"=>true,"stat"=>"finance","children"=>$rStat);
    	
    }

    /**
	* calcule la répartition des types de logement
    * @param int $idLieu
	* 
    * @return array
    */
    function typeLogement($idLieu=false){

    	$rStat = "";
    	
    	if($idLieu){
    		$join = "INNER JOIN gevu_lieux le ON le.id_lieu = s.id_lieu 
				INNER JOIN gevu_lieux l ON le.lft BETWEEN l.lft AND l.rgt AND l.id_lieu = ".$idLieu;
    	}
    	
    	//récupère les données Répartition des logements par typologie
    	$sql = "SELECT COUNT(*) nbLog, s.Type_Logement
			FROM gevu_stats s 
			".$join."
			WHERE Categorie_Module = 'L'
			GROUP BY s.Type_Logement
			ORDER BY s.Type_Logement";
    	$stmt = $this->db->query($sql);
    	$arr = $stmt->fetchAll();
    	$rType="";
    	$min=$arr[0]["nbLog"];
    	$max=$min;
    	foreach ($arr as $type) {
    		$geos = $this->getGeoStat($this->db, "Type_Logement", $type["Type_Logement"], false, $idLieu);
    		if($min > $type["nbLog"])$min = $type["nbLog"];
    		if($max < $type["nbLog"])$max = $type["nbLog"];
			$rType[] = array("name"=>$type["Type_Logement"], "stat"=>"typo","nb"=>$type["nbLog"],"geos"=>$geos);			
    	}
		return array("name"=>"Répartition des logements par typologie", "min"=>$min, "max"=>$max,"visible"=>true, "stat"=>"typo","children"=>$rType);			
    }
    
    /**
	* calcule la répartition des stationnements
    * @param int $idLieu
	* 
    * @return array
    */
    function typeSationnement($idLieu=false){

    	$rStat = "";
    	
    	if($idLieu){
    		$join = "INNER JOIN gevu_lieux le ON le.id_lieu = s.id_lieu 
				INNER JOIN gevu_lieux l ON le.lft BETWEEN l.lft AND l.rgt AND l.id_lieu = ".$idLieu;
    	}
	    $sql = "SELECT COUNT(*) nbLog, s.Contrat
			FROM gevu_stats s 
			".$join."
			WHERE s.Type_Logement IN ('GA','GD','GP','TO') AND s.Contrat != ''
			GROUP BY s.Contrat
			ORDER BY s.Contrat";
    	$stmt = $this->db->query($sql);
    	$arr = $stmt->fetchAll();
    	$rType="";
    	$min=$arr[0]["nbLog"];
    	$max=$min;
    	foreach ($arr as $type) {
    		$geos = $this->getGeoStat($this->db, "Type_Logement", "'GA','GD','GP','TO'", "IN", false, $idLieu);	    		
    		$rType[] = array("name"=>$type["Contrat"], "stat"=>"statio","nb"=>$type["nbLog"]);			
    		if($min > $type["nbLog"])$min = $type["nbLog"];
    		if($max < $type["nbLog"])$max = $type["nbLog"];
    	}
    	//rassemble les stats pour les stationnement					    	
        return array("name"=>"Nombres de stationnement", "min"=>$min, "max"=>$max,"visible"=>true,"stat"=>"statio","children"=>$rType);
    }

    /**
	* calcule la répartition des vacances locatives
    * @param int $idLieu
	* 
    * @return array
    */
    function typeVacances($idLieu=false){

    	$rStat = "";
    	
    	if($idLieu)
    		$from = " FROM gevu_lieux le 
				INNER JOIN gevu_lieux l ON le.lft BETWEEN l.lft AND l.rgt AND l.id_lieu = ".$idLieu;
    	else
    		$from = " FROM gevu_lieux le ";
    	
        //récupère les données Nb de vacance
    	$sql = "SELECT COUNT(*) nbLog, s.Categorie_Module, s.Occupation
			".$from."
			WHERE s.Categorie_Module IN ('L','G','C')
			GROUP BY s.Categorie_Module, s.Occupation
			ORDER BY s.Categorie_Module, s.Occupation";
    	
    	$sql = "SELECT COUNT(sLO.id_lieu) nbLO, COUNT(sLV.id_lieu) nbLV, COUNT(sGO.id_lieu) nbGO, COUNT(sGV.id_lieu) nbGV, COUNT(sCO.id_lieu) nbCO, COUNT(sCV.id_lieu) nbCV
			".$from."
    		LEFT JOIN gevu_stats sLO ON sLO.id_lieu = le.id_lieu AND sLO.Categorie_Module = 'L' AND sLO.Occupation = 'Occupé' 
			LEFT JOIN gevu_stats sLV ON le.id_lieu = sLV.id_lieu AND sLV.Categorie_Module = 'L' AND sLV.Occupation = 'Vacant' 
			LEFT JOIN gevu_stats sGO ON le.id_lieu = sGO.id_lieu AND sGO.Categorie_Module = 'G' AND sGO.Occupation = 'Occupé' 
			LEFT JOIN gevu_stats sGV ON le.id_lieu = sGV.id_lieu AND sGV.Categorie_Module = 'G' AND sGV.Occupation = 'Vacant' 
			LEFT JOIN gevu_stats sCO ON le.id_lieu = sCO.id_lieu AND sCO.Categorie_Module = 'C' AND sCO.Occupation = 'Occupé' 
			LEFT JOIN gevu_stats sCV ON le.id_lieu = sCV.id_lieu AND sCV.Categorie_Module = 'C' AND sCV.Occupation = 'Vacant'"; 
    	
    	$stmt = $this->db->query($sql);
    	$arr = $stmt->fetchAll();
    	$stats = "";
    	
	    $geos[] = $this->getGeoStat($this->db, array("Categorie_Module", "Occupation"), array("L", "Occupé"), false, $idLieu);	    		
    	$geos[] = $this->getGeoStat($this->db, array("Categorie_Module", "Occupation"), array("L", "Vacant"), false, $idLieu);	    		
		$geos[] = $this->getGeoStat($this->db, array("Categorie_Module", "Occupation"), array("G", "Occupé"), false, $idLieu);	    		
    	$geos[] = $this->getGeoStat($this->db, array("Categorie_Module", "Occupation"), array("G", "Vacant"), false, $idLieu);	    		
    	$geos[] = $this->getGeoStat($this->db, array("Categorie_Module", "Occupation"), array("C", "Occupé"), false, $idLieu);	    		
    	$geos[] = $this->getGeoStat($this->db, array("Categorie_Module", "Occupation"), array("C", "Vacant"), false, $idLieu);
    		    		
    	$rStat[] = array("name"=>"Logements","stat"=>"vacLog","visible"=>true
			,"children"=>array(
				array("name"=>"Occupé","stat"=>"vacLog","nb"=>$arr[0]["nbLO"],"geos"=>$geos[0])
				,array("name"=>"Vacant","stat"=>"vacLog","nb"=>$arr[0]["nbLV"],"geos"=>$geos[1])
				));			
    	$rStat[] = array("name"=>"Garages","stat"=>"vacGar","visible"=>true
			,"children"=>array(
				array("name"=>"Occupé","stat"=>"vacGar","nb"=>$arr[0]["nbGO"],"geos"=>$geos[2])
				,array("name"=>"Vacant","stat"=>"vacGar","nb"=>$arr[0]["nbGV"],"geos"=>$geos[3])
				));			
		$rStat[] = array("name"=>"Commerces","stat"=>"vacCom","visible"=>true
			,"children"=>array(
				array("name"=>"Occupé","stat"=>"vacCom","nb"=>$arr[0]["nbCO"],"geos"=>$geos[4])
				,array("name"=>"Vacant","stat"=>"vacCom","nb"=>$arr[0]["nbCV"],"geos"=>$geos[5])
				));								
        return array("name"=>"Vacances locatives","visible"=>true,"children"=>$rStat);
    }
    
    /**
	* calcule la répartition des ages
    * @param int $idLieu
	* 
    * @return array
    */
    function typeAge($idLieu=false){

    	$rStat = "";
    	
    	if($idLieu){
    		$join = "INNER JOIN gevu_lieux le ON le.id_lieu = s.id_lieu 
				INNER JOIN gevu_lieux l ON le.lft BETWEEN l.lft AND l.rgt AND l.id_lieu = ".$idLieu;
    	}
        //récupère les données d'age du patrimoine
    	$sql = "SELECT COUNT(*) nbLog, s.Annee_Construction, (YEAR(CURRENT_DATE()) - s.Annee_Construction) as age
			FROM gevu_stats s 
			".$join."
			WHERE s.Annee_Construction != ''
			GROUP BY s.Annee_Construction";
    	$stmt = $this->db->query($sql);
    	$arr = $stmt->fetchAll();
    	$min=$arr[0]["nbLog"];
    	$max=$min;
    	foreach ($arr as $type) {
    		$geos = $this->getGeoStat($this->db, "Annee_Construction", $type["Annee_Construction"], false, $idLieu);
			$rStat[] = array("name"=>$type["age"],"stat"=>"age","annee"=>$type["Annee_Construction"],"nb"=>$type["nbLog"],"geos"=>$geos);			
    		if($min > $type["nbLog"])$min = $type["nbLog"];
    		if($max < $type["nbLog"])$max = $type["nbLog"];
    	}
        return array("name"=>"Répartition par âge", "min"=>$min, "max"=>$max,"visible"=>true,"stat"=>"age","children"=>$rStat);
    }
    
	/**
	* récupère le Nb de bâtiments d'habitation pour un type donné
    * @param string 	$type
    * @param string 	$val
    * @param  string 	$op
    * @param string 	$nom
    * @param int 		$idLieu
    * 
    * @return array
    */
    function batNbType($nom, $type, $val, $op=false, $idLieu=false){
    	if($idLieu){
    		$join = "INNER JOIN gevu_lieux le ON le.id_lieu = s.id_lieu 
				INNER JOIN gevu_lieux l ON le.lft BETWEEN l.lft AND l.rgt AND l.id_lieu = ".$idLieu;
    	}
    	
    	$w = $this->getWhere($type, $val, $op);
    	
    	$sql = "SELECT COUNT(*)
				, COUNT(DISTINCT s.code_batiment) nbBat
			FROM gevu_stats s 
			".$join."
			".$w;
    	$stmt = $this->db->query($sql);
    	$arr = $stmt->fetchAll();
    	$geos = $this->getGeoStat($this->db, $type, $val, $op, $idLieu);
    	
    	if(is_array($type))
	    	$idStat = "NbBat-".implode($type,"_")."-".implode($val,"_");
	    else 
	    	$idStat = "NbBat-".$type."-".$val;
	    
    	return array("name"=>$nom,"visible"=>true, "stat"=>$idStat, "nb"=>$arr[0]['nbBat'],"geos"=>$geos);			
    }

	/**
	* récupère le Nb de logement d'habitation pour un type donné
    * @param string 	$type
    * @param string 	$val
    * @param  string 	$op
    * @param string 	$nom
    * @param int 		$idLieu
    * 
    * @return array
    */
    function logNbType($nom, $type, $val, $op=false, $idLieu=false){
    	if($idLieu){
    		$join = "INNER JOIN gevu_lieux le ON le.id_lieu = s.id_lieu 
				INNER JOIN gevu_lieux l ON le.lft BETWEEN l.lft AND l.rgt AND l.id_lieu = ".$idLieu;
    	}
    	
    	$w = $this->getWhere($type, $val, $op);
    	
    	$sql = "SELECT COUNT(*) nbLog
			FROM gevu_stats s 
			".$join."
			".$w;
    	$stmt = $this->db->query($sql);
    	$arr = $stmt->fetchAll();
    	$geos = $this->getGeoStat($this->db, $type, $val, $op, $idLieu);
    	
    	if(is_array($type))
	    	$idStat = "NbLog-".implode($type,"_")."-".implode($val,"_");
	    else 
	    	$idStat = "NbLog-".$type."-".$val;
    	
    	return array("name"=>$nom,"visible"=>true, "stat"=>$idStat, "nb"=>$arr[0]['nbLog'],"geos"=>$geos);			
    }
    
    
	/**
	* calcule le where pour une stat
    * @param string 	$type
    * @param string 	$val
    * @param  string 	$op
    * 
    * @return string
    */
    function getWhere($stat, $val, $op=false){

       	//création du where
       	$r = " WHERE ";
       	$w = "";
       	if(is_array($stat)){
       		for ($i = 0; $i < count($stat); $i++) {
	       		if($val=="non vide") $w .= $r." s.".$stat[$i]." != '' ";
	       		elseif($op=="LIKE") $w .= $r." s.".$stat[$i]." LIKE '".$val[$i]."' ";
	       		elseif($op=="IN") $w .= $r." s.".$stat[$i]." IN (".$val[$i].") ";
	       		else $w .= $r." s.".$stat[$i]." = '".$val[$i]."' ";
	       		$r = " AND ";       			
       		}       			
       	}else{
       		if($val=="non vide") $w = $r." s.".$stat." != '' ";
       		elseif($op=="LIKE")$w = $r." s.".$stat." LIKE '".$val."' ";
       		elseif($op=="IN") $w = $r." s.".$stat." IN (".$val.") ";
       		else $w = $r." s.".$stat." = '".$val."' ";
      	}

      	return $w;
    }
    
}
