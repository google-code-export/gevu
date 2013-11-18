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
	    	array("Globale : type de logement", WEB_ROOT_AJAX."/stat/globale?type=TypeLog")
	    	,array("Antenne : type et nb. de logement", WEB_ROOT_AJAX."/stat/antenne?type=ArbreTypeLog")
	    	,array("Antenne : nb de stationnement", WEB_ROOT_AJAX."/stat/antenne?type=garage")
	    	,array("Antenne : vacance des logements", WEB_ROOT_AJAX."/stat/antenne?type=vac_log")
	    	,array("Antenne : vacance des commerces", WEB_ROOT_AJAX."/stat/antenne?type=vac_com")
	    	,array("Antenne : vacance des garages", WEB_ROOT_AJAX."/stat/antenne?type=vac_gar")
	    	,array("Antenne : logement collectif/individuel", WEB_ROOT_AJAX."/stat/antenne?type=col_log")
	    	,array("Antenne : logement en ZUS", WEB_ROOT_AJAX."/stat/antenne?type=zus_log")
	    	,array("Antenne : logement co-propriétaire", WEB_ROOT_AJAX."/stat/antenne?type=copro")
	    	,array("Antenne : type de financement", WEB_ROOT_AJAX."/stat/antenne?type=financement")
	    	,array("Antenne : âge du patrimoine", WEB_ROOT_AJAX."/stat/antenne?type=age")
	    	,array("Antenne : géolocalisation", WEB_ROOT_AJAX."/stat/antenne?type=geoTypeLog")
	    	
	    	);
	}

	/**
	 * statistiques pour les antennes
	 */
	public function antenneAction() {
		
		try {			
			$db = new Models_DbTable_Gevu_antennes();
			$s = new GEVU_Statistique();
			
			if ($this->_getParam('type', 0)){
				$type = $this->_getParam('type', 0);
				if($type == "ArbreTypeLog"){
					$this->view->stats = $s->getArbreTypeLog($this->_getParam('typeLog', 0));
				}elseif($type == "geoTypeLog"){
					$this->view->stats = $s->getArbreTypeLog($this->_getParam('typeLog', 0));
				}else{
					$this->view->stats = $db->getStatType($type);			
				}
			}else{
				$this->view->stats = $db->getStatType();			
			}
		}catch (Zend_Exception $e) {
	          echo "Récupère exception: " . get_class($e) . "\n";
	          echo "Message: " . $e->getMessage() . "\n";
		}
			
	}

	/**
	 * statistiques globale
	 */
	public function globaleAction() {
		
		if($this->_getParam('type', 0)){
			$type = $this->_getParam('type', 0);
			if($type=="TypeLog"){
				$db = new Models_DbTable_Gevu_motsclefs();
				//récupère les types de logement
				$this->view->stats = $db->getAllByType(54);
			}
			if($type=="patrimoineDonGen"){
				$oStat = new GEVU_Statistique($this->_getParam('idBase', false));
				$json = $oStat->getPatrimoineDonGen($this->_getParam('idBase', false));
				$this->view->stats = $json;			
			}
			if($type=="patrimoineDiag"){
				$oStat = new GEVU_Statistique($this->_getParam('idBase', false));
				$json = $oStat->getPatrimoineDiag($this->_getParam('idBase', false));
				$this->view->stats = $json;			
			}
			if($type=="antenneDonGen"){
				$oStat = new GEVU_Statistique($this->_getParam('idBase', false));
				$json = $oStat->getAntenneDonGen($this->_getParam('idBase', false), $this->_getParam('idLieu', -1));
				$this->view->stats = $json;			
			}
		}
			
	}
	
}
