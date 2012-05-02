<?php
class GEVU_Import extends GEVU_Site{
    	
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
	
	
	public function addScenario($file){
		try {
		
		//charge le fichier de scenario
		$xml = simplexml_load_file($file);
		
		//création des tables
		//$idBase = "gevu_new_alceane";
		$site = new GEVU_Site();
		$db = $site->getDb($idBase);
		$dbS = new Models_DbTable_Gevu_scenario($db);
		$dbSc = new Models_DbTable_Gevu_scenes($db);
		$this->dbCr = new Models_DbTable_Gevu_criteres($db);
		
		//on crée un scénario pour le fichier
		$idS = $dbS->ajouter(array("lib"=>"fichier scenarisation.xml"));
		//on boucle sur les grilles pour créer les paramètres
		//[{"num":0,"etapes":[{"id_scene":24,"id_type_controle":"8"}]},{"num":1,"etapes":[]}]
		$etapes = "[";
		$i=0;
		foreach ($xml->grille as $grille) {
			//création des étapes
			$etapes .= '{"num":'.$i.',"etapes":[';
			//boucle pour créer les étapes
			//[{"idCritNU":"-1","idCritSE":"
			//<node idCrit=\"-1\" ref=\"critères\" isBranch=\"true\">\n  
			//	<node id_critere=\"140\" ref=\"3_cr_ent_01 : Oui\" isBranch=\"true\" CondRep=\"2_1\">\n    
			//		<node ref=\"3_cr_ent_02\" criteres=\"\" isBranch=\"false\"/>\n
			//	</node>\n  
			//	<node id_critere=\"142\" ref=\"3_cr_ent_03\" isBranch=\"false\" CondRep=\"\"/>\n
			//</node>"}]
			$crit = -1;
			foreach ($grille->question as $question) {
				if($crit == -1){
					//récupération du type de controle
					$crit = $this->dbCr->findByRef($question["id"]);
					//création d'une nouvelle scène
					$idSc = $dbSc->ajouter(array("id_scenario"=>$idS,"type"=>"DiagTypeControle_".$crit["id_type_controle"]));
					$scene = '<node idCrit=\"-1\" ref=\"critères\" isBranch=\"true\">\n';									
				}
				//concaténation des paramètres
				$scene .= $this->addSceneParam($question);
			}
			//mise à jour des paramètres
			$dbSc->edit($idSc, array('params'=>'[{"idCritNU":"-1","idCritSE":"'.$scene.'</node>"}]','xml'=>$scene.'</node>'));
			$etapes .= '{"id_scene":'.$idSc.',"id_type_controle":"'.$crit["id_type_controle"].'"}]},';
			$i++;
		}
		//on fini par une étape vide
		$etapes .= '{"num":'.$i.',"etapes":[]}]';
		$dbS->edit($idS, array("params"=>$etapes));
		
		}catch (Zend_Exception $e) {
			// Appeler Zend_Loader::loadClass() sur une classe non-existante
          	//entrainera la levée d'une exception dans Zend_Loader
          	echo "Récupère exception: " . get_class($e) . "\n";
          	echo "Message: " . $e->getMessage() . "\n";
          	// puis tout le code nécessaire pour récupérer l'erreur
		}		
	}
	public function addSceneParam($question, $crit=false){
		
		if(!$crit) $crit = $this->dbCr->findByRef($question["id"]);
				
		if($question['reponse']){
			switch ($question['reponse']) {
				case 1:
					$mot="Oui";
					break;
				case 2:
					$mot="Non";
					break;
				case 124:
					$mot="N. A.";
					break;
			}
			if($question['reponse']==1)$mot="Oui"; else $mot="non"; 
			$scene = '<node id_critere=\"'.$crit["id_critere"].'\" ref=\"'.$crit["ref"].' : '.$mot.'\" isBranch=\"true\" CondRep=\"2_'.$question['reponse'].'\" >\n';
		}else{
			$scene = '<node id_critere=\"'.$crit["id_critere"].'\" ref=\"'.$crit["ref"].'\" criteres=\"\" isBranch=\"false\" >\n';			
		}
		if($question->count() > 1 ){
			foreach ($question->children() as $child) {
				$scene .= $this->addSceneParam($child);  
			}
		}
		$scene .= '</node>\n';  
		return $scene; 									
	}
	
