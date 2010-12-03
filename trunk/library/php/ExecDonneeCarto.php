<?php
/*
header('Content-type: text/html; charset=iso-8859-1');
header('Content-type: text/html; charset=UTF-8');


*/
require_once("../../param/ParamPage.php");


$GrilleGeo = $objSite->infos["GRILLE_GEO"];

$resultat = "";
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
	case 'get_markers':
		$resultat = get_marker($objSite, $_GET['id'], $_GET['southWestLat'], $_GET['northEastLat'],$_GET['southWestLng'], $_GET['northEastLng'], $_GET['zoom'], $_GET['MapQuery'], $themes);
		break;
	case 'get_theme_markers':
		get_theme_markers($_GET['id']);
		break;
	case 'sauve_marker':
		sauve_marker($_GET['action'],$_GET['id'],$_GET['zoommin'],$_GET['zoommax'],$_GET['lat'],$_GET['lng'],$_GET['adresse'],$_GET['type']);
		break;
	case 'get_kml':
		get_kml($_GET['BBOX']);
		break;
	case 'get_rub_kml':
		get_rub_kml($_GET['id'],$_GET['MapQuery']);
		break;
	case 'get_stat_kml':
		get_stat_kml($g,$objSite);
		break;
	case 'get_arbo_territoire':
		$resultat = get_arbo_territoire($g->id,$objSite);
		break;
	case 'get_stat_kml_handi':
		$resultat = get_stat_kml_handi($g,$objSite);
		break;
	case 'get_arbo_grille':
		$resultat = get_arbo_grille($g->id,$objSite,$_GET['idGrille']);
		break;
		
}

echo $resultat;


function get_arbo_grille($idRub,$objSite,$idGrille) {
	
	$grille = new Grille($objSite);
	
	$path = PathRoot."/bdd/carto/ArboGrille_".$objSite->id."_".$idRub."_".$idGrille.".xml";

	$xml = $objSite->GetFile($path);
	if(!$xml){
	
			$xml = "<grilles idSite='".$objSite->id."' idRub='".$idRub."' idGrille='".$idGrille."' >";

			//r�cup�ration des �l�ments des sites enfants
	 		if($objSite->infos["SITE_ENFANT"]!=-1){
				//r�cup�ration des éléments avec la grille
	 			$arrG = $grille->FiltreRubAvecGrilleMultiSite($idRub,$idGrille,true);
	 			if(count($arrG)>0){
	 				//trie le r�sultat
		 			ksort($arrG); 					
		 			foreach($arrG as  $key=>$val){
		 				$xml.=$val["xml"];
					}	
	 			}
			}
	 		
			$xml .= "</grilles>";

		$objSite->SaveFile($path,utf8_encode($xml));
	}		
	return $xml;
	
			
}


function get_arbo_territoire($idRub,$objSite,$niv=0) {
	
	$idGrille=$objSite->infos["GRILLE_TERRE"];
	$grille = new Grille($objSite);
	
	$path = PathRoot."/bdd/carto/ArboTerritoire_".$objSite->id."_".$idRub.".xml";
//	$xml = $objSite->GetFile($path);
//	if(!$xml){
		//r�cup�ration des territoires du granulat
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
			$xml .= "<terre checked='1' idSite='".$objSite->id."' idRub='".$r["id_rubrique"]."' titreRub=\"".$r["rTitre"]."\" idGrille='".$idGrille."'   idMot='".$r["id_mot"]."'  titreMot=\"".$r["mTitre"]."\" >";
	 		$xml .= get_arbo_territoire($r["id_rubrique"],$objSite,$niv+1);
			//r�cup�ration des �l�ments des sites enfants
	 		if($objSite->infos["SITE_ENFANT"]!=-1){
				//r�cup�ration des �tablissements
	 			$arrG = $grille->FiltreRubAvecGrilleMultiSite($r["id_rubrique"],$objSite->infos["GRILLE_ETAB"]);
	 			if(count($arrG)>0){
	 				$xml .= "<terre checked='1' idSite='".$objSite->id."' idRub='".$r["id_rubrique"]."' titreRub=\"Etablissements\" idGrille='".$idGrille."' >";
		 			//trie le r�sultat
		 			ksort($arrG); 					
		 			foreach($arrG as  $key=>$val){
		 				$xml.=$val["xml"];
					}	
					$xml .= "</terre>";				
	 			}
				//r�cup�ration des �'�tablissements
	 			$arrG = $grille->FiltreRubAvecGrilleMultiSite($r["id_rubrique"],$objSite->infos["GRILLE_VOIRIE"]);
	 			if(count($arrG)>0){
	 				$xml .= "<terre checked='1' idSite='".$objSite->id."' idRub='".$r["id_rubrique"]."' titreRub=\"Voiries\" idGrille='".$objSite->infos["GRILLE_VOIRIE"]."' >";
		 			//trie le r�sultat
		 			ksort($arrG); 					
		 			foreach($arrG as  $key=>$val){
		 				$xml.=$val["xml"];
					}	
					$xml .= "</terre>";				
	 			}
	 		}
	 		
			$xml .= "</terre>";
		}
				
		if($niv==0)	
			$xml .= "</terres>";
		$objSite->SaveFile(PathRoot."/bdd/carto/ArboTerritoire_".$objSite->id."_".$idRub.".xml",utf8_encode($xml));
