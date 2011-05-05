<?php
class Granulat
{
  public $id;
  public $titre;
  public $descriptif;
  public $texte;
  public $moyen;
  public $cause;
  public $consequence;
  public $localisation;
  public $arrDoc;
  public $TitreParent;
  public $IdParent;
  public $trace;
  public $TypeTree;
  public $TypeSaisie;
  public $domEtat;
  
  public $site;
  

  	function __tostring() {
    return "Cette classe permet de définir et manipuler un granulat : .<br/>";
    }

  	function __construct($id, $site, $complet=true) {

    $this->trace = TRACE;
	if($this->trace)
	    echo "$id, $site <br/>";
	
    $this->id = $id;
    $this->site = $site;
	$this->GetProps();
    if($complet && $id!=-1){
		$this->GetDocs();
	}
  }
  


  	function GetKml($gra=-1,$niv=0){
		
		if(is_int($gra))
			$gra = $this;
			
		if($this->trace)
	    	echo "Granulat:GetKml: id=$gra->id <br/>";

		//r�cup�re les kml
		$kmls = $this->GetDocs($gra->id, $this->site->infos["CARTE_TYPE_DOC"]);

		$ficsKml ="";
		if(count($kmls)>0){
			foreach($kmls as $kml){
				$ficsKml .= $kml->fichier."*";
			}
		}else{
			//on ne remonte que jusqu'au grand parent
			//on supprime cette �tape pour flex
			//
			if($gra->IdParent && $niv<6){
				$grap = new Granulat($gra->IdParent,$gra->site,false);
				$ficsKml = $this->GetKml($grap,$niv+1);	
			}
			//
		}
		
		return $ficsKml;
		
	}
  
	
	function GetIdsScope($inCache=false,$allNiv=false){
		
		//purge la liste des enfants
		if(!$inCache){
			$this->DelEnfantId();
		}
		
		//v�rifie si on traite une ligne de transport
		$ligne = $this->VerifExistGrille($this->site->infos["GRILLE_LIGNE_TRANS"]);
		$idMotLiens = $this->site->infos["MOT_CLEF_LIGNE_TRANS"];
		//v�rifie si on traite une chaine de d�placement
		if($ligne==-1){
			$ligne = $this->VerifExistGrille($this->site->infos["GRILLE_CHAINE_DEPLA"]);
			$idMotLiens = $this->site->infos["MOT_CLEF_CHAINE_DEPLA"];
		}
		
		//r�cup�re les enfants
		if($ligne!=-1)
			$ids = $this->GetEnfantIdsInLiens($this->id,",",$idMotLiens);
		else
			$ids = $this->GetEnfantIds($this->id,",",-1,$inCache,$allNiv).$this->id;

		//v�rifie si $ids est renseign�
		if(!$ids)
			$ids=-1;
		
		return $ids;
	}

	function GetFilAriane($id=-1, $niv=0, $sep=" | "){

		if($id==-1)
			$id=$this->id;
		//echo "$id, $niv<br/>";
			
		//cr�ation du granulat
		$g = new Granulat($id, $this->site);
		
		$FilAriane="";
		
		if($g->IdParent!=0){
			$FilAriane.=$this->GetFilAriane($g->IdParent, ($niv+1));
		}else{
			return "";
		}
				
		$FilAriane.=$sep.$g->titre;
					
		return $FilAriane;
		
	}	
  	function GetEtatDiagListe($idDoc,$PourFlex=false,$SaveFile=true){
		
		if($this->trace)
	    	echo "Granulat:GetEtatDiagListe: id=$this->id idDoc=$idDoc<br/>";

		//$ids = $this->GetIdsScope();
   		$path = PathRoot."/bdd/EtatDiag/".$this->site->id."_".$this->id."_".$idDoc."_".$PourFlex.".xml";
	    $contents = $this->site->GetFile($path);
   		if(!$contents){   			
			//construction du xml
			$grille = new Grille($this->site);
			$contents = utf8_encode($grille->GetEtatDiagListe($this->id,$idDoc,$PourFlex,$this->IdParent));
		
			if($SaveFile)
				$this->site->SaveFile($path,$contents);
   		}
			
		$pathJumeau = PathRoot."/bdd/EtatDiag/".$this->site->id."_".$this->id."_".$idDoc."_".$PourFlex."_jumeau.xml";
	    $contentsJumeau = $this->site->GetFile($pathJumeau);
   		if(!$contentsJumeau){
   			//récupère les jumeaux 
	   		$req = $this->GetJumeaux();
					
			//récupère la liste des états jumeaux
			$aJumeau = false;
			while($r = mysql_fetch_assoc($req)) {
				foreach($r as $k=>$val){
					if($k!="id_rubrique" && $val != null){
						if(!$aJumeau){
							//supprime la fin du xml : </vbox>
							$contents = substr($contents,0,-7);
							$aJumeau = true;					
						}
						//récupère la liste des etat diag
						$oSiteJumeau = new Site($this->site->sites,$k,false);
						$grille = new Grille($oSiteJumeau);
						$contentsJumeau = utf8_encode($grille->GetEtatDiagListe($this->id,$idDoc,$PourFlex,$this->IdParent));
						//suprime le début <vbox flex='1'> et la fin du xml </vbox>
						$contentsJumeau = substr($contentsJumeau,15,-7);
						$contents .= $contentsJumeau;
					}
				}		
			}
			if($aJumeau){
				//ajoute la fin du xml : </vbox>
				$contents .= "</vbox>";
				if($SaveFile)
					$this->site->SaveFile($pathJumeau,$contents);
			}
   		}

		return $contents; 
		
	}


	function GetStatBassinGare(){
		
		$stat = "";
		$nbPop = 0;
		$nbPopHandi = 0;
		$communes="";
		$typeERP = $this->GetValeurForm($this->site->infos["GRILLE_ETAB"],"","","","",$this->id,"mot_2");
		if($typeERP==$this->site->infos["MOT_CLEF_PANG"] || $typeERP==$this->site->infos["MOT_CLEF_GARE"]){
			$grille = new Grille($this->site);
			$arrIdRub = split(",",$grille->GetXulNoeudCommune($this->id,true));
			foreach($arrIdRub as $idRub){
				if($idRub){
					$rs = $this->GetGrille($this->site->infos["GRILLE_TERRE"],"",$idRub);	 
					$champs = "";
					while ($r =  mysql_fetch_assoc($rs)) {
						$champs .= "<champ idDon='".$r["id_donnee"]."' champ='".$r["champ"]."' titre=\"".$r["titre"]."\">".$r["valeur"]."</champ>";
						if($r["champ"]=="ligne_2")
							$nbPop+=$r["valeur"];
						if($r["champ"]=="ligne_3")
							$nbPopHandi+=$r["valeur"];
					}
					$communes .= "<commune id='".$idRub."' >".$champs."</commune>";
				}
			}
			$stat = "<bassin idSite='".$this->site->id."' id='".$this->id."' nbPop='".$nbPop."'  nbPopHandi='".$nbPopHandi."' >";
			$stat .= $communes;
			$stat .= "</bassin>";
			
		}
		
		
		return $stat;
		
	}

	function GetActeurBassinGare(){
		
		$xml="";
		$acteurs="";
		$typeERP = $this->GetValeurForm($this->site->infos["GRILLE_ETAB"],"","","","",$this->id,"mot_2");
		if($typeERP==$this->site->infos["MOT_CLEF_PANG"] || $typeERP==$this->site->infos["MOT_CLEF_GARE"]){
			$grille = new Grille($this->site);
			/*calcul les acteurs des communes
			$arrIdRub = split(",",$grille->GetXulNoeudCommune($this->id,true));
			foreach($arrIdRub as $idRub){
				$rs = $this->GetGrille($this->site->infos["GRILLE_ACTEUR"],"",$idRub);	 
				$champs = "";
				while ($r =  mysql_fetch_assoc($rs)) {
					$champs .= "<champ idDon='".$r["id_donnee"]."' champ='".$r["champ"]."' titre=\"".$r["titre"]."\">".$r["valeur"]."</champ>";
				}
				$acteurs .= "<acteur id='".$idRub."' >".$champs."</acteur>";
			}
			*/
			//calcul les acteurs des parents
			$arrIdRub = split(",",$this->GetParentIds("",","));
			foreach($arrIdRub as $idRub){
				if($idRub){
					$rs = $this->GetGrille($this->site->infos["GRILLE_ACTEUR"],"",$idRub);	 
					$champs = "";
					$idDon=0;
					while ($r =  mysql_fetch_assoc($rs)) {
						if($idDon!=$r["id_donnee"] && $idDon!=0){
							$acteurs .= "<acteur id='".$idRub."' >".$champs."</acteur>";
							$champs = ""; 
							$idDon=$r["id_donnee"];
						}
						$champs .= "<champ idDon='".$r["id_donnee"]."' champ='".$r["champ"]."' titre=\"".$r["titre"]."\">".$r["valeur"]."</champ>";
						$idDon=$r["id_donnee"];
					}
					if($champs!=""){
						$acteurs .= "<acteur id='".$idRub."' >".$champs."</acteur>";
					}
				}	
			}
			if($acteurs!=""){
				$xml = "<acteurs id='".$this->id."' >";
				$xml .= $acteurs;
				$xml .= "</acteurs>";
			}
		}		
		return $xml;
		
	}
	
