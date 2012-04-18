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
	    	array("Antenne : type et nb. de logement", WEB_ROOT_AJAX."/stat/antenne")
	    	,array("Antenne : nb de stationnement", WEB_ROOT_AJAX."/stat/antenne?type=garage")
	    	,array("Antenne : vacance des logements", WEB_ROOT_AJAX."/stat/antenne?type=vac_log")
	    	,array("Antenne : vacance des commerces", WEB_ROOT_AJAX."/stat/antenne?type=vac_com")
	    	,array("Antenne : vacance des garages", WEB_ROOT_AJAX."/stat/antenne?type=vac_gar")
	    	
	    	);
	}

	/**
	 * statistiques pour le logement
	 */
	public function antenneAction() {
		$db = new Models_DbTable_Gevu_antennes();
		if($this->_getParam('type', 0)){
			$this->view->stats = $db->getStatType($this->_getParam('type', 0));			
		}else{
			$this->view->stats = $db->getStatType();			
		}
		
	}
	
	/**
	 	AC      ACCESSION       
		CA      CAVES   
		DI      DIVERS  
		D2      DEUX PIECES DUPLEX      
		D3      TROIS PIECES DUPLEX     
		D4      QUATRE PIECES DUPLEX    
		D5      CINQ PIECES DUPLEX      
		D6      SIX PIECES DUPLEX       
		D7      SEPT PIECES DUPLEX      
		FO      FOYERS  
		GA      GARAGE  
		GP      PARKING COUVERT 
		LO      LOCAL   
		LV      LOCAL A VELO    
		P1      PAVILLON 1 PIECE        
		P2      PAVILLON 2 PIECES       
		P3      PAVILLON 3  PIECES      
		P4      PAVILLON 4 PIECES       
		P5      PAVILLON 5 PIECES       
		P6      PAVILLON 6 PIECES       
		P7      PAVILLON 7 PIECES       
		P8      PAVILLON 8 PIECES       
		R4      QUATRE PIECE TRIPLEX    
		R5      CINQ PIECES  TRIPLEX    
		TO      PARKING EXTERIEUR       
		T1      STUDIO  
		T2      DEUX PIECES     
		T3      TROIS PIECES    
		T4      QUATRE PIECES   
		T5      CINQ PIECES     
		T6      SIX PIECES      
		T7      SEPT PIECES     
		T8      HUIT PIECES ET PLUS     
		1B      STUDIO CUISINE  

CMLIB   Catégorie de Module
UCUN    Antenne de rattachement
PKPR    Code groupe
BLBA    Bâtiment
ADRESSE Adresse
LKLO    Code Logement
TYPE    Type Logement
ETAGE   Etage
SURRE   Surface Réelle
SURAP   Surface Appliquée
LOYER   Loyer
SLS     SLS
CHARGES Charges
OCCUPATION      Logement Occupe /Vacant
MOTIF_VACANCE   Motif Vacance
CDECL   Date d'entrée
CDSCL   Date de Sortie
AGE1    Age Signataire 1
AGE2    Age Signataire 2
NBENF1  Nb enfants moins de 0-10 ans
NBENF2  Nb enfants de 11-18 ans
NBENF3  Nb enfants plus de 18 ans
AUTPERS Nb autre occupants
REIMP1  Revenu imposable  signataire 1
REIMP2  Revenu imposable autre personne
REIMPAUT        Revenu imposable  signataire 3
CATMEN  Catégorie de  Ménage
PLAF    Plafond de ressources
RESS    Ressources
POURC   % du plafond de ressources
NATIO1  Nationalité Signataire 1
NATIO2  Nationalité Signataire 2
SOCPRO1 Catégorie socio Prof Signataire 1
SOCPRO2 Catégorie socio Prof Signataire 2
EMPLOYEUR1      Employeur Signataire 1
EMPLOYEUR2      Employeur Signataire 2
SITFAM1 Situation de famille Signataire 1
SITFAM2 Situation de famille Signataire 2
RESERVAT        Réservataire
ISOLE   Personne isolée
STAB    Situation Stable
FRAGI   Situation Fragile
ETUDI   Etudiant
RMI     Rmi
API     API
AAH     AAH
FNS     FNS
ASCENSEUR       Ascenseur
MODCHAUF        Mode de Chauffage
RESAN   Année de ressource
	 * 
	 * 
	 */
	
	
}
