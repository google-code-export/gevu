<?php

require_once 'codes.php';

if(isset($_GET['idLieu'])){
	$query = 'UPDATE gevu_geos SET lat = '.$_GET['lat'].', lng = '.$_GET['lng'].', '.$_GET['pitch'].', heading = '.$_GET['heading'].', zoom_cell = '.$_GET['zoom_cell'].' WHERE gevu_geos.id_lieu = '.$_GET['idLieu'];
}

echo $query;

$result = mysql_query($query);

