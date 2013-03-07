<?php

//session_start();
require_once("../../param/ParamPage.php");

if(isset($_GET['f'])){
    $fonction = $_GET['f'];
}
else
    $fonction = '';
    
if(isset($_GET['themes'])){
    $themes = $_GET['themes'];
}
else
    $themes = '';
    
if(isset($_GET['theme'])){
    $themes = $_GET['theme'];
}

switch ($fonction) {
    case 'get_arbo_territoire':
        $resultat = get_arbo_territoire($g->id,$objSite);
        Header("content-type: application/xml");
        break;    
}


function get_arbo_territoire($idRub,$objSite,$niv=0) {
    
    $idGrille=$objSite->infos["GRILLE_TERRE"];
    $grille = new Grille($objSite);
    
    $path = PathRoot."/bdd/carto/ArboTerritoire_".$objSite->id."_".$idRub.".xml";
    $xml = $objSite->GetFile($path);
    if(!$xml){
        //récupération des territoires du granulat
        $sql = "SELECT dc.valeur, dc.champ, da.id_donnee, r.titre rTitre, r.id_rubrique
                , m.titre mTitre, m.id_mot
            FROM spip_articles a
                INNER JOIN spip_rubriques r ON r.id_rubrique = a.id_rubrique
                    AND r.id_parent =".$idRub."
                INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
                INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
                    AND fd.id_form =".$idGrille."
                INNER JOIN spip_forms_donnees_champs dc ON dc.id_donnee = da.id_donnee
                    AND champ = 'mot_1'
                INNER JOIN spip_mots m ON m.id_mot = dc.valeur
            ORDER BY mTitre, rTitre";
        //echo $sql."<br/>";
        $DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
        $DB->connect();
        $req = $DB->query($sql);
        $DB->close();
        if($niv==0) 
            $xml = "<terres idSite='".$objSite->id."' idRub='".$idRub."' >";
        else
            $xml="";
        while($r = mysql_fetch_assoc($req)) {
            $xml .= "<terre checked='1' idSite='".$objSite->id."' idRub='".$r["id_rubrique"]."' titreRub=\"".utf8_encode($r["rTitre"])."\" idGrille='".$idGrille."'   idMot='".$r["id_mot"]."'  titreMot=\"".utf8_encode($r["mTitre"])."\" >";
            $xml .= get_arbo_territoire($r["id_rubrique"],$objSite,$niv+1);
            
            /*récupération des sites enfants
            if($objSite->infos["SITE_ENFANT"]!=-1){
                foreach($objSite->infos["SITE_ENFANT"] as $id=>$type){
                    $SiteEnf = new Site($objSite->sites, $id);
                    $xml .= get_arbo_territoire($r["id_rubrique"],$SiteEnf,$niv+1);
                }           
            }
            */          
            //récupération des établissements
            $arrG = $grille->FiltreRubAvecGrilleMultiSite($r["id_rubrique"],$objSite->infos["GRILLE_ETAB"],"parent");
            if(count($arrG)>0){
                $xml .= "<terre checked='1' idSite='".$objSite->id."' idRub='".$r["id_rubrique"]."' titreRub=\"Etablissements\" idGrille='".$idGrille."' >";
                //trie le résultat
                ksort($arrG);                   
                foreach($arrG as  $key=>$val){
                    $xml.=$val["xml"];
                }   
                $xml .= "</terre>";             
            }
            //récupération des voiries
            $arrG = $grille->FiltreRubAvecGrilleMultiSite($r["id_rubrique"],$objSite->infos["GRILLE_VOIRIE"],"parent");
            if(count($arrG)>0){
                $xml .= "<terre checked='1' idSite='".$objSite->id."' idRub='".$r["id_rubrique"]."' titreRub=\"Voiries\" idGrille='".$objSite->infos["GRILLE_VOIRIE"]."' >";
                //trie le résultat
                ksort($arrG);                   
                foreach($arrG as  $key=>$val){
                    //récupération des voies
                    $xml .= "<terre checked='1' idSite='".$val["rub"]["site"]."' idRub='".$val["rub"]["id_rubrique"]."' titreRub=\"".utf8_encode($val["rub"]["titre"])."\" idGrille='".$val["rub"]["id_form"]."' >";
                    $arrV = $grille->FiltreRubAvecGrilleMultiSite($val["rub"]["id_rubrique"],78,"parent");
                    ksort($arrV);                   
                    foreach($arrV as  $keyV=>$valV){
                        $xml.=$valV["xml"];
                    }
                    $xml .= "</terre>";                                 
                    //$xml.=$val["xml"];
                }   
                $xml .= "</terre>";             
            }           
            
            $xml .= "</terre>";
        }
                
        if($niv==0) 
            $xml .= "</terres>";
        //$xml = utf8_encode($xml);
        $objSite->SaveFile(PathRoot."/bdd/carto/ArboTerritoire_".$objSite->id."_".$idRub.".xml",$xml);
    }
    return $xml;
}

?>