	function GetEtatDiag($PourFlex=false,$SaveFile=false,$calcul=false){
		
		$deb = microtime(true);
				
		if($this->trace)
	    	echo $deb."Granulat:GetEtatDiag: id= $this->id<br/>";

	    //r�cup�re toutes les rubrique enfants
	    $ids = $this->GetIdsScope();
	    	
		//calculer l'état du diagnostique
		$grille = new Grille($this->site);
		$numDiag=0;
		if(!$calcul){
			//r�cup�re les rubriques ayant un diagnostique
			$rs = $grille->FiltreRubAvecGrille($this->id,$this->site->infos["GRILLE_REP_CON"]);
			$numDiag = mysql_num_rows($rs);
			$fin = microtime(true)-$deb;
			if($this->trace)
	    		echo "Granulat:GetEtatDiag:FiltreRubAvecGrille  id = $this->id num = $num  $fin<br/>";
			if($_SESSION['ForceCalcul']){
				//calcul les diagnostiques
				while ($r =  mysql_fetch_assoc($rs)) {
					//cr�ation du granulta
					$g = new Granulat($r["id_rubrique"],$this->site,false);
					$g->GetEtatDiag($PourFlex,$SaveFile,$calcul);
				}
			}
		}
		//calcul ou r�cup�re les diagnostiques
		$EtatOui = $grille->GetEtatDiagOui($ids,$this->id,$calcul);
		$Etat1 = $grille->GetEtatDiagHandi($ids,1,$this->id,$calcul);
		$Etat2 = $grille->GetEtatDiagHandi($ids,2,$this->id,$calcul);
		$Etat3 = $grille->GetEtatDiagHandi($ids,3,$this->id,$calcul);
		$EtatAppli = $grille->GetEtatDiagApplicable($ids,$this->id,$calcul);
		
		//sort dans le cas du calcul
		$fin = microtime(true)-$deb;
		if($this->trace)
    		echo "Granulat:GetEtatDiag:avant retour $fin<br/>";
				
		//calculer le l'indicateur d'accessibilit�
		$fin = microtime(true)-$deb;
		if($this->trace)
    		echo "Granulat:GetEtatDiag:calculer le l'indicateur d'accessibilit� $fin<br/>";
		$moteurObst = $this->GetHandiObstacle($Etat1,$Etat2,$Etat3,"moteur");
		$moteur = $this->GetHandiAccess($moteurObst,$EtatAppli["r"]["moteur"],$Etat3["r"]["moteur"]);
		
		$audioObst = $this->GetHandiObstacle($Etat1,$Etat2,$Etat3,"audio");
		$audio = $this->GetHandiAccess($audioObst,$EtatAppli["r"]["audio"],$Etat3["r"]["audio"]);
		
		$visuObst = $this->GetHandiObstacle($Etat1,$Etat2,$Etat3,"visu");
		$visu = $this->GetHandiAccess($visuObst,$EtatAppli["r"]["visu"],$Etat3["r"]["visu"]);
		
		$cogObst = $this->GetHandiObstacle($Etat1,$Etat2,$Etat3,"cog");
		$cog = $this->GetHandiAccess($cogObst,$EtatAppli["r"]["cog"],$Etat3["r"]["cog"]);
		
		//calculer les icones suppl�mentaires
		$FormIds = $this->GetFormIds(-1,$this->id);
		//ajoute le parent
		$ids .= ",".$this->IdParent;
		$Icos = $grille->GetEtatDiagIcones($FormIds, $ids);	

		//calcul les documents des articles
		$xulDoc = "";
		$Arts = $this->GetArticleInfo();
		$xul = new Xul($this->site);
		$IcosDoc ="<icones id='ico_'>";
		//petit bug flex quand il n'y a qu'une icone
		$IcosDoc .="<icone id='vide' />";
		while($r = mysql_fetch_assoc($Arts)) {
			$IcosDoc .= $xul->GetFriseDocsIco($r["id_article"],-1,false,true);
		}
		//v�rifie s'il y a une g�olocalisation
		$geo = $this->GetValeurForm($this->site->infos["GRILLE_GEO"],"lat");
		if($geo)
			$IcosDoc .= "<icone id='kml' />";		
		$IcosDoc .="</icones>";
				
		//r�cup�re le niveau d'ach�vement de l'�tat
		$numDiag = $grille->GetNumEtatDiagFait($this->id)." sur ".$numDiag;	
		//initialisation du xml
		$xml = "<EtatDiag idSite='".$this->site->id."' idRub='".$this->id."' titre=\"".utf8_encode($this->titre)."\" TauxCalc='".$numDiag."' >";
		//construction du xml
		if($PourFlex){
			
			$xml .= "<Obstacles id='moteur' >
				<niv0>".$EtatOui["r"]["moteur"]."</niv0>
				<niv1>".$Etat1["r"]["moteur"]."</niv1>
				<niv2>".$Etat2["r"]["moteur"]."</niv2>
				<niv3>".$Etat3["r"]["moteur"]."</niv3>
				<handi>".$moteur."</handi>
				<appli>".$EtatAppli["r"]["moteur"]."</appli>
			</Obstacles>";
			$xml .= "<Obstacles id='audio' >
				<niv0>".$EtatOui["r"]["audio"]."</niv0>
				<niv1>".$Etat1["r"]["audio"]."</niv1>
				<niv2>".$Etat2["r"]["audio"]."</niv2>
				<niv3>".$Etat3["r"]["audio"]."</niv3>
				<handi>".$audio."</handi>
				<appli>".$EtatAppli["r"]["audio"]."</appli>
			</Obstacles>";
			$xml .= "<Obstacles id='cognitif' >
				<niv0>".$EtatOui["r"]["cog"]."</niv0>
				<niv1>".$Etat1["r"]["cog"]."</niv1>
				<niv2>".$Etat2["r"]["cog"]."</niv2>
				<niv3>".$Etat3["r"]["cog"]."</niv3>
				<handi>".$cog."</handi>
				<appli>".$EtatAppli["r"]["cog"]."</appli>
			</Obstacles>";
			$xml .= "<Obstacles id='visuel' >
				<niv0>".$EtatOui["r"]["visu"]."</niv0>
				<niv1>".$Etat1["r"]["visu"]."</niv1>
				<niv2>".$Etat2["r"]["visu"]."</niv2>
				<niv3>".$Etat3["r"]["visu"]."</niv3>
				<handi>".$visu."</handi>
				<appli>".$EtatAppli["r"]["visu"]."</appli>
			</Obstacles>";
			
			$xml .= $Icos; 
			$xml .= $IcosDoc;
			$xml .= $this->GetStatBassinGare();
			$xml .= $this->GetActeurBassinGare();
		}else{
			$xml .= $EtatOui["xml"];
			$xml .= $Etat1["xml"];
			$xml .= $Etat2["xml"];
			$xml .= $Etat3["xml"];
			$xml .= "<Applicables id='IndicAcc_' moteur='".$moteur."' audio='".$audio."' visu='".$visu."' cog='".$cog."' ></Applicables>";
			$xml .= "<AppliVal id='AppliVal_' moteur='".$moteurObst."-".$EtatAppli["r"]["moteur"]."' audio='".$audioObst."-".$EtatAppli["r"]["audio"]."' visu='".$visuObst."-".$EtatAppli["r"]["visu"]."' cog='".$cogObst."-".$EtatAppli["r"]["cog"]."' ></AppliVal>";
			$xml .= $Icos; 
			$xml .= $IcosDoc;
		}
		//finalisation du xml
		$xml .= "</EtatDiag>";			

		$fin = microtime(true)-$deb;
		if($this->trace)
    		echo "Granulat:GetEtatDiag:avant retour final $fin<br/>";
		
		if($SaveFile){
			$path = PathRoot."/bdd/EtatDiag/".$this->site->id."_".$this->id."_flex.xml";
			$this->site->SaveFile($path,$xml);
		}
		
		//vérifie si la même rubrique est présente dans les sites enfants
		
		
		return $xml;
		
	}

	function GetJumeaux(){

		//construction de la requête
		$select = "SELECT r.id_rubrique";
		$from = " FROM ".$this->site->infos["SQL_DB"].".spip_rubriques r ";
		$where = " WHERE r.id_rubrique =".$this->id;
		$oSitesFreres = array();
		$i = 1;
		if($this->site->infos["SITE_PARENT"]!=-1){
			//récupère les parents du site
	 		foreach($this->site->infos["SITE_PARENT"] as $idSiteParent=>$typeSiteParent)
			{
				$siteParent = new Site($this->site->sites,$idSiteParent,false);
				//construction de la requête pour chaque frères
				if($siteParent->infos["SITE_ENFANT"]!=-1){
			 		foreach($siteParent->infos["SITE_ENFANT"] as $idSiteFrere=>$typeSiteFrere)
					{
						if($idSiteFrere!=$this->site->id){
							$oSiteFrere = new Site($this->site->sites,$idSiteFrere,false);
							$select .= ", r".$i.".id_rubrique ".$idSiteFrere;
							$from .= " LEFT JOIN ".$oSiteFrere->infos["SQL_DB"].".spip_rubriques r".$i." ON r".$i.".id_rubrique = r.id_rubrique AND r".$i.".titre = r.titre";
							$i++;
						}
					}
				}
			}
		}
		
		$sql = $select.$from.$where;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
	
		return $req;
	}
	
	function GetEtatDiagFrere($xmlEtatDiag="", $SaveFile="true"){

		$req = $this->GetJumeaux();
				
		//ajoute l'état de diagnostic pour chaque rubrique frère
		$EtatsDiags = "<EtatsDiags>".$xmlEtatDiag;
		$aFrere =false;
		while($r = mysql_fetch_assoc($req)) {
			foreach($r as $k=>$val){
				if($k!="id_rubrique" && $val != null){
					$aFrere = true;
					$oSiteFrere = new Site($this->site->sites,$k,false);
					$g = new Granulat($val,$oSiteFrere);
					$EtatsDiags .= $g->GetEtatDiag(true,true);
				}
			}		
		}
		if(!$aFrere){
			$EtatsDiags = "";
		}else{
			$EtatsDiags .= "</EtatsDiags>";
			$EtatsDiags = utf8_encode($EtatsDiags);
		}
		
		if($SaveFile){
			$path = PathRoot."/bdd/EtatDiag/".$this->site->id."_".$this->id."_flex_frere.xml";
			$this->site->SaveFile($path,$EtatsDiags);
		}
		
		return $EtatsDiags;	
	}
	
