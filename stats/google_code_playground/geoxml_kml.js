var map;
var geoXml; 
var toggleState = 1;

function initialize() {
  if (GBrowserIsCompatible()) {
    geoXml = new GGeoXml("http://code.google.com/apis/kml/documentation/KML_Samples.kml");
    map = new GMap2(document.getElementById("map_canvas"));
    map.setCenter(new GLatLng(37.42228990140251, -122.0822035425683), 16);
    map.addControl(new GLargeMapControl());
    map.addControl(new GLargeMapControl());
    map.addOverlay(geoXml);
  }
} 

function toggleMyKml() {
  if (toggleState == 1) {
    map.removeOverlay(geoXml);
    toggleState = 0;
  } else {
    map.addOverlay(geoXml);
    toggleState = 1;
  }
}​