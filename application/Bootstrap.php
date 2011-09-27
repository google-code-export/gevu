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

	    return $moduleLoader;
	}


}

