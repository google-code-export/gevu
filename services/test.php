<?php
//<a href="http://localhost:8080/gevu/public/index.php/migration?idExi=1&idBaseSrc=gevu_android&idRegSrc=android&idRegDst=ref&idBaseDst=gevu_new&dir=LocalToServer">TEST</a>

try {
	require_once( "../application/configs/config.php" );
	
	/*
	$dumpSettings = array(
        'include-tables' => array('table1', 'table2'),
        'exclude-tables' => array('table3', 'table4'),
        'compress' => 'GZIP',
        'no-data' => false,
        'add-drop-database' => false,
        'add-drop-table' => false,
        'single-transaction' => true,
        'lock-tables' => false,
        'add-locks' => true,
        'extended-insert' => true,
        'disable-foreign-keys-check' => false
    );
    $dump = new Mysqldump('clouddueling', 'root', 'password', 'localhost', 'mysql', $dumpSettings);
    $dump->start('storage/work/dump.sql');
    */
		
	/*
	$ftp = ftp_connect("ns367089.ovh.net", 21);
	ftp_login($ftp, "gevu", "TVES2013");
		
	$liste_fichiers = ftp_nlist($ftp, '.');
	ftp_put($ftp, "/www/data/android/IMG_20140402_135704.jpg", "/mnt/sdcard/DCIM/Camera/IMG_20140402_135704.jpg", FTP_BINARY);		
	*/
	
	//
	$mig = new GEVU_Migration();
	$mig->migreTabletteToServeur(1,"gevu_android","android","tves","gevu_test");
	$mig->migreRefServeurToLocal("gevu_ref","tves","android","gevu_android");
	//

	/*
	$dbED = new Models_DbTable_Gevu_exisxdroits();
	$dbED->edit(21, 3, '[{"lib":"GEVU alcéane","id":"3_gevu_new"},{"lib":"Alceane interne","id":"3_gevu_alceane"}]');
	*/
	/*
	$s = new GEVU_Statistique();
	//$json = $s->getAntenneDonGen("gevu_new", 3);
	//$json = $s->getPatrimoineDiag("gevu_new");
	//$json = $s->getPatrimoineDonGen("gevu_alceane");
	$json = $s->getBatimentDonGen("gevu_alceane", 5);
	echo json_encode($json);
	*/
	
	/*
	$alceane = new GEVU_Alceane();
	header ("Content-Type:text/xml");  
	echo $alceane->getArboLogement(5,"gevu_alceane");
	//echo $alceane->getArboAntenne(1,"gevu_alceane");
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
	
	//
	$idBase = "gevu_alceane";	
	$idExi = 1;
	$idLieu = 113;
	$idScenario = 18;

	$d = new GEVU_Diagnostique();
	//$d->deleteDiagCampagne(300, $idExi, $idBase);
	//$d->deleteDiag(354, $idExi, $idBase);
	//$d->ajoutUtiDiag($idLieu, $idExi, $idBase);
	//$d->getContact($idBase,"Models_DbTable_Gevu_batiments",array("id"=>1,"type"=>"contact_proprietaire"));
	//$arr = $d->getUtiLieuLock('1', 'gevu_new', 'ref', 'gevu_android', 'android');
	//$arr = $d->setUtiLieuLock($idExi, "gevu_android", "android", "gevu_alceane", "ref");
	//$arr = $d->getUtiIdLieuLock($idExi, $idLieu, $idBase);
	//$d->ajouterContact($idBase, "Models_DbTable_Gevu_batiments", array("idCtc"=>"11","idLien"=>"1","type"=>"contact_gardien"));
	//$d->deleteLieu($idLieu, $idExi, $idBase);
	//$arr = $d->getDiagListe(array("handi"=>"moteur","idLieu"=>15601,"niv"=>0),$idBase);
	//echo 'toto';
	//$d->ajoutLieu(1,$idExi,$idBase);
	//$d->edit(3142,array("ref"=>"machn"),"Models_DbTable_Gevu_espacesxinterieurs",$idBase);
	//$arr = $d->getChaineDepla(1,$idBase);
	//$d->genereDiagWithIti(1,$idBase);	
	//$arr = $d->getNodeRelatedData($idLieu, $idExi, $idBase, $idScenario);
	//$arr = $d->getLieuCtl(170, $idScenario, $idBase);	//23427
	//$arr = $d->copiecolleLieu(8, 1, $idExi, "gevu_valdemarne", array("gevu_etudiants","ref","gevu_valdemarne","ref"));	
	//$arr = $d->getLieuCtl($idLieu, $idScenario, $idBase);	
	//$arr = $d->getXmlNode(1, $idBase);
	//$arr = $d->getNodeRelatedData($idLieu, $idExi, $idBase, $idScenario);
	
	
	//$db = new Models_DbTable_Gevu_objetsxinterieurs();
	//$db->ajoutDiag($idExi, 12, $idLieu, 106, $idBase);
	//
	
	//$db = new Models_DbTable_Gevu_espacesxinterieurs();
	//$db->edit(3142, array("ref"=>"bidule"));
	
	/*
    $dbScene = new Models_DbTable_Gevu_scenes();
    //$dbScene->getArboScenar("A743D8B5-A567-BEAB-F3ED-AC8DE1FBDBA9");
    $scene = $dbScene->copiecolle("A743D8B5-A567-BEAB-F3ED-AC8DE1FBDBA9","71AD65B0-5EA8-C1CA-12C6-69060AFD7B35");
    echo $scene->saveXML();
	*/

	print_r($arr);
	
}catch (Zend_Exception $e) {
	echo "
	<h3>Exception information:</h3>
  	<p>
      <b>Message:</b>".$e->getMessage()."
  	</p>
	<h3>Stack trace:</h3>
  	<pre>".$e->getTraceAsString()."</pre>";
}