//	}
	return $xml;
}




function get_stat_kml($g,$objSite) {
	
	//$objSite = new Site($SITES,$site,"");
	//$g = new Granulat($id, $objSite);
	$pck = "";
	$gEnfs = $g->GetEnfants();
	foreach($gEnfs as $gE){
		$arrGeo = $gE->GetGeo();
		$nbEnf = count(split(',',$gE->GetEnfantIds($gE->id,",")));
		$coorStat = GetCoorStat($arrGeo,$nbEnf);
		$pck .='<Placemark>
				<name>'.$gE->titre.'</name>
				<styleUrl>#visu</styleUrl>
				<Polygon>
					<extrude>1</extrude>
					<altitudeMode>relativeToGround</altitudeMode>
					<outerBoundaryIs>
						<LinearRing>
							<coordinates>
							'.GetCoorStat($arrGeo,$nbEnf).'
							</coordinates>
						</LinearRing>
					</outerBoundaryIs>
				</Polygon>
			</Placemark>';		
		
	}
	$folder ='<Folder>
			<name>'.$g->titre.'</name>
			<open>1</open>
			<description />';
	$folder .= $pck;
	$folder .= '</Folder>';
	header('Content-Type: application/vnd.google-earth.kml+xml');
	header("Content-Disposition: attachment; filename=\"Stat.kml\"");
	//on construit un kml à partir de plusieurs placemarks
	$kml = "<?xml version='1.0' encoding='UTF-8'?>";
	$kml .= "<kml xmlns='http://earth.google.com/kml/2.0'>";
	$kml .= '<Document>
		<name>'.$g->titre.'</name>
		<open>1</open>
		<Style id="visu">
			<LineStyle>
				<color>ff7f0000</color>
				<width>2</width>
			</LineStyle>
			<PolyStyle>
				<color>447f0000</color>
			</PolyStyle>
		</Style>
		<Style id="audio">
			<LineStyle>
				<color>ff098191</color>
				<width>3</width>
			</LineStyle>
			<PolyStyle>
				<color>bf00b3ff</color>
			</PolyStyle>
		</Style>
		<Style id="moteur">
			<LineStyle>
				<color>cc00ffff</color>
				<width>3</width>
			</LineStyle>
			<PolyStyle>
				<color>cc00ffff</color>
			</PolyStyle>
		</Style>';
	$kml .= $folder;
	$kml .=  "</Document>
		</kml>";
	echo $kml;
		
}


