<?php
require_once( "../param/ParamAppli.php" );
require_once('../library/php/odtphp/odf.php');
$cc = new GEVU_CalculCouts();

//en attendant la nouvelle version
//on utilise les requête de la version XUL
require_once( "../param/Constantes.php" );
$site = new Site($SITES,"trouvilleERP1");
$grille = new Grille($site);

$idExi = 1;
$c = new Model_DbTable_Gevu_contacts();
$arrContact = $c->findById_exi($idExi);

$idEtab = 6871;
//chargement des données d'établissement
$gra = new Granulat($idEtab,$site);
$art = mysql_fetch_assoc($gra->GetArticleInfo());

$XpInfos = '/tabbox/tabpanels/tabpanel/tabbox/tabpanels/tabpanel/vbox/hbox[2]/grid/columns/column[2]'; 

//chargement du modèle de rapport
$odf = new odf(ROOT_PATH.'\data\rapports\models\rapport1.odt');

/*
//création d'un rapport pour un établissement
$odf->setVars('commune', $gra->TitreParent);
$odf->setVars('établissement', $gra->titre);

//récupération des images
$arrDocs = $gra->GetDocs($idEtab,"1,2");
if(count($arrDocs>0)){
	$odf->setImage('img_etab', $arrDocs[0]->path);
}else{
	//sans image on en met une par defaut
	$odf->setImage('img_etab', '../images/check_no.png');
}

$odf->setVars('rédacteur', $arrContact['prenom'].' '.$arrContact['nom']);
$today = strftime( "%A %d %B %Y" , time());
$odf->setVars('date_redaction', $today);

$odf->setVars('diagnostiqueur', $art['nom']);
//information pas présente actuellement dans la base
$odf->setVars('structure_diagnostiqueur', '???');
$odf->setVars('date_passage', $art['maj']);

//récupération de l'état des lieux
$url = 'http://www.gevu.eu/library/php/ExeAjax.php?f=GetEtatDiag&site=trouvilleERP1&id='.$gra->id;
$strXml = $site->GetCurl($url);
$xmlEtaLieux = simplexml_load_string($strXml);

$odf->setImage('img_niv_reg_moteur', '../images/'.$xmlEtaLieux->Applicables['moteur'].'.png');
$odf->setImage('img_niv_reg_audio', '../images/'.$xmlEtaLieux->Applicables['audio'].'.png');
$odf->setImage('img_niv_reg_visu', '../images/'.$xmlEtaLieux->Applicables['visu'].'.png');
$odf->setImage('img_niv_reg_cog', '../images/'.$xmlEtaLieux->Applicables['cog'].'.png');

$odf->setImage('img_niv_app_moteur', '../images/'.$xmlEtaLieux->Applicables['moteur'].'.png');
$odf->setImage('img_niv_app_audio', '../images/'.$xmlEtaLieux->Applicables['audio'].'.png');
$odf->setImage('img_niv_app_visu', '../images/'.$xmlEtaLieux->Applicables['visu'].'.png');
$odf->setImage('img_niv_app_cog', '../images/'.$xmlEtaLieux->Applicables['cog'].'.png');


$url = 'http://www.gevu.eu/library/php/ExeAjax.php?f=GetTreeObs&site=trouvilleERP1&id='.$gra->id;
$strXml = $site->GetCurl($url);
$xmlObs = simplexml_load_string($strXml);
$obs = $odf->setSegment('obs');
foreach($xmlObs->rows->row->vbox as $r){
	if(!$r['hidden']){
    	$obs->setVars('obs_diag', $r->label['value']." ".$r->hbox->comment);	
	}
}

//récupère les informations de l'établissement
$url = 'http://www.gevu.eu/library/php/ExeAjax.php?f=GetTabForm&site=trouvilleERP1&ParamNom=GetTabForm&id='.$gra->id.'&type=Etab';
$strXml = $site->GetCurl($url);
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
$odf->setVars('etab_cat_erp', $cat[0]['label']);
$odf->setVars('etab_proprio', $dons[0]->textbox[5]['value']);
$odf->setVars('etab_adresse', $dons[0]->textbox[2]['value'].', '.$dons[0]->textbox[3]['value'].', '.$dons[0]->textbox[4]['value']);
*/
//récupère la liste des batiments
$url = 'http://www.gevu.eu/library/php/ExeAjax.php?f=GetTree&site=trouvilleERP1&ParamNom=GetOntoTree&type=bat&id='.$gra->id;
$strXml = $site->GetCurl($url);
$xmlBats = simplexml_load_string($strXml);
$e_bats = $odf->setSegment('etab_bats');
$rBats = $xmlBats->xpath("/tree/treechildren/treeitem/treerow");
/*
foreach($rBats as $r){
	$e_bats->setVars('bat_nom', $r->treecell[1]['label']);
	//récupère les infos du batiment
	$url = 'http://www.gevu.eu/library/php/ExeAjax.php?f=GetTabForm&ParamNom=GetTabForm&site=trouvilleERP1&id='.$r->treecell[0]['label'].'&type=Bat';
	$strXml = $site->GetCurl($url);
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
*/


