<?php
class GEVU_ModifBase{

	 /**
     * @param integer $T
     * @param integer $id
     * @param array $data 
     */
	function updateTable($T, $id, $data){
		switch($T){
		case 0:
            $rr = new Model_DbTable_Gevu_batiments;
            $rr->edit($id, $data);
			break;
		
		case 1:
			break;
			
		case 2:
            $rr = new Model_DbTable_Gevu_diagnosticsxvoirie;
            $rr->edit($id, $data);
			break;
		
		case 3:
			break;
		
		case 4:
            $rr = new Model_DbTable_Gevu_espaces;
            $rr->edit($id, $data);
			break;
		
		case 5:
            $rr = new Model_DbTable_Gevu_espacesxexterieurs();
            $rr->edit($id, $data);
			break;
		
		case 6:
            $rr = new Model_DbTable_Gevu_espacesxinterieurs();
            $rr->edit($id, $data);
			break;
		
		case 7:
            $rr = new Model_DbTable_Gevu_etablissements();
            $rr->edit($id, $data);
			break;
			
		case 8:
			break;
		
		case 9:
			break;
		
		case 10:
            $rr = new Model_DbTable_Gevu_niveaux();
            $rr->edit($id, $data);
			break;
		
		case 11:
            $rr = new Model_DbTable_Gevu_objetsxexterieurs();
            $rr->edit($id, $data);
			break;
		
		case 12:
            $rr = new Model_DbTable_Gevu_objetsxinterieurs();
            $rr->edit($id, $data);
			break;
		
		case 13:
			$rr = new Model_DbTable_Gevu_objetsxvoiries();
			$rr->edit($id, $data);
			break;
			
		case 14:
			break;
		
		case 15:
            $rr = new Model_DbTable_Gevu_parcelles();
            $rr->edit($id, $data);
			break;
		
		case 16:
			break;
		
		case 17:
			break;
		}
	}
	
	
    /**
     * @param integer $T
     * @param integer $id 
     */
    function deleteNode($T, $id){
        switch($T){
        case 0:
            $rr = new Model_DbTable_Gevu_batiments;
            $rr->remove($id);
            break;
        
        case 1:
            break;
            
        case 2:
            $rr = new Model_DbTable_Gevu_diagnosticsxvoirie;
            $rr->remove($id);
            break;
        
        case 3:
            break;
        
        case 4:
            $rr = new Model_DbTable_Gevu_espaces;
            $rr->remove($id);
            break;
        
        case 5:
            $rr = new Model_DbTable_Gevu_espacesxexterieurs();
            $rr->remove($id);
            break;
        
        case 6:
            $rr = new Model_DbTable_Gevu_espacesxinterieurs();
            $rr->remove($id);
            break;
        
        case 7:
            $rr = new Model_DbTable_Gevu_etablissements();
            $rr->remove($id);
            break;
            
        case 8:
            break;
        
        case 9:
            break;
        
        case 10:
            $rr = new Model_DbTable_Gevu_niveaux();
            $rr->remove($id);
            break;
        
        case 11:
            $rr = new Model_DbTable_Gevu_objetsxexterieurs();
            $rr->remove($id);
            break;
        
        case 12:
            $rr = new Model_DbTable_Gevu_objetsxinterieurs();
            $rr->remove($id);
            break;
        
        case 13:
            $rr = new Model_DbTable_Gevu_objetsxvoiries();
            $rr->remove($id);
            break;
            
        case 14:
            break;
        
        case 15:
            $rr = new Model_DbTable_Gevu_parcelles();
            $rr->remove($id);
            break;
        
        case 16:
            break;
        
        case 17:
            break;
        }
    }

}
?>