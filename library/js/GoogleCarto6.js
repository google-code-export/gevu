//<![CDATA[

var mapDiv;
var map;
var mgr;
var eventDiv;
var tagDiv;
var tagInput;
var marker_id = 0;
var marker_info = new Array;
var trace = false;
var MiniCarte = false;

/* Create our "tiny" marker icon
var icon = new GIcon();
icon.image = "http://ns26212.ovh.net/img/marker.png";
icon.iconSize = new GSize(10, 10);
icon.iconAnchor = new GPoint(6, 20);
icon.infoWindowAnchor = new GPoint(5, 1);
*/

function showAddress(address, id, query) {
  if (geocoder) {
	  var lat;
	  var lng;
	geocoder.getLatLng(
	  address,
	  function(point) {
		if (!point) {
		  alert(address + " not found");
		  document.getElementById('err'+id).innerHTML += " pas trouvée";
		} else {
		  lat = point.lat();
		  lng = point.lng();
		  url = pathRoot+"ExecDonneeCarto.php?site="+site+"&f=sauve_marker&action=Modifier&id="+id+"&lat="+lat+"&lng="+lng+"&zoommin=10&zoommax=18&type=Mixte&adresse="+address;
		  GetResult(url, 'err'+id);
		  GetMarkers(id, query);
		  //document.getElementById('result'+id).innerHTML = "trouvée";
		  //AjaxRequest(url,'result'+id);
		ReverceGeocoding(lat, lng);
		}
	  }
	);
	
  }
}

function ReverceGeocoding(lat, lng){
	latlng = new GLatLng(lat, lng);
	geocoder.getLocations(latlng, function(addresses) {
	  //alert(lat+", "+lng+" "+latlng.toUrlValue());
	  if(addresses.Status.code != 200) {
	    alert("impossible de trouver l'adresse de la geolocalisation : " + latlng.toUrlValue());
	  } else { 
	    var result = addresses.Placemark[0];
	    if(document.getElementById('formAdresse'))
			document.getElementById('formAdresse').value = result.address;
	    //map.openInfoWindow(latlng, result.address);
	  }
	});

}

function SauveMarker(id)
{
    //alert("sauvemarker");
	action = window.document.forms["marker"].action.selectedIndex;
    actionInput = window.document.forms["marker"].action.options[action].value;
    if(actionInput=="Voir"){
        url = "vyaep.php?frag="+id;
	    write_line('SauveMarker url ' + url);
        parent.window.location=url;
    }else{
	    url = pathRoot+"ExecDonneeCarto.php?f=sauve_marker&action="+actionInput
		    +"&site="+site
		    +"&id="+id
		    +"&zoommin="+window.document.forms["marker"].zoommin.value
		    +"&zoommax="+window.document.forms["marker"].zoommax.value
		    +"&adresse="+window.document.forms["marker"].adresse.value
		    +"&type="+window.document.forms["marker"].type.value
		    +"&lat="+window.document.forms["marker"].lat.value
		    +"&lng="+window.document.forms["marker"].lng.value;
	    write_line('SauveMarker url ' + url);
		var reponse = GetResult(url);
	    write_line('SauveMarker reponse ' + reponse);
  	}
  	map.closeInfoWindow();
}

