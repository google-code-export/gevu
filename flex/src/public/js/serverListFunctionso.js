var aktPrjObj = null;

function getBaseLayerIdx() // search for BaseLayer to retrieve the resolutions
{
    var lyrColl = map.layers;
    for(var i=0;i<lyrColl.length;i++)
    {
        if(lyrColl[i].isBaseLayer==true)
            return(i);
    }
}

function findKarten(kid)
{
    var obj=null;

    for(var i=0;i<kartenObj["karten"].length;i++)
    {
        if(kartenObj["karten"][i]["options"].kid == kid)
        {   obj = kartenObj["karten"][i];
            break;
        }
    }

    return obj;
}

function findMap(mid, flag)
{
    var obj = null;
    var idx = null;

    for(var i=0;i<mapObj["map"].length;i++)
    {
        if(mapObj["map"][i].mid == mid)
        {   obj = mapObj["map"][i];
            idx = i;
            break;
        }
    }

    if(flag==1)
        return idx;
    else
        return obj;
}

function findServer(sid)
{
    var obj=null;
    for(var i=0;i<serverObj["server"].length;i++)
    {
        if(serverObj["server"][i]["vendor"].sid == sid)
        {   if(serverObj["server"][i]["vendor"].service == "GOOGLE")
                obj = serverObj["server"][i];                        //Google Layer läßt sich nicht clonen
            else                                                     //ist aber egal, weil sowieso höchsten 1x in map
                obj = clone(serverObj["server"][i]);
            break;
        }
    }

    return obj;
}

function allServer()
{
    var obj=null;
    obj = serverObj["server"];
    return obj;
}

var NL  = "\r\n";
var TAB = "    ";

function testFind()
{
    var dieKarte = findKarten(10100);

    if(dieKarte != null)
    {
        var msg = "Karte:" + dieKarte.title + NL;
        var fidMap = dieKarte["options"]["map"];
        var dieMap = findMap(fidMap);
        if(dieMap != null)
            msg += TAB + "Map:" + dieMap.title + NL;
        else
            msg += TAB + "Map nicht gefunden" + NL;

        var fidServerArr = dieKarte["options"]["layers"];
        for(var i=0;i<fidServerArr.length;i++)
        {   var fidServer = fidServerArr[i];
            var derServer = findServer(fidServer);
            if(derServer != null)
                msg += TAB + TAB + "Server:" + derServer.title + NL;
            else
                msg += TAB + TAB + "Server nicht gefunden" + NL;
        }
    }

    output = msg;
}

function afterMapCreation()
{
    //dummy;
}

function mapInit(NR)
{
var NL  = "\r\n";
var TAB = "    ";

if(typeof map == 'object')
    destroyKarte();

if(NR!=-1)
    var dieKarte = findKarten(NR);
else
    var dieKarte = aktPrjObj[0];

if(dieKarte != null)
{
    var msg = "Karte:" + dieKarte.title + NL;

    lon = parseFloat(dieKarte["options"]["center"][0]);
    lat = parseFloat(dieKarte["options"]["center"][1]);
    zoom = parseInt(dieKarte["options"]["zoom"]);

    msg += lon + TAB + lat + TAB + zoom + TAB + dieKarte["options"]["map"] + NL;

    var fidMap = dieKarte["options"]["map"];
    var dieMap = findMap(fidMap);
    if(dieMap != null)
    {   msg += TAB + "Map:" + dieMap.title + NL;

        dieMap.options.theme = OpenLayers.Theme;
        map = new OpenLayers.Map('map', dieMap.options);
        map.mid = dieMap.mid;
        map.aktLayer = 0;
        document.getElementById("epsg").innerHTML = map.projection;

        if(NR==-1)
        {
            lon  = dieMap["options"]["center"].lon;
            lat  = dieMap["options"]["center"].lat;
            zoom = parseInt(dieMap["options"]["zoom"]);
        }

        var fidServerArr = dieKarte["options"]["layers"];
        for(var i=0;i<fidServerArr.length;i++)
        {   var fidServer = fidServerArr[i];
            var derServer = findServer(fidServer);
            if(derServer != null)
            {   msg += TAB + TAB + "Server:" + derServer.title + NL;
                //if(fidServerArr.length==1)
                if(i==0) // 1.Layer per Definition = Baselayer!!!
                    derServer.options.isBaseLayer = true;
                derServer.vendor.lyrQChecked = [];
                for(var j=0;j<derServer.vendor.lyrQueryable.length;j++)
                    derServer.vendor.lyrQChecked[derServer.vendor.lyrQChecked.length]=0;
                addLayer(derServer);
            }
            else
                msg += TAB + TAB + "Server nicht gefunden" + NL;
        }

        map.addControl(new OpenLayers.Control.PanZoomBar());//, new OpenLayers.Pixel(2,30));
        map.addControl(new OpenLayers.Control.MouseDefaults());
        map.addControl(new OpenLayers.Control.MousePosition({element:document.getElementById("rota"), numdigits:3}));

        map.setCenter(new OpenLayers.LonLat(lon, lat), zoom);

        //bei Einzelfensteranwendung
        try{
            Wunderbar();
        }catch(err){;};

        afterMapCreation();
    }
    else
        msg += TAB + "Map nicht gefunden" + NL;
}
else
    msg += "Karte nicht gefunden" + NL;

}

