<?php
class GEVU_Solution{
    /**
    * @param  string $name
    * @param  string $greeting
    * @return string
    */
    public function hello($name, $greeting = 'Hello')
    {
    	return $greeting . ', ' . $name;
    }
    /**
    * @return recorset
    */
    public function getAll()
    {
    	$dbSolus = new Model_DbTable_Gevu_solutions();
    	$rs = $dbSolus->getAll();
    	return $rs->toArray();
    }

	public function getCols(){

    	$arr = array("cols"=>array(
    		array("titre"=>"Mise à jour","champ"=>"maj","visible"=>true)
    		, array("titre"=>"Id. solution","champ"=>"id_solution","visible"=>true)
    		, array("titre"=>"Libellé","champ"=>"lib","visible"=>true)
    		, array("titre"=>"Type de solution","champ"=>"id_type_solution","visible"=>true,"objName"=>"Model_DbTable_Gevu_typesxsolutions")    		
    		));    	
    	return $arr;
		
    }        

	public function edit($id, $data){

    	$dbSolus = new Model_DbTable_Gevu_solutions();
    	$rs = $dbSolus->edit($id,$data);
    			
    }        
    
}
?>