function GetMarkers(id, query) {
	var bounds = map.getBounds();
	var southWest = bounds.getSouthWest();
	var northEast = bounds.getNorthEast();

	//pour gérer les fonction vers l'iframe
	//= pas de string comme paramètre
	if(query==0)
		query=='adminDon';
	
	mapQuery = query;
	idRub = id;
	if(!alpha)
		alpha=-1;
		
	//calcul de l'url
	url = pathRoot+'ExecDonneeCarto.php?f=get_markers'
		+ '&id=' + id
	    + '&site='+site
		+ '&Alpha=' + alpha
		+ '&MapQuery=' + query
		+ '&zoom=' + map.getZoom()
		+ '&southWestLat=' + southWest.lat()
		+ '&northEastLat=' + northEast.lat()
		+ '&southWestLng=' + southWest.lng()
		+ '&northEastLng=' + northEast.lng();
	//gestion des thémes
	if(query=='themes') {
		//gestion de la coche des thémes
		i = document.themesTopos.elements.length;
		for (var m=0;m<i;m++)
		{
			if(document.themesTopos.elements[m].checked==true)
				themes = themes + document.themesTopos.elements[m].value + ',';
		}
		themes = themes.substring(0,themes.length-1);
		url = url + '&themes=' + themes
	}
	if(query=='theme')
		url = url + '&theme=' + mot;
	if(query=='adminDon')
		query='admin';
	//alert('GoogleCarto3.js:GetMarkers:query='+query);

	//supprime tout les markers
	map.clearOverlays();
	//map.removeOverlay();
	//var mgrOptions = { borderPadding: 10, maxZoom: 18, trackMarkers: true };
	//mgr = new GMarkerManager(map, mgrOptions);

	//affiche le loading
	//loadDiv = document.getElementById("loadDiv");
	//loadDiv.style.display = 'block';

	//attent la réponse
	write_line('GetMarkers url ' + url);
	var xml = GetXmlUrlToDoc(url);
	
	//cache le loading
	//loadDiv.style.display = 'none';
	//eval(http.responseText);

	//traitement de la réponse
	write_line('GetMarkers xml='+xml,'green');

	for (var i = 0; i < xml.firstChild.childNodes.length; i++){

		var cd = xml.firstChild.childNodes[i];
			
		write_line('GetMarkers i = ' + i);
		var lat = cd.getAttribute("lat");
		var lng = cd.getAttribute("lng");
		var n = i;//cd.getAttribute("i");
		var id_rubrique = cd.getAttribute("idRub");
		var titre = cd.getAttribute("titre");
		var zoommin = cd.getAttribute("zoommin");
		var zoommax = cd.getAttribute("zoommax");
		var adresse = cd.getAttribute("adresse");
		var cartotype = cd.getAttribute("cartotype");
		var urlKml = cd.getAttribute("kml");
		var idDoc = cd.getAttribute("idDoc");
		
		var urlGeoRss = false;
		if(cd.childNodes[0].textContent)urlGeoRss = cd.childNodes[0].textContent;
		
		if(!zoommin)
			zoommin = 13;
		else
			zoommin = parseInt(zoommin);

		write_line('Markers INFO ' + query + ' | ' + n + ' | ' + id_rubrique + ' | ' + lat + ' | ' + lng + ' | ' + zoommin + ' | ' + zoommax + ' | ' + adresse);
		//construction de la carte suivant le type de requête
		switch (query)
		{
		  case 'admin':
				if(n==0) {
					//alert('Markers INFO ' + query + ' | ' + n + ' | ' + id_rubrique + ' | ' + lat + ' | ' + lng + ' | ' + zoommin + ' | ' + zoommax + ' | ' + adresse);
					n++;
					if(lat!='')
						var point = new GLatLng(lat,lng);
					else
						var point = map.getCenter();
					write_line('admin - point ' + point, 'red');
					var marker = new GMarker(point, {draggable: true});
					map.addOverlay(marker);
					marker.marker_id = n;
					for (var eventName in markerEvents)
					{
						addShowEvent(marker, 'marker[n]<', eventName, 'green', markerEvents[eventName]);
					}
					write_line('added overlay ' + marker.marker_id, 'red');
					//contenu admin
					contenu_topic = '<form name="marker" ><table >';
					contenu_topic += '<tr><td><input type="hidden" name="id" value="' + id_rubrique + '" /></td></tr>';
					contenu_topic += '<tr><td>Titre : ' + titre + '</td></tr>';
					contenu_topic += '<tr><td>Lat : <input type="text" name="lat" value="' + point.lat() + '" /></td></tr>';
					contenu_topic += '<tr><td>Lng : <input type="text" name="lng" value="' + point.lng() + '" /></td></tr>';
					contenu_topic += '<tr><td>zoom min : <input type="text" name="zoommin" value="' + zoommin + '" /></td></tr>';
					contenu_topic += '<tr><td>zoom max : <input type="text" name="zoommax" value="' + zoommax + '" /></td></tr>';
					contenu_topic += '<tr><td>Type : <input type="text" name="type" value="' + cartotype + '" /></td></tr>';
					contenu_topic += '<tr><td>Adresse : <input id="formAdresse" type="text" name="adresse" value="' + adresse + '" /></td></tr>';
					contenu_topic += '<tr><td>Action : <select name="action">';
					contenu_topic += '<option value="Modifier">Modifier</option>';
					contenu_topic += '</select></td></tr>';
					contenu_topic +="<tr><td>";
					contenu_topic +="<input type='button' name='GL' value='Geolocaliser' onclick=\"showAddress(window.document.marker.adresse.value," + id_rubrique + ",'" + query + "')\" />";
					contenu_topic += '<input type="button" name="Submit" value="Sauver" onclick="SauveMarker(' + id_rubrique + ')" />';
					contenu_topic +="</td></tr>";
					contenu_topic +="<tr><td>";
					contenu_topic +="<input type='button' name='kml' value='Ajout KML(Z)' onclick=\"GetFichierKml('" + idDoc + "')\" />";
					contenu_topic +="<input type='button' name='kml' value='Ajout GeoRSS' onclick=\"AddGeoRSS('" + id_rubrique + "')\" />";
					contenu_topic +='<div id="result' + id_rubrique + '" /><div id="err' + id_rubrique + '" /><div id="' + idDoc + '" />';
					contenu_topic +="</td></tr>";
					contenu_topic += '</table></form>';
					//GESTION DU DRAG & DROP
					GEvent.addListener(marker, "dragstart", function() {
					  map.closeInfoWindow();
					  });
					GEvent.addListener(marker, "dragend", function() {
					  marker.openInfoWindowHtml(contenu_topic);
					  p = marker.getPoint();
					  window.document.forms["marker"].lat.value=p.lat();
					  window.document.forms["marker"].lng.value=p.lng();
					  window.document.forms["marker"].zoommin.value=map.getZoom();
					  window.document.forms["marker"].zoommax.value=17;
					  window.document.forms["marker"].type.value=map.getCurrentMapType().getName();
					  ReverceGeocoding(p.lat(), p.lng());
					  });
					//construction des onglets
					var infoTabs = new Array(new GInfoWindowTab("Topic",contenu_topic));
					marker_info[n]=infoTabs;
					//marker.openInfoWindowTabsHtml(infoTabs);
					//attribut le type de carte
					GetMapType(cartotype);
					//affiche le kml du granulat
					GetRubKml(id_rubrique,query,urlKml);
					//affiche le geoRss
					if(urlGeoRss)GetGeoXml(urlGeoRss);
					//vérifie s'il faut afficher les kmls supplémentaires
					if(cd.childNodes.length>0){
						//window.document.getElementById("BassinGare").style.visibility='visible';
						for (var j = 0; j < cd.firstChild.childNodes.length; j++){
							var kmlSup = cd.firstChild.childNodes[j];
							GetRubKml("","",kmlSup.getAttribute("url"));
						}
					}
					
					write_line('zoommin ' + zoommin, 'red');
					
					map.setCenter(new GLatLng(lat, lng), zoommin);
					
					//map.setZoom(zoommin);
				}
		   break;

		  case 'idFiche':
				if(lat!='') {
					var point = new GLatLng(lat,lng);
					//centre la carte
					map.setCenter(new GLatLng(lat, lng), zoommin);
					//attribut le type de carte
					GetMapType(cartotype);
					//charge le kml
					GetRubKml(id_rubrique,query,urlKml);
				}
		    break;
		}
	}
	

	/*************************fin *****************************/
	//mgr.refresh();
	//bug ie sur chargement kml
   	//if(window.XMLHttpRequest){ // Firefox
		//charge le kml du département
		//var geoXmlDep = new GGeoXml("http://ns39182.ovh.net/kml/dordogne.kml");
		//write_line('geoXml: '+geoXml,'red');
	    //map.addOverlay(geoXmlDep);
	//}

	//write_line('mgr.getMarkerCount('+map.getZoom()+'): '+mgr.getMarkerCount(map.getZoom()), "red");

}