	function GetEtatDiagFamille($path, $pathFrere, $pathEtatFamille){
	
	
		$this->domEtat = new DOMDocument;
		$this->domEtat->Load($path);
		$xpath = new DOMXPath($this->domEtat);

		$domFrere = new DomDocument;
		$domFrere->load($pathFrere);
		$xpathFrere = new DomXPath($domFrere);

		$Sumquery = "sum(/EtatsDiags/EtatDiag/Obstacles[@id='moteur'])";
		$sum = $xpathFrere->evaluate($Sumquery);
		
		//calcul les état pour la famille
		$this->CalculEtatDiagFamille("moteur", $xpath, $xpathFrere);		
		$this->CalculEtatDiagFamille("audio", $xpath, $xpathFrere);		
		$this->CalculEtatDiagFamille("cognitif", $xpath, $xpathFrere);		
		$this->CalculEtatDiagFamille("visuel", $xpath, $xpathFrere);		
				
		$this->domEtat->save($pathEtatFamille);	
	
		return $this->domEtat->saveXML(); 
	}

	function CalculEtatDiagFamille($handi, $xpath, $xpathFrere){

		$sums = array();
		//additionne et remplace chaque niveau de EtatDiag
		for($i=0; $i<4; $i++){
			$query = "/EtatDiag/Obstacles[@id='".$handi."']/niv".$i;
			$entries = $xpath->query($query);		
			$element = $entries->item(0); 		
			$Sumquery = "sum(/EtatsDiags".$query.")";
			$sums[$i] = $xpathFrere->evaluate($Sumquery);
	
			$xEl = $this->domEtat->createElement("niv".$i);
			$text = $this->domEtat->createTextNode($sums[$i]);
			$xEl->appendChild($text);
		    $element->parentNode->replaceChild($xEl, $element);
		}
		//additionne et remplace le nombre de critère applicable
		$query = "/EtatDiag/Obstacles[@id='".$handi."']/appli";
		$entries = $xpath->query($query);		
		$element = $entries->item(0); 		
		$Sumquery = "sum(/EtatsDiags".$query.")";
		$sums[4] = $xpathFrere->evaluate($Sumquery);
		$xEl = $this->domEtat->createElement("appli");
		$text = $this->domEtat->createTextNode($sums[4]);
		$xEl->appendChild($text);
	    $element->parentNode->replaceChild($xEl, $element);
		
		//calcul l'handicateur
		$Obst = $sums[1]+($sums[2]*2)+($sums[3]*3);
		$h = $this->GetHandiAccess($Obst,$sums[4],$sums[3]);		
		
		//remplace l'handicateur
		$query = "/EtatDiag/Obstacles[@id='".$handi."']/handi";
		$entries = $xpath->query($query);		
		$element = $entries->item(0); 		
		$xEl = $this->domEtat->createElement("handi");
		$text = $this->domEtat->createTextNode($h);
		$xEl->appendChild($text);
	    $element->parentNode->replaceChild($xEl, $element);

	    //modifie le taux de calcul une seule fois
	    $TCfait = 0;  		
	    $TCafaire = 0;  		
	    if($handi == "moteur"){
			$query = "/EtatsDiags/EtatDiag";
	    	$entries = $xpathFrere->query($query);
	    	foreach($entries as $xEl){
	    		$attTC = $xEl->getAttribute("TauxCalc");
	    		//décompose le texte
	    		$arrTC = explode(" sur ",$attTC);
	    		$TCfait += $arrTC[0];  		
	    		$TCafaire += $arrTC[1];  		
	    	}
			$query = "/EtatDiag";
			$entries = $xpath->query($query);		
	    	$element = $entries->item(0); 		
	    	$element->setAttribute("TauxCalc", $TCfait." sur ".$TCafaire);
	    }
	}
	
	function GetHandiObstacle($Etat1,$Etat2,$Etat3,$Handi){
		$handi = $Etat1["r"][$Handi]
			+($Etat2["r"][$Handi]*2)
			+($Etat3["r"][$Handi]*3);  
			
		return $handi;	
	}
	
	function GetHandiAccess($HandiObst,$HandiAppli, $Handi3){
		if($HandiAppli==0)
			return "A";	
		//calcul le coefficient d'handicateur
		$handi = $HandiObst/$HandiAppli;
		//retourn la lettre correspond au coefficient
		//suivant l'interval et suivant la contrainte de niveau trois
		if($handi>=0 && $handi<=0.2 && $Handi3==0)	
			return "A";	
		if($handi>0.2 && $handi<=0.4 && $Handi3==0)	
			return "B";
		//attention on r�initialise l'interval pour afficher les cas pr�c�dent ayant un Handi 3
		// 0.4 devient 0	
		if($handi>=0 && $handi<=0.6)	
			return "C";	
		if($handi>0.6 && $handi<=0.8)	
			return "D";	
		if($handi>0.8)	
			return "E";	
	}
	
