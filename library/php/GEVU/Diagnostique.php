<?php
class GEVU_Diagnostique{
    
    public function getAll(){
    	
    	$t = new Model_DbTable_Gevu_lieux();
    	$r = $t->getAll();
    	return $r;
    	 
    /*
        mysql_connect('localhost', 'root', '');
        mysql_select_db('gevu_solus');
        $res = mysql_query('SELECT id_lieu,lib FROM gevu_lieux');
        while($row=mysql_fetch_assoc($res)){
            $t[]=$row;
        }
        return $t;*/
    }
    
	public function getSon($idParent=0){
    	$t = new Model_DbTable_Gevu_lieux();
    	$r = $t->findById_parent($idParent);
    	return $r;
    }
}
?>