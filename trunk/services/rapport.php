<?php
set_time_limit(3000);
$temps_debut = microtime(true);

//en attendant la nouvelle version
//on utilise les requêtes de la version XUL
require_once( "../param/ParamPage.php" );

require_once( "../param/ParamAppli.php" );
require_once('../library/php/odtphp/odf.php');

try {

$grille = new Grille($objSite);
$xul = new Xul($objSite);

$idExi = 1;

$c = new Model_DbTable_Gevu_contacts();
$arrContact = $c->findById_exi($idExi);

//chargement des données d'établissement
$art = mysql_fetch_assoc($g->GetArticleInfo());

if($pxml!=-1){
	$pxml = stripslashes($pxml);
	$arrP = json_decode($pxml);
}else{
   	$arrP=-1;			
}

$XpInfos = '/tabbox/tabpanels/tabpanel/tabbox/tabpanels/tabpanel/vbox/hbox[2]/grid/columns/column[2]'; 

//récupère le modele
$m = new Model_DbTable_Gevu_docs();
$rm = $m->findByIdDoc($_REQUEST['model']);


//chargement du modèle de rapport
//$pathModel = PathRoot.'/data/rapports/models/RAPPORTDIAGVF.odt';

//echo $pathModel;
//pour le debugage
$ps = str_replace("/home/gevun/www/", "C:/wamp/www/gevu/", $rm['path_source']);
//$ps = $rm['path_source'];
$odf = new odf($ps);


//création d'un rapport pour un établissement
$odf->setVars('commune', utf8_encode($g->TitreParent));

$odf->setVars('etablissement', utf8_encode($g->titre));

//récupération des images
$arrDocs = $g->GetDocs($g->id,"1,2");
if(count($arrDocs)>0){
	if($arrDocs[0]->largeur > $arrDocs[0]->hauteur)
    	$odf->setImage('img_etab', $arrDocs[0]->path,0,9);
    else		
    	$odf->setImage('img_etab', $arrDocs[0]->path,9,0);
}else{
	//sans image on en met une par defaut
	$odf->setImage('img_etab', '../images/check_no.png');
}
$odf->setVars('redacteur', $arrContact['prenom'].' '.$arrContact['nom']);
$today = strftime( "%A %d %B %Y" , time());
$odf->setVars('date_redaction', $today);

$odf->setVars('diagnostiqueur', utf8_encode($art['nom']));
//information pas présente actuellement dans la base
$odf->setVars('structure_diagnostiqueur', '???');
$odf->setVars('date_passage', utf8_encode($art['maj']));

//récupération de l'état des lieux
$strXml = utf8_encode($g->GetEtatDiag());
$xmlEtaLieux = simplexml_load_string($strXml);

$odf->setImage('img_niv_reg_moteur', '../images/'.$xmlEtaLieux->Applicables['moteur'].'.png');
$odf->setImage('img_niv_reg_audio', '../images/'.$xmlEtaLieux->Applicables['audio'].'.png');
$odf->setImage('img_niv_reg_visu', '../images/'.$xmlEtaLieux->Applicables['visu'].'.png');
$odf->setImage('img_niv_reg_cog', '../images/'.$xmlEtaLieux->Applicables['cog'].'.png');

$odf->setImage('img_niv_app_moteur', '../images/'.$xmlEtaLieux->Applicables['moteur'].'.png');
$odf->setImage('img_niv_app_audio', '../images/'.$xmlEtaLieux->Applicables['audio'].'.png');
$odf->setImage('img_niv_app_visu', '../images/'.$xmlEtaLieux->Applicables['visu'].'.png');
$odf->setImage('img_niv_app_cog', '../images/'.$xmlEtaLieux->Applicables['cog'].'.png');


$odf->setImage('img_niv_reg_moteur1', '../images/'.$xmlEtaLieux->Applicables['moteur'].'.png');
$odf->setImage('img_niv_reg_audio1', '../images/'.$xmlEtaLieux->Applicables['audio'].'.png');
$odf->setImage('img_niv_reg_visu1', '../images/'.$xmlEtaLieux->Applicables['visu'].'.png');
$odf->setImage('img_niv_reg_cog1', '../images/'.$xmlEtaLieux->Applicables['cog'].'.png');

$odf->setImage('img_niv_app_moteur1', '../images/'.$xmlEtaLieux->Applicables['moteur'].'.png');
$odf->setImage('img_niv_app_audio1', '../images/'.$xmlEtaLieux->Applicables['audio'].'.png');
$odf->setImage('img_niv_app_visu1', '../images/'.$xmlEtaLieux->Applicables['visu'].'.png');
$odf->setImage('img_niv_app_cog1', '../images/'.$xmlEtaLieux->Applicables['cog'].'.png');

//récupère les informations de l'établissement
$strXml =  utf8_encode($grille->GetXulTab('Etab', $g->id, 'Etab'));
$xmlEtab = simplexml_load_string($strXml);

$dons = $xmlEtab->xpath($XpInfos);
$erp = $xmlEtab->xpath($XpInfos."/menulist[1]/menupopup/menuitem[@selected='true']/@label");
$structure = "???";
if($erp[0]['label']=='Oui'){
	$structure = "Etablissement recevant du public (ERP)";
}
$odf->setVars('etab_structure', $structure);
$cat = $xmlEtab->xpath($XpInfos."/menulist[3]/menupopup/menuitem[@selected='true']/@label");
$odf->setVars('etab_cat_erp', utf8_encode($cat[0]['label']));
$odf->setVars('etab_proprio', utf8_encode($dons[0]->textbox[5]['value']));
$odf->setVars('etab_adresse', $dons[0]->textbox[2]['value'].', '.$dons[0]->textbox[3]['value'].', '.$dons[0]->textbox[4]['value']);


$e_bats = $odf->setSegment('etab_bats');
//initialise la liste des éléments de diagnostics
$arrEleDiag = array();

//récupère la liste des batiments
$strXml = utf8_encode($grille->GetTree('bat',$g->id));
$xmlBats = simplexml_load_string($strXml);
$rBats = $xmlBats->xpath("/tree/treechildren/treeitem/treerow");
foreach($rBats as $r){
	$e_bats->setVars('bat_nom', $r->treecell[1]['label']);
	//récupère les infos du batiment
	//$url = WebRoot.'/library/php/ExeAjax.php?f=GetTabForm&ParamNom=GetTabForm&site='.$objSite->id.'&id='.$r->treecell[0]['label'].'&type=Bat';
	$strXml = utf8_encode($grille->GetXulTab('Bat', $r->treecell[0]['label'], 'Bat'));
	$xmlBat = simplexml_load_string($strXml);
	$dons = $xmlBat->xpath($XpInfos);
	
	$opt = $xmlBat->xpath($XpInfos."/menulist[7]/menupopup/menuitem[@selected='true']/@label");
	$e_bats->setVars('bat_classement', $opt[0]['label']);
	$opt = $xmlBat->xpath($XpInfos."/menulist[5]/menupopup/menuitem[@selected='true']/@label");
	$e_bats->setVars('bat_zppaup', $opt[0]['label']);
	$opt = $xmlBat->xpath($XpInfos."/menulist[8]/menupopup/menuitem[@selected='true']/@label");
	$e_bats->setVars('bat_nb_niveau', $opt[0]['label']);
	$opt = $xmlBat->xpath($XpInfos."/menulist[12]/menupopup/menuitem[@selected='true']/@label");
	$e_bats->setVars('bat_nb_ascenseur', $opt[0]['label']);
	$opt = $xmlBat->xpath($XpInfos."/menulist[14]/menupopup/menuitem[@selected='true']/@label");
	$e_bats->setVars('bat_parcking_in', $opt[0]['label']);
	$opt = $xmlBat->xpath($XpInfos."/menulist[15]/menupopup/menuitem[@selected='true']/@label");
	$e_bats->setVars('bat_parcking_out', $opt[0]['label']);
	$e_bats->setVars('bat_date_construction', $dons[0]->textbox[13]['value']);
	$e_bats->merge();
	
	//enregistre l'élément
	$arrEleDiag[] = array("id"=>$r->treecell[0]['label']."","nom"=>$r->treecell[1]['label']."");
	
}

//récupère la liste des parcelles
$strXml = utf8_encode($grille->GetTree('parcelle',$g->id));
$xmlBats = simplexml_load_string($strXml);
$rBats = $xmlBats->xpath("/tree/treechildren/treeitem/treerow");
foreach($rBats as $r){
	$e_bats->setVars('bat_nom', $r->treecell[1]['label']);
	//récupère les infos du batiment
	//$url = WebRoot.'/library/php/ExeAjax.php?f=GetTabForm&ParamNom=GetTabForm&site='.$objSite->id.'&id='.$r->treecell[0]['label'].'&type=Bat';
	$strXml = utf8_encode($grille->GetXulTab('Parcelle', $r->treecell[0]['label'], 'Parcelle'));
	$xmlBat = simplexml_load_string($strXml);
	$dons = $xmlBat->xpath($XpInfos);
	
	$opt = $xmlBat->xpath($XpInfos."/menulist[7]/menupopup/menuitem[@selected='true']/@label");
	$e_bats->setVars('bat_classement', $opt[0]['label']);
	$opt = $xmlBat->xpath($XpInfos."/menulist[5]/menupopup/menuitem[@selected='true']/@label");
	$e_bats->setVars('bat_zppaup', $opt[0]['label']);
	$opt = $xmlBat->xpath($XpInfos."/menulist[8]/menupopup/menuitem[@selected='true']/@label");
	$e_bats->setVars('bat_nb_niveau', $opt[0]['label']);
	$opt = $xmlBat->xpath($XpInfos."/menulist[12]/menupopup/menuitem[@selected='true']/@label");
	$e_bats->setVars('bat_nb_ascenseur', $opt[0]['label']);
	$opt = $xmlBat->xpath($XpInfos."/menulist[14]/menupopup/menuitem[@selected='true']/@label");
	$e_bats->setVars('bat_parcking_in', $opt[0]['label']);
	$opt = $xmlBat->xpath($XpInfos."/menulist[15]/menupopup/menuitem[@selected='true']/@label");
	$e_bats->setVars('bat_parcking_out', $opt[0]['label']);
	$e_bats->setVars('bat_date_construction', $dons[0]->textbox[13]['value']);
	$e_bats->merge();

	//enregistre l'élément
	$arrEleDiag[] = array("id"=>$r->treecell[0]['label']."","nom"=>$r->treecell[1]['label']."");
}

$odf->mergeSegment($e_bats);


$bats = $odf->setSegment('bats');
$plans = $odf->setSegment('plan_bats');
foreach($arrEleDiag as $r){
	$idBat = $r['id'];
    $gBat = new Granulat($idBat,$objSite);
	
	$plans->setVars('plan_bat_nom', 'Plan : ' . utf8_encode($gBat->titre));
	
	$arrDocs = $gBat->GetDocs($gBat->id,"1,2");
	if(count($arrDocs)>0){
		if($arrDocs[0]->largeur > $arrDocs[0]->hauteur)
	    	$plans->setImage('plan_bat_img', $arrDocs[0]->path,0,9);	    	
	    else		
	    	$plans->setImage('plan_bat_img', $arrDocs[0]->path,9,0);
	}else{
    	$plans->setImage('plan_bat_img', '../images/check_no.png');	
	}
	
	//calcul le cout pour le batiments
	$arrCout = getCout($g, $idBat, $arrP);
		
	$bats->setVars('bat_nom', $r['nom']);
	$bats->setVars('bat_cout_reg', $arrCout["reg"]);
    $bats->setVars('bat_cout_sou', $arrCout["sou"]);
    $bats->setVars('bat_cout_tot', $arrCout["reg"]+$arrCout["sou"]);
    
    //récupère les niveaux
    $cReg=0; $cSou=0;
    $arrNivs = $gBat->GetEnfants(true);
    foreach($arrNivs as $niv){
        $bats->nivs->niv_nom(utf8_encode($niv->titre));
		$arrCout = getCout($g, $niv->id, $arrP);
        $bats->nivs->niv_cout_reg($arrCout["reg"]);
        $bats->nivs->niv_cout_sou($arrCout["sou"]);
        $bats->nivs->niv_cout_tot($arrCout["reg"]+$arrCout["sou"]);
        $cReg+=$arrCout["reg"]; $cSou+=$arrCout["sou"];
        
		$plans->plan_nivs->plan_niv_nom('Plan niveau :' . utf8_encode($niv->titre));
		$arrDocs = $niv->GetDocs($niv->id,"1,2");
		if(count($arrDocs)>0){
			if($arrDocs[0]->largeur > $arrDocs[0]->hauteur)
		    	$plans->plan_nivs->setImage('plan_niv_img', $arrDocs[0]->path,0,9);	    	
		    else		
		    	$plans->plan_nivs->setImage('plan_niv_img', $arrDocs[0]->path,9,0);        	
    	}else{
        	$plans->plan_nivs->setImage('plan_niv_img', '../images/check_no.png');
		}
        
    }
    $bats->setVars('niv_cout_reg_tot', $cReg);
    $bats->setVars('niv_cout_sou_tot', $cSou);
    $bats->setVars('niv_cout_tot_tot', $cReg+$cSou);

    $bats->merge();
    $plans->merge();

}
$odf->mergeSegment($plans);
$odf->mergeSegment($bats);


$odf->setImage('img_cadastre', '../images/kml.png');

$probs = $odf->setSegment('probs');

//pour chaque batiments
$j=0;
$z=0;
$arrIdParent = array();
foreach($arrEleDiag as $r){

	//récupère les problèmes
	//$url = WebRoot.'/library/php/ExeAjax.php?f=GetTreeProb&site='.$objSite->id.'&id='.$r->treecell[0]['label'].'&type=Bat';
	$strXml = utf8_encode($grille->GetTreeProb($r['id']));
	$xmlProb = simplexml_load_string($strXml);
	
	foreach($xmlProb->rows->row as $rP){
		$refsProb = explode("*", $rP->vbox[0]->label["id"]);
		//si le hbox n'est pas vide on passe à un nouveau lieu
		if($rP->vbox[2]->hbox){
			//on enregistre le problème précédent
			$j++;
			if($j>1){
				$probs->merge();
			}
			
			//on crée le nouveau problème
			$idRubLieu = substr($rP->vbox[2]->hbox->label["id"],9);
			$gProb = new Granulat($idRubLieu, $objSite);
			
			//$strAriane = utf8_encode($gProb->GetFilAriane());
			$strAriane = utf8_encode($gProb->TitreParent." | ".$gProb->titre);
			//echo "-".$j." - ".$strAriane."<br/>"; 
			//$temps_fin = microtime(true);
			//echo 'Temps : '.round($temps_fin - $temps_debut, 4)." s. <br/>";
			$probs->setVars('prob_num', $j);     
		    $probs->setVars('prob_ariane', $strAriane);
		    
		
		    if($gProb->IdParent && !in_array($gProb->IdParent,$arrIdParent)){
		    	$arrIdParent[] = $gProb->IdParent;
		    	$arrDocs = $gProb->GetDocs($gProb->IdParent,"1,2");
				if(count($arrDocs)>0){
	
					$maxTof = false;			
					foreach($arrDocs as $Docs){	
						if(!$maxTof){
							$maxTof = true;			
							if($Docs->largeur > $Docs->hauteur)
						    	$probs->probimgs->setImage('prob_img', $Docs->path,0,9);	    	
						    else		
						    	$probs->probimgs->setImage('prob_img', $Docs->path,9,0);
						    $probs->probimgs->merge();
							//pour les traces
							//$z++;
							//echo "  - ".$z." - ".$Docs->path."<br/>"; 
						}
					}        	
				}else{
				   	$probs->probimgs->setImage('prob_img', '../images/check_no.png');	    	
				    $probs->probimgs->merge();
				}
			}else{
			   	$probs->probimgs->setImage('prob_img', '../images/check_yes.png');	    	
			    $probs->probimgs->merge();
		    }
		}
	    //$_SESSION['ForceCalcul'] = true;
	    //$strXml = $gProb->GetEtatDiag(true, true);
	    //$strXml = $grille->GetEtatDiagListeTot($gProb->id);
	    //$Probs = simplexml_load_string($strXml);
		
		if(count($rP->vbox[4]->hbox)==2){
			$tof = $rP->vbox[4]->hbox[1]->label[2]['value'];
	    	$idDonRef = substr($rP->vbox[4]->hbox[0]->label[1]['onclick'],14,-2);
	    	$const = $rP->vbox[4]->hbox[0]->label[0]['value'];
	    	$const = substr($const,strpos($const, ":")+1);
	    	$code = mb_detect_encoding($const, "auto");
	    	if($code != "UTF-8" && $code != "ASCII"){
	    		$const  = "";
	    	}
		    //récupère la légende du problème
			$xmlLeg = utf8_encode($grille->GetXulLegendeControle($idDonRef,$objSite->infos["GRILLE_CONTROL_".$_SESSION['version']]));
		    $LegProb = simplexml_load_string($xmlLeg);
		}else{
			$tof = $rP->vbox[4]->hbox->label[2]['value'];	
		}
		$type = $LegProb->hbox[0]->label[0]['value'];
		$cri = $LegProb->hbox[0]->label[1]['value'];
	    $probs->rowprob->prob_const($const);
	    $probs->rowprob->prob_mesure('prob_mesure :'.$j);
	    
	    //ajoute les observations
	    $rs = $grille->GetCritereObs($idRubLieu,$cri);
		while($rO = mysql_fetch_assoc($rs)){
			if($rO['ComVal3']!=""){
		    	$probs->rowprob->probobs->obs_diag(utf8_encode($rO['ComVal3']));
				$probs->rowprob->probobs->merge();
			}
		}
	
		//ajoute les images réglementaires
		for ($i = 2; $i < count($LegProb->hbox[0]->label); $i++) { 
	    	$src = getImgReg($type."", $LegProb->hbox[0]->label[$i]['value']."");
	    	$probs->rowprob->regimg->setImage('prob_reg', $src);
			$probs->rowprob->regimg->merge();
	    }
	
	    foreach($LegProb->hbox[1]->image as $img){
	    	$src = str_replace(WebRoot,PathRoot,$img['src']);
	    	$probs->rowprob->probimg->setImage('prob_defici', $src);;
			$probs->rowprob->probimg->merge();
	    }
	    //$probs->rowprob->setImage('prob_niv_gen', '../images/audio2.jpg');
	    $arrSolus = getCout($g, $idRubLieu, $arrP, $refsProb[2], true);
	    foreach($arrSolus as $solus){
		    $probs->rowprob->probsolus->prob_solus($solus[0]);
		    $probs->rowprob->probsolus->prob_prod($solus[1]);
		    $probs->rowprob->probsolus->prob_cout($solus[2]);    
			$probs->rowprob->probsolus->merge();
	    }
	    
		$probs->rowprob->merge();
	}
	$probs->merge();
}
$odf->mergeSegment($probs);

/**/

$odf->exportAsAttachedFile();

}catch (SegmentException  $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    var_dump($e);
}