function get_stat_kml_handi($g,$objSite) {
	
	//$objSite = new Site($SITES,$site,"");
	//$g = new Granulat($id, $objSite);
	$pck = "";
	//r�cup�re les coordonn�es g�ographiques
	$arrGeo = $g->GetGeo();
	//r�cup�re le diagnostic
	$EtatDiag = simplexml_load_string($g->GetEtatDiag(true));
	//cr�ation des placemarks par handicap
	foreach($EtatDiag->Obstacles as $obst){
		$stat = array($obst->niv0,$obst->niv1,$obst->niv2,$obst->niv3);
		$coorStat = GetCoorStatCarre($arrGeo,$stat);
		$pck .='<Placemark>
				<name>'.$obst["id"]." ".$obst->handi.'</name>
				<styleUrl>#visu</styleUrl>
				<Polygon>
					<extrude>1</extrude>
					<altitudeMode>relativeToGround</altitudeMode>
					<outerBoundaryIs>
						<LinearRing>
							<coordinates>
							'.$coorStat.'
							</coordinates>
						</LinearRing>
					</outerBoundaryIs>
				</Polygon>
			</Placemark>';		
		
	}
	$folder ='<Folder>
			<name>'.$g->titre.'</name>
			<open>1</open>
			<description />';
	$folder .= $pck;
	$folder .= '</Folder>';
	header('Content-Type: application/vnd.google-earth.kml+xml');
	header("Content-Disposition: attachment; filename=\"Stat.kml\"");
	//on construit un kml à partir de plusieurs placemarks
	$kml = "<?xml version='1.0' encoding='UTF-8'?>";
	$kml .= "<kml xmlns='http://earth.google.com/kml/2.0'>";
	$kml .= '<Document>
		<name>'.$g->titre.'</name>
		<open>1</open>
		<Style id="visu">
			<LineStyle>
				<color>ff7f0000</color>
				<width>2</width>
			</LineStyle>
			<PolyStyle>
				<color>447f0000</color>
			</PolyStyle>
		</Style>
		<Style id="audio">
			<LineStyle>
				<color>ff098191</color>
				<width>3</width>
			</LineStyle>
			<PolyStyle>
				<color>bf00b3ff</color>
			</PolyStyle>
		</Style>
		<Style id="moteur">
			<LineStyle>
				<color>cc00ffff</color>
				<width>3</width>
			</LineStyle>
			<PolyStyle>
				<color>cc00ffff</color>
			</PolyStyle>
		</Style>';
	$kml .= $folder;
	$kml .=  "</Document>
		</kml>";
	echo $kml;
		
}



function GetCoorStatCarre($arrGeo,$stats){
	
	$coor='';
	$lat = $arrGeo['lat'];
	$lng = $arrGeo['lng'];
	$lrg = 0.01;
	$coor.=$lng.','.$lat.",".($stats[0]*100)." ";
	$coor.= $lng.','.($lat+$lrg).",".($stats[1]*100)." ";
	$coor.=($lng+$lrg).','.($lat+$lrg).",".($stats[2]*100)." ";
	$coor.=($lng+$lrg).','.$lat.",".($stats[3]*100)." ";
	$coor.=$lng.','.$lat.",".($stats[0]*100)." ";
	$coor.='';
							
	return $coor;
}


