<?php

class GEVU_Site{
    
    var $cache;
	var $idBase;
    var $idExi;
    var $idInst;
    var $dbAnt;
    var $dbBat;
    var $dbC;
    var $dbD;
    var $dbDoc;
    var $dbEspInt;
    var $dbEspExt;
    var $dbG;
    var $dbGrp;
    var $dbI;
    var $dbInt;
    var $dbL;
    var $dbLieu;
    var $dbLDoc;
    var $dbLoc;
    var $dbLog;
    var $dbNiv;
    var $dbO;
    var $dbObjExt;
	var $dbP;
	var $dbPtc;
	var $dbPcl;
    var $dbScena;
    var $dbScene;
    var $dbSta;
    var $dbTypCtl;
    var $db;
    
	/**
	* constructeur de la class
	*
    * @param string $idBase
    * 
    */
	function __construct($idBase=false, $cache = false){    	
		
		try {			
		    $this->getDb($idBase);
	        
	        $this->idInst = -1;
	        
			$frontendOptions = array(
	            'lifetime' => 86400, // temps de vie du cache en seconde
	            'automatic_serialization' => true,
	        	'caching' => $cache //active ou desactive le cache
	        );  
	        $backendOptions = array(
	            // Répertoire où stocker les fichiers de cache
	            'cache_dir' => '../tmp/'
	        ); 
	        // créer un objet Zend_Cache_Core
	        $this->cache = Zend_Cache::factory('Core',
	                                     'File',
	                                     $frontendOptions,
	                                     $backendOptions); 
		}catch (Zend_Exception $e) {
	          echo "Récupère exception: " . get_class($e) . "\n";
	          echo "Message: " . $e->getMessage() . "\n";
		}
	}

    /**
    * @param string $c
    */
    function removeCache($c){
        $res = $this->manager->remove($c);
    }
    
    /**
     * retourne une connexion à une base de donnée suivant son nom
    * @param string $idBase
    * @return Zend_Db_Adapter_Abstract
    */
    public function getDb($idBase){
    	
 		$db = Zend_Db_Table::getDefaultAdapter();
    	if($idBase){
    		//change la connexion à la base
			$arr = $db->getConfig();
			$arr['dbname']=$idBase;
			$db = Zend_Db::factory('PDO_MYSQL', $arr);	
    	}
    	$this->db = $db;
    	return $db;
    }

}