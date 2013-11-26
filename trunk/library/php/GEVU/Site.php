<?php

class GEVU_Site{
    
    var $cache;
	var $idBase;
    var $idExi;
    var $idInst;
    var $diag;
    var $dbAnt;
    var $dbBat;
    var $dbC;
	var $dbCD;
	var $dbD;
    var $dbDoc;
	var $dbExi;
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
    //pour l'optimisation
    var $bTrace = false;
    var $echoTrace = false;
    var $temps_debut;
    var $temps_inter;
    var $temps_nb=0;
    
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
	* fonction pour tracer l'éxécution du code
	*
    * @param string $message
    * 
    */
	public function trace($message){
		if($this->bTrace){
			$temps_fin = microtime(true);
			$tG = str_replace(".",",",round($temps_fin - $this->temps_debut, 4));
			$tI = str_replace(".",",",round($temps_fin - $this->temps_inter, 4));
			$mess = $this->temps_nb." | ".$message." |".$tG."|".$tI."<br/>";
			if($this->echoTrace)
				$this->echoTrace .= $mess;
			else
				echo $mess;
			$this->temps_inter = $temps_fin;
			$this->temps_nb ++;
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
    * @param string $idReg
    * 
    * @return Zend_Db_Adapter_Abstract
    */
    public function getDb($idBase, $idReg=""){

		$this->idBase = $idBase;			
    	$db = Zend_Db_Table::getDefaultAdapter();    	
    	if($idReg){
	    	$db = Zend_Registry::get($idReg);	
    	}    	
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
    
   /**
     * transforme des pramètres en chaine de caractère
     *
     * @param array $params
     * @param boolean $md5
     *   
     * @return string
     */
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

   /**
     * transforme un XML en objet
     *
     * @param XML $dom
     *   
     * @return objet
     */
	function xmlToObject($dom) {
		
		// validation à partir de la DTD référencée dans le document.
		/* En cas d'erreur, on ne va pas plus loin
		if (!@$dom->validate()) {
			return false;
		}
		*/
		
		// création de l'objet résultat
		$object = new stdClass();
	
		// on référence l'adresse du fichier source
		//$object->source = $fileName;
	
		// on récupère l'élément racine, on le met dans un membre
		// de l'objet nommé "root"
		$root = $dom->documentElement;
		$object->root = new stdClass();
	
		// appel d'une fonction récursive qui traduit l'élément XML
		// et passe la main à ses enfants, en parcourant tout l'arbre XML.
		$this->getDomElementToObject($root, $object->root);
	
		return $object;
	}
	
   /**
     * transforme un élément XML en objet
     *
     * @param XML $dom_element
     * @param XML $object_element
     *   
     * @return objet
     */
	function getDomElementToObject($dom_element, $object_element) {
	
		// récupération du nom de l'élément
		$object_element->name = $dom_element->nodeName;
	
		// récupération de la valeur CDATA,
		// en supprimant les espaces de formatage.
		$object_element->textValue = trim($dom_element->firstChild->nodeValue);
	
		// Récupération des attributs
		if ($dom_element->hasAttributes()) {
			$object_element->attributes = array();
			foreach($dom_element->attributes as $attName=>$dom_attribute) {
				$object_element->attributes[$attName] = $dom_attribute->value;
			}
		}
	
		// Récupération des éléments fils, et parcours de l'arbre XML
		// on veut length >1 parce que le premier fils est toujours
		// le noeud texte
		if ($dom_element->childNodes->length > 1) {
			$object_element->children = array();
			foreach($dom_element->childNodes as $dom_child) {
				if ($dom_child->nodeType == XML_ELEMENT_NODE) {
					$child_object = new stdClass();
					$this->getDomElementToObject($dom_child, $child_object);
					array_push($object_element->children, $child_object);
				}
			}
		}
	}		
}