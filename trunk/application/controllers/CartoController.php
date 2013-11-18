<?php

/**
 * StatController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class CartoController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$this->view->title = "Cartographies disponibles";
		$this->view->json = $this->_getParam('json', 0);
		$this->view->headTitle($this->view->title, 'PREPEND');
	    $this->view->stats = array(
	    	array("Patrimoine","Cartographie des DPE", WEB_ROOT_AJAX."/graph/typelogement")
	    	);
	}

	/**
	 * cartographie des diagnostics du patrimoine
	 */
	public function patrimoinedpeAction() {
		
		try {
			$this->view->idBase = $this->_getParam('idBase', false);			
		}catch (Zend_Exception $e) {
	          echo "Récupère exception: " . get_class($e) . "\n";
	          echo "Message: " . $e->getMessage() . "\n";
		}
			
	}

	/**
	 * cartographie des diagnostics des âges du patrimoine
	 */
	public function patrimoineageAction() {
		
		try {
			$this->view->idBase = $this->_getParam('idBase', false);			
		}catch (Zend_Exception $e) {
	          echo "Récupère exception: " . get_class($e) . "\n";
	          echo "Message: " . $e->getMessage() . "\n";
		}
			
	}

	/**
	 * cartographie des données générales du patrimoine
	 */
	public function patrimoinedongenAction() {
		
		try {
			$this->view->idBase = $this->_getParam('idBase', false);			
		}catch (Zend_Exception $e) {
	          echo "Récupère exception: " . get_class($e) . "\n";
	          echo "Message: " . $e->getMessage() . "\n";
		}
			
	}
	
	/**
	 * cartographie des données générales de l'antenne
	 */
	public function antennedongenAction() {
		
		try {
			$this->view->idBase = $this->_getParam('idBase', false);
			$this->view->idLieu = $this->_getParam('idLieu', false);
			$s = new GEVU_Site();
			$db = $s->getDb($this->view->idBase);
			$dbA = new Models_DbTable_Gevu_antennes($db);
			$this->view->data = $dbA->findByIdLieu($this->_getParam('idLieu', -1)); 
						
		}catch (Zend_Exception $e) {
	          echo "Récupère exception: " . get_class($e) . "\n";
	          echo "Message: " . $e->getMessage() . "\n";
		}
			
	}
	
	
}
