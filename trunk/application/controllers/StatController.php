<?php

/**
 * StatController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class StatController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$this->view->title = "Statistiques disponibles";
	    $this->view->headTitle($this->view->title, 'PREPEND');
	    $this->view->stats = array(
	    	array("Antenne : type et nb. de logement", "stat/antenne?data=type")
	    	);
	}

	/**
	 * statistiques pour le logement
	 */
	public function antenneAction() {
		$db = new Models_DbTable_Gevu_antennes();
		$this->view->stats = $db->getStatType();
		
	}
	
	/**
	 * 	AC      ACCESSION       
		CA      CAVES   
		DI      DIVERS  
		D2      DEUX PIECES DUPLEX      
		D3      TROIS PIECES DUPLEX     
		D4      QUATRE PIECES DUPLEX    
		D5      CINQ PIECES DUPLEX      
		D6      SIX PIECES DUPLEX       
		D7      SEPT PIECES DUPLEX      
		FO      FOYERS  
		GA      GARAGE  
		GP      PARKING COUVERT 
		LO      LOCAL   
		LV      LOCAL A VELO    
		P1      PAVILLON 1 PIECE        
		P2      PAVILLON 2 PIECES       
		P3      PAVILLON 3  PIECES      
		P4      PAVILLON 4 PIECES       
		P5      PAVILLON 5 PIECES       
		P6      PAVILLON 6 PIECES       
		P7      PAVILLON 7 PIECES       
		P8      PAVILLON 8 PIECES       
		R4      QUATRE PIECE TRIPLEX    
		R5      CINQ PIECES  TRIPLEX    
		TO      PARKING EXTERIEUR       
		T1      STUDIO  
		T2      DEUX PIECES     
		T3      TROIS PIECES    
		T4      QUATRE PIECES   
		T5      CINQ PIECES     
		T6      SIX PIECES      
		T7      SEPT PIECES     
		T8      HUIT PIECES ET PLUS     
		1B      STUDIO CUISINE  
	 * 
	 */
	
	
}
