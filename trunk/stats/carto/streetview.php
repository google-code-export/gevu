<?php
$idLieu = $_GET["idLieu"];

//emplacement pour la requête sql
$lat = 49.489982;
$lng = 0.159880;

/*$query = 'Select lib, lat, lng
from gevu_geos g
inner join gevu_lieux l ON g.id_lieu = l.id_lieu
where l.id_lieu IN ('.$_GET['ids'].')';*/

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
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
			function initialisation(){
				//var centreCarte = new google.maps.LatLng(<?php echo $query;?>);
				//ou var centreCarte = new google.maps.LatLng(<?php echo $query.lat;?>, <?php echo $query.lng;?>);
				var centreCarte = new google.maps.LatLng(<?php echo $lat;?>, <?php echo $lng;?>);
				var optionsCarte = {
					zoom: 8,
					center: centreCarte,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				var maCarte = new google.maps.Map(document.getElementById("EmplacementDeMaCarte"), optionsCarte);
				var optionsPanoramiqueStreetView = {
					position: centreCarte,
					pov: {
						heading: 34,
						pitch: 10,
						zoom: 0.2
					}
				};
				var panoramiqueStreetView = new google.maps.StreetViewPanorama(document.getElementById('EmplacementPanoramiqueStreetView'), optionsPanoramiqueStreetView);
				maCarte.setStreetView(panoramiqueStreetView);

				//https://google-developers.appspot.com/maps/documentation/javascript/examples/streetview-events
				google.maps.event.addListener(panoramiqueStreetView, 'pov_changed', function() {
				      var headingCell = document.getElementById('heading_cell');
				      var pitchCell = document.getElementById('pitch_cell');
					  var zoomCell = document.getElementById('zoom_cell');
				      headingCell.value = panoramiqueStreetView.getPov().heading;
				      pitchCell.value = panoramiqueStreetView.getPov().pitch;
				      zoomCell.value = panoramiqueStreetView.getPov().zoom;
				  });
				  
				/*function valider(){
					if{
						(headingCell.value = panoramiqueStreetView.getPov().heading)
							and (pitchCell.value = panoramiqueStreetView.getPov().pitch)
								and (zoomCell.value = panoramiqueStreetView.getPov().zoom)
									!= 0
					  }
					UPDATE gevu_geos;
				}*/

			}
			 google.maps.event.addDomListener(window, 'load', initialisation);
			 
/*function addAddressToMap(response) {
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
}*/

/*function showLocation() {
 var address = document.forms[0].q.value;
 geocoder.getLocations(address, addAddressToMap);
}*/

/*function findLocation(address) {
 document.forms[0].q.value = address;
 showLocation();
}*/
		</script>
	</head>
	
	<body>
	<form action="#" onsubmit="showLocation(); return false;">
      <p>
        <b>Coordonnées ou adresse :<?php echo $idLieu;?></b>
        <input type="text" name="q" value="" class="address_input" size="40" />
        <input type="submit" name="find" value="Rechercher" />
      </p>
      <p>
        <input type="text" id="heading_cell" value="" />
        <input type="text" id="pitch_cell" value="" />
        <input type="text" id="zoom_cell" value="" />
        <input type="submit" name="valider" value="Valider" />
      </p>
		<table align="left"><div id="EmplacementDeMaCarte"></div></table>
		<table align="right"><div id="EmplacementPanoramiqueStreetView"></div></table>
	</body>
</html>