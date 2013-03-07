<?php
class Stat{

  private $site;

  	function __construct($site) {
	    $this->site = $site;
	}
	
	function getXmlStat($type, $idArt, $idGrille){

		$path = PathRoot."/bdd/carto/Stat_".$this->site->id."_".$idGrille."_".$idArt."_".$type.".xml";
	
		$xml = $this->site->GetFile($path);
		if(!$xml){
			switch ($type) {
				case 'socio':
					$xml = $this->getSocio($idArt, $idGrille);
					break;
				case 'tranche':
					$xml =$this->getTranche($idArt, $idGrille);
					break;
				case 'CompoFamille':
					$xml =$this->getCompoFamille($idArt, $idGrille);
					break;
				case 'TypeLog':
					$xml =$this->getTypeLog($idArt, $idGrille);
					break;
			}	
			$this->site->SaveFile($path, utf8_encode($xml));
		}
		return $xml;	
	}
	
	function getSocio($idArt, $idGrille){
		
		if($idGrille == 87){
			//récupère l'identifiant de l'antenne
			$ref = $this->getRefAntenne($idArt);		
				
			$sql = "SELECT fdc.valeur lib, count( DISTINCT r.id_rubrique) nb
				FROM `spip_forms_donnees_champs` fdc 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee AND fd.id_form = 85
					INNER JOIN spip_forms_donnees_articles fda ON fda.id_donnee = fdc.id_donnee 
					INNER JOIN spip_articles a ON a.id_article = fda.id_article 
					INNER JOIN spip_rubriques r ON r.id_rubrique = a.id_rubrique 
					INNER JOIN spip_rubriques rp ON rp.id_rubrique = r.id_parent
					INNER JOIN spip_rubriques rpp ON rpp.id_rubrique = rp.id_parent AND rpp.id_rubrique
					INNER JOIN spip_articles rppa ON rppa.id_rubrique = rpp.id_rubrique 
					INNER JOIN spip_forms_donnees_articles fdarpp ON fdarpp.id_article = rppa.id_article 
					INNER JOIN spip_forms_donnees fdrpp ON fdrpp.id_donnee = fdarpp.id_donnee AND fdrpp.id_form = 82
					INNER JOIN spip_forms_donnees_champs fdcrpp ON fdcrpp.id_donnee = fdrpp.id_donnee 
						AND fdcrpp.champ = 'ligne_2' AND fdcrpp.valeur = '".$ref."'    
				WHERE fdc.champ = 'ligne_30'
				GROUP BY fdc.valeur
			";
		}

		if($idGrille == 82){
			$sql = "SELECT fdc.valeur lib, count( DISTINCT r.id_rubrique) nb
				FROM `spip_forms_donnees_champs` fdc 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee AND fd.id_form = 85
					INNER JOIN spip_forms_donnees_articles fda ON fda.id_donnee = fdc.id_donnee 
					INNER JOIN spip_articles a ON a.id_article = fda.id_article 
					INNER JOIN spip_rubriques r ON r.id_rubrique = a.id_rubrique 
					INNER JOIN spip_rubriques rp ON rp.id_rubrique = r.id_parent
					INNER JOIN spip_rubriques rpp ON rpp.id_rubrique = rp.id_parent 
					INNER JOIN spip_articles rppa ON rppa.id_rubrique = rpp.id_rubrique 
						AND rppa.id_article = ".$idArt."
				WHERE `champ` = 'ligne_30'
				GROUP BY fdc.valeur"; 
		}
	
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		return $this->getXml($req);
    }

