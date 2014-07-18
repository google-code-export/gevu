<?php
class GEVU_Rapport extends GEVU_Site{

	var $arrMC;
	var $odf;
	var $pathDebug = ROOT_PATH;		
	//var $pathDebug;
	var $arrCoutSolus;
	var $arrAriane;
	var $ariane;
	var $idExi;
	var $arrExi;
	var $idLieu;
	var $arrEtatLieux;
		
	/**
	 * récupère la liste des diagnostics pour un lieu
	 *
	 * @param int $idLieu
	 * @param string $idBase
	 *
	 * @return array
	 */
	public function getRapportFait($idLieu, $idBase){
		
		$this->getDb($idBase);
		
		$dbR = new Models_DbTable_Gevu_rapports($this->db);
		return $dbR->findByIdLieu($idLieu);
		
	}
	
	/**
	 * ajoute les solutions par defaut
	 *
	 * @param int $idLieu
	 * @param string $idBase
	 *
	 * @return string 
	 */
	public function setSolusDefaut($idLieu, $idExi, $idBase){
		
		$this->getDb($idBase);
		
		$dbDS = new Models_DbTable_Gevu_diagnosticsxsolutions($this->db);
		$dbS = new Models_DbTable_Gevu_solutionsxcriteres();
		
		$arrDiags = $dbDS->findByIdLieuAFaire($idLieu);
		foreach ($arrDiags as $diag) {
			//vérifie si le côut est déjà calculé
			if(!isset($this->arrCoutSolus[$diag['id_critere']])){
				//récupère les solutions
				$arrCrit = $dbS->findByIdCritere($diag['id_critere']);
				//calcule le cout par defaut
				$this->arrCoutSolus[$diag['id_critere']]=$this->calculDefautCout($arrCrit[0]);				
			}
			$dbDS->ajouterDiags(explode(",", $diag['diags']), $this->arrCoutSolus[$diag['id_critere']], $idExi);	
		}
		return "OK";
		
	}

    /**
     * Calcul le cout par defaut d'une solution
     *
     * @param array 	$arrCS
     *
     * @array
     */
    public function calculDefautCout($arrCS){
    	
		//calcul le cout par defaut
		$coutSymbolique = 1;    	
		$coutTotal = 0;    	
		if($arrCS['id_solution']){
			$vn["id_solution"] = $arrCS["id_solution"];				
			$vn["id_cout"] = $arrCS["Sid_cout"];
			if($arrCS["Sunite"]){
				$vn["unite"] = $coutSymbolique;				
				$coutTotal += $arrCS["Sunite"]*$coutSymbolique;
			}				
			if($arrCS["Spose"]){
				$vn["pose"] = $coutSymbolique;				
				$coutTotal += $arrCS["Spose"]*$coutSymbolique;					
			}
			if($arrCS["Smetre_lineaire"]){
				$vn["metre_lineaire"] = $coutSymbolique;				
				$coutTotal += $arrCS["Smetre_lineaire"]*$coutSymbolique;					
			}
			if($arrCS["Smetre_carre"]){
				$vn["metre_carre"] = $coutSymbolique;				
				$coutTotal += $arrCS["Smetre_carre"]*$coutSymbolique;					
			}
			if($arrCS["Sachat"]){
				$vn["achat"] = $coutSymbolique;				
				$coutTotal += $arrCS["Sachat"]*$coutSymbolique;					
			}
			$vn["cout"] = $coutTotal;				
		}
		if($arrCS["id_produit"]){
			$vn["id_produit"] = $arrCS["id_produit"];				
			$vn["id_cout"] = $arrCS["Sid_cout"];
			if($arrCS["unite"]){
				$vn["unite"] = $coutSymbolique;				
				$coutTotal += $arrCS["unite"]*$coutSymbolique;
			}				
			if($arrCS["pose"]){
				$vn["pose"] = $coutSymbolique;				
				$coutTotal += $arrCS["pose"]*$coutSymbolique;					
			}
			if($arrCS["metre_lineaire"]){
				$vn["metre_lineaire"] = $coutSymbolique;				
				$coutTotal += $arrCS["metre_lineaire"]*$coutSymbolique;					
			}
			if($arrCS["metre_carre"]){
				$vn["metre_carre"] = $coutSymbolique;				
				$coutTotal += $arrCS["metre_carre"]*$coutSymbolique;					
			}
			if($arrCS["achat"]){
				$vn["achat"] = $coutSymbolique;				
				$coutTotal += $arrCS["achat"]*$coutSymbolique;					
			}
			$vn["cout"] = $coutTotal;								
		}
		return $vn;   	
    }
    	
