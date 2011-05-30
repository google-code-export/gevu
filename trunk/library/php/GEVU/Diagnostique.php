<?php

class GEVU_Diagnostique{
    
    var $manager;
    var $TableNames;
    
    function __construct(){
    
    	$this->TableNames = array(
    		"batiments", 
			"diagnostics", 
			"diagnosticsxvoirie", 
			"docsxlieux", 
			"espaces", 
			"espacesxexterieurs", 
			"espacesxinterieurs", 
			"etablissements", 
			"georss", 
			"geos", 
			"niveaux", 
			"objetsxexterieurs", 
			"objetsxinterieurs", 
			"objetsxvoiries", 
			"observations", 
			"parcelles", 
			"problemes", 
			"synchros"
    	);
    	
    	
        $frontendOptions = array(
            'lifetime' => 7200, // temps de vie du cache de 2 heures
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
    * @return array
    */
    public function getAll(){
    	
    	$t = new Model_DbTable_Gevu_lieux();
    	$r = $t->getAll();
    	return $r;
    }
    
    /**
    * @param int $idParent
    * @return array
    */
	public function getSon($idParent=0){
    	$t = new Model_DbTable_Gevu_lieux();
    	$r = $t->findByLieu_parent($idParent);
    	return $r;
    }
    
	/**
    * @param int $idLieu
    * @return array
    */
	public function getFields($idLieu=0){
    	$t = new Model_DbTable_Gevu_lieux();
    	$r = $t->findById_lieu($idLieu);
    	$tmp = $this->getNodeType($idLieu);
    	$r[0]['type'] = $tmp;
    	return $r[0];
    }
    
	/**
    * @param int $idLieu
    * @return string
    */
	public function getXmlNode($idLieu=0){
	   $mdhash = md5("GEVU_Diagnostique-getXmlNode-$idLieu");
	   $xml = $this->manager->load($mdhash);
        if(!$xml){
    		$xml="";
        	$z = new Model_DbTable_Gevu_lieux();
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
    		$this->manager->save($xml, $mdhash);
        }
        $dom = new DomDocument();
        $dom->loadXML($xml);
    	return $dom;
    }
    
    /**
    * @param int $idLieu
    * @return array
    */
    public function getNodeType($idLieu=0){
        /*$table = new Model_DbTable_Gevu_lieux();
               
        $s = $table	->select()
                    ->from( array("g" => "gevu_lieux"),array('Bi' => '(1)') )                           
                    ->where( "g.id_lieu = ?", $idLieu )
                    ->group("Bi");
               
        $rows = $table->fetchAll($s)->toArray();
        if(count($rows)>0) $result[]=$rows[0];

		$ss = $table->select()
					->from( array("g" => "gevu_batiments"),array("Bi" => "(0)") )
					->where( "g.id_lieu = ?", $idLieu )
					->group("Bi");
		$rows = $table->fetchAll($ss);
		if(count($rows)>0) $result[]=$rows[0];*/
        
        
		/*******************************************/
        /*******************************************/
        $str=  "(SELECT 0 Bi FROM gevu_batiments g WHERE g.id_lieu=$idLieu)  UNION
        		(SELECT 1 Bi FROM gevu_diagnostics g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 2 Bi FROM gevu_diagnosticsxvoirie g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 3 Bi FROM gevu_docsxlieux g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 4 Bi FROM gevu_espaces g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 5 Bi FROM gevu_espacesxexterieurs g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 6 Bi FROM gevu_espacesxinterieurs g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 7 Bi FROM gevu_etablissements g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 8 Bi FROM gevu_georss g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 9 Bi FROM gevu_geos g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 10 Bi FROM gevu_niveaux g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 11 Bi FROM gevu_objetsxexterieurs g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 12 Bi FROM gevu_objetsxinterieurs g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 13 Bi FROM gevu_objetsxvoiries g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 14 Bi FROM gevu_observations g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 15 Bi FROM gevu_parcelles g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 16 Bi FROM gevu_problemes g WHERE g.id_lieu=$idLieu)  UNION
				(SELECT 17 Bi FROM gevu_lieux g1 INNER JOIN gevu_synchros g2 ON g1.id_lieu=g2.id_lieu WHERE g1.id_lieu=@id);";
				
        $db = Zend_Db_Table::getDefaultAdapter();
    	$stmt = $db->query($str);
    	$stmt->setFetchMode(Zend_Db::FETCH_NUM);
    	$result = $stmt->fetchAll();
    	
        /*******************************************/
    	/*******************************************/
    	
    	for($i=0; $i<count($result); ++$i){
    		$res[]=$this->TableNames[$result[$i][0]];
    	}
    	
        return $res;
    }
}
?>
