<?php

/**
 * PspController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class PspController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$this->view->title = "Bilan disponibles";
	    $this->view->headTitle($this->view->title, 'PREPEND');
	    $this->view->stats = array(
	    	array("Bilan général", WEB_ROOT_AJAX."/psp/bilangeneral")
	    	, array("Bilan criètere", WEB_ROOT_AJAX."/psp/bilancritere")
	    	, array("Plan d'investissement", WEB_ROOT_AJAX."/psp/planinvestissement")
	    	);
	}

	/**
	 * 
	 */
	public function bilangeneralAction() {
		$this->view->idBase = $this->_getParam('idBase', false);			
		$oS = new GEVU_Statistique();
		$this->view->rs = $oS->getPatrimoinePSP($this->view->idBase, $this->getWhere());
					
	}

	/**
	 * 
	 */
	public function bilancritereAction() {
		$this->view->idBase = $this->_getParam('idBase', false);			
		$oS = new GEVU_Statistique();
		$this->view->rs = $oS->getPatrimoinePSP($this->view->idBase, $this->getWhere());
	}

	/**
	 * 
	 */
	public function planinvestissementAction() {
		$this->view->idBase = $this->_getParam('idBase', false);			
		$oS = new GEVU_Statistique();
		$this->view->rs = $oS->getPSP($this->view->idBase, $this->getWhere());
	}
	
	/**
	 * 
	 */
	function getWhere(){
		$where = "";
		if($this->_getParam('ant', false))$where="Ant='".$this->_getParam('ant')."'";
		if($this->_getParam('grp', false))$where="pgr='".$this->_getParam('grp')."'";
		if($this->_getParam('bat', false))$where="code_bat='".$this->_getParam('bat')."'";
		return $where;
	}
	
}