$bats = $odf->setSegment('bats');
foreach($rBats as $r){
    $bats->setVars('bat_nom', $r->treecell[1]['label']);

    //récupère les références de critère
    $rs = $grille->GetTreeProb($r->treecell[0]['label']."",true);
    $refs ="-1";
	while ($r =  mysql_fetch_assoc($rs)) {
		$refs .= ",'".$r['idCrit']."'";
	}
	//récupère les couts liées aux critères
    $couts = $cc->getCoutsByCriteres($refs);
    
    $bats->setVars('bat_cout_reg', '???');
    $bats->setVars('bat_cout_sou', '???');
    $bats->setVars('bat_cout_tot', '???');
    for ($i = 1; $i <= 3; $i++) {
        $bats->nivs->niv_nom('???'.$i);
        $bats->nivs->niv_cout_reg('???'.$i);
        $bats->nivs->niv_cout_sou('???'.$i);
        $bats->nivs->niv_cout_tot('???'.$i);
    }
    $bats->setVars('niv_cout_reg_tot', '???');
    $bats->setVars('niv_cout_sou_tot', '???');
    $bats->setVars('niv_cout_tot_tot', '???');
    $bats->merge();
}
$odf->mergeSegment($bats);


$odf->setImage('img_cadastre', '../images/kml.png');

$plans = $odf->setSegment('plan_bats');
foreach($rBats as $r){
    $plans->setVars('plan_bat_nom', 'Plan : ' . $r->treecell[1]['label']);
    $plans->setImage('plan_bat_img', '../images/Personnel.png');
    for ($i = 1; $i <= 3; $i++) {
        $plans->plan_nivs->plan_niv_nom('Plan niv :' . $i);
    	$plans->plan_nivs->setImage('plan_niv_img', '../images/visu0.jpg');
    }
    $plans->merge();
}
$odf->mergeSegment($plans);

$probs = $odf->setSegment('probs');
for ($j = 1; $j <= 2; $j++) {
    $probs->setVars('prob_num', 'num : ' . $j);
    $probs->setVars('prob_ariane', 'ariane ' . $j);
    $probs->setImage('prob_img', '../trouville/spip1/IMG/jpg/4655_44274_4-01-10_11-39-07.jpg');
    for ($i = 1; $i <= 3; $i++) {
        $probs->prob->prob_reg('prob_reg :' . $i);
        $probs->prob->prob_const('prob_const :' . $i);
        $probs->prob->prob_mesure('prob_mesure :' . $i);
        $probs->prob->setImage('prob_defici', '../images/audio1.jpg');
    	$probs->prob->setImage('prob_niv_gen', '../images/audio2.jpg');
        $probs->prob->prob_solus('prob_solus :' . $i);
        $probs->prob->prob_prod('prob_prod :' . $i);
        $probs->prob->prob_cout('prob_cout :' . $i);
    }
    $probs->merge();
}
$odf->mergeSegment($probs);

$odf->exportAsAttachedFile();
  

?>