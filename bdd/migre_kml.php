<?php
	$site = "trouvilleERP1";
	require_once("../param/ParamPage.php");
	require_once('../library/php/odtphp/zip/PclZipProxy.php');
	require_once('../library/php/odtphp/zip/PhpZipProxy.php');
	
	//pour le zip
	$zip = new PclZipProxy();
	$dom = new DOMDocument;
	
	//récupère la liste des documents kml
	$sql = "SELECT d.id_document
	            , fichier
	            , d.titre
	            , id_type
	            , d.date 
	    FROM spip_documents d
	    WHERE id_type IN (75,76)";
	$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
	$db->connect();
	$rs = $db->query($sql);

	while($r=mysql_fetch_array($rs)){
		//vérifie si le fichier existe
		$filename = $objSite->infos["pathSpip"].$r['fichier'];
		$filename = str_replace(WebRoot, PathRoot, $filename);
		if (file_exists($filename)) {
		    //vérifie le type de fichier
		    if($r['fichier']==76){
		    	echo "OOOO on décompresse le fichier : ".$filename;
		    	/*
		    	if ($zip->open($filename) == true) {
		    		$content = $zip->getFromName('content.xml');
		    	}
				*/	
		    }else{
		    	$modif = false;
				$content = file_get_contents($filename);
			    $replace = $objSite->infos["pathSpip"];
				//vérifie si un lien vers gevu.eu est précisé
			    $find = "http://www.gevu.eu/trouville/spip/";
			    $pos = strpos($content, $find);
				if ($pos !== false) {
					//remplace le lien
					echo substr($content, $pos, strpos($content, "</href>", $pos)-$pos)."<br/>";
					$content = str_replace($find,$replace,$content);
					echo substr($content, $pos, strpos($content, "</href>", $pos)-$pos)."<br/><br/>";
		    		$modif = true;
				}
			    $find = "http://www.gevu.eu/trouville/spip1/";
			    $pos = strpos($content, $find);
				if ($pos !== false) {
					//remplace le lien
					echo substr($content, $pos, strpos($content, "</href>", $pos)-$pos)."<br/>";
					$content = str_replace($find,$replace,$content);
					echo substr($content, $pos, strpos($content, "</href>", $pos)-$pos)."<br/><br/>";
		    		$modif = true;
				}
				if($modif){
					//enregistre le fichier
					$fp = fopen($filename, 'w');
					fwrite($fp, $content);
					fclose($fp);				 					
					echo "-- MODIFICATION FAITE --<br/><br/>";
				}
		    }
		}
	}

?>