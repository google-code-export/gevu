<?php

class MotClef
{
  public $id;
  public $titre;
  public $descriptif;
  public $id_groupe;
  private $site;

  function __tostring() {
    return "Cette classe permet de définir et manipuler un mot clef : .<br/>";
    }

  function __construct($id, $site) {

    //echo "$id, $site login=".$site["SQL_LOGIN"]."<br/>";
	$this->id = $id;
    $this->site = $site;
	$this->GetProps();
}

	public function GetProps()
	{
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		//charge les propiétés du mot
		$sql = "SELECT id_groupe , titre, descriptif
			FROM spip_mots m
			WHERE m.id_mot = ".$this->id;
		$req = $DB->query($sql);
		$DB->close();
		$data = $DB->fetch_assoc($req);
		$this->titre = $data['titre'];
		$this->descriptif = $data['descriptif'];
		$this->id_groupe = $data['id_groupe'];
	}

	public function GetNb($ScopeTopos = "")
	{

		$Where = " ";
		if($ScopeTopos!="")
			$Where = " AND mr.id_rubrique IN(".$ScopeTopos.")";
			
		//récupère les sous thème
		$sql = "SELECT COUNT(m.id_mot) nb
			FROM spip_mots m
				INNER JOIN spip_mots_rubriques mr ON mr.id_mot = m.id_mot
			WHERE m.id_mot=".$this->id.$Where;
		//echo $this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";

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

	public function GetFilAriane()
	{
		return 'GetNavigation<br/>';
	}

}


?>