	/**
	 * création d'un rapport pour un lieu et un modèle
	 *
	 * @param int $idModele
	 * @param int $idLieu
	 * @param int $idExi
	 * @param string $idBase
	 *
	 * @return array
	 */
	public function creaRapport($idModele, $idLieu, $idExi, $idBase){
	
		try{
		set_time_limit(30000);		
		$this->bTrace = false;
		$this->temps_debut = microtime(true);
		$this->idLieu = $idLieu;
		$this->idBase = $idBase;
				
		//initialisation des objets	
		$this->getDb($idBase);
		$this->dbE = new Models_DbTable_Gevu_exis();
		$this->dbDoc = new Models_DbTable_Gevu_docs($this->db);
		$this->dbMC = new Models_DbTable_Gevu_motsclefs($this->db);
		$this->dbL = new Models_DbTable_Gevu_lieux($this->db);
		$this->dbCnt = new Models_DbTable_Gevu_contacts($this->db);
		$this->dbDS = new Models_DbTable_Gevu_diagnosticsxsolutions($this->db);
		$this->dbProb = new Models_DbTable_Gevu_problemes($this->db);
		$this->dbInst = new Models_DbTable_Gevu_instants($this->db);
		$this->dbRapport = new Models_DbTable_Gevu_rapports($this->db);
		$this->dbDocRapport = new Models_DbTable_Gevu_docsxrapports($this->db);
		$this->oDiag = new GEVU_Diagnostique($this->db);
		$this->dbEtab = new Models_DbTable_Gevu_etablissements($this->db);
				
		//récupère l'auteur
		$this->idExi = $idExi; 
		$this->arrExi = $this->dbE->findById_exi($idExi);
				
		//récupère le Modele
		$rm = $this->dbDoc->findByIdDoc($idModele);
		//vérifie le type de rapport
		$typeRapport = $rm['branche'];
		
		//récupère les mots clefs
		$this->arrMC = $this->dbMC->getAll();
		
		//charge le modèle
		//pour le debugage
		if($this->pathDebug)$ps = str_replace("/home/gevu/www", $this->pathDebug, $rm['path_source']);
		else $ps = $rm['path_source'];
		$this->trace($ps);
		$config = array(
    	'ZIP_PROXY' => 'PclZipProxy',
    	'DELIMITER_LEFT' => '{',
    	'DELIMITER_RIGHT' => '}',
		'PATH_TO_TMP' => ROOT_PATH.'/tmp'
   		);
		$this->odf = new odf($ps, $config);		
		/*dégugage du contenu xml
		header("Content-Type:text/xml");
		echo $this->odf->getContentXml();
		return;
		*/
		
		//récupération de l'état des lieux
		$this->arrEtatLieux = $this->oDiag->getNodeRelatedData($idLieu, $idExi, $idBase);
		
		//récupère le fil d'ariane du lieu
		$this->arrAriane = $this->arrEtatLieux["ariane"];
		$this->ariane = "";
		foreach ($this->arrAriane as $l) {
			$this->ariane .= $l['lib']." - ";			
		}
		$this->trace($this->ariane);
		
		/*
		[{"id_type_doc": 8,"lib": "Rapport bâtiment"}, {"id_type_doc": 9,"lib": "Rapport espace"}, {"id_type_doc": 10,"lib": "Rapport niveau"}
		, {"id_type_doc": 11,"lib": "Rapport objet"}, {"id_type_doc": 12,"lib": "Fiche logement"}, {"id_type_doc": 13,"lib": "Rapport logement"}];
		*/
		switch ($typeRapport) {
			case 8:
				$this->creaRapportBat();
				break;			
			case 9:
				$this->creaRapportEspace();
				break;			
			case 10:
				$this->creaRapportNiv();
				break;			
			case 11:
				$this->creaRapportEspace();
				break;			
			case 12:
				$this->creaFicheLog();
				break;			
			case 13:
				$this->creaRapportLog();
				break;			
			default:
				$this->creaRapportDefaut();
				break;
		}
				
		//on enregistre le fichier
		$idInst = $this->dbInst->ajouter(array("id_exi"=>$idExi,"nom"=>"Création rapport"));
		
		$nomFic = preg_replace('/[^a-zA-Z0-9-_\.]/','', $nomEtab);
		$nomFic = $idModele."_".$idLieu."_".$nomFic."_".$idInst.".odt";
		//copie le fichier dans le répertoire data
		$newfile = ROOT_PATH."/data/rapports/documents/".$nomFic;
		copy($this->odf->tmpfile, $newfile);
		
		//on enregistre le doc dans la base
		$idDoc = $this->dbDoc->ajouter(array("id_instant"=>$idInst,"url"=>WEB_ROOT."/data/rapports/documents/".$nomFic,"titre"=>$nomFic,"path_source"=>$newfile,"content_type"=>"application/vnd.oasis.opendocument.text"));
		$idRap = $this->dbRapport->ajouter(array("id_lieu"=>$idLieu, "id_exi"=>$idExi, "lib"=>$nomFic));
		$this->dbDocRapport->ajouter(array("id_doc"=>$idDoc,"id_rapport"=>$idRap));
		
		//on propose de télécharger le rapport
		$this->odf->exportAsAttachedFile($nomFic);
		
		$this->trace("FIN");
		
		}catch (SegmentException  $e) {
			echo "Récupère exception: " . get_class($e) . "\n";
			echo "Message: " . $e->getMessage() . "\n";
			var_dump($e);
		}		
		
	}
	
