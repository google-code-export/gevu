<?php
 $username = 'username';
 $password = 'password';
 $database = 'gevu_new';
 $server = '127.0.0.1';


//require('codes.php');
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

//mysql_query("SELECT id_lieu, lat, lng, adresse, kml FROM gevu_geos WHERE id_lieu = 3 AND id_geo = 2", $connection);

// Création document
$dom = new DOMDocument('1.0', 'UTF-8');

// Créer l'élément KML et l'ajoute à la racine du document
$node = $dom->createElementNS('http://earth.google.com/kml/2.1', 'kml');
$parNode = $dom->appendChild($node);

// Crée un élément du document KML et l'ajouter à l'élément KML.
$dnode = $dom->createElement('Document');
$docNode = $parNode->appendChild($dnode);
 
// Parcourt les résultats de MySQL, créer un repère pour chaque ligne.
while ($row = @mysql_fetch_assoc($result))
{
if (false){
 // if($row['kml']){ //vérifie si kml. Si oui on charge l'objet dans un placemark. Cette requête récupère les coordonnées où y'a le kml dans la base. Coordonnées string c'est ce qu'il y a dans résult. géénrère kml
	//  $xmlPlacemark = simplexml_load_string($row['kml']);
	//  if($xmlPlacemark){
           //      $result = $xmlPlacemark->xpath('//coordinates');                  
              //   $coorStr = $result[0]."";
         
	 // $result = $xmlPlacemark->xpath('//coordinates');
	//  $coorStr = $result[0]."";
  }else{
  	$coorStr = $row['lng'] . ','  . $row['lat'] . ',' . $row['lng']+0.6 . ',' . $row['lat']+6;
  }
  // Crée un repère Placemark et l'ajouter au document.
  $node = $dom->createElement('Placemark');
  $placeNode = $docNode->appendChild($node);
  // Crée un attribut id et attribuez-lui la valeur de la colonne id.
  $placeNode->setAttribute('id_lieu', 'placemark' . $row['id_lieu']);
  
  // Créer nom, éléments de description, attributs, et adresse
  $nameNode = $dom->createElement('name',htmlentities($row['id_lieu']));
  $placeNode->appendChild($nameNode);
  $descNode = $dom-> createElement('description', $row['adresse']);
  $placeNode->appendChild($descNode);
  $styleUrl = $dom->createElement('styleUrl', '#' . $row['type'] . 'Style');
  $placeNode->appendChild($styleUrl);
 // Créer un point
  $pointNode = $dom->createElement('Point');
  $placeNode->appendChild($pointNode);
 //Créer lignes
 	/*$Style = $dom->createElement('LineStyle');
	$placeNode->appendChild($Style);
	$Style1 = $dom->createElement('PolyStyle');
	$Style->appendChild($Style1);
	
	$Polygone = $dom->createElement('Polygon');
	$Poly->appendChild($Polygone);
	$tess = $dom->createElement('tesselate', '1');
	$Polygone->appendChild($tess); */
 
 
	$lineNode = $dom->createElement('LineString');
	$placeNode->appendChild($lineNode);
	$exnode = $dom->createElement('extrude', '1');
	$lineNode->appendChild($exnode);
	$almodenode =$dom->createElement(altitudeMode,'relativeToGround');
	$lineNode->appendChild($almodenode);
   
  // Créer des coordonnées en donnant latitude et longitude
  $coorNode = $dom->createElement('coordinates', $coorStr);
  $pointNode->appendChild($coorNode);
  $lineNode->appendChild($coorNode);
}

$kmlOutput = $dom->saveXML();
header('Content-type: application/vnd.google-earth.kml+xml'); //mon Php a comme header un content-type. Ce qu'on génère comme xml est du kml.
echo $kmlOutput;

?>