function sauve_marker($action,$id,$zoommin,$zoommax,$lat,$lng,$adresse,$type) {

	global $objSite, $GrilleGeo;

	// on vérifie qu'un choix est bien pass?
	switch ($action) {
		case "Modifier":
		  	//récupère l'id_donnée
			$sql = "SELECT fd.id_donnee
				FROM spip_forms_donnees fd
					INNER JOIN spip_forms_donnees_articles da ON da.id_donnee = fd.id_donnee
					INNER JOIN spip_articles a ON a.id_article = da.id_article AND a.id_rubrique = ".$id."
				WHERE fd.id_form = ".$GrilleGeo;
			//echo $sql;
			$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
			$DB->connect();
			$req = $DB->query($sql);
			$DB->close();
			
			if (mysql_num_rows($req) == 0) {
				GetRubNewGeoloc($id,$zoommin,$zoommax,$lat,$lng,$adresse,$type);
			}else{
				$row = mysql_fetch_assoc($req);
				$IdDon = $row['id_donnee'];
				//echo "suprrime les champs sauf kml<br/>";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$sql = "DELETE FROM spip_forms_donnees_champs WHERE id_donnee = ".$IdDon." AND champ <> 'texte_1'" ;
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
				
				//mise à jour des champs
				$sql = "INSERT INTO `spip_forms_donnees_champs` (valdec,`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$lat.", ".$IdDon.", 'ligne_1', ".$lat.", now())";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
				$sql = "INSERT INTO `spip_forms_donnees_champs` (valdec,`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$lng.", ".$IdDon.", 'ligne_2', ".$lng.", now())";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
				$sql = "INSERT INTO `spip_forms_donnees_champs` (valint,`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$zoommin.", ".$IdDon.", 'ligne_3', ".$zoommin.", now())";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
				$sql = "INSERT INTO `spip_forms_donnees_champs` (valint,`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$zoommax.", ".$IdDon.", 'ligne_4', ".$zoommax.", now())";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
				echo "ExeDonneCarto:SauveMarker:type=".$type."<br/>";
				if($type=="Mixte")
					$type = 5;
				if($type=="Satellite")
					$type = 4;
				if($type=="Plan")
					$type = 3;
				echo "ExeDonneCarto:SauveMarker:type=".$type."<br/>";
				$sql = "INSERT INTO `spip_forms_donnees_champs` (`id_donnee`, `champ`, `valeur`, `maj`, valint)
					VALUES (".$IdDon.", 'mot_1', '".$type."', now(), ".$type.")";
				echo "ExeDonneCarto:SauveMarker:".$objSite->infos["SQL_DB"]."sql=".$sql."<br/>";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
				$adresse=utf8_decode($adresse);
				$sql = "INSERT INTO `spip_forms_donnees_champs` (`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$IdDon.", 'ligne_7', \"".$adresse."\", now())";
				//echo $sql."<br/>";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
			}

		  	break;
		case "Supprimer":
		  $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
		  break;
  }
}

