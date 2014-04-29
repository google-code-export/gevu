/*
 * Copyright (c) 2008-2011 Institut Geographique National France, released under the
 * BSD license.
 */

 /**
 * Property: key
 *
 * The API key to use
 */
const APIkeyDev= "c4p0y3y827jx03whl91uk689";
const APIkeyProd = "pbgl1pc4di7xqdgp64e6deqi";
const APIkey = "uxjsvi48w3yfzgeqop5zr22h";

var flexApp = parent.document.getElementById('diag');

function setGeoParam(params) {
	flexApp.modifLieu(params);
}

var urlPicto = 'http://api.ign.fr/geoportail/api/js/2.0.2/img/marker.png',
	GeoXYFormLon, GeoXYFormLat, Description;

function addMarker(params){
    //[lat, lng, type_carte, zoom_max, kml, idLieu, lib, sw, ne, lat_sv, lng_sv , heading, pitch, zoom_sv]
	//centre la carte
    viewer.getMap().setCenterAtLonLat(params[1], params[0], params[3]);	    
	
	//ajoute un marker
	var vectorLayer = new OpenLayers.Layer.Vector("Overlay");
    var attributes= {
            //sauvegarde du type du picto courant:
            'pictoUrl'       : urlPicto,
            'description': "",
            externalGraphic:urlPicto	
        };
    /*
    var center = new OpenLayers.LonLat(params[1], params[0]).transform(
            new OpenLayers.Projection("EPSG:4326"),
            viewer.getProjectionObject()
        );
    var geo = new OpenLayers.Geometry.Point(params[1], params[0]);
	var feature = new OpenLayers.Feature.Vector(geo, attributes);
	vectorLayer.addFeatures(feature);
	viewer.getMap().addLayer(vectorLayer);	
    updateXYForm(feature);
    viewer.getVariable('selectCntrl').select(feature);
	*/
    
}

/**
 * Property: viewer
 * {<Geoportal.Viewer>} the viewer global instance.
 */
