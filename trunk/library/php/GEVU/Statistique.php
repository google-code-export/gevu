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
