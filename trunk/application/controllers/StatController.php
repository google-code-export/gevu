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
	
}