function GetInfoTab(image,lien,titre,localisation,txt){
	//contenu topic
	var contenu_topic ='<div class="BlocGranulatGM">';
	contenu_topic += '<div class="BlocGranulatImg">'+image+'</div>';
	contenu_topic += '<div class="BlocGranulatTopic"><a href="'+lien+'">'+titre+'</a></div>';
	contenu_topic += '<div class="BlocGranulatNotice">'+txt+'</div>';
	contenu_topic += '<div class="BlocGranulatTopos">'+localisation+'</div>';
	contenu_topic += '</div>';
	//fin contenu topic
	//construction des onglets
	var infoTabs = new Array(new GInfoWindowTab("Infos",contenu_topic));
	if(thematique!='')
		infoTabs.push(new GInfoWindowTab("Thematique",thematique));
	if(fam!='')
		infoTabs.push(new GInfoWindowTab("Famille",fam));
	
	return infoTabs;

}

function GetGeoXml(url) {
  try {
    var geoXml = new GGeoXml(url);
    map.addOverlay(geoXml);
   } catch(ex2){alert("erreur:GetGeoXml:"+ex2);}
} 

function GetRubKml(id, query, url){
  try {
	var kml = "";
	var geoXmlRub ;
	//bug ie sur chargement kml
   	//if(window.XMLHttpRequest){
		//calcul de l'url un flux kml
	//url = pathRoot+'ExecDonneeCarto.php?f=get_rub_kml&site='+site+'&id='+id+'&query='+query;
	//url = 'http://localhost/onadabase/kml/samTest.kmz';
	//alert("GetRubKml:"+url);
	if(url){
		var urls = url.split('*');
		if(urls.length<2) {
			//alert("GetRubKml::"+url);
			geoXmlRub = new GGeoXml(url);
			map.addOverlay(geoXmlRub);
			write_line('GetRubKml url ' + url,'red');
		} else {
			for (i=0; i<urls.length; i=i+1) {
				if(urls[i]!=""){
					geoXmlRub = new GGeoXml(urls[i]);
					map.addOverlay(geoXmlRub);
					write_line('GetRubKml urls'+i+' ' + urls[i],'red');
				}
			}
		}	
	}
   } catch(ex2){alert("erreur:"+ex2);}
}

