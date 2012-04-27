<?php

class GEVU_Statistique extends GEVU_Site{
        
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
	 * 
    * @return string
    */
	public function getArbreTypeLog(){
	   $c = str_replace("::", "_", __METHOD__); 
	   $s = $this->cache->load($c);
       if(!$s){

        	//récupère la hiérarchie des antennes - groupe - batiments
    		$sql = "SELECT la.lib libA, la.id_lieu idA, lg.lib libG, lg.id_lieu idG, lb.lib libB, lb.id_lieu idB, COUNT(s.id_lieu) nbStat, m.titre 
				FROM gevu_antennes a
				    INNER JOIN gevu_lieux la on la.id_lieu = a.id_lieu
					INNER JOIN gevu_lieux lg on lg.lieu_parent = la.id_lieu
					INNER JOIN gevu_lieux lb on lb.lieu_parent = lg.id_lieu
					INNER JOIN gevu_lieux le on le.lft BETWEEN lb.lft AND lb.rgt
					LEFT JOIN gevu_stats s on s.id_lieu = le.id_lieu
				    LEFT JOIN gevu_motsclefs m on m.code = s.Categorie_Module 
				GROUP BY la.id_lieu, lg.id_lieu, lb.id_lieu, m.id_motclef
				HAVING nbStat > 0";
       	
	        $db = Zend_Db_Table::getDefaultAdapter();
	    	$stmt = $db->query($sql);
	    	$arr = $stmt->fetchAll();

	    	$idA = -1; $idG = -1; $idB = -1;
	        $rs = array("name"=>"Alcéane","children"=>array());
	        foreach ($arr as $r) {
	        	if($idA != $r['idA']){
	        		if($idA != -1){
	        			$rs['children'][] = $rsA; 
	        		} 
	        		$rsA = array("name"=>$r['libA'],"children"=>array());
	        		$idA = $r['idA'];
	        	}
	        	if($idG != $r['idG']){
	        		if($idG != -1){
	        			$idB = $r['idB'];
	        			$rsA['children'][] = $rsG;
	        		} 
	        		$rsG = array("name"=>$r['libG'],"children"=>array());
	        		$idG = $r['idG'];
	        	}
	        	if($idB != $r['idB']){
	        		if($idB != -1){
	        			$rsG['children'][] = $rsB;
	        		} 
	        		$rsB = array("name"=>$r['libB'],"children"=>array());
	        		$idB = $r['idB'];
	        	}
	        	$rsL = array("name"=>$r['titre'],"size"=>$r['nbStat']);
	        	$rsB['children'][] = $rsL;
	        }
    		$this->cache->save($rs, $c);
        }
        
        return $rs;
    }
    
}
