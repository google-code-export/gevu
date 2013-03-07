<?php
class Document{
  public $type;
  public $id;
  public $fichier;
  public $largeur;
  public $hauteur;
  public $trace;
  private $site;
  public $svgns = ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" ';
  
  function __tostring() {
    return "Cette classe permet de définir et manipuler un document.<br/>";
    }

  function __construct($site, $data=-1, $id=-1) {
  	
		$this->site = $site;
		$this->trace = TRACE;
  		
		if($data!=-1){
		    $this->type = $data['id_type'];
		    $this->id = $data['id_document'];
		    if(substr($data['fichier'],0,5)=="http:")
		    	$this->fichier = $data['fichier'];
		    else
		    	$this->fichier = $site->infos["pathSpip"].$data['fichier'];
		    $this->path = str_replace(WebRoot, PathRoot, $this->fichier);
		    $this->largeur = $data['largeur'];
		    $this->hauteur = $data['hauteur'];
		    /*
			if($data['rtitre'])
				$this->titre = $data['rtitre'];
			*/
		    if($data['dtitre'])
				$this->titre = $data['dtitre'];
	  	}	
		if($id!=-1){
			$sql ="SELECT * FROM spip_documents where id_document = ".$id;
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$db->connect();
			$req = $db->query($sql);
			$data = $db->fetch_assoc($req);
			$db->close();
			
			$this->type = $data['id_type'];
		    $this->id = $data['id_document'];
		    $this->fichier = $site->infos["pathSpip"].$data['fichier'];
		    $this->largeur = $data['largeur'];
		    $this->hauteur = $data['hauteur'];
			$this->titre = $data['titre'];
	  	}	
	  	

    }

    function GetAllDocsFic($type){

		$sql ="SELECT d.fichier, r.id_rubrique, a.id_article 
			FROM spip_documents d
				INNER JOIN spip_documents_articles da ON da.id_document = d.id_document
				INNER JOIN spip_articles a ON a.id_article = da.id_article
				INNER JOIN spip_rubriques r ON r.id_rubrique = a.id_rubrique
			WHERE id_type = ".$type." ORDER BY d.fichier";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		return $req;    	    	
    }
    
    function GetSvgGallerie($large, $haut){

		$svg = '<svg id="svg_'.$this->id.'" width="'.$large.'" height="'.$haut.'" '.$this->svgns.' >';
		//$svg = '<rect x="0" y="0" width="'.$large.'" height="'.$haut.'" fill="black"  />';	
		$svg .= "<svg preserveAspectRatio='xMidYMid meet' viewBox='0 0 ".$this->largeur." ".$this->hauteur."'  >
					<image onclick=\"EnlargeSvg('svg_".$this->id."',800,600)\" width='".$this->largeur."' height='".$this->hauteur."' xlink:href='".$this->fichier."' />
				</svg>
			</svg>";
		return $svg;    	
    	
    }

    function GetSvgImage($large, $haut){

		$svg = '<svg preserveAspectRatio="xMidYMid meet" viewBox="0 0 '.$large.' '.$haut.' '.$this->svgns.' " >
					<image width="'.$this->largeur.'" height="'.$this->hauteur.'" xlink:href="'.$this->fichier.'" />
				</svg>';
		return $svg;    	
    	
    }
    
    
	 function DimensionImage($LargeurMax, $HauteurMax, $fic="", $balise="img") {
		
		if($fic=="")
			$fic=$this->fichier;
			
		//echo "$Image, $HauteurMax, $LargeurMax, $Hauteur, $Largeur \n";
		$Dimension ="";
		if($this->largeur > $this->hauteur) {
			$Dimension = "width='".$LargeurMax."' ";
		}else {
			$Dimension = "height='".$HauteurMax."' ";
		}
		//gestion du XUL
		if($balise=='image'){
			$Dimension = "maxWidth='".$LargeurMax."' ";
			$Dimension .= " maxHeight='".$HauteurMax."' ";	
		}
		//echo "src='".$Image."' ".$Dimension." \n";
		return "<".$balise." src=\"".$fic."\" ".$Dimension." alt=\"".$this->titre."\" border=\"0\" align=\"absbottom\" />";
	}
	
