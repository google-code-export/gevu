<?php
try {
	set_time_limit(200000);
	header('Content-Type: text/html; charset=utf-8');
	require_once( "../application/configs/config.php" );
	$application->bootstrap();
	
	$imp = new GEVU_Import();
	//$imp->addScenario("C:/wamp/www/gevu/param/scenarisation.xml");
	//$imp->addScenario("C:/wamp/www/gevu/param/logementV3.xml");
	
	//echo $imp->traiteImportLogement(561, 1, 1, 18, "gevu_new");
	//echo $imp->importGeos("c:\wamp\www\gevu\data\carto\AlceaneCompLatLng2013-11-09 06-06-31.csv","gevu_new","adresse");
	//echo $imp->traiteImportPiece(561, 1, "gevu_alceane");
	//$imp->creaDiagEspace(18, "gevu_alceane");
	
	$imp->addDoc($_REQUEST);

}catch (Zend_Exception $e) {
	echo "
	<h3>Exception information:</h3>
  	<p>
      <b>Message:</b>".$e->getMessage()."
  	</p>
	<h3>Stack trace:</h3>
  	<pre>".$e->getTraceAsString()."</pre>";
}