function GetMapType(type){
	if(type=="Mixte")
		map.setMapType(G_HYBRID_MAP);
	else
		map.setMapType(G_SATELLITE_MAP);
	if(type=="Plan")
		map.setMapType(G_NORMAL_MAP);
}

// 2.43 events strings
//	Event Name		Map	Mrk	Ply	Inf	Drg	Mty	Tly	Cpy
//	===============	===	===	===	===	===	===	===	===
//	addmaptype		1	-	-	-	-	-	-	-
//	addoverlay		1	-	-	-	-	-	-	-
//	clearlisteners	1	1	-	-	-	-	-	-
//	clearoverlays	0	-	-	-	-	-	-	-
//	click			2	0	-	-	1	-	-	-
//	closeclick		-	-	-	0	-	-	-	-
//	contextmenu		-	-	-	-	-	-	-	-
//	dblclick		2	0	-	-	1	-	-	-
//	drag			0	-	-	-	1	-	-	-
//	dragend			0	-	-	-	1	-	-	-
//	dragstart		0	-	-	-	1	-	-	-
//	error			-	-	-	-	-	-	-	-
//	infowindowclose	0	1	-	-	-	-	-	-
//	infowindowopen	0	1	-	-	-	-	-	-
//	load			-	-	-	-	-	-	-	-
//	maptypechanged	0	-	-	-	-	-	-	-
//	mousedown		-	0	-	-	1	-	-	-
//	mousemove		1	-	-	-	-	-	-	-
//	mouseout		1	0	-	-	-	-	-	-
//	mouseover		1	0	-	-	-	-	-	-
//	mouseup			-	0	-	-	1	-	-	-
//	move			0	-	-	-	-	-	-	-
//	moveend			0	-	-	-	-	-	-	-
//	movestart		0	-	-	-	-	-	-	-
//	newcopyright	-	-	-	-	-	0	0	0
//	remove			0	0	0	-	-	-	-	-
//	removemaptype	-	-	-	-	-	-	-	-
//	removeoverlay	1	-	-	-	-	-	-	-
//	resize			0	-	-	-	-	-	-	-
//	unload			-	-	-	-	-	-	-	-
//	zoom			2	-	-	-	-	-	-	-
//	zoomend			-	-	-	-	-	-	-	-
//
var mapEvents = {
	click:2,closeclick:0,dblclick:2,closeclick:0,
	move:0,movestart:0,moveend:0,
	zoom:2,zoomend:2,
	resize:0,contextmenu:0,load:0,error:0,
	maptypechanged:0,newcopyright:0,
	infowindowopen:0,infowindowclose:0,
	addoverlay:1,removeoverlay:1,clearoverlays:0,
	mouseup:1,mousedown:1,mousemove:1,mouseover:1,mouseout:1,
	dragstart:0,dragend:0,drag:0,
	addmaptype:1,removemaptype:1,
	remove:0,clearlisteners:1
	};
