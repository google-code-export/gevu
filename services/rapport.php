<?php
require_once( "../param/ParamAppli.php" );
require_once('../library/php/odtphp/odf.php');

//en attendant la nouvelle version
//on utilise les requêtes de la version XUL
require_once( "../param/ParamPage.php" );

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

//chargement du modèle de rapport
$odf = new odf(ROOT_PATH.'\data\rapports\models\RAPPORTDIAGVFtot.odt');

//création d'un rapport pour un établissement
$odf->setVars('commune', utf8_encode($g->TitreParent));

$odf->setVars('etablissement', utf8_encode($g->titre));


//récupération des images
$arrDocs = $g->GetDocs($g->id,"1,2");
if(count($arrDocs)>0){
	$odf->setImage('img_etab', $arrDocs[0]->path);
}else{
	//sans image on en met une par defaut
	$odf->setImage('img_etab', '../images/check_no.png');
}
$odf->setVars('redacteur', $arrContact['prenom'].' '.$arrContact['nom']);
$today = strftime( "%A %d %B %Y" , time());
$odf->setVars('date_redaction', utf8_encode($today));

$odf->setVars('diagnostiqueur', utf8_encode($art['nom']));
//information pas présente actuellement dans la base
$odf->setVars('structure_diagnostiqueur', '???');
$odf->setVars('date_passage', utf8_encode($art['maj']));


//récupération de l'état des lieux
$url = WebRoot.'/library/php/ExeAjax.php?f=GetEtatDiag&site='.$objSite->id.'&id='.$g->id;
$strXml = $objSite->GetCurl($url);
$xmlEtaLieux = simplexml_load_string($strXml);

$odf->setImage('img_niv_reg_moteur', '../images/'.$xmlEtaLieux->Applicables['moteur'].'.png');
$odf->setImage('img_niv_reg_audio', '../images/'.$xmlEtaLieux->Applicables['audio'].'.png');
$odf->setImage('img_niv_reg_visu', '../images/'.$xmlEtaLieux->Applicables['visu'].'.png');
$odf->setImage('img_niv_reg_cog', '../images/'.$xmlEtaLieux->Applicables['cog'].'.png');

$odf->setImage('img_niv_app_moteur', '../images/'.$xmlEtaLieux->Applicables['moteur'].'.png');
$odf->setImage('img_niv_app_audio', '../images/'.$xmlEtaLieux->Applicables['audio'].'.png');
$odf->setImage('img_niv_app_visu', '../images/'.$xmlEtaLieux->Applicables['visu'].'.png');
$odf->setImage('img_niv_app_cog', '../images/'.$xmlEtaLieux->Applicables['cog'].'.png');


$rs = $grille->GetTreeObs($id,false, true);
$obs = $odf->setSegment('obs');
while($r = mysql_fetch_assoc($rs)){
   	$obs->setVars('obs_diag', utf8_encode($r['titreRubPar']." | ".$r['titreRub']." : ".$r['ComVal3']));	
	$obs->merge();
}
$odf->mergeSegment($obs);

//récupère les informations de l'établissement
$url = WebRoot.'/library/php/ExeAjax.php?f=GetTabForm&site='.$objSite->id.'&ParamNom=GetTabForm&id='.$g->id.'&type=Etab';
$strXml = $objSite->GetCurl($url);
//$strXml = $gra->GetXmlGrillesValues(-1, true);
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

//récupère la liste des batiments
$url = WebRoot.'/library/php/ExeAjax.php?f=GetTree&site='.$objSite->id.'&ParamNom=GetOntoTree&type=bat&id='.$g->id;
$strXml = $objSite->GetCurl($url);
$xmlBats = simplexml_load_string($strXml);

$e_bats = $odf->setSegment('etab_bats');
$rBats = $xmlBats->xpath("/tree/treechildren/treeitem/treerow");
foreach($rBats as $r){
	$e_bats->setVars('bat_nom', $r->treecell[1]['label']);
	//récupère les infos du batiment
	$url = WebRoot.'/library/php/ExeAjax.php?f=GetTabForm&ParamNom=GetTabForm&site='.$objSite->id.'&id='.$r->treecell[0]['label'].'&type=Bat';
	$strXml = $objSite->GetCurl($url);
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
}
$odf->mergeSegment($e_bats);