	function GetTreeChildren($type,$id=-1){

	    if($this->trace)
	    	echo "Granulat:GetTreeChildren: type = $type Cols = $Cols, id= $id<br/>";
		
		$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTree']/Querys/Query[@fonction='GetTreeChildren_".$type."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		if($id==-1){
			$id = $this->id;
		}
	
		$where = str_replace("-parent-", $id, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		
	    if($this->trace)
			echo "Granulat:GetTreeChildren:sql=".$sql."<br/>";

		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		
		if(mysql_num_rows($req)>0)
			return $req;
		else
			return false;

	}
  
 
/*
 * Parcourt r�cursivement les enfants afin de cr�er l'arborescence des rubriques et articles dans spip (correspondant � l'import)  
 */ 
  	function SetRubElements($xml, $idParent, $rubriques, $articles, $dom, $update) {
  		
  		//$rubriques = $xml->GetElements($Xpath);
  		/*if($this->trace)
			print_r($rubriques);
			print_r($articles);
		* */
  		$i = 0;
  		$g = new Granulat($idParent, $this->site); 
  		
  		foreach($articles as $article) {
  			
  			if ($g->VerifExistArticle($article['id'], $article['idRub'])==-1) { 
  				
	  			$nouvelArt = $dom->createElement("art");
				$nouvelArt->setAttribute("oldId", $article['id']);
	  			
	  			$donnees = $article->donnees;
	  			$idGrille = $donnees->grille;
	  			$idAuteur = $article->auteur;
	  			$champs = $donnees->champs;
	  			$date = $article->date;
	  			$maj = $article->maj;
	  			
	  			$idArt = $g->SetNewArticleComplet(utf8_decode($article), $date, $maj);
	  			if ($idAuteur!= "") $g->AddAuteur($idArt, $idAuteur);
	  			
	  			$nouvelArt->setAttribute("newId", $idArt);
	  			$nouvelArt->setAttribute("newRub", $g->id);
	  			
		  		$dom->lastChild->appendChild($nouvelArt);
	  			
	  			if($this->trace)
	  					print_r($donnees->donnee);
	  					
	  			foreach($donnees->donnee as $donnee){
	  				$j=0;
	  				if($this->trace)
	  					print_r($donnee->valeur);
	
	  				$idDon = $g->AddIdDonnee($idGrille, $idArt, $donnee->date, $donnee->maj);
					if($this->trace)
						echo "Granulat/AddXmlFile/- cr�ation de la donnee ".$idDon."<br/>";	
	  				
					foreach($donnee->valeur as $valeur) {
						if($valeur!='non'){
							$valeur=utf8_decode($valeur);
							$champ = $champs[0]->champ[$j];
							if($this->trace)
								echo "Granulat/AddXmlFile/--- gestion des champs multiples ".substr($champ,0,8)."<br/>";
							if(substr($champ,0,8)=="multiple"){
								$valeur=$champ;
							//attention il ne doit pas y avoir plus de 10 choix
								$champ=substr($champ,0,-2);
							}
							if($this->trace) {
								echo "Granulat/AddXmlFile/-- r�cup�ration du type de champ ".$champ."<br/>";
								echo "Granulat/AddXmlFile/-- r�cup�ration de la valeur du champ ".$valeur."<br/>";
							}
							$row = array('champ'=>$champ, 'valeur'=>$valeur);
							
							$grille = new Grille($g->site);
							if($this->trace)
								echo "Granulat/AddXmlFile/--- cr�ation du champ <br/>";
							$grille->SetChamp($row, $idDon, false);
							
						}
						$j++;
					}
	  			}
	  		if ($update) 
	  			$g->UpdateIdArt($idArt, $article['id'], $article['idRub']);
  			} 	
  		}
  		
  		foreach($rubriques as $rubrique) {
  			
  			if ($rubrique['idAdmin']!="") {
  				if ($update) {
  					$g->UpdateAdminRub($rubrique['id'], $rubrique['idAdmin']);
  				}
  			}
  			
  			if ($g->VerifExistRubrique($rubrique['id'], $rubrique['idParent'])==-1) {
	  			$nouvelleRub = $dom->createElement("rub");
				$nouvelleRub->setAttribute("oldId", $rubrique['id']);
	  			
	  			$idEnfant = $g->SetNewEnfant(utf8_decode($rubrique));
	  			$g->SetMotClef($rubrique->motclef, $idEnfant);
	  			
	  			$nouvelleRub->setAttribute("newId", $idEnfant);
	  			$nouvelleRub->setAttribute("parentId", $idParent);
		  		$dom->lastChild->appendChild($nouvelleRub);
		  		
		  		if ($update) {
		  			$g->UpdateIdRub($idEnfant, $rubrique['id'], $rubrique['idParent']);
		  		} else if ($rubrique['idAdmin']!="")	{
		  			$g->UpdateAdminRub($idEnfant, $rubrique['idAdmin']);
		  		}
  			} else $idEnfant = $rubrique['id'];
	  			
  			$g->SetRubElements($xml, $idEnfant, $rubrique->rubrique, $rubrique->article, $dom, $update);
  			//$i++;  //$rubriques[$i]->rubrique, $rubriques[$i]->article
  		}	
  	}
  	
  	/*
  	 * V�rifie l'existence d'une rubrique dans la table spip_rubriques, retourne -1 si la rubrique n'est pas trouv�e
  	 * 
  	 */
	public function VerifExistRubrique($idRub, $idParent) {
		
		$sql = "SELECT id_rubrique
				FROM spip_rubriques
				WHERE id_rubrique = ".$idRub." AND id_parent = ".$idParent;
		;//LIMIT 0 , 93";

		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		if($data = $DB->fetch_assoc($req)) {
			return $data['id_rubrique'];
		} else return -1;
	}
	
	/*
  	 * V�rifie l'existence d'un article dans la table spip_articles, retourne -1 si l'article n'est pas trouv�
	 * 	 * 
	 */
	public function VerifExistArticle($idArt, $idRub) {
		
		$sql = "SELECT id_article
				FROM spip_articles
				WHERE id_article = ".$idArt." AND id_rubrique = ".$idRub;
		;//LIMIT 0 , 93";

		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		if($data = $DB->fetch_assoc($req)) {
			return $data['id_article'];
		} else return -1;
	}

	public function VerifExistGrille($idGrille,$idRub=-1) {
		
		if($idRub==-1)
			$idRub = $this->id;
		
		$sql = "SELECT a.id_article
			FROM spip_articles a
				INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
				INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
				INNER JOIN spip_forms_donnees_champs dc ON dc.id_donnee = da.id_donnee
				INNER JOIN spip_forms_champs fc ON fc.champ = dc.champ
			WHERE a.id_rubrique =".$idRub."
				AND fd.id_form =".$idGrille;
			;//LIMIT 0 , 93";

		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		if($data = $DB->fetch_assoc($req)) {
			return $data['id_article'];
		} else return -1;
	}
	
		
	/*
	 * Met � jour les identifiants des rubriques dans les tables spip_rubriques, spip_mots_rubriques et spip_articles 
	 * 
	 */
	public function UpdateIdRub($idRubOld, $idRubNew, $idParent) {
		
		if($this->trace)
			echo "Synchro:UpdateIdRub:idRubNew ".$idRubNew;
		
		$sql = "UPDATE `spip_rubriques`
				SET id_rubrique = ".$idRubNew."
				WHERE id_rubrique = ".$idRubOld;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		$sql = "UPDATE `spip_rubriques`
				SET id_parent = ".$idParent."
				WHERE id_rubrique = ".$idRubNew;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		$sql = "UPDATE `spip_mots_rubriques`
				SET id_rubrique = ".$idRubNew."
				WHERE id_rubrique = ".$idRubOld;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
				
	}
	
	/*
	 * Met � jour les identifiants des articles dans les tables spip_articles, spip_forms_articles, spip_forms_donnees_articles et spip_auteurs_articles
	 * 
	 */
	public function UpdateIdArt($idArtOld, $idArtNew, $idRubNew) {
		
		if($this->trace)
			echo "Synchro:UpdateIdArt:idArtNew ".$idArtNew;
		
		$sql = "UPDATE `spip_articles`
				SET id_article = ".$idArtNew."
				WHERE id_article = ".$idArtOld;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		$sql = "UPDATE `spip_forms_articles`
				SET id_article = ".$idArtNew."
				WHERE id_article = ".$idArtOld;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		$sql = "UPDATE `spip_forms_donnees_articles`
				SET id_article = ".$idArtNew."
				WHERE id_article = ".$idArtOld;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		$sql = "UPDATE `spip_auteurs_articles`
				SET id_article = ".$idArtNew."
				WHERE id_article = ".$idArtOld;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		$sql = "UPDATE `spip_articles`
				SET id_rubrique = ".$idRubNew."
				WHERE id_article = ".$idArtNew;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
	}
	
	
	public function GetIdAdmin($idRub) {
		
		$sql = "SELECT id_auteur, id_rubrique FROM spip_auteurs_rubriques a WHERE a.id_rubrique=".$idRub;					
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		$idAuteur=-1;
		if ($r = $DB->fetch_assoc($req)){
			$idAuteur = $r['id_auteur']; 
		}
		return $idAuteur;
		
	}
	
  	function SetAuteur($newId,$objet){

	  	//pas de cr�ation d'auteur pour les rubriques
	  	if($objet=="rubrique")
	  		return;
	  		
	  	if($this->site->scope["login"]!=-1){
				//association de l'article � l'auteur
				$sql = "INSERT INTO spip_auteurs_".$objet."s (id_".$objet.",id_auteur)
					SELECT ".$newId.", id_auteur FROM spip_auteurs where login='".$this->site->scope["login"]."'";					
				$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
				$req = $DB->query($sql);
				$DB->close();
		}
  	
  	}
  
  	function AddAuteur($idArt, $idAuteur) {
  		$sql = "INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES (".$idAuteur.", ".$idArt."	)"	;	
 		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
  	}
  	
  	function GetAuteurArticle($idArt) {
	  
		//association de l'article � l'auteur
		$sql = "SELECT id_auteur, id_article FROM spip_auteurs_articles a WHERE a.id_article=".$idArt;					
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		if ($r = $DB->fetch_assoc($req)){
			$idAuteur = $r['id_auteur']; 
		}
		return $idAuteur;
  	}
  
  	function SetNewEnfant($titre,$id=-1){

	if($id==-1)
		$id=$this->id;
	
	//ajoute un nouvel enfant
	$sql = "INSERT INTO spip_rubriques
		SET titre = ".$this->site->GetSQLValueString($titre, "text").", id_parent=".$id;
	
	$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
	$req = $DB->query($sql);
	$newId = mysql_insert_id();
	$DB->close();
			
	return $newId;
  
  }
  
  	function SetNewArticle($titre,$id=-1){

		if($id==-1)
			$id=$this->id;
		
		//ajoute un nouvel enfant
		$sql = "INSERT INTO spip_articles
			SET titre = ".$this->site->GetSQLValueString($titre, "text")
				.", statut='prepa'
				, date = now()"
				.", id_rubrique=".$id;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$newId = mysql_insert_id();
		$DB->close();
	
		$this->SetAuteur($newId,'article');
		
		return $newId;
  
 	}

  	function SetRSS($url){

		$rss = $this->site->GetCurl($url);
		$grille = new Grille($this->site);
		$idDon = $grille->AddDonnee($this->id, $this->site->infos["GRILLE_GEORSS"],false,-1,true);
		$grille->SetChamp(array("champ"=>"url_1","valeur"=>$url),$idDon);
		$grille->SetChamp(array("champ"=>"texte_1","valeur"=>$this->site->GetSQLValueString($rss, "html")),$idDon);

		return "GeoRR ajout�e = ".$url;
  
 	}

  	function GetXmlRSS(){

  		$rs = $this->GetGrille($this->site->infos["GRILLE_GEORSS"]);
		$xml = "<geoRSS>";
		while($r = mysql_fetch_assoc($rs)) {
			if($r['champ']=="url_1"){
				$xml .= "<url idDon='".$r['id_donnee']."'><![CDATA[".$r['valeur']."]]></url>";			
			}
		}
		$xml .= "</geoRSS>";
  		
		return $xml;
  
 	}
 	
 	function SetNewSyndic($titre,$descriptif,$url,$id=-1){

		if($id==-1)
			$id=$this->id;
		
		//ajoute un nouvel enfant
		$sql = "INSERT INTO spip_syndic
			SET nom_site = ".$this->site->GetSQLValueString($titre, "text")
				.", url_site='".$url."' "
				.", descriptif='".$descriptif."' "
				.", statut='prepa' , date = now()"
				.", id_rubrique=".$id;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$newId = mysql_insert_id();
		$DB->close();
		
		return $newId;
  
 	}
 	
  	function SetNewArticleComplet($titre, $date, $maj, $id=-1) {
  		if($id==-1)
			$id=$this->id;
	
		//ajoute un nouvel enfant
		$sql = "INSERT INTO spip_articles
			SET titre = ".$this->site->GetSQLValueString($titre, "text")
				.", statut='prepa'
				, date ='".$date
				."', maj ='".$maj
				."', id_rubrique=".$id;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$newId = mysql_insert_id();
		$DB->close();
	
		$this->SetAuteur($newId,'article');
		
		return $newId;
  	}
  	
  	function SetMotClef($id_mot,$id=-1,$type="rubrique"){

	if($id==-1)
		$id=$this->id;
	
	//ajoute un nouveau mot clef
	if ($id_mot != ""){
		if($type=='rubrique')
			$sql = "INSERT INTO spip_mots_rubriques
					SET id_mot = ".$id_mot.", id_rubrique=".$id;
		if($type=='syndic')
			$sql = "INSERT INTO spip_mots_syndic
					SET id_mot = ".$id_mot.", id_syndic=".$id;
			
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$newId = mysql_insert_id();
		$DB->close();
	} else $newId = -1;
	return $newId;
  
  }
  
	function GetXmlGrilles(){
		$xml = "";
		//r�cup�re les grilles du granulat 
		$rsG = $this->GetFormIds(-1,$this->id);
		if(mysql_num_rows($rsG)>0){
			$xml .= "<grilles>";
			while($rG = mysql_fetch_assoc($rsG)) {
				$xml .= "<grille id='".$rG['id_form']."' titre='".$rG['titre']."' idArt='".$rG['id_article']."' />";
			}
			$xml .= "</grilles>";
		}
		return $xml;			
	}
  
  
	function GetXmlGrillesValues($idGrille=-1,$valeurs=false){
		$xml = "";
		//r�cup�re les grilles du granulat 
		$rsG = $this->GetFormIds(-1,$this->id,$idGrille);
		if(mysql_num_rows($rsG)>0){
			$xml .= "<grilles>";
			while($rG = mysql_fetch_assoc($rsG)) {
				//$titre = $this->site->XmlParam->XML_entities($rG['titre']);
				$xml .= "<grille id='".$rG['id_form']."' idArt='".$rG['id_article']."' >";
				$rsD = $this->GetIdDonnees($rG['id_form'], $rG['id_article']);
				if(mysql_num_rows($rsD)>0){
					while($rD = mysql_fetch_assoc($rsD)) {
						$xml .= "<donnee id='".$rD['id_donnee']."' idGrille='".$rG['id_form']."' >";
						if($valeurs){
							//foreach($valeurs as $val){
							//	if($val["id_form"]==$rG['id_form']){
									$rsV = $this->GetInfosDonnee($rD['id_donnee']);									
									while($rV = mysql_fetch_assoc($rsV)) {										
										$xml .= "<valeur id='".$rD['id_donnee']."' champ='".$rV["champ"]."' ";
										$xml .= " valeur=\"".utf8_encode($this->site->XmlParam->XML_entities($rV["valeur"]))."\" >";
										//v�rifie qu'on ne traite pas un mot
										if(substr($rV["champ"],0,4)=="mot_" && $rV["valeur"]!=""){
											$mc = new MotClef($rV["valeur"],$this->site);
											$xml .= "<motclef id='".$mc->id."' idGroupe='".$mc->id_groupe."' titre=\"".utf8_encode($this->site->XmlParam->XML_entities($mc->titre))."\" descriptif=\"".utf8_encode($this->site->XmlParam->XML_entities($mc->descriptif))."\"  />";	
										}
										$xml .= "</valeur>";
									}															
							//	}
							//}
						}
						$xml .= "</donnee>";
					}
				}
				$xml .= "</grille>";
			}
			$xml .= "</grilles>";
		}
		return $xml;			
	}
	
	
	function GetXmlGrilleMots(){
		$xml = "";

		//r�cup�re les mots-clef du granulat
		$rsMC = $this->GetTypeMotClef("rubrique");
		if(count($rsMC)>0){
			$xml .= "<motsclefs>";
			foreach($rsMC as $mc) {
				$xml .= "<motclef id='".$mc->id."' titre='".$mc->titre."'  />";
			}
			$xml .= "</motsclefs>";
		}
		
		return $xml;	
		
	}
		
	function GetXmlCartoDonnee($row,$fin=false){
		$xml="";
		
		$xml .= "<CartoDonnee lat='".$row['lat']."'";
		
		$xml .= " lng='".$row['lng']."'";
		
		if($row['id_rubrique'])
			$id = $row['id_rubrique'];
		else
			$id = $row['id'];
		
		$xml .= " idRub='".$id."'";
				
		$xml .= " idSite='".$this->site->id."'";
		
		$xml .= " titre=\"".utf8_encode($this->site->XmlParam->XML_entities($row['titre']))."\"";
		
		/*
		$markers .= "topic_$i ".DELIM;
		//Topic
		$markers .=Root."/new/lieux.php?site=".$objSite->id."&VoirEn=Topos&Rub=".$row['id_rubrique']."&query=".$NewQuery.DELIM;//lien
		//$markers .=get_fenetre_info($row,"Topic").DELIM;//localisation
		if($row['navig'])
			$markers .=$row['navig'].DELIM;		
		else
			$markers .=" ".DELIM;
		//$markers .=$g->GetImages(68, 45).DELIM;//image
		$markers .= "".DELIM;//image
		
		$markers .=utf8_encode(tronquer($row['texte'],60)).DELIM;
		//création des onglets pour le granulats
		//$Val = $g->GetValeurForm($this->site->infos["GRILLE_Granulat"],"Titre", "", "  ", "Titre : ");
		//if(substr($row['descriptif'], -2)!="00")
		//if($Val!=" ")
			//Famillie sauf pour département et communes
		//	$markers .=get_fenetre_info($row,"Granulat").DELIM;
		//else
			$markers .="".DELIM;

		 if(substr($row['descriptif'], -4)!="0000")
			//Thematique sauf pour département
			$markers .=get_fenetre_info($row,"Thematique").DELIM;
		else
			$markers .="".DELIM;
		*/
		//zoom

		$xml .= " zoommin='".$row['zoommin']."'";
		
		$xml .= " zoommax='".$row['zoommax']."'";
		
		//adresse
		$xml .= " adresse=\"".utf8_encode($this->site->XmlParam->XML_entities($row['adresse']))."\"";
		
		//type carte
		$xml .= " cartotype='".$row['cartotype']."'";
		
		//lien vers le kml
		$kml="";
		if($row['docArtkml'])
			$kml = $this->site->infos["pathSpip"].$row['docArtkml'];
		if($kml=="")	
			$kml = $row['kml'];
		if($kml=="")
			$kml = $this->GetKml();
		$xml .= " kml='".$kml."'";
				
		//cr�ation de l'identidiant xul
		$idDoc = 'val'.DELIM.$this->site->infos["GRILLE_GEO"].DELIM.$row["idDon"].DELIM."fichier".DELIM.$row["idArt"];
		$xml .= " idDoc='".$idDoc."'";
		
		//finalisation des attributs de CartoDonnee
		if($fin)
			$xml .= " />";
		else
			$xml .= " >";
		
		return $xml;
						
	}
	
	
  	function GetGeo($id=-1,$idDon=-1,$niv=-1) {
		if($id==-1)
			$g = $this;
		else
			$g = new Granulat($id,$this->site);
		if($idDon==-1){
			$where = " WHERE r.id_rubrique =".$g->id;
			$result['query'] = "admin";
			$result['id'] = $g->id;
		}else{
			$where = " WHERE fd.id_donnee =".$idDon;
			$result['query'] = "adminDon";
			$result['id'] = $idDon;
		}
		
		$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, r.id_parent, da.id_donnee
				,dc1.valeur lat, dc2.valeur lng, dc3.valeur zoom, dc4.valeur type, dc5.valeur zoommax
				, dc7.valeur adresse
				, dc8.valeur kml
				, dArt.fichier docArtkml
				FROM spip_rubriques r
				INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
				INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
				INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee AND fd.id_form = ".$this->site->infos["GRILLE_GEO"]."
				INNER JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
				INNER JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
				INNER JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
				INNER JOIN spip_forms_donnees_champs dc5 ON dc5.id_donnee = da.id_donnee AND dc5.champ = 'ligne_4'
				INNER JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'mot_1'
			INNER JOIN spip_forms_donnees_champs dc7 ON dc7.id_donnee = da.id_donnee AND dc7.champ = 'ligne_7'
			LEFT JOIN spip_forms_donnees_champs dc8 ON dc8.id_donnee = da.id_donnee AND dc8.champ = 'texte_1'
			LEFT JOIN spip_documents_articles doca ON doca.id_article = a.id_article
			LEFT JOIN spip_documents dArt ON dArt.id_document = doca.id_document AND dArt.id_type IN (".$this->site->infos["CARTE_TYPE_DOC"].")
				".$where."
			ORDER BY dc1.valdec, dArt.fichier DESC
			LIMIT 0 , 1";
		//echo $sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
	
		$db->connect();
		$requete =  $db->query($sql);
		$db->close();

		$GmapType = "G_SATELLITE_MAP";
		
		$result['lat'] = $this->site->infos["DEF_LAT"];
		$result['lng'] = $this->site->infos["DEF_LNG"];
		$result['zoom'] = $this->site->infos["DEF_ZOOM"];
		$result['zoommax'] = $this->site->infos["DEF_ZOOM"]+4;
		$result['idType'] = $this->site->infos["MOT_CLEF_DEF_TYPE_CARTE"];
		$result['type'] = $GmapType;
		$result['adresse'] = "";		
		$result['kml'] = "";
		$result['docArtkml'] = "";
		$r =  $db->fetch_assoc($requete);
		//gestion de la localisation parente si localisation  null
		if(!$r['lat']){
			//niveau pour �viter de boucler trop longtemps
			if($g->IdParent!=0 && $niv<3){
				$result = $this->GetGeo($g->IdParent,-1,$niv+1);
			}
		}else {
			$result['lat'] = $r['lat'];
			$result['lng'] = $r['lng'];
			if($r['zoom'])
				$result['zoom'] = $r['zoom'];
			if($r['zoommax'])
				$result['zoommax'] = $r['zoommax'];
			if($r['type']==3)
				$GmapType = "G_NORMAL_MAP";
			if($r['type']==5)
				$GmapType = "G_HYBRID_MAP";
			if($r['type']==4)
				$GmapType = "G_SATELLITE_MAP";				
			$result['type'] = $GmapType;
			$result['idType'] = $r['type'];
			$result['adresse'] = $r['adresse'];		
			//lien vers le kml
			$kml="";		
			
			if($r['docArtkml'])
				$kml = $this->site->infos["pathSpip"].$r['docArtkml'];
			if($kml=="")	
				$kml = $r['kml'];
			if($kml=="")
				$kml = $this->GetKml();
			$result['kml']=$kml;
			
		}
		
		return $result;
	}
  
	function GetArticle($extraSql=""){
		//r�cup�re pour la rubrique l'article ayant les condition de extra
		$sql = "SELECT a.id_article
			FROM spip_rubriques r
				INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique  
			WHERE r.id_rubrique = ".$this->id." ".$extraSql;
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		//v�rifie l'existance de l'article pour les forms
		if ($r = $DB->fetch_assoc($req)){
			$artId = $r['id_article']; 
		} else {
			//Cr�ation de l'article pour la rubrique
			$NomGrille = $this->site->GetSQLValueString($this->titre, "text");
			$sql = "INSERT INTO `spip_articles` (`titre`, id_rubrique, statut, date)
				VALUES (".$NomGrille.",".$this->id.",'prepa', now())";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$req = $DB->query($sql);
			$artId = mysql_insert_id();
			$DB->close();
			$this->SetAuteur($artId,'article');
		}

		return $artId; 

	}
	
	/*
	 * Retourne un tableau contenant l'id de l'article, le titre, les dates de cr�ation et de mise � jour pour une rubrique
	 */
	function GetArticleInfo($extraSql="",$idRub=true){
		
		if($idRub)
			$whereRub = " WHERE a.id_rubrique = ".$this->id;
		else
			$whereRub = " WHERE 1 ";
		
		$wherePubli = " ";	
		if(isset($_SESSION['ContEditPublie']))
			if($_SESSION['ContEditPublie'])
				$wherePubli = " AND a.statut='publie' ";	
		
		//r�cup�re pour la rubrique l'article ayant les condition de extra
		$sql = "SELECT a.id_article ,a.titre, a.date, a.maj, a.statut, aa.id_auteur, au.nom
			FROM spip_articles a 
				LEFT JOIN spip_auteurs_articles aa ON aa.id_article = a.id_article 	
				LEFT JOIN spip_auteurs au ON au.id_auteur = aa.id_auteur 	
			".$whereRub." ".$wherePubli." ".$extraSql."
				";
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();

		return $req; 
			
	}
  
	/*
	 * Retourne les id de grille pour un article ou une rubrique
	 */
	function GetFormIds($idArticle,$idRub=-1) {
		
		if($idRub==-1)
			$sql = "SELECT DISTINCT fd.id_form
				FROM spip_forms_donnees_articles fa
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fa.id_donnee
				WHERE fa.id_article = ".$idArticle;
		else
			$sql = "SELECT DISTINCT fd.id_form, a.id_article, f.titre
				FROM spip_forms_donnees_articles fa
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fa.id_donnee
					INNER JOIN spip_forms f ON f.id_form = fd.id_form
					INNER JOIN spip_articles a ON a.id_article = fa.id_article
						AND a.id_rubrique = ".$idRub;
		
		//echo $sql."<br/>"; spip_forms_articles
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		return $req; 	
	}
	
	/*
	 * Retourne l'ensemble des id de donn�es d'une grille donn�e pour un article 
	 */
	function GetIdDonnees($idGrille, $idArticle) {
		
		//v�rifie si on renvoit toute les donn�es quelques soit la form
		if($idGrille==-1)
			$sql = "SELECT fd.id_donnee, fd.date, fd.maj, fd.id_form idGrille
				FROM spip_forms_donnees_articles da 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee 
				WHERE da.id_article = ".$idArticle;
		else
			$sql = "SELECT fd.id_donnee, fd.date, fd.maj
				FROM spip_forms_donnees_articles da 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee AND fd.id_form = ".$idGrille."
				WHERE da.id_article = ".$idArticle;
			
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		return $req;	
	}
	
	/*
	 * Retourne le tableau contenant l'id, le champ, la valeur et la date de mise � jour d'une donn�e
	 */
	function GetInfosDonnee($idDonnee) {
		
		$sql = "SELECT fdc.id_donnee, fdc.champ, fdc.valeur, fdc.maj
					,fc.titre
				FROM spip_forms_donnees_champs fdc
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee
					INNER JOIN spip_forms_champs fc ON fc.champ = fdc.champ AND fc.id_form = fd.id_form
				WHERE fdc.id_donnee =".$idDonnee;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		return $req;	
		
	}
	
	function GetIdDonnee($formId, $artId=-1, $doublon=false){

		if($artId==-1)
			$artId = $this->GetArticle();
		
		$donId = false;
		
		if(!$doublon){
			//v�rifie l'existence de la donnee
	  		$sql = "SELECT fd.id_donnee
				FROM spip_forms_donnees_articles da 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee AND fd.id_form = ".$formId."
			WHERE da.id_article = ".$artId;
			//echo $sql."<br/>";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$req = $DB->query($sql);
			$DB->close();
			$r = $DB->fetch_assoc($req);
			$donId= $r['id_donnee'];
		}
		
		if(!$donId){
			//attache le form � l'article
			/*
			$sql = "INSERT INTO `spip_forms_articles` (id_form, id_article)
				VALUES (".$formId.",".$artId.")";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$req = $DB->query($sql);
			$DB->close();
			*/
			//cr�ation de la donn�e du formulaire
			$sql = "INSERT INTO `spip_forms_donnees` (`id_form`, `date`,`confirmation`, `statut`, `rang`)
				VALUES (".$formId.", now(), 'valide', 'prop', 1)";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$req = $DB->query($sql);
			$donId = mysql_insert_id();
			$DB->close();
			//attache la donn�e � l'article
			$sql = "INSERT INTO `spip_forms_donnees_articles` (`id_donnee`, `id_article`)
				VALUES (".$donId.", ".$artId.")";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$req = $DB->query($sql);
			$DB->close();
			//echo "-- cr�ation de la donn�e ".$donId." \n<br/>"; 
		}

		return $donId;

	}
  
	function AddIdDonnee($formId, $artId=-1, $date, $maj) {
		
		if($artId==-1)
			$artId = $this->GetArticle();

		//attache le form � l'article
		/*$sql = "INSERT INTO `spip_forms_articles` (id_form, id_article)
				VALUES (".$formId.",".$artId.")";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();*/
			
		//cr�ation de la donn�e du formulaire
		$sql = "INSERT INTO `spip_forms_donnees` (`id_form`, `date`, `maj`, `confirmation`, `statut`, `rang`)
				VALUES (".$formId.", '".$date."', '".$maj."', 'valide', 'prop', 1)";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$donId = mysql_insert_id();
		$DB->close();
		//attache la donn�e � l'article
		$sql = "INSERT INTO `spip_forms_donnees_articles` (`id_donnee`, `id_article`)
				VALUES (".$donId.", ".$artId.")";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		//echo "-- cr�ation de la donn�e ".$donId." \n<br/>"; 
		
		return $donId;
	}
	
	function GetLiens($rReq=false){
	
		//r�cup�re les liens du granulat
		$sql = "SELECT nom_site, url_site
			FROM `spip_syndic`
			WHERE statut = 'publie' AND id_rubrique =".$this->id;
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		if($rReq)
			return $req;
		
		$valeur="";
		while($r = $DB->fetch_assoc($req)) {
			$valeur .= "<a href='".$r['url_site']."' style='cursor: pointer; cursor: hand;' target='_new' >".$r['nom_site']."</a><br/>";
		}
		
		return $valeur;
	}


	public function TronqueTexte($max_caracteres , $space='1' , $points='1',$tocut="")
	{
		if($tocut=="")
			$tocut = $this->texte;
		

		if (strlen($tocut)>$max_caracteres){
			if ($space=='1'){
				$max_caracteres=strrpos(substr($tocut, 0, $max_caracteres), " ");
			}
			$tocut = substr($tocut, 0, $max_caracteres);
			if ($points=='1'){
				$tocut.=' ...';
			}
		}
		return $tocut;
	}
		
	public function GetScope($id=-1)
	{
		if($id==-1)
			$id = $this->id;

		$Scope = $this->GetEnfantIds();
		$Scope = str_replace(DELIM,",",$Scope);
		$Scope .= $id;
		
		return $Scope;
		
	}

	public function EstParent($id)
	{
		$arrParent = split("[".DELIM."]", $this->GetParentIds());
		//echo "<br/>EstParent ".$id."<br/>";
		//print_r($arrParent);		
		return in_array($id, $arrParent);
	}

	public function GetParentIds($id = "",$sep="")
	{
		if($id =="")
			$id = $this->id;
		if($sep =="")
			$sep = DELIM;
			
		//r�cup�re les sous th�me
		$sql = "SELECT id_rubrique, titre, r.id_parent
			FROM spip_rubriques r
			WHERE r.id_rubrique = ".$id;
	
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();

		$valeur="";
		while($r = $DB->fetch_assoc($req)) {
			$valeur .= $this->GetParentIds($r['id_parent'],$sep);
			$valeur .= $r['id_rubrique'].$sep;
		}
		
		return $valeur;

	}

	public function GetEnfantIds($id = "", $sep="", $maxNiv=-1, $inCache=false, $allNiv=false)
	{
		if($id =="")
			$id = $this->id;
		if($sep=="")
			$sep=DELIM;
			
		if(!$inCache){
			//r�cup�re les sous th�me
			$sql = "SELECT id_rubrique, titre
				FROM spip_rubriques r
				WHERE r.id_parent = ".$id;
		}else{
			$sql = "SELECT id_rubrique FROM spip_rubriques_enfants 
				WHERE id_parent = ".$id;
		}
		//echo $this->site->infos["SQL_LOGIN"]."<br/>";
	
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();

		$valeur="";
		while($r = $DB->fetch_assoc($req)) {
			if($maxNiv==-1 && !$inCache){
				$valeur .= $this->GetEnfantIds($r['id_rubrique'],$sep,$maxNiv,$inCache,$allNiv);
			}
			if($allNiv){
				$g = new Granulat($r['id_rubrique'],$this->site);
				$valeur .= $g->GetIdsScope($inCache,$allNiv);
			}
			$valeur .= $r['id_rubrique'].$sep;
			if(!$inCache){
				//ajoute dans la table de cache
				$this->SetEnfantId($r['id_rubrique']);
			}
		}
		
		return $valeur;

	}

	function SetEnfantId($id)
	{
		/*v�rifie si la ligne existe
		$sql = "SELECT id_rubrique FROM spip_rubriques_enfants 
			WHERE id_parent = ".$this->id." AND id_rubrique=".$id;
		//echo $this->site->infos["SQL_LOGIN"]."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		$r = $DB->fetch_assoc($req);
		
		if(!$r){
			//creation de la ligne
			$sql = "INSERT INTO spip_rubriques_enfants 
				SET id_parent = ".$this->id.", id_rubrique=".$id;
			//echo $this->site->infos["SQL_LOGIN"]."<br/>";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$DB->query($sql);
			$DB->close();			
		}
		*/
		//creation de la ligne
		$sql = "INSERT INTO spip_rubriques_enfants 
			SET id_parent = ".$this->id.", id_rubrique=".$id;
		//echo $this->site->infos["SQL_LOGIN"]."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->query($sql);
		$DB->close();			
		
	}

	function DelEnfantId()
	{
		//creation de la ligne
		$sql = "DELETE FROM spip_rubriques_enfants 
			WHERE id_parent = ".$this->id;
		//echo $this->site->infos["SQL_LOGIN"]."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->query($sql);
		$DB->close();			
		
	}
	
	public function GetEnfantIdsInLiens($id = "", $sep="", $idMot=-1)
	{
		if($id =="")
			$id = $this->id;
		if($sep=="")
			$sep=DELIM;

		if($idMot==-1)
			$JoinMot = "";
		else
			$JoinMot = " INNER JOIN spip_mots_syndic ms ON ms.id_syndic = s.id_syndic AND ms.id_mot =".$idMot;
		
		//r�cup�re les sous th�me
		$sql = "SELECT s.id_syndic, s.descriptif
			FROM spip_syndic s
			".$JoinMot." 
			WHERE s.id_rubrique = ".$id;
		//echo $this->site->infos["SQL_LOGIN"]."<br/>";
	
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();

		$vals=array();
		while($r = $DB->fetch_assoc($req)) {
			//ajoute la rubrique en lien
			array_push ($vals, $r['descriptif']);
			$this->SetEnfantId($r['descriptif']);
			//ajoute les enfants
			$arrIds = split($sep,$this->GetEnfantIds($r['descriptif'],$sep));
			foreach($arrIds as $i){
				if($i!="")
					array_push ($vals, $i);
			}
		}
		$vals = array_unique($vals);
		return implode($sep, $vals);

	}
	
	public function GetTypeForm($idRub=-1){

		if($idRub==-1)
			$idRub = $this->id;
		
		$arrlisteGrilles = $this->GetFormIds(-1,$idRub);
		if(mysql_num_rows($arrlisteGrilles)>0){
			while($rowGrille = mysql_fetch_assoc($arrlisteGrilles)) {
				//r�cup�ration du js
				$Xpath = "/XmlParams/XmlParam[@nom='FilAriane']/fil[@idForm='".$rowGrille['id_form']."']";
				$nodes = $this->site->XmlParam->GetElements($Xpath);		
				foreach($nodes as $node)
				{
					return $node;		
				}
			}
		}else{
			return "";
		}
	}
	
	
	public function GetProps()
	{
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		if($this->trace)
			echo "//charge les propi�t�s du granulat $this->id -<br/>";
		$sql = "SELECT r.titre rtitre, r.id_rubrique, r.descriptif, r.texte, r.id_parent rpid
				, rp.titre rptitre
				, a.texte atexte, a.chapo , a.descriptif adesc, a.ps, a.extra, a.date
			FROM spip_rubriques r
				LEFT JOIN spip_articles a ON a.id_rubrique = r.id_rubrique AND a.statut = 'publie'
				LEFT JOIN spip_rubriques rp ON rp.id_rubrique = r.id_parent
			WHERE r.id_rubrique = ".$this->id
			." ORDER BY a.date DESC";
		//echo $sql."<br/>";
		$req = $DB->query($sql);
		$DB->close();
		$data = $DB->fetch_assoc($req);
		$this->titre = $data['rtitre'];
		$this->descriptif = $data['descriptif'];
		$this->texte = $data['texte'];
		$this->localisation = "";
		$this->TitreParent = $data['rptitre'];
		$this->IdParent = $data['rpid'];
		$this->adesc= $data['adesc'];
		$this->adate= $data['date'];
		$this->atexte= $data['atexte'];
		$this->achapo= $data['chapo'];
		$this->ps= $data['ps'];		

	}
	
	function SetGeoRef($lat,$lon,$coors){
		
		$sql = "DELETE FROM ona_geo
			WHERE id_rubrique = ".$this->id;
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		$DB->query($sql);
		$DB->close();

		$sql = "INSERT INTO ona_geo (id_rubrique, lat , lon, coors, name)
			VALUES('".$this->id."', '".$lat."', '".$lon."', '".$coors."', \"".$this->titre."\")"; 
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		$DB->query($sql);
		$DB->close();
		
	}
	
	function SetGeoStat($idInd,$year,$val){
		
		//v�rifie si la GeoRef existe
		$sql = "DELETE FROM ona_indicator_values 
			WHERE area = ".$this->id." AND variable=".$idInd." AND year=".$year ;
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
	
		$sql = "INSERT INTO ona_indicator_values (variable, area , year, value)
			VALUES('".$idInd."', '".$this->id."', '".$year."', '".$val."')"; 
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		$DB->query($sql);
		$DB->close();
		
	}
		
	
	
	public function GetValeurForm($form, $titre, $valdefaut="", $sep="", $deb="", $id=-1, $champ=-1)
	{
		if($id==-1)
			$id=$this->id;

		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
	
		//r�cup�re la valeur de la donn�e
		if($champ==-1){
			$sql = "SELECT dc.valeur
				FROM spip_articles a
					INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
					INNER JOIN spip_forms_donnees_champs dc ON dc.id_donnee = da.id_donnee
					INNER JOIN spip_forms_champs fc ON fc.champ = dc.champ
				WHERE a.id_rubrique =".$id."
					AND fd.id_form =".$form."
					AND fc.titre ='".$titre."'";
		}else{
			$sql = "SELECT dc.valeur
				FROM spip_articles a
					INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
					INNER JOIN spip_forms_donnees_champs dc ON dc.id_donnee = da.id_donnee
				WHERE a.id_rubrique =".$id."
					AND fd.id_form =".$form."
					AND dc.champ ='".$champ."'";
		}
		//echo $this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";

		$req = $DB->query($sql);
		$DB->close();

		$valeur=$valdefaut;
		while($r = $DB->fetch_assoc($req)) {
			if($r['valeur']!="")
				$valeur=$deb.$r['valeur'].$sep;
		}
		
		return $valeur;
	}

	public function GetGrille($IdGrille, $ExtraSql="", $idRub=-1)
	{
		if($idRub==-1)
			$idRub=$this->id;
		
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
	
		//r�cup�re les sous th�me
		$sql = "SELECT dc.valeur, dc.champ, da.id_donnee, fc.titre
			FROM spip_articles a
				INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
				INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee AND fd.id_form =".$IdGrille."
				INNER JOIN spip_forms_donnees_champs dc ON dc.id_donnee = da.id_donnee
				INNER JOIN spip_forms_champs fc ON fc.champ = dc.champ AND fc.id_form =".$IdGrille."
			WHERE a.id_rubrique =".$idRub.$ExtraSql."
			ORDER BY da.id_donnee, fc.rang, dc.champ";
		//echo $this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
	
		$req = $DB->query($sql);
		$DB->close();

		return $req;
	}
	
	public function GetDocs($id=-1,$type=-1)
	{
		if($id==-1)
			$id=$this->id;
			
		if($type==-1)
			$whereType = "";
		else
			$whereType = " AND d.id_type IN (".$type.") ";
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		//charge les documents du granulat au niveau de la rubrique
		$sql = "SELECT r.titre rtitre, r.id_rubrique, r.descriptif
				, d.fichier, d.hauteur, d.largeur, d.id_document, d.id_type, d.titre dtitre
			FROM spip_rubriques r
				INNER JOIN spip_documents_rubriques dr ON dr.id_rubrique = r.id_rubrique
				INNER JOIN spip_documents d ON d.id_document = dr.id_document
			WHERE r.id_rubrique = ".$id.$whereType
			." ORDER by d.id_type";
		//au niveau des articles
		$sql = "SELECT a.titre rtitre, a.id_rubrique, a.descriptif
				, d.fichier, d.hauteur, d.largeur, d.id_document, d.id_type, d.titre dtitre
			FROM spip_articles a
				INNER JOIN spip_documents_articles da ON da.id_article = a.id_article
				INNER JOIN spip_documents d ON d.id_document = da.id_document
			WHERE a.id_rubrique = ".$id.$whereType
			." ORDER by d.id_type";
			$req = $DB->query($sql);
		$DB->close();
		$i = 0;
		$this->arrDoc = array(); 
		while($data = $DB->fetch_assoc($req)) {
			$this->arrDoc[$i] = new Document($this->site, $data);
			//v�rifie s'il y a des fichiers multim�dia
			$i ++;
		}
		return $this->arrDoc;
	}
	
	public function VerifMultiMedia($arrDoc){
		if(count($arrDoc)<=0)
			$verif = false;
		else{
			foreach($arrDoc as $doc){
				if($doc->type==1 || $doc->type==10 || $doc->type==14)
					$verif = true;
			}
		}
		return $verif;
	}
	
	public function GetArtDocs($idArt)
	{
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		//charge les documents du granulat
		$sql = "SELECT d.fichier, d.hauteur, d.largeur, d.id_document, d.id_type, d.titre dtitre
			FROM spip_documents_articles da 
				INNER JOIN spip_documents d ON d.id_document = da.id_document
			WHERE da.id_article = ".$idArt
			." ORDER by d.id_type";
		$req = $DB->query($sql);
		$DB->close();
		$i = 0;
		$arrDoc=array();
		while($data = $DB->fetch_assoc($req)) {
			$arrDoc[$i] = new Document($this->site, $data);
			$i ++;
		}
		return $arrDoc;
	}
	
	public function GetEnfants($complet=true, $id=-1)
	{
		if($id==-1)$id=$this->id;
		
		$sql = "SELECT id_rubrique, titre
			FROM spip_rubriques
			WHERE id_parent = ".$id
			." ORDER BY titre";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		$i = 0;
		$arrliste=false;
		while($data = $DB->fetch_assoc($req)) {
			$arrliste[$i] = new Granulat($data['id_rubrique'], $this->site,$complet);
			$i ++;
		}
		return $arrliste;
	}

	/*
	 * Retourne un tableau des enfants d'une rubrique contenant l'id, le titre et le descriptif des rubriques
	 */
	public function GetListeEnfants()
	{
		$sql = "SELECT id_rubrique, titre, descriptif
			FROM spip_rubriques
			WHERE id_parent = ".$this->id
			." ORDER BY titre";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		$req = $DB->query($sql);
		$i = 0;
		$DB->close();
		$arrliste = array();
		while($data = $DB->fetch_assoc($req)) {
			$arrliste[$i] = array("id"=>$data['id_rubrique'], "titre"=>$data['titre'], "descriptif"=>$data['descriptif']);
			$i ++;
		}
	
		return $arrliste;
	}

	public function GetImages($Largeur, $Hauteur, $vignette=false)
	{
		$GranulatTofs = "";
		$FicLastTof = "";
		if($this->arrDoc){ 
			foreach ($this->arrDoc as $Doc) {
				//print_r($Doc);
				if($Doc->type == 10 && !$vignette){
					//if($FicLastTof=="")
					//	$FicLastTof="http://91.121.20.191/new/img/LogoCRMorbihan.jpg";
					$GranulatTofs = "<object type='application/x-shockwave-flash' 
							data='admin/includes/player_flv.swf' 
							width='".$Largeur."' height='".$Hauteur."' >
						<param name='movie' value='".$_SERVER["DOCUMENT_ROOT"]."/new/admin/includes/player_flv.swf'>
						<param name='FlashVars' value='flv=".$Doc->fichier."&amp;width=".$Largeur."&amp;height=".$Hauteur."&amp;bgcolor1=ffffff&amp;bgcolor2=ffffff&amp;buttoncolor=999999&amp;buttonovercolor=0&amp;slidercolor1=cccccc&amp;slidercolor2=999999&amp;sliderovercolor=666666&amp;textcolor=0&amp;showstop=1&amp;title=&amp;startimage=".$FicLastTof."'>
						<param name='wmode' value='opaque'>
						<span><a href='".$Doc->fichier."' rel='enclosure'>".$Doc->fichier."</a></span>
					</object>";
				}else{
					if($vignette)
						$GranulatTofs .= $Doc->GetVignette($Largeur, $Hauteur);
					else
						$GranulatTofs .= $Doc->DimensionImage($Largeur, $Hauteur);
					$FicLastTof = $Doc->fichier;
				}
			}
		}
		return $GranulatTofs;
	}

	public function GetMotClef() {
		//r�cup�re lid du granulat
		$sql = "SELECT id_mot, id_rubrique
			FROM `spip_mots_rubriques`
			WHERE id_rubrique =".$this->id;
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
				
		$valeur="";
		while($r = $DB->fetch_assoc($req)) {
			$valeur .= $r['id_mot'].DELIM;
		}
		//enl�ve le dernier d�lmiteur
		$valeur = substr($valeur,0,-1);
		return $valeur;
	}

	public function GetTypeMotClef($type,$id=-1) {
		if($id==-1)
			$id=$this->id;
		//r�cup�re lid du granulat
		$sql = "SELECT id_mot
			FROM spip_mots_".$type."s
			WHERE id_".$type." =".$id;
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
				
		$valeur=array();
		$i=0;
		while($r = $DB->fetch_assoc($req)) {
			$m = new MotClef($r['id_mot'],$this->site);
			$valeur[$i] = $m;
		}
		return $valeur;
}
	
	public function GetTypeAuteur($type,$id=-1) {
		if($id==-1)
			$id=$this->id;
		//r�cup�re lid du granulat
		$sql = "SELECT a.id_auteur, a.nom, a.login
			FROM spip_auteurs a 
				INNER JOIN spip_auteurs_".$type."s at ON at.id_auteur = a.id_auteur
			WHERE at.id_".$type." =".$id;
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
				
		return $req;
}

	public function GetParent($id = "") {
		
		if($id =="")
			$id = $this->id;
			
		//r�cup�re les sous th�me
		$sql = "SELECT id_rubrique, titre, r.id_parent
			FROM spip_rubriques r
			WHERE r.id_rubrique = ".$id;
	
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		if($r = $DB->fetch_assoc($req)) {
			return $r['id_parent'];
		} else return -1;
	}
	
	public function CopyRub($idParent) {
		
		//$idParent = $this->GetParent($this->id);
		if($this->trace) echo "Granulat/copy/- idParent ".$idParent."<br/>";
		$arrListeEnfants = $this->GetEnfants();
		//$idArticle = $this->GetArticle();
		//if($this->trace) echo "Granulat/copy/- arrListeEnfants ".print_r($arrListeEnfants)."<br/>";
		
		$motclef = $this->GetMotClef();
		
		$arrListeInfoArticle = $this->GetArticleInfo();
		
		$g = new Granulat($idParent, $this->site);
		$idEnfant = $g->SetNewEnfant($this->titre);
		$gra = new Granulat($idEnfant, $this->site);
		$gra->descriptif = $this->descriptif;
		$gra->texte = $this->texte;
		if ($motclef!="") $gra->SetMotClef($motclef);
		
		$grille = new Grille($gra->site);
		
		while($article = mysql_fetch_assoc($arrListeInfoArticle)) {
			$idArt = $gra->SetNewArticleComplet($article['titre'], $article['date'], $article['maj']);
			$arrListeDonnees = $gra->GetIdDonnees(-1, $article['id_article']);
			while($donnee = mysql_fetch_assoc($arrListeDonnees)){
	  			$idDon = $gra->AddIdDonnee($donnee['idGrille'], $idArt, $donnee['date'], $donnee['maj']);
				if($this->trace)
					echo "Granulat/copy/- cr�ation de la donnee ".$idDon."<br/>";	
	  			
				$arrListeDonneeInfos = $gra->GetInfosDonnee($donnee['id_donnee']);
				while($Donnee = mysql_fetch_assoc($arrListeDonneeInfos)){
					if($Donnee['valeur']!='non'){
						$valeur=$Donnee['valeur'];
						$champ = $Donnee['champ'];
						if($this->trace)
							echo "Granulat/copy/--- gestion des champs multiples ".substr($champ,0,8)."<br/>";
						if(substr($champ,0,8)=="multiple"){
							$valeur=$champ;
						//attention il ne doit pas y avoir plus de 10 choix
							$champ=substr($champ,0,-2);
						}
						if($this->trace) {
							echo "Granulat/copy/-- r�cup�ration du type de champ ".$champ."<br/>";
							echo "Granulat/copy/-- r�cup�ration de la valeur du champ ".$valeur."<br/>";
						}
						$row = array('champ'=>$champ, 'valeur'=>$valeur);
						
						if($this->trace)
							echo "Granulat/copy/--- cr�ation du champ <br/>";
						$grille->SetChamp($row, $idDon, false);
					}
				}
			}
		}	
		
		if ($arrListeEnfants != null) {
			foreach($arrListeEnfants as $granulat) {
				$granulat->CopyRub($idEnfant);
			}
		}		
	}

	public function CopyRubToSite($idParent,$idParentDst,$siteDst) {
		
		//$idParent = $this->GetParent($this->id);
		if($this->trace) echo "Granulat/copy/- idParent ".$idParent."<br/>";
		$arrListeEnfants = $this->GetEnfants();
		//$idArticle = $this->GetArticle();
		//if($this->trace) echo "Granulat/copy/- arrListeEnfants ".print_r($arrListeEnfants)."<br/>";
		
		$motclef = $this->GetMotClef();
		
		$arrListeInfoArticle = $this->GetArticleInfo();
		
		$gSrc = new Granulat($idParent, $this->site);
		
		$gDst = new Granulat($idParentDst, $siteDst);
		$idEnfant = $gDst->SetNewEnfant($this->titre);
		$graNew = new Granulat($idEnfant, $siteDst);
		$graNew->descriptif = $this->descriptif;
		$graNew->texte = $this->texte;
		if ($motclef!="") $graNew->SetMotClef($motclef);
		
		$grille = new Grille($graNew->site);
		
		while($article = mysql_fetch_assoc($arrListeInfoArticle)) {
			$idArt = $graNew->SetNewArticleComplet($article['titre'], $article['date'], $article['maj']);
	  		//r�cup�re les donn�es du granulat source
			$arrListeDonnees = $gSrc->GetIdDonnees(-1, $article['id_article']);
			while($donnee = mysql_fetch_assoc($arrListeDonnees)){
	  			$idDon = $graNew->AddIdDonnee($donnee['idGrille'], $idArt, $donnee['date'], $donnee['maj']);
				if($this->trace)
					echo "Granulat/copy/- cr�ation de la donnee ".$idDon."<br/>";	
	  			//r�cup�re les valeurs de la donnee source
				$arrListeDonneeInfos = $gSrc->GetInfosDonnee($donnee['id_donnee']);
				while($Donnee = mysql_fetch_assoc($arrListeDonneeInfos)){
					if($Donnee['valeur']!='non'){
						$valeur=$Donnee['valeur'];
						$champ = $Donnee['champ'];
						if($this->trace)
							echo "Granulat/copy/--- gestion des champs multiples ".substr($champ,0,8)."<br/>";
						if(substr($champ,0,8)=="multiple"){
							$valeur=$champ;
						//attention il ne doit pas y avoir plus de 10 choix
							$champ=substr($champ,0,-2);
						}
						if($this->trace) {
							echo "Granulat/copy/-- r�cup�ration du type de champ ".$champ."<br/>";
							echo "Granulat/copy/-- r�cup�ration de la valeur du champ ".$valeur."<br/>";
						}
						$row = array('champ'=>$champ, 'valeur'=>$valeur);
						
						if($this->trace)
							echo "Granulat/copy/--- cr�ation du champ <br/>";
						$grille->SetChamp($row, $idDon, false);
					}
				}
			}
		}	
		
		if ($arrListeEnfants != null) {
			foreach($arrListeEnfants as $granulat) {
				$granulat->CopyRubToSite($gSrc->id,$idEnfant,$siteDst);
			}
		}		
	}
	
}

?>