function getCout($g, $id, $arrP, $idDon=-1, $lib=false){
	if($idDon==-1){
		$ids = $g->GetEnfantIds($id).$id."*";
	}else{
		$ids = -1;
	}
	if($lib){
		$arrLib = array();
		$dbSolus = new Model_DbTable_Gevu_solutions();
		$dbProds = new Model_DbTable_Gevu_produits();
	}
	$cReg=0; $cSou=0;
	foreach($arrP as $solus){
		foreach($solus->couts as $couts){
			foreach($couts as $cout){
				if(strrpos($ids, $cout->idRub.'*') || $cout->idDon==$idDon){
					$c=0;
					foreach($cout->Couts as $ct){
						$c += $ct->val*$ct->q;
					}
					if($cout->regle)$cReg += $c; else $cSou += $c;
					if($lib){
						$s = $dbSolus->findById_solution($solus->idSolus);
						$arrLib[]=array($s['lib'],"",$c);
					}
					foreach($cout->SousCouts as $souscouts){
						foreach($souscouts as $scout){
							$c=0;
							foreach($scout->Couts as $sct){
								$c += $sct->val*$sct->q;
							}
							if($scout->regle)$cReg += $c; else $cSou += $c;
							if($lib){
								$s = $dbProds->findById_produit($scout->idProd);
								$arrLib[]=array("",$s['ref']." : ".$s['description'],$c);
							}
							
						}
					}
				}
			}
		}
	}
	if($lib){
		if(count($arrLib)==0){
			$arrLib[]=array("","",$cReg+$cSou);
		}
		return $arrLib;
	}
	return array("reg"=>$cReg, "sou"=>$cSou);
	
}

function getImgReg($type, $regle){
	if($type=="Souhaitable"){
		$type="S";
	}else{
		$type="R";
	}
	$path = '';
	if($regle=="ERP_IOP"){
		$path = '../images/'.$type.'ERP.png';
	}
	if($regle=="ERP_IOP existant"){
		$path = '../images/'.$type.'ERPexistant.png';
	}
	if($regle=="Voirie"){
		$path = '../images/'.$type.'VOIRIE.png';
	}
	if($regle=="Logement"){
		$path = '../images/'.$type.'LOGEMENT.png';
	}
	if($regle=="Travail"){
		$path = '../images/'.$type.'CT.png';
	}
	if($regle=="Modalité particulière"){
		$path = '../images/'.$type.'MODPART.png';
	}
	
	if($path==''){
		$path = '../images/check_no.png';	
	}
	
	return $path;
	
}

?>