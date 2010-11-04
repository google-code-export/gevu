<?php
date_default_timezone_set('Europe/Paris');
error_reporting(E_ALL|E_STRICT);
ini_set("display_errors", "on");

define ("WEB_ROOT","http://localhost/gevu");
define ("ROOT_PATH","c:\wamp\www\gevu");

set_include_path(get_include_path().PATH_SEPARATOR."C:\wamp\www\ZendFramework-1.10.8\library");
set_include_path(get_include_path().PATH_SEPARATOR."C:\wamp\www\ZendFramework-1.10.8\extras\library");
set_include_path(get_include_path().PATH_SEPARATOR.ROOT_PATH."\library\php");

// *ZAMFBROWSER IMPLEMENTATION*
//set_include_path(get_include_path().PATH_SEPARATOR."C:\wamp\www\exemples\php\ZamfBrowser_v.1.0\browser");
//require_once( "ZendAmfServiceBrowser.php" );


// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
    
/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();
?>