	function getTranche($idArt, $idGrille){
		
		if($idGrille == 87){
			//récupère l'identifiant de l'antenne
			$ref = $this->getRefAntenne($idArt);		
		
			$sql = "SELECT fdc.valeur lib, count( DISTINCT r.id_rubrique) nb
				FROM `spip_forms_donnees_champs` fdc 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee AND fd.id_form = 82
					INNER JOIN spip_forms_donnees_articles fda ON fda.id_donnee = fdc.id_donnee 
					INNER JOIN spip_articles a ON a.id_article = fda.id_article 
					INNER JOIN spip_rubriques r ON r.id_rubrique = a.id_rubrique 
					INNER JOIN spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee 
						AND fdc1.champ = 'ligne_2' AND fdc1.valeur = '".$ref."'    
				WHERE fdc.champ = 'ligne_6'
				GROUP BY fdc.valeur    
			";
		}

		if($idGrille == 82){
			$sql = "SELECT fdc.valeur lib, count( DISTINCT a.id_rubrique) nb
				FROM `spip_forms_donnees_champs` fdc 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee AND fd.id_form = 82
					INNER JOIN spip_forms_donnees_articles fda ON fda.id_donnee = fdc.id_donnee 
					INNER JOIN spip_articles a ON a.id_article = fda.id_article 
						AND a.id_article = ".$idArt."
					WHERE fdc.champ = 'ligne_6'
				GROUP BY fdc.valeur
			"; 
		}
	
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		return $this->getXml($req);
    }

