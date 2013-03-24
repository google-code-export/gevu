<?php
require_once( "../application/configs/config.php" );
$application->bootstrap();


try {

	/*
	$dbDS = new Models_DbTable_Gevu_diagnosticsxsolutions();
	$arr = $dbDS->findByIdLieu(6675, "gevu_trouville");
	*/
	
	/*
	$dbSC = new Models_DbTable_Gevu_solutionsxcriteres();
	$arr = $dbSC->findByIdCritere(74, true);
	*/
	
	/*
	$dbDiag = new Models_DbTable_Gevu_diagnostics();
	$arr = $dbDiag->getDiagSolus(10694, "gevu_trouville", "gevu_new");
	*/
	
	/*
	$rapport = new GEVU_Rapport();
	$rapport->getSolusProb(10694, "gevu_trouville");
	*/
	
	/*
	$migre = new GEVU_Migration("gevu_transfert","gevu_new");
	//$migre->migreLieu(1, 23687, 1);
	$migre->migreSolutions(1);
	*/
	
	/*
	$a = new AUTH_LoginManager();
	$user = new AUTH_LoginVO();
	$user->username='samszo';
	$user->password='samszo';
	$r = $a->verifyUser($user);
	*/
	
	/*
	$idBase = "gevu_valdemarne";	
	$idExi = 1;
	$idLieu = 23385;
	$idScenario = 13;

	$d = new GEVU_Diagnostique();
	//$d->getContact($idBase, "Models_DbTable_Gevu_etablissements", array("type"=>"contact_proprietaire"));
	//$d->deleteLieu(3432, 1);
	//$d->getDiagListe(array("handi"=>"visuel","idLieu"=>$idLieu,"niv"=>"2"),$idBase);
	//$d->ajoutLieu(1,$idExi,$idBase);
	//$d->edit(3142,array("ref"=>"machn"),"Models_DbTable_Gevu_espacesxinterieurs",$idBase);
	//$arr = $d->getChaineDepla(1,$idBase);
	//$d->genereDiagWithIti(1,$idBase);	
	$arr = $d->getNodeRelatedData($idLieu, $idExi, $idBase, $idScenario);
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
	
	/*
    $dbScene = new Models_DbTable_Gevu_scenes();
    //$dbScene->getArboScenar("A743D8B5-A567-BEAB-F3ED-AC8DE1FBDBA9");
    $scene = $dbScene->copiecolle("C93BAB7E-6B6A-86DA-8761-6900A57BCF80","7E9BF06F-25A7-9230-1E77-DCE6990C265D");
    //vérifie la valeur du xml
    $s = new GEVU_Site();
    $object = $s->xmlToObject($scene);
    echo $scene->saveXML();

	$dbScenario = new Models_DbTable_Gevu_scenario();
	$arr = $dbScenario->exporteScenarProduits(12);
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