	public function addDoc($data){

		try {
			$prefix = "";
			if($data['objName']=="model_rapport"){
				$rep = SEP_PATH.'data'.SEP_PATH.'rapports'.SEP_PATH.'models';
			}elseif($data['objName']=='Models_DbTable_Gevu_docsxproblemes'){
				$rep = SEP_PATH.'data'.SEP_PATH.'problemes'.SEP_PATH.$data['idBase'];
				//vérifie s'il faut créer le répertoire existe
				if (!is_dir(ROOT_PATH.$rep)) {
                    mkdir(ROOT_PATH.$rep);  
                }
                //initialisation du préfixe
                $prefix = "prob_".$data['objId']."_";
			}elseif($data['objName']=='Models_DbTable_Gevu_docsxlieux'){
				$rep = SEP_PATH.'data'.SEP_PATH.'lieux'.SEP_PATH.$data['idBase'];
				//vérifie s'il faut créer le répertoire existe
				if (!is_dir(ROOT_PATH.$rep)) {
                    mkdir(ROOT_PATH.$rep);  
                }
                //initialisation du préfixe
                $prefix = "lieux_".$data['objId']."_";
			}else{
				$rep = SEP_PATH.'data'.SEP_PATH.'upload';    
			}
		
			$adapter = new Zend_File_Transfer_Adapter_Http();
		        //echo ROOT_PATH.'/data/upload';
		    $adapter->setDestination(ROOT_PATH.$rep);
		    //echo ROOT_PATH.$rep;
		    
			if (!$adapter->receive()) {
				$messages = $adapter->getMessages();
				echo implode("Mauvaise réception\n", $messages);
		      }else{
				// Retourne toutes les informations connues sur le fichier
				$files = $adapter->getFileInfo();
				foreach ($files as $file => $info) {
					// Les validateurs sont-ils OK ?
					if (!$adapter->isValid($file)) {
						print "Désolé mais $file ne correspond à ce que nous attendons";
						continue;
					}
					//renomme le fichier pour éviter les doublons
					$tabDecomp = explode('.', $info["name"]);
					$extention = ".".strtolower($tabDecomp[sizeof($tabDecomp)-1]);
					$data['new_name'] = $prefix.uniqid().$extention;
			        $data['rep'] = ROOT_PATH.$rep.SEP_PATH; 
					$path = $data['rep'].$data['new_name'];					
			        $url = WEB_ROOT.$rep.SEP_PATH.$data['new_name'];

			        $dataDoc = array(
			    		"url"=>$url,"titre"=>$info["name"],"content_type"=>$adapter->getMimeType()
			    		,"path_source"=>$path
			    		,"tronc"=>$data['objName']
			    		);
					
			    	rename(ROOT_PATH.$rep.'/'.$info["name"],$path);
			    		
			    		
					$this->saveDoc($data, $dataDoc);
					
					if($data['objName']=='imp_csv'){
						$this->traiteDoc($idDoc, $path);
					}
					//print_r($info);					
				}
		      }
		      
		}catch (Zend_Exception $e) {
			// Appeler Zend_Loader::loadClass() sur une classe non-existante
          	//entrainera la levée d'une exception dans Zend_Loader
          	echo "Récupère exception: " . get_class($e) . "\n";
          	echo "Message: " . $e->getMessage() . "\n";
          	// puis tout le code nécessaire pour récupérer l'erreur
		}
     	//echo json_encode(array("error"=>$info["error"]));   
     	echo json_encode($info);   
	}
    
    public function saveDoc($data, $dataDoc){
    	
    	if($dataDoc["url"]=="url")return ;
    	
    	$site = new GEVU_Site($data['idBase']);
    	
		$ins = new Models_DbTable_Gevu_instants($site->db);
		$idIns = $ins->ajouter(array("nom"=>$nom."_addDoc","id_exi"=>$data['idExi']));

		$dataDoc["id_instant"] = $idIns;
		
		$doc = new Models_DbTable_Gevu_docs($site->db);
		$idDoc = $doc->ajouter($dataDoc,false);
		
		if($data['objName']=='img_solus'){
			$doc_obj = new Models_DbTable_Gevu_docsxsolutions();
			$doc_obj->ajouter(array("id_doc"=>$idDoc,"id_instant"=>$idIns,"id_solution"=>$data['objId']),false);		
		}
		if($data['objName']=='img_produit'){
			$doc_obj = new Models_DbTable_Gevu_docsxproduits();
			$doc_obj->ajouter(array("id_doc"=>$idDoc,"id_instant"=>$idIns,"id_produit"=>$data['objId']),false);		
		}
		if($data['objName']=='Models_DbTable_Gevu_docsxproblemes' || $data['objName']=='Models_DbTable_Gevu_docsxlieux' ){
			//redimensionne l'image pour la vignette
			$this->fctredimimage(120,120,'',"vig_".$data['new_name'],$data['rep'],$data['new_name']);
			//redimensionne l'image pour l'affichage web
			$this->fctredimimage(400,400,'','',$data['rep'],$data['new_name']);
			//ajoute une signature
			$this->fcttexteimage("GEVU", '', '',$data['rep'], $data['new_name'], 'HG');
			if($data['objName']=='Models_DbTable_Gevu_docsxproblemes'){
				$doc_obj = new Models_DbTable_Gevu_docsxproblemes($site->db);
				$doc_obj->ajouter(array("id_doc"=>$idDoc,"id_instant"=>$idIns,"id_probleme"=>$data['objId']));		
			}
			if($data['objName']=='Models_DbTable_Gevu_docsxlieux'){
				$doc_obj = new Models_DbTable_Gevu_docsxlieux($site->db);
				$doc_obj->ajouter(array("id_doc"=>$idDoc,"id_instant"=>$idIns,"id_lieu"=>$data['objId']));
			}		
		}		
    }