	function getTypeLog($idArt, $idGrille){

		if($idGrille == 87){
			//récupère l'identifiant de l'antenne
			$ref = $this->getRefAntenne($idArt);		
			
			$sql = "SELECT fc.titre lib, SUM(fdc.valeur) nb
				FROM `spip_forms_donnees_champs` fdc 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee AND fd.id_form = 82
					INNER JOIN spip_forms_champs fc ON fc.champ = fdc.champ AND fc.id_form = fd.id_form
				    	AND fc.champ IN ('ligne_20','ligne_21','ligne_22','ligne_23','ligne_24','ligne_25','ligne_26','ligne_27')
					INNER JOIN spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee 
						AND fdc1.champ = 'ligne_2' AND fdc1.valeur = '".$ref."'   
				GROUP BY fc.titre
				HAVING nb > 0";
		}

		if($idGrille == 82){
			$sql = "SELECT fc.titre lib, SUM(fdc.valeur) nb
				FROM `spip_forms_donnees_champs` fdc 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee AND fd.id_form = 82
					INNER JOIN spip_forms_donnees_articles fda ON fda.id_donnee = fdc.id_donnee 
					INNER JOIN spip_articles a ON a.id_article = fda.id_article 
					  AND a.id_article = ".$idArt."
					INNER JOIN spip_forms_champs fc ON fc.champ = fdc.champ AND fc.id_form = fd.id_form
				WHERE fdc.champ IN ('ligne_20','ligne_21','ligne_22','ligne_23','ligne_24','ligne_25','ligne_26','ligne_27')
				GROUP BY fc.titre
				HAVING nb > 0"; 
		}
	
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		return $this->getXml($req);
    }
    
    
    function getCompoFamille($idArt, $idGrille){
		
		if($idGrille == 87){
			//récupère l'identifiant de l'antenne
			$ref = $this->getRefAntenne($idArt);		
			
			$sql = "SELECT fc.titre, fdc.valeur lib, count(DISTINCT r.id_rubrique) nb
				FROM spip_forms_donnees_champs fdc 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee AND fd.id_form = 85
					INNER JOIN spip_forms_donnees_articles fda ON fda.id_donnee = fdc.id_donnee 
					INNER JOIN spip_articles a ON a.id_article = fda.id_article 
					INNER JOIN spip_rubriques r ON r.id_rubrique = a.id_rubrique 
					INNER JOIN spip_rubriques rp ON rp.id_rubrique = r.id_parent
					INNER JOIN spip_rubriques rpp ON rpp.id_rubrique = rp.id_parent AND rpp.id_rubrique
					INNER JOIN spip_articles rppa ON rppa.id_rubrique = rpp.id_rubrique 
					INNER JOIN spip_forms_donnees_articles fdarpp ON fdarpp.id_article = rppa.id_article  
					INNER JOIN spip_forms_donnees fdrpp ON fdrpp.id_donnee = fdarpp.id_donnee AND fdrpp.id_form = 82
					INNER JOIN spip_forms_donnees_champs fdcrpp ON fdcrpp.id_donnee = fdrpp.id_donnee 
						AND fdcrpp.champ = 'ligne_2' AND fdcrpp.valeur = '".$ref."'    
					INNER JOIN spip_forms_champs fc ON fc.champ = fdc.champ AND fc.id_form = fd.id_form
				WHERE fdc.champ IN ('ligne_34','ligne_35')
				GROUP BY fc.titre, fdc.valeur;";
		}

		if($idGrille == 82){
			$sql = "SELECT fc.titre, fdc.valeur lib, count(DISTINCT r.id_rubrique) nb
				FROM spip_forms_donnees_champs fdc 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee AND fd.id_form = 85
					INNER JOIN spip_forms_donnees_articles fda ON fda.id_donnee = fdc.id_donnee 
					INNER JOIN spip_articles a ON a.id_article = fda.id_article 
					INNER JOIN spip_rubriques r ON r.id_rubrique = a.id_rubrique 
					INNER JOIN spip_rubriques rp ON rp.id_rubrique = r.id_parent
					INNER JOIN spip_rubriques rpp ON rpp.id_rubrique = rp.id_parent 
					INNER JOIN spip_articles rppa ON rppa.id_rubrique = rpp.id_rubrique 
						AND rppa.id_article = ".$idArt." 
					INNER JOIN spip_forms_champs fc ON fc.champ = fdc.champ AND fc.id_form = fd.id_form
				WHERE fdc.champ IN ('ligne_34','ligne_35')
				GROUP BY fc.titre, fdc.valeur"; 
		}
	
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
				//construction du XML
		$xml = "<dataset>";
		$xml1 = "<SITFAM1>";
		$xml2 = "<SITFAM2>";
		$axes = "";
		$arrAxe = array();
		while($r = mysql_fetch_assoc($req)) {
			if($r["lib"]!=""){
				$nb = $r["nb"];
				if($r["titre"]=="SITFAM1"){
					$xml1 .= "<".$r["lib"].">".$r["nb"]."</".$r["lib"].">";
				}else{
					$xml2 .= "<".$r["lib"].">".$r["nb"]."</".$r["lib"].">";
				}
				if(!in_array($r["lib"],$arrAxe)){
					$arrAxe[] = $r["lib"];
					$axes .= "<axe>".$r["lib"]."</axe>";
				}			
			}
		}
		$xml1 .= "</SITFAM1>";
		$xml2 .= "</SITFAM2>";
		$xml .= $xml1.$xml2.$axes."</dataset>";
		
		
		return $xml;    
   
    }
    
    function getRefAntenne($idArt){
		
		//récupère l'identifiant de l'antenne
		$sql = "SELECT fdc.valeur
			FROM spip_forms_donnees_champs fdc 
				INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee AND fd.id_form = 87
				INNER JOIN spip_forms_donnees_articles fda ON fda.id_donnee = fdc.id_donnee AND fda.id_article = ".$idArt."
			WHERE fdc.champ = 'ligne_1'
		";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		$r = mysql_fetch_assoc($req);		

		return $r["valeur"];
    }
    
    function getXml($req, $axe=false){
		//construction du XML
		$xml = "<dataset>";
		$axes = "";
		$arrAxe = array();
		while($r = mysql_fetch_assoc($req)) {
			$xml .= "<item>";
			foreach($r as $c=>$v){
				$xml .= "<".$c.">".$v."</".$c.">";			
			}
			if($axe){
				if(!in_array($r["lib"],$arrAxe)){
					$arrAxe[] = $r["lib"];
					$axes .= "<axe>".$r["lib"]."</axe>";
				}			
			}
			$xml .= "</item>";				
		}
		$xml .= $axes."</dataset>";
		return $xml;
    }
}
?>