function addLayer_IE(jsonStr)
{
    var objSrv = eval( '(' + jsonStr + ')' );

    serverObj["server"][serverObj["server"].length] = objSrv["server"][0];
    addLayer(serverObj["server"][serverObj["server"].length-1]);
    try{
        Wunderbar();
    }catch(err){};


}

function addNewLayer(idx)
{
    var theServers = allServer();
    theServers[idx].vendor.lyrQChecked = [];
    for(var j=0;j<theServers[idx].vendor.lyrQueryable.length;j++)
        theServers[idx].vendor.lyrQChecked[j]=0;

    addLayer(theServers[idx]);
    try{
        Wunderbar();
    }catch(err){};
}

function addLayer(obj)
{
    try{
    if( (typeof map.layers[map.aktLayer].isVector != 'undefined') && map.layers[map.aktLayer].isVector == true)
    {
       swapPanel(map.aktLayer);
    }
    else
        if(typeof editControl != 'undefined')
        {   try{
                editControl.activateControl(ctrlNavigation);
                editControl.div.style.display="none";
            }catch(err){};
        }
    }catch(err){};

    obj["options"].displayOutsideMaxExtent=true;

    if(obj.vendor.service == "GOOGLE")
    {   var lyr = new OpenLayers.Layer.Google(obj["title"], obj["options"]);
        lyr.vendor  = obj["vendor"];
        map.addLayer(lyr);
    }
    else if(obj.vendor.service == "BASELAYER")
    {
        var lyr = new OpenLayers.Layer(obj["title"], obj["url"], obj["params"], obj["options"] );
        lyr.vendor  = obj["vendor"];
        lyr.setVisibility(lyr.options.isVisible);
        lyr.isBaseLayer=true;
        map.addLayer(lyr);
    }
    else if(obj.vendor.service == "WMS")
    {   var lyr = new OpenLayers.Layer.WMS(obj["title"], obj["url"], obj["params"], obj["options"] );
        lyr.vendor  = obj["vendor"];
        lyr.vendor.info_format = "text/html";
        lyr.vendor.feature_count = 1;
        lyr.setVisibility(lyr.options.isVisible);
        map.addLayer(lyr);
    }
    else if(obj.vendor.service == "MAPSERVER")
    {   var lyr = new OpenLayers.Layer.MapServer(obj["title"], obj["url"], obj["params"], obj["options"] );
        lyr.vendor  = obj["vendor"];
        lyr.vendor.info_format = "text/html";
        lyr.vendor.feature_count = 1;
        lyr.setVisibility(lyr.options.isVisible);
        map.addLayer(lyr);
    }
    else if(obj.vendor.service == "IMG")
    {
        var tmpFromProjection = new OpenLayers.Projection(obj.options["projection"]);
        var tmpToProjection = new OpenLayers.Projection(map.projection);

        var tmpImgBounds = obj.vendor.imgBounds.clone().transform(tmpFromProjection,tmpToProjection);
        obj["options"].resolutions = map.layers[getBaseLayerIdx()].resolutions;
        obj["options"].maxResolution = obj["options"].resolutions[0];

        var lyr = new OpenLayers.Layer.Image(obj["title"], obj["url"], tmpImgBounds, obj.vendor.imgSize);
        lyr.options = obj["options"];
        lyr.vendor  = obj["vendor"];
        lyr.setVisibility(lyr.options.isVisible);
        lyr.isBaseLayer = lyr.options.isBaseLayer;
        map.addLayer(lyr);
    }
    else if(obj.vendor.service == "WFS")
    {   obj["url"] = setLocalUrl(obj["url"]);
        var lyr = new OpenLayers.Layer.WFS(obj["title"], obj["url"], obj["params"][0], obj["params"][1] );
        lyr.vendor  = obj["vendor"];
        lyr.options = obj["options"];
        lyr.setVisibility(lyr.options.isVisible);
        map.addLayer(lyr);
    }
    else if(obj.vendor.service == "KML" && obj.vendor.parseOptions == "GroundOverlay")
    {
        obj["url"] = setLocalUrl(obj["url"]);
        KML_Request(obj["url"]);
    }
    else if(obj.vendor.service == "GML" || obj.vendor.service == "KML" || obj.vendor.service == "GEOJSON" || obj.vendor.service == "GEORSS" || obj.vendor.service == "WKT")
    {
        if(obj["vendor"].service.toLowerCase() == "kml")
            obj["options"].format = OpenLayers.Format.KML;
        else if(obj["vendor"].service.toLowerCase() == "gml")
            obj["options"].format = OpenLayers.Format.GML;
        else if(obj["vendor"].service.toLowerCase() == "geojson")
            obj["options"].format = OpenLayers.Format.GeoJSON;
        else if(obj["vendor"].service.toLowerCase() == "georss")
            obj["options"].format = OpenLayers.Format.GeoRSS;
        else if(obj["vendor"].service.toLowerCase() == "wkt")
            obj["options"].format = OpenLayers.Format.WKT;

        obj["url"] = setLocalUrl(obj["url"]);

        var lyr = new OpenLayers.Layer.GML(obj["title"], obj["url"], obj["options"]);
        var randomColorStyle = new OpenLayers.Style(OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style["default"]));
        tampla = obj;
        try{
            if(typeof obj.vendor.styleMap != 'undefined')
                lyr.styleMap = obj.vendor.styleMap;
            else if(typeof myStyleMap != 'undefined' && myStyleMap != null)
                lyr.styleMap = myStyleMap;
            else
                lyr.styleMap = new OpenLayers.StyleMap({fillOpacity: 0.5, fillColor: randomColor(), strokeWidth: 3, strokeColor: randomColor(128,128,128, 127,127,127, true), pointRadius: 3});
        }catch(err){alert(err.description);};

        lyr.vendor  = obj["vendor"];
        lyr.setVisibility(lyr.options.isVisible);
        map.addLayer(lyr);
    }
    else if(obj.vendor.service == "VECTOR")
    {   var lyr = new OpenLayers.Layer.Vector("Editable Vectors");
        var randomColorStyle = new OpenLayers.Style(OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style["default"]));
        var selectColorStyle = new OpenLayers.Style(OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style["select"]));

        try{
            lyr.styleMap = new OpenLayers.StyleMap({'default':{fillOpacity: 0.5, fillColor: randomColor(128,128,128, 127,127,127), strokeWidth: 3, strokeColor: randomColor(128,128,128, 127,127,127, true), pointRadius: 3}, 'select':{fillOpacity: 0.5, fillColor: 'yellow', strokeWidth: 2, strokeColor: 'yellow', pointRadius: 5}});
        }catch(err){};

        lyr.options = obj["options"];
        lyr.vendor  = obj["vendor"];
        lyr.setVisibility(lyr.options.isVisible);
        map.addLayer(lyr);
    }
}

