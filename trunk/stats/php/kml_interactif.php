<?php
require('codes.php');

// Ouvre connexion MySQL
$connection=mysql_connect ($server, $username, $password);
if (!$connection) 
{
  die('Not connected : ' . mysql_error());
}
// Définit la BDD active
$db_selected = mysql_select_db($database, $connection);
if (!$db_selected) 
{
  die ('Can\'t use db : ' . mysql_error());
}

// Sélectionne la requête que l'on veut
$query = 'SELECT id_lieu, lat, lng, adresse, kml FROM gevu_geos WHERE id_lieu = 3 AND id_geo = 2';
$result = mysql_query($query);
if (!$result) 
{
  die('Invalid query: ' . mysql_error());
}

// Création document
$dom = new domxml_new_doc('1.0');

// Créer l'élément KML et l'ajoute à la racine du document
$node = $dom->create_element_ns('http://earth.google.com/kml/2.1', 'kml');
$parNode = $dom->append_child($node);

// Crée un élément du document KML et l'ajouter à l'élément KML.
$dnode = $dom->create_element('Document');
$docNode = $parNode->append_child($dnode);
 
// Parcourt les résultats de MySQL, créer un repère pour chaque ligne.
while ($row = @mysql_fetch_assoc($result))
{
  // Crée un repère Placemark et l'ajouter au document.
  $node = $dom->create_element('Placemark');
  $placeNode = $docNode->append_child($node);
  // Crée un attribut id et attribuez-lui la valeur de la colonne id.
  $placeNode->set_attribute('id_lieu', 'placemark' . $row['id_lieu']);

  // Créer nom, éléments de description, attributs, et adresse
  $nameNode = $dom->create_element('name',htmlentities($row['name']));
  $placeNode->append_child($nameNode);
  $descNode = $dom->  create_element('description', $row['address']);
  $placeNode->append_child($descNode);
  $styleUrl = $dom->create_element('styleUrl', '#' . $row['type'] . 'Style');
  $placeNode->append_child($styleUrl);
 // Créer un point
  $pointNode = $dom->create_element('Point');
  $placeNode->append_child($pointNode);
 //Créer lignes
	$lineNode = $dom->createElement('LineString');
	$placeNode->appendChild($lineNode);
	$exnode = $dom->createElement('extrude', '1');
	$lineNode->appendChild($exnode);
	$almodenode =$dom->createElement(altitudeMode,'relativeToGround');
	$lineNode->appendChild($almodenode);
   
  // Créer des coordonnées en donnant latitude et longitude
  $coorStr = $row['lng'] . ','  . $row['lat'];
  $coorNode = $dom->create_element('coordinates', $coorStr);
  $pointNode->append_child($coorNode);
  $lineNode->appendChild($coorNode);
}

$kmlOutput = $dom->dump_mem(TRUE, 'UTF-8');
header('Content-type: application/vnd.google-earth.kml+xml');
echo $kmlOutput;
?>