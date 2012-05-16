<?php

/**
 * StatController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class GraphController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$this->view->title = "Graphiques disponibles";
		$this->view->json = $this->_getParam('json', 0);
		$this->view->headTitle($this->view->title, 'PREPEND');
	    $this->view->stats = array(
	    	array("Antenne","type et nb. de logement", WEB_ROOT_AJAX."/graph/typelogement")
	    	,array("Antenne","nb. de logement", WEB_ROOT."http://localhost/gevu/stats/antenne/batiments-antenne.html")
	    	,array("Antenne","Selection d'un logement", WEB_ROOT."/graph/Graphiques-Nombre-vacances-types/ESSAI.html")
	    	);
	}

	/**
	 * statistiques pour les antennes
	 */
	public function typelogementAction() {
		
		try {

			$db = new Models_DbTable_Gevu_motsclefs();
			//récupère les types de logement
			$this->view->typesLog = $db->getAllByType(54);
			
			
		}catch (Zend_Exception $e) {
	          echo "Récupère exception: " . get_class($e) . "\n";
	          echo "Message: " . $e->getMessage() . "\n";
		}
			
	}
	
}