function get_marker($objSite, $id, $southWestLat, $northEastLat, $southWestLng, $northEastLng, $zoom, $query="", $themes="", $i = 0) {


	// on récupère les markers suivants les coordonnée
	$NewQuery = "idFiche";
	//
	
	//construction de la requ�te
	$statut = "";//" AND a.statut = 'publie' ";
	$statut = " ";
	$sql = "SELECT DISTINCT r.id_rubrique, r.titre, r.descriptif, r.texte
			, a.id_article idArt, da.id_donnee idDon
			, dc1.valdec lat, dc2.valdec lng, dc3.valint zoommin, dc4.valint zoommax
			, m.titre cartotype , dc7.valeur adresse
			, dc8.valeur kml
			, dArt.fichier docArtkml
			FROM spip_rubriques r
			INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique 
			INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article ".$statut."
			INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee AND fd.id_form = ".$objSite->infos["GRILLE_GEO"]."
			INNER JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
			INNER JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
			INNER JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
			INNER JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'ligne_4'
			INNER JOIN spip_forms_donnees_champs dc5 ON dc5.id_donnee = da.id_donnee AND dc5.champ = 'mot_1'					
			INNER JOIN spip_mots m ON m.id_mot = dc5.valeur					
			INNER JOIN spip_forms_donnees_champs dc7 ON dc7.id_donnee = da.id_donnee AND dc7.champ = 'ligne_7'
			LEFT JOIN spip_forms_donnees_champs dc8 ON dc8.id_donnee = da.id_donnee AND dc8.champ = 'texte_1'
			LEFT JOIN spip_documents_articles doca ON doca.id_article = a.id_article
			LEFT JOIN spip_documents dArt ON dArt.id_document = doca.id_document AND dArt.id_type IN (".$objSite->infos["CARTE_TYPE_DOC"].")
			";
		
	switch ($query) {
		case "admin":
			//requète pour un élément
			$sql .= " WHERE r.id_rubrique =".$id." 
				ORDER BY dc1.valdec, dArt.fichier DESC
				LIMIT 0 , 1";
		  	break;
		case "adminDon":
			//requète pour un élément
			$sql .= " WHERE fd.id_donnee =".$id."  
				ORDER BY dc1.valdec, dArt.fichier DESC
				LIMIT 0 , 1";
		  	break;
		case "all":
			//requète pour un élément
			$sql .= " WHERE 1  
				ORDER BY dc1.valdec, dArt.fichier DESC
				";
		  	break;
		case "allEtatDiag":
			//on boucle sur toute les rubrique sans geoc et on recherche le premier parent
			$sql .= " WHERE 1  
				ORDER BY dc1.valdec, dArt.fichier DESC
				";
			$SaveFile = true;
		  	break;
	}

	$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
	$DB->connect();
	//charge les propiétés du granulat
	$req = $DB->query($sql);
	$DB->close();
	//echo $query." ".$objSite->infos["SQL_DB"]." ".$sql."<br/>";
	
	//initialisation du xml
	$xml = "<CartoDonnees idSite='".$objSite->id."' idRub='".$id."' query='".$query."' >";
	
	//$i = 0;
	while($row = mysql_fetch_assoc($req))
	{

		$path = PathRoot."/bdd/carto/".$query."_".$objSite->id."_".$row['id_rubrique'].".xml";
		//pour g�rer le plantage de connexion mysql
		//if(file_exists($path))	continue;
			
		$g = new Granulat($row['id_rubrique'], $objSite,false);
		//echo "recup�re le granulat = ".$row['id_rubrique']."<br/>";
		
		//construction des markers
		$xmlRub = $g->GetXmlCartoDonnee($row);
						
		//v�rifie s'il faut r�cup�rer le diagnostic
		$saveStat = true;//false;
		//if($query=="allEtatDiag"){
			
			$saveStat = true;
			
			//$xml .= $g->GetEtatDiag(true,true);

			//r�cup�re les grilles du granulat 
			$xmlRub.= $g->GetXmlGrilles();
			//r�cup�re les mots-clef du granulat
			$xmlRub.= $g->GetXmlGrilleMots();
						
		//}
		
		//ajoute le bassin de gare
		//$xmlRub .= CalculBassinGare($g, $saveStat);
		
		//ajoute les geoRss
		$xmlRub .= $g->GetXmlRSS();

		//finalisation du xml
		$xmlRub .= "</CartoDonnee>";
		$xml .= $xmlRub;
		//pour l'enregistrement
		$xmlRub = "<CartoDonnees>".$xmlRub."</CartoDonnees>";

	/***************************************************************fin*******************************/
		//pour �viter le bug des connexion mysql
		//sleep(1);
		if($SaveFile){
			$objSite->SaveFile($path,$xmlRub);
		}
		
		$i++;

	}

	//gestion de la g�olocalisation par le parent quand la requ�te est vide
	if($i==0){
		$g = new Granulat($id, $objSite);
		if($g->IdParent!=0)
			$xml = get_marker($objSite, $g->IdParent, $southWestLat, $northEastLat, $southWestLng, $northEastLng, $zoom, $query, $themes, $i);		
	}else{
		//calcul toute les donn�es vides
		if($query=="allEtatDiag"){
			$g = new Granulat($id,$objSite);
			$xml .= CalculCartoDonneevide($g);		
		}
		//finalisation du xml
		$xml .= "</CartoDonnees>";		
	}
			
	//echo $markers;
	if($SaveFile){
		$objSite->SaveFile(PathRoot."/bdd/carto/".$query."_".$objSite->id."_".$id.".xml",$xml);
	}
	return $xml;
	
}

function CalculCartoDonneevide($g){

	//cr�ation des donn�es sur les enfants qui n'ont pas de grille g�o
	$grille = new Grille($g->site);
	$ids = $g->GetIdsScope(true);
    $xmlEnf="";
	$rs = $grille->FiltreRubSansGrille($g->id,$g->site->infos["GRILLE_REP_CON"]);
	$i=6;
	while ($rEnf =  mysql_fetch_assoc($rs)) {
    	$gEnf = new Granulat($rEnf["id_rubrique"],$g->site,false);
    	$row = $gEnf->GetGeo();
    	//echo "CalculCartoDonneevide:".$gEnf->titre." ".$gEnf->id."<br/>";
    	//construction de la ligne en incr�mentant un peu la position
    	if($lat==$row["lat"]){
    		$lat = $row["lat"].$i;
    		$i++;
    	}else{
    		$lat = $row["lat"];
    		$i=6;
    	}
		$r = array("id_rubrique"=>$gEnf->id
			,"titre"=>$gEnf->titre
			, "idArt"=>-1
			, "idDon"=>-1
			, "lat"=> $lat
			, "lng"=> $row["lng"]
			, "zoommin"=> $row["zoom"]
			, "zoommax"=> $row["zoommax"]
			, "cartotype"=> $row["type"]
			, "adresse"=> $row["adresse"]
			, "kml"=> $row["kml"]
			, "docArtkml"=> $row["docArtkml"]
			);
		$xmlEnf .= $gEnf->GetXmlCartoDonnee($r);
		//r�cup�re les grilles du granulat 
		$xmlEnf.= $gEnf->GetXmlGrilles();
		//r�cup�re les mots-clef du granulat
		$xmlEnf.= $gEnf->GetXmlGrilleMots();
		//finalisation du xml
		$xmlEnf .= "</CartoDonnee>";
    }
	return $xmlEnf;  	
}


