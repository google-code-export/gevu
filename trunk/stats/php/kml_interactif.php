<?php
require_once 'codes.php';


// Sélectionne la requête que l'on veut
$query = 'SELECT  ref, g.id_lieu, lat,  lng, adresse, kml 
FROM  gevu_geos g
INNER JOIN  gevu_antennes a ON g.id_lieu = a.id_lieu
WHERE a.id_lieu IN ('.$_GET['ids'].')';
$result = mysql_query($query);
if (!$result) 
{
  die('Invalid query: ' . mysql_error());
}

//mysql_query("SELECT id_lieu, lat, lng, adresse, kml FROM gevu_geos WHERE id_lieu = 3 AND id_geo = 2", $connection);

// Création document
$dom = new DOMDocument('1.0', 'UTF-8');

// Créer l'élément KML et l'ajoute à la racine du document
$nodeKML = $dom->createElementNS('http://earth.google.com/kml/2.1', 'kml');
$dom->appendChild($nodeKML);

// Crée un élément du document KML et l'ajouter à l'élément KML.
$nodeDoc = $dom->createElement('Document');
$nodeKML->appendChild($nodeDoc);
 
// Parcourt les résultats de MySQL, créer un repère pour chaque ligne.
while ($row = @mysql_fetch_assoc($result))
{
    
  	// Crée un repère Placemark et l'ajouter au document.
	$nodeP = getKMLPlacemark($dom, $row);
	$nodeDoc->appendChild($nodeP);
	$nodeP1 = getKMLPlacemark($dom, $row);
	$nodeDoc1->appendChild($nodeP1);
	
	//Créer première balise style
function getKMLStyle($row, $dom, $docNode){
	$styleNode = $dom->createElement('Style');
	$styleNode->setAttribute('id', 's_ylw-pushpin_hl' . $row['id']);
	$docNode->appendChild($styleNode);

	$LineStyleNode = $dom->createElement('LineStyle');
		$nodeLineColor = $dom->createElement('color', 'ff0000ff');
	$LineStyleNode->appendChild($nodeLineColor);
	$PolyStyleNode = $dom->createElement('PolyStyle');
		$nodePolyColor = $dom->createElement('color', 'ff0000ff');	
	$PolyStyleNode->appendChild($nodePolyColor);	

	$styleNode->appendChild($LineStyleNode);
	$placeNode = $docNode->appendChild($PolyStyleNode);

	return $styleNode;
}
	
	//Créer deuxième balise style
function getKMLStyle1($row, $dom, $docNode){
	$styleNode1 = $dom->createElement('Style');
	$styleNode1->setAttribute('id', 's_ylw-pushpin_hl' . $row['id']);
	$docNode1->appendChild($styleNode1);

	$LineStyleNode1 = $dom->createElement('LineStyle');
		$nodeLineColor1 = $dom->createElement('color', 'ff0000ff');
	$LineStyleNode1->appendChild($nodeLineColor);
	$PolyStyleNode1 = $dom->createElement('PolyStyle');
		$nodePolyColor1 = $dom->createElement('color', 'ff0000ff');	
	$PolyStyleNode1->appendChild($nodePolyColor1);	

	$styleNode1->appendChild($LineStyleNode1);
	$placeNode1 = $docNode->appendChild($PolyStyleNode1);

	return $styleNode1;
}
	//Créer balise style de carte	
function getMapStyle($row, $dom, $docNode){
	$StyleMapnode = $dom->createElement('StyleMap');
	$placeNode3->setAttribute('id', 'm_ylw-pushpin' . $row['id']);
	$placeNode3 = $docNode->appendChild($StyleMapnode);


	$pair = $dom->createElement('pair');
		$key = $dom->createElement('key', 'normal');
		$styleUrl = $dom->createElement('styleUrl', '#' . $row['type'] . 's_ylw-pushpin');
	$pair->appendChild($styleUrl);
	$pair->appendChild($key);
	$pair = $dom->createElement('pair');	
		$key = $dom->createElement('key', '#s_ylw-pushpin_hl');
		$styleUrl = $dom->createElement('styleUrl', '#' . $row['type'] . 's_ylw-pushpin_hl');	
	$pair->appendChild($styleUrl);
	$pair->appendChild($key);
	
	$StyleMapnode = $docNode->appendChild($pair);
	
	return $StyleMapnode;
}

function getKMLPlacemark($dom, $row){

	// Créer des coordonnées en donnant latitude et longitude
	if (false){
	}else{
		$coorStr = $row['lng'].','.$row['lat'].',0 '
				.($row['lng']-0.00027169).','.($row['lat']+0.000486).',0 '
				.($row['lng']-0.00056495).','.($row['lat']-0.000054).',0 '
				.$row['lng'].','.$row['lat'].',0 ';
		//$coorStr = '0.1105855171821202,49.52564189740646,0 0.1103138214001675,49.52612762237801,0 0.1100205604428695,49.52558762241633,0 0.1105855171821202,49.52564189740646,0'
	}
	
	
	$nodePlace = $dom->createElement('Placemark');
	// Crée un attribut id et attribuez-lui la valeur de la colonne id.
	$nodePlace->setAttribute('id_lieu', 'placemark' . $row['id_lieu']);
	// Créer nom, éléments de description, attributs, et adresse
	$nodeName = $dom->createElement('name',htmlentities($row['id_lieu']));
	$nodePlace->appendChild($nodeName);

	$nodeDesc = $dom->createElement('description', $row['adresse']);
	$nodePlace->appendChild($nodeDesc);

	$nodeStyleUrl = $dom->createElement('styleUrl', '#' . $row['type'] . 'm_ylw-pushpin');
	$nodePlace->appendChild($nodeStyleUrl);

	$nodePoly = $dom->createElement('Polygon');
		$nodeTessa = $dom->createElement('tessellate', '1');
		$nodeOuter = $dom->createElement('outerBoundaryIs');
			$nodeLineRing = $dom->createElement('LinearRing');
				$nodeCoor = $dom->createElement('coordinates', $coorStr);
			$nodeLineRing->appendChild($nodeCoor);
		$nodeOuter->appendChild($nodeLineRing);
	$nodePoly->appendChild($nodeOuter);
	$nodePoly->appendChild($nodeTessa);

	$nodePlace->appendChild($nodePoly);

	return $nodePlace;
}

$kmlOutput = $dom->saveXML();
header('Content-type: application/vnd.google-earth.kml+xml'); //mon Php a comme header un content-type. Ce qu'on génère comme xml est du kml.
echo $kmlOutput;





}


?>