var markerEvents = {
	click:0,dblclick:0,
	infowindowopen:1, infowindowclose:1,closeclick:0,
	mouseup:0,mousedown:0,mouseout:0,mouseover:0,
	load:0,error:0,remove:0,clearlisteners:1
	};
var infowindowEvents = { closeclick:0,
	mouseup:1,mousedown:1,mousemove:1,mouseover:1
	};
var map_click_behavior = "create";
var marker_click_behavior = "open";
var marker_mouseover_behavior = "mouseover";

var showMouseMove = false;
function map_click_changed(s) {

	alert("map_click_changed");
	write_line('map_click_changed: '+s, "red");
	map_click_behavior = s;
}
function marker_click_changed(s) {

	alert("marker_click_changed");
	write_line('marker_click_behavior: '+s, "#ff0000");
	marker_click_behavior = s;
}
GMarker.prototype.toString = function() {
	if (typeof(this.marker_id) != 'undefined') {
		return 'GMarker(#'+this.marker_id+')';
	}
	else {
		return 'GMarker(?)';
	}
}
function write_line(s, color) {

	//alert("write_line");
	try {
		color = color || 'black';
		if(trace)GLog.write( s, color );
	}
	catch(e) {
		alert("exception in write_line\n"+e);
	}
}
function addTag() {

	alert("addTag");
	var tagStr = tagInput.value;
	if (tagStr) {
		write_line(tagStr);
		tagInput.value = "";
	}
}
function instanceOf(object, constructorFunction) {

	alert("instanceof");
	while (object != null) {
		if (object == constructorFunction.prototype) {
			return true
		}
		object = object.__proto__;
	}
	return false;
}
function toString(a) {

	//alert("tostring");
	var s = 'oops';
	try {
		if (typeof(a) == 'undefined') s = 'undefined';
		else if (typeof(a) == 'null') s = 'null';
		else {
			//s = a.toString();
			s = ''+a;
		}
	}
	catch(e) {
		var a_to = typeof(a);
		var a_pto = '';
		if (a_to == 'object') {
			pto = typeof(a.prototype);
		}
		alert('exception in toString:'+e+'\ntypeof(a)='+a_to+'\ntypeof(a.prototype)='+a_pto);
		s = 'exception in toString';
	}
	return s;
}
function showEvent(what, which, color, nargs, a, b) {

	//alert("showEvent");
	if (which == 'mousemove' && !showMouseMove) return;
	try {
		var argStr = '';
		switch(nargs){
		case 2:
			argStr = toString(a) + ',' + toString(b);
			break;
		case 1:
			argStr = toString(a);
			break;
		case 0:
			break;
		default:
			alert('showEvent: '+what+','+which+','+color+' invalid nargs:'+nargs);
		}
	    var eventStr = what + '-- ' + which + '(' + argStr + ')';
        write_line('showEvent: '+eventStr, color);
	}
	catch(e) {
		write_line('exception in showEvent('+what+','+which+'):'+e,'red');
	}
}
function addShowEvent(base, baseName, eventName, color, nargs) {
	//alert("addshowEvent");
	try {
		var ev;
		switch(nargs) {
		case 0:
			ev = GEvent.addListener(base, eventName, function() {
				showEvent(baseName, eventName, color, 0);
			});
			break;
		case 1:
			ev = GEvent.addListener(base, eventName, function(a) {
				showEvent(baseName, eventName, color, 1, a);
			});
			break;
		case 2:
			ev = GEvent.addListener(base, eventName, function(a,b) {
				showEvent(baseName, eventName, color, 2, a, b);
			});
			break;
		default:
			alert('addShowEvent: '+baseName+','+eventName+','+color+' invalid nargs:'+nargs);
			break;
		}
		//return ev;
	}
	catch(e) {
		write_line('exception in addShowEvent:'+e,'red');
	}
}
function marker_click_handler(marker) {
	//alert("map_click_handler");
	write_line('in marker_click_handler:'+marker+' _ ','red');
}