    public function traiteDoc($idDoc, $creerModele=false){

		$doc = new Models_DbTable_Gevu_docs();
		$docInfos = $doc->findById_doc($idDoc);    		
    	
    	//chargement du fichier
		$chaines = file($docInfos['path_source']);
		
		//chargement de la description du traitement 
		$pimp = new Models_DbTable_Gevu_paramximport();
		$trtmts = $pimp->findByType_import($docInfos['tronc']);
		
		// parcourt toute les lignes du fichier
		foreach ($chaines as $x => $chaine) {
			$chaine = trim($chaine); 
			$arr = explode("\t", $chaine);
			$err = "";
			$Querys=array(); 
			$nbCol = count($arr);
			for ($j=0; $j<$nbCol; $j++) { 
				if($x==0){
					//vérification de l'entête des colonnes
					if($arr[$j]!=$trtmts[$j]['colSource']){
						$err .= "La colonne ".($j+1)." se nomme '".$arr[$j]."' au lieu de '".$trtmts[$j]['colSource']."'.\n";
					}
					//création du modèle
					if($creerModele)$pimp->ajouter(array("colSource"=>$arr[$j],"type_import"=>$docInfos['tronc'],"ordre"=>$j),false); 
				}else{
					//construction des tableaux pour les requêtes
					$arrQuery = explode(".", $trtmts[$j]['objDest']);
					//si le tableau contient 3 éléments la valeur est un identifiant à trouver
					//Model_DbTable_Gevu_criteres.Model_DbTable_Gevu_typesxcontroles.id_type_controle
					if(count($arrQuery)>2){
						//vérification si la valeur est renseignée
						if($arr[$j]!=""){
							$className = $arrQuery[1];
							$objDb = new $className();
							//récupère les différentes valeurs
							$arrVal = explode(";", $arr[$j]);
							foreach($arrVal as $ref){
								if($creerModele){
									//création du modèle
									$val = $objDb->ajouter(array($trtmts[$j]["colChamp"]=>$ref));								
								}else{
									//respect du modèle existant
									$val = $objDb->existe(array($trtmts[$j]["colChamp"]=>$ref));
								}
								if(!$val){
									$err .= "La valeur '".$ref."' de la colonne '".$trtmts[$j]['colSource']."' n'est pas une référence.\n";
								}
								$Querys[$arrQuery[0]][]=array($arrQuery[2]=>$val);
							}
						}
					}else{
						$Querys[$arrQuery[0]][]=array($arrQuery[1]=>$arr[$j]);					
					}
				}
				if($err!="")return "Le fichier n'est pas bien formaté.\n".$err;
			}
			//exécution des requêtes
			if(count($Querys)>0){
			    switch ($docInfos['tronc']) {
				    case 'csv_criteres':
			    		//création des valeurs
			    		$json = '{';
			    		foreach($Querys['Models_DbTable_Gevu_criteres'] as $kQ=>$v){
			    			$json .= '"'.key($v).'":"'.str_replace('"','\"',$v[key($v)]).'",';
			    		}
			    		$json=substr($json,0,-1).'}';
			    		$vals = json_decode($json,true); 
				    	//on crée le critère
				    	$objDb = new Models_DbTable_Gevu_criteres();
						$id = $objDb->ajouter($vals,false);
	
						//puis les tables asocciées
						foreach($Querys as $kQ=>$vQ){
							if($kQ!='Models_DbTable_Gevu_criteres'){							
								$objDb = new $kQ();
								foreach($Querys[$kQ] as $v){
									$objDb->ajouter($id,$v[key($v)]);
								}
							}
						}					
					break;
				    
					case 'csv_solutions':
						//vérifie si une solution est définie
						if(array_key_exists('Models_DbTable_Gevu_solutions', $Querys)){
				    		//création des valeurs
				    		$json = '{';
				    		foreach($Querys['Models_DbTable_Gevu_solutions'] as $kQ=>$v){
				    			$json .= '"'.key($v).'":"'.str_replace('"','\"',$v[key($v)]).'",';
				    		}
				    		$json=substr($json,0,-1).'}';
				    		$vals = json_decode($json,true); 
					    	$objDb = new Models_DbTable_Gevu_solutions();
							$id = $objDb->ajouter($vals);
							//puis les tables asocciées
							foreach($Querys as $kQ=>$vQ){
								if($kQ!='Models_DbTable_Gevu_solutions'){							
									$objDb = new $kQ();
									foreach($Querys[$kQ] as $v){
										$objDb->ajouter($id,$v[key($v)]);
									}
								}
							}
						}					
					break;

					case 'csv_solutions_cout':
						//vérifie si une solution est définie
						if(array_key_exists('Models_DbTable_Gevu_couts', $Querys)){
				    		//création des valeurs
				    		$json = '{';
				    		foreach($Querys['Models_DbTable_Gevu_couts'] as $kQ=>$v){
				    			$json .= '"'.key($v).'":"'.str_replace('"','\"',$v[key($v)]).'",';
				    		}
				    		$json=substr($json,0,-1).'}';
				    		$vals = json_decode($json,true); 
					    	$objDb = new Models_DbTable_Gevu_couts();
							$id = $objDb->ajouter($vals);
							//puis les tables asocciées
							foreach($Querys as $kQ=>$vQ){
								if($kQ!='Models_DbTable_Gevu_couts'){							
									$objDb = new $kQ();
									foreach($Querys[$kQ] as $v){
										$objDb->ajouter(array("id_cout"=>$id,key($v)=>$v[key($v)]));
									}
								}
							}
						}					
					break;
					
			    }
				if($err!="")return "Le fichier n'est pas bien formaté.\n".$err;
			}
    	}
    	    
    }

