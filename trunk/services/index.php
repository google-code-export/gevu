<?php
try {
	require_once( "../application/configs/config.php" );

	/*
	$s = new GEVU_Statistique();
	$json = $s->getPatrimoineDiag("gevu_new");
	$json = $s->getPatrimoineDonGenPatrimoine("gevu_new");
	*/
	
	/*
	$alceane = new GEVU_Alceane();
	header ("Content-Type:text/xml");  
	echo $alceane->getArboAntenne(1,"gevu_new");
	*/
	
	/*
	$s = new GEVU_Site();
	$arr = $s->getDb("","");
	*/
	
	/*
	$mig = new GEVU_Migration();
	echo $mig->migreRefServeurToLocal("gevu_ref", "gevu_android");
	*/
	
	/*
	$dbED = new Models_DbTable_Gevu_exisxdroits();
	$arr = $dbED->getUtiDroit(3, "gevu_trouville");
	*/
	
	/*
	$rapport = new GEVU_Rapport();
	$rapport->setSolusDefaut(6671,1,"gevu_trouville");
	$rapport->creaRapport(546,6671,1,"gevu_trouville");
	*/
	/*
	$a = new AUTH_LoginManager();
	$user = new AUTH_LoginVO();
	$user->username='lucky';
	$user->password='lucky';
	$r = $a->verifyUser($user);
	print_r($r);
	*/
	
	/*
	$idBase = "gevu_new";	
	$idExi = 16;
	$idLieu = 113;
	$idScenario = 18;

	$d = new GEVU_Diagnostique();
	//$d->ajoutUtiDiag($idLieu, $idExi, $idBase);
	//$d->getContact($idBase,"Models_DbTable_Gevu_batiments",array("id"=>1,"type"=>"contact_proprietaire"));
	//$arr = $d->getUtiLieuLock($idExi, "gevu_new", "serveur", "gevu_android", "local");
	//$arr = $d->setUtiLieuLock($idExi, "gevu_android", "android", "gevu_new", "serveur");
	//$arr = $d->getUtiIdLieuLock($idExi, $idLieu, $idBase);
	//$d->ajouterContact($idBase, "Models_DbTable_Gevu_batiments", array("idCtc"=>"11","idLien"=>"1","type"=>"contact_gardien"));
	//$d->deleteLieu($idLieu, $idExi, $idBase);
	//$d->getDiagListe(array("handi"=>"moteur","idLieu"=>23198,"niv"=>0),$idBase);
	//$d->ajoutLieu(1,$idExi,$idBase);
	//$d->edit(3142,array("ref"=>"machn"),"Models_DbTable_Gevu_espacesxinterieurs",$idBase);
	//$arr = $d->getChaineDepla(1,$idBase);
	//$d->genereDiagWithIti(1,$idBase);	
	//$arr = $d->getNodeRelatedData($idLieu, $idExi, $idBase, $idScenario);
	//$arr = $d->getLieuCtl(170, $idScenario, $idBase);	//23427
	$arr = $d->copiecolleLieu(23601, 23600, $idExi, $idBase);	
	//$arr = $d->getLieuCtl($idLieu, $idScenario, $idBase);	
	$arr = $d->getXmlNode(1, $idBase);
	//$arr = $d->getNodeRelatedData($idLieu, $idExi, $idBase, $idScenario);
	
	//$db = new Models_DbTable_Gevu_objetsxinterieurs();
	//$db->ajoutDiag($idExi, 12, $idLieu, 106, $idBase);
	*/
	
	//$db = new Models_DbTable_Gevu_espacesxinterieurs();
	//$db->edit(3142, array("ref"=>"bidule"));
	
	/*
    $dbScene = new Models_DbTable_Gevu_scenes();
    //$dbScene->getArboScenar("A743D8B5-A567-BEAB-F3ED-AC8DE1FBDBA9");
    $scene = $dbScene->copiecolle("A743D8B5-A567-BEAB-F3ED-AC8DE1FBDBA9","71AD65B0-5EA8-C1CA-12C6-69060AFD7B35");
    echo $scene->saveXML();
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
	echo "
	<h3>Exception information:</h3>
  	<p>
      <b>Message:</b>".$e->getMessage()."
  	</p>
	<h3>Stack trace:</h3>
  	<pre>".$e->getTraceAsString()."</pre>";
}
echo $response;

