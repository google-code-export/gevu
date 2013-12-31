<?php

class GEVU_Alceane extends GEVU_Site{
    	
	var $oDiag;
		
	/**
	* constructeur de la class
	*
    * @param string $idBase
    * @param boolean $cache
    * 
    */
	public function __construct($idBase=false, $cache = true)
    {
    	parent::__construct($idBase, $cache);
		
    }

	/**
	* Trouve des lieux 
    * @param string $query
    * @param string $idBase
    * 
    * @return DomDocument
    */
	public function findLieu($query, $idBase=false){
		$c = str_replace("::", "_", __METHOD__)."_".$query."_".$idBase;
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
        $dbL = new Models_DbTable_Gevu_lieux($this->db);
        $dbG = new Models_DbTable_Gevu_geos($this->db);
        //initialise l'objet diagnostique
        $oDiag = new GEVU_Diagnostique($idBase);
        
    	$xml='<node idLieu="1" lib="Lieux trouvés" fake="0">';

        //recherche dans les noms de lieu
    	$arrLieux = $dbL->trouveByLib($query);
        $first = true;
    	foreach ($arrLieux as $r) {
    		if($first) $xml .= '<node idLieu="-1" lib="Dans les noms" fake="0">';
    		$first=false;
			$xml .= $oDiag->getXmlLieu($r,true);
        }
        if(!$first)$xml .= "</node>";
    	
    	//recherche dans les adresses
    	$arrGeo = $dbG->trouveByAdresse($query);
        $first = true;
    	foreach ($arrGeo as $r) {
    		if($first) $xml .= '<node idLieu="-1" lib="Dans les adresses" fake="0">';
    		$first=false;
			$xml .= $oDiag->getXmlLieu($r,true);
        }
        if(!$first)$xml .= "</node>";
        
        $xml .= "</node>";

        //return $xml;
        //
        if($xml!=""){
	        $dom = new DomDocument();
	        $dom->loadXML($xml);
        }else{
	        $dom = false;
        }
        return $dom;
        //
	}
	
	/**
	 * récupère l'arboressence initiale d'Alceane au format xml
    * @param int $idLieu
    * @param string $idBase
    * 
    * @return DomDocument
    */
	public function getArboAntenne($idLieu=0, $idBase=false){
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idBase;
		/*
		$dom = new DomDocument();
		$dom->load('../tmp/arboAlceane.xml'); 
        return $dom;
		*/
	   	$xml = false;//$this->cache->load($c);
        if(!$xml){
			//initialise les gestionnaires de base de données
			$this->getDb($idBase);
        	$dbL = new Models_DbTable_Gevu_lieux($this->db);
        	$dbA = new Models_DbTable_Gevu_antennes($this->db);
        	$dbG = new Models_DbTable_Gevu_groupes($this->db);
        	$dbB = new Models_DbTable_Gevu_batiments($this->db);
        	$dbLog = new Models_DbTable_Gevu_logements($this->db);
        	
        	//initialise l'objet diagnostique
        	$oDiag = new GEVU_Diagnostique($idBase);
        	
    		$xml='<node idLieu="1" lib="ALCEANE" fake="0">';
        	//récupère les antennes
        	$arrA = $dbL->getChildForTypeControle($idLieu, 60);
        	foreach ($arrA as $a) {
        		//récupère le lieu associé
        		$rLieuA = $dbL->findById_lieu($a['id_lieu']);
        		$xml .= $oDiag->getXmlLieu($rLieuA[0]);
        		//récupère les groupes de l'antenne
        		$arrG = $dbL->getChildForTypeControle($a['id_lieu'], 61);
        		foreach ($arrG as $g) {
	        		//récupère le lieu associé
	        		$rLieuG = $dbL->findById_lieu($g['id_lieu']);
	        		$xml .= $oDiag->getXmlLieu($rLieuG[0]);
	        		//récupère les bâtiments du groupe
	        		$arrB = $dbL->getChildForTypeControle($g['id_lieu'], 45);
	        		foreach ($arrB as $b) {
		        		//récupère le lieu associé
		        		$rLieuB = $dbL->findById_lieu($b['id_lieu']);
		        		$xml .= $oDiag->getXmlLieu($rLieuB[0]);
		        		//récupère les logement du bâtiment
		        		$arrL = $dbL->getChildForTypeControle($b['id_lieu'], 62);
		        		foreach ($arrL as $l) {
			        		//récupère le lieu associé
			        		$rLieuL = $dbL->findById_lieu($l['id_lieu']);
			        		$xml .= $oDiag->getXmlLieu($rLieuL[0]);
	        				$xml .= "</node>";
		        		}
	        			$xml .= "</node>";
	        		}
	        		$xml .= "</node>";
        		}
	        	$xml .= "</node>";
        	}
        	$xml .= "</node>";        	
	    	$this->cache->save($xml, $c);
        }

        //echo $xml;
        if($xml!=""){
	        $dom = new DomDocument();
	        $dom->loadXML($xml);
        }else{
	        $dom = false;
        }
        return $dom;
        
    }
        
    
}

