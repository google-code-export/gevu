<?php
require_once( "../application/configs/config.php" );


try {

	/*
	$a = new AUTH_LoginManager();
	$user = new AUTH_LoginVO();
	$user->username='samszo';
	$user->password='samszo';
	$r = $a->verifyUser($user);
	*/
	
	/*
	$idBase = "gevu_new";	
	$idExi = 1;
	$idLieu = 23053;
	$idScenario = 13;

	$d = new GEVU_Diagnostique();
	$d->getDiagListe(array("handi"=>"moteur","idLieu"=>23198,"niv"=>0),$idBase);
	//$d->ajoutLieu(1,$idExi,$idBase);
	$d->edit(3142,array("ref"=>"machn"),"Models_DbTable_Gevu_espacesxinterieurs",$idBase);
	//$arr = $d->getChaineDepla(1,$idBase);
	//$d->genereDiagWithIti(1,$idBase);	
	//$arr = $d->getNodeRelatedData(6, $idExi, $idBase, $idScenario);
	//$arr = $d->getLieuCtl(6, $idScenario, $idBase);	
	*/
	
	//$arr = $d->copiecolleLieu(22992, 22991, $idExi, $idBase);	
	//$arr = $d->getLieuCtl($idLieu, $idScenario, $idBase);	
	//$arr = $d->getXmlNode($idLieu, $idBase);
	//$arr = $d->getNodeRelatedData($idLieu, $idExi, $idBase, $idScenario);
	
	//$db = new Models_DbTable_Gevu_objetsxinterieurs();
	//$db->ajoutDiag($idExi, 12, $idLieu, 106, $idBase);

	//$db = new Models_DbTable_Gevu_espacesxinterieurs();
	//$db->edit(3142, array("ref"=>"bidule"));
	
	//
    $dbScene = new Models_DbTable_Gevu_scenes();
    $scene = $dbScene->copiecolle("A743D8B5-A567-BEAB-F3ED-AC8DE1FBDBA9","71AD65B0-5EA8-C1CA-12C6-69060AFD7B35");
	//

	
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

