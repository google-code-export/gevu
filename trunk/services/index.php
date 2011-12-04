<?php
require_once( "../param/ParamAppli.php" );


try {
	
	/*
	$idBase = "gevu_new_alceane";
	$idExi = 1;
	$d = new GEVU_Diagnostique();
	$arr = $d->getNodeRelatedData(212,$idExi,$idBase);
	$arr = $d->calculDiagForLieu(1,-1,$idBase);
	$d->findLieu(212,"",$idBase);
	$xml = $d->getXmlNode(212,$idBase);
	$db = $d->getDb($idBase);
	$o = new Models_DbTable_Gevu_diagnostics($db);
	$o->getAllDesc(13);
	$o = new Models_DbTable_Gevu_observations();
	$arr = $o->ajouter(array("num_marker"=>"2121","lib"=>"test","id_diag"=>-100),1,$idBase);
	$o = new Models_DbTable_Gevu_problemes();
	$arr = $o->findDocs(329,$idBase);
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

