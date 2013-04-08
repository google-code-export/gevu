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
  	$coorStr = $row['lng'] . ','  . $row['lat'] . ',' . $row['lng']-0.00027169 . ',' . $row['lat']+0.000486  . ',' . $row['lng']-0.00056495 . ',' . $row['lat']-0.000054  . ',' . $row['lng'] . ','  . $row['lat'];
  	//$coorStr = '0.1105855171821202,49.52564189740646,0 0.1103138214001675,49.52612762237801,0 0.1100205604428695,49.52558762241633,0 0.1105855171821202,49.52564189740646,0'
  
  }
  
  
  // Crée un repère Placemark et l'ajouter au document.
  $node = $dom->createElement('Placemark');
  $placeNode = $docNode->appendChild($node);
  // Crée un attribut id et attribuez-lui la valeur de la colonne id.
  $placeNode->setAttribute('id_lieu', 'placemark' . $row['id_lieu']);
  
  // Créer nom, éléments de description, attributs, et adresse
  $nameNode = $dom->createElement('name',htmlentities($row['id_lieu']));
  $placeNode->appendChild($nameNode);
  
//Créer première balise style
  $node = $dom->createElement('Style');
  $placeNode = $docNode->appendChild($node);
  $placeNode->setAttribute('id', 's_ylw-pushpin_hl' . $row['id']);
  
 	$LineStyle = $dom->createElement('LineStyle');
	$placeNode = $docNode->appendChild($LineStyle);
	
	$color = $dom->createElement('color', 'ff0000ff');
	$LineStyle->appendChild($color);
	
	$PolyStyle = $dom->createElement('PolyStyle');
	$placeNode = $docNode->appendChild($PolyStyle);

	$color = $dom->createElement('color', 'ff0000ff');
	$PolyStyle->appendChild($color);
	
//Créer deuxième balise style
  $node = $dom->createElement('Style');
  $placeNode = $docNode->appendChild($node);
  $placeNode->setAttribute('id', 's_ylw-pushpin' . $row['id']);

 	$LineStyle = $dom->createElement('LineStyle');
	$placeNode = $docNode->appendChild($LineStyle);
	
	$color = $dom->createElement('color', 'ff0000ff');
	$LineStyle->appendChild($color);
	
	$PolyStyle = $dom->createElement('PolyStyle');
	$placeNode = $docNode->appendChild($PolyStyle);

	$color = $dom->createElement('color', 'ff0000ff');
	$PolyStyle->appendChild($color);
	

//Créer balise style de carte	
  $node = $dom->createElement('StyleMap');
  $placeNode = $docNode->appendChild($node);
  $placeNode->setAttribute('id', 'm_ylw-pushpin' . $row['id']);
  
	$pair = $dom->createElement('pair');
	$placeNode = $docNode->appendChild($pair);
	
	$key = $dom->createElement('key', 'normal');
	$pair->appendChild($key);
	
	$styleUrl = $dom->createElement('styleUrl', '#' . $row['type'] . 's_ylw-pushpin');
	$pair->appendChild($styleUrl);
	
 	$pair = $dom->createElement('pair');
	$placeNode = $docNode->appendChild($pair);
	
	$key = $dom->createElement('key', '#s_ylw-pushpin_hl');
	$pair->appendChild($key);
	
	$styleUrl = $dom->createElement('styleUrl', '#' . $row['type'] . 's_ylw-pushpin_hl');
	$pair->appendChild($styleUrl);


//Créer Placemark	
  $node = $dom->createElement('Placemark');
  $placeNode = $docNode->appendChild($node);
  $placeNode->setAttribute('id', 'Placemark' . $row['id']);
  
  $descNode = $dom-> createElement('description', $row['adresse']);
  $placeNode->appendChild($descNode);
  
  $styleUrl = $dom->createElement('styleUrl', '#' . $row['type'] . 'm_ylw-pushpin');
  $placeNode->appendChild($styleUrl);

  $pointNode = $dom->createElement('Polygon');
  $placeNode->appendChild($pointNode);

	$exnode = $dom->createElement('tessellate', '1');
	$pointNode->appendChild($exnode);

	$outer = $dom->createElement('OunterBoundaryIs');
	$pointNode->appendChild($outer); 
	
	$lineNode = $dom->createElement('LinearRing');
	$outer->appendChild($lineNode);

 

	/*$almodenode =$dom->createElement(altitudeMode,'relativeToGround');
	$lineNode->appendChild($almodenode);*/
   
  // Créer des coordonnées en donnant latitude et longitude
  $coorNode = $dom->createElement('coordinates', $coorStr);
  $pointNode->appendChild($coorNode);
  $lineNode->appendChild($coorNode);
}

$kmlOutput = $dom->saveXML();
header('Content-type: application/vnd.google-earth.kml+xml'); //mon Php a comme header un content-type. Ce qu'on génère comme xml est du kml.
echo $kmlOutput;

?>