viewer= null;

	/**
	 * Function: initMap
	 * Load the application. Called when all information have been loaded by
	 * <loadAPI>().
	 */
	function initMap() {
	   
		
		
	    // ----- Traduction
	    translate();
	
	    // ----- Options
	    
	    var options= {
	        mode:'normal',
			territory:'FXX',
			proxy:'assets/proxy.php?url='
	    };
	
	    viewer= new Geoportal.Viewer.Default('viewerDiv', OpenLayers.Util.extend(
	        options,
	        // API keys configuration variable set by <Geoportal.GeoRMHandler.getConfig>
	        // variable contenant la configuration des clefs API remplie par <Geoportal.GeoRMHandler.getConfig>
	        window.gGEOPORTALRIGHTSMANAGEMENT===undefined? {'apiKey':APIkey} : gGEOPORTALRIGHTSMANAGEMENT)
	    );
	    if (!viewer) {
	        // problem ...
	        OpenLayers.Console.error(OpenLayers.i18n('new.instance.failed'));
	        return;
	    }
	   
	    // ----- Layers
	    viewer.addGeoportalLayers(['ORTHOIMAGERY.ORTHOPHOTOS','GEOGRAPHICALGRIDSYSTEMS.MAPS',"TRANSPORTNETWORKS.ROADS",
	               				"TRANSPORTNETWORKS.RAILWAYS",
	            				"HYDROGRAPHY.HYDROGRAPHY",
	            				"BUILDINGS.BUILDINGS",
	            				"GEOGRAPHICALNAMES.NAMES"]);	
	    
	    // ----- Autres
		viewer.getMap().setCenterAtLonLat(2.464750,48782749, 10);		
	
		var myStyleMap = new OpenLayers.StyleMap({
			"default": new OpenLayers.Style({
				'pointRadius': 10,
				'fillColor': '#ff0000'
			}),
			"select": new OpenLayers.Style({
				'pointRadius': 15,
				'fillColor': '#ff00ff'
			})
		  });
		
		viewer.getMap().addLayer(
				"KML",
				'GEVU',
				"../../data/lieux/gevu_new/lieux_1334_4fb20048a8966.kml",
				{
					visibility:true,
					styleMap:myStyleMap
				},
				{
					formatOptions:{extractStyles :false}
				}
			);

		
		/*-------------------------------------------ajoute la recherche inverse -------------------------------------*/
		var tbx= viewer.getMap().getControlsByClass('Geoportal.Control.ToolBox')[0];
		var panel= new Geoportal.Control.Panel({
		    div:OpenLayers.Util.getElement(tbx.id+'_search')
		});

		var gazetteer = new Geoportal.Control.LocationUtilityService.ReverseGeocode(
		        new Geoportal.Layer.OpenLS.Core.LocationUtilityService(
		            'PositionOfInterest:OPENLS;ReverseGeocode',
		            {
		                formatOptions: {
		                }
		            }
		        ),{
		            title: 'gpControlLocationUtilityServiceReverseGeocode.title',
		            setZoom: Geoportal.Control.LocationUtilityService.ReverseGeocode.setZoomForBDNyme
		        }
		    );
		panel.addControls([gazetteer]);
		viewer.getMap().addControls([panel]);
		
	/*-------------------------------------------déclaration de vlayer-------------------------------------*/
	var vlayer= new OpenLayers.Layer.Vector('dessin',{
	    externalProjection: OpenLayers.Projection.CRS84.clone(),//projection propre à la couche
	    internalProjection: viewer.getMap().getProjection(),//projection de la carte
	    displayInLayerSwitcher:false,//ne pas afficher dans le gestionnaire des couches
	    supportedFormats:{// formats d'enregistrement (ici uniquement kml)
	        kml:{
	            formatClass: OpenLayers.Format.KML,
	            options:{
	                mime: 'application/vnd.google-earth.kml'
	            }
	        }
	    },
	    supportedProjections:{
	    	kml:[
	    	     'CRS:84'
	    	     ]
	    },
	    styleMap: new OpenLayers.StyleMap({
	        // par défaut: le dessin avec le picto sélectionné
	        "default"   : new OpenLayers.Style(
	            OpenLayers.Util.applyDefaults({
	                'externalGraphic': urlPicto, //"${getUrl}",
	                'graphicOpacity' : 1.0,
	                'graphicWidth'   : 21,
	                'graphicHeight'  : 25
	            }, OpenLayers.Feature.Vector.style["default"]),
	            {
	                context : {
	                    getUrl: function(feature){
	                        var url=feature.attributes.pictoUrl;
	                        return url;
	                    }
	                }
	            }
	        ),
	        // temporaire : on n'affiche rien !
	        "temporary" : new OpenLayers.Style(
	            OpenLayers.Util.extend({
	                display:'none'
	            }, OpenLayers.Feature.Vector['temporary'])
	        ),
	        // sélectionné : le dessin avec le marqueur rouge
	        "select"    : new OpenLayers.Style(
	            OpenLayers.Util.applyDefaults({
	                'externalGraphic': urlPicto,
	                'graphicOpacity' : 0.8,
	                'graphicWidth'   : 21,
	                'graphicHeight'  : 25
	            }, OpenLayers.Feature.Vector.style["select"])
	        )},
	        {
	            extendDefault:false
	        }
	    ),
	    eventListeners:{
	        "featureunselected": function(e) {
	            if (e.feature.style) {
	                delete e.feature.style;
	                e.feature.style= null;
	            }
	            e.feature.attributes.pictoUrl=urlPicto;
	
	            this.drawFeature(e.feature,'default');
	        },
	        "beforefeatureadded": function(e) {
	            //this===vlayer===e.feature.layer !
	            //désélection :
	            viewer.getVariable('selectCntrl').unselectAll();
	        },
	        "featureselected": function(e) {
	            //this===vlayer===e.feature.layer !
	            // destruction du style pour utiliser le style de la couche en mode "sélectionné"
	
	            if (e.feature.style) {
	                delete e.feature.style;
	                e.feature.style= null;
	            }
	            this.drawFeature(e.feature,'select');
	            //rafraichissement du formulaire de saisie :
	            updateXYForm(e.feature);
	        }
	    }
	});
	viewer.getMap().addLayer(vlayer);
	viewer.setVariable('points', vlayer);

	/*-------------------------------------------Controle pour la saisie d'objets(ctrl+mousedown)-------------------------------------*/
	var draw_feature= new OpenLayers.Control.DrawFeature(
	    vlayer,
	    OpenLayers.Handler.Point,
	    {
	        autoActivate:true,
	        callbacks:{
	            done: function (geometry) {
	                //this.layer=== var vlayer= viewer.getVariable('points');
	                var attributes= {
	                    //sauvegarde du type du picto courant:
	                    'pictoUrl'       : urlPicto,
	                    'description': ""
	                };
	                // Creer le nouveau point
	                var feature= new OpenLayers.Feature.Vector(geometry, attributes);
	                this.layer.addFeatures([feature]);
	                updateXYForm(feature);
	                viewer.getVariable('selectCntrl').select(feature);
	            }
	        },
	        handlerOptions:{
	            keyMask: OpenLayers.Handler.MOD_CTRL
	        }
	    }
	);
	viewer.getMap().addControl(draw_feature);
		

	/*-------------------------------------------Controle pour le deplacement----------------------------------*/
	var drag_control= new OpenLayers.Control.DragFeature(
	    viewer.getVariable('points'),
	    {
	        onDrag:dragPoi,
	        onComplete:updateXYForm
	    }
	);
	viewer.getMap().addControl(drag_control);
	drag_control.activate();


	/*-------------------------------------------Controle pour la selection d'objets----------------------------------*/
	var select_feature= new OpenLayers.Control.SelectFeature(
	    viewer.getVariable('points'),
	    {
	        clickout: false,
	        toggle: true,
	        multiple: false,
	        hover: false,
	        toggleKey: null, // ctrl key removes from selection
	        multipleKey: null, // shift key adds to selection
	        box: false
	    }
	);
	viewer.getMap().addControl(select_feature);
	select_feature.activate();
	viewer.setVariable('selectCntrl', select_feature);
}

	/*-------------------------------------------mise à jour du formulaire-------------------------------------------------------*/
	function updateXYForm (feature, pix) {
	    if (!feature || !feature.geometry  || !feature.geometry.x || !feature.geometry.y) {
	        return;
	    }
	
	    var pt= feature.geometry.clone();
	    if (pt) {
	        pt.transform(viewer.getMap().getProjection(),feature.layer.externalProjection);
	
	        GeoXYFormLon = pt.x;
	        GeoXYFormLat = pt.y;
	        Description = feature.attributes['description'];
	
	        delete pt;
	    }
	}


	function dragPoi (feature, pix) {
	
		viewer.getVariable('selectCntrl').unselectAll();
		viewer.getVariable('selectCntrl').select(feature);
		updateXYForm (feature, pix);
	}	
/**
 * Function: loadAPI
 * Load the configuration related with the API keys.
 * Called on "onload" event.
 * Call <initMap>() function to load the interface.
 */
function loadAPI() {
    // wait for all classes to be loaded
    // on attend que les classes soient chargées
    if (checkApiLoading('loadAPI();',['OpenLayers','Geoportal','Geoportal.Viewer','Geoportal.Viewer.Default'])===false) {
        return;
    }
    
    Geoportal.GeoRMHandler.getConfig([APIkey], null,null, {
        onContractsComplete: initMap
    });
}

// assign callback when "onload" event is fired
// assignation de la fonction à appeler lors de la levée de l'évènement "onload"
window.onload= loadAPI;
