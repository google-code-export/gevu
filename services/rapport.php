<?php
try {
	require_once( "../application/configs/config.php" );
	$application->bootstrap();	
	//http://localhost/gevu/services/rapport.php?idExi=1&idLieu=1113&idBase=gevu_clrp&idModele=546	
	$_GET["idModele"]=546;$_GET["idLieu"]=1113;$_GET["idExi"]=1;$_GET["idBase"]="gevu_clrp";
	if(!isset($_GET["idModele"]) || !isset($_GET["idLieu"]) || !isset($_GET["idExi"]) || !isset($_GET["idBase"])){
		echo "variables invalises";
	}else{
		$rapport = new GEVU_Rapport();
		$rapport->creaRapport($_GET["idModele"], $_GET["idLieu"], $_GET["idExi"], $_GET["idBase"]);
	}	
}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
	echo "Message: " . $e->getMessage() . "\n";
}
