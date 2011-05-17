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
    * @param  string $idParent
    * @return array
    */
	public function getSon($idParent=0){
    	$t = new Model_DbTable_Gevu_lieux();
    	$r = $t->findById_parent($idParent);
    	return $r;
    }
}
?>