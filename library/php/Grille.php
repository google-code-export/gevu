<?php
class Grille{
  public $id;
  public $XmlParam;
  public $XmlScena;
  public $trace;
  public $titre;
  public $type;
  public $idScope;
  public $idsInScope;
  public $idDst;
  public $ordre; //por g?rer l'odre de la sc?narisation
  private $site;

  function __tostring() {
    return "Cette classe permet de d?finir et manipuler des grilles.<br/>";
    }

  function __construct($site, $id=-1, $complet=true, $type="", $idScope=-1, $crea=false) {
	//echo "new Site $sites, $id, $scope<br/>";
	$this->trace = TRACE;

    $this->site = $site;
    $this->id = $id;
    $this->type = $type;
    //gestion du scope des rubriques
    if($idScope!=-1){
    	$this->idScope = $idScope;
    	$g = new Granulat($idScope,$this->site);
    	//gestion de la cr?ation des lignes
    	if($crea){
    		//on prend le scope ? partir de la rubrique
    		$this->idsInScope = $g->GetEnfantIds($g->id,",")."-1";
    	}else{
    		//pour afficher les ?l?ments on prend le scope ? partir du parent de la rubrique
    		$this->idsInScope = $g->GetEnfantIds($g->IdParent,",")."-1";    		
    	}
    	
    }
	$this->XmlScena = new XmlParam(XmlScena);
	
	if($complet){
		$this->GetProps();
	}

	//echo "FIN new grille <br/>";
		
    }

    function RechercheRubId($champ,$valeur,$idGrille=-1) {
		if($this->trace)
			echo "Grille:RechercheRubId://recherche l'id d'une rubrique avec sa valeur = $valeur et son champ=$champ <br/>";

		if($idGrille==-1)$idGrille=$this->id;

		$arrVarVal = array(
			array("-idGrille-", $idGrille)
			,array("-champ-", $champ)
			,array("-valeur-", $valeur)
			);
		$rows = $this->site->RequeteSelect('Grille_RechercheRubId',$arrVarVal);
		$row =  mysql_fetch_assoc($rows);
		return $row["idRub"];
    	
    }
      
    
	public function GetCritereObs($idRub, $cri)
	{
		
		$sql = "SELECT a.id_article idArt, a.titre titreArt, a.date aDate
					, fd.id_donnee idDon
					, fdc1.valeur ComVal1
					, fdc2.valeur ComVal2
					, fdc3.valeur ComVal3
					, fdc4.valeur ComVal4
					, fdc5.valeur ComVal5
					, d.fichier ComFic
				FROM spip_rubriques r
					INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique					
					INNER JOIN spip_forms_donnees_articles fda ON fda.id_article = a.id_article
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fda.id_donnee
						 AND fd.id_form =67
					INNER  JOIN spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
						 AND fdc5.champ = 'ligne_5' AND fdc5.valeur = '".$cri."' 
					INNER  JOIN spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
						 AND fdc4.champ = 'ligne_4'
					INNER  JOIN spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
						 AND fdc3.champ = 'ligne_3'
					INNER  JOIN spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
						 AND fdc2.champ = 'ligne_2'
					INNER  JOIN spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
					 	AND fdc1.champ = 'ligne_1'

					LEFT JOIN spip_documents_articles da ON da.id_article = a.id_article
					LEFT JOIN spip_documents d ON d.id_document = da.id_document					 
				WHERE r.id_rubrique = ".$idRub;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		
		return $result;
	}

    
    
	function GetTree($type,$id){
		
		
		//recuperation des colonnes
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetTreeChildren_".$type."']/col";
		$Cols = $this->site->XmlParam->GetElements($Xpath);		

		//recuperation des js
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetTreeChildren_".$type."']/js";
		$js = $this->site->GetJs($Xpath, array($type,$id));
		
		$tree = "<tree flex=\"1\" 
			id=\"tree".$type."\"
			seltype='multiple'
			class='ariane'
			".$js."
			>";
		$tree .= '<treecols>';
		$tree .= '<treecol  id="id" primary="true" cycler="true" flex="1" persist="width ordinal hidden"/>';

		$i=0;
		foreach($Cols as $Col)
		{
			//la premiere colonne est le bouton pour deplier
			if($i!=0){
				if($Col["hidden"])
					$visible = $Col["hidden"];
				else
					$visible = "false";
				if($Col["type"]=="checkbox"){
					$tree .= '<treecol id="treecol_'.$Col["tag"].'" label="'.$Col["tag"].'" type="checkbox" editable="true" persist="width ordinal hidden" />';
				}else{
					$tree .= '<treecol id="treecol_'.$Col["tag"].'" hidden="'.$visible.'" label="'.$Col["tag"].'" flex="6"  persist="width ordinal hidden" />';
					//$tree .= '<splitter class="tree-splitter"/>';
				}
			}
			$i++;
		}
		$tree .= '</treecols>';
		$tree .= $this->site->GetTreeChildren($type, $Cols, $id);
		$tree .= '</tree>';
		/*
		header('Content-type: application/vnd.mozilla.xul+xml');
		$tree = $objSite->GetTreeChildren($type, $Cols, $id);
		*/
		return $tree;
		
	}
    
	public function GetEtatDiagListeTot($idRub, $rs=false)
	{
		
		//construit les objets n?cessaires
		$objXul = new Xul($this->site);
		
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtatDiagListeTot']";

		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRub, $Q[0]->where);					
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "GetEtatDiagListeTot".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		
		//renvoie uniquement le r�sultat de la requ�te
		if($rs) return $result;
			
		//construction du xul
		$xml = "<etats>";
		$idRubOld=-1;
		while ($r =  $db->fetch_assoc($result)) {

				if($r["id_rubrique"]!=$idRubOld){
					//finalise le pr?c?dent lieu
					if($idRubOld!=-1)
						$xml .= "</lieu>";
					
					$idRubOld=$r["id_rubrique"];
					$xml .= "<lieu>";
				}
		
				$xml .= "<crit>";				
				//ajoute la l�gende				
				$xml .= "<legende>";
				$xml .= utf8_encode($this->GetXulLegendeControle($r['idDonCont'],$this->site->infos["GRILLE_CONTROL_".$_SESSION['version']]));
				$xml .= "</legende>";
									
		    	//ajoute l'affirmation
				$xml .= '<affirm>'.utf8_encode($this->site->XmlParam->XML_entities($r['affirm'])).'</affirm>';			

				//finalise le crit?re				
				$xml .= "</crit>";
		}
		$xml .= "</lieu></etats>";
		
