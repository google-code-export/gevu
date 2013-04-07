<?php
class GEVU_Rapport extends GEVU_Site{

	var $arrMC;
	var $odf;
	var $pathDebug="C:\wamp\www\gevu\data\lieux";
	
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
				
		//initialisation des objets	
		$this->getDb($idBase);
		$dbE = new Models_DbTable_Gevu_exis();
		$dbDoc = new Models_DbTable_Gevu_docs($this->db);
		$dbMC = new Models_DbTable_Gevu_motsclefs($this->db);
		$dbL = new Models_DbTable_Gevu_lieux($this->db);
		$dbCnt = new Models_DbTable_Gevu_contacts($this->db);
		$dbDS = new Models_DbTable_Gevu_diagnosticsxsolutions($this->db);
		$dbProb = new Models_DbTable_Gevu_problemes($this->db);
		$dbInst = new Models_DbTable_Gevu_instants($this->db);
		$dbRapport = new Models_DbTable_Gevu_rapports($this->db);
		$dbRapport = new Models_DbTable_Gevu_rapports($this->db);
		$dbDocRapport = new Models_DbTable_Gevu_docsxrapports($this->db);
		$oDiag = new GEVU_Diagnostique($this->db);
		
		//récupère l'auteur 
		$arrExi = $dbE->findById_exi($idExi);
				
		//récupère le Modele
		$rm = $dbDoc->findByIdDoc($idModele);

		//récupère les mots clefs
		$this->arrMC = $dbDoc->getAll();
		
		//charge le modèle
		//pour le debugage
		//$ps = str_replace("/home/gevu/www/", "C:/wamp/www/gevu/", $rm['path_source']);
		$ps = $rm['path_source'];
		$this->odf = new odf($ps);		
		
		//récupération de l'état des lieux
		$arrEtatLieux = $oDiag->getNodeRelatedData($idLieu, $idExi, $idBase);
		
		//récupère le fil d'ariane du lieu
		$arrAriane = $arrEtatLieux["ariane"];
		$ariane = "";
		foreach ($arrAriane as $l) {
			$ariane .= $l['lib']." - ";			
		}
		$this->trace($ariane);
		
		$this->odf->setVars('commune', $ariane);
		$nomEtab = $arrAriane[0]['parLib'];
		$this->odf->setVars('etablissement', $nomEtab);
		
		//récupération des images
		$arrDocs = $arrEtatLieux["Models_DbTable_Gevu_docsxlieux"];
		//ajout des images au modèle
		$this->odf = $this->setImage($this->odf, 'img_etab', $arrDocs);
		
		//ajout des coordonnée de l'auteur
		$this->odf->setVars('redacteur', $arrExi['nom'].' '.$arrExi['mail']);
		$today = strftime( "%A %d %B %Y" , time());
		$this->odf->setVars('date_redaction', $today);
		
		//récupère la dernière campagne de diagnostique
		$dbDiag = new Models_DbTable_Gevu_diagnostics($this->db);		
		$arrLastDiag = $dbDiag->findLastDiagForLieu($idLieu);
		
		$this->odf->setVars('diagnostiqueur', $arrLastDiag[0]['nom']);
		//information pas présente actuellement dans la base
		$this->odf->setVars('structure_diagnostiqueur', '???');
		$this->odf->setVars('date_passage', $arrLastDiag[0]['maintenant']);
		
		$arrDiag = $arrEtatLieux["___diagnostics"]["diag"];
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
		$dons = $arrEtatLieux["Models_DbTable_Gevu_etablissements"][0];
		$geo = $arrEtatLieux["Models_DbTable_Gevu_geos"][0];
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
		$arrBat = $dbL->getChildForTypeControle($idLieu, 45);
		foreach($arrBat as $r){
			//récupère les infos du batiment
			$arrEtatBat = $oDiag->getNodeRelatedData($r['id_lieu'], $idExi, $idBase);
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
		$arrParc = $dbL->getChildForTypeControle($idLieu, 51);
		foreach($arrParc as $r){
			//récupère les infos du batiment
			$arrEtatParc = $oDiag->getNodeRelatedData($r['id_lieu'], $idExi, $idBase);
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
			$arrDocs = $r["Models_DbTable_Gevu_docsxlieux"];
			//ajout des images au modèle
			$plans = $this->setImage($plans, 'plan_bat_img', $arrDocs);
			if(count($arrDocs)>0){
				$plans->setVars('plan_bat_nom', 'Plan : ' . $arrDocs[0]['titre']);
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
			foreach($arrNivs as $niv){
				$bats->nivs->niv_nom($niv["lib"]);
				$arrCoutNiv = $dbDS->getCoutsByIdLieu($niv['id_lieu']);
				$bats->nivs->niv_cout_reg($arrCoutNiv["reg"]);
				$bats->nivs->niv_cout_sou($arrCoutNiv["sou"]);
				$bats->nivs->niv_cout_tot($arrCoutNiv["reg"]+$arrCoutNiv["sou"]);
				$cReg+=$arrCoutNiv["reg"]; 
				$cSou+=$arrCoutNiv["sou"];
				if($this->trace){
					echo $niv["lib"]."<br/>";
					$temps_fin = microtime(true);
					echo 'Temps : '.round($temps_fin - $this->temps_debut, 4)." s. <br/>";
				}
				
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

		//dégugage du contenu xml
		//header("Content-Type:text/xml");
		//echo $this->odf->getContentXml();
		
		//on enregistre le fichier
		$idInst = $dbInst->ajouter(array("id_exi"=>$idExi,"nom"=>"Création rapport"));
		
		$nomFic = preg_replace('/[^a-zA-Z0-9-_\.]/','', $nomEtab);
		$nomFic = $idModele."_".$idLieu."_".$nomFic."_".$idInst.".odt";
		//copie le fichier dans le répertoire data
		$newfile = ROOT_PATH."/data/rapports/documents/".$nomFic;
		copy($this->odf->tmpfile, $newfile);
		
		//on enregistre le doc dans la base
		$idDoc = $dbDoc->ajouter(array("id_instant"=>$idInst,"url"=>WEB_ROOT."/data/rapports/documents/".$nomFic,"titre"=>$nomFic,"path_source"=>$newfile,"content_type"=>"application/vnd.oasis.opendocument.text"));
		$idRap = $dbRapport->ajouter(array("id_lieu"=>$idLieu, "id_exi"=>$idExi, "lib"=>$nomFic));
		$dbDocRapport->ajouter(array("id_doc"=>$idDoc,"id_rapport"=>$idRap));
		
		//on propose de télécharger le rapport
		$this->odf->exportAsAttachedFile($nomFic);
		
		$this->trace("FIN");
		
		}catch (SegmentException  $e) {
			echo "Récupère exception: " . get_class($e) . "\n";
			echo "Message: " . $e->getMessage() . "\n";
			var_dump($e);
		}		
		
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
	
		if(count($arrDocs)>0){
			$doc = $arrDocs[0];
			if($doc["content-type"]='image/gif' || $doc["content-type"]='image/jpeg' || $doc["content-type"]='image/png'){
				//récupère la taille de l'image
				if($this->pathDebug)$path = str_replace("/home/gevu/www/data/lieux",$this->pathDebug, $doc['path_source']);
				else $path = $doc['path_source'];
				/*
				$size = getimagesize($doc['url']);
				if($size[0] > $size[1])
					$oOdf->setImage($balise, $path, 0, 9);						
				else
				*/
					$oOdf->setImage($balise, $path, 9, 0);
			}
			$this->trace("setImage : ".$path);
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
