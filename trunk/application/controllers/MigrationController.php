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
    	$dir = $this->_getParam('dir', false);			
    	if(!$dir || !$idBaseSrc || !$idRegSrc || !$idBaseDst || !$idRegDst){
    		$this->view->result = "Les paramètres de base de données sont manquants : $dir _ $idRegSrc _ $idBaseSrc - $idRegDst _ $idBaseDst";
    	}else{
    		if($dir=="ServeurToLocal"){
    			$mig = new GEVU_Migration();
    			$this->view->result = $mig->migreRefServeurToLocal($idBaseSrc, $idRegSrc, $idRegDst, $idBaseDst);    			
    		}elseif ($dir=="LocalToServer"){
    			$mig = new GEVU_Migration();
    			$this->view->result = $mig->migreTabletteToServeur($idExi, $idBaseSrc, $idRegSrc, $idRegDst, $idBaseDst);    			
    		}elseif ($dir=="getUtiLieuLock"){
	    		$oDiag = new GEVU_Diagnostique();
				$oDiag->bTrace = true; // pour afficher les traces
		    	$oDiag->temps_debut = microtime(true);
		    	$oDiag->echoTrace = $dir;
	    		//synchronise les bases
	    		$oDiag->getUtiLieuLock($idExi, $idBaseSrc, $idRegSrc, $idBaseDst, $idRegDst);
	    		$oDiag->trace("synchronisation des bases effectuées.");
	    		$this->view->result = $oDiag->echoTrace;
    		}elseif($dir=="setUtiLieuLock"){
	    		$oDiag = new GEVU_Diagnostique();
	    		$oDiag->bTrace = true; // pour afficher les traces
		    	$oDiag->temps_debut = microtime(true);
		    	$oDiag->echoTrace = $dir;
    			$oDiag->trace("Les paramètres de base de données sont : ".$idRegSrc." _ ".$idBaseSrc." _ ".$idRegDst." _ ".$idBaseDst);
		    	
	    		//met à jour les adresses des documents
	    		$db = $oDiag->getDb($idBaseSrc, $idRegSrc);
    			$dbDoc = new Models_DbTable_Gevu_docs($db);
	    		$dbDoc->changeAdressesByTitre(WEB_ROOT."/data/ftpMob/img/", ROOT_PATH."/data/ftpMob/img/");
	    		$oDiag->trace("mise à jour des adresses des images éffectuées.");
	    		
	    		//synchronise les bases
	    		$oDiag->setUtiLieuLock($idExi, $idBaseSrc, $idRegSrc, $idBaseDst, $idRegDst, true);
	    		$oDiag->trace("synchronisation des bases effectuées.");
	    		
	    		//supprime la base temporaire
	    		//$db->query("DROP DATABASE ".$idBaseSrc);
	    		//$oDiag->trace("base temporaire supprimée.");
	    		$this->view->result = $oDiag->echoTrace;
    		}
    		
    	}
    	
    	
    }

    
}