	function creaRapportDefaut(){

		$this->odf->setVars('commune', $this->ariane);
		$nomEtab = $this->arrAriane[0]['parLib'];
		$this->odf->setVars('etablissement', $nomEtab);
		
		//récupération des images
		$arrDocs = $this->arrEtatLieux["Models_DbTable_Gevu_docsxlieux"];
		//ajout des images au modèle
		$this->odf = $this->setImage($this->odf, 'img_etab', $arrDocs);
		
		//ajout des coordonnée de l'auteur
		$this->odf->setVars('redacteur', $arrExi['nom'].' '.$arrExi['mail']);
		$today = strftime( "%A %d %B %Y" , time());
		$this->odf->setVars('date_redaction', $today);
		
		//récupère la dernière campagne de diagnostique
		$dbDiag = new Models_DbTable_Gevu_diagnostics($this->db);		
		$arrLastDiag = $dbDiag->findLastDiagForLieu($this->idLieu);
		
		$this->odf->setVars('diagnostiqueur', $arrLastDiag[0]['nom']);
		//information pas présente actuellement dans la base
		$this->odf->setVars('structure_diagnostiqueur', '???');
		$this->odf->setVars('date_passage', $arrLastDiag[0]['maintenant']);
		
		$arrDiag = $this->arrEtatLieux["___diagnostics"]["diag"];
		$this->trace("Etat des lieux Etablissement");
		
		$this->odf->setImage('img_niv_reg_moteur', '../images/'.$arrDiag['handicateur1']['moteur'][0].'.png');
		$this->odf->setImage('img_niv_reg_audio', '../images/'.$arrDiag['handicateur1']['auditif'][0].'.png');
		$this->odf->setImage('img_niv_reg_visu', '../images/'.$arrDiag['handicateur1']['visuel'][0].'.png');
		$this->odf->setImage('img_niv_reg_cog', '../images/'.$arrDiag['handicateur1']['cognitif'][0].'.png');
		$this->trace("handicateur 1");
		
		$this->odf->setImage('img_niv_app_moteur', '../images/'.$arrDiag['handicateur']['moteur'][0].'.png');
		$this->odf->setImage('img_niv_app_audio', '../images/'.$arrDiag['handicateur']['auditif'][0].'.png');
		$this->odf->setImage('img_niv_app_visu', '../images/'.$arrDiag['handicateur']['visuel'][0].'.png');
		$this->odf->setImage('img_niv_app_cog', '../images/'.$arrDiag['handicateur']['cognitif'][0].'.png');
		$this->trace("handicateur");
		
		$this->odf->setImage('img_niv_reg_moteur1', '../images/'.$arrDiag['handicateur1']['moteur'][0].'.png');
		$this->odf->setImage('img_niv_reg_audio1', '../images/'.$arrDiag['handicateur1']['auditif'][0].'.png');
		$this->odf->setImage('img_niv_reg_visu1', '../images/'.$arrDiag['handicateur1']['visuel'][0].'.png');
		$this->odf->setImage('img_niv_reg_cog1', '../images/'.$arrDiag['handicateur1']['cognitif'][0].'.png');
		$this->trace("1 handicateur 1");
		
		$this->odf->setImage('img_niv_app_moteur1', '../images/'.$arrDiag['handicateur']['moteur'][0].'.png');
		$this->odf->setImage('img_niv_app_audio1', '../images/'.$arrDiag['handicateur']['auditif'][0].'.png');
		$this->odf->setImage('img_niv_app_visu1', '../images/'.$arrDiag['handicateur']['visuel'][0].'.png');
		$this->odf->setImage('img_niv_app_cog1', '../images/'.$arrDiag['handicateur']['cognitif'][0].'.png');
		$this->trace("1 handicateur");
						
		//récupère les informations de l'établissement
		$dons = $this->arrEtatLieux["Models_DbTable_Gevu_etablissements"][0];
		$geo = $this->arrEtatLieux["Models_DbTable_Gevu_geos"][0];
		$erp = $dons["reponse_1"];
		$structure = "???";
		if($erp=='1'){
			$structure = "Etablissement recevant du public (ERP)";
		}
		$this->odf->setVars('etab_structure', $structure);
		$this->odf->setVars('etab_cat_erp', $this->getTitreMC($dons["reponse_2"]));
		//récupère le proprio
		if($dons["contact_proprietaire"]){
			$proprio = $dbCnt->findById_contact($dons["contact_proprietaire"]);
			$proprio = $proprio['prenom']." ".$proprio['nom'];
		}else
			$proprio = "???";
		$this->odf->setVars('etab_proprio', $proprio);
		$this->odf->setVars('etab_adresse', $geo["adresse"]);
		
		
		//initialise la liste des éléments de diagnostics
		$arrEleDiag = array();
		
		//construction de la structure de l'atablissement 
		//pour les batiments
		$e_bats = $this->odf->setSegment('etab_bats');		
		//récupère la liste des batiments
		$arrBat = $dbL->getChildForTypeControle($this->idLieu, 45);
		foreach($arrBat as $r){
			//récupère les infos du batiment
			$arrEtatBat = $oDiag->getNodeRelatedData($r['id_lieu'], $this->idExi, $idBase);
			$dons = $arrEtatBat["Models_DbTable_Gevu_batiments"][0];
				
			$e_bats->setVars('bat_nom', $r['lib']);
			$e_bats->setVars('bat_classement', $this->getTitreMC($dons['reponse_6']));
			$e_bats->setVars('bat_zppaup', $this->getTitreMC($dons['reponse_4']));
			$e_bats->setVars('bat_nb_niveau', $dons['reponse_7']);
			$e_bats->setVars('bat_nb_ascenseur', $dons['reponse_11']);
			$e_bats->setVars('bat_parcking_in', $dons['reponse_13']);
			$e_bats->setVars('bat_parcking_out', $dons['reponse_14']);
			$e_bats->setVars('bat_date_construction', $dons['date_achevement']);
			$e_bats->merge();
		
			//enregistre l'élément
			$arrEleDiag[] = $arrEtatBat;
			$this->trace($r['lib']);
				
		}
		$this->odf->mergeSegment($e_bats);
				
		//construction de la structure de l'atablissement 
		//pour les parcelles
		$e_parcs = $this->odf->setSegment('etab_parcs');		
		//récupère la liste des parcelles
		$arrParc = $dbL->getChildForTypeControle($this->idLieu, 51);
		foreach($arrParc as $r){
			//récupère les infos du batiment
			$arrEtatParc = $oDiag->getNodeRelatedData($r['id_lieu'], $this->idExi, $idBase);
			$dons = $arrEtatParc["Models_DbTable_Gevu_parcelles"][0];

			$e_parcs->setVars('parc_ref', $r['lib']." ".$dons['ref']);
			$e_parcs->setVars('parc_superficie', $dons['superficie']);
			$e_parcs->setVars('parc_cadastre', $dons['ref_cadastre']);
			$e_parcs->setVars('parc_cloture', $this->getTitreMC($dons['cloture']));
			$e_parcs->merge();
		
			//enregistre l'élément
			$arrEleDiag[] = $arrEtatParc;
			$this->trace($r['lib']);
				
		}		
		$this->odf->mergeSegment($e_parcs);
		
		
		$bats = $this->odf->setSegment('bats');
		$plans = $this->odf->setSegment('plan_bats');
		foreach($arrEleDiag as $r){

			//récupération de l'identifiant de lieu
			if(isset($r["Models_DbTable_Gevu_parcelles"]))
				$idLieuDiag = $r["Models_DbTable_Gevu_parcelles"][0]["id_lieu"];
			else
				$idLieuDiag = $r["Models_DbTable_Gevu_geos"][0]["id_lieu"];
			
			//récupération des images
			if(isset($r["Models_DbTable_Gevu_docsxlieux"])){
				$arrDocs = $r["Models_DbTable_Gevu_docsxlieux"];
				//ajout des images au modèle
				$plans = $this->setImage($plans, 'plan_bat_img', $arrDocs);
				if(count($arrDocs)>0){
					$plans->setVars('plan_bat_nom', 'Plan : ' . $arrDocs[0]['titre']);
				}				
			}
			 	
			//calcul le cout pour le batiments
			$arrCout = $dbDS->getCoutsByIdLieu($idLieuDiag);
		
			$bats->setVars('bat_nom', $r['ariane'][0]['parLib']);
			$bats->setVars('bat_cout_reg', $arrCout["reg"]);
			$bats->setVars('bat_cout_sou', $arrCout["sou"]);
			$bats->setVars('bat_cout_tot', $arrCout["reg"]+$arrCout["sou"]);
			 	
			$this->trace($r['ariane'][0]['parLib']." : ".$arrCout["reg"]." ".$arrCout["sou"]);
				
			//récupère les niveaux
			$arrNivs = 	$dbL->getChildForTypeControle($idLieuDiag, 46);
			$cReg="";$cSou="";
			foreach($arrNivs as $niv){
				$bats->nivs->niv_nom($niv["lib"]);
				$arrCoutNiv = $dbDS->getCoutsByIdLieu($niv['id_lieu']);
				$bats->nivs->niv_cout_reg($arrCoutNiv["reg"]);
				$bats->nivs->niv_cout_sou($arrCoutNiv["sou"]);
				$bats->nivs->niv_cout_tot($arrCoutNiv["reg"]+$arrCoutNiv["sou"]);
				$cReg+=$arrCoutNiv["reg"]; 
				$cSou+=$arrCoutNiv["sou"];
				$this->trace($niv["lib"]);
				
				/*
				$arrEtatNiv = $oDiag->getNodeRelatedData($niv['id_lieu'], $idExi, $idBase);				
				$plans->plan_nivs->plan_niv_nom('Plan niveau :' . $niv["lib"]);
				$arrDocs = $arrEtatNiv["Models_DbTable_Gevu_docsxlieux"];
				$plans->plan_nivs = $this->setImage($plans->plan_nivs, 'plan_niv_img', $arrDocs);
				
				$this->trace("nombre de plan : ".count($arrDocs));
				*/
			}
			$bats->setVars('niv_cout_reg_tot', $cReg);
			$bats->setVars('niv_cout_sou_tot', $cSou);
			$bats->setVars('niv_cout_tot_tot', $cReg+$cSou);
			$bats->merge();
			$plans->merge();
		
		}
		$this->odf->mergeSegment($plans);
		$this->odf->mergeSegment($bats);
		
		$this->odf->setImage('img_cadastre', '../images/kml.png');		
		
		$probs = $this->odf->setSegment('probs');		
		//pour chaque batiments et chaques parcelles
		$j=0;
		$z=0;
		$arrIdParent = array();
		$oLieu = -1;
		foreach($arrEleDiag as $r){
		
			//récupération de l'identifiant de lieu
			if(isset($r["Models_DbTable_Gevu_parcelles"]))
				$idLieuDiag = $r["Models_DbTable_Gevu_parcelles"][0]["id_lieu"];
			else
				$idLieuDiag = $r["Models_DbTable_Gevu_geos"][0]["id_lieu"];
				
			$this->trace($j."/".count($arrEleDiag)." - Identifiant du lieu : ".$idLieuDiag);
						
			//récupère les problèmes
			$arrProbs = $dbDS->getProblemesForLieu($idLieuDiag, $idBase);
			$this->trace("Nb de problème : ".count($arrProbs));
			 	
			//if($j>0) exit();
				
			
			foreach($arrProbs as $rP){
				//vérfie si on passe à un nouveau lieu
				if($oLieu != $rP['id_lieu']){
					//on enregistre le problème précédent
					$oLieu = $rP['id_lieu'];
					$j++;
					$z=0;
					if($j>1){
						$probs->merge();
					}
					//if($j>10) exit();
						
					//récupère les données du nouveau problème
					//$arrEtatProb = $oDiag->getNodeRelatedData($rP['id_lieu'], $idExi, $idBase);
					//$arrAriane = $arrEtatProb["ariane"];
										
					//construction du fil d'ariane
					$arrAriane = $dbL->getFullPath($rP['id_lieu']);
					$strAriane = "";
					$bFil=false;
					foreach ($arrAriane as $l) {
						if($l['id_lieu']==$idLieuDiag)$bFil=true;
						if($bFil)$strAriane .= $l['lib']." - ";			
					}
					$this->trace($j." : ".$strAriane);
										
					$probs->setVars('prob_num', $j);
					$probs->setVars('prob_ariane', $strAriane);
		
					//$arrDocs = $arrEtatProb["Models_DbTable_Gevu_docsxlieux"];
					if($rP['idsDoc']){
						$arrDocs = $dbDoc->findByIdDoc($rP['idsDoc']);
						$probs = $this->setImage($probs, 'prob_img', $arrDocs);						
					}else{
						$probs->setImage('prob_img', '../images/check_no.png');						
					}
				}
				
				$probs->rowprob->prob_const($rP['affirmation']);
				if($rP['idsProb']){				
					//récupère les problème/observation
					$rs = $dbProb->findById_probleme($rP['idsProb']);
					foreach ($rs as $rO) {
						$probs->rowprob->probobs->prob_mesure('prob_mesure :'.$rO['mesure']);
						$probs->rowprob->probobs->obs_diag($rO['observations']);
						$probs->rowprob->probobs->merge();
					}
				}else{
					$probs->rowprob->probobs->prob_mesure('');
					$probs->rowprob->probobs->obs_diag('');
					$probs->rowprob->probobs->merge();
				}
				$this->trace($j.":".$z.": fin problème");
								
				//ajoute les images réglementaires
				$src = $this->getImgReg($rP['typeCrit'], $rP['droitCrit']);
				$probs->rowprob->regimg->setImage('prob_reg', $src);
				$probs->rowprob->regimg->merge();
				$this->trace('prob_reg:'.$src);
				
				
				//ajoute les image d'handicateur
				$urlHandiImg = "../images/handi_audio".$rP['handicateur_auditif']."cog".$rP['handicateur_cognitif']."moteur".$rP['handicateur_moteur']."visu".$rP['handicateur_visuel'].".png"; 
				$probs->rowprob->probimg->setImage('prob_defici', $urlHandiImg);
				$probs->rowprob->probimg->merge();
				$this->trace('prob_defici:'.$urlHandiImg);
				
				$probs->rowprob->probsolus->prob_solus($rP['solution']);
				$probs->rowprob->probsolus->prob_prod($rP['produit']);
				$probs->rowprob->probsolus->prob_cout($rP['dscout']);
				if($rP['idsDocSolus']!=""){
					//ajoute l'image du produit
					$arrDocs = $dbDoc->findByIdDoc($rP['idsDocSolus']);
					$probs->rowprob->probsolus = $this->setImage($probs->rowprob->probsolus, 'prob_imgsolus', $arrDocs);
				}else{
					$probs->rowprob->probsolus->prob_imgsolus("");
				}
				if($rP['idsDocProd']!=""){
					//ajoute l'image du produit
					$arrDocs = $dbDoc->findByIdDoc($rP['idsDocProd']);
					$probs->rowprob->probsolus = $this->setImage($probs->rowprob->probsolus, 'prob_imgprod', $arrDocs);
				}else{
					$probs->rowprob->probsolus->prob_imgprod("");
				}
				$probs->rowprob->probsolus->merge();
			  
				$probs->rowprob->merge();
				
				$this->trace($j.":".$z.": FIN : ".$rP['critRef']);
				$z++;
			}
			$probs->merge();
		}
		$this->odf->mergeSegment($probs);
		
		$this->trace("Presque Fin");
		
		
	}
	
