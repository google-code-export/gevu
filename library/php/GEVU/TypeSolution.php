<?php
class GEVU_TypeSolution{

    /**
    * @return recorset
    */
    public function getAll()
    {
    	$db = new Model_DbTable_Gevu_types_solutions();
    	$rs = $db->get();
    	return $rs->toArray();
    }

}
?>