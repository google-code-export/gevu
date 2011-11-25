<?php

class GEVU_Diagnostique extends GEVU_Site{
    	
	/**
	* calcul le diagnostic d'un lieu
	* et renvoie les résultats 
    * @param string $idLieu
    * @param string $idBase
    * @return Array
    */
	public function calculDiagForLieu($idLieu, $idBase=false){
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idBase; 
	   	$r = $this->cache->load($c);
        if(!$r){
			$this->idBase = $idBase;
			//connexion à la base
	    	$db = $this->getDb($idBase);
	    	//création de la table
	        $dbL = new Models_DbTable_Gevu_lieux($db);
	        
	        //récupère les diags oui
	        $r['DiagOui'] = $dbL->getDiagReponse($idLieu, 1);
	        //récupère les diags non
	        $r['DiagNon'] = $dbL->getDiagReponse($idLieu, 2);
	        //récupère tous les diags
	        $r['DiagTot'] = $dbL->getDiagReponse($idLieu);
	        
	        $this->cache->save($r, $c);
        }        
	        
        return $r;
	}
	
	/**
	* cherche un lieu à partir d'un texte
	* renvoie les résultats 
    * @param string $txtLieu
    * @param string $idBase
    * @return Array
    */
	public function findLieu($idLieu=-1, $txtLieu="", $idBase=false){
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idBase; 
	   	$r = $this->cache->load($c);
        if(!$r){
			$this->idBase = $idBase;
			//connexion à la base
	    	$db = $this->getDb($idBase);
	    	//création de la table
	        $dbL = new Models_DbTable_Gevu_lieux($db);
	        $r = array();
	        if($idLieu){
		        //recherche par identifiant
		        $arr = $dbL->findById_lieu($idLieu);
		        $r['id'] = $this->setResultLieu($arr,$dbL);
		        $r['lib'] = "";
	        }
	        if($txtLieu){
		        //recherche par lib
		        $arr = $dbL->findByLib($txtLieu);
		        $r['lib'] = $this->setResultLieu($arr,$dbL);
				$r['id'] = ""; 
	        }
	    	$this->cache->save($r, $c);
        }        
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
        	//ajoute le fil d'ariane du lieux pour afficher les résultats
        	$arrL = $dbLieu->getFullPath($r[$i]['id_lieu']);
        	//création du xml pour afficher dans l'arboressence
			$dom = false;
			$j = 1;
			$nbA = count($arrL);
        	foreach ($arrL as $L){
        		if(!$dom){
					$dom = new DomDocument();
					$dom->loadXML($this->getXmlLieu($L)."</node>");        			
					$foo = $dom->documentElement;
        		}else{
					$d = new DomDocument();
					//vérifie s'il faut ajouter les noeuds enfants de l'item trouvé
					if($j == $nbA){
						$d = $this->getXmlNode($L['id_lieu'],$this->idBase);
					}else{
						$d->loadXML($this->getXmlLieu($L)."</node>");
					}
					if($d){
						$mNewNode = $dom->importNode($d->documentElement, true);
						$foo->appendChild($mNewNode);
					}        			
					$foo = $foo->lastChild;
        		}
        		$j ++;
			}
			$s = $dom->saveXML();        						
        	$result[] = array("ariane"=>$arrL,"tree"=>$dom); 
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
        if($xml!=""){
	        $dom = new DomDocument();
	        $dom->loadXML($xml);
        }else{
	        $dom = false;
        }
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
        	$xml .= $this->getXmlLieu($v);
        	//vérifie s'il faut afficher les enfants
        	if($v['nbDiag']>0) return "";
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
        	$xml = $this->getXmlLieu($r[0])
        		.$xml
        		."</node>\n";
    	}
        
		return $xml;
    }
    
    /**
     * Récupération du format xml d'un lieu
     *
     * @param array $rLieu
     * @return string
     */
    public function getXmlLieu($rLieu){
        return "<node idLieu=\"".$rLieu['id_lieu']."\" lib=\"".htmlspecialchars($rLieu['lib'])."\" niv=\"".$rLieu['niv']."\" fake=\"0\" >";
    }

    /**
     * Récupération d'un diagnostic complet
     *
     * @param int $idLieu
     * @param string $idBase
     * @return array
     */
    public function getDiagComplet($idLieu=0, $idBase=false){
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idBase; 
	   	$rs = $this->cache->load($c);
        if(!$rs){           
            //connexion à la base
    		$db = $this->getDb($idBase);            
			//création des tables 
        	$d = new Models_DbTable_Gevu_diagnostics($db);
        	$p = new Models_DbTable_Gevu_problemes($db);
        	$o = new Models_DbTable_Gevu_observations($db);
        	
        	$rs['questions'] = $d->getAllDesc($idLieu);
        	$rs['problemes'] = $p->findById_lieu($idLieu);
        	$rs['observations'] = $o->findById_lieu($idLieu);

        	$this->cache->save($rs, $c);
        }
        return $rs;
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
            
    		//création de la table lieux
        	$dbL = new Models_DbTable_Gevu_lieux($db);
			//création de la table diagnostics
        	$d = new Models_DbTable_Gevu_diagnostics($db);
        	
        	//création du fil d'ariane
            $res['ariane']=$dbL->getFullPath($idLieu);
	        
            //récupération des données courantes
			$Rowset = $dbL->find($idLieu);
			$lieu = $Rowset->current();
			
			//récupération des données lieés
            $dt = $dbL->getDependentTables();
            foreach($dt as $t){
				$items = $lieu->findDependentRowset($t);
				if($items->count()){
					//vérifie si on traite un cas particulier
	            	if($t=="Models_DbTable_Gevu_diagnostics" || $t=="Models_DbTable_Gevu_problemes"  || $t=="Models_DbTable_Gevu_observations" ){
	            		//récupère les informations seulement si ce n'est pas déjà fait
	            		// car une même requête renvoie les infromations des 3 tables
	            		if(!isset($res["___diagnostics"])){
				        	$res["___diagnostics"] = $d->getCampagnes($idLieu);			
	            		}	
	            	}else{
						$res[$t]=$items->toArray();
	            	}
            	}
			}
			//vérifie si les enfants ont des diagnostics
			$rs = $dbL->findByLieu_parent($idLieu);
			foreach ($rs as $r){
				if($r['nbDiag']>0){
					//récupère les informations de l'enfant
					//$resEnf = $this->getNodeRelatedData($r['id_lieu'],$idBase);
					$resEnf = $d->getCampagnes($r['id_lieu']);
					if(count($res["___diagnostics"])== 0)$res["___diagnostics"]=$resEnf;
					else $res["___diagnostics"] = array_merge($res["___diagnostics"], $resEnf);			
				}
			}
            
            $this->cache->save($res, $c);
        }
        return $res;
    }
        
}

