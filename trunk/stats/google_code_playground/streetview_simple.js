<html>
	<head>
		<title>Code Playground de Geo XML en KML</title>
	</head>
	
	<body>
		<script type="text/javascript">
		
function initialize() {
  var fenwayPark = new GLatLng(42.345573,-71.098326);
  panoramaOptions = { latlng:fenwayPark };
  myPano = new GStreetviewPanorama(document.getElementById("pano"), panoramaOptions);
  GEvent.addListener(myPano, "error", handleNoFlash);
}

function handleNoFlash(errorCode) {
  if (errorCode == FLASH_UNAVAILABLE) {
    alert("Error: Flash doesn't appear to be supported by your browser");
    return;
  }
}  ​
		</script>
	</body>
</html>