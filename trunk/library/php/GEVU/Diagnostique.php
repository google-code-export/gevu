<?php

class GEVU_Diagnostique extends GEVU_Site{
    
	/**
	* cherche un lieu à partir d'un texte
	* renvoie les résultats 
    * @param string $txtLieu
    * @param string $idBase
    * @return Array
    */
	public function findLieu($txtLieu, $idBase=false){
		//connexion à la base
    	$db = $this->getDb($idBase);
    	//création de la table
        $dbL = new Models_DbTable_Gevu_lieux($db);
        $r = array();
        //recherche par identifiant
        $arr = $dbL->findById_lieu($txtLieu);
        $r['id'] = $this->setResultLieu($arr,$dbL);
        //recherche par lib
        $arr = $dbL->findByLib($txtLieu);
        $r['lib'] = $this->setResultLieu($arr,$dbL);
        
        return $r;
	}
    
	/**
	* formate le résultat d'une recherche de lieu 
    * @param Array $r
    * @param Models_DbTable_Gevu_lieux $dbLieu
    * @return Array
    */
	public function setResultLieu($r, $dbLieu){
        $result = array();
		//pour chaque lieu trouvé
        $nb = count($r);
        for ($i = 0; $i < $nb; $i++) {
        	//ajoute le fil d'ariane du lieux pour l'afficher dans l'aboressence
        	$arrL = $dbLieu->getFullPath($r[$i]['id_lieu']);
        	$result[] = $arrL; 
        }
		return $result;
	}
	
	
	/**
	 * récupère la descendance d'un noeud au format xml
    * @param int $idLieu
    * @param string $idBase
    * @param integer $nivMax
    * @return DomDocument
    */
	public function getXmlNode($idLieu=0, $idBase=false, $nivMax=3){
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idBase; 
	   	$xml = $this->cache->load($c);
        if(!$xml){
    		$xml="";
    		//connexion à la base
    		$db = $this->getDb($idBase);
    		//création de la table
        	$dbLieu = new Models_DbTable_Gevu_lieux($db);
        	$r = $dbLieu->findById_lieu($idLieu);
        	$xml = $this->getXmlEnfant($idLieu, $dbLieu, $nivMax);
	    	$this->cache->save($xml, $c);
        }
        $dom = new DomDocument();
        $dom->loadXML($xml);
        return $dom;
    }
        
    /**
     * Récupération des enfants d'un lieu
     *
     * @param integer $idLieu
     * @param Models_DbTable_Gevu_lieux $dbLieu
     * @param integer $nivMax
     * @param integer $niv
     * @return string
     */
    public function getXmlEnfant($idLieu, $dbLieu, $nivMax=1, $niv=0){
    	    	
    	$r = $dbLieu->findByLieu_parent($idLieu);
		$xml ="";$xmlEnf="";
		foreach ($r as $v){
        	$xml .="<node idLieu=\"".$v['id_lieu']."\" lib=\"".htmlspecialchars($v['lib'])."\" niv=\"".$v['niv']."\" fake=\"0\" >";
        	//vérifie s'il faut afficher les enfants
        	if($nivMax > $niv){
		    	//récupère le xml des enfants
	    		$xml .= $this->getXmlEnfant($v['id_lieu'], $dbLieu, $nivMax, $niv+1);
        	}else{
    			$xml .="<node idLieu=\"-10\" lib=\"loading...\" fake=\"1\" icon=\"voieIcon\" />";
	    	}
		    $xml .= "</node>\n";				
        }
        
        //création de la racine
    	if($niv==0){
        	$r = $dbLieu->findById_lieu($idLieu);
        	$xml ="<node idLieu=\"".$r[0]['id_lieu']."\" lib=\"".htmlspecialchars($r[0]['lib'])."\" niv=\"".$r[0]['niv']."\" fake=\"0\" >"
        		.$xml
        		."</node>\n";
    	}
        
		return $xml;
    }
    
    /**
     * Récupération des données liées à iun lieu
     *
     * @param int $idLieu
    * @param string $idBase
     * @return array
     */
    public function getNodeRelatedData($idLieu=0, $idBase=false){
    
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idBase; 
	   	$res = $this->cache->load($c);
    	        
        if(!$res){
            $res = array();
            
            //connexion à la base
    		$db = $this->getDb($idBase);
            
        	$dbL = new Models_DbTable_Gevu_lieux($db);
        	
            $res['ariane']=$dbL->getFullPath($idLieu);
	        
			$Rowset = $dbL->find($idLieu);
			$lieu = $Rowset->current();
            $dt = $dbL->getDependentTables();
            foreach($dt as $t){
				$items = $lieu->findDependentRowset($t);
				if($items->count()){
					//vérifie si on traite un cas particulier
	            	if($t=="Models_DbTable_Gevu_diagnostics" || $t=="Models_DbTable_Gevu_problemes"  || $t=="Models_DbTable_Gevu_observations" ){
	            		//récupère les informations seulement si ce n'est pas déjà fait
	            		if(!isset($res["___diagnostics"])){
				        	$d = new Models_DbTable_Gevu_diagnostics($db);
				        	$res["___diagnostics"] = $d->getAllDesc($idLieu);			
	            		}	
	            	}else{
						$res[$t]=$items->toArray();
	            	}
            	}
			}
            
            $this->cache->save($res, $c);
        }
        return $res;
    }
        
}

