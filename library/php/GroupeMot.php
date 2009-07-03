<?php

class GroupeMot
{
  public $id;
  public $titre;
  public $descriptif;
  public $texte;
  public $id_parent;
  public $titre_parent;
  private $site;

  function __tostring() {
    return "Cette classe permet de définir et manipuler un groupe de mot : .<br/>";
    }

  function __construct($id, $site) {

    //echo "$id, $site login=".$site->infos["SQL_LOGIN"]."<br/>";
	$this->id = $id;
    $this->site = $site;
	$this->GetProps();
}

	public function GetProps()
	{
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		//charge les propiétés du mot
		$sql = "SELECT gm.id_parent, gm.titre, gm.texte, gm.descriptif, gmp.titre titre_parent
			FROM spip_groupes_mots gm
				LEFT JOIN spip_groupes_mots gmp ON gmp.id_groupe = gm.id_parent
			WHERE gm.id_groupe = ".$this->id;
		$req = $DB->query($sql);
		$DB->close();
		$data = $DB->fetch_assoc($req);
		$this->titre = $data['titre'];
		$this->descriptif = $data['descriptif'];
		$this->texte = $data['texte'];
		//le groupe mot racine est son propre parent
		if($data['id_parent']==0)
			$this->id_parent = $this->id;
		else
			$this->id_parent = $data['id_parent'];
		$this->titre_parent = $data['titre_parent'];
	}

	public function GetFilAriane($id=-1, $url)
	{
		if($id==-1)
			$id=$this->id;

		//récupère les articles
		$sql = "SELECT id_parent, titre
			FROM spip_groupes_mots gm
			WHERE gm.id_groupe = ".$id;
		//echo $sql."<br/>";
		
		$db = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
	
		$db->connect();
		$requete = $db->query($sql);
		$db->close();
		$valeur = "";
		while ($r = $db->fetch_assoc($requete)) {
			//gestion de la page du milieu à afficher
			if($id==$this->site->infos["GRP_TOPIC"])
				$VoirEn = "IntroTheme";
			else
				$VoirEn = "ListeTheme";
			//calcul du lien
			$lien =  $this->site->GetLien($url
				, array("GrpMot","VoirEn")
				, array($id,$VoirEn)
				, array("Mot")
				);
			$valeur .= $this->GetFilAriane($r['id_parent'], $url);
			$valeur .= "<a href='".$lien."'>".$r['titre']."</a> / ";
		}
	
		return $valeur;

	}


	public function EstParent($id)
	{
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		//charge les propiétés du mot
		$sql = "SELECT id_parent
			FROM spip_groupes_mots gm
			WHERE gm.id_groupe = ".$id;
		$req = $DB->query($sql);
		$DB->close();
		$data = $DB->fetch_assoc($req);
		if($this->id==$data['id_parent'])
			$val = true;
		else
			$val = false;
		
		return $val;
	}

	public function GetNb($ScopeTopos ="")
	{

		$Where = " ";
		if($ScopeTopos!="")
			$WhereRub = " AND mr.id_rubrique IN(".$ScopeTopos.")";
	
		//récupère les enfants du groupe de mot
		$WhereGroupe = " WHERE m.id_groupe IN (".$this->GetEnfants().$this->id.") ";
		
		//récupère les sous thème
		$sql = "SELECT COUNT(*) nb
			FROM spip_mots m
				INNER JOIN spip_mots_rubriques mr ON mr.id_mot = m.id_mot
			".$WhereGroupe.$WhereRub;
		//echo $sql."<br/>";

		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		$r = $DB->fetch_assoc($req);
		return $r['nb'];
	}

	public function GetListeLienMots($bloc)
	{

		//récupère les mot du groupe de mot
		$sql = "SELECT id_mot, titre
			FROM spip_mots m
			WHERE id_groupe = ".$this->id
			." ORDER BY  titre";
		//echo $sql."<br/>";

		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();

		$valeur = "";
		while ($r = $DB->fetch_assoc($requete)) {
			$lien = $this->site->GetLien("themes.php?"
				, array("GrpMot","Mot","VoirEn")
				, array($this->id,$r['id_mot'],"Mot")
				, array("Rub","PageCourante","RubSelect","PasCourant","SiteSelect")
				);
			$nbmot = $bloc->GetSiteNbTopic($this->site, -1, $r['id_mot']);
			if($nbmot>0)
				//$valeur .= "<a href='".$lien."'>".$r['titre']." (".$nbmot.")</a><br>";
				$valeur .= "<a href='".$lien."'>".$r['titre']."</a><br>";
		}
		return $valeur;
	}

