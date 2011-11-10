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
        $r['id'] = $dbL->findById_lieu($txtLieu);
        //recherche par lib
        $r['lib'] = $dbL->findByLib($txtLieu);
        //ajoute les fils d'ariane pour chaque lieu trouvé
        //pour compléter l'arbre des territoires
        $nb = count($r['id']);
        for ($i = 0; $i < $nb; $i++) {
        	$arrL = $dbL->getFullPath($r[$i]['id_lieu'],'rgt');
        	
        }

        return $r;
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
        	$xml.="<node idLieu=\"".$r[0]['id_lieu']."\" lib=\"".htmlspecialchars($r[0]['lib'])."\" niv=\"".$r[0]['niv']."\" fake=\"0\" >";
        	$xml.= $this->getXmlEnfant($idLieu, $dbLieu, $nivMax);
	      	$xml.="</node>\n";
        	
        	/*
        	$r = $z->findByLieu_parent($idLieu);
        	if(count($r)==0){
        		$xml.=" />\n";
        	}
        	else{
        		$xml.=">\n";
        		foreach ($r as $v){
        			$xml.="<node idLieu=\"".$v['id_lieu']."\" lib=\"".htmlspecialchars($v['lib'])."\" niv=\"".$v['niv']."\" fake=\"0\"";
        			$s = $z->findByLieu_parent($v['id_lieu']);
        			if(count($s)==0){
    	    			$xml.=" />\n";
        			}else{
        				//$xml.=">\n<node idLieu=\"-10\" fake=\"1\" />\n</node>\n";
        				//-----------
        				$xml.=">\n";
        				foreach ($s as $w){
        					$xml.="<node idLieu=\"".$w['id_lieu']."\" lib=\"".htmlspecialchars($w['lib'])."\" niv=\"".$w['niv']."\" fake=\"0\"";
        					$t = $z->findByLieu_parent($w['id_lieu']);
        					if(count($t)==0){
    	    					$xml.=" />\n";
        					}else{
        						$xml.=">\n<node idLieu=\"-10\" lib=\"loading...\" fake=\"1\" icon=\"voieIcon\" />\n</node>\n";
        					}
        				}
        				$xml.="</node>\n";
        				//-----------
        			}
        		}
        		$xml.="</node>\n";
    		}
    		*/
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
		$xml ="";
		foreach ($r as $v){
        	$xml .="<node idLieu=\"".$v['id_lieu']."\" lib=\"".htmlspecialchars($v['lib'])."\" niv=\"".$v['niv']."\" fake=\"0\" >";
		    if($nivMax > $niv){
		    	$xml .= $this->getXmlEnfant($v['id_lieu'], $dbLieu, $nivMax, $niv+1);
		    }
		    $xml .="</node>\n";
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

