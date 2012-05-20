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
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/annee-construction.html")
	    	,array("Antenne","bâtiment", WEB_ROOT."/graph/antenne/batiments-antenne.html")
	    	,array("Antenne","caractéristiques", WEB_ROOT."/graph/antenne/caracteristiques-antennes.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/diag-barre.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/graphique-en-barre.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/log-zus.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/logement-collectif-individuel.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/logement-copropriete.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/nb_stationnement.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/toutes-categories-antennes.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/type-financement.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/type_logement.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/vac_commerce.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/vac_garage.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/vac_logement.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/vacances.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/vac-garages.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/vac-logements.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/vac-commerces.html")
	    	,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/nb-stationnements.html")
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
