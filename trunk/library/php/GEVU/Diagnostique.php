<?php
class GEVU_Diagnostique{
    
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
		$xml="";
    	$t = new Model_DbTable_Gevu_lieux();
    	$r = $t->findById_lieu($idLieu);
    	$xml.="<node idLieu=\"".$r[0]['id_lieu']."\" lib=\"".$r[0]['lib']."\" niv=\"".$r[0]['niv'];
    	
    	$r = $t->findByLieu_parent($idLieu);
    	if(count($r)==0){
    		$xml.="\" />\n";
    	}
    	else{
    		$xml.="\">\n";
    		foreach ($r as $v){
    			$s = $t->findByLieu_parent($v['id_lieu']);
    			if(count($s)==0){
	    			$xml.="<node idLieu=\"".$v['id_lieu']."\" lib=\"".$v['lib']."\" niv=\"".$v['niv']."\" />\n";
    			}else{
    				$xml.="<node idLieu=\"".$v['id_lieu']."\" lib=\"".$v['lib']."\" niv=\"".$v['niv']."\">\n<node id=\"-10\"/>\n</node>\n";
    			}
    		}
    		$xml.="</node>\n";
		}
    	return $xml;
    }
}
?>
