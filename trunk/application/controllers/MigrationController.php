<?php

class MigrationController extends Zend_Controller_Action
{


    public function indexAction()
    {
    	$idBaseSrc = $this->_getParam('idBaseSrc', false);			
    	$idRegSrc = $this->_getParam('idRegSrc', false);			
    	$idBaseDst = $this->_getParam('idBaseDst', false);			
    	$idRegDst = $this->_getParam('idRegDst', false);			
    	$idExi = $this->_getParam('idExi', false);			
    	if(!$idBaseSrc || !$idRegSrc || !$idBaseDst || !$idRegDst){
    		$this->view->result = "Les paramètres de base de données sont manquants : $idRegSrc _ $idBaseSrc - $idRegDst _ $idBaseDst";
    	}else{
    		$oDiag = new GEVU_Diagnostique();
    		$db = $oDiag->getDb($idBaseSrc, $idRegSrc);
    		//met à jour les adresses des documents
    		$dbDoc = new Models_DbTable_Gevu_docs();
    		$dbDoc->changeAdressesByTitre(WEB_ROOT."/data/ftpMob/img/", ROOT_PATH."/data/ftpMob/img/");
    		$this->view->result = "mise à jour des adresses des images éffectuées.";
    		
    		//synchronise les bases
    		$oDiag->setUtiLieuLock($idExi, $idBaseSrc, $idRegSrc, $idBaseDst, $idRegDst);
    		$this->view->result .= "synchronisation des bases effectuées.";
    		
    		//supprime la base temporaire
    		$db->query("DROP DATABASE ".$idBaseSrc);
    		$this->view->result .= "base temporaire supprimée.";
    		
    	}
    	
    	
    }

    
}



