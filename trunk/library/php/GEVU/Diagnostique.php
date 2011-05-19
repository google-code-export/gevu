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
    	$z = new Model_DbTable_Gevu_lieux();
    	$r = $z->findById_lieu($idLieu);
    	$xml.="<node idLieu=\"".$r[0]['id_lieu']."\" lib=\"".$r[0]['lib']."\" niv=\"".$r[0]['niv']."\" fake=\"0\"";
    	
    	$r = $z->findByLieu_parent($idLieu);
    	if(count($r)==0){
    		$xml.=" />\n";
    	}
    	else{
    		$xml.=">\n";
    		foreach ($r as $v){
    			$xml.="<node idLieu=\"".$v['id_lieu']."\" lib=\"".$v['lib']."\" niv=\"".$v['niv']."\" fake=\"0\"";
    			$s = $z->findByLieu_parent($v['id_lieu']);
    			if(count($s)==0){
	    			$xml.=" />\n";
    			}else{
    				//$xml.=">\n<node idLieu=\"-10\" fake=\"1\" />\n</node>\n";
    				//-----------
    				$xml.=">\n";
    				foreach ($s as $w){
    					$xml.="<node idLieu=\"".$w['id_lieu']."\" lib=\"".$w['lib']."\" niv=\"".$w['niv']."\" fake=\"0\"";
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
		$dom = new DomDocument();
		$dom->loadXML($xml);
    	return $dom;
    }
}
?>