	public function GetTagCloudMots($bloc)
	{

		//récupère les mot du groupe de mot
		$sql = "SELECT id_mot, titre
			FROM spip_mots m
			WHERE id_groupe = ".$this->id
			." ORDER BY  titre";
		//echo $sql."<br/>";

		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();

		//Calcul les intervalles de taille
		$Max = 0;
		$Min = 1;
		$Tot = 1;
		$i = 0;
		$nb = mysql_num_rows($req);
		while ($r = $DB->fetch_assoc($requete)) {
			$lien = $bloc->GetLien("themes.php?"
				, array("GrpMot","Mot","VoirEn")
				, array($this->id,$r['id_mot'],"Mot")
				, array("Rub","PageCourante","RubSelect","PasCourant","SiteSelect")
				);
			$nbmot = $bloc->GetSiteNbTopic($this->site, -1, $r['id_mot']);
			if($nbmot>0){
				$Tot += $nbmot;
				if($Max < $nbmot){
					$Max = $nbmot;
				}
				$valeurs[$i] = array('id_mot'=>$r['id_mot'],'nb'=>$nbmot,'titre'=>$r['titre'],'lien'=>$lien);
				$i++;
			}
		}
		
	
		if($nb > 0) {
			$IntVals[0] = ($Max-$Min)/3;
			$IntVals[1] = ($Max-$Min)/1.5;
			//echo $Max.", ".$Min.", ".$Tot.", ".$nb.", ".$IntVals[0].", ".$IntVals[1];
			//création des liens
			$iMot = 0;
			//$prems = true;
			foreach($valeurs as  $val)
			{
				//récupère la classe du tag
				$class = $this->GetTagCloudClass($val['nb'],"",$Max,$Min,$IntVals);
				
				/*
				$js = " ";
				if($pop!=3) {
					//gestion du lien lumineux
					$js = " onMouseOver=\"ChangeClass('tm".$r['pgmid']."_".$r['gmid']."','BlocSujetsBlocLiensHover','mot'); ChangeClass('tm".$r['pgmid']."_".$r['gmid']."_".$iMot."','".$class."Over','mot'); \" ";
					$js .= " onMouseOut=\"ChangeClass('tm".$r['pgmid']."_".$r['gmid']."','BlocSujetsBlocLiens','mot'); ChangeClass('tm".$r['pgmid']."_".$r['gmid']."_".$iMot."','".$class."','mot'); \" ";
				}
				$jsMaxMot = "<script language='JavaScript'>	maxMot++; </script>";

				//gestion de la fenêtre popup
				//onclick=\"opener.location='".$lien."';\" 
				if($pop==1)
					$href = "<a href='#' onclick=\"ChangeOpener('".$lien."','','');\" title='Visitez le Topic' id='tm".$r['pgmid']."_".$r['gmid']."_".$iMot."' class='".$class."' ".$js." >".$r['titre']."</a> ";
				else
					$href = "<a href='".$lien."' id='tm".$r['pgmid']."_".$r['gmid']."_".$iMot."' class='".$class."' ".$js." >".$r['titre']."</a> ";
				*/
				$href = "<a href='".$val['lien']."' class='".$class."' >".$val['titre']."</a> ";

				$liste .= $jsMaxMot.$href;
				
				$iMot ++;
			}
		}
		return $liste;	
	}

	function GetTagCloudClass($nb,$groupe,$Max,$Min,$IntVals) {

		$class = "";

		if ($nb <= $Min) {
		   $class = "smallestTag".$groupe;
		} elseif ($nb > $Min and $nb <= $IntVals[0]) {
		   $class = "smallTag".$groupe;
		} elseif ($nb > $IntVals[0] and $nb <= $IntVals[1]) {
		   $class = "mediumTag".$groupe;
		} elseif ($nb > $IntVals[1] and $nb < $Max) {
		   $class = "largeTag".$groupe;
		} elseif ($nb >= $Max) {
		   $class = "largestTag".$groupe;
		}

		return $class;
	}
	
	public function GetEnfants($id=-1)
	{

		if($id==-1)
			$id = $this->id;

		//récupère les enfants du groupe de mots
		$sql = "SELECT id_groupe, titre
			FROM spip_groupes_mots
			WHERE id_parent=".$id
			." ORDER BY titre";
		//echo $sql."<br/>";

		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		$valeur = "";
		while ($r = $DB->fetch_assoc($requete)) {
			$valeur .=  $this->GetEnfants($r['id_groupe']);
			$valeur .= $r['id_groupe'].",";
		}
	
		return $valeur;
	}


	public function GetNbParent()
	{
	
		//récupère les sous thème
		$sql = "SELECT COUNT(id_mot) nb
			FROM spip_mots m
				INNER JOIN spip_groupes_mots gm ON gm.id_groupe = m.id_groupe
				INNER JOIN spip_groupes_mots gmp ON gmp.id_groupe = gm.id_parent
			WHERE gmp.id_groupe=".$this->id;
		//echo $sql."<br/>";

		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		$r = $DB->fetch_assoc($req);
		return $r['nb'];
	}

	public function GetLogo()
	{
		return 'GetLogo<br/>';
	}

}


?>