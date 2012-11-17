<?php

class GEVU_Site{
    
    var $cache;
	var $idBase;
    var $idExi;
    var $idInst;
    var $dbAnt;
    var $dbBat;
    var $dbC;
	var $dbCD;
	var $dbD;
    var $dbDoc;
    var $dbEspInt;
    var $dbEspExt;
    var $dbG;
    var $dbGrp;
    var $dbI;
    var $dbInt;
    var $dbL;
	var $dbLCD;
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
    * @param boolean $cache
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
    	
		$this->idBase = $idBase;			
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

   /**
     * Récupère le contenu body d'une url
     *
     * @param string $url
     * @param array $param
     * @param boolean $cache
     *   
     * @return string
     */
	function getUrlBodyContent($url, $param=false, $cache=true) {
		$html = false;
		if(substr($url, 0, 7)!="http://")$url = urldecode($url);
		if($cache){
			$c = str_replace("::", "_", __METHOD__)."_".md5($url); 
			if($param)$c .= "_".$this->getParamString($param);
		   	$html = $this->cache->load($c);
		}
        if(!$html){
	    	$client = new Zend_Http_Client($url);
	    	if($param)$client->setParameterGet($param);
	    	try {
				$response = $client->request();
				$html = $response->getBody();
			}catch (Zend_Exception $e) {
				echo "Récupère exception: " . get_class($e) . "\n";
			    echo "Message: " . $e->getMessage() . "\n";
			}				
        	if($cache)$this->cache->save($html, $c);
        }
		return $html;
	}
    
	function getParamString($params, $md5=false){
		$s="";
		foreach ($params as $k=>$v){
			$v = str_replace(".","_",$v);
			$v = str_replace(",","_",$v);
			if($md5) $s .= "_".md5($v);
			else $s .= "_".$v;
		}
		return $s;	
	}    
}