<?php
require_once( "../param/ParamAppli.php" );

try {

/*

$s = new Model_DbTable_Gevu_criteres();
$data = array("criteres"=>"La banque d'accueil permet la communication visuelle entre les usagers et le personnel","ref"=>"3_cr_acc_06","handicateur_moteur"=>"1","handicateur_auditif"=>"2","handicateur_visuel"=>"1","handicateur_cognitif"=>"3","id_type_controle"=>"1","affirmation"=>"La banque d'accueil ne permet pas la communication visuelle entre les usagers et le personnel");
$s->edit(6, $data);

$s = new Model_DbTable_Gevu_contacts();
$data = array("nom"=>"kj","prenom"=>"kj","fixe"=>"kj","mobile"=>"kj","mail"=>"kj","url"=>"kj","observations"=>"kj");
$s->ajouter($data);
	
$s = new Model_DbTable_Gevu_docs();
$rs = $s->getAll();
print_r($rs);
	
$s = new Model_DbTable_Gevu_solutionsxproduits();
$rs = $s->findByIdProduit(6);

$s = new Model_DbTable_Gevu_entreprises();
$data = array("nom"=>"kj","num"=>"kj","voie"=>"kj","code_postal"=>"kj","ville"=>"kj","pays"=>"kj","telephone"=>"kj","fax"=>"kj","mail"=>"kj","url"=>"kj","observations"=>"kj");
$s->ajouter($data);


$s = new Model_DbTable_Gevu_produits();
$rs = $s->getAll();
$data = array("url"=>"kjh","titre"=>"csv","content_type"=>"text/csv");
$s->ajouter($data,false);

$lm = new AUTH_LoginManager();
$u = new AUTH_LoginVO();
$u->username="samszo";
$u->password="Samszo0";
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
	->setClass('Model_DbTable_Gevu_solutionsxmetiers')
	->setClass('Model_DbTable_Gevu_solutionsxcriteres')
	->setClass('Model_DbTable_Gevu_solutionsxproduits')
	->setClass('Model_DbTable_Gevu_docs')
	->setClass('Model_DbTable_Gevu_metiers')
	->setClass('Model_DbTable_Gevu_produits')
	->setClass('Model_DbTable_Gevu_entreprises')
	->setClass('Model_DbTable_Gevu_criteres')
	->setClass('Model_DbTable_Gevu_criteresxtypesxdroits')
	->setClass('Model_DbTable_Gevu_criteresxtypesxdeficiences')
	->setClass('Model_DbTable_Gevu_criteresxtypesxcriteres')
	->setClass('Model_DbTable_Gevu_typesxsolutions')
	->setClass('Model_DbTable_Gevu_typesxcontroles')
	->setClass('Model_DbTable_Gevu_typesxdroits')
	->setClass('Model_DbTable_Gevu_typesxdeficiences')
	->setClass('Model_DbTable_Gevu_typesxcriteres')	
	->setClass('Model_DbTable_Gevu_contacts')
	->setClass('Model_DbTable_Gevu_couts')
	->setClass('Model_DbTable_Gevu_docsxsolutions')
	
	//pour l'authentification
	->setClass("AUTH_LoginManager")
	->setClass("AUTH_LoginVO")
	;
	
$server->setClassMap('LoginVO','AUTH_LoginVO');	

$response = $server->handle();

}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
   		
echo $response;
