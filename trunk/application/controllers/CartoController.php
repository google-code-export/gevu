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
	 * statistiques pour le patrimoine
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
	 * statistiques pour le patrimoine
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
	 * statistiques pour le patrimoine
	 */
	public function patrimoinedongenAction() {
		
		try {
			$this->view->idBase = $this->_getParam('idBase', false);			
		}catch (Zend_Exception $e) {
	          echo "Récupère exception: " . get_class($e) . "\n";
	          echo "Message: " . $e->getMessage() . "\n";
		}
			
	}
	
	
	
}