function CalculBassinGare($g,$saveStat=false){

	if($g->trace)
    	echo "Granulat:CalculBassinGare:g->id = $g->id g->titre= $g->titre<br/>";
	
	//cr�ation du bassin de gare si n�cessaire
	$kml = "";
	
	//v�rifie le type d'ERP
	$typeERP = $g->GetValeurForm($g->site->infos["GRILLE_ETAB"], "", "", "", "", -1, "mot_2");
	if($g->trace)
    	echo "Granulat:CalculBassinGare:typeERP = $typeERP<br/>";
	if($typeERP==$g->site->infos["MOT_CLEF_PANG"] || $typeERP==$g->site->infos["MOT_CLEF_GARE"]){
		$grille = new Grille($g->site);
		$arrIds = split(",",$grille->GetXulNoeudCommune($g->id,true));
		//boucle pour trouver les kml
		foreach($arrIds as $idEnf){
			if($idEnf){
				$gEnf = new Granulat($idEnf,$g->site,false);
				//r�cup�re le kml du granulat mais pas celui de ses parents => $niv>6
				$docKmls = $gEnf->GetDocs($gEnf->id, $gEnf->site->infos["CARTE_TYPE_DOC"]);;
				foreach($docKmls as $docKml){
					$kml .= "<kml url='".$docKml->fichier."' />";
					if($saveStat){
						//r�cup�re les infos de geo
						$row = $gEnf->GetGeo();
						//r�cup�re le kml
						if($docKml->type==$gEnf->site->infos["KMZ_TYPE_DOC"]){
		        			$xml = GetXmlFromKmz($docKml->path);						
						}else{
							$xml = simplexml_load_file($docKml->path);					
						}
						//r�cup�ration des coordonn�es de la communes
						/*
						$xml->registerXPathNamespace("xmlns","http://earth.google.com/kml/2.1");
						$Xpath = "//xmlns:coordinates";
						//$Xpath = "/Document/Placemark/Polygon/outerBoundaryIs/LinearRing/coordinates";
						$coors = $xml->xpath($Xpath);
						*/
						if($xml){
							$coors = $xml->Document->Placemark->Polygon->outerBoundaryIs->LinearRing->coordinates."";
							if($coors==""){
								echo "toto";
							}
							//enregistrement dans la geo
							$gEnf->SetGeoRef($row["lat"],$row["lng"],$coors." ");
							//r�cup�re le nombre d'habitant
							$nbHab = $gEnf->GetValeurForm($gEnf->site->infos["GRILLE_TERRE"], "", "", "", "", -1, "ligne_2");
							//enregistrement dans la stat
							$gEnf->SetGeoStat(1,2006,$nbHab+1000);
							//r�cup�re le nombre d'handicap�
							$nbHan = $gEnf->GetValeurForm($gEnf->site->infos["GRILLE_TERRE"], "", "", "", "", -1, "ligne_3");
							//enregistrement dans la stat
							$gEnf->SetGeoStat(2,1999,$nbHan+1000);
						}
					}
				}
			}
		} 	
	}
	
	if($kml!="")
		$kml = "<BassinGare>".$kml."</BassinGare>";
	return $kml;  	
}

function GetXmlFromKmz($path){
	
	$zip = new ZipArchive;
	$kml="";
	if ($zip->open($path) === TRUE) {
	    $kml = $zip->getFromIndex(0);
	    $zip->close();
	} else {
	    echo '�chec';
	}
	return  simplexml_load_string($kml);
}



?>