	function creaRapportBat(){

		$nomBat = $this->arrAriane[0]['parLib'];
		$this->odf->setVars('Bâtiment', $nomBat);
		
		//récupération des images
		$arrDocs = $this->arrEtatLieux["Models_DbTable_Gevu_docsxlieux"];
		//ajout des images au modèle
		$this->odf = $this->setImage($this->odf, 'img_Bat', $arrDocs);
		
		
		//récupère la dernière campagne de diagnostique
		$dbDiag = new Models_DbTable_Gevu_diagnostics($this->db);		
		$arrLastDiag = $dbDiag->findLastDiagForLieu($this->idLieu);

		$this->setInfoAuteurDiag($arrLastDiag);
				
		$arrDiag = $this->arrEtatLieux["___diagnostics"]["diag"];
		$this->trace("Etat des lieux Etablissement");
		
		$this->setDiagIndicateur($arrDiag);
		$this->setDiagIndicateur($arrDiag,"1");
		$this->setInfoEtab();						
		
				
		//récupère les infos du batiment
		$dons = $this->arrEtatLieux["Models_DbTable_Gevu_batiments"][0];
		$this->setInfoBat($nomBat, $dons);				

		
		//construction de la strucre du batiment
		$bats = $this->odf->setSegment('bats');
		$plans = $this->odf->setSegment('plan_bats');			
		//récupération des images
		if(isset( $this->arrEtatLieux["Models_DbTable_Gevu_docsxlieux"])){
			$arrDocs =  $this->arrEtatLieux["Models_DbTable_Gevu_docsxlieux"];
			//filtre les plans
			$docs = "";
			foreach ($arrDocs as $doc) {
				if($doc["branche"]==4)$docs[]=$doc;
			}
			//ajout des images au modèle
			$plans = $this->setImage($plans, 'plan_bat_img', $docs);
			if($docs){
				$plans->setVars('plan_bat_nom', 'Plan : ' . $docs[0]['titre']);
			}				
		}
		 				 	
		//calcul le cout pour le batiments
		$arrCout = $this->dbDS->getCoutsByIdLieu($this->idLieu);
	
		$bats->setVars('bat_nom', $nomBat);
		$bats->setVars('bat_cout_reg', $arrCout["reg"]);
		$bats->setVars('bat_cout_sou', $arrCout["sou"]);
		$bats->setVars('bat_cout_tot', $arrCout["reg"]+$arrCout["sou"]);
		 	
		$this->trace($nomBat." : ".$arrCout["reg"]." ".$arrCout["sou"]);
			
		//récupère les niveaux
		$arrNivs = 	$this->dbL->getChildForTypeControle($this->idLieu, 46);
		$cReg="";$cSou="";
		foreach($arrNivs as $niv){
			$bats->nivs->niv_nom($niv["lib"]);
			$arrCoutNiv = $this->dbDS->getCoutsByIdLieu($niv['id_lieu']);
			$bats->nivs->niv_cout_reg($arrCoutNiv["reg"]);
			$bats->nivs->niv_cout_sou($arrCoutNiv["sou"]);
			$bats->nivs->niv_cout_tot($arrCoutNiv["reg"]+$arrCoutNiv["sou"]);
			$cReg+=$arrCoutNiv["reg"]; 
			$cSou+=$arrCoutNiv["sou"];
			$this->trace($niv["lib"]);			
		}
		$bats->setVars('niv_cout_reg_tot', $cReg);
		$bats->setVars('niv_cout_sou_tot', $cSou);
		$bats->setVars('niv_cout_tot_tot', $cReg+$cSou);
		$bats->merge();
		$plans->merge();
		
		$this->odf->mergeSegment($plans);
		$this->odf->mergeSegment($bats);		
		
		$this->odf->setImage('img_cadastre', '../images/kml.png');		
		
		//ajoute les problèmes
		$this->setProb();
		
		$this->trace("Presque Fin");
		
		
	}	
	
