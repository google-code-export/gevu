<?php
require_once( "../param/ParamAppli.php" );

class Model_DbTable_Gevu_Diagnostique{

    public function Model_DbTable_Gevu_Diagnostique(){
    
    }
    
    public function GetSon(){
        mysql_connect('localhost', 'root', '');
        mysql_select_db('gevu_solus');
        
        $res = mysql_query('SELECT id_lieu,lib FROM gevu_lieux');
        while($row=mysql_fetch_assoc($res)){
            $t[]=$row;
        }
        return $t;
    }
    
    public function GetParam(){/*
        $t = new Model_DbTable_Gevu_lieux()
        $t->getAll();
        return $t;*/
    }
}
?>