		return $xml;
	}

	public function GetEtatDiagListe($idRub, $idDoc,$PourFlex=false,$idScope=false,$rs=false)
	{
		//r?cup?re les info de l'id xul
		$arrDoc = split("_",$idDoc);
		
		//construit les objets n?cessaires
		$objXul = new Xul($this->site);
		
		if($arrDoc[0]==0){
			//r?cup?re les crit?re suivant leur validation
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtatDiagOuiListe']";
			//$champ = $this->site->infos["CHAMPS_CONTROL_DEFFICIENCE"]["champ"];
			//$valeur = $this->site->infos["CHAMPS_CONTROL_DEFFICIENCE"]["valeur"][$arrDoc[1]];
			$champ = $this->site->infos["CHAMPS_CONTROL_DEFFICIENCE"][$arrDoc[1]];
		}else{
			//r?cup?re les crit?re suivant leur validation
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtatDiagHandiListe']";
			//$champ = $this->site->infos["CHAMPS_CONTROL_DIAG"][$arrDoc[1]];
			$champ = $this->site->infos["CHAMPS_CONTROL_DEFFICIENCE"][$arrDoc[1]];
			$valeur = $arrDoc[0];
		}

		if($this->trace)
			echo "Grille:GetEtatDiagListe:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRub, $Q[0]->where);		
		$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
		$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
		$from = str_replace("-valeur-", $valeur, $from);
		$from = str_replace("-champ-", $champ, $from);
		$from = str_replace("-TypeHandi-", $arrDoc[1], $from);
		$from = str_replace("-handi-", $arrDoc[0], $from);
		
		
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "GetEtatDiagListe".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		
		//renvoie uniquement le r�sultat de la requ�te
		if($rs) return $result;
			
		//construction du xul
		$xul = "<vbox flex='1'>";
		$idRubOld=-1;
		while ($r =  $db->fetch_assoc($result)) {
			
				if($r["id_rubrique"]!=$idRubOld){
					//finalise le pr?c?dent lieu
					if($idRubOld!=-1 && $PourFlex)
						$xul .= "</lieu>";
					
					$idRubOld=$r["id_rubrique"];
					//ajoute le fil d'ariane
					if($PourFlex)
						$xul .= "<lieu><ariane>";
					$xul .= '<hbox class="menubar">'.$objXul->GetFilAriane("",$r["id_rubrique"],0,$idScope).'</hbox>';
					if($PourFlex)
						$xul .= "</ariane>";
				}
				//ajoute les infos du granulat
				//$g = new Granulat($r["id_rubrique"],$this->site);
				//$xul .= '<hbox class="menubar" >'.$g->TitreParent.' | '.$g->titre.'</hbox>';
				
				//ajoute le crit?re				
				if($PourFlex)
					$xul .= "<crit>";
				else	
					$xul.="<hbox>";				
				
				//ajoute la l?gende				
				if($PourFlex)
					$xul .= "<legende>";
				$xul .= $this->GetXulLegendeControle($r['idDonCont'],$this->site->infos["GRILLE_CONTROL_".$_SESSION['version']]);
				if($PourFlex)
					$xul .= "</legende>";
				
				//ajoute les liens 
				if($PourFlex)
					$xul .= "<liens>";
				$xul.= $this->GetXulLiensDonnee($r['idDonRep'],$r['valRef']);
				if($PourFlex)
					$xul .= "</liens>";
				else
					$xul.="</hbox>";
					
		    	//ajoute l'affirmation
				$xul .= '<textbox  multiline="true" id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($r['affirm']).'"/>';			
				//finalise le crit?re				
				if($PourFlex)
					$xul .= "</crit>";
		}
		//finalise le xml
		if($PourFlex && $idRubOld!=-1)
			$xul .= "</lieu>";
		$xul .= "</vbox>";
		
		return $xul;
	}
    
    public function GetXulLiensDonnee($idDon,$valRef)
	{
		$xul = "";
		//v?rifie s'il y a une grille geo
		$idDonV = $this->VerifDonneeLienGrille($idDon,$this->site->infos["GRILLE_GEO"]);
		if($idDonV){
			$xul .="<vbox>";
			$xul.="<image onclick=\"ExecCarto(".$this->GetRubDon($idDonV).",".$idDonV.");\" src='".$this->site->infos["pathImages"]."kml.png' />";
			//$idArt = $this->GetArtDon($idDonV);
			//$xul.= $this->GetXulLiensArticle($idArt);
			$xul .="</vbox>";
		}
		//v?rifie s'il y a une grille observation
		$Dons = $this->GetLienDonnee($idDon,$valRef,"GRILLE_OBS");
		while ($r =  mysql_fetch_assoc($Dons)) {
			$xul .="<vbox flex='1'>";
			$xul .="<hbox>";
			$xul.="<image onclick=\"ShowPopUp(".$this->site->infos["GRILLE_OBS"].", ".$r['id_donnee'].");\" src='".$this->site->infos["pathImages"]."obs.png' />";
			$xul .="</hbox>";
			$xul.= $this->GetXulLiensArticle($r['id_article']);
			$xul .="</vbox>";
		}
		//v?rifie s'il y a une grille signalement probleme
		$Dons = $this->GetLienDonnee($idDon,$valRef,"GRILLE_SIG_PROB");
		while ($r =  mysql_fetch_assoc($Dons)) {
				$xul .="<vbox flex='1'>";
				$xul .="<hbox>";
				$xul.="<image onclick=\"ShowPopUp(".$this->site->infos["GRILLE_SIG_PROB"].", ".$r['id_donnee'].");\" src='".$this->site->infos["pathImages"]."sigprob.png' />";
				$xul .="</hbox>";
				$xul.= $this->GetXulLiensArticle($r['id_article']);
				$xul .="</vbox>";
		}
		
		return $xul;
	}
	
    public function GetXulLiensArticle($idArt)
	{
		$gra = new Granulat(-1,$this->site);
		//v?rifie s'il y a des documents
		$docs = $gra->GetArtDocs($idArt);
		$oXul = new Xul($this->site);
		$xul = $oXul->GetFriseDocsIco($idArt,-1,false);
		//if($gra->VerifMultiMedia($docs))
		//	$xul.="<image onclick=\"alert(".count($docs).");\" src='images/file.gif' />";
		//Ajoute le lien admin
		$xul .="<label id='adminArt_".$idArt."' class='text-linkAdmin' onclick=\"OuvreArticle(".$idArt.");\" value=\"Admin\"/>";
		
		return $xul;
	}
	
	
	public function GetEtatDiagIcones($FormIds, $ids)
	{
		$icones ="<icones id='ico_'>";
		//boucle sur les grilles de la rubrique
		while($row = mysql_fetch_assoc($FormIds)) {
			//r?cup?re les crit?res des icones suppl?mentaire
			$Xpath = "/XmlParams/icones/objet[@IdGrille='".$row["id_form"]."']";
			if($this->trace)
				echo "Grille:GetEtatDiagIcones:Xpath".$Xpath."<br/>";
			$CritIcos = $this->site->XmlParam->GetElements($Xpath);
			if($CritIcos){
				foreach($CritIcos[0]->question as $q){
					$idDon = false;
					//v?rifie s'il faut chercher par rapport aux grilles d'information
					if($q["srcIdGrille"]){ 
						$idDon = $this->RechercheDonneeId($q["srcIdGrille"],$row["id_article"],$q["srcIdChamp"],$q["srcCheckVal"],$ids,$q["srcRefCont"]);
						//v?rifie s'il faut traiter un deuxi?me crit?re
						$qB = $q->question;
						if($idDon && $qB){
							$idDon = $this->RechercheDonneeId($qB["srcIdGrille"],$row["id_article"],$qB["srcIdChamp"],$qB["srcCheckVal"],$ids,$qB["srcRefCont"]);
							$q = $qB;
						}
					}
					/*v?rifie s'il faut chercher par rapport aux grilles de r?ponse
					if($q["id"]){
						$idDon = $this->RechercheDonneeId($this->site->infos["GRILLE_REP_CON"],$row["id_article"],$q["srcIdChamp"],$q["srcCheckVal"],$ids);
					}
					*/
					//ajoute l'icone
					if($idDon){
						$icones .= 	"<icone id='".$q->icone["id"]."' />";		
					}
				}	
			}
		}
		$icones .="</icones>";
		return $icones;
	}

    function SetEtatDiag($idRub,$handi,$audio,$cog,$moteur,$visu){
    	//supprime l'etatDiag
		$this->DelEtatDiag($idRub,$handi);
		//v?rifie les valeurs
		if(!$audio)$audio=0;
		if(!$cog)$cog=0;
		if(!$moteur)$moteur=0;
		if(!$visu)$visu=0;
    	//enregistre le diag
		$sql = "INSERT INTO ona_etatdiag (id_rubrique, handi, moteur, audio, visu, cog)
			VALUES (".$idRub.",".$handi.",".$moteur.",".$audio.",".$visu.",".$cog.")";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$req = $db->query($sql);
		$id = $db->insert_id();
		$db->close();
		return $id;
    }

    function SetEtatDiagDonnees($idEtat,$from,$where){
		$sql = "INSERT INTO ona_etatdiag_donnees (id_etatdiag, idDonRep, idDonCont) SELECT ".$idEtat."
				, fdcRep.id_donnee 
				, fdcRef.id_donnee "
			.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
     }
    
    function DelEtatDiag($idRub,$handi){
		//supprime la relation des ?tatdiag au donn?e
    	$sql = "DELETE FROM ona_etatdiag_donnees 
			WHERE id_etatdiag IN (SELECT id_etatdiag FROM ona_etatdiag 
					WHERE id_rubrique=".$idRub." AND handi=".$handi.")";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$db->query($sql);
    	
		//supprime les ?tatdiag
		$sql = "DELETE FROM ona_etatdiag 
			WHERE id_rubrique=".$idRub." AND handi=".$handi;
		$db->query($sql);
		$db->close();
    }
    
    public function FiltreRubAvecGrilleMultiSite($idRub,$idGrille,$GetAll=false)
	{
		$arrG = array();
		//r?cup?re les grilles pour le site
		if($this->site->infos["SITE_ENFANT"]==-1){ 					
			$path = PathRoot."/bdd/carto/ArboGrille_".$this->site->id."_".$idRub."_".$idGrille.".xml";
			$xml = $this->site->GetFile($path);
			if(!$xml){
				$rs = $this->FiltreRubAvecGrille($idRub,$idGrille,false,$GetAll);
				$arr = $this->GetRubGeoGrille($idGrille,$idRub,$this->site,$rs,"",$arrG,$GetAll);
				$arrG = $arr[0];
				$xmlG = $arr[1];			
				$this->site->SaveFile($path,$xmlG);
			}			
		}else{
			//r?cup?re les grille des sites dans le cas d'un site parent 					
	 		foreach($this->site->infos["SITE_ENFANT"] as $id=>$type)
			{
				$oSiteEnf = new Site($this->site->sites,$id,false);
				$grille = new Grille($oSiteEnf);

				$path = PathRoot."/bdd/carto/ArboGrille_".$oSiteEnf->id."_".$idRub."_".$idGrille.".xml";
				$xmlG = "";
				$xml = $oSiteEnf->GetFile($path);
				if(!$xml){
				
					//r?cup?ration des rubrique avec la grille
					$rs = $grille->FiltreRubAvecGrille($idRub,$idGrille,false,$GetAll);
					$arr = $grille->GetRubGeoGrille($idGrille,$idRub,$oSiteEnf,$rs,$xmlG,$arrG,$GetAll);
					if($arr[1]!=""){
						$arrG = $arr[0];
						$xmlG = $arr[1];					
						$oSiteEnf->SaveFile($path,$xmlG);
					}
				}else{
					//construction du tableau
					$key = $oSiteEnf->id."_".$idRub."_".$idGrille;
					$arrG[$key]= array("xml"=>$xml);
				}
			}				
		}
		return $arrG;
		
	}
	
	public function GetRubGeoGrille($idGrille, $idRub,$oSiteEnf,$rs,$xmlG,$arrG,$GetAll)
	{
		$g = new Granulat($idRub,$oSiteEnf,false);
		$xml = "";
		while($row = mysql_fetch_assoc($rs)) {
			$key = $oSiteEnf->strtokey($row["titre"]."_".$oSiteEnf->id."_".$row["id_rubrique"]);
			//pour suprimer les doublons entre site
			$key = $oSiteEnf->strtokey($row["titre"]."_".$row["id_rubrique"]);
			$titre = utf8_encode($this->site->XmlParam->XML_entities($row["titre"]));
			$xml = "<terre checked='1' idSite='".$oSiteEnf->id."' idRub='".$row["id_rubrique"]."' titreRub=\"".$titre."\" idGrille='".$idGrille."' >";
			
			$geo = $g->GetGeo($row["id_rubrique"]);
			$xml .= "<CartoDonnee lat='".$geo['lat']."'";		
			$xml .= " lng='".$geo['lng']."'";
			$xml .= " idRub='".$row['id_rubrique']."'";				
			$xml .= " titre=\"".$titre."\"";
			$xml .= " idSite='".$oSiteEnf->id."'";
			$xml .= " zoommin='".$geo['zoom']."'";
			$xml .= " kml='".$geo['kml']."'";
			$xml .= " adresse=\"".utf8_encode($this->site->XmlParam->XML_entities($geo['adresse']))."\"";
			$xml .= " cartotype='".$geo['type']."'";
			$xml .= " idGrille='".$idGrille."'";
			$xml .= " />";

			//v?rifie s'il faut charger des grilles enfants
			/*
			$Xpath = "/XmlParams/XmlParam/menuSrc[@idForm='".$idGrille."']";
			if($this->trace)
				echo "Grille:FiltreRubAvecGrilleMultiSite:Xpath".$Xpath."<br/>";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			//calcul les grilles enfants
			if(count($Q)>0){ 					
		 		foreach($Q[0]->menuDst as $grilleEnf)
				{
					if($grilleEnf["idForm"]){
						$arrSG = $this->FiltreRubAvecGrilleMultiSite($row["id_rubrique"],$grilleEnf["idForm"],$GetAll);	
					}
					ksort($arrSG);
					$i=0;
					$xmlSG=""; 					
					foreach($arrSG as  $k=>$val){
						if($i==0)$xmlSG = "<terre checked='1' idSite='".$oSiteEnf->id."' idRub='".$row["id_rubrique"]."' titreRub=\"".utf8_encode($this->site->XmlParam->XML_entities($val["rub"]["gTitre"]))."\" idGrille='".$grilleEnf["idForm"]."' >";
						//ajoute les grilles enfants
						$xmlSG .= $val["xml"];
						$i++;
					}
					if($xmlSG!=""){
						$xml .= $xmlSG."</terre>";
					}
				}
			}
			*/
			$xml .= "</terre>";
			$row['site'] = $oSiteEnf->id;
			$arrG[$key]= array("xml"=>$xml,"rub"=>$row);
			$xmlG .= $xml;
		}
		return array($arrG,$xmlG);		
	}
	
	
	public function FiltreRubAvecGrille($id,$idsGrille,$GetArr=false,$GetAll=false)
	{
	
		//v�rifie s'il faut r�cup�rer les enfants
		if($GetAll){
			$sqlEnf = "";
		}else{
			$sqlEnf = "INNER JOIN spip_rubriques_enfants re ON re.id_rubrique = r.id_rubrique AND re.id_parent =".$id;
		}
		if($GetAll==="parent"){
			$sqlEnf = "INNER JOIN spip_rubriques re ON re.id_rubrique = r.id_parent AND re.id_rubrique =".$id;
		}
		
		$sql = "SELECT DISTINCT r.id_rubrique, r.titre, f.id_form, f.titre gTitre
			FROM spip_rubriques r
			".$sqlEnf."
			INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
			INNER JOIN spip_forms_donnees_articles fda ON fda.id_article = a.id_article 
			INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fda.id_donnee AND fd.id_form IN (".$idsGrille.")
			INNER JOIN spip_forms f ON f.id_form = fd.id_form 
			";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:FiltreRubAvecGrille".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		
		if($GetArr){
			$arrG = array();
			while($row = mysql_fetch_assoc($result)) {
				$key = $this->site->strtokey($row["titre"]."_".$this->site->id."_".$row["id_rubrique"]);
				$xml = "<terre idSite='".$this->site->id."' idRub='".$row["id_rubrique"]."' titreRub=\"".$row["titre"]."\" idGrille='".$row["id_form"]."' />";
				$arrG[$key]= array("xml"=>$xml,"rub"=>$row);								
			}
			$result = $arrG;
		}
			
		return $result;
		
	}

    public function FiltreRubSansGrille($id,$idsGrille)
	{
		$sql = "SELECT DISTINCT r.id_rubrique, fd.id_donnee
			FROM spip_rubriques r
			INNER JOIN spip_rubriques_enfants re ON re.id_rubrique = r.id_rubrique AND re.id_parent =".$id."
			INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
			INNER JOIN spip_forms_donnees_articles fda ON fda.id_article = a.id_article 
			LEFT JOIN spip_forms_donnees fd ON fd.id_donnee = fda.id_donnee AND fd.id_form IN (".$idsGrille.")
			WHERE fd.id_donnee IS NULL";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:FiltreRubAvecGrille".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
			
		return $result;
		
	}
	
    public function GetNumEtatDiagFait($id)
	{
		$sql = "SELECT COUNT(DISTINCT re.id_rubrique) nb
			FROM spip_rubriques_enfants re 
				INNER JOIN ona_etatdiag oe ON re.id_rubrique = oe.id_rubrique
			WHERE re.id_parent = ".$id;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:GetNumEtat".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		$r =  $db->fetch_assoc($result);
		
		return $r['nb'];
		
	}
    
	public function GetEtatDiagSum($idRub,$handi)
	{
		//r?cup?re la somme des ?tat de diagnostic
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtatDiagSum']";
		if($this->trace)
			echo "Grille:GetEtatDiagSum:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRub, $Q[0]->where);
		$where = str_replace("-handi-", $handi, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:GetEtatDiagSum".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		$r =  $db->fetch_assoc($result);
		return $r;
	}
	
	
    public function GetEtatDiagOui($ids,$idRub,$calcul)
	{
		if(!$calcul){
			$r =  $this-> GetEtatDiagSum($idRub,0);		
		}else{
			//r?cup?re le nombre de crit?res valid?s
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtatDiagOui']";
			if($this->trace)
				echo "Grille:GetEtatDiagOui:Xpath".$Xpath."<br/>";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-ids-", $ids, $Q[0]->where);
			$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
			$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
			$sql = $Q[0]->select.$from.$where;
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$db->connect();
			
			$result = $db->query($sql);
			if($this->trace)
				echo "Grille:GetEtatDiagOui".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
			$db->close();				
			$r =  $db->fetch_assoc($result);
			//conserve l?tat du diagnostique
			$idEtat = $this->SetEtatDiag($idRub,0,$r['audio'],$r['cog'],$r['moteur'],$r['visu']);
			$this->SetEtatDiagDonnees($idEtat,$from,$where);			
		}
				
		//construction du xml
		$xml = "<CritsValides id='0_' moteur='".$r['moteur']."' audio='".$r['audio']."' visu='".$r['visu']."' cog='".$r['cog']."' ></CritsValides>";
			
		return array("xml"=>$xml,"r"=>$r);

	}

	public function GetEtatDiagHandi($ids,$handi,$idRub,$calcul)
	{
		if(!$calcul){
			$r =  $this-> GetEtatDiagSum($idRub,$handi);		
		}else{
			//r?cup?re le nombre de crit?res valid?s
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtatDiagHandi']";
			if($this->trace)
				echo "Grille:GetEtatDiagHandi:Xpath".$Xpath."<br/>";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-ids-", $ids, $Q[0]->where);
			$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
			$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
			$from = str_replace("-handi-", $handi, $from);
			$sql = $Q[0]->select.$from.$where;
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$db->connect();
			$result = $db->query($sql);
			if($this->trace)
				echo "Grille:GetEtatDiagHandi".$this->site->infos["SQL_DB"]." ".$sql."<br/>";
			$db->close();
			$r =  $db->fetch_assoc($result);
			//conserve l?tat du diagnostique
			$idEtat = $this->SetEtatDiag($idRub,$handi,$r['audio'],$r['cog'],$r['moteur'],$r['visu']);
			$this->SetEtatDiagDonnees($idEtat,$from,$where);			
		}
		
		$xml = "<Obstacles id='".$handi."_' moteur='".$r['moteur']."' audio='".$r['audio']."' visu='".$r['visu']."' cog='".$r['cog']."' ></Obstacles>";
		if($this->trace)
			echo "Grille:GetEtatDiagHandi:r=".print_r($r)."<br/>";
					
		return array("xml"=>$xml,"r"=>$r);
	}
	
	
	public function GetEtatDiagApplicable($ids,$idRub,$calcul)
	{
		if(!$calcul){
			$r =  $this->GetEtatDiagSum($idRub,4);		
		}else{
			//r?cup?re le nombre de crit?res valid?s
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtatDiagApplicable']";
			if($this->trace)
				echo "Grille:GetEtatDiagHandi:Xpath".$Xpath."<br/>";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-ids-", $ids, $Q[0]->where);
			$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
			$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
			$sql = $Q[0]->select.$from.$where;

			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$db->connect();
			$result = $db->query($sql);
			if($this->trace)
				echo "Grille:GetEtatDiagApplicable".$this->site->infos["SQL_DB"]." ".$sql."<br/>";
			$db->close();
				
			$r =  $db->fetch_assoc($result);
			//conserve l?tat du diagnostique
			$idEtat = $this->SetEtatDiag($idRub,4,$r['audio'],$r['cog'],$r['moteur'],$r['visu']);
			$this->SetEtatDiagDonnees($idEtat,$from,$where);			
		}

		$xml = "<Applicables id='IndicAcc_' moteur='".$r['moteur']."' audio='".$r['audio']."' visu='".$r['visu']."' cog='".$r['cog']."' ></Applicables>";
		if($this->trace)
			echo "Grille:GetEtatDiagApplicable:r=".print_r($r)."<br/>";
			
		return array("xml"=>$xml,"r"=>$r);
	}

    
	public function GetProps()
	{
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		if($this->trace)
			echo "//charge les propi?t?s de la grille $this->id -<br/>";
		$sql = "SELECT titre
			FROM spip_forms 
			WHERE id_form = ".$this->id;
		//echo $sql."<br/>";
		$req = $DB->query($sql);
		$DB->close();
		$data = $DB->fetch_assoc($req);
		$this->titre = $data['titre'];

	}
    
    function GetTreeProb($idRub, $rs=false, $arr=false, $force=false, $rapport=false){
    	
		$path = PathRoot."/bdd/EtatDiag/GetTreeProb_".$this->site->id."_".$idRub."_".$rs."_".$arr.".xml";
		if(!$force){
			$str = $this->site->GetFile($path);
			if($str){
				if($arr){
					return json_decode($str, true);
				}
				return $str;			
			}			
		}    
    	$g = new Granulat($idRub,$this->site);
    	//r?cup?re les rubriques enfants
    	$ids = $g->GetIdsScope();
    	    	
		//r?cup?re les identifiants des rubriques de la racine ayant un Probl�me
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetListeSignalementProbleme']";
		if($this->trace)
			echo "Grille:GetTreeProb:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $ids, $Q[0]->where);
		$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
		$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:GetTreeProb:".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		if($rs){
			return $result;
		}
		if($arr){
			$arr =array();
			while ($r =  $db->fetch_assoc($result)) {
				$a = array();
				foreach($r as $lb=>$val){
					$a[$lb] = utf8_encode($val);
				}
				$arr[] = $a;
			}
			$this->site->SaveFile($path,json_encode($arr));
			return $arr;
		}
		
		$xul ='<grid flex="1">';
		//on cache la colonne de r?f?rence	
		$xul.='<columns>';	
			$xul.='<column flex="1" hidden="true"/>';	
			$xul.='<column flex="1"/>';
			$xul.='<column flex="1"/>';			
			$xul.='<column flex="1" hidden="true"/>';			
			$xul.='<column flex="1"/>';			
		$xul.='</columns>';	
		$xul.='<rows>';
		$oidRubPar=-1;
		$oidRub=-1;
		$oidArt=-1;
		$oidCont=-1;
		while ($r =  $db->fetch_assoc($result)) {
			if($this->trace)
				echo "Grille:GetTreeProb:".$r["idRub"]." ".$r["idArt"]." ".$r["idDon"]."<br/>";
		
		if 	(!$r["ReponsePhoto"])	$r["ReponsePhoto"] = 'Non';
				
		$idDoc = 'val'.DELIM.$this->site->infos["GRILLE_SIG_PROB"].DELIM.$r["idDon"].DELIM."Modif".DELIM.$r["idArt"];
		$xul.="<row>";
			$xul.="<vbox hidden='true' >";
				$xul.="<label id='".$idDoc."' value='".$idDoc."' />";
			$xul.="</vbox>";

			$xul.="<vbox>";
				if($r["idRubPar"]!=$oidRubPar){
					$xul.="<label value=\"".$this->site->XmlParam->XML_entities($r["titreRubPar"])."\"/>";
					$xul.="<hbox>";
						$xul.="<label id='adminRubPar_".$r["idRubPar"]."' class='text-linkAdmin' onclick=\"OuvreLienAdmin(".$r["idRubPar"].");\" value=\"Admin\"/>";
			    		$xul.="<!--<image onclick=\"SetVal('".$idDoc."');\" src='".$this->site->infos["pathImages"]."check_yes.png' /> -->";
			    		$xul.="<image onclick=\"DelRubriqueParent('".$r["idRubPar"]."');\" src='".$this->site->infos["pathImages"]."check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";
			
			$xul.="<vbox>";
				if($r["idRub"]!=$oidRub){
					$xul.="<label value=\"".$this->site->XmlParam->XML_entities($r["titreRub"])."\"/>";
					$xul.="<hbox>";
						$xul.="<label id='adminRub_".$r["idRub"]."' class='text-linkAdmin' onclick=\"OuvreLienAdmin(".$r["idRub"].");\" value=\"Admin\"/>";
			    		$xul.="<!--<image onclick=\"SetVal('".$idDoc."');\" src='".$this->site->infos["pathImages"]."check_yes.png' /> -->";
			    		$xul.="<image onclick=\"DelRubrique('".$r["idRub"]."', '".$idRub."');\" src='".$this->site->infos["pathImages"]."check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";

			
			$xul.="<vbox hidden='true'>";
				if($r["idArt"]!=$oidArt){
					$xul.="<label value=\"".$this->site->XmlParam->XML_entities($r["titreArt"])."\"/>";
					$xul.="<hbox>";
						$xul.="<label id='adminArt_".$r["idArt"]."' class='text-linkAdmin' onclick=\"OuvreArticle(".$r["idArt"].");\" value=\"Admin\"/>";
						$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='".$this->site->infos["pathImages"]."check_yes.png' />";
			    		$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='".$this->site->infos["pathImages"]."check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";
			
			
			$xul.="<vbox>";
				if($r["idCont"]!=$oidCont){
					$xul.="<hbox>";
						if($rapport){
							$xul.="<label value=\"".$this->site->XmlParam->XML_entities($r["affir"])."\"/>";
						}else{
							$xul.="<label value=\"Problème n° ".$r["idPbPlan"]." : ".$this->site->XmlParam->XML_entities($r["TextCont"])."\"/>";
						}
						$xul.="<label class='text-linkAdmin' onclick=\"OuvreControle(".$r["idDonneCont"].");\" value='(".$r["idCont"].")'/>";
		    		$xul.="</hbox>";
				}
				$xul.="<hbox>";
					$xul.="<label value='    - ".$r["RepCont"]."'/>";
					$xul.="<label value='".$r["aDate"]."'/>";
					$xul.="<label value='Photo : ".$r["ReponsePhoto"]."'/>";
					$xul.="<label id='adminDon_".$r["idDon"]."' class='text-linkAdmin' onclick=\"OuvreDonnee(".$this->site->infos["GRILLE_SIG_PROB"].",".$r["idDon"].");\" value=\"Admin\"/>";
					$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='".$this->site->infos["pathImages"]."check_yes.png' />";
		    		$xul.="<image onclick=\"DelArticleProb('".$r["idArt"]."', '".$idRub."');\" src='".$this->site->infos["pathImages"]."check_no.png' />";
		    	$xul.="</hbox>";
			$xul.="</vbox>";
					
		    $xul.="</row>";	
			$oidRubPar=$r["idRubPar"];
			$oidRub=$r["idRub"];
			$oidArt=$r["idArt"];
			$oidCont=$r["idCont"];
		
		}
		$xul .='</rows>';	
		$xul .='</grid>';	
		
		$this->site->SaveFile($path,$xul);
		
	   	return $xul;
    	
    }
    
    function GetTableauBord($idRub){
    	
    	$g = new Granulat($idRub,$this->site);
    	//r?cup?re les rubriques enfants
    	$ids = $g->GetIdsScope();
    	    	
		//r?cup?re les identifiants des rubriques de la racine ayant un Probl�me
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetListeSignalementProbleme']";
		if($this->trace)
			echo "Grille:GetTableauBord:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $ids, $Q[0]->where);
		$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
		$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:GetTableauBord:".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		
		$xul ='<grid flex="1">';
		//on cache la colonne de r?f?rence	
		$xul.='<columns>';	
			$xul.='<column flex="1" hidden="true"/>';	
			$xul.='<column flex="1"/>';
			$xul.='<column flex="1"/>';			
			$xul.='<column flex="1" hidden="true"/>';			
			$xul.='<column flex="1"/>';			
		$xul.='</columns>';	
		$xul.='<rows>';

		$oidRubPar=-1;
		$oidRub=-1;
		$oidArt=-1;
		
		while ($r =  $db->fetch_assoc($result)) {
			if($this->trace)
				echo "Grille:GetTableauBord:".$r["idRub"]." ".$r["idArt"]." ".$r["idDon"]."<br/>";

		if 	(!$r["ReponsePhoto"])	$r["ReponsePhoto"] = 'Non';
		$idDoc = 'val'.DELIM.$this->site->infos["GRILLE_OBS"].DELIM.$r["idDon"].DELIM."Sup".DELIM.$r["idArt"];
		$xul.="<row>";
			$xul.="<vbox hidden='true' >";
				$xul.="<label id='".$idDoc."' value='".$idDoc."' />";
			$xul.="</vbox>";

			$xul.="<vbox>";
				if($r["idRubPar"]!=$oidRubPar){
					$xul.="<label value='".$r["titreRubPar"]."'/>";
					$xul.="<hbox>";
						$xul.="<label id='adminRubPar_".$r["idRubPar"]."' class='text-linkAdmin' onclick=\"OuvreLienAdmin(".$r["idRubPar"].");\" value=\"Admin\"/>";
			    		$xul.="<image onclick=\"DelRubriqueParentObs('".$r["idRubPar"]."');\" src='".$this->site->infos["pathImages"]."check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";
			
			$xul.="<vbox>";
				if($r["idRub"]!=$oidRub){
					$xul.="<label value='".$r["titreRub"]."'/>";
					$xul.="<hbox>";
						$xul.="<label id='adminRub_".$r["idRub"]."' class='text-linkAdmin' onclick=\"OuvreLienAdmin(".$r["idRub"].");\" value=\"Admin\"/>";
			    		$xul.="<image onclick=\"DelRubriqueObs('".$r["idRub"]."', '".$idRub."');\" src='".$this->site->infos["pathImages"]."check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";

			
			$xul.="<vbox hidden='true'>";
				if($r["idArt"]!=$oidArt){
					$xul.="<label value='".$r["titreArt"]."'/>";
					$xul.="<hbox>";
						$xul.="<label id='adminArt_".$r["idArt"]."' class='text-linkAdmin' onclick=\"OuvreArticle(".$r["idArt"].");\" value=\"Admin\"/>";
						$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='".$this->site->infos["pathImages"]."check_yes.png' />";
			    		$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='".$this->site->infos["pathImages"]."check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";
			
			
			$xul.="<vbox>";
				if($r["idCont"]!=$oidCont){
					$xul.="<hbox>";
						$xul.="<label value=\"Probl�me n� ".$r["idPbPlan"]." : ".$this->site->XmlParam->XML_entities($r["TextCont"])."\"/>";
						$xul.="<label   value='(".$r["idCont"].")'/>";
						$xul.="<!--<label value=' Commentaires : ".$r["obs"]."'/> -->";
		    		$xul.="</hbox>";
				}
				if($r["obs"]!=$oidObs) {
					$xul.="<hbox>";
						$xul.="<label value=\" Commentaires : ".$this->site->XmlParam->XML_entities($r["obs"])."\"/>";
					$xul.="</hbox>";
				}
				$xul.="<hbox>";
					$xul.="<label value='    - ".$r["RepCont"]."'/>";
					$xul.="<label value='".$r["aDate"]."'/>";
					$xul.="<label value='Photo : ".$r["ReponsePhoto"]."'/>";
					$xul.="<label id='adminDon_".$r["idDon"]."' class='text-linkAdmin' onclick=\"OuvreDonnee(".$this->site->infos["GRILLE_SIG_PROB"].",".$r["idDon"].");\" value=\"Admin\"/>";
					$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='".$this->site->infos["pathImages"]."check_yes.png' />";
		    		$xul.="<image onclick=\"DelArticleObs('".$r["idDon"]."', '".$idRub."');\" src='".$this->site->infos["pathImages"]."check_no.png' />";
		    	$xul.="</hbox>";
			$xul.="</vbox>";
						
		    $xul.="</row>";	
			$oidRubPar=$r["idRubPar"];
			$oidRub=$r["idRub"];
			$oidArt=$r["idArt"];
			$oidCont=$r["idCont"];
			$oidObs=$r["obs"];
		}

		$xul .='</rows>';	
		$xul .='</grid>';	
		
		
	   	return $xul;
    	
    }
    

    function GetListeChamp($idGrille=-1){
    	
    	if($idGrille==-1)
    		$idGrille=$this->id;
    	
		$sql = "SELECT fc.titre, fc.champ
				FROM spip_forms_champs fc 
				WHERE fc.id_form = ".$idGrille;
			
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		return $req;	
    	
    }
    
 
    
    
    function GetTreeObs($idRub,$AjoutObs=false, $rs=false){
    	
    	$g = new Granulat($idRub,$this->site);
    	//r?cup?re les rubriques enfants
    	$ids = $g->GetIdsScope();

    	$oXul = new Xul($this->site);
    	
		//r?cup?re les identifiants des rubriques de la racine ayant un Probl�me
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetListeObservations']";
		if($this->trace)
			echo "Grille:GetTreeObs:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $ids, $Q[0]->where);
		$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
		$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:GetTreeObs:".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		if($rs) return $result; 
		
		$xul ='<grid flex="1">';
		//on cache la colonne de r?f?rence	
		$xul.='<columns>';	
			$xul.='<column flex="1" hidden="true"/>';	
			$xul.='<column flex="1"/>';
			$xul.='<column flex="1"/>';			
			$xul.='<column flex="1" hidden="true"/>';			
			$xul.='<column flex="1"/>';			
		$xul.='</columns>';	
		$xul.='<rows>';
		
		$oidRubPar=-1;
		$oidRub=-1;
		$oidArt=-1;
		
		while ($r =  $db->fetch_assoc($result)) {
			if($this->trace)
				echo "Grille:GetTreeObs:".$r["idRub"]." ".$r["idArt"]." ".$r["idDon"]."<br/>";

			$idDoc = 'val'.DELIM.$this->site->infos["GRILLE_OBS"].DELIM.$r["idDon"].DELIM."Sup".DELIM.$r["idArt"];
			$xul.="<row>";
			$xul.="<vbox hidden='true' >";
				$xul.="<label id='".$idDoc."' value='".$idDoc."' />";
			$xul.="</vbox>";

			//source des observations
			if(!$AjoutObs){
				$xul.="<vbox>";
					if($r["idRubPar"]!=$oidRubPar){
						$xul.="<label value=\"".$this->site->XmlParam->XML_entities($r["titreRubPar"])."\"/>";
						$xul.="<hbox>";
							$xul.="<label id='adminRubPar_".$r["idRubPar"]."' class='text-linkAdmin' onclick=\"OuvreLienAdmin(".$r["idRubPar"].");\" value=\"Admin\"/>";
				    		$xul.="<!--<image onclick=\"SetVal('".$idDoc."');\" src='".$this->site->infos["pathImages"]."check_yes.png' /> -->";
				    		$xul.="<image onclick=\"DelRubriqueParentObs('".$r["idRubPar"]."');\" src='".$this->site->infos["pathImages"]."check_no.png' />";
				    	$xul.="</hbox>";
					}
				$xul.="</vbox>";
			}
						
			//
			if(!$AjoutObs){
				$xul.="<vbox>";
				if($r["idRub"]!=$oidRub){
					$xul.="<label value=\"".$this->site->XmlParam->XML_entities($r["titreRub"])."\"/>";
					$xul.="<hbox>";
						$xul.="<label id='adminRub_".$r["idRub"]."' class='text-linkAdmin' onclick=\"OuvreLienAdmin(".$r["idRub"].");\" value=\"Admin\"/>";
			    		$xul.="<!--<image onclick=\"SetVal('".$idDoc."');\" src='".$this->site->infos["pathImages"]."check_yes.png' /> -->";
			    		$xul.="<image onclick=\"DelRubriqueObs('".$r["idRub"]."', '".$idRub."');\" src='".$this->site->infos["pathImages"]."check_no.png' />";
			    	$xul.="</hbox>";
				}
				$xul.="</vbox>";
			}

			
			$xul.="<vbox hidden='true'>";
				if($r["idArt"]!=$oidArt){
					$xul.="<label value=\"".$this->site->XmlParam->XML_entities($r["titreArt"])."\"/>";
					$xul.="<hbox>";
						$xul.="<label id='adminArt_".$r["idArt"]."' class='text-linkAdmin' onclick=\"OuvreArticle(".$r["idArt"].");\" value=\"Admin\"/>";
						$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='".$this->site->infos["pathImages"]."check_yes.png' />";
			    		$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='".$this->site->infos["pathImages"]."check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";
			
			
			$xul.="<vbox>";
			if($r["idCont"]!=$oidCont && !$AjoutObs){
				$xul.="<hbox>";
					$xul.="<label value=\"Problème n° ".$r["idPbPlan"]." : ".$this->site->XmlParam->XML_entities($r["TextCont"])."\"/>";
					$xul.="<label   value='(".$r["idCont"].")'/>";
					$xul.="<!--<label value=\" Commentaires : ".$r["obs"]."\"/> -->";
	    		$xul.="</hbox>";
			}
			if($r["obs"]!=$oidObs  && !$AjoutObs) {
				$xul.="<hbox>";
					$xul.="<label value=\" Commentaires : ".$this->site->XmlParam->XML_entities($r["obs"])."\"/>";
				$xul.="</hbox>";
			}
			$xul.="<hbox >";
				$xul.="<label value=' - ".$r["RepCont"]."'/>";
				$xul.="<label value=\" : ".$this->site->XmlParam->XML_entities($r["obs"])."\" />";
				$xul.="<label value='".$r["aDate"]."'/>";
				$xul.="<label value='Photo : ".$r["ReponsePhoto"]."' />";				
				$xul.="<label id='adminDon_".$r["idDon"]."' class='text-linkAdmin' onclick=\"OuvreDonnee(".$this->site->infos["GRILLE_OBS"].",".$r["idDon"].");\" value=\"Admin\"/>";
				$xul.="<image onclick=\"GetXulForm(".$this->site->infos["GRILLE_OBS"].",".$r["idDon"].",'".$idDoc."_com');\" src='".$this->site->infos["pathImages"]."check_yes.png' />";
	    		$xul.="<image onclick=\"DelArticleObs('".$r["idDon"]."', '".$idRub."', ".$AjoutObs.");\" src='".$this->site->infos["pathImages"]."check_no.png' />";
	    	$xul.="</hbox>";
			//pour voir le d?tail
	    	$xul.="<box id='".$idDoc."_com' />";
	    	if($r["ReponsePhoto"]){
				$xul.= $oXul->GetFriseDocs($idRub, "images", $r["idArt"],100,60);
			}	    	
	    	$xul.="</vbox>";
						
		    $xul.="</row>";	
			$oidRubPar=$r["idRubPar"];
			$oidRub=$r["idRub"];
			$oidArt=$r["idArt"];
			$oidCont=$r["idCont"];
			$oidObs=$r["obs"];
		}

		$xul .='</rows>';	
		$xul .='</grid>';	
		
		
	   	return $xul;
    	
    }
    
	function GetTreeCsv($idRub){
    	
    	$g = new Granulat($idRub,$this->site);
    	//r?cup?re les rubriques enfants
    	$ids = $g->GetIdsScope();
    	
		//r?cup?re les identifiants des rubriques de la racine ayant un Probl�me
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetListeSignalementProbleme']";
		if($this->trace)
			echo "Grille:GetTreeCsv:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $ids, $Q[0]->where);
		$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
		$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:GetTreeCsv:".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		
		header("Content-Type: application/csv-tab-delimited-table"); // text/csv
		header("Content-disposition: attachment; filename=SignalementPb.csv"); 
		header('Expires: 0');
		header('Pragma: no-cache'); 
		
		echo 'Rubrique Parent;Rubrique;Id Crit?re;Id Pb;Questions Probl�me;Crit?re r?glementaire;Date;Observations';
		echo "\n";
		
		while ($r =  $db->fetch_assoc($result)) {
			if($this->trace)
				echo "Grille:GetTreeCsv:".$r["idRub"]." ".$r["idArt"]." ".$r["idDon"]."<br/>";

			echo $r["titreRubPar"].";";
			echo $r["titreRub"].';';
			echo $r["idCrit"].';';
			echo $r["idPbPlan"].';';
			$text = html_entity_decode($this->site->XmlParam->XML_entities($r["TextCont"]));
			echo str_replace(';', ',', $text).';';
			//echo $r["idCont"].';';
			//crit?re r?glementaire
			if($r["regle"])
				echo 'oui;';
			else
				echo 'non;';
			echo $r["aDate"].';';
			$textObs = html_entity_decode($r["obs"]);
			echo str_replace(';', ',', $textObs).';';
			echo "\n" ;	
		}    	
    }
    
    function RechercheDonneeId($grille,$idArt,$champ,$valeur,$idsRub=-1,$ref=-1) {
		if($this->trace)
			echo "Grille:RechercheDonneeId://recherche l'id d'une donn?e avec son article $idArt sa valeur = $valeur et son champ=$champ <br/>";
		
		//r?cup?re la requ?te suivant le type de recherche	
		if($idsRub==-1)
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_RechercheDonneeId']";
		else{
			if(!$ref)
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_RechercheDonneeIdByRub']";
			else		
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_RechercheDonneeIdByRubRef']";
		}		
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $idsRub, $Q[0]->where);
		$from = str_replace("-champ-", $champ, $Q[0]->from);
		$from = str_replace("-valeur-", $valeur, $from);
		$from = str_replace("-grille-", $grille, $from);
		$from = str_replace("-idArt-", $idArt, $from);
		$from = str_replace("-ref-", $ref, $from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row["idDon"];
    	
    }
    
    
    function GetObjId($donId,$obj) {
		if($this->trace)
			echo "Grille:GetObjId://r?cup?re l'identifiant de l'objet ".$obj." ".$donId."<br/>";

		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetId".$obj."']";
    	
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $donId, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row["id"];
    	
    }

    function GereScenarisation($row, $donId, $qiParent) {

    	$xul="";
		$critere = $this->GetValeur($donId,'ligne_1'); 
		//v?rifie s'il faut prendre en compte la r?ponse de la question parente
		if($qiParent==1)
	    	$Xpath = "//question[@id='".$critere."']";
	    else{
	    	//r?cup?re les infos de la question parente
	    	$arrQi = split("_",$qiParent);
	    	$criterePar = $this->GetValeur($arrQi[2],'ligne_1'); 
	    	$reponsePar = $this->GetValeur($arrQi[2],'mot_1'); 
	    	$Xpath = "//question[@id='".$criterePar."' and @reponse='".$reponsePar."']/question[@id='".$critere."']";
	    }
	    	
    	if($this->trace)
			echo "Grille:GereScenarisation:r?cup?re les param?tre ? ex?cuter ".$Xpath."<br/>";
    	$scena = $this->XmlScena->GetElements($Xpath);
    	
    	if(!$scena) return;
    	
		$idArt = $this->GetObjId($donId,'Article');
    	
    	foreach($scena as $qi)
		{
			//v?rifie que la r?ponse correspond au crit?re
			//ou n'est pas d?finie
			if($qi["reponse"]==$row["valeur"] || !$qi["reponse"]){
				$OldCrit = "";
		    	foreach($qi as $q)
				{
					//r?cup?re les param?tre de la question
					$critere = $q["id"]."";
					//pour ?viter la cr?ation de doublon pour une sous question interm?diare 
					//cf. douche
					if($critere != $OldCrit){
						$OldCrit=$critere;
						$idDon = $this->GetDonneeCritere($idArt,$critere);
						
						//v?rifie si la donn?e est trouv?e
						if(!$idDon){
							return "<label value=\"Ce crit?re ".$critere." n'existe pas !\" />";
						}
						
						//v?rifie si la donn?e correspond au choix de diagnostic
						$verif = $this->VerifChoixDiagnostic(-1, $_SESSION['type_controle'], $_SESSION['type_contexte'],$critere); 
						if($verif){
							//v?rifie s'il faut cr?er la r?ponse ? la question
							if($q["valeur"]){
								//r?pond ? la question
								$r = array("grille"=>$row["grille"],"champ"=>$q["champ"],"valeur"=>$q["valeur"]);
								$this->SetChamp($r, $idDon);
								$this->GereWorkflow($row, $idDon);		
							}else{
								//cr?ation du formulaire
								$xul .= $this->GetXulForm($idDon,$row["grille"]);
							}
						}
					}
				}
			}
		}
    	return $xul;
    }
    
    function GereWorkflow($row, $donId) {

    	$xul="";
    	$Xpath = "/XmlParams/XmlParam/workflow[@srcId='".$row['grille'].";".$row['champ']."']";
		if($this->trace)
			echo "Grille:GereWorkflow:r?cup?re les param?tre du workflow ? ex?cuter ".$Xpath."<br/>";
    	$wfs = $this->site->XmlParam->GetElements($Xpath);
    	
    	if(!$wfs) return;

    	foreach($wfs as $wf)
		{
			//v?rifie s'il faut r?cup?rer l'identifiant de l'objet de destination
			if($wf['dstObj'])
				$id = $this->GetObjId($donId,$wf['dstObj']);

			switch ($wf['dstQuery']) {
				case "ShowArtGrille":
					if($this->trace)
						echo "Grille:GereWorkflow:".$wf['dstQuery']."==".$donId."<br/>";					
					//r?cup?re le formulaire xul
					$xul = $this->GetXulForm($donId,$this->site->infos["GRILLE_SIG_PROB"]);
					break;	
				case "AddNewTab":
					$xul = $this->GetXulTabPanels($row['idRub'],$this->site->infos["GRILLE_SIG_PROB"],"SignalementProbleme");
					break;	
				case "AddNewArtGrille":
					if($this->trace)
						echo "Grille:GereWorkflow:AddNewArtGrille ".$row['valeur']."==".$wf['srcCheckVal']."<br/>";					
					if($row['valeur']==$wf['srcCheckVal']){						
						//r?cup?ration du granulat
						$gra = new Granulat($id,$this->site);
						
						if($wf['trsObjet']=="controles" ){
							$gTrs = new Granulat($wf['trsId'],$this->site);
							$id = $gra->SetNewEnfant($gTrs->titre);
							$this->AddQuestionReponse($wf['trsId'],$id);
							if($wf['trsId']==$this->site->infos["RUB_PORTE1"] 
								|| $wf['trsId']==$this->site->infos["RUB_PORTE1"] )
									{ // Porte
								$id1 = $gra->SetNewEnfant("Face 1 ");
								$this->AddQuestionReponse($this->site->infos["RUB_PORTE_FACE"],$id1);
								$id2 = $gra->SetNewEnfant("Face 2 ");
								//il n'y a qu'une rubrique pour la face des portes
								$this->AddQuestionReponse($this->site->infos["RUB_PORTE_FACE"],$id2);
							}
						}else{
							//gestion pour le signalement Probl�me
							if($wf['trsId']==$this->site->infos["GRILLE_SIG_PROB"]){
								$ref = $this->GetValeur($donId,"ligne_1");
								$reponseId = $this->GetValeur($donId,"mot_1");
								$reponse = $this->GetMot($reponseId);
								
								$idArt = $gra->SetNewArticle("Probl�me ".$ref." ".date('j/m/y - H:i:s'));
								//ajoute une nouvelle donnee
								$idDon = $this->AddDonnee($id, $wf['trsId'], false, $idArt);
								if($this->trace)
									echo "Grille:GereWorkflow://gestion pour le signalement Probl�me ".$ref."<br/>";
								$row=array("champ"=>"ligne_3","valeur"=>$ref);
								$this->SetChamp($row,$idDon);
								$row2=array("champ"=>"ligne_5","valeur"=>$reponse);
								$this->SetChamp($row2,$idDon);
							}else{
								if($wf['trsId']==$this->site->infos["GRILLE_OBS"]){
									$ref = $this->GetValeur($donId,"ligne_1");
									$reponseId = $this->GetValeur($donId,"mot_1");
									$reponse = $this->GetMot($reponseId);
									
									$idArt = $gra->SetNewArticle("Observations ".$ref." ".date('j/m/y - H:i:s'));
									//ajoute une nouvelle donnee
									$idDon = $this->AddDonnee($id, $wf['trsId'], false, $idArt);
									if($this->trace)
										echo "Grille:GereWorkflow://gestion pour les observations ".$ref."<br/>";
									$row=array("champ"=>"ligne_4","valeur"=>$donId);
									$this->SetChamp($row,$idDon);
									$row=array("champ"=>"ligne_5","valeur"=>$ref);
									$this->SetChamp($row,$idDon);
									$row=array("champ"=>"ligne_1","valeur"=>$reponse);
									$this->SetChamp($row,$idDon);
								} else {
									$idArt = $gra->SetNewArticle($gTrs->titre);
									//ajoute une nouvelle donnee
									$idDon = $this->AddDonnee($id, $wf['trsId'], false, $idArt);	
								}
							}
							//r?cup?re le formulaire xul
							$xul = $this->GetXulForm($idDon,$wf['trsId']);
						}
						//renvoie le formulaire
						return $xul;
					}
					break;	
				case "AddNewMotClef":	
					if($this->trace)
						echo "Grille:GereWorkflow:AddNewMotClef ".$row['valeur']."==".$wf['srcCheckVal']."<br/>";	
					if($row['valeur']==$wf['srcCheckVal']){	
						$gra = new Granulat($id,$this->site);	
						if($wf['trsObjet']=="motclef" ){
							$gra->SetMotClef($wf['trsId'],$id);
						}	
					}
					break;	
				case "ShowDonnee":	
					if($wf['trsId']==$this->site->infos["GRILLE_SIG_PROB"] || $wf['trsId']==$this->site->infos["GRILLE_OBS"]) {
						//r?cup?re le formulaire xul
						$xul = $this->GetXulForm($donId,$wf['trsId']);
					}
					return $xul;
					break;	
				default:								
					if($this->trace)
						echo "//workflow path query ".$wf['dstQuery']."<br/>";
					
					$Q = $this->site->XmlParam->GetElements($wf['dstQuery']);
					$where = str_replace("-id-", $id, $Q[0]->where);
					$set = str_replace("-param-", $row['valeur'], $Q[0]->set);
					$sql = $Q[0]->update.$set.$where;
					$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
					$db->connect();
					$db->query($sql);
					$db->close();
					if($this->trace)
						echo "//ex?cution du workflow ".$sql."<br/>";
				break;
			}								
		}
		
		if($this->trace)
			echo "Grille:GereWorflow:xul=".$xul."<br/>";
		return $xul;
		
	}	

	function GetMot($idMot) {
		
		//r?cup?re la valeur d'un champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetMot']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idMot, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row['titre'];
		
	}
	
	function GetIdMot($titre) {
		//r?cup?re la valeur d'un champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetIdMot']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-titre-", $titre, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row['id_mot'];
		
	}
	
	function GetGrilleId($rows, $donId) {

    	$Xpath = "/XmlParams/XmlParam/majliee[@srcId='55;ligne_1']/@dstQuery";
		$donnees = $xml->GetElements($Xpath);
		if($this->trace)
			echo "//r?cup?ration des valeurs de workflow ".$donnees."<br/>";
    	
		//suppression des ?ventuelle champ pour la donn?e
		$this->DelDonnee($donId);
		
		//cr?ation des valeurs
		while ($row =  mysql_fetch_assoc($rows)) {
			$this->SetChamp($row, $donId, false);
			//echo "--- ".$donId." nouvelle valeur ".$i;
		}
		
	}	
	
	function AddXmlDonnee($xmlSrc){
			
		if($this->trace)
			echo "Grille/AddXmlDonnee IN //r?cuparation de la d?finition des donn?es ".$xmlSrc."<br/>";
		$xml = new XmlParam($xmlSrc);		
		
		$action = $xml->xml->grille["action"]."";
		
		$Xpath = "/donnees";
		$donnees = $xml->GetElements($Xpath);
		if($this->trace)
			echo "Grille/AddXmlDonnee/r?cup?ration des valeurs de donn?e ".$donnees."<br/>";
		
		$idGrille = $donnees[0]->grille;
		if($this->trace)
			echo "Grille/AddXmlDonnee/r?cup?ration de l'identifiant de la grille ".$idGrille."<br/>";
		
		//r?cup?ration de la d?finition des champs
		$Xpath = "/donnees/champs";
		$champs = $xml->GetElements($Xpath);
		$first=true;
		foreach($donnees[0]->donnee as $donnee)
		{
			$idRub = $donnee->rub;
			if($this->trace)
				echo "Grille/AddXmlDonnee/- r?cup?ration de l'identifiant de la rubrique ".$idRub."<br/>";
			
			//r?cuparation du granulat
			$g = new Granulat($idRub, $this->site); 
			$idArt = $g->GetArticle();
			if($this->trace)
				echo "Grille/AddXmlDonnee/- r?cup?ration ou cr?ation du dernier article en cours de r?daction ".$idArt."<br/>";
			
				//v?rifie s'il fut supprimer les valeurs
			if($action!="ajout"){
				if($first){
					$this->DelGrilleArt($idGrille,$idArt);
					if($this->trace)
						echo "Grille/AddXmlDonnee/suppression des anciennes donn?es ".$idArt."<br/>";
					$first=false;
				}
					
				$idDon = $g->GetIdDonnee($idGrille, $idArt, true);
				if($this->trace)
					echo "Grille/AddXmlDonnee/- cr?ation de la donnee ".$idDon."<br/>";
				$supChamp = false;
			}else{
				$supChamp = true;
			}
			
			$i=0;
			foreach($donnee->valeur as $valeur)
			{
				$valeur=utf8_decode($valeur);
				$champ = $champs[0]->champ[$i];
				//v?rifie s'il faut r?cup?rer l'id_donn?e
				if($i==0 && $action=="ajout"){
					$idDon = $this->RechercheDonneeId($idGrille,$idArt,$champ,$valeur);	
				}
				if($valeur!='non'){
					if($this->trace)
						echo "Grille/AddXmlDonnee/--- gestion des champs multiples ".substr($champ,0,8)."<br/>";
					if(substr($champ,0,8)=="multiple"){
						$valeur=$champ;
						//attention il ne doit pas y avoir plus de 10 choix
						$champ=substr($champ,0,-2);
					}
					if($this->trace)
						echo "Grille/AddXmlDonnee/-- r?cup?ration du type de champ ".$champ."<br/>";
					$row = array('champ'=>$champ, 'valeur'=>$valeur);
					if($this->trace)
						echo "Grille/AddXmlDonnee/-- r?cup?ration de la valeur du champ ".$valeur."<br/>";
					$this->SetChamp($row, $idDon,$supChamp);
					if($this->trace)
						echo "Grille/AddXmlDonnee/--- cr?ation du champ <br/>";
				}
				$i++;
			}
			
		}
		if($this->trace)
			echo "Grille/AddXmlDonnee OUT //<br/>";
		
	}
    
    function AddGrilles($idRubSrc, $idRubDst, $redon=false){
			
		//r?cuparation des grilles des articles publi?s de la rubrique
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetGrillesPublie']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRubSrc, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "AddGrilles ".$idRubSrc." ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		
		$result = ""; 
		while ($row =  $db->fetch_assoc($rows)) {
			$idDon = $this->AddDonnee($idRubDst, $row["id_form"], $redon);
			$result .= $row["id_form"]." ".$row["titre"]." ".$idDon."<br/>";		
		}
		
		return $result;
	}
	
	
	function AddQuestionReponse($idRubSrc, $idRubDst){
		
		//cr?ation du granulat
		$g = new Granulat($idRubDst,$this->site);
		
		/*la rubrique des questions est directement pass?e en param?tre 			
		//pour les controles r?cup?ration des rubriques dans les liens de la rubrique Src 
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetRubInLiens']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRubSrc, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Grille:AddQuestionReponse:rubSrc".$sql."<br/>";
						
		while ($row =  $db->fetch_assoc($rows)) {
*/		

			//r?cup?ration du droit de la derni?re donn?e pour la rubrique parente de la destination
			$droit = $this->GetDroitParent($g->IdParent);
			
			//r?cup?ration des questions publi? pour un type de controle
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_AddQuestion']";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-idRubSrc-", $idRubSrc, $Q[0]->where);
			//ajoute les crit?re de version
			$from = str_replace("-idForm-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $Q[0]->from);			
			$sql = $Q[0]->select.$from.$where;
			$dbQ = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$dbQ->connect();
			$rowsQ = $dbQ->query($sql);
			$dbQ->close();
			if($this->trace)
				echo "Grille:AddQuestionReponse:Liste question".$sql."<br/>";
			$first=true;
			$rowQo = -1;
			while ($rowQ =  $dbQ->fetch_assoc($rowsQ)) {
				if($first){
					//ajoute le mot clef type de controle ? la rubrique
					$g->SetMotClef($rowQ["typecon"]);
					$first=false;
				}
				//v?rifie si le contr?le est coh?rent par rapport au parent
				if($this->GereCoheDroit($rowQ, $droit)){
					//prise en compte des doublons suite ? l'attribution de plusieurs droits
					if($rowQo != $rowQ["ref"]){
						//ajoute une nouvelle donn?e r?ponse pour la question
						$idDon = $g->GetIdDonnee($rowQ["FormRep"],-1,true);
						if($this->trace)
							echo "Grille:AddQuestionReponse:ajoute une nouvelle donn?e r?ponse pour la question".$idDon."<br/>";
						//ajoute la question
						$r = array("champ"=>"ligne_2","valeur"=>$rowQ["question"]);
						$this->SetChamp($r,$idDon,false);
						//ajoute la r?f?rence
						$r = array("champ"=>"ligne_1","valeur"=>$rowQ["ref"]);
						$this->SetChamp($r,$idDon,false);
						//ajoute la r?ponse par d?faut
						$r = array("champ"=>"mot_1","valeur"=>$rowQ["valdef"]);
						$this->SetChamp($r,$idDon,false);
						//ajoute la donn?e r?f?rente
						$r = array("champ"=>"ligne_3","valeur"=>$rowQ["id_donnee"]);
						$this->SetChamp($r,$idDon,false);
								
						$rowQo = $rowQ["ref"];
					}
				}
			}
		//}
		
	}
	
	function GereCoheDroit($rQ, $droit){

		return true;
		
		//v?rifie si la question est coh?rente par rapport au questionnaire parent
		//$Xpath = "/XmlParams/XmlParam/CoheDroit[@srcId='".$rQ['id_form'].";".$row['droit']."']";
		$Xpath = "/XmlParams/XmlParam/CoheDroit[@dstId='".$rQ['id_form']."' and @dstCheckVal='".$rQ['droit']."' and @srcCheckVal='".$droit."' ]";
		if($this->trace)
			echo "Grille:GereCoheDroit:r?cup?re la coh?rence ".$Xpath."<br/>";
    	$coh = $this->site->XmlParam->GetCount($Xpath);
		if($this->trace)
			echo "Grille:GereCoheDroit:coh=".$coh."<br/>";
    
    	if($coh>0)
    		$cohe=true;
    	else
    		$cohe=false;
		return $cohe;
	}

	function GetDroitParent($id){
		//r?cup?ration des droits pour la rubrique parente
		$rParDon = $this->GetLastDonne($id);

		//r?cup?re le champ droit de la donn?e du parent
		$Xpath = "/XmlParams/XmlParam/CoheDroit[@srcId='".$rParDon['id_form']."']/@srcChamp";
    	$srcChamps = $this->site->XmlParam->GetElements($Xpath);
		$srcChamp = $srcChamps[0];
		
		//r?cup?re la valeur du champ droit
		$droit = $this->GetValeur($rParDon['id_donnee'], $srcChamp);
		
		return $droit;
	}

	function GetValeur($idDon, $champ){
		//r?cup?re la valeur d'un champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetValeurChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$where = str_replace("-champ-", $champ, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row['valeur'];
	}
	
	function GetLastDonne($id){
		//r?cup?ration de la derni?re donn?e d'une rubriques 
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetLastDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row;
	}

	function GetDonneeCritere($idArt,$critere){
		//pour la sc?narisarisation
		//r?cup?ration de la donn?e d'un article correspondant au crit?re 
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetDonneeCritere']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idArt-", $idArt, $Q[0]->where);
		$from = str_replace("-critere-", $critere, $Q[0]->from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row['id_donnee'];
	}
	
	
	function AddDonnee($idRub, $idGrille=-1, $redon=false, $idArt=-1,$doublon=false){
		
		if($idGrille==-1)
			$idGrille=$this->id;
			
		//r?cuparation du granulat
		$g = new Granulat($idRub, $this->site);
		
		if($idArt==-1)
			//"r?cup?ration ou cr?ation du dernier article en cours de r?daction"; 
			$idArt = $g->GetArticle(" AND a.statut='prepa'");
				
		if($redon){
			//r?cup?re les derni?res donn?es publi?es
			$g = new Granulat($redon, $this->site);
			$rows = $g->GetGrille($idGrille, " AND a.statut='publie'");
			$oDonnee="";
			while ($row =  mysql_fetch_assoc($rows)) {
				//v?rifie s'il on change de donnee
				if($row["id_donnee"]!=$oDonnee){
					$idDon = $g->GetIdDonnee($idGrille, $idArt, true);
					$oDonnee=$row["id_donnee"];
				}
				$this->SetChamp($row, $idDon, false);
				//echo "--- ".$donId." nouvelle valeur ".$i;
			}
		}else{
			//r?cup?ration ou cr?ation d'une nouvelle donn?e
			$idDon = $g->GetIdDonnee($idGrille, $idArt, $doublon);
			//r?cup?re la d?finition des champs sans valeur
			$rows = $this->GetChamps($idGrille);
			//initialisation de la donn?e
			$this->SetChamps($rows, $idDon);
		}

		//echo "idRub = ".$idRub." idArt = ".$idArt." idDon = ".$idDon."<br/>"; 
		return $idDon;
	
	}

	function GetChamps($idGrille=-1){
	
		if($idGrille==-1)
			$idGrille=$this->id;

		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetChamps']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idGrille-", $idGrille, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo $sql."<br/>";
		
		return $result;
	
	}
	
	function SetChamps($rows, $donId) {

		//suppression des ?ventuelle champ pour la donn?e
		$this->DelDonnee($donId);
		
		//cr?ation des valeurs
		while ($row =  mysql_fetch_assoc($rows)) {
			$this->SetChamp($row, $donId, false);
			//echo "--- ".$donId." nouvelle valeur ".$i;
		}
		
	}	
	  
	function DelDonnee($donId) {

		//Supression des valeurs de champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_DelDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $donId, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo "Grille:DelDonnee:".$sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		
	}	

	function DelGrilleDonnee($donId) {

		//Supression des valeurs de champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_DelGrilleDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $donId, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo "Grille:DelGrilleDonnee:".$sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		
	}	
	
	function DelGrilleArt($idGrille, $idArt) {

		if($this->trace)
			echo "Grille:DelGrilleArt:GetDonneeArtForm $idGrille, $idArt<br/>";
		//r?cup?ration des donn?es pour un article et une grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonneeArtForm']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idArt-", $idArt, $Q[0]->where);
		$from = str_replace("-idGrille-", $idGrille, $Q[0]->from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Grille:DelGrilleArt:GetDonneeArtForm=".$sql."<br/>";
		//echo $sql."<br/>";
		while ($r =  $db->fetch_assoc($result)) {
			//Supression des valeurs de champ
			$this->DelDonnee($r["id_donnee"]);
			//suppression des donnee
			$this->DelGrilleDonnee($r["id_donnee"]);
		}
		
	}	
	
	function SetChamp($row, $donId, $del=true) {

		if($del)
			//supression de la valeur
			$this->DelChamp($row, $donId); 
		
		//prise en compte des choix multiple
		if($row["valeur"]=="supprime")
			return;
			
		//prise encompte de l'importation par csv
		$arrC = split("_",$row["champ"]);
		if(count($arrC)>2){
			//on traite le champ d'un csv
			if($row["valeur"]=="oui"){
				$row["valeur"] = $row["champ"];
				$row["champ"] = $arrC[0]."_".$arrC[1];	
			}else{
				return;
			}
		}
		
		//cr?ation de la valeur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_InsChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$values = str_replace("-idDon-", $donId, $Q[0]->values);
		$values = str_replace("-champ-", $row["champ"], $values);
		$values = str_replace("'-val-'", $this->site->GetSQLValueString($row["valeur"],"text"), $values);
		//prise en compte des entier et des d?cimaux
		if(is_numeric($row["valeur"])){
			$values = str_replace("'-valint-'",$row["valeur"], $values);
			$values = str_replace("'-valdec-'",$row["valeur"], $values);
		}else{
			$values = str_replace("'-valint-'","0", $values);
			$values = str_replace("'-valdec-'","0", $values);
		}	
		
		$sql = $Q[0]->insert.$values;
		if($this->trace)
			echo $this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo "--- ".$donId." nouvelle valeur ".$i;
		
	}	
	
	function DelChamp($row, $donId) {

		//supression de la valeur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_DelChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $donId, $Q[0]->where);
		$where = str_replace("-champ-", $row["champ"], $where);
		//prise en compte des choix multiples
		if(substr($row['champ'], 0, 8)=='multiple')
			$where = str_replace("-choix-", $row["valeur"], $where);
		else
			$where = str_replace("AND valeur = '-choix-'", "", $where); 
		
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo $sql." ".substr($row['champ'], 0, 8)."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo "--- ".$donId." nouvelle valeur ".$i;
		
	}	
	
	function GetXulTab($src, $id, $dst="Rub", $recur = false){

		
		//chaque ligne est un onglet
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetXulTabForm".$dst."']";
		if($this->trace)
			echo "GetXulTab Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "GetXulTab ".$dst." ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();

		//initialisation de la tabbox
		$tabbox = '<tabbox class="tabbox" flex="1" id="tabbox_'.$src.'_'.$dst.'_'.$id.'">';
		$tabbox .= '<tabs>';
		$i=0;
		$tabpanels ="";
		while ($r =  $db->fetch_assoc($result)) {
			//on exclu les grille g?o
			if($r["id"]!=$this->site->infos["GRILLE_GEO"] && $r["id"]!=$this->site->infos["GRILLE_GEORSS"]){
				$tabbox .= '<tab class="tab" id="tab'.$r["id"].'" label="'.$r["titre"].'" />';
				//v?rifie s'il faut cr?er un formulaire ou un sous onglet
				if($Q[0]->dst=='Form' )
					$tabpanels .= $this->GetXulTabPanels($r["idArt"], $r["id"],'Form',$recur);
				else
					$tabpanels .= $this->GetXulTabPanels($src, $r["id"],$Q[0]->dst,$recur);
			}
			$i++;
		}
		
		//prise en compte des onglets li?s par le workflow
		$row = array("idRub"=>$id,"grille"=>"GetXulTabForm","champ"=>$dst);
		$WFtabpanels = $this->GereWorkflow($row,-1);
		if($WFtabpanels!=""){
			$tabbox .= '<tab class="tab" id="tabWF'.$r["id"].'" label="Signalement(s) probl�me(s)" />';
			
		}
		
		
		if($i!=0){
			$tabbox .= '</tabs>';
			$tabbox .= '<tabpanels>';
			$tabbox .= $tabpanels;
			$tabbox .= $WFtabpanels;
			$tabbox .= '</tabpanels>';
			$tabbox .= '</tabbox>';
		}else
			$tabbox = "";
			
		return $tabbox;
		
	}


	function GetXulTabPanels($src, $id, $dst="Rub", $recur = false){

		//on n'affiche pas les grille g?olo
		if($id == $this->site->infos["GRILLE_GEO"] || $id==$this->site->infos["GRILLE_GEORSS"])
			return;

		$oXul = new Xul($this->site);	
		$gra = new Granulat(-1,$this->site);
			
		//r?cup?re les articles de la rubrique
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetXulTabPanels".$dst."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$where = str_replace("-src-", $src, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetXulTabPanels ".$this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$req = $db->query($sql);
		$db->close();

		//initialisation du panel
		$tabpanel = '<tabpanel class="tabpanel" flex="1" id="tabpanel_'.$src.'_'.$dst.'_'.$id.'">';	
		
		//ajoute les onglets des sous rubriques
		if($recur)
			$tabpanel .= $this->GetXulTab($src, $id, $dst, $recur);
		
		//ajoute les groupbox pour chaque article
		if($id==$this->site->infos["GRILLE_REP_CON"]){
			$tabpanel .='<grid flex="1">';
			//on cache la colonne de r?f?rence	
			$tabpanel .='<columns>';	
			$tabpanel .='<column hidden="true"/>';	
			$tabpanel .='<column flex="1"/>';
			$tabpanel .='<column />';			
			$tabpanel .='<column />';			
			$tabpanel .='</columns>';	
			$tabpanel .='<rows>';	
			$tabpanel .='<row><label value="R?f?rence" hidden="true" /><label value="Question"/><label value="R?ponse"/><label value="Observations"/></row>';	
		}
		if($id==$this->site->infos["GRILLE_SIG_PROB"]){
			$tabpanel .='<vbox flex="1">';
		}
		$MemeId=false;
		while($r = $db->fetch_assoc($req)) {
			//$tabpanel .= '<groupbox >';	
			//$tabpanel .= '<caption label="'.$r["titre"].'"/>';
			if($Q[0]->dst=='Form'){
				//ex?cution suivant les type de grille
				switch ($id) {
					case $this->site->infos["GRILLE_GEO"]:
						$tabpanel .= "";
						break;
					case $this->site->infos["GRILLE_GEORSS"];
						$tabpanel .= "";
						break;
					case $this->site->infos["GRILLE_ACTEUR"]:
						//construction des ?l?ments du panel 
						$idDoc = "acteur*".$dst."*".$r["id"]."*".$id."*".$src;
						if(!$MemeId){
							$tabpanel .="<vbox flex='1' id='".$idDoc."' >";
							$tabpanel .="<hbox>";
							$tabpanel .="<button label='Ajouter un acteur' oncommand=\"AddNewDonnee('".$idDoc."',".$this->site->infos["GRILLE_ACTEUR"].");\"/>";
							$tabpanel .="</hbox>";
							$MemeId = true;
						}
						$tabpanel .="<groupbox><hbox>";
						$tabpanel .= $this->GetXulForm($r["id"], $id);
						$tabpanel .="</hbox></groupbox>";												
						break;
					case $this->site->infos["GRILLE_SIG_PROB"]:
						$tabpanel .='<hbox>';
						$tabpanel .='<vbox>';
						//ajoute le nom de l'article 
						$tabpanel .='<label value="'.$r["titre"].'" />';
						//ajoute la carte 
						$tabpanel .= $this->GetXulCarto(-1,$src);
						$tabpanel .='</vbox>';
						//ajoute les donn?es de chaque article
						$tabpanel .= $this->GetXulForm($r["id"], $id);
						$tabpanel .='</hbox>';
						break;
					case $this->site->infos["GRILLE_REP_CON"]:
						$verif = $this->VerifChoixDiagnostic($r["id"], $_SESSION['type_controle'], $_SESSION['type_contexte']); 
						if ($verif) {
							$arrTabpanel[$this->ordre]= $this->GetXulForm($r["id"], $id);
						}
						break;
					default:
						//v?rifie s'il faut afficher une carte
						$idDon = $this->VerifDonneeLienGrille($r["id"],$this->site->infos["GRILLE_GEO"]);
						if($idDon && $id!=$this->site->infos["GRILLE_ACTEUR"]){
							$carto = $this->GetXulForm($idDon, $this->site->infos["GRILLE_GEO"]);
							$AddGeo = "";
						}else{
							$carto = "";
							$m = "Ajouter une position";
							$AddGeo ="<button label='".$m."' oncommand=\"AddPlacemark(".$r["idRub"].",'".$this->type."');\"/>";
						}
						
						//construction des ?l?ments du panel 
						$idDoc = "box*".$dst."*".$r["id"]."*".$id."*".$src;
						$tabpanel .="<vbox flex='1' id='".$idDoc."' >";

						//v?rifie s'il faut afficher le bouton d'ajout d'acteur
						$idDon = $this->VerifDonneeLienGrille($r["id"],$this->site->infos["GRILLE_ACTEUR"]);
						$AddActeur ="";
						if(!$idDon){
							$AddActeur .="<hbox>";
							$AddActeur .="<button label='Ajouter un acteur' oncommand=\"AddNewDonnee('".$idDoc."',".$this->site->infos["GRILLE_ACTEUR"].");\"/>";
							$AddActeur .="</hbox>";
						}
						
						//ajout le bloc document
						$idDoc = "doc*".$dst."*".$r["id"]."*".$id."*".$src;
						$tabpanel .="<hbox flex='1'>";
						$tabpanel .="<vbox flex='1'>";
						$tabpanel .= $AddActeur;
						$tabpanel .= $oXul->GetFriseDocsIco($src,$idDoc);
						$tabpanel .="</vbox>";
						$tabpanel .="</hbox>";
												
						//ajoute le bloc du formaulaire de la grille principale
						$tabpanel .="<hbox flex='1'>";
						$tabpanel .= $this->GetXulForm($r["id"], $id);
						$tabpanel .= $AddGeo;
						$tabpanel .= $carto;
						$tabpanel .="</hbox>";
												
						//fin des ?l?ment du panel
						$tabpanel .="</vbox>";
				}				
				
			
			}else{
				//ajoute la tabbox de destination
				$tabpanel .= $this->GetXulTab($src, $r["id"], $Q[0]->dst, $recur);	
			}
		}
		if($id==$this->site->infos["GRILLE_REP_CON"]){
			if($arrTabpanel){
				//prise en compte de l'ordre de sc?narisation des crit?res
				ksort($arrTabpanel);
				foreach($arrTabpanel as $p){
					$tabpanel .= $p;
				}
			}
			$tabpanel .='</rows>';	
			$tabpanel .='</grid>';	
		}
		if($id==$this->site->infos["GRILLE_SIG_PROB"]){
			$tabpanel .='</vbox>';
		}
		if($id==$this->site->infos["GRILLE_ACTEUR"]){
			$tabpanel .='</vbox>';
		}	
		
		
		$tabpanel .= '</tabpanel>';

		return $tabpanel;
	}

	function GetXulNoeudTransport($idRub, $idDon){
		
		//initalisation du xul
		$xul = "<groupbox ><hbox>";
		$xulGare = "<vbox id='NoeudsGare' ><label value='Le(s) gare(s)'/>";
		$xulPang = "<vbox id='NoeudsPang' ><label value='Le(s) PANG(s)'/>";		
		$xulVoirie = "<vbox id='NoeudsBus' ><label value=\"Le(s) arr?t(s) de Bus\"/>";		
		
		//r?cup?re la liste des gares et des pang
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtabTransport']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $this->idsInScope, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetXulNoeudTransport ".$this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$req = $db->query($sql);
		$db->close();
		while($r = $db->fetch_assoc($req)){
			//v?rifie si l'?l?ment est s?lectionn?
			if($this->VerifLiensInRub($idRub,$r["id_rubrique"]))
				$check = "true";
			else
				$check = "false";
			//construction du xul
			$idDoc = "val*".$this->site->infos["GRILLE_LIGNE_TRANS"]."*".$idDon."*".$idRub."*".$r["id_rubrique"];
			$xulCB = "<checkbox id='".$idDoc."' oncommand=\"SetElementLigne(".$r["id_rubrique"].",".$idRub.");\" checked='".$check."' label=\"".$r["titre"]."\"/>";				
			if($r["valeur"]==$this->site->infos["MOT_CLEF_PANG"])
				$xulPang .= $xulCB;
			if($r["valeur"]==$this->site->infos["MOT_CLEF_GARE"])
				$xulGare .= $xulCB;
		}
		
		//r?cup?re la liste des ?l?ments de voirie transport
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetVoirieTransport']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $this->idsInScope, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetXulNoeudTransport ".$this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$req = $db->query($sql);
		$db->close();
		while($r = $db->fetch_assoc($req)){
			//v?rifie si l'?l?ment est s?lectionn?
			if($this->VerifLiensInRub($idRub,$r["id_rubrique"]))
				$check = "true";
			else
				$check = "false";
			//construction du xul
			$idDoc = "val*".$this->site->infos["GRILLE_LIGNE_TRANS"]."*".$idDon."*".$idRub."*".$r["id_rubrique"];
			$xulVoirie .= "<checkbox id='".$idDoc."' oncommand=\"SetElementLigne(".$r["id_rubrique"].",".$idRub.");\" checked='".$check."' label=\"".$r["titreRubParent"]."\"/>";				
		}
		
		
		$xulGare .= "</vbox>";
		$xulPang .= "</vbox>";		
		$xulVoirie .= "</vbox>";
		$xul .= $xulGare.$xulPang.$xulVoirie."</hbox></groupbox>";

		return $xul;
	
	}

	
	function GetXulNoeudDeplacement($idRub, $idDon){
		
		//initalisation du xul
		$xul = "<groupbox ><hbox>";
		$xulVoirie = "<vbox id='NoeudsVoirie' ><label value='Le(s) Voirie(s)'/>";
		$xulEtab = "<vbox id='NoeudsEtab' ><label value='Le(s) Etablissement(s)'/>";		
		
		//r?cup?re la liste des ?tablissement et des voirie
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetNoeudDeplacement']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $this->idsInScope, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetXulNoeudDeplacement ".$this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$req = $db->query($sql);
		$db->close();
		while($r = $db->fetch_assoc($req)){
			//v?rifie si l'?l?ment est s?lectionn?
			if($this->VerifLiensInRub($idRub,$r["id_rubrique"]))
				$check = "true";
			else
				$check = "false";
			//construction du xul
			$idDoc = "val*".$r["id_form"]."*".$idDon."*".$idRub."*".$r["id_rubrique"];
			$xulCB = "<checkbox id='".$idDoc."' oncommand=\"SetElementChaine(".$r["id_rubrique"].",".$idRub.");\" checked='".$check."' label=\"".$r["titre"]."\"/>";				
			if($r["id_form"]==$this->site->infos["GRILLE_ETAB"])
				$xulEtab .= $xulCB;
			if($r["id_form"]==$this->site->infos["GRILLE_VOIRIE"])
				$xulVoirie .= $xulCB;
		}
		
		$xulEtab .= "</vbox>";
		$xulVoirie .= "</vbox>";
		$xul .= $xulEtab.$xulVoirie."</hbox></groupbox>";

		return $xul;
	
	}
	
	function GetXulNoeudCommune($idRub,$idJuste=false){
		
		//initalisation du xul
		$xul = "<groupbox ><hbox>";
		$xulCom = "<vbox id='NoeudsCommunes' ><label value='Le(s) Communes(s)'/>";		
		
		//r?cup?re la liste des communes
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetNoeudCommunes']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		/*
		$where = str_replace("-ids-", $this->idsInScope, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		*/
		$sql = $Q[0]->select.$Q[0]->from.$Q[0]->where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetXulNoeudCommune ".$this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$req = $db->query($sql);
		$db->close();
		//initialisation de la liste des ids juste
		$ids = "";
		while($r = $db->fetch_assoc($req)){
			//v?rifie si l'?l?ment est s?lectionn?
			if($this->VerifLiensInRub($idRub,$r["id_rubrique"])){
				$check = "true";
				$ids .= $r["id_rubrique"].",";
			}else{
				$check = "false";
			}
			//construction du xul
			$idDoc = "val*".$r["id_form"]."*".$r["id_donnee"]."*".$idRub."*".$r["id_rubrique"];
			$xulCom .= "<checkbox id='".$idDoc."' oncommand=\"SetElementChaine(".$r["id_rubrique"].",".$idRub.");\" checked='".$check."' label=\"".$r["titre"]."\"/>";				
		}
		
		$xulCom .= "</vbox>";
		$xul .= $xulCom."</hbox></groupbox>";

		if($idJuste)
			return substr($ids,0,-1);
		else
			return $xul;
	
	}
	
	
	function VerifLiensInRub($idRub,$idRubVerif){
		
		//v?rifie si un lien est pr?sent dans un rubrique
		//est poss?de la r?f?rence ? une rubrique 
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_VerifLiensInRub']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRub, $Q[0]->where);
		$where = str_replace("-idRubVerif-", $idRubVerif, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "VerifLiensInRub ".$this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$req = $db->query($sql);
		$db->close();
		
		return $db->fetch_assoc($req);

	}

	
	function VerifDonneeLienGrille($idDon,$idGrille){
		
		//v?rifie si une grille est dans la rubrique de la donnee
		//dans le cas o? la donnee est d'une autre grille que celle recherchh?e
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_VerifDonneeLienGrille']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $idDon, $Q[0]->where);
		$from = str_replace("-idGrille-", $idGrille, $Q[0]->from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "VerifDonneeLienGrille ".$this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$req = $db->query($sql);
		$r = $db->fetch_assoc($req);
		$db->close();
		
		if($r['idDonV'])
			return $r['idDonV'];
		else
			return false;
	}

	function GetLienDonnee($idDon,$valRef,$cstGrille){
		
		//r?cup?re les donn?es par rapport ? une r?f?rence dans une autre grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetLienDonnee".$cstGrille."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $idDon, $Q[0]->where);
		$from = str_replace("-valRef-", $valRef, $Q[0]->from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetLienDonnee ".$this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$req = $db->query($sql);
		$db->close();
		
		return $req;

	}
	
	
	function VerifQuestionIntermediaire($critere){
		
		$Xpath = "/questionnaire/grille/question[@id='".$critere."']";
		$Q = $this->XmlScena->GetElements($Xpath);
		if($Q)
			return true;
		else
			return false;
	}

	function GetOrdreQuestionIntermediaire($critere){
		
		$Xpath = "/questionnaire/grille/question[@id='".$critere."']/preceding-sibling::*";
		$Q = $this->XmlScena->GetElements($Xpath);
		$nb = count($Q)+1;
		return $nb;
	}
	
	function VerifChoixDiagnostic ($idDon, $typeCritere, $typeContexte, $critere=""){
		
		//quand il n'y a aucun crit?re on ne renvoie rien
		if(!$typeContexte || !$typeCritere)
			return false;
		if(!$typeCritere[0]
			&& !$typeCritere[1]
			) return false;
		if(!$typeContexte[0] 
			&& !$typeContexte[1]
			&& !$typeContexte[2]
			&& !$typeContexte[3]
			&& !$typeContexte[4]
			&& !$typeContexte[5]
			)
			return false;	
		
				
		//si crit est d?fini on g?re une sc?narisation
		if($critere==""){
			// On r?cupere le critere corespondant ? la donn?e (grille 59 Diagnostic)
			$critere = $this->GetValeur($idDon,'ligne_1'); 
			//v?rifie s'il faut traiter les questions interm?diaires pour V2
			if($_SESSION['version']=="V2"){
				if(!$this->VerifQuestionIntermediaire($critere)){
					return false;
				}else{
					//v?rifie l'ordre dans le xml
					$this->ordre = $this->GetOrdreQuestionIntermediaire($critere);												
				}
			}
		}
						
		if($this->trace)
			echo "Grille:VerifChoixDiagnostic:On r?cupere la donn?e corespondant au critere (grille ".$this->site->infos["GRILLE_CONTROL_".$_SESSION['version']]." Controle)<br/>";
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonneeCritere']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$from = str_replace("-critere-", $critere, $Q[0]->from);
		$where = str_replace("-idForm-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $Q[0]->where);				
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "VerifChoixDiagnostic ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		
		$verif = false;
		if ($r = $db->fetch_assoc($req)) {
			if($this->trace)
				echo "Grille:VerifChoixDiagnostic: On recupere la valeur du type de critere propre ? la donn?e (multiple_1 reglementaire ou souhaitable)<br/>";
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonneeChoix']";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-id-", $r['idDonnee'], $Q[0]->where);
					
			$sql = $Q[0]->select.$Q[0]->from.$where.$Q[0]->and_multiple1;
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			if($this->trace)
				echo "VerifChoixDiagnostic ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
			$db->connect();
			$req = $db->query($sql);
			$db->close();
			
			while($r1 = $db->fetch_assoc($req)) {
				if($this->trace)
					echo "Grille:VerifChoixDiagnostic:typeCritere[0]=".$typeCritere[0]." typeCritere[1]=".$typeCritere[1]." valeur=".$r['valeur']."<br/>";
				$verif = true;
				
				//v?rifie les crit?res r?gl?mentaires souhaitables
				$ok = false;
				if(($typeCritere[0]== $r1['valeur'] || $typeCritere[1]== $r1['valeur']) ){ 
					$ok = $r1['valeur'];
					$verif = true;
				}else 
					$verif = false;
				
				
				//v?rifie le contexte r?gl?mentaire uniquement dans le cas des crit?res r?gl?mentaires 
				if ($ok =='multiple_1_1' && $verif) {
					// On recupere la valeur du type de droit r?gelementaire (multiple_2)
					$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonneeChoix']";
					$Q = $this->site->XmlParam->GetElements($Xpath);
					$where = str_replace("-id-", $r['idDonnee'], $Q[0]->where);
							
					$sql = $Q[0]->select.$Q[0]->from.$where.$Q[0]->and_multiple2;
					$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
					if($this->trace)
						echo "VerifChoixDiagnostic ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
					$db->connect();
					$req = $db->query($sql);
					$db->close();
					
					$verif = false;
					while ($r2 = $db->fetch_assoc($req)) {
						//pour r�gler le probl�me des choix vide
						if($r2['valeur']!=""){
							if($typeContexte[0]== $r2['valeur'] 
								|| $typeContexte[1]== $r2['valeur'] 
								|| $typeContexte[2]== $r2['valeur'] 
								|| $typeContexte[3]== $r2['valeur']
								|| $typeContexte[4]== $r2['valeur']
								|| $typeContexte[5]== $r2['valeur']) 
								$verif = true;
						}
						if($this->trace)
							echo "Grille:VerifChoixDiagnostic:typeContexte=".print_r($typeContexte)." verif=".$verif." valeur=".$r2['valeur']."<br/>";
					}
				}
				if($this->trace)
					echo "Grille:VerifChoixDiagnostic:ok=".$ok." verif=".$verif."<br/>";
			} 	
		}
		if($this->trace)
			echo "Grille:VerifChoixDiagnostic:END<br/>";
		return $verif;
	}
	
  	function GetRubDon($idDon) {
  
  
		//requ?te pour r?cup?rer la rubrique de la donn?e
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetRubDon']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetRubDon ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		$r = $db->fetch_assoc($req);
		
		return $r["id"];
		
		
	}

  	function GetArtDon($idDon) {
  
  
		//requ?te pour r?cup?rer l'article de la donn?e
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetArtDon']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetArtDon ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		$r = $db->fetch_assoc($req);
		
		return $r["id"];
		
		
	}
	
			
  function GetXulForm($idDon, $idGrille,$qi="") {
  
  
		//requ?te pour r?cup?rer les donn?es de la grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$where = str_replace("-idGrille-", $idGrille, $where);
		
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetXulForm ".$this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		
		$labels ="";
		$controls="";
		//ajoute les controls pour chaque grille
		if($idGrille==$this->site->infos["GRILLE_REP_CON"]){
			$form = '<row flex="1" id="row_'.$idGrille.'_'.$idDon.'" >';	
		}else{
			$form = '<grid flex="1">';	
			$form .= '<columns flex="1">';	
			$labels = '<column flex="1" align="end">';	
			//$form .= '<caption label="Donn?e : '.$idDon.'"/>';
			$controls = '<column flex="1">';
		}
		$oChamp = "";
		$MultiSelect = "";
		while($r = $db->fetch_assoc($req)) {
						
			$idDoc = 'val'.DELIM.$idGrille.DELIM.$r["id_donnee"].DELIM.$r["champ"].DELIM.$r["id_article"];
			if($this->trace)
				echo "GetXulForm/construction de l'identifiant ".$idDoc."<br/>";
			switch ($idGrille) {
				case $this->site->infos["GRILLE_REG_LEG"]:
					//construstion de la r?gle l?gislative
					$labels .= '<label class="labelForm" control="first" multiligne="true" value="'.$r['titre'].'"/>';
					$controls .= $this->GetXulRegLeg($idDoc, $r);
					break;					
				case $this->site->infos["GRILLE_GEO"]:
					//on ne construit pas la grille GEO
					$labels .= '';
					$controls .= '';
					break;					
				case $this->site->infos["GRILLE_GEORSS"]:
					//on ne construit pas la grille GEO
					$labels .= '';
					$controls .= '';
					break;					
				default:
					if($this->trace)
						echo "GetXulForm //prise en compte des champs multiples ".$oChamp." MultiSelect=".$MultiSelect."<br/>";
					if($oChamp == $r['champ']){
						if($this->trace)
							echo "GetXulForm affiche le nouveau champ<br/>";
						$controls .= $this->GetXulControl($idDoc, $r);
						//conserve la valeur
						$MultiSelect .= "'".$r['valeur']."',";
					}else{
						$labels .= '<label class="labelForm" control="first" multiligne="true" value="'.$r['titre'].'"/>';
						if(substr($r['champ'], 0, 8)=='multiple'){
							if($this->trace)
								echo "GetXulForm d?but construction du multiple<br/>";
							$controls .= '<groupbox id="'.$id.'" '.$js.' >';
							$controls .= '<hbox>';
							//affiche le bouton s?lecionn?
							$controls .= $this->GetXulControl($idDoc, $r);
							//conserve la valeur
							$MultiSelect .=  "'".$r['valeur']."',";
						}else{
							//v?rifie si la ligne pr?c?dente ?tait multiple
							if($MultiSelect!=""){
								//r?cup?re les multiples non s?lectionn?
								$controls .= $this->GetXulControl($idDoc, $r,substr($MultiSelect,0,-1));
								//fin du multiselect
								$controls .= '</hbox>';
								$controls .= '</groupbox>';
								$MultiSelect = "";
							}else{
								//v?rifie s'il faut ajouter la l?gende de la donn?e dans la liste des r?ponses
								if($idGrille== $this->site->infos["GRILLE_REP_CON"]
									&& $r['champ']=="ligne_3"){
										$legende = "";
										if($_SESSION['ShowLegendeControle'])
											$legende = $this->GetXulLegendeControle($r['valeur'],$this->site->infos["GRILLE_CONTROL_".$_SESSION['version']]);
								}else
									$controls .= $this->GetXulControl($idDoc, $r);
							}
						}
						//conserve la ligne pour la fin
						$lastRow = $r;
						$oChamp = $r['champ'];						
					}
			}
		}
		if($this->trace)
			echo "GetXulForm // FIN prise en compte des champs multiples ".$oChamp." MultiSelect=".$MultiSelect."<br/>";
		if($MultiSelect!=""){
			//r?cup?re les multiple non s?lectionn?
			$controls .= $this->GetXulControl($idDoc, $lastRow, substr($MultiSelect,0,-1));
			//fin du multiselect
			$controls .= '</hbox>';
			$controls .= '</groupbox>';
		}
		
		//v?rifie s'il faut afficher la liste des noeuds de transport
		if($idGrille == $this->site->infos["GRILLE_LIGNE_TRANS"]){
			$controls .= $this->GetXulNoeudTransport($lastRow["id_rubrique"],$idDon);
			$labels .= "<label class='labelForm' control='first' value='S?lectionner les ?l?ments constituant la ligne'/>";
		}
		
		if($this->trace)
			echo "v?rifie s'il faut afficher le liste des noeud de chaine de d?placement $idGrille == ".$this->site->infos["GRILLE_CHAINE_DEPLA"];
		if($idGrille == $this->site->infos["GRILLE_CHAINE_DEPLA"]){
			$controls .= $this->GetXulNoeudDeplacement($lastRow["id_rubrique"],$idDon);
			$labels .= "<label class='labelForm' control='first' value='S?lectionner les ?l?ments constituant la cha?ne de d?placement'/>";
		}
		
		//v?rifie s'il faut afficher les bassins de gare
		if($idGrille == $this->site->infos["GRILLE_ETAB"]){
			$typeERP = $this->GetValeur($idDon,"mot_2");
			if($typeERP==$this->site->infos["MOT_CLEF_PANG"] || $typeERP==$this->site->infos["MOT_CLEF_GARE"]){
				$controls .= $this->GetXulNoeudCommune($lastRow["id_rubrique"]);
				$labels .= "<label class='labelForm' control='first' value='S?lectionner les communes constituant le bassin de gare'/>";
			}
		}
		
		
		if($idGrille!=$this->site->infos["GRILLE_REP_CON"]){
			$controls .= '</column>';	
			$labels .= '</column>';
			$form .= $labels.$controls.'</columns>';
		}
		
		if($idGrille==$this->site->infos["GRILLE_REP_CON"]){
			//ajout un bouton observation
			$controls.="<button image='".$this->site->infos["pathImages"]."IconeEcrire.gif' oncommand=\"AddObservation('".$idDoc."',".$this->site->infos["MOT_CLEF_OBS"].");\"/>";
			$form .= $controls.$legende.'</row>';
			//ajout d'une ligne pour les questions interm?diaires
			$form .= '<row id="row_'.$idGrille.'_'.$idDon.'_qi" />';	
		}else
			$form .= '</grid>';	
		
		if($idGrille == $this->site->infos["GRILLE_GEO"]){
			$carto = true;
			//$form .= '<groupbox >';	
			//$form .= '<caption label="Cartographie"/>';
			//ajoute la carte
			$form = $this->GetXulCarto($idDon);
			//$form .= '</groupbox>';
		}
		/*
		//v?rifie s'il faut ajouter le bouton de cr?ation de placemark
		$geo = $this->VerifDonneeLienGrille($idDon,$this->site->infos["GRILLE_GEO"]); 
		if(!$geo && $idGrille!=$this->site->infos["GRILLE_REP_CON"]){
			$form .="<button label='Ajouter une géolocalisation' oncommand=\"AddPlacemark();\"/>";
		}
		if($geo && $idGrille==$this->site->infos["GRILLE_OBS"])
			$form .= $this->GetXulForm($geo, $this->site->infos["GRILLE_GEO"]);
		*/	
		return $form;
	
	}

	function GetXulLegendeControle($idDon, $idGrille){
		//requ?te pour r?cup?rer les donn?es de la grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetLegendeControle']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$where = str_replace("-idGrille-", $idGrille, $where);
		
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetXulLegendeControle ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		
		$labels = "";
		$ico1 = "";
		$ico2 = "";
		$ico3 = "";
		$ico4 = "";
		$oRef = -1;
		while($r = $db->fetch_assoc($req)) {
			if($oRef!=$r["ref"]){
				//construstion des icones
				if($r['moteur']>0)
					$ico1 = '<image src="'.$this->site->infos["pathImages"].'moteur'.$r['moteur'].'.jpg"/>';
				if($r['audio']>0)
					$ico2 = '<image src="'.$this->site->infos["pathImages"].'audio'.$r['audio'].'.jpg"/>';
				if($r['visu']>0)
					$ico3 = '<image src="'.$this->site->infos["pathImages"].'visu'.$r['visu'].'.jpg"/>';
				if($r['cog']>0)
					$ico4 = '<image src="'.$this->site->infos["pathImages"].'cog'.$r['cog'].'.jpg"/>';
				$labels .= '<label value="'.$r['type'].'"/>';
				$labels .= '<label value="'.$r['ref'].'"/>';				
			}
			$labels .= '<label value="'.$r['droit'].'"/>';
			/*
			switch ($r["champ"]) {
				case "multiple_1":
					//construstion r?glementaire
					if($r['valeur']=="multiple_1_1")
						$labels .= '<label value="Réglementaire"/>';
					//construstion souhaitable
					if($r['valeur']=="multiple_1_2")
						$labels .= '<label value="Souhaitable"/>';
					break;
				case "multiple_2":
					if($r['valeur']=="multiple_2_1")
						$labels .= '<label value="Travail"/>';
					if($r['valeur']=="multiple_2_2")
						$labels .= '<label value="EPR_IOP"/>';
					if($r['valeur']=="multiple_2_3")
						$labels .= '<label value="Logement"/>';
					if($r['valeur']=="multiple_2_4")
						$labels .= '<label value="Voirie"/>';
					if($r['valeur']=="multiple_2_5")
						$labels .= '<label value="ERP_IOP existant"/>';
					if($r['valeur']=="multiple_2_6")
						$labels .= '<label value="Modalité particuliére"/>';
					break;
				case "multiple_3":
					//construstion des icones
					if($r['valeur']=="multiple_3_1")
						$ico1 = '<image src="'.$this->site->infos["pathImages"].'moteur';
					if($r['valeur']=="multiple_3_2")
						$ico2 = '<image src="'.$this->site->infos["pathImages"].'audio';
					if($r['valeur']=="multiple_3_3")
						$ico3 = '<image src="'.$this->site->infos["pathImages"].'visu';
					if($r['valeur']=="multiple_3_4")
						$ico4 = '<image src="'.$this->site->infos["pathImages"].'cog';
					break;
				//construstion des couleurs par rapport aux handicateurs 
				case "ligne_2":
					if($ico1!="")
						$ico1 .= $r['valeur'].'.jpg"/>';
					break;
				case "ligne_3":
					if($ico2!="")
						$ico2 .= $r['valeur'].'.jpg"/>';
					break;
				case "ligne_4":
					if($ico3!="")
						$ico3 .= $r['valeur'].'.jpg"/>';
					break;
				case "ligne_5":
					if($ico4!="")
						$ico4 .= $r['valeur'].'.jpg"/>';
					break;
				case "mot_1":
					$m = new MotClef($r['valeur'],$this->site);
					//$labels .= '<label value="'.$r['titre'].' : '.$m->titre.'"/>';
					$labels .= '<label value="'.$m->titre.'"/>';
					break;
				case "ligne_1": //r?f?rence
					$labels .= '<label value="'.$r['valeur'].'"/>';
					break;
			}
			*/					
		}
		//v?rifie s'il n'y a d'erreur sur les icones
		if(substr($ico1,-1)!=">" && $ico1!="")$ico1.='.jpg"/>';
		if(substr($ico2,-1)!=">" && $ico2!="")$ico2.='.jpg"/>';
		if(substr($ico3,-1)!=">" && $ico3!="")$ico3.='.jpg"/>';
		if(substr($ico4,-1)!=">" && $ico4!="")$ico4.='.jpg"/>';
				
		$xul = "<vbox class='legende' ><hbox>".$labels."</hbox><hbox>".$ico1.$ico2.$ico3.$ico4."</hbox></vbox>";
		
		return $xul;
	}
	
	
	function GetXulCarto($idDon,$idRub=-1)
	{
		//v?rifie s'il faut afficher la carte
		if(!$_SESSION['ShowCarte'])
			return;
			
		$xul="";
		if($idRub!=-1){
			$xul = "<iframe height='450px' width='500px' src='".$this->site->infos["urlCarto"]."?id=".$idRub."'  id='BrowerGlobal' />";
			//$xul = "<iframe height='450px' width='500px' src='http://www.mundilogiweb.com/onadabase/kml/garedelille.kmz'  id='BrowerGlobal' />";			
		}else{
			$xul = "<iframe height='450px' width='500px' src='".$this->site->infos["urlCarto"]."?id=".$this->GetRubDon($idDon)."'  id='BrowerGlobal' />";
			//$xul = "<iframe height='450px' width='500px' src='http://maps.google.fr/maps?f=q&hl=fr&geocode=&q=http:%2F%2Fwww.mundilogiweb.com%2Fonadabase%2Fkml%2Fgaredelille.kmz&ie=UTF8&t=h&z=16'  id='BrowerGlobal' />";
			//$xul = "<iframe height='450px' width='500px' src='http://www.mundilogiweb.com/onadabase/kml/garedelille.kmz'  id='BrowerGlobal' />";			
		}		
		return	$xul;	
	
	}

	function GetXulRegLeg($id, $row)
	{
		
		/*r?sultat de row
		champ 	rang 	titre 	type 	obligatoire 	extra_info 	
		ligne_1 	6 	valeur ?talon 	ligne 	  	  	  	  	  	 
		ligne_2 	7 	valeur ?talon 2 	ligne 	  	  	  	  	  	 
		ligne_3 	4 	Nom de la valeur 	ligne 	  	  	  	  	  	 
		mot_1 	5 	op?rateur 		mot 	18 	  	  	  	 
		mot_2 	8 	Unit?s 		mot 	19 	  	  	  	 
		select_1 	9 	r?gle respect?e 	select radio		
		*/
		
		switch ($row['champ']) {
			case 'ligne_1':
				//r?cup?ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control = '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				$control .= '<label id="trace'.$id.'" value=""/>';
				break;
			case 'ligne_2':
				//r?cup?ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control = '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				$control .= '<label id="trace'.$id.'" value=""/>';
				break;
			case 'ligne_3':
				//construction du control
				$control = '<label value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
				break;
			case 'mot_1':
				$mot = New MotClef($row["valeur"], $this->site);
				$control = '<label value="'.$this->site->XmlParam->XML_entities($mot->titre).'"/>';			
				break;
			case 'mot_2':
				$mot = New MotClef($row["valeur"], $this->site);
				$control = '<label value="'.$this->site->XmlParam->XML_entities($mot->titre).'"/>';			
				break;
			case 'select_1':
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='radio']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<groupbox>';
				$control .= '<caption label="'.$row['titre'].'"/>';
				$control .= '<radiogroup id="'.$id.'" '.$js.' >';
				$control .= '<hbox>';
				$control .= $this->GetChoixVal($row);
				$control .= '<label id="trace'.$id.'" value=""/>';
				$control .= '</hbox>';
				$control .= "</radiogroup>";
				$control .= '</groupbox>';
				break;
		}

		return $control;
	
	}
	
	function GetXulControl($id, $row, $MultiSelect="")
	{
		$control = '';
		switch ($row['type']) {
			case 'multiple':
				if($this->trace)
					echo "GetXulControl MultiSelect=".$MultiSelect."<br/>";
				$id = 'val'.DELIM.$row["grille"].DELIM.$row["id_donnee"].DELIM.$row["champ"].DELIM.$row["id_article"].DELIM.$r['choix'];
				$control .= $this->GetChoixVal($row,'multiple',$MultiSelect);
				break;
			case 'select':
				//prise en compte de l'affichage liste
				if($row['extra_info']=="liste"){
					//r?cup?ration des js
					$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='menu']";
					$js = $this->site->GetJs($Xpath, array($id));
					//construction du control
					$control .= '<menulist id="'.$id.'" '.$js.' ><menupopup >';
					$control .= $this->GetChoixVal($row,'menuitem');				
					$control .= '</menupopup></menulist>';
				}else{				
					//r?cup?ration des js
					$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='radio']";
					$js = $this->site->GetJs($Xpath, array($id));
					//construction du control
					$control .= '<groupbox>';
					//$control .= '<caption label="'.$row['titre'].'"/>';
					$control .= '<radiogroup id="'.$id.'" '.$js.' >';
					$control .= '<hbox>';
					$control .= $this->GetChoixVal($row);
					$control .= '</hbox>';
					$control .= "</radiogroup>";
					$control .= '</groupbox>';
				}
				break;
			case 'mot':
				//r?cup?ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='menu']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<menulist id="'.$id.'" '.$js.' ><menupopup >';
				$control .= $this->GetChoixVal($row,'menuitem');				
				$control .= '</menupopup></menulist>';
				break;
			case 'fichier':
				//r?cup?ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='fichier']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<button id="btn'.$id.'" label="Parcourir" '.$js.' />';
				//affichage de l'adresse du document
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				$control .= '<textbox  '.$js.' hidden="true" id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
				break;
			case 'url':
				//construction du control
				if($row["valeur"]!=""){
					$control .="<label id='fa_".$id."' class='text-link' onclick=\"window.open('".$row["valeur"]."');\" value=\"Voir\"/>";					
				}
				//r?cup?ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				$control .= '<textbox  '.$js.' multiline="true" id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
				break;
			default:
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				
				//gestion de l'affichage des controles
				switch ($row["grille"]) {
					case $this->site->infos["GRILLE_REP_CON"]:
						//on cache le textbox r?f?rence
						$hidden = "false";
						if($row["champ"]=="ligne_1") $hidden = "true";
						$control .= '<textbox  '.$js.' hidden="'.$hidden.'" multiline="true" class="txtRepCon" id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
						break;
					case $this->site->infos["GRILLE_OBS"]:
						//on emp?che l'?dition des r?f?rences
						$type = "textbox";
						if($row["champ"]=="ligne_1" ||$row["champ"]=="ligne_4" || $row["champ"]=="ligne_5" ) $type = "label";
						$control .= '<'.$type.' '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
						break;
					default:
						$control .= '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				}	
		}	
		
		$control .= '<label id="trace'.$id.'" hidden="true" value=""/>';

		return $control;

	}

	function GetChoixVal($row,$type='radio',$multiSelect="")
	{
		//requ?te pour r?cup?rer les donn?es de la grille
		if($multiSelect!="")
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetChoix".$row['type']."NotIn']";
		else
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetChoix".$row['type']."']";
		
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $row['grille'], $Q[0]->where);
		$where = str_replace("-champ-", $row['champ'], $where);
		$where = str_replace("-extra_info-", $row['extra_info'], $where);
		$where = str_replace("-valeur-", $row['valeur'], $where);
		$where = str_replace("-multiSelect-", $multiSelect, $where);
				
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetChoixVal ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();

		$control = "";
		while($r = $db->fetch_assoc($req)) {
			$select = 'false';
			if($row['valeur']==$r['choix'])
				$select = 'true';
			
			if($this->trace)
				echo "extra_info ".$row['extra_info']." type ".$type." "."select ".$select." ".$row['valeur']."==".$r['choix']."<br/>";
						
			switch ($type) {
				case 'radio':
					$control .= "<radio id='".$r['choix']."' selected='".$select."' label=\"".$this->site->XmlParam->XML_entities($r["titre"])."\"/>";
					break;
				case 'menuitem':
					$control .= "<menuitem id='".$r['choix']."' value='".$r['choix']."' selected='".$select."' label=\"".$this->site->XmlParam->XML_entities($r['titre'])."\"/>";
					break;
				case 'multiple':
					if($multiSelect=="")
						$select = 'true';
					//r?cup?ration des js
					$id = 'val'.DELIM.$row["grille"].DELIM.$row["id_donnee"].DELIM.$row["champ"].DELIM.$row["id_article"].DELIM.$r['choix'];
					$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='multiple']";
					$js = $this->site->GetJs($Xpath, array($id));
					$control .= "<checkbox ".$js." id='".$id."' checked='".$select."' label=\"".$this->site->XmlParam->XML_entities($r['titre'])."\"/>";
					break;
			}
		}
		
		return $control;

	}
	
  }


?>
