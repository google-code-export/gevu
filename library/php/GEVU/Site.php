<?php

class GEVU_Site{
    
    var $cache;
    
    function __construct(){    	
    	
        $frontendOptions = array(
            'lifetime' => 86400, // temps de vie du cache en seconde
            'automatic_serialization' => true
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
    * @return Zend_Db_Table
    */
    public function getDb($idBase){
    	
 		$db = Zend_Db_Table::getDefaultAdapter();
    	if($idBase){
    		//change la connexion à la base
			$arr = $db->getConfig();
			$arr['dbname']=$idBase;
			$db = Zend_Db::factory('PDO_MYSQL', $arr);	
    	}
    	return $db;
    }

}