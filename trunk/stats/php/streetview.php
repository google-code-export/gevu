<?php

require_once 'codes.php';
//$_GET["idLieu"]=3;
if(isset($_GET["idLieu"])){
	$idLieu = $_GET["idLieu"];
	$query = 'SELECT lib, lat, lng, heading, pitch, zoom_cell, adresse
	FROM gevu_geos g
	INNER join gevu_lieux l ON g.id_lieu = l.id_lieu
	WHERE l.id_lieu = '.$_GET['idLieu'];
	$result = mysql_query($query);
	if (!$result)
	{
		die('Invalid query: ' . mysql_error());
	}
	$rGeo = @mysql_fetch_assoc($result);
}else{
	$rGeo['lat']=49.489982;
	$rGeo['lng']=0.159880;
	$rGeo['heading']=-13.49416165586274;
	$rGeo['pitch']=-6.302286124974989;
	$rGeo['zoom_cell']=1;
}
?>
<html>
	<head>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
		<meta charset="UTF-8" />
		<title>Streetview</title>
		<style type="text/css">
			html {
				height: 100%;
				width: 100%
			}
			body {
				height: 100%;
				margin: 0;
				padding: 0
			}
			#EmplacementDeMaCarte, #EmplacementPanoramiqueStreetView {
				height: 50%;
				width: 50%
			}
			#EmplacementDeMaCarte {
				align:left;
				position: absolute
			}
			#EmplacementPanoramiqueStreetView {
				margin:00px auto 0 auto;
				position: relative;
				float:right;
				width:100%
				margin-left:500px;
				margin-right:-5px;
			}
		</style>
		<script type="text/javascript" src="../js/jquery.min.js"></script>
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
			var idLieu = <?php echo $_GET["idLieu"];?>;
			function initialisation(){
				var centreCarte = new google.maps.LatLng(<?php echo $rGeo['lat'];?>, <?php echo $rGeo['lng'];?>);
				var optionsCarte = {
					zoom: 8,
					center: centreCarte,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				var maCarte = new google.maps.Map(document.getElementById("EmplacementDeMaCarte"), optionsCarte);
				var optionsPanoramiqueStreetView = {
					position: centreCarte,
					pov: {
						heading: <?php echo $rGeo['heading'];?>,
						pitch: <?php echo $rGeo['pitch'];?>,
						zoom: <?php echo $rGeo['zoom_cell'];?>
					}
				};
				var panoramiqueStreetView = new google.maps.StreetViewPanorama(document.getElementById('EmplacementPanoramiqueStreetView'), optionsPanoramiqueStreetView);
				maCarte.setStreetView(panoramiqueStreetView);

				//https://google-developers.appspot.com/maps/documentation/javascript/examples/streetview-events
				google.maps.event.addListener(panoramiqueStreetView, 'pov_changed', function() {
				      var headingCell = document.getElementById('heading_cell');
				      var pitchCell = document.getElementById('pitch_cell');
					  var zoomCell = document.getElementById('zoom_cell');
				      var lat = document.getElementById('lat');
					  var lng = document.getElementById('lng');
				      headingCell.value = panoramiqueStreetView.getPov().heading;
				      pitchCell.value = panoramiqueStreetView.getPov().pitch;
				      zoomCell.value = panoramiqueStreetView.getPov().zoom;
				      lat.value = panoramiqueStreetView.getPov().lat;
				      lng.value = panoramiqueStreetView.getPov().lng;
				  });	

			}
			 google.maps.event.addDomListener(window, 'load', initialisation);
			 
		function addAddressToMap(response) {
		 maCarte.clearOverlays();
		 if (!response || response.Status.code != 200) {
		   alert("Sorry, we were unable to geocode that address");
		 } else {
		   place = response.Placemark[0];
		   point = new GLatLng(place.Point.coordinates[1],
		                       place.Point.coordinates[0]);
		   //marker = new GMarker(point);
		   //maCarte.addOverlay(marker);
		   //marker.openInfoWindowHtml(place.address + '<br>' +
		     //'<b>Pays :</b> ' + place.AddressDetails.Country.CountryNameCode);
		 }
		}

		function showLocation() {
		 var address = document.forms[0].q.value;
		 geocoder.getLocations(address, addAddressToMap);
		}
		
		function findLocation(address) {
		 document.forms[0].q.value = address;
		 showLocation();
		}

		function modifBase() {
		   	var headingCell = document.getElementById('heading_cell');
		   	var pitchCell = document.getElementById('pitch_cell');
		   	var zoomCell = document.getElementById('zoom_cell');
		   	var lat = document.getElementById('lat');
		   	var lng = document.getElementById('lng');
			var p = {"lat":lat.value, "lng":lng.value, "pitch":pitchCell.value, "heading":headingCell.value, "zoom_cell":zoomCell.value, "idLieu":idLieu};
			$.get("MAJ_geos.php", p,
				 function(data){
					result = data;
				 });
		}
		
		</script>
	</head>
	
	<body>
      <p>
        <b>Coordonnées ou adresse :</b>
        <input type="text" name="q" value="<?php echo $rGeo["adresse"];?>" class="address_input" size="40" />
        <input type="submit" name="find" value="Rechercher" />
      </p>
      <p>
        <label>lat</label><input type="text" id="lat" value="<?php echo $rGeo["lat"];?>" />
        <label>lng</label><input type="text" id="lng" value="<?php echo $rGeo["lng"];?>" />
        <label>heading</label><input type="text" id="heading_cell" value="<?php echo $rGeo["heading"];?>" />
        <label>pitch</label><input type="text" id="pitch_cell" value="<?php echo $rGeo["pitch"];?>" />
        <label>zoom</label><input type="text" id="zoom_cell" value="<?php echo $rGeo["zoom_cell"];?>" />
        <input type="submit" name="valider" value="Valider" onclick="modifBase()" />
      </p>
		<table align="left"><div id="EmplacementDeMaCarte"></div></table>
		<table align="right"><div id="EmplacementPanoramiqueStreetView"></div></table>
	</body>
</html>