	function creaRapportLog(){

		$arr = $this->arrEtatLieux["Models_DbTable_Gevu_stats"][0];
		$this->odf->setVars('num', $arr["Code_Logement"]);

		$arrGeo = $this->arrEtatLieux["Models_DbTable_Gevu_geos"][0];
		$this->odf->setVars('commune', $arrGeo['ville']);
		$this->odf->setVars('adresse', $arrGeo['adresse']." ".$arrGeo['codepostal']." ".$arrGeo['ville']);
		
		$this->odf->setVars('groupe', $arr['Code_groupe']);
		$this->odf->setVars('bat', $arr['Code_Batiment']);
		
		$this->odf->setVars('type', $arr["Type_Logement"]);
		$this->odf->setVars('surface', $arr["Surface_Appliquee"]);
		$this->odf->setVars('nbPiece', $arr["Nombre_pieces"]);
		$this->odf->setVars('etage', $arr["Etage"]);
		$this->odf->setVars('ascenseur',"???");
		$this->odf->setVars('date_construction',$arr["Annee_Construction"]);
				
		//récupération des images
		$arrDocs = $this->arrEtatLieux["Models_DbTable_Gevu_docsxlieux"];
		//ajout des images au modèle
		$this->odf = $this->setImage($this->odf, 'img_log', $arrDocs);
		
		
		//récupère la dernière campagne de diagnostique
		$dbDiag = new Models_DbTable_Gevu_diagnostics($this->db);		
		$arrLastDiag = $dbDiag->findLastDiagForLieu($this->idLieu);

		$this->setInfoAuteurDiag($arrLastDiag);
				
		$arrDiag = $this->arrEtatLieux["___diagnostics"]["diag"];
		$this->trace("Etat des lieux Etablissement");
		
		$this->setDiagIndicateur($arrDiag);
		$this->setDiagIndicateur($arrDiag,"1");
		
		//construction de la strucre des plans
		if(isset( $this->arrEtatLieux["Models_DbTable_Gevu_docsxlieux"])){
			$arrDocs =  $this->arrEtatLieux["Models_DbTable_Gevu_docsxlieux"];
			//filtre les plans
			$docs = "";
			foreach ($arrDocs as $doc) {
				if($doc["branche"]==4)$docs[]=$doc;
			}
			//ajout des images au modèle
			$this->odf = $this->setImage($this->odf, 'plan_log_img', $docs);
			if($docs){
				$this->odf->setVars('plan_log_nom', 'Plan : ' . $docs[0]['titre']);
			}				
		}
		 				 	
		//calcul le cout
		$arrCout = $this->dbDS->getCoutsByIdLieu($this->idLieu);
	
		$this->odf->setVars('cout_reg', $arrCout["reg"]);
		$this->odf->setVars('cout_sou', $arrCout["sou"]);
		$this->odf->setVars('cout_tot', $arrCout["reg"]+$arrCout["sou"]);
		 	
		$this->trace($arrCout["reg"]." ".$arrCout["sou"]);
					
		$this->odf->setImage('img_cadastre', '../images/kml.png');		
		
		//ajoute les problèmes
		$this->setProb();
		
		$this->trace("Presque Fin");
		
		
	}		
	
	function creaRapportNiv(){

		$nom = $this->arrAriane[0]['parLib'];
		$this->odf->setVars('Niveau', $nom);
		
		//récupération des images
		$arrDocs = $this->arrEtatLieux["Models_DbTable_Gevu_docsxlieux"];
		//ajout des images au modèle
		$this->odf = $this->setImage($this->odf, 'img_Niv', $arrDocs);
		
		
		//récupère la dernière campagne de diagnostique
		$dbDiag = new Models_DbTable_Gevu_diagnostics($this->db);		
		$arrLastDiag = $dbDiag->findLastDiagForLieu($this->idLieu);
		
		$this->setInfoAuteurDiag($arrLastDiag);
		
		$arrDiag = $this->arrEtatLieux["___diagnostics"]["diag"];
		$this->trace("Etat des lieux");
		
		$this->setDiagIndicateur($arrDiag);
		$this->setDiagIndicateur($arrDiag,"1");
		$this->setInfoEtab();						
		
				
		//récupère les infos du batiment
		$idBat = "";
		foreach ($this->arrAriane as $l){
			if($l['id_type_controle']==45){
				$idBat = $l['id_lieu'];
				$nomBat = $l["lib"];
			}		
		}
		if($idBat){
			$arrBat = $this->oDiag->getNodeRelatedData($idBat, $this->idExi, $this->idBase);
			$dons = $arrBat["Models_DbTable_Gevu_batiments"][0];
			$this->setInfoBat($nomBat, $dons);				
		}
		

		
		//construction de la structure du batiment
		$plans = $this->odf->setSegment('plan_bats');			
		//récupération des images
		if(isset($arrBat["Models_DbTable_Gevu_docsxlieux"])){
			$arrDocs =  $arrBat["Models_DbTable_Gevu_docsxlieux"];
			//filtre les plans
			$docs = "";
			foreach ($arrDocs as $doc) {
				if($doc["branche"]==4)$docs[]=$doc;
			}
			//ajout des images au modèle
			$plans = $this->setImage($plans, 'plan_bat_img', $docs);
			if($docs){
				$plans->setVars('plan_bat_nom', 'Plan : ' . $docs[0]['titre']);
			}				
		}
		$plans->merge();
		$this->odf->mergeSegment($plans);
		
		//calcul le cout pour le niveau
		$arrCout = $this->dbDS->getCoutsByIdLieu($this->idLieu);
	
		$this->odf->setVars('niv_cout_reg', $arrCout["reg"]);
		$this->odf->setVars('niv_cout_sou', $arrCout["sou"]);
		$this->odf->setVars('niv_cout_tot', $arrCout["reg"]+$arrCout["sou"]);
		 	
		$this->trace($arrCout["reg"]." ".$arrCout["sou"]);
			
		$this->odf->setVars('niv_cout_reg_tot', $cReg);
		$this->odf->setVars('niv_cout_sou_tot', $cSou);
		$this->odf->setVars('niv_cout_tot_tot', $cReg+$cSou);
				
		$this->odf->setImage('img_cadastre', '../images/kml.png');		
		
		//ajoute les problèmes
		$this->setProb();
		
		$this->trace("Presque Fin");
				
	}		

