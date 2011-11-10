<?php
require_once( "../param/ParamAppli.php" );


try {
	/*
	$idBase = "gevu_new_alceane";
	$d = new GEVU_Diagnostique();
	$xml = $d->getXmlNode(1,$idBase);
	$arr = $d->getNodeRelatedData(4870,$idBase);
	$db = $d->getDb($idBase);
	$o = new Models_DbTable_Gevu_diagnostics($db);
	$o->getAllDesc(13);
	*/
	
$server = new Zend_Amf_Server();

$server->addDirectory(APPLICATION_PATH);

$server->setClass('GEVU_Diagnostique');

	//pour l'authentification
$server->setClass("AUTH_LoginManager")
	   ->setClass("AUTH_LoginVO");	
$server->setClassMap('LoginVO','AUTH_LoginVO');	

$server->setProduction(false);

$response = $server->handle();
//var_dump($server->getFunctions());   		

}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
echo $response;

