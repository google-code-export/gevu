<?php
try {
	require_once( "../application/configs/config.php" );
	
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