	function creaRapportEspace(){

		$nom = $this->arrAriane[0]['parLib'];
		$this->odf->setVars('objet', $nom);
		
		//récupération des images
		$arrDocs = $this->arrEtatLieux["Models_DbTable_Gevu_docsxlieux"];
		//ajout des images au modèle
		$this->odf = $this->setImage($this->odf, 'img_objet', $arrDocs);
		
		
		//récupère la dernière campagne de diagnostique
		$dbDiag = new Models_DbTable_Gevu_diagnostics($this->db);		
		$arrLastDiag = $dbDiag->findLastDiagForLieu($this->idLieu);
		
		$this->setInfoAuteurDiag($arrLastDiag);
		
		$arrDiag = $this->arrEtatLieux["___diagnostics"]["diag"];
		$this->trace("Etat des lieux");
		
		$this->setDiagIndicateur($arrDiag);
		$this->setDiagIndicateur($arrDiag,"1");
		$this->setInfoEtab();						
						
		//calcul le cout pour le niveau
		$arrCout = $this->dbDS->getCoutsByIdLieu($this->idLieu);
	
		$this->odf->setVars('cout_reg', $arrCout["reg"]);
		$this->odf->setVars('cout_sou', $arrCout["sou"]);
		$this->odf->setVars('cout_tot', $arrCout["reg"]+$arrCout["sou"]);
		 							
		//ajoute les problèmes
		$this->setProb();
		
		$this->trace("Presque Fin");
				
	}		
	
	function creaFicheLog(){

		$arr = $this->arrEtatLieux["Models_DbTable_Gevu_stats"][0];
		$this->odf->setVars('num', $arr["Code_Logement"]);

		$this->odf->setVars('groupe', $arr['Code_groupe']);
		$this->odf->setVars('bat', $arr['Code_Batiment']);
		
		$this->odf->setVars('type', $arr["Type_Logement"]);
		$this->odf->setVars('surface', $arr["Surface_Appliquee"]);
		$this->odf->setVars('nbPiece', $arr["Nombre_pieces"]);
		$this->odf->setVars('etage', $arr["Etage"]);
		$this->odf->setVars('ascenseur',"???");
		
		
		//récupère la dernière campagne de diagnostique
		$dbDiag = new Models_DbTable_Gevu_diagnostics($this->db);		
		$arrLastDiag = $dbDiag->findLastDiagForLieu($this->idLieu);
		
		$this->setInfoAuteurDiag($arrLastDiag);
		
		$arrDiag = $this->arrEtatLieux["___diagnostics"]["diag"];
		$this->trace("Etat des lieux");
		
		$this->setDiagIndicateur($arrDiag);
								
		$this->trace("Presque Fin");
				
	}		
	
	
	function creaRapportObjet(){

		$nom = $this->arrAriane[0]['parLib'];
		$this->odf->setVars('espace', $nom);
		
		//récupération des images
		$arrDocs = $this->arrEtatLieux["Models_DbTable_Gevu_docsxlieux"];
		//ajout des images au modèle
		$this->odf = $this->setImage($this->odf, 'img_espace', $arrDocs);
		
		
		//récupère la dernière campagne de diagnostique
		$dbDiag = new Models_DbTable_Gevu_diagnostics($this->db);		
		$arrLastDiag = $dbDiag->findLastDiagForLieu($this->idLieu);
		
		$this->setInfoAuteurDiag($arrLastDiag);
		
		$arrDiag = $this->arrEtatLieux["___diagnostics"]["diag"];
		$this->trace("Etat des lieux");
		
		$this->setDiagIndicateur($arrDiag);
		$this->setDiagIndicateur($arrDiag,"1");
		$this->setInfoEtab();						
						
		//calcul le cout pour le niveau
		$arrCout = $this->dbDS->getCoutsByIdLieu($this->idLieu);
	
		$this->odf->setVars('cout_reg', $arrCout["reg"]);
		$this->odf->setVars('cout_sou', $arrCout["sou"]);
		$this->odf->setVars('cout_tot', $arrCout["reg"]+$arrCout["sou"]);
		 							
		//ajoute les problèmes
		$this->setProb();
		
		$this->trace("Presque Fin");
				
	}		
	
	
	function setInfoAuteurDiag($arrLastDiag){
		
		if(count($arrLastDiag)==0){
			$arrLastDiag[0]['nom']="pas de diagnostiqueur";
			$arrLastDiag[0]['maintenant']="aucun diagnostic";
		}
		
		//ajout des coordonnée de l'auteur
		$this->odf->setVars('redacteur', $this->arrExi['nom'].' '.$this->arrExi['mail']);
		$today = strftime( "%A %d %B %Y" , time());
		$this->odf->setVars('date_redaction', $today);
		$this->odf->setVars('diagnostiqueur', $arrLastDiag[0]['nom']);
		//information pas présente actuellement dans la base
		$this->odf->setVars('structure_diagnostiqueur', '???');
		$this->odf->setVars('date_passage', $arrLastDiag[0]['maintenant']);
		
	}
	
	function setInfoBat($nomBat, $dons){
		$this->odf->setVars('bat_nom', $nomBat);
		$this->odf->setVars('bat_classement', $this->getTitreMC($dons['reponse_6']));
		$this->odf->setVars('bat_zppaup', $this->getTitreMC($dons['reponse_4']));
		$this->odf->setVars('bat_nb_niveau', $dons['reponse_7']);
		$this->odf->setVars('bat_nb_ascenseur', $dons['reponse_11']);
		$this->odf->setVars('bat_parcking_in', $dons['reponse_13']);
		$this->odf->setVars('bat_parcking_out', $dons['reponse_14']);
		$this->odf->setVars('bat_date_construction', $dons['date_achevement']);
		$this->trace($nomBat);
		
	}
	
