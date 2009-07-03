<?php
//vérifie si la page est include ou ajax
if(!$g){
	$ajax = true;
	require_once("../param/ParamPage.php");
}

//récupération des documents
//$docs = $g->GetArtDocs($_GET['idArt']);
$doc = new Document($objSite,-1,$_GET['idDoc']);

?>
<html >
  <head>
  </head>
  <body >
	<?php
		/*
		foreach($docs as $doc){
			if($doc->id==$_GET['idDoc'])
				echo $doc->GetFlv(200,150); 
		}
		*/
		switch ($doc->type) {
			case 10: //'flv'
				echo $doc->GetFlv(200,150); 
				break;
			case 14: //'mp3'
				echo $doc->GetMp3(200,150); 
				break;
		}
		?>	
  </body>
</html>
