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
		$moduleLoader->addResourceType('dbgevu', 'Models/DbTable/Gevu', 'Models_DbTable_Gevu');
						
	    return $moduleLoader;
	}
	/*merci à http://jameshd.wordpress.com/2010/09/27/zend-multi-db/
	protected function _initDbAdaptersToRegistry()
	{
		$this->bootstrap('multidb');
		$resource = $this->getPluginResource('multidb');
		$resource->init();
				
		$Adapter1 = $resource->getDb('bd1');
		$Adapter2 = $resource->getDb('bd2');		
		Zend_Registry::set('gevu_new_alceane', $Adapter1);
		Zend_Registry::set('gevu',$Adapter2);
		
	}
	*/
	
}

