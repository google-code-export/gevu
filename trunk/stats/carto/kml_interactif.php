<?php
require('phpsqlajax_dbinfo.php');

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
 
/*Création du style
$restStyleNode = $dom->create_element('Style');
$restStyleNode->set_attribute('id', 'restaurantStyle');
$restIconstyleNode = $dom->create_element('IconStyle');
$restIconstyleNode->set_attribute('id', 'restaurantIcon');
$restIconNode = $dom->create_element('Icon');
$restHref = $dom->create_element('href', 'http://maps.google.com/mapfiles/kml/pal2/icon63.png');
$restIconNode->append_child($restHref);
$restIconstyleNode->append_child($restIconNode);
$restStyleNode->append_child($restIconstyleNode);
$docNode->append_child($restStyleNode);
$barStyleNode = $dom->create_element('Style');
$barStyleNode->set_attribute('id', 'barStyle');
$barIconstyleNode = $dom->create_element('IconStyle');
$barIconstyleNode->set_attribute('id', 'barIcon');
$barIconNode = $dom->create_element('Icon');
$barHref = $dom->create_element('href', 'http://maps.google.com/mapfiles/kml/pal2/icon27.png');
$barIconNode->append_child($barHref);
$barIconstyleNode->append_child($barIconNode);
$barStyleNode->append_child($barIconstyleNode);
$docNode->append_child($barStyleNode);*/

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
   
  // Créer des coordonnées en donnant latitude et longitude
  $coorStr = $row['lng'] . ','  . $row['lat'];
  $coorNode = $dom->create_element('coordinates', $coorStr);
  $pointNode->append_child($coorNode);
}

$kmlOutput = $dom->dump_mem(TRUE, 'UTF-8');
header('Content-type: application/vnd.google-earth.kml+xml');
echo $kmlOutput;
?>