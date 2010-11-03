<?php
require_once( "../param/ParamAppli.php" );

/*
$s = new Model_DbTable_Gevu_solutionsxcriteres();
$rs = $s->getAll();
$data = array("url"=>"kjh","titre"=>"csv","content_type"=>"text/csv");
$s->ajouter($data,false);

$lm = new AUTH_LoginManager();
$u = new AUTH_LoginVO();
$u->username="samszo";
$u->password="samszo";
$au = $lm->verifyUser($u);
*/

$server = new Zend_Amf_Server();
//voir s'il ne faut pas passer par des objects en dehors du framework pour �viter 
//une s�rialisation trop lourde
//des erreurs dans la s�rialisation
//cf. la suppression du paramam�tre en trop dans le block commentaire de Zend_Db_Table_Abstract->find()
//$server->addDirectory(dirname(__FILE__) .'/../library/php/');

// *ZAMFBROWSER IMPLEMENTATION*
$server->setClass( "ZendAmfServiceBrowser" );
ZendAmfServiceBrowser::$ZEND_AMF_SERVER = $server;


$server->setClass('Model_DbTable_Gevu_solutions')
	->setClass('Model_DbTable_Gevu_metiers')
	->setClass('Model_DbTable_Gevu_typesxsolutions')
	->setClass('Model_DbTable_Gevu_solutionsxmetiers')
	->setClass('Model_DbTable_Gevu_docs')
	->setClass('Model_DbTable_Gevu_criteres')
	->setClass('Model_DbTable_Gevu_solutionsxcriteres')
	->setClass('Model_DbTable_Gevu_solutionsxproduits')
	->setClass('Model_DbTable_Gevu_produits')
	->setClass('Model_DbTable_Gevu_entreprises')
	
	//pour l'authentification
	->setClass("AUTH_LoginManager")
	->setClass("AUTH_LoginVO");
	
$server->setClassMap('LoginVO','AUTH_LoginVO');	

$response = $server->handle();
		
echo $response;
?>