	function GetFlv($width=400,$height=300){
		$flv = '<object type="application/x-shockwave-flash" data="'.$this->site->infos['urlLibSwf'].'player_flv.swf" width="'.$width.'" height="'.$height.'">
			<param name="movie" value="'.$this->site->infos['urlLibSwf'].'player_flv.swf" />
			<param name="FlashVars" value="flv='.$this->fichier.'&amp;width='.$width.'&amp;height='.$height.'&amp;bgcolor1=ffffff&amp;bgcolor2=cccccc&amp;buttoncolor=999999&amp;buttonovercolor=0&amp;slidercolor1=cccccc&amp;slidercolor2=999999&amp;sliderovercolor=666666&amp;textcolor=0&amp;showstop=1&amp;title=&amp;startimage=/images/icone_voir.jpg" />
			<param name="wmode" value="opaque" />
			<span><a href="'.$this->fichier.'" rel="enclosure">'.$this->fichier.'</a></span>
			</object>';
		
		return $flv;
		
	}
	
	function GetMp3($width=400,$height=300){
		$mp3 = '<object id="audioplayer'.$this->id.'" width="'.$width.'" height="'.$height.'" data="'.$this->site->infos['urlLibSwf'].'neoplayer_multi.swf" type="application/x-shockwave-flash">
		<param value="opaque" name="wmode"/>
		<param value="'.$this->site->infos['urlLibSwf'].'neoplayer_multi.swf" name="movie"/>
		<param value="mp3='.$this->fichier.'&bgcolor1=ffffff&bgcolor2=cccccc&buttoncolor=999999&buttonovercolor=0&slidercolor1=cccccc&slidercolor2=999999&sliderovercolor=666666&textcolor=0&showstop=1&showinfo=1" name="FlashVars"/>
		<span>
		<a rel="enclosure" href="'.$this->fichier.'</a>
		</span>';	
		
		return $mp3;
		
	}
		
	function GetVignette($LargeurMax, $HauteurMax)
	{
		$vignette = $this->fichier;
		$vignette = str_replace("jpg/", "", $vignette);
		$vignette = str_replace("IMG/", "IMG/vignettes/", $vignette);
		$vignette = $this->DimensionImage($LargeurMax, $HauteurMax, $vignette);
		
		return $vignette;
		
	}
	
	function AddNew($row)
	{
		//ajoute un nouveau document dans la table
		if($this->trace)
			echo "Document:AddNew:row=".print_r($row)."<br/>";
		
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='AddNewDoc']";
		if($this->trace)
			echo "Document:AddNew:Xpath=".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);		
		$values = str_replace("-titre-", $row['titre'], $Q[0]->values);
		$values = str_replace("-type-", $row['type'], $values);
		$values = str_replace("-desc-", $row['desc'], $values);
		$values = str_replace("-fichier-", $row['fichier'], $values);
		$values = str_replace("-taille-", $row['taille'], $values);
		$values = str_replace("-largeur-", $row['largeur'], $values);
		$values = str_replace("-hauteur-", $row['hauteur'], $values);
		$sql = $Q[0]->insert.$values;
		if($this->trace)
			echo "Document:AddNew:sql=".$sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$db->query($sql);
		$id = mysql_insert_id();
		$db->close();

		if($this->trace)
			echo "Document:AddNew:id=".$id."<br/>";

		//ajoute la relation avec la destination
		if($row['idArt']){	
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='AddNewArtDoc']";
			if($this->trace)
				echo "Document:AddNew:Xpath=".$Xpath."<br/>";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$values = str_replace("-idArt-", $row['idArt'], $Q[0]->values);
			$values = str_replace("-idDoc-", $id, $values);
			$sql = $Q[0]->insert.$values;
			if($this->trace)
				echo "Document:AddNew:sql=".$sql."<br/>";
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
			$db->connect();
			$db->query($sql);
			$db->close();
		}
			
		$d = new Document($this->site,-1,$id);	
		
		return $d;
		
	}
	
}
?>