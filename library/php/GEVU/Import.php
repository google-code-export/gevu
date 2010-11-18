<?php
class GEVU_Import{
    /**
    * @param  string $name
    * @param  string $greeting
    * @return string
    */
    public function critere($data)
    {
    	//Identifiant	Critères	Réglementaire	Souhaitable	Type de droit	Travail	ERP_IOP	Logement	Voirie	ERP_IOP existant	Modalité particulière	motrice	auditive	visuelle	cognitive	Affirmations	Handicateur moteur	Handicateur auditif	Handicateur visuel	Handicateur cognitif																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																											
    
    	return $greeting . ', ' . $name;
    }

	public function addDoc($data){

		try {
		    
			$adapter = new Zend_File_Transfer_Adapter_Http();
		        //echo ROOT_PATH.'/data/upload';
		    $adapter->setDestination(ROOT_PATH.'/data/upload');
		               		
			if (!$adapter->receive()) {
				$messages = $adapter->getMessages();
				echo implode("\n", $messages);
		      }else{
				// Retourne toutes les informations connues sur le fichier
				$files = $adapter->getFileInfo();
				foreach ($files as $file => $info) {
					// Les validateurs sont-ils OK ?
					if (!$adapter->isValid($file)) {
						print "Désolé mais $file ne correspond à ce que nous attendons";
						continue;
					}
			        $url = str_replace(ROOT_PATH,WEB_ROOT,$adapter->getFileName());
			        $path = $adapter->getFileName();
			        $dataDoc = array(
			    		"url"=>$url,"titre"=>$info["name"],"content_type"=>"text/csv"
			    		,"path_source"=>$path
			    		,"tronc"=>$data['objName']
			    		);			        
					$this->saveDoc($data, $dataDoc);
					$this->traiteDoc($idDoc, $path);
					print_r($info);					
				}
		      }
		      
		}catch (Zend_Exception $e) {
			// Appeler Zend_Loader::loadClass() sur une classe non-existante
          	//entrainera la levée d'une exception dans Zend_Loader
          	echo "Récupère exception: " . get_class($e) . "\n";
          	echo "Message: " . $e->getMessage() . "\n";
          	// puis tout le code nécessaire pour récupérer l'erreur
		}
        
    }
    
    public function saveDoc($data, $dataDoc){

		$doc = new Model_DbTable_Gevu_docs();
		$idDoc = $doc->ajouter($dataDoc,false);

		$ins = new Model_DbTable_Gevu_instants();
		$nom = get_class();
		$idIns = $ins->ajouter(array("nom"=>$nom."_addDoc","id_exi"=>$data['idExi']),false);
		
		$exidoc = new Model_DbTable_Gevu_instantsxdocs();
		$exidoc->ajouter(array("id_doc"=>$idDoc,"id_instant"=>$idIns),false);

    	
    }

    public function traiteDoc($idDoc, $creerModele=false){

		$doc = new Model_DbTable_Gevu_docs();
		$docInfos = $doc->findById_doc($idDoc);    		
    	
    	//chargement du fichier
		$chaines = file($docInfos['path_source']);
		
		//chargement de la description du traitement 
		$pimp = new Model_DbTable_Gevu_paramximport();
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
			    		foreach($Querys['Model_DbTable_Gevu_criteres'] as $kQ=>$v){
			    			$json .= '"'.key($v).'":"'.str_replace('"','\"',$v[key($v)]).'",';
			    		}
			    		$json=substr($json,0,-1).'}';
			    		$vals = json_decode($json,true); 
				    	//on crée le critère
				    	$objDb = new Model_DbTable_Gevu_criteres();
						$id = $objDb->ajouter($vals,false);
	
						//puis les tables asocciées
						foreach($Querys as $kQ=>$vQ){
							if($kQ!='Model_DbTable_Gevu_criteres'){							
								$objDb = new $kQ();
								foreach($Querys[$kQ] as $v){
									$objDb->ajouter($id,$v[key($v)]);
								}
							}
						}					
					break;
				    
					case 'csv_solutions':
						//vérifie si une solution est définie
						if(array_key_exists('Model_DbTable_Gevu_solutions', $Querys)){
				    		//création des valeurs
				    		$json = '{';
				    		foreach($Querys['Model_DbTable_Gevu_solutions'] as $kQ=>$v){
				    			$json .= '"'.key($v).'":"'.str_replace('"','\"',$v[key($v)]).'",';
				    		}
				    		$json=substr($json,0,-1).'}';
				    		$vals = json_decode($json,true); 
					    	$objDb = new Model_DbTable_Gevu_solutions();
							$id = $objDb->ajouter($vals);
							//puis les tables asocciées
							foreach($Querys as $kQ=>$vQ){
								if($kQ!='Model_DbTable_Gevu_solutions'){							
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
						if(array_key_exists('Model_DbTable_Gevu_couts', $Querys)){
				    		//création des valeurs
				    		$json = '{';
				    		foreach($Querys['Model_DbTable_Gevu_couts'] as $kQ=>$v){
				    			$json .= '"'.key($v).'":"'.str_replace('"','\"',$v[key($v)]).'",';
				    		}
				    		$json=substr($json,0,-1).'}';
				    		$vals = json_decode($json,true); 
					    	$objDb = new Model_DbTable_Gevu_couts();
							$id = $objDb->ajouter($vals);
							//puis les tables asocciées
							foreach($Querys as $kQ=>$vQ){
								if($kQ!='Model_DbTable_Gevu_couts'){							
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