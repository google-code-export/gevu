<?php
class GEVU_ModifBase{

	 /**
     * @param integer $data
     * @param integer $existe
     * @param array $existe 
     */
	function updateTable($T, $id, $data){
		switch($T){
		case 0:
			break;
		
		case 1:
			break;
			
		case 2:
			break;
		
		case 3:
			break;
		
		case 4:
			break;
		
		case 5:
			break;
		
		case 6:
			break;
		
		case 7:
			break;
			
		case 8:
			break;
		
		case 9:
			break;
		
		case 10:
			break;
		
		case 11:
			break;
		
		case 12:
			break;
		
		case 13:
			$rr = new Model_DbTable_Gevu_objetsxvoiries();
			$rr->edit($id, $data);
			break;
			
		case 14:
			break;
		
		case 15:
			break;
		
		case 16:
			break;
		
		case 17:
			break;
		}
	}

}
?>