function destroyKarte()
{
    //!!! ACHTUNG : OpenLayers map.js modifiziert !!!
    //!!!           Zeile 192 Besonderheit für IE eingetragen !!!
    if(map.layers)
      for(var i=0;i<map.layers.length;i++)
          map.removeLayer(map.layers[i]);

    if(map.events)
        map.events.remove('click');

    if(typeof editControl != 'undefined')
    {   try{
        editControl.activateControl(editControl.controls[0]);
        editControl.controls[1].destroy();
        editControl.controls[2].destroy();
        editControl.controls[3].destroy();
        editControl.controls[4].destroy();
        editControl.controls[5].destroy();
        editControl.controls[6].destroy();
        editControl.controls[7].destroy();
        editControl.controls[0].destroy();
        editControl.destroy();
        }catch(err){};
        editControl = null;
    }

    try{
        for(var i=0;i<map.controls.length;i++)
            map.controls[i].destroy();
    }catch(err){;}

    try{
        map.destroy();
    }catch(err){;}


}

function clone(oriObj){
    if(oriObj == null || typeof(oriObj) != 'object')
        return oriObj;

    var tmpObj = new oriObj.constructor();
    for(var key in oriObj)
        tmpObj[key] = clone(oriObj[key]);

    return tmpObj;
}

function randomColor(r,g,b, ri,gi,bi, flag)
{
    if(flag==true)
        var randomIdx = parseInt(parseInt((3*15+1)*Math.random()-0.0001) % 3);
    else
        randomIdx=-1;

    var red  = (randomIdx==0) ? parseInt(r - ri*Math.random()) : parseInt(r + ri*Math.random());
    var green= (randomIdx==1) ? parseInt(g - gi*Math.random()) : parseInt(g + gi*Math.random());
    var blue = (randomIdx==2) ? parseInt(b - bi*Math.random()) : parseInt(b + bi*Math.random());

    return("#" + DecToHex(red) + DecToHex(green) + DecToHex(blue));
}

function DecToHex(dec)
{
    var hexStr = "0123456789ABCDEF";
    var low = dec % 16;
    var high = (dec - low)/16;
    hex = "" + hexStr.charAt(high) + hexStr.charAt(low);
    return hex;
}