function map_click_handler(overlay,point) {
	//alert("map_click_handler");
	//try {
	//	showEvent('map <', 'click', 'black', 2, overlay, point);
	//}
	//catch(e) {
	//	write_line('exception1 in map_click_handler:'+e,'red');
	//}
	//alert(marker_click_behavior);
	write_line('in map_click_handler:'+point+' _ '+overlay,'red');
	try{
		if (overlay) {
			if (marker_click_behavior=='delete') {
    			map.removeOverlay(overlay);
				write_line('removed overlay ' + overlay.marker_id, 'red');
			}
			else if (marker_click_behavior=='open') {
				var htmlStr;
				if (typeof overlay.marker_id != 'undefined') {
					if (typeof marker_info[overlay.marker_id] == 'string') {
						htmlStr = marker_info[overlay.marker_id];
						write_line('htmlStr '+htmlStr, 'red');
						write_line('htmlStr.substr(0, 7) '+htmlStr.substr(0, 7), 'red');
						if(htmlStr.substr(0, 7)=="http://"){
							window.top.document.location.replace(htmlStr);
						}
					}else{
						overlay.openInfoWindowTabsHtml(marker_info[overlay.marker_id]);
						//overlay.openInfoWindowTabs(marker_info[overlay.marker_id])
					}
					write_line('opened info window for '+marker_info[overlay.marker_id], 'red');
				} else {
					write_line('marker_id unefined ', 'red');
				}
			}
		}
		else if (point) {
			if (map_click_behavior=='create') {
				/*
				var marker = new GMarker(point,icon);
				map.addOverlay(marker);
				marker.marker_id = marker_info.length;
				for (var eventName in markerEvents) {
					addShowEvent(marker, 'marker['+marker.marker_id+'] <', eventName, 'green', markerEvents[eventName]);
				}
				htmlStr = '<form name="marker" ><table >';
				htmlStr += '<tr><td><input type="hidden" name="id" value="' + frag + '" /></td></tr>';
				htmlStr += '<tr><td>Titre : <input type="text" name="titre" /></td></tr>';
				htmlStr += '<tr><td>Localisation : <input type="text" name="loca" value="' + point.toUrlValue() + '" /></td></tr>';
				htmlStr += '<tr><td>Date : <input type="text" name="date" /></td></tr>';
				htmlStr += '<tr><td>Action : <select name="action">';
				htmlStr += '<option value="Ajouter">Ajouter</option>';
				htmlStr += '<option value="Supprimer">Supprimer</option>';
				htmlStr += '<option value="Modifier">Modifier</option>';
				htmlStr += '</select></td></tr>';
				htmlStr += '<input type="hidden" name="zoom" value="' + map.getZoom() + '" />';
				htmlStr += '<input type="hidden" name="lat" value="' + point.lat() + '" />';
				htmlStr += '<input type="hidden" name="lng" value="' + point.lng() + '" />';
				htmlStr += '<input type="hidden" name="id_marker" value="-1" />';
				htmlStr += '<tr><td><input type="button" name="Submit" value="Executer" onclick="SauveMarker(' + frag + ')" />';
				htmlStr += '<embed src="../ChaoticumPapillonae/CreaPapiDyna.php" name="SVG2"';
				htmlStr += 'width="80" height="80" type="image/svg-xml" pluginspage="http://www.adobe.com/svg/viewer/install/"></td>';
				htmlStr += '</tr></table></form>';
				marker_info[marker.marker_id]=[new GInfoWindowTab("Actions", htmlStr)];
				write_line('added overlay ' + marker.marker_id, 'red');
				*/
			}
		}
	}
	catch(e) {
		write_line('exception2 in map_click_handler:'+e,'red');
	}
}
function beep() {
	alert('beep');
}