    /**
     * importation d'un fichier de logement 
     *
     * @param int $idDoc = l'identifiant du document qui contient les données
     * @param int $idLieuParent = l'identifiant du lieu (la ville) où les éléments seront crées
     * @param int $idExi = l'identifiant de l'existence exécutant l'importation
     * 
     */
    public function traiteImportLogement($idDoc, $idLieuParent, $idExi){
		
    	//création des models
    	$this->dbDoc = new Models_DbTable_Gevu_docs();
		$this->dbObjExt = new Models_DbTable_Gevu_objetsxexterieurs();
		$this->dbLieu = new Models_DbTable_Gevu_lieux();
		$this->dbI = new Models_DbTable_Gevu_instants();
		$this->dbAnt = new Models_DbTable_Gevu_antennes();
		$this->dbGrp = new Models_DbTable_Gevu_groupes();
		$this->dbBat = new Models_DbTable_Gevu_batiments();
		$this->dbSta = new Models_DbTable_Gevu_stats();
		$this->dbNiv = new Models_DbTable_Gevu_niveaux();
		$this->dbLoc = new Models_DbTable_Gevu_locaux();
		$this->dbEspInt = new Models_DbTable_Gevu_espacesxinterieurs();
		$this->dbPtc = new Models_DbTable_Gevu_partiescommunes();
		$this->dbPcl = new Models_DbTable_Gevu_parcelles();
		$this->dbEspExt = new Models_DbTable_Gevu_espacesxexterieurs();
		$this->dbLog = new Models_DbTable_Gevu_logements();
		$this->dbG = new Models_DbTable_Gevu_geos();
		
		$diag = new GEVU_Diagnostique();
		
    	//création de l'instant
		$c = str_replace("::", "_", __METHOD__); 
		$this->idInst = $this->dbI->ajouter(array("id_exi"=>$idExi,"nom"=>$c));		
		
		$docInfos = $this->dbDoc->findByIdDoc($idDoc);    		
    	
    	//chargement du fichier
		$chaines = file($docInfos['path_source']);
		
		//pour optimiser la récupération des informations
		$arrAnt = array("ref"=>-1);
		$arrGrp = array("ref"=>-1);    			
    	$arrBat = array("ref"=>-1);
    	$arrNiv = array("ref"=>-1);
		$arrPtc = array("ref"=>-1);
		$arrPcl = array("ref"=>-1);
		$arrLoc = array("ref"=>-1);
		
		// parcourt toute les lignes du fichier
		foreach ($chaines as $x => $chaine) {
			$chaine = trim($chaine); 
			$arr = explode(";", $chaine);
			$err = "";
			$nbCol = count($arr);
			/** TODO
			 * il y a plus de colonne que de valeur
			 */
			//if($nbCol!="60") $err .= "Le nombre de colonne de la ligne $x n'est pas bon : $nbCol";
			//on ne traite pas la première ligne
			if($err=="" && $x > 0){
				
   				//récupère l'antenne
    			if($arrAnt["ref"]!=$arr[1]) $arrAnt = $this->dbAnt->getByRef($arr[0], $this->idInst, $idLieuParent);
   				//récupère le groupe
    			if($arrGrp["ref"]!=$arr[2]) $arrGrp = $this->dbGrp->getByRef($arr[1], $this->idInst, $arrAnt["id_lieu"]);    			
    			//récupère le bâtiment
		    	if($arrBat["ref"]!=$arr[3]) $arrBat = $this->dbBat->getByRef($arr[4], $this->idInst, $arrGrp["id_lieu"]);

		    	//on traite les lignes suivant le type de module
			    switch ($arr[15]) {
				    case 'ANTENNE TV':
			    		//recherche la référence
				    	$arrObj = $this->dbObjExt->getByRef($arr[13], $this->idInst, $arrBat["id_lieu"], $arr[15]." - ".$arr[13],array("id_type_objet_ext"=>85));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
					break;
				    case 'BATIMENT ADMINISTRATIF':
			    		//récupère le niveau
			    		/** TODO
			    		 * AJOUTER INFO ASCENSEUR
			    		 * , array("reponse_3"=>$arr[47]
			    		 */ 
			    		if($arrNiv["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrNiv = $this->dbNiv->getByRef($arrBat["id_lieu"]."_".$arr[20], $this->idInst, $arrBat["id_lieu"], "NIVEAU - ".$arr[20], array("reponse_3"=>$arr[47]));				    	
				    	//récupère le local
				    	$arrObj = $this->dbLoc->getByRef($arr[13], $this->idInst, $arrNiv["id_lieu"], $arr[15]." - ".$arr[13], array("activite"=>88));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
					break;
				    case 'CAVE':
			    		//récupère le niveau
			    		if($arrNiv["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrNiv = $this->dbNiv->getByRef($arrBat["id_lieu"]."_".$arr[20], $this->idInst, $arrBat["id_lieu"], "NIVEAU - ".$arr[20], array("reponse_3"=>$arr[47]));				    				    		
			    		//récupère la partie commune
			    		$ref = $arrBat["id_lieu"]."_".$arr[20]."_".$arr[6];
			    		if($arrPtc["ref"]!=$ref) $arrPtc = $this->dbPtc->getByRef($ref, $this->idInst, $arrNiv["id_lieu"], $arr[15]." - ".$arr[20]." - ".$arr[6]);				    	
			    		//recherche la référence
				    	$arrObj = $this->dbEspInt->getByRef($arr[13], $this->idInst, $arrPtc["id_lieu"], $arr[15]." - ".$arr[13],array("id_type_specifique_int"=>75));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
					break;
				    case 'COMMERCE':
			    		//récupère le niveau
			    		if($arrNiv["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrNiv = $this->dbNiv->getByRef($arrBat["id_lieu"]."_".$arr[20], $this->idInst, $arrBat["id_lieu"], "NIVEAU - ".$arr[20], array("reponse_3"=>$arr[47]));				    	
				    	//recherche la référence
				    	$arrObj = $this->dbLoc->getByRef($arr[13], $this->idInst, $arrNiv["id_lieu"], $arr[15]." - ".$arr[13], array("activite"=>86));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
					break;
				    case 'DIVERS':
			    		//récupère le niveau
			    		if($arrNiv["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrNiv = $this->dbNiv->getByRef($arrBat["id_lieu"]."_".$arr[20], $this->idInst, $arrBat["id_lieu"], "NIVEAU - ".$arr[20], array("reponse_3"=>$arr[47]));				    	
				    	//recherche la référence
				    	$arrObj = $this->dbLoc->getByRef($arr[13], $this->idInst, $arrNiv["id_lieu"], $arr[15]." - ".$arr[13]);
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
				    	break;
				    case 'ESPACE PUBLICITAIRE':
			    		//recherche la référence
				    	$arrObj = $this->dbObjExt->getByRef($arr[13], $this->idInst, $arrBat["id_lieu"], $arr[15]." - ".$arr[13],array("id_type_objet_ext"=>89));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
					break;
				    case 'FOYER':
			    		//récupère le niveau
			    		if($arrNiv["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrNiv = $this->dbNiv->getByRef($arrBat["id_lieu"]."_".$arr[20], $this->idInst, $arrBat["id_lieu"], "NIVEAU - ".$arr[20], array("reponse_3"=>$arr[47]));				    	
				    	//recherche la référence
				    	$arrObj = $this->dbLoc->getByRef($arr[13], $this->idInst, $arrNiv["id_lieu"], $arr[15]." - ".$arr[13], array("activite"=>90));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
						break;
				    case 'GARAGE':
			    		//récupère le niveau
			    		if($arrNiv["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrNiv = $this->dbNiv->getByRef($arrBat["id_lieu"]."_".$arr[20], $this->idInst, $arrBat["id_lieu"], "NIVEAU - ".$arr[20], array("reponse_3"=>$arr[47]));				    				    		
			    		//récupère la partie commune
			    		$ref = $arrBat["id_lieu"]."_".$arr[20]."_".$arr[6];
			    		if($arrPtc["ref"]!=$ref) $arrPtc = $this->dbPtc->getByRef($ref, $this->idInst, $arrNiv["id_lieu"], $arr[15]." - ".$arr[20]." - ".$arr[6]);				    	
			    		//recherche la référence
				    	$arrObj = $this->dbEspInt->getByRef($arr[13], $this->idInst, $arrPtc["id_lieu"], $arr[15]." - ".$arr[13],array("id_type_specifique_int"=>91));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
					break;
				    case 'JARDIN':
			    		//récupère la parcelle
			    		if($arrPcl["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrPcl = $this->dbPtc->getByRef($arrGrp["id_lieu"]."_".$arr[20], $this->idInst, $arrGrp["id_lieu"]);				    				    		
			    		//recherche la référence
				    	$arrObj = $this->dbEspExt->getByRef($arr[13], $this->idInst, $arrPcl["id_lieu"], $arr[15]." - ".$arr[13],array("id_type_specifique_ext"=>81));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
					break;
				    case 'LOCAL':
			    		//récupère le niveau
			    		if($arrNiv["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrNiv = $this->dbNiv->getByRef($arrBat["id_lieu"]."_".$arr[20], $this->idInst, $arrBat["id_lieu"], "NIVEAU - ".$arr[20], array("reponse_3"=>$arr[47]));				    	
				    	//recherche la référence
				    	$arrObj = $this->dbLoc->getByRef($arr[13], $this->idInst, $arrNiv["id_lieu"], $arr[15]." - ".$arr[13], array("activite"=>68));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
					break;
				    case 'LOCAL PROFESSIONNEL':
			    		//récupère le niveau
			    		if($arrNiv["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrNiv = $this->dbNiv->getByRef($arrBat["id_lieu"]."_".$arr[20], $this->idInst, $arrBat["id_lieu"], "NIVEAU - ".$arr[20], array("reponse_3"=>$arr[47]));				    	
				    	//recherche la référence
				    	$arrObj = $this->dbLoc->getByRef($arr[13], $this->idInst, $arrNiv["id_lieu"], $arr[15]." - ".$arr[13], array("activite"=>68));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
						break;
				    case 'LOCAL VELO':
			    		//récupère le niveau
			    		if($arrNiv["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrNiv = $this->dbNiv->getByRef($arrBat["id_lieu"]."_".$arr[20], $this->idInst, $arrBat["id_lieu"], "NIVEAU - ".$arr[20], array("reponse_3"=>$arr[47]));				    	
				    	//recherche la référence
				    	$arrObj = $this->dbLoc->getByRef($arr[13], $this->idInst, $arrNiv["id_lieu"], $arr[15]." - ".$arr[13], array("activite"=>74));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
					break;
				    case 'LOGEMENT':
			    		//récupère le niveau
			    		if($arrNiv["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrNiv = $this->dbNiv->getByRef($arrBat["id_lieu"]."_".$arr[20], $this->idInst, $arrBat["id_lieu"], "NIVEAU - ".$arr[20], array("reponse_3"=>$arr[47]));				    	
			    		//recherche la référence
				    	$arrObj = $this->dbLog->getByRef($arr[13], $this->idInst, $arrNiv["id_lieu"], $arr[15]." - ".$arr[13]);
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
					break;
				    case 'PARKING':
			    		//récupère la parcelle
			    		if($arrPcl["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrPcl = $this->dbPtc->getByRef($arrGrp["id_lieu"]."_".$arr[20], $this->idInst, $arrGrp["id_lieu"]);				    				    		
			    		//recherche la référence
				    	$arrObj = $this->dbEspExt->getByRef($arr[13], $this->idInst, $arrPcl["id_lieu"], $arr[15]." - ".$arr[13],array("id_type_specifique_ext"=>48));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
					break;
				    case 'RESIDENCE':
			    		//récupère le niveau
			    		if($arrNiv["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrNiv = $this->dbNiv->getByRef($arrBat["id_lieu"]."_".$arr[20], $this->idInst, $arrBat["id_lieu"], "NIVEAU - ".$arr[20], array("reponse_3"=>$arr[47]));				    	
				    	//recherche la référence
				    	$arrObj = $this->dbLoc->getByRef($arr[13], $this->idInst, $arrNiv["id_lieu"], $arr[15]." - ".$arr[13], array("activite"=>92));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
						break;
					break;
				    case 'SECHOIR ou 2ème CAVE':
			    		//récupère le niveau
			    		if($arrNiv["ref"]!=$arrBat["id_lieu"]."_".$arr[20]) $arrNiv = $this->dbNiv->getByRef($arrBat["id_lieu"]."_".$arr[20], $this->idInst, $arrBat["id_lieu"], "NIVEAU - ".$arr[20], array("reponse_3"=>$arr[47]));				    				    		
			    		//récupère la partie commune
			    		$ref = $arrBat["id_lieu"]."_".$arr[20]."_".$arr[6];
			    		if($arrPtc["ref"]!=$ref) $arrPtc = $this->dbPtc->getByRef($ref, $this->idInst, $arrNiv["id_lieu"], $arr[15]." - ".$arr[20]." - ".$arr[6]);				    	
			    		//recherche la référence
				    	$arrObj = $this->dbEspInt->getByRef($arr[13], $this->idInst, $arrPtc["id_lieu"], $arr[15]." - ".$arr[13],array("id_type_specifique_int"=>93));
			    		//ajoute la stat
			    		$this->dbSta->ajouterByImport($arr, $arrObj['id_lieu'], $this->idInst);			    		
					break;
			    }
			    //mise à jour de l'adresse
			    $this->dbG->editByLieu($arrObj['id_lieu'],array("adresse"=>$arr[7]." ".$arr[9], "codepostal"=>$arr[10], "ville"=>$arr[11], "pays"=>"France"));
			    
			    				
			}
			if($err!="")return "Le fichier n'est pas bien formaté.\n".$err;
    	}
    	    
    }    


    /**
     * importation d'un fichier pour ajouter des adresses
     *
     * @param string $path = adresse du fichier
     * 
     */
    function importGeos($path, $findBy="Code_Logement"){
		
    	$chaines = file($path);

    	$inArr = array("40 RUE EDMOND CASAUX","63 RUE DES HETRES","65 AVENUE PAUL VERLAINE","65 RUE D'IENA","8 AVENUE VLADIMIR KOMAROV");
    	
		$this->dbSta = new Models_DbTable_Gevu_stats();
		$this->dbG = new Models_DbTable_Gevu_geos();
    	
		// parcourt toute les lignes du fichier
		foreach ($chaines as $x => $chaine) {
			$chaine = trim($chaine); 
			$arr = explode(";", $chaine);
			if($x > 0){
				$var = array_search($arr[0], $inArr);
				if (in_array($arr[0], $inArr)) {
									//récupère l'identifiant de lieu
					if($findBy=="Code_Logement"){
						$rs = $this->dbSta->findIdLieuByCode_Logement($arr[13]);
					    //mise à jour de l'adresse
					    $this->dbG->editByLieu($rs[0]['id_lieu'],array("adresse"=>$arr[7]." ".$arr[9], "codepostal"=>$arr[10], "ville"=>$arr[11], "pays"=>"France"));
					}
					if($findBy=="adresse"){
						$rs = $this->dbG->findIdsLieuxByAdresse($arr[0], $arr[1], $arr[2], $arr[3]);
					    //mise à jour de l'adresse
					    if(count($rs)>0){
						    $this->dbG->editByIdsLieux($rs[0]['ids'],array("lat"=>$arr[4], "lng"=>$arr[5]));
					    }
					}
				}
				
			}
		}
	}    
    
/** merci à http://j-reaux.developpez.com/tutoriel/php/fonctions-redimensionner-image/
// 	---------------------------------------------------------------
// fonction de REDIMENSIONNEMENT physique "PROPORTIONNEL" et Enregistrement
// 	---------------------------------------------------------------
// retourne : 1 (vrai) si le redimensionnement et l enregistrement ont bien eu lieu, sinon rien (false)
// 	---------------------------------------------------------------
// La FONCTION : fctredimimage ($W_max, $H_max, $rep_Dst, $img_Dst, $rep_Src, $img_Src)
// Les parametres :
// - $W_max : LARGEUR maxi finale --> ou 0
// - $H_max : HAUTEUR maxi finale --> ou 0
// - $rep_Dst : repertoire de l image de Destination (deprotégé) --> ou '' (meme repertoire)
// - $img_Dst : NOM de l image de Destination --> ou '' (meme nom que l image Source)
// - $rep_Src : repertoire de l image Source (deprotégé)
// - $img_Src : NOM de l image Source
// 	---------------------------------------------------------------
// 3 options :
// A- si $W_max != 0 et $H_max != 0 : a LARGEUR maxi ET HAUTEUR maxi fixes
// B- si $H_max != 0 et $W_max == 0 : image finale a HAUTEUR maxi fixe (largeur auto)
// C- si $W_max == 0 et $H_max != 0 : image finale a LARGEUR maxi fixe (hauteur auto)
// Si l'image Source est plus petite que les dimensions indiquees : PAS de redimensionnement.
// 	---------------------------------------------------------------
// $rep_Dst : il faut s'assurer que les droits en écriture ont été donnés au dossier (chmod)
// - si $rep_Dst = ''   : $rep_Dst = $rep_Src (meme repertoire que l image Source)
// - si $img_Dst = '' : $img_Dst = $img_Src (meme nom que l image Source)
// - si $rep_Dst='' ET $img_Dst='' : on ecrase (remplace) l image source !
// 	---------------------------------------------------------------
// NB : $img_Dst et $img_Src doivent avoir la meme extension (meme type mime) !
// Extensions acceptees (traitees ici) : .jpg , .jpeg , .png
// Pour ajouter d autres extensions : voir la bibliotheque GD ou ImageMagick
// (GD) NE fonctionne PAS avec les GIF ANIMES ou a fond transparent !
// 	---------------------------------------------------------------
// UTILISATION (exemple) :
// $redimOK = fctredimimage(120,80,'reppicto/','monpicto.jpg','repimage/','monimage.jpg');
// if ($redimOK == 1) { echo 'Redimensionnement OK !';  }
*/ 	
function fctredimimage($W_max, $H_max, $rep_Dst, $img_Dst, $rep_Src, $img_Src) {
 // ------------------------------------------------------------------
 $condition = 0;
 // Si certains parametres ont pour valeur '' :
   if ($rep_Dst == '') { $rep_Dst = $rep_Src; } // (meme repertoire)
   if ($img_Dst == '') { $img_Dst = $img_Src; } // (meme nom)
 // ------------------------------------------------------------------
 // si le fichier existe dans le répertoire, on continue...
 if (file_exists($rep_Src.$img_Src) && ($W_max!=0 || $H_max!=0)) { 
   // ----------------------------------------------------------------
   // extensions acceptees : 
   $ExtfichierOK = '" jpg jpeg png"'; // (l espace avant jpg est important)
   // extension fichier Source
   $tabimage = explode('.',$img_Src);
   $extension = $tabimage[sizeof($tabimage)-1]; // dernier element
   $extension = strtolower($extension); // on met en minuscule
   // ----------------------------------------------------------------
   // extension OK ? on continue ...
   if (strpos($ExtfichierOK,$extension) != '') {
      // -------------------------------------------------------------
      // recuperation des dimensions de l image Src
      $img_size = getimagesize($rep_Src.$img_Src);
      $W_Src = $img_size[0]; // largeur
      $H_Src = $img_size[1]; // hauteur
      // -------------------------------------------------------------
      // condition de redimensionnement et dimensions de l image finale
      // -------------------------------------------------------------
      // A- LARGEUR ET HAUTEUR maxi fixes
      if ($W_max != 0 && $H_max != 0) {
         $ratiox = $W_Src / $W_max; // ratio en largeur
         $ratioy = $H_Src / $H_max; // ratio en hauteur
         $ratio = max($ratiox,$ratioy); // le plus grand
         $W = $W_Src/$ratio;
         $H = $H_Src/$ratio;   
         $condition = ($W_Src>$W) || ($W_Src>$H); // 1 si vrai (true)
      }      // -------------------------------------------------------------
      // B- HAUTEUR maxi fixe
      if ($W_max == 0 && $H_max != 0) {
         $H = $H_max;
         $W = $H * ($W_Src / $H_Src);
         $condition = $H_Src > $H_max; // 1 si vrai (true)
      }
      // -------------------------------------------------------------
      // C- LARGEUR maxi fixe
      if ($W_max != 0 && $H_max == 0) {
         $W = $W_max;
         $H = $W * ($H_Src / $W_Src);         
         $condition = $W_Src > $W_max; // 1 si vrai (true)
      }
      // -------------------------------------------------------------
      // on REDIMENSIONNE si la condition est vraie
      // -------------------------------------------------------------
      // Par defaut : 
	  // Si l'image Source est plus petite que les dimensions indiquees :
	  // PAS de redimensionnement.
	  // Mais on peut "forcer" le redimensionnement en ajoutant ici :
	  // $condition = 1;
      if ($condition == 1) {
         // ----------------------------------------------------------
         // creation de la ressource-image "Src" en fonction de l extension
         switch($extension) {
         case 'jpg':
         case 'jpeg':
           $Ress_Src = imagecreatefromjpeg($rep_Src.$img_Src);
           break;
         case 'png':
           $Ress_Src = imagecreatefrompng($rep_Src.$img_Src);
           break;
         }
         // ----------------------------------------------------------
         // creation d une ressource-image "Dst" aux dimensions finales
         // fond noir (par defaut)
         switch($extension) {
         case 'jpg':
         case 'jpeg':
           $Ress_Dst = imagecreatetruecolor($W,$H);
           break;
         case 'png':
           $Ress_Dst = imagecreatetruecolor($W,$H);
           // fond transparent (pour les png avec transparence)
           imagesavealpha($Ress_Dst, true);
           $trans_color = imagecolorallocatealpha($Ress_Dst, 0, 0, 0, 127);
           imagefill($Ress_Dst, 0, 0, $trans_color);
           break;
         }
         // ----------------------------------------------------------
         // REDIMENSIONNEMENT (copie, redimensionne, re-echantillonne)
         imagecopyresampled($Ress_Dst, $Ress_Src, 0, 0, 0, 0, $W, $H, $W_Src, $H_Src); 
         // ----------------------------------------------------------
         // ENREGISTREMENT dans le repertoire (avec la fonction appropriee)
         switch ($extension) { 
         case 'jpg':
         case 'jpeg':
           imagejpeg ($Ress_Dst, $rep_Dst.$img_Dst);
           break;
         case 'png':
           imagepng ($Ress_Dst, $rep_Dst.$img_Dst);
           break;
         }
         // ----------------------------------------------------------
         // liberation des ressources-image
         imagedestroy ($Ress_Src);
         imagedestroy ($Ress_Dst);
      }
      // -------------------------------------------------------------
   }
 }
// 	---------------------------------------------------------------
 // si le fichier a bien ete cree
 if ($condition == 1 && file_exists($rep_Dst.$img_Dst)) { return true; }
 else { return false; }
}
// retourne : 1 (vrai) si le redimensionnement et l enregistrement ont bien eu lieu, sinon rien (false)
// 	---------------------------------------------------------------    


// 	---------------------------------------------------------------
// fonction d AJOUT DE TEXTE a une image et Enregistrement
// 	---------------------------------------------------------------
// retourne : 1 (vrai) si l ajout de texte a bien ete ajoute, sinon rien (false)
// 	---------------------------------------------------------------
// La FONCTION : fcttexteimage ($chaine, $rep_Dst, $img_Dst, $rep_Src, $img_Src, $position)
// Les parametres :
// - $chaine : TEXTE a ajouter
// - $rep_Dst : repertoire de l image de Destination (deprotégé) --> ou '' (meme repertoire)
// - $img_Dst : NOM de l image de Destination --> ou '' (meme nom que l image Source)
// - $rep_Src : repertoire de l image Source (deprotégé)
// - $img_Src : NOM de l image Source
// - $position : position du texte sur l image
// 	---------------------------------------------------------------
// ATTENTION : si le texte est TROP long, il risque d etre tronque !
// 	---------------------------------------------------------------
// Position du texte sur l image (valeurs possibles) :
// $position = 'HG' --> en Haut a Gauche (valeur par defaut)
// $position = 'HD' --> en Haut a Droite
// $position = 'HC' --> en Haut au Centre
// $position = 'BG' --> en Bas a Gauche
// $position = 'BD' --> en Bas a Droite
// $position = 'BC' --> en Bas au Centre
// 	---------------------------------------------------------------
// $rep_Dst : il faut s'assurer que les droits en écriture ont été donnés au dossier (chmod)
// - si $rep_Dst = ''   : $rep_Dst = $rep_Src (meme repertoire que l image Source)
// - si $img_Dst = '' : $img_Dst = $img_Src (meme nom que l image Source)
// - si $rep_Dst='' ET $img_Dst='' : on ecrase (remplace) l image source !
// 	---------------------------------------------------------------
// NB : $img_Dst et $img_Src doivent avoir la meme extension (meme type mime) !
// Extensions acceptees (traitees ici) : .jpg , .jpeg , .png
// Pour ajouter d autres extensions : voir la bibliotheque GD ou ImageMagick
// (GD) NE fonctionne PAS avec les GIF ANIMES ou a fond transparent !
// 	---------------------------------------------------------------
// UTILISATION (exemple copyright, ou legende de l image) :
// $texteOK = fcttexteimage('copyright : MOI','reppicto/','monpicto.jpg','repimage/','monimage.jpg','BG');
// if ($texteOK == 1) { echo 'Ajout du texte OK !';  }
// 	---------------------------------------------------------------
function fcttexteimage($chaine, $rep_Dst, $img_Dst, $rep_Src, $img_Src, $position) {
 $condition = 0;
 // ------------------------------------------------------------------
   $position = strtoupper($position); // on met en majuscule (par defaut)
 // Si certains parametres ont pour valeur '' :
   if ($rep_Dst == '') { $rep_Dst = $rep_Src; } // (meme repertoire)
   if ($img_Dst == '') { $img_Dst = $img_Src; } // (meme nom)
   if ($position == '') { $position = 'BG'; } // en Bas A Gauche (valeur par defaut)
 // ------------------------------------------------------------------
 // si le fichier existe dans le répertoire, on continue...
 if (file_exists($rep_Src.$img_Src) && $chaine!='') { 
   // ----------------------------------------------------------------
   // extensions acceptees : 
   $ExtfichierOK = '" jpg jpeg png"'; // (l espace avant jpg est important)
   // extension fichier Source
   $tabimage = explode('.',$img_Src);
   $extension = $tabimage[sizeof($tabimage)-1]; // dernier element
   $extension = strtolower($extension); // on met en minuscule
   // ----------------------------------------------------------------
   // extension OK ? on continue ...
   if (strpos($ExtfichierOK,$extension) != '') {
      // -------------------------------------------------------------
      // recuperation des dimensions de l image Src
      $img_size = getimagesize($rep_Src.$img_Src);
      $W_Src = $img_size[0]; // largeur
      $H_Src = $img_size[1]; // hauteur
      // -------------------------------------------------------------
      // creation de la ressource-image "Dst" en fonction de l extension
      // (a partir de l image source)
      switch($extension) {
      case 'jpg':
      case 'jpeg':
        $Ress_Dst = imagecreatefromjpeg($rep_Src.$img_Src);
        break;
      case 'png':
        $Ress_Dst = imagecreatefrompng($rep_Src.$img_Src);
        break;
      }
      // -------------------------------------------------------------
      // creation de l image TEXTE
      // -------------------------------------------------------------
      // dimension de l image "Txt" en fonction :
      // - de la longueur du texte a afficher
      // - des dimensions des caracteres (7x15 pixels par caractere)
      // ATTENTION : si le texte est TROP long, il risque d etre tronque !
      $W = strlen($chaine) * 7;
      if ($W > $W_Src) { $W = $W_Src; }
      $H = 15; // 15 pixels de haut (par defaut)
      // -------------------------------------------------------------
      // creation de la ressource-image "Txt" (en fonction de l extension)
      switch($extension) {
      case 'jpg':
      case 'jpeg':
      case 'png':
        $Ress_Txt = imagecreatetruecolor($W,$H);
        // Couleur du Fond : blanc
        $blanc = imagecolorallocate ($Ress_Txt, 255, 255, 255);
        imagefill ($Ress_Txt, 0, 0, $blanc);
        // Couleur du Texte : noir
        $textcolor = imagecolorallocate($Ress_Txt, 0, 0, 0);
        // Ecriture du TEXTE
        imagestring($Ress_Txt, 3, 0, 0, $chaine, $textcolor);
        break;
      }
      // -------------------------------------------------------------
      // positionnement du TEXTE sur l image
      // -------------------------------------------------------------
      if ($position == 'HG') {
         $X_Dest = 0;
         $Y_Dest = 0;
      }
      if ($position == 'HD') {
         $X_Dest = $W_Src - $W;
         $Y_Dest = 0;
      }
      if ($position == 'HC') {
         $X_Dest = ($W_Src - $W)/2;
         $Y_Dest = 0;
      }
      if ($position == 'BG') {
         $X_Dest = 0;
         $Y_Dest = $H_Src - $H;
      }
      if ($position == 'BD') {
         $X_Dest = $W_Src - $W;
         $Y_Dest = $H_Src - $H;
      }
      if ($position == 'BC') {
         $X_Dest = ($W_Src - $W)/2;
         $Y_Dest = $H_Src - $H;
      }
      // -------------------------------------------------------------
      // copie par fusion de l image "Txt" sur l image "Dst"
      // (avec transparence de 50%)
      // -------------------------------------------------------------
      imagecopymerge ($Ress_Dst, $Ress_Txt, $X_Dest, $Y_Dest, 0, 0, $W, $H, 50);
      // ----------------------------------------------------------
      // ENREGISTREMENT dans le repertoire (en fonction de l extension)
      switch ($extension) { 
      case 'jpg':
      case 'jpeg':
        imagejpeg ($Ress_Dst, $rep_Dst.$img_Dst);
        $condition = 1;
        break;
      case 'png':
        imagepng ($Ress_Dst, $rep_Dst.$img_Dst);
        $condition = 1;
        break;
      }
      // -------------------------------------------------------------
      // liberation des ressources-image
      imagedestroy ($Ress_Txt);
      imagedestroy ($Ress_Dst);
      // -------------------------------------------------------------
   }
 }
// 	---------------------------------------------------------------
 // si le fichier a bien ete cree
 if ($condition == 1 && file_exists($rep_Dst.$img_Dst)) { return true; }
 else { return false; }
}
// retourne : 1 (vrai) si l ajout de texte a bien ete ajoute, sinon rien (false)
// 
}
?>