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
	    	array("Antenne","Type et nb. de logement", WEB_ROOT_AJAX."/graph/typelogement")
	    	//array("Antenne","Type et nb. de logement", WEB_ROOT."/graph/antenne/toutes-categories-antennes.html")
	    	,array("Antenne","Année de construction", WEB_ROOT."/graph/antenne/annee-construction.html")
	    	,array("Antenne","Type de bâtiment", WEB_ROOT."/graph/antenne/batiments-antenne.html")
	    	,array("Antenne","Arbre : type, nb. et vacance", WEB_ROOT."/graph/antenne/caracteristiques-antennes.html")
	    	,array("Antenne","Barre : type, nb. et vacance", WEB_ROOT."/graph/antenne/diag-barre.html")
	    	,array("Antenne","Barre : type et nb. de bâtiment", WEB_ROOT."/graph/antenne/graphique-en-barre.html")
	    	,array("Antenne","Nombre de logements ZUS", WEB_ROOT."/graph/antenne/log-zus.html")
	    	,array("Antenne","Nombre de logements collectifs / individuels", WEB_ROOT."/graph/antenne/logement-collectif-individuel.html")
	    	,array("Antenne","Nombre de copropriétés", WEB_ROOT."/graph/antenne/logement-copropriete.html")
	    	,array("Antenne","Nombre de stationnements", WEB_ROOT."/graph/antenne/nb_stationnement.html")
	    	,array("Antenne","Types de financements", WEB_ROOT."/graph/antenne/type-financement.html")
	    	//quel différence avec l'autre graphique à barre mise à part la couleur 
	    	//,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/type_logement.html")
	    	,array("Antenne","Nombre de vacance des commerces", WEB_ROOT."/graph/antenne/vac_commerce.html")
	    	,array("Antenne","Nombre de vacance garages", WEB_ROOT."/graph/antenne/vac_garage.html")
	    	,array("Antenne","Nombre de vacance garages : comparaison", WEB_ROOT."/graph/antenne/vac-garages.html")
	    	,array("Antenne","Nombre de vacances logements", WEB_ROOT."/graph/antenne/vac_logement.html")
	    	//ne marche pas ,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/vacances.html")
	    	//ne marche pas ,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/vac-logements.html")
	    	//ne marche pas ,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/vac-commerces.html")
	    	//ne marche pas ,array("Antenne","année de construction", WEB_ROOT."/graph/antenne/nb-stationnements.html")
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
