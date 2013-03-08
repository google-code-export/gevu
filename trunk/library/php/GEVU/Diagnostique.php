<?php

class GEVU_Diagnostique extends GEVU_Site{
    	
	var $idScenar;
	var $idUnivers;
		
	/**
	* constructeur de la class
	*
    * @param string $idBase
    * @param boolean $cache
    * 
    */
	public function __construct($idBase=false, $cache = false)
    {
    	parent::__construct($idBase, $cache = false);
		
    }
	

    /**
     * Recherche un contacts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param string $idBase
     * @param string $obj
     * @param array $params
     *
     * @return array
     */
    public function getContact($idBase, $obj, $params)
    {
    	//initialise les gestionnaires de base de données
    	$this->getDb($idBase);
    	$o = new $obj($this->db);
    	$clef = $o->info(Zend_Db_Table_Abstract::PRIMARY);
    	$clef = $clef[1];
    	$table = $o->info(Zend_Db_Table_Abstract::NAME);
    	 
    	$query = $o->select()
    	->from( array("t" => $table), array($params['type']))
    	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
    	->joinInner(array('c' => 'gevu_contacts'),
    			't.'.$params['type'].' = c.id_contact',array('id_contact','nom','prenom','fixe','mobile','fax','mail'))
    			->where( "t.".$clef." = ?", $params['id']);
    
    	return $o->fetchAll($query)->toArray();
    }
    
    /**
     * Ajoute un contacts avec la valeur spécifiée
     *
     * @param string $idBase
     * @param string $obj
     * @param array $params
     *
     */
    public function ajouterContact($idBase, $obj, $params)
    {
    	//initialise les gestionnaires de base de données
    	$this->getDb($idBase);
    	$o = new $obj($this->db);
    	$clef = $o->info(Zend_Db_Table_Abstract::PRIMARY);
    	$clef = $clef[1];
    	$table = $o->info(Zend_Db_Table_Abstract::NAME);
    	    	 
    	$data = array($params['type']=>$params['idCtc']);
    	$o->update($data, $table.'.'.$clef.' = ' . $params['idLien']);
    }
    
    /**
     * Supprime un contact avec la valeur spécifiée
     *
     * @param string $idBase
     * @param string $obj
     * @param array $params

     */
    public function removeContact($idBase, $obj, $params)
    {
    	//initialise les gestionnaires de base de données
    	$this->getDb($idBase);
    	$o = new $obj($this->db);
    	$clef = $o->info(Zend_Db_Table_Abstract::PRIMARY);
    	$clef = $clef[1];
    	$table = $o->info(Zend_Db_Table_Abstract::NAME);
    	    	 
    	$data = array($params['type']=>-1);
    	$o->update($data, $table.'.'.$clef.' = ' . $params['idLien']);
    }    
	/**
	* copie et colle un lieu
    * @param int $idLieu
    * @param string $Obj
    * @param int $idExi
    * @param string $idBase
    * 
	* @return xml
    */
	public function copiecolleLieu($idLieuSrc, $idLieuDst, $idExi, $idBase=false){
			
            $this->idExi = $idExi;
            
			//initialise les gestionnaires de base de données
			$this->getDb($idBase);
        	if(!$this->dbL) $this->dbL = new Models_DbTable_Gevu_lieux($this->db);
			if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->db);
		
			//création d'un nouvel instant
			$c = str_replace("::", "_", __METHOD__); 
			$idInst = $this->dbI->ajouter(array("id_exi"=>$idExi,"nom"=>$c));
        	