	function setInfoEtab(){
		//récupère les informations de l'établissement
		$idEtab = 0;
		foreach ($this->arrAriane as $l){
			if($l['id_type_controle']==44)$idEtab = $l['id_lieu'];
		}
		if($idEtab){
			$arrEtab = $this->oDiag->getNodeRelatedData($idEtab, $this->idExi, $this->idBase);
			$dons = $arrEtab["Models_DbTable_Gevu_etablissements"][0];
			$geo = $arrEtab["Models_DbTable_Gevu_geos"][0];
			$erp = $dons["reponse_1"];
			$structure = "???";
			if($erp=='1'){
				$structure = "Etablissement recevant du public (ERP)";
			}
			$this->odf->setVars('etab_structure', $structure);
			$this->odf->setVars('etab_cat_erp', $this->getTitreMC($dons["reponse_2"]));
			//récupère le proprio
			if($dons["contact_proprietaire"]){
				$proprio = $dbCnt->findById_contact($dons["contact_proprietaire"]);
				$proprio = $proprio['prenom']." ".$proprio['nom'];
			}else
				$proprio = "???";
			$this->odf->setVars('etab_proprio', $proprio);
			$this->odf->setVars('etab_adresse', $geo["adresse"]);
			
		}
		
	}
	
	function setProb(){
		$probs = $this->odf->setSegment('probs');		
		//pour chaque batiments et chaques parcelles
		$j=0;
		$z=0;
		$arrIdParent = array();
		$oLieu = -1;
				
		$this->trace($j."/1 - Identifiant du lieu : ".$this->idLieu);
					
		//récupère les problèmes
		$arrProbs = $this->dbDS->getProblemesForLieu($this->idLieu, $this->idBase);
		$this->trace("Nb de problème : ".count($arrProbs));
		 	
		foreach($arrProbs as $rP){
			//vérfie si on passe à un nouveau lieu
			if($oLieu != $rP['id_lieu']){
				//on enregistre le problème précédent
				$oLieu = $rP['id_lieu'];
				$j++;
				$z=0;
				if($j>1){
					$probs->merge();
				}
									
				//construction du fil d'ariane
				$arrAriane = $this->dbL->getFullPath($rP['id_lieu']);
				$strAriane = "";
				$bFil=false;
				foreach ($arrAriane as $l) {
					if($l['id_lieu']==$this->idLieu)$bFil=true;
					if($bFil)$strAriane .= $l['lib']." - ";			
				}
				$this->trace($j." : ".$strAriane);
									
				$probs->setVars('prob_num', $j);
				$probs->setVars('prob_ariane', $strAriane);
	
				if($rP['idsDoc']){
					$arrDocs = $this->dbDoc->findByIdDoc($rP['idsDoc']);
					$probs = $this->setImage($probs, 'prob_img', $arrDocs);						
				}else{
					$probs->setImage('prob_img', '../images/check_no.png');						
				}
			}
			
			$probs->rowprob->prob_const($rP['affirmation']);
			if($rP['idsProb']){				
				//récupère les problème/observation
				$rs = $this->dbProb->findById_probleme($rP['idsProb']);
				foreach ($rs as $rO) {
					$probs->rowprob->probobs->prob_mesure('prob_mesure :'.$rO['mesure']);
					$probs->rowprob->probobs->obs_diag($rO['observations']);
					$probs->rowprob->probobs->merge();
				}
			}else{
				$probs->rowprob->probobs->prob_mesure('');
				$probs->rowprob->probobs->obs_diag('');
				$probs->rowprob->probobs->merge();
			}
			$this->trace($j.":".$z.": fin problème");
							
			//ajoute les images réglementaires
			$src = $this->getImgReg($rP['typeCrit'], $rP['droitCrit']);
			$probs->rowprob->regimg->setImage('prob_reg', $src);
			$probs->rowprob->regimg->merge();
			$this->trace('prob_reg:'.$src);
			
			
			//ajoute les image d'handicateur
			$urlHandiImg = "../images/handi_audio".$rP['handicateur_auditif']."cog".$rP['handicateur_cognitif']."moteur".$rP['handicateur_moteur']."visu".$rP['handicateur_visuel'].".png"; 
			$probs->rowprob->probimg->setImage('prob_defici', $urlHandiImg);
			$probs->rowprob->probimg->merge();
			$this->trace('prob_defici:'.$urlHandiImg);
			
			$probs->rowprob->probsolus->prob_solus($rP['solution']);
			$probs->rowprob->probsolus->prob_prod($rP['produit']);
			$probs->rowprob->probsolus->prob_cout($rP['dscout']);
			if($rP['idsDocSolus']!=""){
				//ajoute l'image du produit
				$arrDocs = $this->dbDoc->findByIdDoc($rP['idsDocSolus']);
				$probs->rowprob->probsolus = $this->setImage($probs->rowprob->probsolus, 'prob_imgsolus', $arrDocs);
			}else{
				$probs->rowprob->probsolus->prob_imgsolus("");
			}
			if($rP['idsDocProd']!=""){
				//ajoute l'image du produit
				$arrDocs = $this->dbDoc->findByIdDoc($rP['idsDocProd']);
				$probs->rowprob->probsolus = $this->setImage($probs->rowprob->probsolus, 'prob_imgprod', $arrDocs);
			}else{
				$probs->rowprob->probsolus->prob_imgprod("");
			}
			$probs->rowprob->probsolus->merge();
		  
			$probs->rowprob->merge();
			
			$this->trace($j.":".$z.": FIN : ".$rP['critRef']);
			$z++;
		}
		$probs->merge();
		$this->odf->mergeSegment($probs);		
	}
	
	function setDiagIndicateur($arrDiag, $type=""){
		$this->odf->setImage('img_niv_reg_moteur'.$type, '../images/'.$arrDiag['handicateur1']['moteur'][0].'.png');
		$this->odf->setImage('img_niv_reg_audio'.$type, '../images/'.$arrDiag['handicateur1']['auditif'][0].'.png');
		$this->odf->setImage('img_niv_reg_visu'.$type, '../images/'.$arrDiag['handicateur1']['visuel'][0].'.png');
		$this->odf->setImage('img_niv_reg_cog'.$type, '../images/'.$arrDiag['handicateur1']['cognitif'][0].'.png');
		$this->trace($type." handicateur 1");
		
		$this->odf->setImage('img_niv_app_moteur'.$type, '../images/'.$arrDiag['handicateur3']['moteur'][0].'.png');
		$this->odf->setImage('img_niv_app_audio'.$type, '../images/'.$arrDiag['handicateur3']['auditif'][0].'.png');
		$this->odf->setImage('img_niv_app_visu'.$type, '../images/'.$arrDiag['handicateur3']['visuel'][0].'.png');
		$this->odf->setImage('img_niv_app_cog'.$type, '../images/'.$arrDiag['handicateur3']['cognitif'][0].'.png');
		$this->trace($type." handicateur 3");
		
		$this->odf->setImage('img_niv_moteur'.$type, '../images/'.$arrDiag['handicateur']['moteur'][0].'.png');
		$this->odf->setImage('img_niv_audio'.$type, '../images/'.$arrDiag['handicateur']['auditif'][0].'.png');
		$this->odf->setImage('img_niv_visu'.$type, '../images/'.$arrDiag['handicateur']['visuel'][0].'.png');
		$this->odf->setImage('img_niv_cog'.$type, '../images/'.$arrDiag['handicateur']['cognitif'][0].'.png');
		$this->trace($type." handicateur");		
	}
	
