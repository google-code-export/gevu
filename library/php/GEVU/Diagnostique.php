<?php

class GEVU_Diagnostique extends GEVU_Site{
    	
	/**
	* constructeur de la class
	*
    * @param string $idBase
    * 
    */
	public function __construct($idBase=false)
    {
    	parent::__construct($idBase);
		
    }
	
	/**
	* ajoute un contrôle pour le lieu 
    * @param int $idLieu
    * @param string $Obj
    * @param int $idExi
    * @param string $idBase
    * 
    */
	public function ajoutCtlLieu($idLieu, $Obj, $idExi, $idBase=false){
			
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
		if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->db);
		
		//création d'un nouvel instant
		$c = str_replace("::", "_", __METHOD__); 
		$idInst = $this->dbI->ajouter(array("id_exi"=>$idExi,"nom"=>$c));
		
		//création de l'objet passé en paramètre
		$db = new $Obj($this->db);
		//ajout du nouveau contrôle
		$db->ajouter(array("id_instant"=>$idInst, "id_lieu"=>$idLieu), false);
			
	}
	
    /**
	* récupère les controles autorisés pour le lieu 
    * @param string $idLieu
    * @param string $idScenar
    * @param string $idBase
    * @param string $forTypeControle
    * 
    * @return Array
    */
	public function getLieuCtl($idLieu, $idScenar, $idBase=false, $forTypeControle=false){
			
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
        if(!$this->dbScena)$this->dbScena = new Models_DbTable_Gevu_scenario($this->db);
        if(!$this->dbScene)$this->dbScene = new Models_DbTable_Gevu_scenes($this->db);
        if(!$this->dbTypCtl)$this->dbTypCtl = new Models_DbTable_Gevu_typesxcontroles($this->db);
        if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
        
        //récupère les paramètres du scénario
      	$arrScenar = $this->dbScena->findById_scenario($idScenar);
        $params = json_decode($arrScenar[0]["params"]);

        //récupération des données du lieu courant
		$r = $this->dbL->find($idLieu);
		$rLieu = $r->current();
		
        //recherche les contrôles possibles
        $arrCtl = "";
        //on boucle sur les scènes
        foreach ($params as $p){
			//on boucle sur les étapes de la scène
        	foreach ($p->etapes as $e){
        		//récupère les informations du contrôle
		        $rTypeCtrl = $this->dbTypCtl->findById_type_controle($e->id_type_controle);
        		//sort s'il n'y a pas d'objet lié
        		if($rTypeCtrl["zend_obj"]=="")continue;
	        	//vérifie si le lieu à le controle
	        	$items = $this->verifIsControle($rTypeCtrl, $rLieu);
				if($items && $items->count()){
					//si le lieu à déjà le contrôle on ne peut pas en ajouter
					return "";
				}
				//récupère le parent qui possède ce type de controle
				$arrLieuxParents = $this->dbL->getParentForTypeControle($idLieu, $rTypeCtrl["zend_obj"]);				

				if($arrLieuxParents){
					//on récupère le détail de la scène
	        		$arrScene = $this->dbScene->findByIdScene($e->id_scene);
	        		$jsScene = json_decode($arrScene[0]["paramsCtrl"]);	        		
		        	$xmlScene = simplexml_load_string($jsScene[0]->idCritSE);
					//calcul le nombre de niveau entre le lieu et son parent
					$niv = $rLieu["niv"]-$arrLieuxParents[0]["niv"];
					//création de la requête Xpath
					$path = "/node";
					for ($i = 0; $i < $niv; $i++) {						
						$path .= "/node";
						//vérifie si un des parents à déjà le contrôle
	        			$result = $xmlScene->xpath($path);
						foreach ($result as $node) {
		        			$rTC = $this->dbTypCtl->findById_type_controle($node["idCtrl"]);
        					if($rTC["zend_obj"]!=""){
			        			$arrLP = $this->dbL->getParentForTypeControle($idLieu, $rTC["zend_obj"]);				        						
								if($arrLP){
									//ajoute une condition dans la requête XML
									$path .= "[@idCtrl=".$node["idCtrl"]."]";
								}
        					};
						}						
					}
					/*on ajoute un niveau 
					 *dans le cas d'une liste de type de contrôle possible
					 *cf. Models_DbTable_Gevu_espacesxinterieurs.getTypeControle
					*/
					if($forTypeControle){
						$path .= $forTypeControle;
					} 
	        		$result = $xmlScene->xpath($path);
					foreach ($result as $node) {
			        	//vérifie si le lieu à le controle
		        		$rTypeCtrl1 = $this->dbTypCtl->findById_type_controle($node["idCtrl"]);						
			        	$items1 = $this->verifIsControle($rTypeCtrl1, $rLieu);
						/*si le lieu à déjà le contrôle on ne peut pas en ajouter
						 */
			        	if($items1 && $items1->count()){
			        		return "";
			        	}else{
							$arrCtl[] = $rTypeCtrl1;										
						}		        	
					}					
				}else{
					//si on ne trouve aucun controle on peut ajouter le contrôle
			        $arrType = $this->dbTypCtl->findById_type_controle($e->id_type_controle);
					$arrCtl[] = $arrType;										
				}
        	}
		}
		return $arrCtl;
	}
	
	/**
	* vérifie si un lieu possède le controle dans une hiérarchie et renvoie les informations
	*  
    * @param xml $xmlCtrl
    * @param Zend_Db_Table_Rowset $rLieu
    * @param boolean $bEnf
    * 
    * @return array
    */
	function verifTreeIsControle($xmlCtrl, $rLieu, $bEnf=true){
		$arr = "";
		foreach ($xmlCtrl as $ctrl) {
			//vérifie si le lieu à le controle
	        $is = $this->verifIsControle($ctrl["idCtrl"], $rLieu);
	 		//dans le cas d'un enfant
	 		if($bEnf){
	 			//on ajoute le ctrl s'il n'existe pas
	        	if($is->count()==0)return $ctrl;
	 			//on renvoie vide s'il existe
	        	if($is->count()==1)return "OK";
	 		} 
	 		//dans le cas d'un parent
	 		if(!$bEnf){
		 		//on ajoute le ctrl enfant s'il existe
		        if($is->count()==1 && $ctrl->count()){
					return $ctrl->node[0];		        	
		        }
	 		}
			//on continue d'explorer la hierarchie des controles
			if($ctrl->count()){
				$arr = $this->verifTreeIsControle($ctrl, $rLieu);
				//on sort si on a trouvé un contrôle à ajouter
				if($arr!="")return $arr;	
			}        	
        }
        return $arr;
	}
	
	/**
	* vérifie si un lieu possède le controle et renvoie les informations
	*  
    * @param array $arrType
    * @param Zend_Db_Table_Rowset $rLieu
    * 
    * @return array
    */
	function verifIsControle($arrType, $rLieu){
		$items = "";
	    //si le contrôle est associé à une table
        if($arrType["zend_obj"]){
	    	//récupère l'objet en rapport avec le lieu
        	$items = $rLieu->findDependentRowset($arrType["zend_obj"]);
        }
        return $items;
	}
		    
    
	/**
	* récupère les documents en rapport avec un lieu
	* et renvoie les résultats 
    * @param string $idLieu
    * @param string $idBase
    * 
    * @return Array
    */
	public function getLieuDocs($idLieu, $idBase=false){
			
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
        if(!$this->dbLDoc)$this->dbLDoc = new Models_DbTable_Gevu_docsxlieux($this->db);
        return $this->dbLDoc->findByIdLieu($idLieu);
        
	}

	/**
	* supprime le documents
	*  
    * @param string $idDoc
    * @param string $idBase
    * 
    * @return void
    */
	public function deleteDoc($idDoc, $idBase=false){
			
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
        if(!$this->dbDoc)$this->dbDoc = new Models_DbTable_Gevu_docs($this->db);
        $this->dbDoc->remove($idDoc, $this->db);
        
	}	

	/**
	* calcul le diagnostic d'un lieu
	* et renvoie les résultats 
    * @param string $idLieu
    * @param int $idExi
    * @param string $idBase
    * @return Array
    */
	public function calculDiagForLieu($idLieu, $idInstant=-1, $idBase=false){
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idInstant."_".$idBase; 
	   	$r = $this->cache->load($c);
        if(!$r){
			
			//initialise les gestionnaires de base de données
			$this->getDb($idBase);
        	if(!$this->dbD)$this->dbD = new Models_DbTable_Gevu_diagnostics($this->db);
	        
	        if($idInstant==-1){
	        	//marque les derniers diagnostics
	        	$this->dbD->setLastDiagForLieu($idLieu);
	        }

	        //récupère les diags oui
	        $r['DiagOui'] = $this->dbD->getDiagReponse($idLieu, $idInstant, 1);
	        //récupère les diags non
	        $r['DiagNon'] = $this->dbD->getDiagReponse($idLieu, $idInstant, 2);
	        //récupère tous les diags
	        $r['DiagTot'] = $this->dbD->getDiagReponse($idLieu, $idInstant);

	        //calcule les handicateurs
			$r['handicateur']['auditif'] = $this->getHandicateur("auditif",$r);
			$r['handicateur']['cognitif'] = $this->getHandicateur("cognitif",$r);
			$r['handicateur']['moteur'] = $this->getHandicateur("moteur",$r);
			$r['handicateur']['visuel'] = $this->getHandicateur("visuel",$r);	        
	        
			//calcul les stats
			$r['stat'] = $this->getArrStat($idLieu, $r);
			
	        $this->cache->save($r, $c);
        }        
	        
        return $r;
	}
	
	/**
	* calcul le XML pour l'affichage des stats
	* 
    * @param string $idLieu
    * @param array $r
    * @return string
    */
	function getXmlStat($idLieu, $r){
		$xml = "<EtatDiag idSite='".$this->idBase."' idLieu='".$idLieu."' TauxCalc='0 sur 0' >";
		foreach ($r['handicateur'] as $k=>$v) {
			$xml .= "<Obstacles id='".$k."' ><niv0>".$v['applicable']."</niv0><niv1>".$r['DiagNon'][0][$k."_1"]."</niv1><niv2>".$r['DiagNon'][0][$k."_2"]."</niv2><niv3>".$r['DiagNon'][0][$k."_3"]."</niv3><handi>".$v[0]."</handi></Obstacles>";
		}
		$xml .= "</EtatDiag>";

		return $xml;
	}
	
	/**
	* calcul un tableau pour l'affichage des stats
	* 
    * @param string $idLieu
    * @param array $r
    * @return array
    */
	function getArrStat($idLieu, $r){
		$arr = array();
		foreach ($r['handicateur'] as $k=>$v) {
			$arr['EtatDiag'][] = array('id'=>$k,'niv0'=>$v['applicable'],'niv1'=>$r['DiagNon'][0][$k."_1"],'niv2'=>$r['DiagNon'][0][$k."_2"],'niv3'=>$r['DiagNon'][0][$k."_3"],'handi'=>$v[0]);
		}
		return $arr;
	}
	
	/**
	* calcul l'handicateur pour une déficience
	* 
    * @param string $typeDef
    * @param array $arrDiag
    * @return array
    */
	function getHandicateur($typeDef, $arrDiag){
		
		$HandiObst = $arrDiag['DiagNon'][0][$typeDef."_1"]
			+($arrDiag['DiagNon'][0][$typeDef."_2"]*2)
			+($arrDiag['DiagNon'][0][$typeDef."_3"]*3);
			  
		$HandiAppli = $arrDiag['DiagOui'][0][$typeDef."_1"]
			+($arrDiag['DiagOui'][0][$typeDef."_2"])
			+($arrDiag['DiagOui'][0][$typeDef."_3"]);
		
		$Handi3 = $arrDiag['DiagNon'][0][$typeDef."_3"];
					
		if($HandiAppli==0)
			return array("A","obstacle"=>$HandiObst,"applicable"=>$HandiAppli,"coef"=>0);	
		//calcul le coefficient d'handicateur
		$handi = $HandiObst/$HandiAppli;
		//calcule la lettre correspond au coefficient
		//suivant l'interval et suivant la contrainte de niveau trois
		if($handi>=0 && $handi<=0.2 && $Handi3==0)	
			return array("A","obstacle"=>$HandiObst,"applicable"=>$HandiAppli,"coef"=>$handi);	
		if($handi>0.2 && $handi<=0.4 && $Handi3==0)	
			return array("B","obstacle"=>$HandiObst,"applicable"=>$HandiAppli,"coef"=>$handi);	
		//attention on r�initialise l'interval pour afficher les cas pr�c�dent ayant un Handi 3
		// 0.4 devient 0	
		if($handi>=0 && $handi<=0.6)	
			return array("C","obstacle"=>$HandiObst,"applicable"=>$HandiAppli,"coef"=>$handi);	
		if($handi>0.6 && $handi<=0.8)	
			return array("D","obstacle"=>$HandiObst,"applicable"=>$HandiAppli,"coef"=>$handi);	
		if($handi>0.8)	
			return array("E","obstacle"=>$HandiObst,"applicable"=>$HandiAppli,"coef"=>$handi);	
	}	

	/**
	* renvoie le diagnostic d'un lieu
    * @param string $idLieu
    * @param int $idExi
    * @param string $idBase
    * @return Array
    */
	public function getDiagForLieu($idLieu, $idExi, $idBase=false){
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idExi."_".$idBase; 
	   	$r = $this->cache->load($c);
        if(!$r){
			$this->idExi = $idExi;
			
			//initialise les gestionnaires de base de données
			$this->getDb($idBase);
        	if(!$this->dbD)$this->dbD = new Models_DbTable_Gevu_diagnostics($this->db);
				        
	        //récupère les campagnes pour le lieu
	        $r = $this->dbD->getCampagnes($idLieu);
	        $nb = count($r);
	        for ($i = 0; $i < $nb; $i++) {        	
		        //récupère les diags 
		        $r[$i]['diag'] = $this->calculDiagForLieu($idLieu, $r[$i]["id_instant"]);
	        }
	        
	        $this->cache->save($r, $c);
        }        
	        
        return $r;
	}
	
	/**
	* cherche un lieu à partir d'un texte
	* renvoie les résultats 
    * @param string $txtLieu
    * @param string $idBase
    * @return Array
    */
	public function findLieu($idLieu=-1, $txtLieu="", $idBase=false){
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idBase; 
	   	$r = $this->cache->load($c);
        if(!$r){
			//initialise les gestionnaires de base de données
			$this->getDb($idBase);
        	if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);

        	$r = array();
	        if($idLieu){
		        //recherche par identifiant
		        $arr = $this->dbL->findById_lieu($idLieu);
		        $r['id'] = $this->setResultLieu($arr);
		        $r['lib'] = "";
	        }
	        if($txtLieu){
		        //recherche par lib
		        $arr = $this->dbL->findByLib($txtLieu);
		        $r['lib'] = $this->setResultLieu($arr);
				$r['id'] = ""; 
	        }
	    	$this->cache->save($r, $c);
        }        
        return $r;
	}
    
	/**
	* formate le résultat d'une recherche de lieu 
    * @param Array $r
    * 
    * @return Array
    */
	public function setResultLieu($r){
        $result = array();
		//pour chaque lieu trouvé
        $nb = count($r);
        for ($i = 0; $i < $nb; $i++) {
        	//ajoute le fil d'ariane du lieux pour afficher les résultats
        	$arrL = $this->dbL->getFullPath($r[$i]['id_lieu']);
        	//création du xml pour afficher dans l'arboressence
			$dom = false;
			$j = 1;
			$nbA = count($arrL);
        	foreach ($arrL as $L){
        		if(!$dom){
					$dom = new DomDocument();
					$dom->loadXML($this->getXmlLieu($L, true));        			
					$foo = $dom->documentElement;
        		}else{
					$d = new DomDocument();
					//vérifie s'il faut ajouter les noeuds enfants de l'item trouvé
					if($j == $nbA){
						$d = $this->getXmlNode($L['id_lieu'],$this->idBase);
					}else{
						$d->loadXML($this->getXmlLieu($L, true));
					}
					if($d){
						$mNewNode = $dom->importNode($d->documentElement, true);
						$foo->appendChild($mNewNode);
					}        			
					$foo = $foo->lastChild;
        		}
        		$j ++;
			}
			$s = $dom->saveXML();        						
        	$result[] = array("ariane"=>$arrL,"tree"=>$dom); 
        }
		return $result;
	}
	
	
	/**
	 * récupère la descendance d'un noeud au format xml
    * @param int $idLieu
    * @param string $idBase
    * @param integer $nivMax
    * @return DomDocument
    */
	public function getXmlNode($idLieu=0, $idBase=false, $nivMax=3){
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idBase; 
	   	$xml = $this->cache->load($c);
        if(!$xml){
    		$xml="";
			//initialise les gestionnaires de base de données
			$this->getDb($idBase);
        	if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
			
        	$r = $this->dbL->findById_lieu($idLieu);
        	$xml = $this->getXmlEnfant($idLieu, $nivMax);
	    	$this->cache->save($xml, $c);
        }
        if($xml!=""){
	        $dom = new DomDocument();
	        $dom->loadXML($xml);
        }else{
	        $dom = false;
        }
        return $dom;
        
    }
        
    /**
     * Récupération des enfants d'un lieu
     *
     * @param integer $idLieu
     * @param integer $nivMax
     * @param integer $niv
     * @return string
     */
    public function getXmlEnfant($idLieu, $nivMax=1, $niv=0){
    	    	
    	$r = $this->dbL->findByLieu_parent($idLieu);
		$xml ="";
		foreach ($r as $v){
        	$xml .= $this->getXmlLieu($v);
        	//vérifie s'il faut afficher les enfants
        	if($v['nbDiag']>0){
				$xml = "";
        		break;        		
        	} 
        	if($nivMax > $niv){
		    	//récupère le xml des enfants
	    		$xml .= $this->getXmlEnfant($v['id_lieu'], $nivMax, $niv+1);
        	}else{
    			$xml .="<node idLieu=\"-10\" lib=\"loading...\" fake=\"1\" icon=\"voieIcon\" />";
	    	}
		    $xml .= "</node>\n";				
        }
        
        //création de la racine
    	if($niv==0){
        	$r = $this->dbL->findById_lieu($idLieu);
        	$xml = $this->getXmlLieu($r[0])
        		.$xml
        		."</node>\n";
    	}
        
		return $xml;
    }
    
    /**
     * Récupération du format xml d'un lieu
     *
     * @param array $rLieu
     * @param boolean $end
     * @return string
     */
    public function getXmlLieu($rLieu, $end=false){
    	$xml = "<node idLieu=\"".$rLieu['id_lieu']."\" lib=\"".htmlspecialchars($rLieu['lib'])."\" niv=\"".$rLieu['niv']."\" fake=\"0\" >";
    	if($end)$xml .= "</node>";
    	return $xml;
    }

    /**
     * Récupération d'un diagnostic complet
     *
     * @param int $idLieu
     * @param string $idBase
     * @param string $idInst
     * 
     * @return array
     */
    public function getDiagComplet($idLieu=0, $idBase=false, $idInst=-1){
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idBase; 
	   	$rs = $this->cache->load($c);
        if(!$rs){           
			//initialise les gestionnaires de base de données
			$this->getDb($idBase);
        	if(!$this->dbD)$this->dbD = new Models_DbTable_Gevu_diagnostics($this->db);
        	if(!$this->dbP)$this->dbP = new Models_DbTable_Gevu_problemes($this->db);
        	if(!$this->dbO)$this->dbO = new Models_DbTable_Gevu_observations($this->db);
        	        	
        	$rs['questions'] = $this->dbD->getAllDesc($idLieu,-1, "", -1, $idInst);
        	$rs['problemes'] = $this->dbP->findById_lieu($idLieu);
        	$rs['observations'] = $this->dbO->findById_lieu($idLieu);

        	$this->cache->save($rs, $c);
        }
        return $rs;
    }

    /**
     * Récupération de la liste des diagnostics
     *
     * @param array $params
     * @param string $idBase
     * @return array
     */
    public function getDiagListe($params, $idBase=false){
		$c = str_replace("::", "_", __METHOD__)."_".$idBase."_".$params['idLieu']."_".$params['handi']."_".$params['niv']; 
	   	$rs = $this->cache->load($c);
        if(!$rs){           
			//initialise les gestionnaires de base de données
			$this->getDb($idBase);
        	if(!$this->dbD)$this->dbD = new Models_DbTable_Gevu_diagnostics($this->db);

	        //récupère les campagnes pour le lieu
	        $rs = $this->dbD->getDiagliste($params['idLieu'], 1, $params['handi'], $params['niv']);
	        $nb = count($rs);
	        $oLieu = -1;
	        for ($i = 0; $i < $nb; $i++) {
	        	//vérifie si on traite un nouveau lieu
	        	if($oLieu != $rs[$i]['dLieu']){
	        		$oLieu = $rs[$i]['dLieu'];
	        		//ajoute le fil d'ariane du lieu
	        		$r[$oLieu]['ariane'] = $this->findLieu($oLieu,"",$idBase);
	        	}        	
		        //ajoute la réponse au diagnostic
        		$r[$oLieu][] = $rs[$i];
	        }
        	

        	$this->cache->save($r, $c);
        }
        return $r;
    }
        
    /**
     * Récupération des données liées à iun lieu
     *
     * @param int $idLieu
     * @param int $idExi
     * @param string $idBase
     * @return array
     */
    public function getNodeRelatedData($idLieu, $idExi, $idBase=false){
    
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idExi."_".$idBase; 
	   	$res = $this->cache->load($c);
    	        
        if(!$res){
            $res = array();
            $this->idExi = $idExi;
            
			//initialise les gestionnaires de base de données
			$this->getDb($idBase);
        	if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
                    	
        	//création du fil d'ariane
            $res['ariane']=$this->dbL->getFullPath($idLieu);
	        
            //récupération des données courantes
			$Rowset = $this->dbL->find($idLieu);
			$lieu = $Rowset->current();
			
			//récupération des données lieés
            $dt = $this->dbL->getDependentTables();
            foreach($dt as $t){
				$items = $lieu->findDependentRowset($t);
				if($items->count()){
					//vérifie si on traite un cas particulier
	            	if($t=="Models_DbTable_Gevu_diagnostics" || $t=="Models_DbTable_Gevu_problemes"  || $t=="Models_DbTable_Gevu_observations" ){
	            		//récupère les informations seulement si ce n'est pas déjà fait
	            		// car une même requête renvoie les informations des 3 tables
	            		if(!isset($res["___diagnostics"])){
				        	$res["___diagnostics"] = $this->getDiagForLieu($idLieu, $idExi, $idBase);			
	            		}
	            	}elseif($t=="Models_DbTable_Gevu_docsxlieux"){
						$dbT = new $t($this->db);
	            		$res[$t]= $dbT->findByIdLieu($idLieu);
	            	}else{
						$res[$t]=$items->toArray();
	            	}
            	}
			}
			//vérifie si les enfants ont des diagnostics
			$rs = $this->dbL->findByLieu_parent($idLieu);
			foreach ($rs as $r){
				if($r['nbDiag']>0){
					//récupère les informations de l'enfant
					$resEnf = $this->getDiagForLieu($r['id_lieu'], $idExi, $idBase);
					if(count($res["___diagnostics"]["enfants"])== 0)$res["___diagnostics"]["enfants"]=$resEnf;
					else $res["___diagnostics"]["enfants"] = array_merge($res["___diagnostics"]["enfants"], $resEnf);			
				}
			}
			//ajoute le dernier diagnostic pour le lieu
	        $res["___diagnostics"]['diag'] = $this->calculDiagForLieu($idLieu,-1,$idBase);
			
            $this->cache->save($res, $c);
        }
        return $res;
    }

    /**
     * Récupération d'un scenario complet
     *
     * @param int $idScenario
     * @param int $idLieu
     * @param int $idTypeCtrl
     * 
     * @return array
     */
    public function getScenarioComplet($idScenario, $idLieu, $idTypeCtrl){
		$c = str_replace("::", "_", __METHOD__)."_".$idScenario; 
	   	$rs = $this->cache->load($c);
        if(!$rs){           

			//initialise les gestionnaires de base de données
        	if(!$this->dbScena)$this->dbScena = new Models_DbTable_Gevu_scenario($this->db);
        	if(!$this->dbScene)$this->dbScene = new Models_DbTable_Gevu_scenes($this->db);
        	if(!$this->dbC)$this->dbC = new Models_DbTable_Gevu_criteres($this->db);

        	//récupère la définition du scénario pour le lieu
    		$arrCtl = $this->getLieuCtl($idLieu, $idScenario, false, "/node[@idCtrl='".$idTypeCtrl."']/node");
        	
        	
        	//récupération des informations de scenario
        	$arrScena = $this->dbScena->findById_scenario($idScenario);
        	$psScena = Zend_Json::decode($arrScena[0]['params']);
        	foreach ($psScena as $pEtapes) {
        		foreach ($pEtapes['etapes'] as $pScene) {
	        		$ctl = $this->getControleInScenario($arrCtl, $pScene);
	        		if($ctl){
	        			if(!isset($rs["criteres"][$pScene['id_type_controle']])){
	        				$rsCrit = $this->dbC->findByIdTypeControle($pScene['id_type_controle']);
		        			$rs["criteres"]["idTypeControle".$pScene['id_type_controle']] = $rsCrit;
		        		}
		        		$rsScene = $this->dbScene->findByIdScene($pScene['id_scene']);
		        		$rsScene['id_type_controle']=$pScene['id_type_controle'];
		        		$rs["etapes"][$pEtapes['num']] = $rsScene;
	        		}
        		}
        	}
        	
        	$this->cache->save($rs, $c);
        }

        return $rs;
    }

    /**
     * Récupère le controle s'il est dans le scénario
     *
     * @param array $arrCtl
     * @param object $scene
     * 
     * @return array/boolean
     */
    public function getControleInScenario($arrCtl, $scene){
        foreach ($arrCtl as $ctl) {
        	if($scene['id_type_controle']==$ctl['id_type_controle']){
        		return $ctl;
        	}
        }
        return false;
    }
    
    /**
     * Enregistre les choix de diagnostic
     *
     * @param int $idExi
     * @param int $idLieu
     * @param string $comment
     * @param array $params
     * @param string $idBase
     * 
     * @return int
     */
    public function setChoix($idExi, $idLieu, $comment, $params, $idBase){
		
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
    	if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->db);
        if(!$this->dbD)$this->dbD = new Models_DbTable_Gevu_diagnostics($this->db);
        if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
    	    	
        //création de l'instant
        $idInst = $this->dbI->ajouter(array('id_exi'=>$idExi,'commentaires'=>$comment,'nom'=>'setChoix'),false,$idBase);
        
        //enregsitre les choix
        $oTypeControle = -1;
       	foreach ($params as $p) {
       		if($oTypeControle != $p['id_type_controle']){
	       		//récupère le lieu enfant correspondant au type de controle
	       		$idLieuControle = $this->dbL->getEnfantForTypeControle($idLieu, $p['id_type_controle'], $idInst);
	       		$oTypeControle = $p['id_type_controle'];       			
       		}
       		//ajoute le diagnostique
       		$this->dbD->ajouter(array('id_critere'=>$p['id_critere'],'id_reponse'=>$p['id_reponse'],'id_instant'=>$idInst,'id_lieu'=>$idLieuControle),false);
       	}
        return true;
    }

    /**
     * Enregistre la modification de lieu
     *
     * @param int $idLieu
     * @param array $data
     * @param string $idBase
     * 
     * @return integer
     */
    public function editLieu($idLieu, $data, $idBase){
		
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
        if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
    	    	
        return $this->dbL->edit($idLieu, $data);
    }

    /**
     * Ajoute un lieu 
     *
     * @param int $idLieuParent
     * @param int $idExi
     * @param string $idBase
     * 
     * @return integer
     */
    public function ajoutLieu($idLieuParent, $idExi, $idBase=false, $lib="Nouveau lieu", $existe=false, $rtnXml=true){
		
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
        if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
		if(!$this->dbG)$this->dbG = new Models_DbTable_Gevu_geos($this->db);
		if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->db);
		
		//création d'un nouvel instant si ajout forcer
		if(!$existe){
			$c = str_replace("::", "_", __METHOD__); 
			$idInst = $this->dbI->ajouter(array("id_exi"=>$idExi,"nom"=>$c));
			$data["id_instant"]= $idInst;
		}
		$data["lieu_parent"]= $idLieuParent;
		$data["lib"]= $lib;
		
		//ajoute un lieu au parent
		$arrLieu = $this->dbL->ajouter($data, $existe, true);
		
		//récupère les coordonnées géographique du parent
		$geos = $this->dbG->findById_lieu($idLieuParent);
		//ajoute les coordonnées géographique au nouveau lieu
		$geos[0]["id_lieu"] = $arrLieu["id_lieu"];
		unset($geos[0]["id_geo"]);
		unset($geos[0]["id_donnee"]);
		if($idInst)$geos[0]["id_instant"] = $idInst;
		$this->dbG->ajouter($geos[0], $existe);

		if($rtnXml){
			//on récupère les informations du lieu
			$xml = $this->getXmlLieu($arrLieu,true);
			return $xml;	
		}else{
			return $arrLieu["id_lieu"];
		}
		
        
    }
    
    
    /**
     * supprime un lieu et tous ces composants
     *
     * @param int $idLieu
     * @param int $idExi
     * @param string $idBase
     * 
     * @return array
     */
    public function deleteLieu($idLieu, $idExi, $idBase=false){
		    	
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
        if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
		if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->db);
		
		//création d'un nouvel instant
		$c = str_replace("::", "_", __METHOD__); 
		$idInst = $this->dbI->ajouter(array("id_exi"=>$idExi,"nom"=>$c));
        
        //récupère les infos du lieu
        $lieu = $this->dbL->findById_lieu($idLieu);
		$this->dbL->remove($idLieu, $this->db);
		
		//retourne les informations du parent
		return $idInst;		
		
    }
    
}