        	//création du fil d'ariane
            $ariane=$this->dbL->getFullChild($idLieuSrc);
            $nbLieu = count($ariane);
            $newIdLieu = -1;
            $newLieu = -1;
            $arrParent = array();
	        for ($i = 0; $i < $nbLieu; $i++) {
            
	            //récupération des données courantes
				$lSrc = $ariane[$i];
	        	$Rowset = $this->dbL->find($lSrc["id_lieu"]);
				$lieuSrc = $Rowset->current();
				
				//création des data du nouveau lieu
				$dataLieu["lib"] = $lieuSrc["lib"];
				$dataLieu["id_instant"] = $idInst;
				$dataLieu["id_type_controle"] = $lieuSrc["id_type_controle"];
				//vérifie qui est le parent
				if($i==0){
					//dans le cas du premier niveau le lieu parent est la destination
					$dataLieu["lieu_parent"] = $idLieuDst;
				} elseif ($lSrc["niv"] > $ariane[$i-1]["niv"]){
					//dans le cas d'un lieu d'un niveau supérieur au précédent lieu le parent est le dernier lieu ajouté
					$dataLieu["lieu_parent"] = $newIdLieu;
				} elseif ($lSrc["niv"] < $ariane[$i-1]["niv"]){
					//dans le cas d'un lieu d'un niveau inférieur au précédent lieu le parent est celui du niveau inférieur
					$dataLieu["lieu_parent"] = $arrParent[$lSrc["niv"]];
				}
				//dans le cas d'un lieu au même niveau que le précédent le lieu parent est le dernier parent calculer
				//donc on garde la même valeur
				$arrParent[$lSrc["niv"]] = $dataLieu["lieu_parent"];
				
				//création du nouveau lieu
				if($i==0){
					$newLieu=$this->dbL->ajouter($dataLieu, false, true);	
					$newIdLieu = $newLieu["id_lieu"];					
				}else{
					$newIdLieu = $this->dbL->ajouter($dataLieu, false);					
				}
				
				//récupération des données lieés
	            $dt = $this->dbL->getDependentTables();
	            foreach($dt as $t){
					$items = $lieuSrc->findDependentRowset($t);
					if($items->count()){
						//vérifie si on traite une des tables qu'on ne copie pas
						// $t!="Models_DbTable_Gevu_diagnostics" && 
						// && $t!="Models_DbTable_Gevu_lieuxinterventions"
						// && $t!="Models_DbTable_Gevu_problemes" && 
		            	if($t!="Models_DbTable_Gevu_stats" && $t!="Models_DbTable_Gevu_observations" && $t!="Models_DbTable_Gevu_docsxlieux"){
		            	 	$dbT = new $t($this->db);
		            	 	foreach ($items as $row) {
		            	 		$d = $row->toArray();
		            	 		$d["id_lieu"] = $newIdLieu;		            	 		
		            	 		if(isset($d["id_instant"]))$d["id_instant"] = $idInst;
		            	 		$k = $dbT->info('primary');
		            	 		unset($d[$k[1]]);
		            	 		$dbT->ajouter($d);
		            	 	}
		            	}
	            	}
				}
			    
            }

