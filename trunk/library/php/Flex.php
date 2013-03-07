<?php
class Flex{
  public $id;
  public $trace;
  private $site;
 
    function __tostring() {
    return "Cette classe permet la création dynamique d'objet XUL.<br/>";
    }

    function __construct($site, $id=-1) {
		//echo "new Site $sites, $id, $scope<br/>";
	  	$this->trace = TRACE;
	
	    $this->site = $site;
	    $this->id = $id;
		
		
		//echo "FIN new grille <br/>";		
    }


    
    function GetKmlPlans(){

		//récupération des kml
		$doc = new Document($this->site);
		$rs = $doc->GetAllDocsFic(75);
		$embed ="";
		$arrFic = array();
		$getPlan ="public function getPlan(nom:String):GroundOverlay{
		   	var groundOverlay:GroundOverlay;
			switch (nom) {\n";
		//parse le kml pour chaque document
		$i=0;
		while ($r =  mysql_fetch_assoc($rs)) {
			$path = $this->site->infos["pathSpip"].$r["fichier"];
			//charge le xml
			$XmlStr = $this->site->GetCurl($path);
			$xml = simplexml_load_string($XmlStr);
			if($xml->GroundOverlay){
				//récupère les infos du plan
				$s = $xml->GroundOverlay->LatLonBox->south;
				$w = $xml->GroundOverlay->LatLonBox->west;
				$n = $xml->GroundOverlay->LatLonBox->north;
				$e = $xml->GroundOverlay->LatLonBox->east;
				$href = $xml->GroundOverlay->Icon->href;
				$href = substr($href,strrpos($href, "/")+1);
				//vérifie si le fichier existe
				$path = PathRoot."/flex/src/plans/".$href;
				if(!file_exists($path)){
					//met le code en commentaire
					$embed .="//[Embed(source=\"/plans/".$href."\")] public var plan_".$r["id_rubrique"].":Class;\n";
				}else{
					//verifie si le fichier est déjà traité
					if (!in_array($href, $arrFic)) {
					    $arrFic[$i] = $href;
						//construit le code AS3 pour la source
						$embed .="[Embed(source=\"/plans/".$href."\")] public var plan_".$i.":Class;\n";
						$key = $i;
						$i++;
					}else{
						$key = array_search($href, $arrFic);
					}
					//construit le code AS3 pour le choix du plan
					$getPlan .= "case 'plan_".$r["id_rubrique"]."':
					groundOverlay = new GroundOverlay(
		                new plan_".$key.",
		                new LatLngBounds(new LatLng(".$s.",".$w."), new LatLng(".$n.",".$e.")));
					 break;\n";
				}
			}
		
		}
		$getPlan .= "default:
			        trace('Not 0, 1, or 2');
			}
			return groundOverlay;\n";				    	
    	echo $embed;
    	echo $getPlan;	
    }
    
}    