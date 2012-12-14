<?php
require_once( "../param/ParamAppli.php" );


try {

	/*
	$idBase = "gevu_prospective";	
	$idExi = 1;
	$idLieu = 23053;
	$idScenario = 13;

	$d = new GEVU_Diagnostique();
	//$d->ajoutLieu(1,$idExi,$idBase);
	//$d->edit(4,array("adresse"=>"Route de Feins, 45230 Adon, France","kml"=>"","lat"=>47.754098,"lng"=>2.8125,"ne"=>"(65.80277639340238, 86.484375)"
	//	,"sw"=>"(-46.55886030311717, -156.09375)","type_carte"=>"terrain","zoom_max"=>2,"zoom_min"=>0),"Models_DbTable_Gevu_geos",$idBase);
	//$arr = $d->getChaineDepla(1,$idBase);
	$d->genereDiagWithIti(1,$idBase);	
	//$arr = $d->getNodeRelatedData(6, $idExi, $idBase, $idScenario);
	//$arr = $d->getLieuCtl(6, $idScenario, $idBase);	
	
	*/
	//$arr = $d->copiecolleLieu(22992, 22991, $idExi, $idBase);	
	//$arr = $d->getLieuCtl($idLieu, $idScenario, $idBase);	
	//$arr = $d->getXmlNode($idLieu, $idBase);
	//$arr = $d->getNodeRelatedData($idLieu, $idExi, $idBase, $idScenario);
	
	//$db = new Models_DbTable_Gevu_objetsxinterieurs();
	//$db->ajoutDiag($idExi, 12, $idLieu, 106, $idBase);

    //$dbScene = new Models_DbTable_Gevu_scenes();
    //$scene = $dbScene->verifIsNodeExiste(321);


	
$server = new Zend_Amf_Server();

$server->addDirectory(APPLICATION_PATH);
$server->addDirectory(dirname(__FILE__) .'/../library/php/');

/*pour l'authentification*/
$server->setClassMap('LoginVO','AUTH_LoginVO');	

$server->setProduction(false);

$response = $server->handle();
//var_dump($server->getFunctions());   		

}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
echo $response;

