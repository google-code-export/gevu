<?php
require_once 'code.php';


// Sélectionne la requête que l'on veut
$query = 'SELECT  ref, g.id_lieu, lat,  lng, adresse, kml 
FROM  gevu_geos g
INNER JOIN  gevu_antennes a ON g.id_lieu = a.id_lieu
WHERE a.id_lieu IN (3, 3530, 8404, 13311, 17062)';
$result = mysql_query($query);
if (!$result) 
{
  die('Invalid query: ' . mysql_error());
}

//mysql_query("SELECT id_lieu, lat, lng, adresse, kml FROM gevu_geos WHERE id_lieu = 3 AND id_geo = 2", $connection);

// Création document
$dom = new DOMDocument('1.0', 'UTF-8');

// Créer l'élément KML et l'ajoute à la racine du document
$nodeNS = $dom->createElementNS('http://earth.google.com/kml/2.1', 'kml');
$dom->appendChild($nodeNS);

// Crée un élément du document KML et l'ajouter à l'élément KML.
$nodeDoc = $dom->createElement('Document');
$dom->appendChild($nodeDoc);
 
// Parcourt les résultats de MySQL, créer un repère pour chaque ligne.
while ($row = @mysql_fetch_assoc($result))
{
    
  	// Crée un repère Placemark et l'ajouter au document.
	$nodeP = getKMLPlacemark($dom, $row);
	$nodeDoc->appendChild($nodeP);
	
	//Créer première balise style
	//return Style;
	
	//Créer deuxième balise style
	//return Style;

	//Créer balise style de carte	
	//return StyleMap;
	
	//Créer Placemark	
	//return Placemark;
   
}

$kmlOutput = $dom->saveXML();
header('Content-type: application/vnd.google-earth.kml+xml'); //mon Php a comme header un content-type. Ce qu'on génère comme xml est du kml.
echo $kmlOutput;


function getKMLPlacemark($dom, $row){

	// Créer des coordonnées en donnant latitude et longitude
	if (false){
	}else{
		$coorStr = $row['lng'] . ','  . $row['lat'] . ',0 '
				. $row['lng']-0.00027169 . ',' . $row['lat']+0.000486  . ',0 '
						. $row['lng']-0.00056495 . ',' . $row['lat']-0.000054  . ',0 '
								. $row['lng'] . ','  . $row['lat']  . ',0 ';
		//$coorStr = '0.1105855171821202,49.52564189740646,0 0.1103138214001675,49.52612762237801,0 0.1100205604428695,49.52558762241633,0 0.1105855171821202,49.52564189740646,0'
	}
	
	
	$nodePlace = $dom->createElement('Placemark');
	// Crée un attribut id et attribuez-lui la valeur de la colonne id.
	$nodePlace->setAttribute('id_lieu', 'placemark' . $row['id_lieu']);
	// Créer nom, éléments de description, attributs, et adresse
	$nodeName = $dom->createElement('name',htmlentities($row['id_lieu']));
	$nodePlace->appendChild($nameNode);

	$nodeDesc = $dom->createElement('description', $row['adresse']);
	$nodePlace->appendChild($nodeDesc);

	$nodeStyleUrl = $dom->createElement('styleUrl', '#' . $row['type'] . 'm_ylw-pushpin');
	$nodePlace->appendChild($nodeStyleUrl);

	$nodePoly = $dom->createElement('Polygon');
		$nodeTessa = $dom->createElement('tessellate', '1');
		$nodeOuter = $dom->createElement('OunterBoundaryIs');
			$nodeLineRing = $dom->createElement('LinearRing');
				$nodeCoor = $dom->createElement('coordinates', $coorStr);
			$nodeLineRing->appendChild($coorNode);
		$nodeOuter->appendChild($nodeLineRing);
	$nodePoly->appendChild($nodeOuter);
	$nodePoly->appendChild($nodeTessa);

	$nodePlace->appendChild($nodePoly);

	return $nodePlace;
}


function getKMLStyle($row, $dom, $docNode){
	$styleNode = $dom->createElement('Style');
	$styleNode->setAttribute('id', 's_ylw-pushpin_hl' . $row['id']);
	$docNode->appendChild($styleNode);

	$LineStyleNode = $dom->createElement('LineStyle');
	$color = $dom->createElement('color', 'ff0000ff');
	$LineStyle->appendChild($color);

	$docNode->appendChild($LineStyleNode);

	$PolyStyle = $dom->createElement('PolyStyle');
	$placeNode = $docNode->appendChild($PolyStyle);

	$color = $dom->createElement('color', 'ff0000ff');
	$PolyStyle->appendChild($color);



}

function StyleMap(){
	$StyleMapnode = $dom->createElement('StyleMap');
	$placeNode3 = $docNode->appendChild($StyleMapnode);
	$placeNode3->setAttribute('id', 'm_ylw-pushpin' . $row['id']);

	$pair = $dom->createElement('pair');
	$placeNode3 = $docNode->appendChild($pair);

	$key = $dom->createElement('key', 'normal');
	$pair->appendChild($key);

	$styleUrl = $dom->createElement('styleUrl', '#' . $row['type'] . 's_ylw-pushpin');
	$pair->appendChild($styleUrl);

	$pair = $dom->createElement('pair');
	$placeNode3 = $docNode->appendChild($pair);

	$key = $dom->createElement('key', '#s_ylw-pushpin_hl');
	$pair->appendChild($key);

	$styleUrl = $dom->createElement('styleUrl', '#' . $row['type'] . 's_ylw-pushpin_hl');
	$pair->appendChild($styleUrl);
}



?>