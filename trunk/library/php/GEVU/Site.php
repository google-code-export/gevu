<?php

class GEVU_Site{
    
    var $cache;
	var $idBase;
    var $idExi;
	var $dbD;
	var $dbL;
	var $dbP;
	var $dbO;
    var $dbScena;
    var $dbScene;
    var $dbC;
    var $dbI;
    var $dbG;
    var $dbLDoc;
    var $dbDoc;
    var $dbTypCtl;
    var $db;
    
	/**
	* constructeur de la class
	*
    * @param string $idBase
    * 
    */
	function __construct($idBase=false){    	
    	
        $this->getDb($idBase);
        
		$frontendOptions = array(
            'lifetime' => 86400, // temps de vie du cache en seconde
            'automatic_serialization' => true,
        	'caching' => false //active ou desactive le cache
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