            $xml = $this->getXmlNode($newLieu["id_lieu"], $idBase);
			return $xml;	
            
	}
	
    
    
	/**
	* ajoute un contrôle pour le lieu 
    * @param int $idLieu
    * @param array $Obj
    * @param int $idExi
    * @param string $idBase
    * @param int $idInst
    * 
    */
	public function ajoutCtlLieu($idLieu, $Obj, $idExi, $idBase=false, $idInst=false){
			
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
		
		if(!$idInst){
			if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->db);			
			//création d'un nouvel instant
			$c = str_replace("::", "_", __METHOD__); 
			$idInst = $this->dbI->ajouter(array("id_exi"=>$idExi,"nom"=>$c));		
		}
		
		//création de l'objet passé en paramètre
		if($Obj["zend_obj"]!=""){
			$data = array();
			$o = $Obj["zend_obj"];
			$db = new $o($this->db);
			if($Obj["zend_obj"]=="Models_DbTable_Gevu_espacesxinterieurs"){
				$data["id_type_specifique_int"]= $Obj["id_type_controle"];
				$data["fonction"]= $Obj["lib"];
			}		
			//initialisation des données
			$data["id_instant"]=$idInst;
			$data["id_lieu"]=$idLieu;
			//ajout du nouveau contrôle
			$db->ajouter($data, false);
		}
		//met à jour le lieu avec le type de controle
		if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
		$this->dbL->edit($idLieu, array("id_type_controle"=>$Obj["id_type_controle"], "lib"=>$Obj["lib"]));
	}
	
    /**
	* récupère les controles autorisés pour le lieu 
    * @param string $idLieu
    * @param string $idScenar
    * @param string $idBase
    * @param string $forTypeControle
    * @param string $forTypeInterv
    * 
    * @return Array
    */
	public function getLieuCtl($idLieu, $idScenar, $idBase=false, $forTypeControle=false, $forTypeInterv=false){
			
		$this->idScenar = $idScenar;
		$arrCrit = "";
		
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
        if(!$this->dbScena)$this->dbScena = new Models_DbTable_Gevu_scenario();
        if(!$this->dbScene)$this->dbScene = new Models_DbTable_Gevu_scenes();
        if(!$this->dbTypCtl)$this->dbTypCtl = new Models_DbTable_Gevu_typesxcontroles();
        if(!$this->dbC)$this->dbC = new Models_DbTable_Gevu_criteres();
        if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
        
        //récupère les paramètres du scénario
      	$arrScenar = $this->dbScena->findById_scenario($idScenar);
      	
        //récupération des données du lieu courant
		$r = $this->dbL->find($idLieu);
		$rLieu = $r->current();

		//récupère la scène de départ du scénario
        $scene = $this->dbScene->findByIdScene($arrScenar[0]["params"]);
        $params = json_decode($scene[0]['paramsCtrl']);
		$xmlScene = simplexml_load_string($params[0]->idCritSE);
		
		foreach ($xmlScene->node as $firstCtl) {
			//récupère les informations du contrôle
		    $rTypeCtrl = $this->dbTypCtl->findById_type_controle($firstCtl["idCtrl"]);
	        //vérifie si le lieu à le controle
	        $items = $this->verifIsControle($rTypeCtrl, $rLieu);
			if($items && $items->count()){
				//vérifie si on recherche les types d'intervention
				if($forTypeInterv){
					//on récupère les types d'intervention
					return $scene[0]['paramsProd'];
				}else
					//si le lieu à déjà le contrôle on ne peut pas en ajouter
					return "";
			}
			//récupère le parent qui possède ce type de controle
			$arrLieuxParents = $this->dbL->getParentForTypeControle($idLieu, $rTypeCtrl["zend_obj"], $rTypeCtrl["id_type_controle"]);				

			if($arrLieuxParents){
				//on réinitialise le tableau des contrôles
				$arrCtl = array(); 
				//calcul le nombre de niveau entre le lieu et son parent
				$niv = $rLieu["niv"]-$arrLieuxParents[0]["niv"];
				//création de la requête Xpath
				$path = "/node/node[@idCtrl=".$rTypeCtrl["id_type_controle"]."]";
				for ($i = 0; $i < $niv; $i++) {						
					$path .= "/node";
					//vérifie si un des parents à déjà le contrôle
					//sauf pour le dernier niveau 
					//car une type de contrôle peut être plusieurs fois dans un scénario
					if($i+1 < $niv){
						$result = $xmlScene->xpath($path);
		        		$nivO = 1000000000;
		        		$pathInt = "";
						foreach ($result as $node) {
			        		$rTC = $this->dbTypCtl->findById_type_controle($node["idCtrl"]);
	        				$arrLP = $this->dbL->getParentForTypeControle($idLieu, $rTC["zend_obj"], $rTC["id_type_controle"]);				        						
							//vérifie si le contrôle est bien dans le bon niveau de lieu
	        				if($arrLP && $arrLP[0]['niv'] >= $i && $arrLP[0]['niv'] < $nivO){
								//ajoute une condition dans la requête XML
								$pathInt = "[@idCtrl=".$node["idCtrl"]."]";
								//enregistre le niveau pour prendre en compte que le niveau le plus haut
								$nivO = $arrLP[0]['niv'];
							}	
						}
						//ajoute une condition dans la requête XML
						$path .= $pathInt;
						
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
		        		//sauf dans le cas d'une liste déroulante
						if($forTypeControle){
							$arrCtl[] = $rTypeCtrl1;										
						}else{
							//recherche les critères pour le controle
							$idCtrl = $node["idCtrl"]."";
							$uid = $node["uid"]."";
							//Recherche la scène
							$arrScene = $this->dbScene->findByIdScenarioType($idScenar, $uid, true);
							//vérifie si on recherche les types d'intervention
							if($forTypeInterv) return $arrScene[0]['paramsProd'];
							//vérifie si la scène possède des critères
							if(count($arrScene)>0 && $arrScene[0]['paramsCrit']){
					        		$rsCrit = $this->dbC->findByIdTypeControle($idCtrl);	        		
									$arrCrit["criteres"]["ctrl_".$idCtrl] = $rsCrit;										
									$arrCrit["etapes"][] = array("idCtrl"=>$idCtrl,"uid"=>$uid,'paramsCrit'=>$arrScene[0]['paramsCrit']);										
							}
							return array("ctrl"=>"","crit"=>$arrCrit);
						}
		        	}else{
						$arrCtl[] = $rTypeCtrl1;										
					}		        	
				}
				return array("ctrl"=>$arrCtl,"crit"=>$arrCrit);				
			}else{
				//si on ne trouve aucun controle on peut ajouter le contrôle
				$arrCtl[] = $rTypeCtrl;										
			}
		}
		return array("ctrl"=>$arrCtl,"crit"=>$arrCrit);
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
        }else{
        	//vérifie si le lieu à le type de controle
        	if($rLieu['id_type_controle']==$arrType["id_type_controle"]){
        		//pour la suite il faut que item soit le résultat d'une requête
        		$items=$rLieu->findDependentRowset("Models_DbTable_Gevu_geos");
        	}
        }
        return $items;
	}
		    
	/**
	* vérifie si un scène possède le controle et renvoie les informations
	*  
    * @param int $sceneTypeCtrl
    * @param array $arrTypeCtrl
    * 
    * @return boolean
    */
	function verifIsControleInScene($sceneTypeCtrl, $arrTypeCtrl){
		foreach ($arrTypeCtrl as $typeCtrl) {
			if($typeCtrl["id_type_controle"]==$sceneTypeCtrl) return true;
		}
		return false;		
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
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".md5($idInstant)."_".$idBase; 
	   	$r = false;//$this->cache->load($c);
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
			
	        if(!$this->dbD){
				$this->getDb($idBase);
	        	$this->dbD = new Models_DbTable_Gevu_diagnostics($this->db);
				//marque les derniers diagnostics car la demande ne vient pas de getnodedata
	        	$this->dbD->setLastDiagForLieu($idLieu);        	
	        }
				        
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
	   	$xml = false;//$this->cache->load($c);
        if(!$xml){
    		$xml="";
			//initialise les gestionnaires de base de données
			$this->getDb($idBase);
        	if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
			
        	//$r = $this->dbL->findById_lieu($idLieu);
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
        	/*on continue d'afficher les enfants
        	if($v['nbDiag']>0){
				$xml = "";
        		break;        		
        	}
        	*/
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
	   	$rs = false;//$this->cache->load($c);
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
	   	$rs = false;//$this->cache->load($c);
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
     * @param int $idScenar
     * 
     * @return array
     */
    public function getNodeRelatedData($idLieu, $idExi, $idBase=false, $idScenar=false){
    
		$c = str_replace("::", "_", __METHOD__)."_".$idLieu."_".$idExi."_".$idBase; 
	   	$res = $this->cache->load($c);
    	        
        if(!$res){
            $res = array();
            $this->idExi = $idExi;
            
			//initialise les gestionnaires de base de données
			$this->getDb($idBase);
        	if(!$this->dbL) $this->dbL = new Models_DbTable_Gevu_lieux($this->db);
	        if(!$this->dbD) $this->dbD = new Models_DbTable_Gevu_diagnostics($this->db);
        	
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
				        	$res["___diagnostics"]["self"] = $this->getDiagForLieu($idLieu, $idExi, $idBase);			
	            		}
	            	}elseif($t=="Models_DbTable_Gevu_geos"){
						$dbT = new $t($this->db);
	            		$res[$t]= $dbT->findById_lieu($idLieu);
	            	}elseif($t=="Models_DbTable_Gevu_docsxlieux"){
						$dbT = new $t($this->db);
	            		$res[$t]= $dbT->findByIdLieu($idLieu);
	            	}elseif($t=="Models_DbTable_Gevu_lieuxinterventions"){
						//on affiche toujours le formulaire d'intervention 
						//donc pas de traitement
	            		$dbT = "";
	            	}else{
						$res[$t]=$items->toArray();
	            	}
            	}
			}
			
			//vérifie si les enfants ont des diagnostics
			/*plus besoin
			$rs = $this->getDiagForLieuEnfants($idLieu, $idExi, $idBase);
			$res["___diagnostics"]["enfants"] = $rs["enfants"];			
			*/
			
			//ajoute le dernier diagnostic pour le lieu
	        $res["___diagnostics"]['diag'] = $this->calculDiagForLieu($idLieu,-1,$idBase);
			
	        if($idScenar){
		        //ajoute les interventions
		        $res["___interventions"] = $this->getTypeInterv($idScenar, $idLieu, $idBase);	
	        }
	        	        
            $this->cache->save($res, $c);
        }
        return $res;
    }

	/**
	* renvoie le diagnostic des enfants d'un lieu
    * @param string $idLieu
    * @param int $idExi
    * @param string $idBase
    * @return Array
    */
	public function getDiagForLieuEnfants($idLieu, $idExi, $idBase=false){
    
        if(!$this->dbL){
			$this->getDb($idBase);
        	$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
			//marque les derniers diagnostics car la demande ne vient pas de getnodedata
        	$this->dbD = new Models_DbTable_Gevu_diagnostics($this->db);
        	$this->dbD->setLastDiagForLieu($idLieu);        	
        }
		        
		//vérifie si les enfants ont des diagnostics
		$rs = $this->dbL->findByLieu_parent($idLieu);
		$res = array();
		foreach ($rs as $r){
			if($r['nbDiag']>0){
				//récupère les informations de l'enfant
				$resEnf = $this->getDiagForLieu($r['id_lieu'], $idExi, $idBase);
				if(count($res["enfants"])== 0)$res["enfants"]=$resEnf;
				else $res["enfants"] = array_merge($res["enfants"], $resEnf);			
			}
		}
		
		return $res;
	}
    
    
    
    /**
     * Recherche les type d'intervention autorisés pour ce lieu
     * et retourne ces entrées.
     *
     * @param int $idScenar
     * @param int $idLieu
     * @param int $idBase
     * 
     */
    public function getTypeInterv($idScenar, $idLieu, $idBase)
    {
    	//récupère la liste des produits du scénario en cours pour le lieu en cours
    	$json = $this->getLieuCtl($idLieu, $idScenar, false, false, true);
    	if($json){
    		$arr = json_decode($json);
    		$ids = "";
    		foreach ($arr as $p) {
    			$ids .= $p->id_produit.", ";
    		}
    		$ids .= "-1";
        	if(!$this->dbInt)$this->dbInt = new Models_DbTable_Gevu_interventions($this->db);
    		return $this->dbInt->findByIdsProduit($ids);     	
    	}else{
    		//il n'y a pas de produit pour le scénario
	        return "no_product";
    	} 
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
        	if(!$this->dbScena)$this->dbScena = new Models_DbTable_Gevu_scenario();
        	if(!$this->dbScene)$this->dbScene = new Models_DbTable_Gevu_scenes();
        	if(!$this->dbC)$this->dbC = new Models_DbTable_Gevu_criteres();

	    	//récupère la liste des contrôles à effectuer
	    	$arrCtl = $this->getLieuCtl($idLieu, $idScenario, false, "/node[@idCtrl='".$idTypeCtrl."']/node");
        	
        	//récupération des informations de scenario
        	$arrScena = $this->dbScena->findById_scenario($idScenario);
        	$psScena = Zend_Json::decode($arrScena[0]['params']);
        	foreach ($psScena as $pEtapes) {
        		foreach ($pEtapes['etapes'] as $pScene) {
        			//vérifie si la scène doit être prise en compte
        			if($this->verifIsControleInScene($pScene['id_type_controle'], $arrCtl)){
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
     * Enregistre les choix de diagnostic
     *
     * @param int $idExi
     * @param int $idLieu
     * @param string $comment
     * @param array $params
     * @param string $idBase
     * @param int $idInst
     * 
     * @return array
     */
    public function setChoix($idExi, $idLieu, $comment, $params, $idBase, $instant=-1){
		
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
    	if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->db);
        if(!$this->dbD)$this->dbD = new Models_DbTable_Gevu_diagnostics($this->db);
        if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
    	    	
        //création de l'instant
        if($instant==-1)$idInst = $this->dbI->ajouter(array('id_exi'=>$idExi,'commentaires'=>$comment,'nom'=>'setChoix'),false,$idBase);
 		else $idInst = $instant;
 		       
        //enregsitre les choix
        $oTypeControle = -1;
        $idDiags = array();
       	foreach ($params as $p) {
       		/*plus besoin car le lieu = le type de controle
       		if($oTypeControle != $p['id_type_controle']){
	       		//récupère le lieu enfant correspondant au type de controle
	       		$idLieuControle = $this->dbL->getEnfantForTypeControle($idLieu, $p['id_type_controle'], $idInst);
	       		$oTypeControle = $p['id_type_controle'];       			
       		}*/
       		$idLieuControle = $idLieu;
       		if(!$p['id_diag']){
	       		//ajoute le diagnostique
	       		$idDiags[] = $this->dbD->ajouter(array('id_critere'=>$p['id_critere'],'id_reponse'=>$p['id_reponse'],'id_instant'=>$idInst,'id_lieu'=>$idLieuControle),false);
       		}else{
       			//modifie le diagnostic
	       		$this->dbD->edit($p['id_diag'], array('id_reponse'=>$p['id_reponse']));
	       		$idDiags[] = $p['id_diag'];
       		}
       	}
        return array("idInst"=>$idInst,"idDiags"=>$idDiags);
    }


    /**
     * Création d'un choix de diagnostic
     *
     * @param int $idExi
     * @param int $idLieu
     * @param string $comment
     * @param array $params
     * @param string $idBase
     * 
     * @return array
     */
    public function creaChoix($idExi, $idLieu, $comment, $p, $idBase){
		
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
    	if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->db);
        if(!$this->dbD)$this->dbD = new Models_DbTable_Gevu_diagnostics($this->db);
        if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
    	    	
        //création de l'instant
        $idInst = $this->dbI->ajouter(array('id_exi'=>$idExi,'commentaires'=>$comment,'nom'=>'setChoix'),false,$idBase);
 		       
		//récupère le lieu enfant correspondant au type de controle
		/*plus besoin car le lieu correspond au type de controle
	    $idLieuControle = $this->dbL->getEnfantForTypeControle($idLieu, $p['id_type_controle'], $idInst);
	    */
        $idLieuControle = $idLieu;
        
   		//ajoute le diagnostique
	    $idDiag = $this->dbD->ajouter(array('id_critere'=>$p['id_critere'],'id_reponse'=>$p['id_reponse'],'id_instant'=>$idInst,'id_lieu'=>$idLieuControle),false);
        return array("idInst"=>$idInst,"idDiag"=>$idDiag);
    }
    
    /**
     * Création d'un nouvel instant
     *
     * @param array $params
     * @param string $idBase
     * 
     * @return int
     */
    public function setInstant($params, $idBase){
		
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
    	if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->db);
    	    	
        //création de l'instant
        $idInst = $this->dbI->ajouter($params,false,$idBase);
        
        return $idInst;
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
     * Enregistre la modification d'un objet
     *
     * @param int $id
     * @param array $data
     * @param string $obj
     * @param string $idBase
     * 
     * @return integer
     */
    public function edit($id, $data, $obj, $idBase){
		
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
        $o = new $obj($this->db);
    	    	
        return $o->edit($id, $data);
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
    public function ajoutLieu($idLieuParent, $idExi=-1, $idBase=false, $lib="Nouveau lieu", $existe=false, $rtnXml=true){
		
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
        if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
		if(!$this->dbG)$this->dbG = new Models_DbTable_Gevu_geos($this->db);
		if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->db);
		
		//création d'un nouvel instant si ajout forcer
		if(!$existe){
			$c = str_replace("::", "_", __METHOD__); 
			$this->idInst = $this->dbI->ajouter(array("id_exi"=>$idExi,"nom"=>$c));
		}
		$data["id_instant"]= $this->idInst;
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
		if($this->idInst)$geos[0]["id_instant"] = $this->idInst;
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
     * genere une arboressence de diagnostic à partir d'un itinéraire
     *
     * @param int $idChaine
     * @param string $idBase
     * 
     * @return array
    */
	public function getChaineDepla($idChaine, $idBase=false){
		
	    //initialise la base
		if($idBase!=$this->idBase)$this->getDb($idBase);		
		if(!$this->dbLCD)$this->dbLCD = new Models_DbTable_Gevu_lieuxchainedeplacements($this->db);
		
		//récupère le détail de la chaîne de déplacement
		$arr = $this->dbLCD->findById_chainedepla($idChaine);
		$nb = count($arr);
		for ($i = 0; $i < $nb; $i++) {
			//ajoute le diagnostique du lieu
			$arr[$i]['diag'] = $this->calculDiagForLieu($arr[$i]['id_lieu'],-1,$idBase);
		}
		return $arr;
	}
        
    
    /**
     * genere une arboressence de diagnostic à partir d'un itinéraire
     *
     * @param int $idChaine
     * @param string $idBase
     * @param int $idExi
     * 
     * @return array
     */
    public function genereDiagWithIti($idChaine, $idBase=false, $idExi=-1){

	    set_time_limit(1000);
    	
	    //initialise la base
		if($idBase!=$this->idBase)$this->getDb($idBase);
    	
    	//initialise les gestionnaires de base de données
        if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
		if(!$this->dbCD)$this->dbCD = new Models_DbTable_Gevu_chainesdeplacements($this->db);
		if(!$this->dbLCD)$this->dbLCD = new Models_DbTable_Gevu_lieuxchainedeplacements($this->db);
		if(!$this->dbG)$this->dbG = new Models_DbTable_Gevu_geos($this->db);
		if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->db);
		
		//création d'un nouvel instant
		if(!$this->idInst){
			$c = str_replace("::", "_", __METHOD__); 
			$this->idInst = $this->dbI->ajouter(array("id_exi"=>$idExi,"nom"=>$c));		
		}
		
        //récupère le lieu univers
		$arrLieux = $this->dbL->findByLft(1);
		$this->idUnivers = $arrLieux[0]['id_lieu'];
		
		//récupère la chaine de déplacement
		$arr = $this->dbCD->findById_chainedepla($idChaine);
		$xml = simplexml_load_string($arr[0]['params']);
		
		//création du lieu de départ à toutes les échelles
		$idLieu = $this->genereDiagWithGeo($xml['start']."", -1, $idBase, $idExi);
		
		//création de l'arboressence du diagnostic à la dernière échelle
		$ordre = 0;
		foreach ($xml->node as $n) {
			$idLieu = $this->genereDiagWithGeo($n['start']."", 0, $idBase, $idExi);
			$this->dbLCD->ajouter(array("id_chainedepla"=>$idChaine, "id_lieu"=>$idLieu, "ordre"=>$ordre));
			$ordre ++;
		}

		//création du lieux d'arrivée à toutes les échelles
		$idLieu = $this->genereDiagWithGeo($xml['end']."", -1, $idBase, $idExi);
		$this->dbLCD->ajouter(array("id_chainedepla"=>$idChaine, "id_lieu"=>$idLieu, "ordre"=>$ordre));

		return $this->getChaineDepla($idChaine, $idBase);
    }	    

    /**
     * genere une arboressence de diagnostic à partir d'une définition geographique
     *
     * @param number $latlng
     * @param int $echelle : permet de définir si on traite toutes les échelles géographique ou juste un niveau spécifique
     * @param string $idBase
     * @param int $idExi
     * 
     * @return int
     */
    public function genereDiagWithGeo($latlng, $echelle=-1, $idBase=false, $idExi=-1){

		//initialise la base
		if($idBase!=$this->idBase)$this->getDb($idBase);
    	
    	//initialise les gestionnaires de base de données
        if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
		if(!$this->dbCD)$this->dbCD = new Models_DbTable_Gevu_chainesdeplacements($this->db);
		if(!$this->dbG)$this->dbG = new Models_DbTable_Gevu_geos($this->db);
		if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->db);
		
		//création d'un nouvel instant
		if(!$this->idInst){
			$c = str_replace("::", "_", __METHOD__); 
			$this->idInst = $this->dbI->ajouter(array("id_exi"=>$idExi,"nom"=>$c));		
		}
		
		//récupère les données géographiques du lieu
		$html = $this->getUrlBodyContent("http://maps.googleapis.com/maps/api/geocode/json?",array('latlng'=>$latlng,'sensor'=>'true'));
		$js = json_decode($html);
		//traite les données géographique
		if($js->status="OK"){
			$arrLieux = $js->results;
			//gestion des niveaux d'échelles
			if($echelle==-1){
				//on inverse l'échelle des lieux
				krsort($arrLieux);
				$idParent = $this->idUnivers;
				$echelle = count($arrLieux)-1;
			}else{
				//on récupère le parent du lieu qu'on traite
				/*
				foreach ($arrLieux[$echelle+1]->address_components as $lp) {
					if($lp->types[0] != "postal_code" ){
						$libP = $lp->long_name;
						break;
					} 
				}
				*/
				//le lieu parent est "normalement" la ville
				$arrParent = $this->dbL->findByLib($arrLieux[$echelle+1]->address_components[1]->long_name);
				$idParent = $arrParent[0]['id_lieu'];				
			}
			//création des lieux correspondant
			$i=0;
			foreach ($arrLieux as $l) {
				if($l->types[0] != "postal_code" && $i<=$echelle){
					//gestion des diags suivant le type de lieu
					switch ($l->types[0]) {
						case "street_address":
							/*on crée un lieux pour l'ensemble des voiries
							$lib = "voiries";
							//ajoute le lieu
							$idLieu = $this->dbL->ajouter(array("lib"=>$lib,"lieu_parent"=>$idParent));
							//ajoute un contrôle de type voiries
							$this->ajoutCtlLieu($idLieu, array("zend_obj"=>"","id_type_controle"=>117,"lib"=>$lib), $idExi, $idBase, $this->idInst);
							$idParent = $idLieu;
							*/
							
							//on crée en premier la voie
							$lib = $l->address_components[1]->long_name;
							//ajoute le lieu
							$idLieu = $this->dbL->ajouter(array("lib"=>$lib,"lieu_parent"=>$idParent));
							//ajoute la géographie
							$this->dbG->ajouter(array("id_lieu"=>$idLieu, "adresse"=>$lib, "zoom_max"=>"17", "type_carte"=>"hybrid", "lat"=>$l->geometry->viewport->northeast->lat, "lng"=>$l->geometry->viewport->northeast->lng));
							//ajoute un contrôle de type voie
							$this->ajoutCtlLieu($idLieu, array("zend_obj"=>"","id_type_controle"=>55,"lib"=>$lib), $idExi, $idBase, $this->idInst);
							$idParent = $idLieu;
							
							//on crée ensuite le tronçon
							//il correspond au trottoir pair et impair
							$numVoie = explode("_",$l->address_components[0]->long_name);
							if($numVoie[0]%2==0)$troncon="tronçon pair"; else $troncon="tronçon impair";
							//ajoute le lieu
							$idLieu = $this->dbL->ajouter(array("lib"=>$troncon,"lieu_parent"=>$idParent));
							//ajoute la géographie
							$this->dbG->ajouter(array("id_lieu"=>$idLieu, "adresse"=>$troncon, "zoom_max"=>"18", "type_carte"=>"hybrid", "lat"=>$l->geometry->viewport->northeast->lat, "lng"=>$l->geometry->viewport->northeast->lng));
							//ajoute un contrôle de type segment
							$this->ajoutCtlLieu($idLieu, array("zend_obj"=>"","id_type_controle"=>130,"lib"=>$troncon), $idExi, $idBase, $this->idInst);
							$idParent = $idLieu;
							
							//on crée enfin le segment à partir du numéro de voie
							$lib = "segment ".$l->address_components[0]->long_name;
							//ajoute le lieu
							$idLieu = $this->dbL->ajouter(array("lib"=>$lib,"lieu_parent"=>$idParent));
							//ajoute la géographie
							$this->dbG->ajouter(array("id_lieu"=>$idLieu, "adresse"=>$l->formatted_address, "lat"=>$l->geometry->location->lat, "lng"=>$l->geometry->location->lng
								, "zoom_max"=>"20", "type_carte"=>"hybrid", "latlng"=>"(".$l->geometry->location->lat.",".$l->geometry->location->lng.")"
								, "ne"=>"(".$l->geometry->viewport->northeast->lat.",".$l->geometry->viewport->northeast->lng.")"
								, "sw"=>"(".$l->geometry->viewport->southwest->lat.",".$l->geometry->viewport->southwest->lng.")"
								, "data"=>json_encode($l)));
							//ajoute un contrôle de type segment
							$this->ajoutCtlLieu($idLieu, array("zend_obj"=>"","id_type_controle"=>56,"lib"=>$lib), $idExi, $idBase, $this->idInst);
							$idParent = $idLieu;
							
							/**TODO:gérer la création des diag suivant le scénario
							 * pour l'instant on le fait en dur 
							 */
							$lib = "Voirie cheminement";
							$idLieu = $this->dbL->ajouter(array("lib"=>$lib,"lieu_parent"=>$idParent));
							$this->dbG->ajouter(array("id_lieu"=>$idLieu, "adresse"=>$l->formatted_address, "zoom_max"=>"20", "type_carte"=>"hybrid", "lat"=>$l->geometry->location->lat, "lng"=>$l->geometry->location->lng));
							$this->ajoutCtlLieu($idLieu, array("zend_obj"=>"","id_type_controle"=>28,"lib"=>$lib), $idExi, $idBase, $this->idInst);
							
							$lib = "Voirie pente";
							$idLieu = $this->dbL->ajouter(array("lib"=>$lib,"lieu_parent"=>$idParent));
							$this->dbG->ajouter(array("id_lieu"=>$idLieu, "adresse"=>$l->formatted_address, "zoom_max"=>"20", "type_carte"=>"hybrid", "lat"=>$l->geometry->location->lat, "lng"=>$l->geometry->location->lng));
							$this->ajoutCtlLieu($idLieu, array("zend_obj"=>"","id_type_controle"=>32,"lib"=>$lib), $idExi, $idBase, $this->idInst);
							
							$lib = "Voirie ressaut";
							$idLieu = $this->dbL->ajouter(array("lib"=>$lib,"lieu_parent"=>$idParent));
							$this->dbG->ajouter(array("id_lieu"=>$idLieu, "adresse"=>$l->formatted_address, "zoom_max"=>"20", "type_carte"=>"hybrid", "lat"=>$l->geometry->location->lat, "lng"=>$l->geometry->location->lng));
							$this->ajoutCtlLieu($idLieu, array("zend_obj"=>"","id_type_controle"=>34,"lib"=>$lib), $idExi, $idBase, $this->idInst);
							
							$lib = "Voirie signalétique";
							$idLieu = $this->dbL->ajouter(array("lib"=>$lib,"lieu_parent"=>$idParent));
							$this->dbG->ajouter(array("id_lieu"=>$idLieu, "adresse"=>$l->formatted_address, "zoom_max"=>"20", "type_carte"=>"hybrid", "lat"=>$l->geometry->location->lat, "lng"=>$l->geometry->location->lng));
							$this->ajoutCtlLieu($idLieu, array("zend_obj"=>"","id_type_controle"=>35,"lib"=>$lib), $idExi, $idBase, $this->idInst);
							
							$lib = "Voirie équipement";
							$idLieu = $this->dbL->ajouter(array("lib"=>$lib,"lieu_parent"=>$idParent));
							$this->dbG->ajouter(array("id_lieu"=>$idLieu, "adresse"=>$l->formatted_address, "lat"=>$l->geometry->location->lat, "lng"=>$l->geometry->location->lng));
							$this->ajoutCtlLieu($idLieu, array("zend_obj"=>"","id_type_controle"=>29,"lib"=>$lib), $idExi, $idBase, $this->idInst);
							
							//on garde le lieu du segment comme référence
							$idLieu = $idParent;
							
							break;
						default:
							$lib = $l->address_components[0]->long_name;
							//ajoute le lieu
							$idLieu = $this->dbL->ajouter(array("lib"=>$lib,"lieu_parent"=>$idParent));
							//ajoute la géographie
							$this->dbG->ajouter(array("id_lieu"=>$idLieu, "adresse"=>$lib, "lat"=>$l->geometry->location->lat, "lng"=>$l->geometry->location->lng
								, "latlng"=>"(".$l->geometry->location->lat.",".$l->geometry->location->lng.")"
								, "data"=>json_encode($l)));
							break;						
					}
					$idParent = $idLieu;
				}
				$i++;
			}
		}
    	return $idLieu;
    }
    
    
    /**
     * supprime un lieu et tous ces composants
     *
     * @param int $idLieu
     * @param int $idExi
     * @param string $idBase
     * @param boolean $hierarchie
     * 
     * @return array
     */
    public function deleteLieu($idLieu, $idExi, $idBase=false, $hierarchie=true){
		    	
		//initialise les gestionnaires de base de données
		$this->getDb($idBase);
        if(!$this->dbL)$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
		if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->db);
		
		//création d'un nouvel instant
		$c = str_replace("::", "_", __METHOD__); 
		$idInst = $this->dbI->ajouter(array("id_exi"=>$idExi,"nom"=>$c));
        
        //suppression du lieu
		$this->dbL->remove($idLieu, $hierarchie);
		
		//retourne les informations du parent
		return $idInst;		
		
    }
    
}

