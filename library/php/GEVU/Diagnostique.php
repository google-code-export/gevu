<?php

class GEVU_Diagnostique{
    
    var $manager;
    
    function __construct(){
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
    	return $r;
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
        						$xml.=">\n<node idLieu=\"-10\" lib=\"loading...\" fake=\"1\" />\n</node>\n";
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
    * @return int
    */
    public function getNodeType($idLieu=0){
        $table = new Model_DbTable_Gevu_lieux();
               
        $s = $table	->select()
                    ->from( array("g" => "gevu_lieux"),array('Bi' => '(1)') )                           
                    ->where( "g.id_lieu = ?", $idLieu )
                    ->group("Bi");
               
        $rows = $table->fetchAll($s)->toArray();
        if(count($rows)>0) $result[]=$rows[0];
    
    
    	$table = new Model_DbTable_Gevu_lieux();

		$s = $table->select()
		->from( array("g" => "gevu_batiments"),array("Bi" => "(0)") )		->where( "g.id_lieu = ?", $idLieu )		->group("Bi");
		$rows = $table->fetchAll($s)->toArray();
		if(count($rows)>0) $result[]=$rows[0];

		
        
        
        $iu=5;
        
        
		/*******************************************/
        /*******************************************/
        /*$STRING = "SET @id=3;
				   (SELECT 0 Bi FROM gevu_batiments g WHERE g.id_lieu=@id)";
        $db = Zend_Db_Table::getDefaultAdapter();
    	$stmt = $db->query($STRING);
    	$stmt->setFetchMode(Zend_Db::FETCH_OBJ);
    	$result = $stmt->fetchAll();*/
    	
        /*******************************************/
    	/*******************************************/
        
        
        return $iu;
    }
}
?>
