<?php
require_once( "../param/ParamAppli.php" );

try {
    // cache fonctionel
    /*$frontendOptions = array(
       'lifetime' => 7200, // temps de vie du cache de 2 heures
       'automatic_serialization' => true
    );  
    $backendOptions = array(
        // Répertoire où stocker les fichiers de cache
        'cache_dir' => './tmp/'
    ); 
    // créer un objet Zend_Cache_Core
    $cache = Zend_Cache::factory('Core',
                                 'File',
                                 $frontendOptions,
                                 $backendOptions);*/

//$aaa = new GEVU_Diagnostique();
//$bbb = $aaa->getNodeRelatedData(419);
    

/*
$s = new Model_DbTable_Gevu_solutions();
$rs = $s->remove(526);


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
$u->password="samszo";
$au = $lm->verifyUser($u);
*/

$s = new Model_DbTable_Gevu_criteresxtypesxdeficiences();
$rs = $s->remove(1,1);
	


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
	->setClass('Model_DbTable_Gevu_docsxproduits')
	->setClass('Model_DbTable_Gevu_rapports')
	->setClass('Model_DbTable_Gevu_lieux')
	->setClass('Model_DbTable_Gevu_docsxlieux')
	->setClass('Model_DbTable_Gevu_motsclefs')
	->setClass('GEVU_ModifBase')
	->setClass('GEVU_Diagnostique')
	->setClass('Model_DbTable_Gevu_objetsxvoiries')
	->setClass('Model_DbTable_Gevu_batiments')
	->setClass('Model_DbTable_Gevu_diagnosticsxvoirie')
	->setClass('Model_DbTable_Gevu_espaces')
	->setClass('Model_DbTable_Gevu_espacesxexterieurs')
	->setClass('Model_DbTable_Gevu_espacesxinterieurs')
	->setClass('Model_DbTable_Gevu_etablissements')
	->setClass('Model_DbTable_Gevu_niveaux')
	->setClass('Model_DbTable_Gevu_objetsxexterieurs')
	->setClass('Model_DbTable_Gevu_objetsxinterieurs')
	->setClass('Model_DbTable_Gevu_parcelles')
	->setClass('Model_DbTable_Gevu_geos')
	->setClass('Model_DbTable_Gevu_tablearborescence')
	
	
	//pour l'authentification
	->setClass("AUTH_LoginManager")
	->setClass("AUTH_LoginVO")
	;
	
$server->setClassMap('LoginVO','AUTH_LoginVO');	
$server->setProduction(false);

$response = $server->handle();

}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
   		
echo $response;