	function getImgReg($type, $regle){
		/**
		1	Réglementaire
		3	Souhaitable
		**/		
		if($type==3){
			$type="S";
		}else{
			$type="R";
		}
		$path = '';
		/**
		2	ERP_IOP
		3	ERP_IOP existant
		4	Travail
		5	Voirie
		6	Modalité particulière
		7	Logement
		**/
		if($regle==2){
			$path = '/images/'.$type.'ERP.png';
		}
		if($regle==3){
			$path = '/images/'.$type.'ERPexistant.png';
		}
		if($regle==5){
			$path = '/images/'.$type.'VOIRIE.png';
		}
		if($regle==7){
			$path = '/images/'.$type.'LOGEMENT.png';
		}
		if($regle==4){
			$path = '/images/'.$type.'CT.png';
		}
		if($regle==6){
			$path = '/images/'.$type.'MODPART.png';
		}
		if($regle=="2-3" || $regle=="2-3-6" || $regle=="2-3-6-7" || $regle=="2-3-7" || $regle=="2-6-7" || $regle=="2-7" || $regle=="3-6" || $regle=="3-6-7"){
			$path = '/images/'.$type.$regle.'.png';
		}		
		if($path==''){
			$path = '/images/check_no.png';
		}
	
		return "..".$path;
		//return WEB_ROOT.$path;
	
	}	

	/**
	 * ajoute une image au modèle
	 *
	 * @param string $balise
	 * @param array $arrDocs
	 *
	 */
	public function setImage($oOdf, $balise, $arrDocs){
	
		if(isset($arrDocs[0])){
			$doc = $arrDocs[0];
			if($doc["content_type"]=='image/gif' || $doc["content_type"]=='image/jpeg' || $doc["content_type"]=='image/png'){
				//récupère la taille de l'image
				if($this->pathDebug)$path = str_replace("/home/gevu/www/data/lieux",$this->pathDebug."/data/lieux", $doc['path_source']);
				else $path = $doc['path_source'];
				/*
				$size = getimagesize($doc['url']);
				if($size[0] > $size[1])
					$oOdf->setImage($balise, $path, 0, 9);						
				else
				*/
				$oOdf->setImage($balise, $path, 9, 0);
				$this->trace("setImage : ".$path);
			}
		}else{
			//sans image on en met une par defaut
			$oOdf->setImage($balise, '../images/check_no.png');
			$this->trace("setImage : NO");
		}
		
		return $oOdf;
	}
		
	
	/**
	 * renvoie le libellé d'un mot clef
	 *
	 * @param int $idMC
	 *
	 * @return string
	 */
	public function getTitreMC($idMC){
	
		if(!$idMC) return "";
		$nb = count($this->arrMC);
		for ($i = 0; $i < $nb; $i++) {
			if($this->arrMC[$i]['id_motclef']==$idMC) return $this->arrMC[$i]['titre'];
		}
		return "";
	}

	
	/**
     * récupère la liste des diagnostics pour un lieu
     *
     * @param int $idLieu
     * @param string $idBase
     * 
     * @return array
     */
	public function getSolusProb($idLieu, $idBase){

	try {
		
			$diag = new GEVU_Diagnostique();
			$dbD = new Models_DbTable_Gevu_diagnostics();		
			
			$arrDB = $diag->db->getConfig();
			$arrD = $dbD->getDiagSolus($idLieu, $idBase, $arrDB['dbname']);
			$arrR = array();
			$idCrit = -1; 
			$idSolus = -1;
			$idProd = -1;
			$i=-1;$j=-1;$k=-1;
			foreach ($arrD as $d) {
				if($idCrit != $d['id_critere']){
					$i ++;
					$idCrit = $d['id_critere'];
					$arrR[$i] = array("id_diag"=>$d['id_diag'],"id_critere"=>$d['id_critere'],"id_type_critere"=>$d['id_type_critere'],"id_reponse"=>$d['id_reponse'],"reponse"=>$d['reponse'],"diagIdLieu"=>$d['diagIdLieu'],"diagLieu"=>$d['diagLieu'],"id_lieu"=>$d['id_lieu'],"lib"=>$d['lib'],"ref"=>$d['ref'],"affirmation"=>$d['affirmation'],"controle"=>$d['controle']);
					$idSolus = -1;
					$idProd = -1;
					$j=-1;$k=-1;
				}
				if($d['id_solution'] && $idSolus != $d['id_solution']){
					$j ++;
					$idSolus = $d['id_solution'];
					$arrR[$i]["solutions"][$j] = array("id_solution"=>$d['id_solution'],"solution"=>$d['solution'], "ref"=>$d['refSolu']);
					$idProd = -1;
					$k=-1;
				}
				if($d['id_produit'] && $idProd != $d['id_produit']){
					$k ++;
					$idProd = $d['id_produit'];
					$arrR[$i]["solutions"][$j]["produits"][$k] = array("id_produit"=>$d['id_produit'],"ref"=>$d['refProd'],"description"=>$d['description'], "marque"=>$d['marque'], "modele"=>$d['modele']);
				}
				if($d['id_cout']){
					$arrR[$i]["solutions"][$j]["produits"][$k]["couts"][] = array("id_cout"=>$d['id_cout'],"unite"=>$d['unite'],"metre_lineaire"=>$d['metre_lineaire'],"metre_carre"=>$d['metre_carre'],"achat"=>$d['achat'],"pose"=>$d['pose'],"solution"=>$d['solution']);
				}						
				if($d['Sid_cout']){
					$arrR[$i]["solutions"][$j]["couts"][] = array("id_cout"=>$d['Sid_cout'],"unite"=>$d['Sunite'],"metre_lineaire"=>$d['Smetre_lineaire'],"metre_carre"=>$d['Smetre_carre'],"achat"=>$d['Sachat'],"pose"=>$d['Spose'],"solution"=>$d['Ssolution']);
				}						
			}
			
			return $arrR;
		    
		}catch (Zend_Exception $e) {
			echo "Récupère exception: " . get_class($e) . "\n";
		    echo "Message: " . $e->getMessage() . "\n";
		}
		return "";
    }    
  	
}	
