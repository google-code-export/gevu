<?php
require_once( "../param/ParamAppli.php" );

// est-ce necessaire de faire un require sur lieux.php?
//require_once("lieux.php")

class Model_DbTable_Gevu_Diagnostique{
    
    public function GetSon(){
    	/*$t[]=1;
    	$t[]=2;
    	return $t;*/
        mysql_connect('localhost', 'root', '');
        mysql_select_db('gevu_solus');
        $res = mysql_query('SELECT id_lieu,lib FROM gevu_lieux');
        while($row=mysql_fetch_assoc($res)){
            $t[]=$row;
        }
        return $t;
    }
    
	public function getAll(){
    	$t = new Model_DbTable_Gevu_lieux();
    	$r = $t->getAll();
    	return $r;
    }
    
	public function getFils($idParent=0){
    	$t = new Model_DbTable_Gevu_lieux();
    	$r = $t->findById_parent(1);
    	return $r;
    }
}


/*
    public function getSon($idParent){
    	$t = new Model_DbTable_Gevu_lieux();
    	$r = $t->findById_parent(1);
    	return $r;
    }
    
	public function getAll(){
        mysql_connect('localhost', 'root', '');
        mysql_select_db('gevu_solus');
        
        $res = mysql_query('SELECT id_lieu,lib FROM gevu_lieux');
        while($row=mysql_fetch_assoc($res)){
            $t[]=$row;
        }
        return $t;
    }
*/
?>
