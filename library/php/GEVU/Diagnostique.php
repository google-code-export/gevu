<?php

class GEVU_Diagnostique{
    
    var $manager;
    var $TableNames;
    
    function __construct(){    	
    	
        $frontendOptions = array(
            'lifetime' => 1, // temps de vie du cache de 2 heures 7200
            'automatic_serialization' => true
        );  
        $backendOptions = array(
            // Répertoire où stocker les fichiers de cache
            'cache_dir' => '../tmp/'
        ); 
        // créer un objet Zend_Cache_Core
        $this->manager = Zend_Cache::factory('Core',
                                     'File',
                                     $frontendOptions,
                                     $backendOptions); 
    }
    
    
	/**
	 * récupère la descendance d'un noeud au format xml
    * @param int $idLieu
    * @param string $idBase
    * @return DomDocument
    */
	public function getXmlNode($idLieu=0, $idBase=false){
	   $shhash = sha1("GEVU_Diagnostique-getXmlNode-$idBase-$idLieu");
	   $xml = $this->manager->load($shhash);
        if(!$xml){
    		$xml="";
    		//connexion à la base
    		$db = $this->getDb($idBase);
    		//création de la table
        	$z = new Models_DbTable_Gevu_lieux($db);
        	$r = $z->findById_lieu($idLieu);
        	$xml.="<node idLieu=\"".$r[0]['id_lieu']."\" lib=\"".htmlspecialchars($r[0]['lib'])."\" niv=\"".$r[0]['niv']."\" fake=\"0\"";
        	
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
    		$this->manager->save($xml, $shhash);
        }
        $dom = new DomDocument();
        $dom->loadXML($xml);
    	return $dom;
    }
    
    /**
     * retourne une connexion à une base de donnée suivant son nom
    * @param string $idBase
    * @return Zend_Db_Table
    */
    public function getDb($idBase){
    	
 		$db = Zend_Db_Table::getDefaultAdapter();
    	if($idBase){
    		//change la connexion à la base
			$arr = $db->getConfig();
			$arr['dbname']=$idBase;
			$db = Zend_Db::factory('PDO_MYSQL', $arr);	
    	}
    	return $db;
    }
    
    
    /**
     * Récupération des données liées à iun lieu
     *
     * @param int $idLieu
    * @param string $idBase
     * @return array
     */
    public function getNodeRelatedData($idLieu=0, $idBase=false){
    
        $shhash = sha1("GEVU_Diagnostique-NodeRelatedData-$idLieu");
        $res = $this->manager->load($shhash);
        
        if(!$res){
            $res = array();
            
            //connexion à la base
    		$db = $this->getDb($idBase);
            
        	$c = new Models_DbTable_Gevu_lieux($db);
        	
            $res['ariane']=$c->getFullPath($idLieu);
	        
			$Rowset = $c->find($idLieu);
			$lieu = $Rowset->current();
            $dt = $c->getDependentTables();
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
            
            $this->manager->save($res, $shhash);
        }
        return $res;
    }
    
    /**
    * @param int $idLieu
    */
    function removeNodeRelatedData($idLieu){
        $shhash = sha1("GEVU_Diagnostique-NodeRelatedData-$idLieu");
        $res = $this->manager->remove($shhash);
    }
    
}
?>
