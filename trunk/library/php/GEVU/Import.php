<?php
class GEVU_Import{
	
	var $dbCr;
	
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
					$scene = '[{"idCritNU":"-1","idCritSE":"<node idCrit=\"-1\" ref=\"critères\" isBranch=\"true\">\n';									
				}
				//concaténation des paramètres
				$scene .= $this->addSceneParam($question);
			}
			//mise à jour des paramètres
			$dbSc->edit($idSc, array('params'=>$scene.'</node>"}]','xml'=>$scene.'</node>'));
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
			if($data['objName']=="model_rapport"){
				$rep = SEP_PATH.'data'.SEP_PATH.'rapports'.SEP_PATH.'models';    
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
					$new_name = uniqid().$extention;
			        $path = ROOT_PATH.$rep.SEP_PATH.$new_name;					
			        $url = WEB_ROOT.$rep.SEP_PATH.$new_name;

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

		$doc = new Models_DbTable_Gevu_docs();
		$idDoc = $doc->ajouter($dataDoc,false);

		$ins = new Models_DbTable_Gevu_instants();
		$nom = get_class();
		$idIns = $ins->ajouter(array("nom"=>$nom."_addDoc","id_exi"=>$data['idExi']),false);
		
		$exidoc = new Models_DbTable_Gevu_instantsxdocs();
		$exidoc->ajouter(array("id_doc"=>$idDoc,"id_instant"=>$idIns),false);
		
		if($data['objName']=='img_solus'){
			$doc_obj = new Models_DbTable_Gevu_docsxsolutions();
			$doc_obj->ajouter(array("id_doc"=>$idDoc,"id_instant"=>$idIns,"id_solution"=>$data['objId']),false);		
		}
		if($data['objName']=='img_produit'){
			$doc_obj = new Models_DbTable_Gevu_docsxproduits();
			$doc_obj->ajouter(array("id_doc"=>$idDoc,"id_instant"=>$idIns,"id_produit"=>$data['objId']),false);		
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
    
}
?>