function initMap() {
	// Event Listeners
	//
	// Event listeners are registered with =GEvent.addListener=. In this example,
	// we echo the lat/lng of the center of the map after it is dragged or moved
	// by the user.
	write_line('initMap', 'red');

	if (GBrowserIsCompatible()) {
		map = new GMap2(mapDiv);
        geocoder = new GClientGeocoder();
		
		if(MiniCarte)
			//zoom avec mollette
			map.enableScrollWheelZoom();

		if(!MiniCarte) {
			map.addControl(new GLargeMapControl());
			map.addControl(new GMapTypeControl());
		}

		if(defzoom)
			map.setCenter(new GLatLng(deflat, deflng), defzoom);
		else
			map.setCenter(new GLatLng(deflat, deflng));
		write_line('map.setCenter ' + deflat +', '+deflng +', '+defzoom);

		for (var eventName in mapEvents) {
			addShowEvent(map, 'map <', eventName, 'black', mapEvents[eventName]);
		}

		/*
		var copyCollection = new GCopyrightCollection('IGN:SCAN25');
		var copyright = new GCopyright(1, new GLatLngBounds(new GLatLng(-90, -180), new GLatLng(90, 180)), 0, "©2007 IGN");
		copyCollection.addCopyright(copyright);

		var tilelayers = [new GTileLayer(copyCollection, 0, 17)];
		tilelayers[0].getTileUrl = CustomGetTileUrl;


		var custommap = new GMapType(tilelayers, new GMercatorProjection(18), "IGN:SCAN25", {errorMessage:"No chart data available"});
		map.addMapType(custommap);
		*/

		//récupère les coordonnées des markers à afficher par rapport à l'identifiant et la requête
		GetMarkers(idRub,mapQuery);
		
		GEvent.addListener(map, 'click', map_click_handler);
		GEvent.addListener(map, 'moveend', function() {write_line('moveend: '+map.getCenter(),'#0000ff')});
		GEvent.addListener(map, 'dragend', function() {
			write_line('dragend: '+map.getCenter(),'#0000ff');
			if(mapQuery=='themes'){
				//recherche les nouveaux markers
				GetMarkers(idRub,mapQuery);
			}//else
				//charge le kml de la commune
				//GetRubKml(idRub,"commune");
		});
		GEvent.addListener(map, 'zoom', function(a,b) {
			write_line('zoom: '+map.getZoom(),'#0000ff')
			}
		);
		GEvent.addListener(map, 'zoomend', function(a,b) {
			write_line('zoomend: '+map.getZoom(),'#0000ff');
			//alert(map.getZoom());
			if(mapQuery=='themes'){
				//recherche les nouveaux markers
				GetMarkers(idRub,mapQuery);
			}//else
				//charge le kml de la commune
				//GetRubKml(idRub,"commune");
		});
		GEvent.addListener(map, 'maptypechanged', function() {write_line('maptype: '+map.getCurrentMapType().getName(),'#0000ff')});

	}


}
function CustomGetTileUrl(a,b) {
		var z = b;
		var f = "";
		//var f = "/maps/?x="+a.x+"&y="+a.y+"&zoom="+z;
		//var f = "../MesGoogleCarte/IMG_2728.JPG";
		//vérifie la première image
		f = "MesGoogleCarte/map/SC25_TOUR_L93_"+a.x+"_"+a.y+"_"+z+".gif";
		write_line('CustomGetTileUrl f ' + f);
		//alert(z+" : "+f);
		return f;
	 }


function initPage() {
	loadTime = new Date();
	//eventDiv = document.getElementById("event_div");
	mapDiv = document.getElementById("map");
	//document.getElementById("map_click_create").click();
	//document.getElementById("marker_click_open").click();
	write_line('document < - onload()', 'red');
	initMap();
}
//]]>
