<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAutoload()
	{
		$moduleLoader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath' => APPLICATION_PATH));

		$loader = Zend_Loader_Autoloader::getInstance();
		$loader->registerNamespace(array('GEVU_','AUTH_'));

	    //pour pouvoir charger les classe à la fois dans le serveur amf et avec l'autoloader
		$moduleLoader->addResourceType('dbgevu', 'models/DbTable/Gevu', 'Models_DbTable_Gevu');
		
  		/*
		Zend_Registry::set("db", $db);
  		
		$registry = Zend_Registry::getInstance();
		$registry->set('config', $config);
		$db = Zend_Db::factory('PDO_MYSQL', $options);
		Zend_Registry::set('my_db', $db);
		*/

	    return $moduleLoader;
	}


}

