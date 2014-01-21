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
	   	$xml = $this->cache->load($c);
        if(!$xml){
			//initialise les gestionnaires de base de données
			$this->getDb($idBase);
        	$dbL = new Models_DbTable_Gevu_lieux($this->db);
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
    				$xml .="<node idLieu=\"-10\" lib=\"chargement...\" fake=\"1\" icon=\"iconCharge\" lockDiag=\"\" />";
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


	/**
	 * récupère l'arboressence des lieux d'Alceane au format xml
	 * suivant un type de lieu
    * @param int $idLieu
    * @param int $type
    * @param string $idBase
    * 
    * @return DomDocument
    */
	public function getArboTypeLieu($idLieu, $type, $idBase=false){
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idBase;
		/*
		$dom = new DomDocument();
		$dom->load('../tmp/arboAlceane.xml'); 
        return $dom;
		*/
	   	$xml = $this->cache->load($c);
        if(!$xml){
			//initialise les gestionnaires de base de données
        	$oDiag = new GEVU_Diagnostique($idBase);
        	$dbL = new Models_DbTable_Gevu_lieux($oDiag->db);
	    	$r = $dbL->findById_lieu($idLieu);
			$xml ="";
			foreach ($r as $v){
	        	$xml .= $oDiag->getXmlLieu($v);
	        	//récupère les logement du bâtiment
	        	$arrL = $dbL->getChildForTypeControle($idLieu, $type);
	        	foreach ($arrL as $l) {
	        		//récupère le lieu associé
	        		$rLieuL = $dbL->findById_lieu($l['id_lieu']);
	        		$xml .= $oDiag->getXmlLieu($rLieuL[0]);
    				//on vérifie si on a atteint le niveau final
    				//62 = logement
	        		if($type!=62)$xml .="<node idLieu=\"-10\" lib=\"chargement...\" fake=\"1\" icon=\"iconCharge\" lockDiag=\"\" />";	        		
	        		$xml .= "</node>";
	        	}
	        	$xml .= "</node>";
			}
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

