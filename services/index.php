<?php
require_once( "../param/ParamAppli.php" );


try {
	
	/*
	$idBase = "gevu_new_alceane";
	$d = new GEVU_Diagnostique();
	$arr = $d->calculDiagForLieu(212,$idBase);
	$arr = $d->getNodeRelatedData(212,$idBase);
	$d->findLieu("","antenne",$idBase);
	$xml = $d->getXmlNode(212,$idBase);
	$db = $d->getDb($idBase);
	$o = new Models_DbTable_Gevu_diagnostics($db);
	$o->getAllDesc(13);
	*/
	
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