$bats = $odf->setSegment('bats');
$plans = $odf->setSegment('plan_bats');
foreach($rBats as $r){
	$idBat = $r->treecell[0]['label']."";
    $gBat = new Granulat($idBat,$objSite,true);
	
	$plans->setVars('plan_bat_nom', 'Plan : ' . utf8_encode($gBat->titre));
	if(count($gBat->arrDoc)>0){
    	$plans->setImage('plan_bat_img', $gBat->arrDoc[0]->path);	
	}else{
    	$plans->setImage('plan_bat_img', '../images/Personnel.png');	
	}
	
	//calcul le cout pour le batiments
	$arrCout = getCout($g, $idBat, $arrP);
		
    $bats->setVars('bat_nom', $r->treecell[1]['label']);
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
    	if(count($niv->arrDoc)>0){
        	$plans->plan_nivs->setImage('plan_niv_img', $niv->arrDoc[0]->path);
    	}else{
        	$plans->plan_nivs->setImage('plan_niv_img', '../images/Personnel.png');
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
//récupère les problèmes
$url = WebRoot.'/library/php/ExeAjax.php?f=GetTreeProb&site='.$objSite->id.'&id='.$r->treecell[0]['label'].'&type=Bat';
$strXml = $objSite->GetCurl($url);
$xmlProb = simplexml_load_string($strXml);
$j=1;
foreach($xmlProb->rows->row as $r){
	$refsProb = explode("*", $r->vbox[0]->label["id"]);
	if($r->vbox[2]->hbox){
		$idRubLieu = substr($r->vbox[2]->hbox->label["id"],9);
		$gProb = new Granulat($idRubLieu, $objSite);
		//$strAriane = utf8_encode($gProb->GetFilAriane());
		$strAriane = utf8_encode($gProb->TitreParent." | ".$gProb->titre);
		if($j>1)$probs->merge();
	    $probs->setVars('prob_num', $j);     
	    $probs->setVars('prob_ariane', $strAriane);
	}
	
    //$_SESSION['ForceCalcul'] = true;
    //$strXml = $gProb->GetEtatDiag(true, true);
    //$strXml = $grille->GetEtatDiagListeTot($gProb->id);
    //$Probs = simplexml_load_string($strXml);
    
	if(count($r->vbox[4]->hbox)==2){
		$tof = $r->vbox[4]->hbox[1]->label[2]['value'];
    	$idDonRef = substr($r->vbox[4]->hbox[0]->label[1]['onclick'],14,-2);
    	$const = substr($r->vbox[4]->hbox[0]->label[0]['value'],16);
	    //récupère la légende du problème
		$xmlLeg = utf8_encode($grille->GetXulLegendeControle($idDonRef,$objSite->infos["GRILLE_CONTROL_".$_SESSION['version']]));
	    $LegProb = simplexml_load_string($xmlLeg);
	}else{
		$tof = $r->vbox[4]->hbox->label[2]['value'];	
	}
    
    //$arrDoc = $gProb->GetDocs($gProb->id,"1,2");
    //problème de la place de l'image en dehors du tableau = image du diag et pas du problème
    if($tof!='Photo : Non'){
    	$probs->rowprob->setImage('prob_img', $arrDoc[0]->path); 
    }else{
    	$probs->rowprob->setImage('prob_img', '../images/Personnel.png'); 
    }
    
	$probs->rowprob->prob_reg($LegProb->hbox[0]->label[2]['value']);
    $probs->rowprob->prob_const($const);
    $probs->rowprob->prob_mesure('prob_mesure :'.$j);
    foreach($LegProb->hbox[1]->image as $img){
    	$src = str_replace(WebRoot,PathRoot,$img['src']);
    	$probs->rowprob->probimg->setImage('prob_defici', $src);;
		$probs->rowprob->probimg->merge();
    }
    //$probs->rowprob->setImage('prob_niv_gen', '../images/audio2.jpg');
    $arrSolus = getCout($g, $idRubLieu, $arrP, true);
    foreach($arrSolus as $solus){
	    $probs->rowprob->probsolus->prob_solus($solus[0]);
	    $probs->rowprob->probsolus->prob_prod($solus[1]);
	    $probs->rowprob->probsolus->prob_cout($solus[2]);    
		$probs->rowprob->probsolus->merge();
    }
    
    
    $j++;
	$probs->rowprob->merge();
}
$probs->merge();
$odf->mergeSegment($probs);


$odf->exportAsAttachedFile();
  
function getCout($g, $id, $arrP, $lib=false){
	$ids = $g->GetEnfantIds($id).$id;
	if($lib){
		$arrLib = array();
		$dbSolus = new Model_DbTable_Gevu_solutions();
		$dbProds = new Model_DbTable_Gevu_produits();
	}
	$cReg=0; $cSou=0;
	foreach($arrP as $solus){
		foreach($solus->couts as $couts){
			foreach($couts as $cout){
				if(strrpos($ids, $cout->idRub.',')){
					$c=0;
					foreach($cout->Couts as $ct){
						$c += $ct->val*$ct->q;
					}
					if($cout->regle)$cReg += $c; else $cSou += $c;
					if($lib){
						$s = $dbSolus->findById_solution($solus->id);
						$arrLib[]=array($s['lib'],"",$c);
					}
					foreach($cout->SousCouts as $souscouts){
						foreach($souscouts as $scout){
							foreach($scout->Couts as $sct){
								$c += $sct->val*$sct->q;
							}
							if($scout->regle)$cReg += $c; else $cSou += $c;
							if($lib){
								$s = $dbProds->findById_solution($solus->id);
								$arrLib[]=array($s['lib'],"",$c);
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



?>