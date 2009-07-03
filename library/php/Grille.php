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
  public $ordre; //por gérer l'odre de la scénarisation
  private $site;

  function __tostring() {
    return "Cette classe permet de définir et manipuler des grilles.<br/>";
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
    	//gestion de la création des lignes
    	if($crea){
    		//on prend le scope à partir de la rubrique
    		$this->idsInScope = $g->GetEnfantIds($g->id,",")."-1";
    	}else{
    		//pour afficher les éléments on prend le scope à partir du parent de la rubrique
    		$this->idsInScope = $g->GetEnfantIds($g->IdParent,",")."-1";    		
    	}
    	
    }
	$this->XmlScena = new XmlParam(XmlScena);
	
	if($complet){
		$this->GetProps();
	}

	//echo "FIN new grille <br/>";
		
    }

	public function GetEtatDiagListe($idRub, $idDoc,$PourFlex=false,$idScope=false)
	{
		//récupère les info de l'id xul
		$arrDoc = split("_",$idDoc);
		
		//construit les objets nécessaires
		$objXul = new Xul($this->site);
		
		if($arrDoc[0]==0){
			//récupère les critère suivant leur validation
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtatDiagOuiListe']";
			$champ = $this->site->infos["CHAMPS_CONTROL_DEFFICIENCE"]["champ"];
			$valeur = $this->site->infos["CHAMPS_CONTROL_DEFFICIENCE"]["valeur"][$arrDoc[1]];
		}else{
			//récupère les critère suivant leur validation
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtatDiagHandiListe']";
			$champ = $this->site->infos["CHAMPS_CONTROL_DIAG"][$arrDoc[1]];
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
			
		//construction du xul
		$xul = "<vbox flex='1'>";
		$idRubOld=-1;
		while ($r =  $db->fetch_assoc($result)) {
			
				if($r["id_rubrique"]!=$idRubOld){
					//finalise le précédent lieu
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
				
				//ajoute le critère				
				if($PourFlex)
					$xul .= "<crit>";
				else	
					$xul.="<hbox>";				
				
				//ajoute la légende				
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
				//finalise le critère				
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
		//vérifie s'il y a une grille geo
		$idDonV = $this->VerifDonneeLienGrille($idDon,$this->site->infos["GRILLE_GEO"]);
		if($idDonV){
			$xul .="<vbox>";
			$xul.="<image onclick=\"ExecCarto(".$this->GetRubDon($idDonV).",".$idDonV.");\" src='".$this->site->infos["pathImages"]."kml.png' />";
			//$idArt = $this->GetArtDon($idDonV);
			//$xul.= $this->GetXulLiensArticle($idArt);
			$xul .="</vbox>";
		}
		//vérifie s'il y a une grille observation
		$Dons = $this->GetLienDonnee($idDon,$valRef,"GRILLE_OBS");
		while ($r =  mysql_fetch_assoc($Dons)) {
			$xul .="<vbox flex='1'>";
			$xul .="<hbox>";
			$xul.="<image onclick=\"ShowPopUp(".$this->site->infos["GRILLE_OBS"].", ".$r['id_donnee'].");\" src='".$this->site->infos["pathImages"]."obs.png' />";
			$xul .="</hbox>";
			$xul.= $this->GetXulLiensArticle($r['id_article']);
			$xul .="</vbox>";
		}
		//vérifie s'il y a une grille signalement probleme
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
		//vérifie s'il y a des documents
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
			//récupère les critéres des icones supplémentaire
			$Xpath = "/XmlParams/icones/objet[@IdGrille='".$row["id_form"]."']";
			if($this->trace)
				echo "Grille:GetEtatDiagIcones:Xpath".$Xpath."<br/>";
			$CritIcos = $this->site->XmlParam->GetElements($Xpath);
			if($CritIcos){
				foreach($CritIcos[0]->question as $q){
					$idDon = false;
					//vérifie s'il faut chercher par rapport aux grilles d'information
					if($q["srcIdGrille"]){ 
						$idDon = $this->RechercheDonneeId($q["srcIdGrille"],$row["id_article"],$q["srcIdChamp"],$q["srcCheckVal"],$ids,$q["srcRefCont"]);
						//vérifie s'il faut traiter un deuxième critère
						$qB = $q->question;
						if($idDon && $qB){
							$idDon = $this->RechercheDonneeId($qB["srcIdGrille"],$row["id_article"],$qB["srcIdChamp"],$qB["srcCheckVal"],$ids,$qB["srcRefCont"]);
							$q = $qB;
						}
					}
					/*vérifie s'il faut chercher par rapport aux grilles de réponse
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
		//vérifie les valeurs
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
				, fdCont.id_donnee "
			.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
     }
    
    function DelEtatDiag($idRub,$handi){
		//supprime la relation des étatdiag au donnée
    	$sql = "DELETE FROM ona_etatdiag_donnees 
			WHERE id_etatdiag IN (SELECT id_etatdiag FROM ona_etatdiag 
					WHERE id_rubrique=".$idRub." AND handi=".$handi.")";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$db->query($sql);
		$db->close();
    	
		//supprime les étatdiag
		$sql = "DELETE FROM ona_etatdiag 
			WHERE id_rubrique=".$idRub." AND handi=".$handi;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$db->query($sql);
		$db->close();
    }
    
    public function FiltreRubAvecGrille($id,$idsGrille)
	{
		$sql = "SELECT DISTINCT r.id_rubrique
			FROM spip_rubriques r
			INNER JOIN spip_rubriques_enfants re ON re.id_rubrique = r.id_rubrique AND re.id_parent =".$id."
			INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
			INNER JOIN spip_forms_donnees_articles fda ON fda.id_article = a.id_article 
			INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fda.id_donnee AND fd.id_form IN (".$idsGrille.")
			";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:FiltreRubAvecGrille".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
			
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
		//récupère la somme des état de diagnostic
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
			//récupère le nombre de critéres validés
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
			//conserve létat du diagnostique
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
			//récupère le nombre de critéres validés
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
			//conserve létat du diagnostique
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
			$r =  $this-> GetEtatDiagSum($idRub,4);		
		}else{
			//récupère le nombre de critéres validés
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
			//conserve létat du diagnostique
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
			echo "//charge les propiétés de la grille $this->id -<br/>";
		$sql = "SELECT titre
			FROM spip_forms 
			WHERE id_form = ".$this->id;
		//echo $sql."<br/>";
		$req = $DB->query($sql);
		$DB->close();
		$data = $DB->fetch_assoc($req);
		$this->titre = $data['titre'];

	}
    
    function GetTreeProb($idRub){
    	
    	$g = new Granulat($idRub,$this->site);
    	//récupère les rubriques enfants
    	$ids = $g->GetIdsScope();
    	    	
		//récupère les identifiants des rubriques de la racine ayant un problème
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
		
		$xul ='<grid flex="1">';
		//on cache la colonne de référence	
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
						$xul.="<label value=\"Problème n ° ".$r["idPbPlan"]." : ".$this->site->XmlParam->XML_entities($r["TextCont"])."\"/>";
						$xul.="<label class='text-linkAdmin' onclick=\"OuvreControle(".$r["idDonneCont"].");\" value='(".$r["idCont"].")'/>";
		    		$xul.="</hbox>";
				}
				$xul.="<hbox>";
					$xul.="<label value='    - ".$r["RepCont"]."'/>";
					$xul.="<label value='".$r["aDate"]."'/>";
					$xul.="<label value='Photo : ".$r["ReponsePhoto"]."'/>";
					$xul.="<label id='adminDon_".$r["idDon"]."' class='text-linkAdmin' onclick=\"OuvreDonnee(".$this->site->infos["GRILLE_SIG_PROB"].",".$r["idDon"].");\" value=\"Admin\"/>";
					$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='".$this->site->infos["pathImages"]."check_yes.png' />";
		    		$xul.="<image onclick=\"DelArticle('".$r["idDon"]."', '".$idRub."');\" src='".$this->site->infos["pathImages"]."check_no.png' />";
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
		
		
	   	return $xul;
    	
    }
    
    function GetTableauBord($idRub){
    	
    	$g = new Granulat($idRub,$this->site);
    	//récupère les rubriques enfants
    	$ids = $g->GetIdsScope();
    	    	
		//récupère les identifiants des rubriques de la racine ayant un problème
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
		//on cache la colonne de référence	
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
						$xul.="<label value=\"Problème n ° ".$r["idPbPlan"]." : ".$this->site->XmlParam->XML_entities($r["TextCont"])."\"/>";
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
    
 
    
    
    function GetTreeObs($idRub,$AjoutObs=false){
    	
    	$g = new Granulat($idRub,$this->site);
    	//récupère les rubriques enfants
    	$ids = $g->GetIdsScope();

    	$oXul = new Xul($this->site);
    	
		//récupère les identifiants des rubriques de la racine ayant un problème
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
		
		$xul ='<grid flex="1">';
		//on cache la colonne de référence	
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
						$xul.="<label value='".$r["titreRubPar"]."'/>";
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
					$xul.="<label value='".$r["titreRub"]."'/>";
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
					$xul.="<label value='".$r["titreArt"]."'/>";
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
					$xul.="<label value=\"Problème n ° ".$r["idPbPlan"]." : ".$this->site->XmlParam->XML_entities($r["TextCont"])."\"/>";
					$xul.="<label   value='(".$r["idCont"].")'/>";
					$xul.="<!--<label value=' Commentaires : ".$r["obs"]."'/> -->";
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
			//pour voir le détail
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
    	//récupère les rubriques enfants
    	$ids = $g->GetIdsScope();
    	
		//récupère les identifiants des rubriques de la racine ayant un problème
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
		
		echo 'Rubrique Parent;Rubrique;Id Critère;Id Pb;Questions Problème;Critère réglementaire;Date;Observations';
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
			//critère réglementaire
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
			echo "Grille:RechercheDonneeId://recherche l'id d'une donnée avec son article $idArt sa valeur = $valeur et son champ=$champ <br/>";
		
		//récupère la requête suivant le type de recherche	
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
			echo "Grille:GetObjId://récupère l'identifiant de l'objet ".$obj." ".$donId."<br/>";

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
		//vérifie s'il faut prendre en compte la réponse de la question parente
		if($qiParent==1)
	    	$Xpath = "//question[@id='".$critere."']";
	    else{
	    	//récupère les infos de la question parente
	    	$arrQi = split("_",$qiParent);
	    	$criterePar = $this->GetValeur($arrQi[2],'ligne_1'); 
	    	$reponsePar = $this->GetValeur($arrQi[2],'mot_1'); 
	    	$Xpath = "//question[@id='".$criterePar."' and @reponse='".$reponsePar."']/question[@id='".$critere."']";
	    }
	    	
    	if($this->trace)
			echo "Grille:GereScenarisation:récupère les paramètre à exécuter ".$Xpath."<br/>";
    	$scena = $this->XmlScena->GetElements($Xpath);
    	
    	if(!$scena) return;
    	
		$idArt = $this->GetObjId($donId,'Article');
    	
    	foreach($scena as $qi)
		{
			//vérifie que la réponse correspond au critère
			//ou n'est pas définie
			if($qi["reponse"]==$row["valeur"] || !$qi["reponse"]){
				$OldCrit = "";
		    	foreach($qi as $q)
				{
					//récupère les paramètre de la question
					$critere = $q["id"]."";
					//pour éviter la création de doublon pour une sous question intermédiare 
					//cf. douche
					if($critere != $OldCrit){
						$OldCrit=$critere;
						$idDon = $this->GetDonneeCritere($idArt,$critere);
						
						//vérifie si la donnée est trouvée
						if(!$idDon){
							return "<label value=\"Ce critère ".$critere." n'existe pas !\" />";
						}
						
						//vérifie si la donnée correspond au choix de diagnostic
						$verif = $this->VerifChoixDiagnostic(-1, $_SESSION['type_controle'], $_SESSION['type_contexte'],$critere); 
						if($verif){
							//vérifie s'il faut créer la réponse à la question
							if($q["valeur"]){
								//répond à la question
								$r = array("grille"=>$row["grille"],"champ"=>$q["champ"],"valeur"=>$q["valeur"]);
								$this->SetChamp($r, $idDon);
								$this->GereWorkflow($row, $idDon);		
							}else{
								//création du formulaire
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
			echo "Grille:GereWorkflow:récupère les paramètre du workflow à exécuter ".$Xpath."<br/>";
    	$wfs = $this->site->XmlParam->GetElements($Xpath);
    	
    	if(!$wfs) return;

    	foreach($wfs as $wf)
		{
			//vérifie s'il faut récupérer l'identifiant de l'objet de destination
			if($wf['dstObj'])
				$id = $this->GetObjId($donId,$wf['dstObj']);

			switch ($wf['dstQuery']) {
				case "ShowArtGrille":
					if($this->trace)
						echo "Grille:GereWorkflow:".$wf['dstQuery']."==".$donId."<br/>";					
					//récupère le formulaire xul
					$xul = $this->GetXulForm($donId,$this->site->infos["GRILLE_SIG_PROB"]);
					break;	
				case "AddNewTab":
					$xul = $this->GetXulTabPanels($row['idRub'],$this->site->infos["GRILLE_SIG_PROB"],"SignalementProbleme");
					break;	
				case "AddNewArtGrille":
					if($this->trace)
						echo "Grille:GereWorkflow:AddNewArtGrille ".$row['valeur']."==".$wf['srcCheckVal']."<br/>";					
					if($row['valeur']==$wf['srcCheckVal']){						
						//récupération du granulat
						$gra = new Granulat($id,$this->site);
						
						if($wf['trsObjet']=="controles" ){
							$gTrs = new Granulat($wf['trsId'],$this->site);
							$id = $gra->SetNewEnfant($gTrs->titre);
							$this->AddQuestionReponse($wf['trsId'],$id);
							if($wf['trsId']==$this->site->infos["RUB_PORTE1"] 
								|| $wf['trsId']==$this->site->infos["RUB_PORTE1"] )
									{ // Porte
								$id1 = $gra->SetNewEnfant("Face 1 ");
								$this->AddQuestionReponse($this->site->infos["RUB_PORTE_FACE1"],$id1);
								$id2 = $gra->SetNewEnfant("Face 2 ");
								$this->AddQuestionReponse($this->site->infos["RUB_PORTE_FACE2"],$id2);
							}
						}else{
							//gestion pour le signalement problème
							if($wf['trsId']==$this->site->infos["GRILLE_SIG_PROB"]){
								$ref = $this->GetValeur($donId,"ligne_1");
								$reponseId = $this->GetValeur($donId,"mot_1");
								$reponse = $this->GetMot($reponseId);
								
								$idArt = $gra->SetNewArticle("Problème ".$ref." ".date('j/m/y - H:i:s'));
								//ajoute une nouvelle donnee
								$idDon = $this->AddDonnee($id, $wf['trsId'], false, $idArt);
								if($this->trace)
									echo "Grille:GereWorkflow://gestion pour le signalement problème ".$ref."<br/>";
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
							//récupère le formulaire xul
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
						//récupère le formulaire xul
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
						echo "//exécution du workflow ".$sql."<br/>";
				break;
			}								
		}
		
		if($this->trace)
			echo "Grille:GereWorflow:xul=".$xul."<br/>";
		return $xul;
		
	}	

	function GetMot($idMot) {
		
		//récupère la valeur d'un champ
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
		//récupère la valeur d'un champ
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
			echo "//récupération des valeurs de workflow ".$donnees."<br/>";
    	
		//suppression des éventuelle champ pour la donnée
		$this->DelDonnee($donId);
		
		//création des valeurs
		while ($row =  mysql_fetch_assoc($rows)) {
			$this->SetChamp($row, $donId, false);
			//echo "--- ".$donId." nouvelle valeur ".$i;
		}
		
	}	
	
	function AddXmlDonnee($xmlSrc){
			
		if($this->trace)
			echo "Grille/AddXmlDonnee IN //récuparation de la définition des données ".$xmlSrc."<br/>";
		$xml = new XmlParam($xmlSrc);		
		
		$action = $xml->xml->grille["action"]."";
		
		$Xpath = "/donnees";
		$donnees = $xml->GetElements($Xpath);
		if($this->trace)
			echo "Grille/AddXmlDonnee/récupération des valeurs de donnée ".$donnees."<br/>";
		
		$idGrille = $donnees[0]->grille;
		if($this->trace)
			echo "Grille/AddXmlDonnee/récupération de l'identifiant de la grille ".$idGrille."<br/>";
		
		//récupération de la définition des champs
		$Xpath = "/donnees/champs";
		$champs = $xml->GetElements($Xpath);
		$first=true;
		foreach($donnees[0]->donnee as $donnee)
		{
			$idRub = $donnee->rub;
			if($this->trace)
				echo "Grille/AddXmlDonnee/- récupération de l'identifiant de la rubrique ".$idRub."<br/>";
			
			//récuparation du granulat
			$g = new Granulat($idRub, $this->site); 
			$idArt = $g->GetArticle();
			if($this->trace)
				echo "Grille/AddXmlDonnee/- récupération ou création du dernier article en cours de rédaction ".$idArt."<br/>";
			
				//vérifie s'il fut supprimer les valeurs
			if($action!="ajout"){
				if($first){
					$this->DelGrilleArt($idGrille,$idArt);
					if($this->trace)
						echo "Grille/AddXmlDonnee/suppression des anciennes données ".$idArt."<br/>";
					$first=false;
				}
					
				$idDon = $g->GetIdDonnee($idGrille, $idArt, true);
				if($this->trace)
					echo "Grille/AddXmlDonnee/- création de la donnee ".$idDon."<br/>";
				$supChamp = false;
			}else{
				$supChamp = true;
			}
			
			$i=0;
			foreach($donnee->valeur as $valeur)
			{
				$valeur=utf8_decode($valeur);
				$champ = $champs[0]->champ[$i];
				//vérifie s'il faut récupérer l'id_donnée
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
						echo "Grille/AddXmlDonnee/-- récupération du type de champ ".$champ."<br/>";
					$row = array('champ'=>$champ, 'valeur'=>$valeur);
					if($this->trace)
						echo "Grille/AddXmlDonnee/-- récupération de la valeur du champ ".$valeur."<br/>";
					$this->SetChamp($row, $idDon,$supChamp);
					if($this->trace)
						echo "Grille/AddXmlDonnee/--- création du champ <br/>";
				}
				$i++;
			}
			
		}
		if($this->trace)
			echo "Grille/AddXmlDonnee OUT //<br/>";
		
	}
    
    function AddGrilles($idRubSrc, $idRubDst, $redon=false){
			
		//récuparation des grilles des articles publiés de la rubrique
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
		
		//création du granulat
		$g = new Granulat($idRubDst,$this->site);
		
		/*la rubrique des questions est directement passée en paramètre 			
		//pour les controles récupération des rubriques dans les liens de la rubrique Src 
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

			//récupération du droit de la dernière donnée pour la rubrique parente de la destination
			$droit = $this->GetDroitParent($g->IdParent);
			
			//récupération des questions publié pour un type de controle
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_AddQuestion']";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-idRubSrc-", $idRubSrc, $Q[0]->where);
			//ajoute les critère de version
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
					//ajoute le mot clef type de controle à la rubrique
					$g->SetMotClef($rowQ["typecon"]);
					$first=false;
				}
				//vérifie si le contrôle est cohérent par rapport au parent
				if($this->GereCoheDroit($rowQ, $droit)){
					//prise en compte des doublons suite à l'attribution de plusieurs droits
					if($rowQo != $rowQ["ref"]){
						//ajoute une nouvelle donnée réponse pour la question
						$idDon = $g->GetIdDonnee($rowQ["FormRep"],-1,true);
						if($this->trace)
							echo "Grille:AddQuestionReponse:ajoute une nouvelle donnée réponse pour la question".$idDon."<br/>";
						//ajoute la question
						$r = array("champ"=>"ligne_2","valeur"=>$rowQ["question"]);
						$this->SetChamp($r,$idDon,false);
						//ajoute la référence
						$r = array("champ"=>"ligne_1","valeur"=>$rowQ["ref"]);
						$this->SetChamp($r,$idDon,false);
						//ajoute la réponse par défaut
						$r = array("champ"=>"mot_1","valeur"=>$rowQ["valdef"]);
						$this->SetChamp($r,$idDon,false);
						//ajoute la donnée référente
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
		
		//vérifie si la question est cohérente par rapport au questionnaire parent
		//$Xpath = "/XmlParams/XmlParam/CoheDroit[@srcId='".$rQ['id_form'].";".$row['droit']."']";
		$Xpath = "/XmlParams/XmlParam/CoheDroit[@dstId='".$rQ['id_form']."' and @dstCheckVal='".$rQ['droit']."' and @srcCheckVal='".$droit."' ]";
		if($this->trace)
			echo "Grille:GereCoheDroit:récupère la cohérence ".$Xpath."<br/>";
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
		//récupération des droits pour la rubrique parente
		$rParDon = $this->GetLastDonne($id);

		//récupère le champ droit de la donnée du parent
		$Xpath = "/XmlParams/XmlParam/CoheDroit[@srcId='".$rParDon['id_form']."']/@srcChamp";
    	$srcChamps = $this->site->XmlParam->GetElements($Xpath);
		$srcChamp = $srcChamps[0];
		
		//récupère la valeur du champ droit
		$droit = $this->GetValeur($rParDon['id_donnee'], $srcChamp);
		
		return $droit;
	}

	function GetValeur($idDon, $champ){
		//récupère la valeur d'un champ
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
		//récupération de la dernière donnée d'une rubriques 
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
		//pour la scénarisarisation
		//récupération de la donnée d'un article correspondant au critère 
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
			
		//récuparation du granulat
		$g = new Granulat($idRub, $this->site);
		
		if($idArt==-1)
			//"récupération ou création du dernier article en cours de rédaction"; 
			$idArt = $g->GetArticle(" AND a.statut='prepa'");
				
		if($redon){
			//récupère les dernières données publiées
			$g = new Granulat($redon, $this->site);
			$rows = $g->GetGrille($idGrille, " AND a.statut='publie'");
			$oDonnee="";
			while ($row =  mysql_fetch_assoc($rows)) {
				//vérifie s'il on change de donnee
				if($row["id_donnee"]!=$oDonnee){
					$idDon = $g->GetIdDonnee($idGrille, $idArt, true);
					$oDonnee=$row["id_donnee"];
				}
				$this->SetChamp($row, $idDon, false);
				//echo "--- ".$donId." nouvelle valeur ".$i;
			}
		}else{
			//récupération ou création d'une nouvelle donnée
			$idDon = $g->GetIdDonnee($idGrille, $idArt, $doublon);
			//récupère la définition des champs sans valeur
			$rows = $this->GetChamps($idGrille);
			//initialisation de la donnée
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

		//suppression des éventuelle champ pour la donnée
		$this->DelDonnee($donId);
		
		//création des valeurs
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
		//récupération des données pour un article et une grille
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
		
		//création de la valeur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_InsChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$values = str_replace("-idDon-", $donId, $Q[0]->values);
		$values = str_replace("-champ-", $row["champ"], $values);
		$values = str_replace("'-val-'", $this->site->GetSQLValueString($row["valeur"],"text"), $values);
		//prise en compte des entier et des décimaux
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
			//on exclu les grille géo
			if($r["id"]!=$this->site->infos["GRILLE_GEO"] && $r["id"]!=$this->site->infos["GRILLE_GEORSS"]){
				$tabbox .= '<tab class="tab" id="tab'.$r["id"].'" label="'.$r["titre"].'" />';
				//vérifie s'il faut créer un formulaire ou un sous onglet
				if($Q[0]->dst=='Form' )
					$tabpanels .= $this->GetXulTabPanels($r["idArt"], $r["id"],'Form',$recur);
				else
					$tabpanels .= $this->GetXulTabPanels($src, $r["id"],$Q[0]->dst,$recur);
			}
			$i++;
		}
		
		//prise en compte des onglets liés par le workflow
		$row = array("idRub"=>$id,"grille"=>"GetXulTabForm","champ"=>$dst);
		$WFtabpanels = $this->GereWorkflow($row,-1);
		if($WFtabpanels!=""){
			$tabbox .= '<tab class="tab" id="tabWF'.$r["id"].'" label="Signalement(s) problème(s)" />';
			
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

		//on n'affiche pas les grille géolo
		if($id == $this->site->infos["GRILLE_GEO"] || $id==$this->site->infos["GRILLE_GEORSS"])
			return;

		$oXul = new Xul($this->site);	
		$gra = new Granulat(-1,$this->site);
			
		//récupère les articles de la rubrique
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
			//on cache la colonne de référence	
			$tabpanel .='<columns>';	
			$tabpanel .='<column hidden="true"/>';	
			$tabpanel .='<column flex="1"/>';
			$tabpanel .='<column />';			
			$tabpanel .='<column />';			
			$tabpanel .='</columns>';	
			$tabpanel .='<rows>';	
			$tabpanel .='<row><label value="Référence" hidden="true" /><label value="Question"/><label value="Réponse"/><label value="Observations"/></row>';	
		}
		if($id==$this->site->infos["GRILLE_SIG_PROB"]){
			$tabpanel .='<vbox flex="1">';
		}
		$MemeId=false;
		while($r = $db->fetch_assoc($req)) {
			//$tabpanel .= '<groupbox >';	
			//$tabpanel .= '<caption label="'.$r["titre"].'"/>';
			if($Q[0]->dst=='Form'){
				//exécution suivant les type de grille
				switch ($id) {
					case $this->site->infos["GRILLE_GEO"]:
						$tabpanel .= "";
						break;
					case $this->site->infos["GRILLE_GEORSS"];
						$tabpanel .= "";
						break;
					case $this->site->infos["GRILLE_ACTEUR"]:
						//construction des éléments du panel 
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
						//ajoute les données de chaque article
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
						//vérifie s'il faut afficher une carte
						$idDon = $this->VerifDonneeLienGrille($r["id"],$this->site->infos["GRILLE_GEO"]);
						if($idDon && $id!=$this->site->infos["GRILLE_ACTEUR"]){
							$carto = $this->GetXulForm($idDon, $this->site->infos["GRILLE_GEO"]);
							$AddGeo = "";
						}else{
							$carto = "";
							$AddGeo ="<button label='Ajouter une géolocalisation' oncommand=\"AddPlacemark(".$r["idRub"].",'".$this->type."');\"/>";
						}
						
						//construction des éléments du panel 
						$idDoc = "box*".$dst."*".$r["id"]."*".$id."*".$src;
						$tabpanel .="<vbox flex='1' id='".$idDoc."' >";

						//vérifie s'il faut afficher le bouton d'ajout d'acteur
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
												
						//fin des élément du panel
						$tabpanel .="</vbox>";
				}				
				
			
			}else{
				//ajoute la tabbox de destination
				$tabpanel .= $this->GetXulTab($src, $r["id"], $Q[0]->dst, $recur);	
			}
		}
		if($id==$this->site->infos["GRILLE_REP_CON"]){
			if($arrTabpanel){
				//prise en compte de l'ordre de scénarisation des critères
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
		$xulVoirie = "<vbox id='NoeudsBus' ><label value=\"Le(s) arrêt(s) de Bus\"/>";		
		
		//récupère la liste des gares et des pang
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
			//vérifie si l'élément est sélectionné
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
		
		//récupère la liste des éléments de voirie transport
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
			//vérifie si l'élément est sélectionné
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
		
		//récupère la liste des établissement et des voirie
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
			//vérifie si l'élément est sélectionné
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
		
		//récupère la liste des communes
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
			//vérifie si l'élément est sélectionné
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
		
		//vérifie si un lien est présent dans un rubrique
		//est possède la référence à une rubrique 
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
		
		//vérifie si une grille est dans la rubrique de la donnee
		//dans le cas où la donnee est d'une autre grille que celle recherchhée
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
		
		//récupère les données par rapport à une référence dans une autre grille
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
	
	function VerifChoixDiagnostic ($idDon, $typeCritere, $typeContexte,$critere=""){
		
		//si crit est défini on gère une scénarisation
		if($critere==""){
			// On récupere le critere corespondant à la donnée (grille 59 Diagnostic)
			$critere = $this->GetValeur($idDon,'ligne_1'); 
			//vérifie s'il faut traiter les questions intermédiaires pour V2
			if($_SESSION['version']=="V2"){
				if(!$this->VerifQuestionIntermediaire($critere)){
					return false;
				}else{
					//vérifie l'ordre dans le xml
					$this->ordre = $this->GetOrdreQuestionIntermediaire($critere);												
				}
			}
		}
		
		/*si aucun contexte ou critère n'est saisi on renvoie toute les questions
		if(!$typeContexte || !$typeCritere)
			return true;
		if(!$typeContexte[0] 
			&& !$typeContexte[1]
			&& !$typeContexte[2]
			&& !$typeContexte[3]
			&& !$typeCritere[0]
			&& !$typeCritere[1]
			)
			return true;	
		*/
				
		if($this->trace)
			echo "Grille:VerifChoixDiagnostic:On récupere la donnée corespondant au critere (grille ".$this->site->infos["GRILLE_CONTROL_".$_SESSION['version']]." Controle)<br/>";
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
				echo "Grille:VerifChoixDiagnostic: On recupere la valeur du type de critere propre à la donnée (multiple_1 reglementaire ou souhaitable)<br/>";
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
				
				//vérifie les critères réglémentaires souhaitables
				if(($typeCritere[0]== $r1['valeur'] || $typeCritere[1]== $r1['valeur']) ) 
					$ok = $r1['valeur'];
				else 
					$verif = false;
				
				
				//vérifie le contexte réglémentaire uniquement dans le cas des critères réglémentaires 
				if ($ok =='multiple_1_1' && $verif) {
					// On recupere la valeur du type de droit régelementaire (multiple_2)
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
						if($typeContexte[0]== $r2['valeur'] 
							|| $typeContexte[1]== $r2['valeur'] 
							|| $typeContexte[2]== $r2['valeur'] 
							|| $typeContexte[3]== $r2['valeur']) 
							$verif = true;
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
  
  
		//requête pour récupérer la rubrique de la donnée
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
  
  
		//requête pour récupérer l'article de la donnée
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
  
  
		//requête pour récupérer les données de la grille
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
			//$form .= '<caption label="Donnée : '.$idDon.'"/>';
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
					//construstion de la règle législative
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
								echo "GetXulForm début construction du multiple<br/>";
							$controls .= '<groupbox id="'.$id.'" '.$js.' >';
							$controls .= '<hbox>';
							//affiche le bouton sélecionné
							$controls .= $this->GetXulControl($idDoc, $r);
							//conserve la valeur
							$MultiSelect .=  "'".$r['valeur']."',";
						}else{
							//vérifie si la ligne précédente était multiple
							if($MultiSelect!=""){
								//récupère les multiples non sélectionné
								$controls .= $this->GetXulControl($idDoc, $r,substr($MultiSelect,0,-1));
								//fin du multiselect
								$controls .= '</hbox>';
								$controls .= '</groupbox>';
								$MultiSelect = "";
							}else{
								//vérifie s'il faut ajouter la légende de la donnée dans la liste des réponses
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
			//récupère les multiple non sélectionné
			$controls .= $this->GetXulControl($idDoc, $lastRow, substr($MultiSelect,0,-1));
			//fin du multiselect
			$controls .= '</hbox>';
			$controls .= '</groupbox>';
		}
		
		//vérifie s'il faut afficher la liste des noeuds de transport
		if($idGrille == $this->site->infos["GRILLE_LIGNE_TRANS"]){
			$controls .= $this->GetXulNoeudTransport($lastRow["id_rubrique"],$idDon);
			$labels .= "<label class='labelForm' control='first' value='Sélectionner les éléments constituant la ligne'/>";
		}
		
		if($this->trace)
			echo "vérifie s'il faut afficher le liste des noeud de chaine de déplacement $idGrille == ".$this->site->infos["GRILLE_CHAINE_DEPLA"];
		if($idGrille == $this->site->infos["GRILLE_CHAINE_DEPLA"]){
			$controls .= $this->GetXulNoeudDeplacement($lastRow["id_rubrique"],$idDon);
			$labels .= "<label class='labelForm' control='first' value='Sélectionner les éléments constituant la chaîne de déplacement'/>";
		}
		
		//vérifie s'il faut afficher les bassins de gare
		if($idGrille == $this->site->infos["GRILLE_ETAB"]){
			$typeERP = $this->GetValeur($idDon,"mot_2");
			if($typeERP==$this->site->infos["MOT_CLEF_PANG"] || $typeERP==$this->site->infos["MOT_CLEF_GARE"]){
				$controls .= $this->GetXulNoeudCommune($lastRow["id_rubrique"]);
				$labels .= "<label class='labelForm' control='first' value='Sélectionner les communes constituant le bassin de gare'/>";
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
			//ajout d'une ligne pour les questions intermédiaires
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
		//vérifie s'il faut ajouter le bouton de création de placemark
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
		//requête pour récupérer les données de la grille
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
		while($r = $db->fetch_assoc($req)) {
			switch ($r["champ"]) {
				case "multiple_1":
					//construstion réglementaire
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
				case "ligne_1": //référence
					$labels .= '<label value="'.$r['valeur'].'"/>';
					break;
			}					
		}
		//vérifie s'il n'y a d'erreur sur les icones
		if(substr($ico1,-1)!=">" && $ico1!="")$ico1.='.jpg"/>';
		if(substr($ico2,-1)!=">" && $ico2!="")$ico2.='.jpg"/>';
		if(substr($ico3,-1)!=">" && $ico3!="")$ico3.='.jpg"/>';
		if(substr($ico4,-1)!=">" && $ico4!="")$ico4.='.jpg"/>';
				
		$xul = "<vbox class='legende' ><hbox>".$labels."</hbox><hbox>".$ico1.$ico2.$ico3.$ico4."</hbox></vbox>";
		
		return $xul;
	}
	
	
	function GetXulCarto($idDon,$idRub=-1)
	{
		//vérifie s'il faut afficher la carte
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
		
		/*résultat de row
		champ 	rang 	titre 	type 	obligatoire 	extra_info 	
		ligne_1 	6 	valeur étalon 	ligne 	  	  	  	  	  	 
		ligne_2 	7 	valeur étalon 2 	ligne 	  	  	  	  	  	 
		ligne_3 	4 	Nom de la valeur 	ligne 	  	  	  	  	  	 
		mot_1 	5 	opérateur 		mot 	18 	  	  	  	 
		mot_2 	8 	Unités 		mot 	19 	  	  	  	 
		select_1 	9 	règle respectée 	select radio		
		*/
		
		switch ($row['champ']) {
			case 'ligne_1':
				//récupération des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control = '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				$control .= '<label id="trace'.$id.'" value=""/>';
				break;
			case 'ligne_2':
				//récupération des js
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
					//récupération des js
					$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='menu']";
					$js = $this->site->GetJs($Xpath, array($id));
					//construction du control
					$control .= '<menulist id="'.$id.'" '.$js.' ><menupopup >';
					$control .= $this->GetChoixVal($row,'menuitem');				
					$control .= '</menupopup></menulist>';
				}else{				
					//récupération des js
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
				//récupération des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='menu']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<menulist id="'.$id.'" '.$js.' ><menupopup >';
				$control .= $this->GetChoixVal($row,'menuitem');				
				$control .= '</menupopup></menulist>';
				break;
			case 'fichier':
				//récupération des js
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
				//récupération des js
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
						//on cache le textbox référence
						$hidden = "false";
						if($row["champ"]=="ligne_1") $hidden = "true";
						$control .= '<textbox  '.$js.' hidden="'.$hidden.'" multiline="true" class="txtRepCon" id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
						break;
					case $this->site->infos["GRILLE_OBS"]:
						//on empèche l'édition des références
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
		//requête pour récupérer les données de la grille
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
					//récupération des js
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
