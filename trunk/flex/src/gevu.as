// ActionScript file
	//pour googlemap sdk
      import com.google.maps.InfoWindowOptions;
      import com.google.maps.LatLng;
      import com.google.maps.Map;
      import com.google.maps.MapEvent;
      import com.google.maps.MapMouseEvent;
      import com.google.maps.MapType;
      import com.google.maps.controls.MapTypeControl;
      import com.google.maps.controls.PositionControl;
      import com.google.maps.controls.ZoomControl;
      import com.google.maps.interfaces.IMapType;
      import com.google.maps.overlays.Marker;
      import com.google.maps.overlays.MarkerOptions;
      import com.google.maps.styles.FillStyle;
      import com.google.maps.styles.StrokeStyle;
      
      import compo.*;
      
      import flash.net.URLRequest;
      
      import mx.collections.*;
      import mx.controls.Alert;
      import mx.controls.treeClasses.*;
      import mx.managers.CursorManager;
      import mx.managers.PopUpManager;
      import mx.rpc.events.ResultEvent;


    private var site:String="alceane";
    /*prod
   	private var urlExeAjax:String="http://www.gevu.org/library/php/ExeAjax.php";
	private var mapKey:String = "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRQz39mjOVnM1cIqThEYinQ2UMSLChQ5GzeL0LKmVf54ALsifsQIHmMHMQ";
    private var urlAllEtatDiag:String="http://www.gevu.org/library/php/ExecDonneeCarto.php?f=GetAllEtatDiag&site="+site;
    private var urlExeCarto:String="http://www.gevu.org/library/php/ExecDonneeCarto.php";
	*/
	//local
   	private var urlExeAjax:String="http://localhost/gevu/library/php/ExeAjax.php";
	private var mapKey:String = "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRSAqqCYJRNRYB52nvFZykN9ZY0cdhRvfhvUr_7t7Rz5_XNkPGDb_GYlQA";
    private var urlAllEtatDiag:String="http://localhost/gevu/library/php/ExecDonneeCarto.php?f=GetAllEtatDiag&site="+site;
    private var urlExeCarto:String="http://localhost/gevu/library/php/ExecDonneeCarto.php";
	//
	
    private var map:Map;
    private var markers:XMLList;
	[Bindable]	private var rsEtatDiag:Object;
	[Bindable]	private var rsCarto:Object;
	[Bindable]	private var rsTerre:XMLList;
	[Bindable]	private var rsParc:XMLList;

	private var accDocs:hbIcoMultimedia;
	private var accBassin:vbBassinGare;
	private var accActeurs:vbActeurs;
	public var accKML:vbCoucheKml;
	public var accStat:cnvStat;

      [Embed(source="/images/A.png")] [Bindable] private var AIcon:Class;
      [Embed(source="/images/B.png")] [Bindable] private var BIcon:Class;
      [Embed(source="/images/C.png")] [Bindable] private var CIcon:Class;
      [Embed(source="/images/D.png")] [Bindable] private var DIcon:Class;
      [Embed(source="/images/E.png")] [Bindable] private var EIcon:Class;

      [Embed(source="/images/audio.jpg")] [Bindable] private var AudioIcon:Class;
      [Embed(source="/images/cog.jpg")] [Bindable] private var CogIcon:Class;
      [Embed(source="/images/moteur.jpg")] [Bindable] private var MoteurIcon:Class;
      [Embed(source="/images/visu.jpg")] [Bindable] private var VisuIcon:Class;


      [Bindable] private var rubMarkers:Array = new Array; 

	[Bindable] public var isDraggable:Boolean=false; 
		
      private var handi:Object = 
        { "A": {
            "color": 0xFF0000,
            "icon": AIcon,
            "markers": []}, 
          "B": {
              "color": 0x0000FF,
	          "icon": BIcon,
              "markers": []},
          "C": {
              "color": 0x0000FF,
	          "icon": CIcon,
              "markers": []},
          "D": {
              "color": 0x0000FF,
	          "icon": DIcon,
              "markers": []},
          "E": {
              "color": 0x0000FF,
	          "icon": EIcon,
              "markers": []}
        };


		private function init():void{
			getXmlTerre("?f=get_arbo_territoire&id=5479&site="+site,readXmlTerre);
			if(site=="alceane"){
				getXmlTerre("?f=get_arbo_parc&site="+site,readXmlParc);				
			}
		}

		private function InitBoxStat():void{
			
			//mapHolder.percentWidth=50;
			//boxTree.percentWidth=20
			//boxEtatLieux.percentWidth=30;
			
		}
		
        private function chartEtatDiagChange(event:Event):void {
            var allSeries:Array = event.currentTarget.series;
            chartTrace.text = "";
            var idDoc:String = "";
            var strHandi:String = "";
            var niv:String = "";
            var titreSelect:String = "";
            //construction de l'identifiant du doc
            //cf. library/php/ExeAjax.php?f=GetEtatDiagListe&id=5610&idDoc=0_audio
            for (var i:int=0; i<allSeries.length; i++) {
                chartTrace.text += "\n" + allSeries[i].id + 
                    " Selected Items: " + allSeries[i].selectedIndices;
				//le type de handicap
                switch (allSeries[i].selectedIndices[0]) {
				    case 0:
				        strHandi = "_moteur";
				        break;
				    case 1:
				        strHandi = "_audio";
				        break;
				    case 2:
				        strHandi = "_cog";
				        break;
				    case 3:
				        strHandi = "_visu";
				        break;
				}				    
                //le niveau seulemet si on a récupéré  l'indice
                if(strHandi!="" && niv==""){
	                switch (allSeries[i].id) {
					    case "_gevu_ColumnSeries1":
					        niv = "0";
					        break;
					    case "_gevu_ColumnSeries2":
					        niv = "1";
					        break;
					    case "_gevu_ColumnSeries3":
					        niv = "2";
					        break;
					    case "_gevu_ColumnSeries4":
					        niv = "3";
					        break;
					}
					//création du titre de la sélection
					titreSelect = allSeries[i].legendData[0].label;	
                }
            }
            
            idDoc = niv+strHandi; 
            //exécute la requête
            ShowListeDiag(idSite.text, idRub.text, idDoc, titreSelect);

        }

		public function rhEtatDiag(event:ResultEvent):void {
			rsEtatDiag = event.result;
			accEtatLieu.selectedIndex=0;			
			TraiteReponseEtatDiag();
		}
      
      public function TraiteReponseEtatDiag():void {
      	
			if(rsEtatDiag && rsEtatDiag.toString()!=""){
				
				//mise à jour du taux de calcul
				TauxCalc.text = "Taux de calcul : "+rsEtatDiag.EtatDiag.TauxCalc;
				
				//mise à jour des icones handicateur
		        for each (var obs:Object in rsEtatDiag.EtatDiag.Obstacles)
		        {
		        	if(obs.id=="moteur")
		        		imgAlphaMoteur.source=handi[obs.handi].icon;
		        	if(obs.id=="audio")
		        		imgAlphaAudio.source=handi[obs.handi].icon;
		        	if(obs.id=="cognitif")
		        		imgAlphaCog.source=handi[obs.handi].icon;
		        	if(obs.id=="visuel")
		        		imgAlphaVisu.source=handi[obs.handi].icon;
		        }
		        
				//gestion des documents multimédia
				
				if(accDocs!=null){
					accEtatLieu.removeChild(accDocs);
				}
				accDocs=new hbIcoMultimedia();
		        accDocs.Init(rsEtatDiag,idRub.text,idSite.text,urlExeAjax);
				var ico:Boolean = accDocs.VerifIco(); 
				if(ico){
					accEtatLieu.addChild(accDocs);
		        }else{
			        accDocs=null;	
		        }
		        

				//gestion des bassins de gare
				if(accBassin!=null){
					accEtatLieu.removeChild(accBassin);
				}
		        if(rsEtatDiag.EtatDiag.bassin){
					accBassin=new vbBassinGare();
		        	accBassin.rsBassin = rsEtatDiag.EtatDiag.bassin;
		        	accBassin.coucheKml=this.accKML;
		        	accBassin.markers=markers;	
					accEtatLieu.addChild(accBassin);
		        }else{
		        	accBassin=null;
		        }
		        
				//gestion des acteurs de la concertation
				if(accActeurs!=null){
					accEtatLieu.removeChild(accActeurs);
				}
		        if(rsEtatDiag.EtatDiag.acteurs){
		        	accActeurs=new vbActeurs();
		        	accActeurs.rsActeurs = rsEtatDiag.EtatDiag.acteurs;	
					accEtatLieu.addChild(accActeurs);
		        }else{
		        	accActeurs=null;
		        }

		 	}     	
      	
      }
      
      
      public function onHolderCreated(event:Event):void {
        map = new Map();
        map.key = mapKey;
		map.addControl(new ZoomControl());
		map.addControl(new PositionControl());
		map.addControl(new MapTypeControl());
        map.addEventListener(MapEvent.MAP_READY, onMapReady);
        map.sensor = "true";
        mapHolder.addChild(map);
        
      }

      public function onHolderResized(event:Event):void {
        map.setSize(new Point(mapHolder.width, mapHolder.height));
      }

      private function onMapReady(event:Event):void {
        map.enableScrollWheelZoom();
        map.enableContinuousZoom();
        map.setCenter(new LatLng(47.12995076, 1.00001335), 7);
     }
     
     public function getXmlTerre(param:String, callback:Function):void {
     	 var strUrl:String = urlExeCarto+param;
         var xmlReq:URLRequest = new URLRequest(strUrl);
         var xmlLoader:URLLoader = new URLLoader(xmlReq);
         xmlLoader.addEventListener("complete", callback);
    }

     public function getXml():void {
         var xmlString:URLRequest = new URLRequest(urlAllEtatDiag);
         var xmlLoader:URLLoader = new URLLoader(xmlString);
         xmlLoader.addEventListener("complete", readXml);
    }

     public function getXmlCarto(idRub:String):void {
	    
	    //paramètre la requête pour récupérer le bon fichier xml
		srvExeCarto.cancel();
		var params:Object = new Object();
		params.f = "get_markers";
		params.id = idRub;
		params.site = idSite.text;
		params.MapQuery = "admin";
		trace ("getXmlCarto:srvExeCarto.url="+srvExeCarto.url+"?f="+params.f+"&id="+params.id+"&site="+params.site+"&MapQuery="+params.MapQuery);
		srvExeCarto.send(params);

    }


    public function readXml(event:Event):void{
        //récupère les geoloc
        var markersXML:XML = new XML(event.target.data);
        markers = markersXML.CartoDonnee;
    }

    public function readXmlTerre(event:Event):void{
        //récupère les territoires
        var terresXML:XML = new XML(event.target.data);
        rsTerre = terresXML.terre;
    }

    public function readXmlParc(event:Event):void{
        //récupère le parc
        var parcXML:XML = new XML(event.target.data);
        rsParc = parcXML.terre;
    }

	public function ForceCalcul():void{
		srvFC.cancel();
		srvFC.url = urlExeAjax;
		var params:Object = new Object();
		params.f = "SetChoixAffichage";
		params.idXul = 'ForceCalcul';
		if(cbForceCalcul.selected){
			params.valeur = "true";		
		}else{
			params.valeur = "false";				
		}
		trace ("ForceCalcul:srvFC.url="+urlExeAjax+"?f="+params.f+"&idXul="+params.idXul+"&valeur="+params.valeur);
		srvFC.send(params);			
			
	}

     private function changeEvtTreeTerre(event:Event):void {

        //reinitialise l'accordion
        if(!accEtatLieu.visible)
			accEtatLieu.visible=true;
        if(accEtatLieu.selectedIndex!=0)
	        accEtatLieu.selectedIndex=0;
		var item:Object=event.currentTarget.selectedItem;
        //showMarkerId(item.@idRub,item.@idSite);
		var param:String = "f=get_markers&MapQuery=admin&id=" + item.@idRub + "&site=" + item.@idSite;
		doRequestDirect(param,GetGeoHandler);
	        	
     }


    public function readXmlCarto(event:ResultEvent):void{
        //récupère les geoloc
        rsCarto = event.result;
		if(rsCarto.toString()!=""){
			//problème quand un seul élément
			/*
	        for each (var carto:Object in rsCarto.CartoDonnees.CartoDonnee)
	        {
		        map.setCenter(new LatLng(carto.lat, carto.lng), carto.zoommax, MapType.HYBRID_MAP_TYPE);	        	
	        }
	        */
	        var carto:Object = rsCarto.CartoDonnees.CartoDonnee;
	        map.setCenter(new LatLng(carto.lat, carto.lng), carto.zoommin, MapType.HYBRID_MAP_TYPE);	        	
		}
    }

      public function createMarkerGrille(idGrille:String): void {

		CursorManager.setBusyCursor();
        var type:String = "grille_"+idGrille;
        //vérifie s'il faut créer les markers ou les rendre visible/invisible
        if(treeEtatLieux.categories[type].markers.length>0){
			toggleCategory(type);
	    }else{
	    	//récupère les géolocalisation pour la grille
			var param:String = "f=get_arbo_grille&idGrille=" + idGrille;
			doRequestDirect(param,GetGrilleHandler);	    		        
	    }
   		CursorManager.removeBusyCursor();

      }

    public function GetGeoHandler(event:Event):void{
    	
		try{
			var markerXml:XML = new XML(event.target.data);
	    	showMarkerRub(markerXml[0].CartoDonnee[0]);
		}catch (err:Error){
		 	// code to react to the error
            Alert.show(err.message);
			CursorManager.removeBusyCursor();
		}
    }

    public function GetGrilleHandler(event:Event):void{
    	
		try{
			var terreXml:XML = new XML(event.target.data);

	        //boucle sur les géoloc 
	        for each (var markerXml:XML in terreXml.terre.CartoDonnee){
        		//contruction du markers
	            var titre:String = markerXml.@titre;
	            var adresse:String = markerXml.@adresse;
	            var latlng:LatLng = new LatLng(markerXml.@lat, markerXml.@lng);
               	var type:String =  "grille_"+markerXml.@idGrille;

		        var marker:Marker = createMarker(latlng, titre, adresse, type, markerXml);
			    treeEtatLieux.categories[type].markers.push(marker);
		        map.addOverlay(marker);            		
	        }

		}catch (err:Error){
		 	// code to react to the error
            Alert.show(err.message);
			CursorManager.removeBusyCursor();
		}
    }


	public function showMarkerRub(markerXml:XML): void {

		CursorManager.setBusyCursor();


			var marker:Marker = createMarkerX(markerXml);
			rubMarkers[markerXml.@idRub] = marker;
			map.addOverlay(marker);

		    //montre les stats
		    //if(sStat)	
				showStat(markerXml);


		    //recentre la carte
		    var mType:IMapType= GetMapType(markerXml.@cartotype);
		    var zoom:Number= markerXml.@zoommin;            		
	        map.setCenter(marker.getLatLng(),zoom , mType);
        
		CursorManager.removeBusyCursor();
                
      }
      
      public function GetMapType(cType:String):IMapType{
      	
      	var mType:IMapType= MapType.PHYSICAL_MAP_TYPE;
      	if(cType=="Mixte")mType= MapType.HYBRID_MAP_TYPE;
      	if(cType=="Plan")mType= MapType.NORMAL_MAP_TYPE;
      	if(cType=="Satellite")mType= MapType.SATELLITE_MAP_TYPE;
      	if(cType=="Relief")mType= MapType.PHYSICAL_MAP_TYPE;
      	
      	return mType;
      }

	public function doRequestDirect(param:String, callback:Function):void{
		
		    var urlRequest:URLRequest = new URLRequest(urlExeCarto);
		    urlRequest.data = param;
			trace ("http.as:doRequestDirect:query=" +urlExeCarto+"?"+urlRequest.data);
		    urlRequest.method = URLRequestMethod.GET;
		    var urlLoader:URLLoader = new URLLoader(urlRequest);
		    urlLoader.addEventListener("complete", callback);
		
	}


      public function showMarkerId(idR:String,idS:String="-1",sStat:Boolean=true): void {

		//si l'identifiant du site est vide car venant d'une liste de critère on prend celui du graphique
		if(idS=="-1")idS=this.idSite.text;

        //vérifie s'il faut créer les markers ou les rendre visible/invisible
        //boucle sur les géoloc 
        var bM:Boolean=false;
        for each (var markerXml:XML in markers){
        	bM=true;
        	//vérifie si le marker a bien l'identifiant
        	var resultG:XMLList;
        	resultG = markerXml.(@idSite == idS && @idRub == idR);
        	if(resultG.length()>0){
        		//contruction du markers
	            var titre:String = markerXml.@titre;
	            var adresse:String = markerXml.@adresse;
	            var latlng:LatLng = new LatLng(markerXml.@lat, markerXml.@lng);
	            var type:String = "grille_"+markerXml.grilles.grille[0].@id
		        //if(!rubMarkers[idRub]){
			        var marker:Marker = createMarker(latlng, titre, adresse, type, markerXml);
				    rubMarkers[idRub] = marker;
			        map.addOverlay(marker);
			    //}
			    //montre les stats
			    if(sStat)	
				    showStat(markerXml);
			    //recentre la carte            		
		        map.setCenter(latlng, markerXml.@zoommin, MapType.HYBRID_MAP_TYPE);
		        break;	        	
        	}  
        }
        
        //requete la geoloc de la rubrique
        if(!bM){
	        var param:String = "f=get_markers&MapQuery=admin&id=" + idR + "&site=" + idS;
			doRequestDirect(param,GetGeoHandler);
        	
        }
        
      }
      
      public function chargeKML(markerXml:XML):void {
      	
      	
	    var arrKml:Array = markerXml.@kml.split("*");
    	//on vide l'accordion
		if(accKML!=null){
			accEtatLieu.removeChild(accKML);
		}
		var i:int=0;
        for each (var kml:String in arrKml){
		    //vérifie s'il faut charger le kml
		    if(kml!=""){
		    	//on créé l'objet des coucheS kml
		    	accKML=new vbCoucheKml();
		        //charge les couches kml
				trace ("chargeKML:kml="+kml);
		        accKML.mapP = map;
		        accKML.kmlUrl = kml;
		        accKML.kmlLat = markerXml.@lat;
		        accKML.kmlLng = markerXml.@lng;
		        accKML.kmlZoom = markerXml.@zoommin;
		        i++;
		    }        	
        }
        if(i!=0){
        	accEtatLieu.addChild(accKML);
        }else{
        	accKML=null;
        }
      	
      }
      
    public function chargeStat(markerXml:XML):void {
      	   	
		//on vide l'accordion
		if(accStat!=null){
			accEtatLieu.removeChild(accStat);
		}
		accStat=null;
		var idGrille:String = "";
		var idArt:String; 
    	var resultType:XMLList = markerXml.grilles.grille;
    	if(resultType.length()>0){
	        for each (var gr:XML in resultType){
	            //on cherche les grilles de stat antenne et batiment
	            if(gr.@id=="87" || gr.@id=="82"){
		            idGrille = gr.@id;
		            idArt = gr.@idArt
		            break;		        		            	
	            }
	        }
	        if(idGrille!=""){
				accStat = new cnvStat();
				accStat.urlSrv = this.urlExeAjax;
				accStat.idArt = idArt;
				accStat.idGrille = idGrille;
				accStat.idSite = this.site; 
		       	accEtatLieu.addChild(accStat);   	
	        }
        }
		      	
	}

	public function createMarkerX(markerXml:XML): Marker {

		//contruction du markers
		var titre:String = markerXml.@titre;
		var adresse:String = markerXml.@adresse;
		var latlng:LatLng = new LatLng(markerXml.@lat, markerXml.@lng);

    	var type:String; 
       	var html:String;
       	html = "<b>" + titre + "</b> <br/>" + adresse;

		//vérifie si on traite un type de marker particulier
    	var resultType:XMLList = markerXml.motsclefs.motclef;
    	if(resultType.length()>0){
	        for each (var mc:XML in resultType){
	            type = "motclef_"+mc.@id;
		       	/*
		       	if(roleUti != "visite"){
		       		var prenom:String=markerXml.grilles.grille.(@id == "25").donnee.valeur.(@champ == "ligne_3").@valeur;
		       		var nom:String=markerXml.grilles.grille.(@id == "25").donnee.valeur.(@champ == "ligne_2").@valeur;
					var tel:String=markerXml.grilles.grille.(@id == "25").donnee.valeur.(@champ == "ligne_4").@valeur;
			       	html += "<br/>Contact : " + prenom + " " + nom;
			       	html += "<br/>Tel : " + tel;
		       	}
				*/
				isDraggable=false;    	
	        }
        }
    	resultType = markerXml.grilles.grille;
    	if(resultType.length()>0){
	        for each (var gr:XML in resultType){
	            //on exclu les grilles géoloc
	            if(gr.@id!="1" && gr.@id!="81"){
		            type = "grille_"+gr.@id;
		             break;		        		            	
	            }
				isDraggable=false;    	
	        }
        }

		//inspiration de http://www.tricedesigns.com/portfolio/googletemps/srcview/
		var markerOptions:MarkerOptions = new MarkerOptions({
                    strokeStyle: new StrokeStyle({color: 0x000000}),
                    fillStyle: new FillStyle({color:treeEtatLieux.categories[type].color, alpha: 0.3}),
                    radius: 12,
                    hasShadow: true
                  })
		markerOptions = new MarkerOptions({draggable: isDraggable, icon: new treeEtatLieux.categories[type].icon, iconOffset: new Point(-16, -32)});

       	var marker:Marker = new Marker(latlng, markerOptions);
        marker.addEventListener(MapMouseEvent.CLICK, function(e:MapMouseEvent):void {
			marker.openInfoWindow(new InfoWindowOptions({contentHTML:html}));
			showStat(markerXml);
        });
		if(isDraggable){
			/*
	        var options:InfoWindowOptions = new InfoWindowOptions({
	            customContent: new InfoWindowTabbedComponent(marker,adresse,"-1",markerXml.@idRub,markerXml.@titre),
	            customOffset: new Point(0, 10),
	            width: 300,
	            height: 120,
	            drawDefaultFrame: true
	        });
	
	        marker.addEventListener(MapMouseEvent.CLICK, function (event:MapMouseEvent):void {
	          var markerContent:String = marker.getLatLng().toString();
	           marker.openInfoWindow(options);
	        });
	        marker.addEventListener(MapMouseEvent.DRAG_END, function(event:MapMouseEvent):void {
	          var markerContent:String = marker.getLatLng().toString();
	          marker.openInfoWindow(options);
	         });
	         */
		}
         
        return marker;
	} 



      public function createMarker(latlng:LatLng, name:String, address:String, type:String, markerXml:XML): Marker {

			//inspiration de http://www.tricedesigns.com/portfolio/googletemps/srcview/
			var markerOptions:MarkerOptions = new MarkerOptions({
                        strokeStyle: new StrokeStyle({color: 0x000000}),
                        fillStyle: new FillStyle({color:treeEtatLieux.categories[type].color, alpha: 0.3}),
                        radius: 12,
                        hasShadow: true
                      })
			markerOptions = new MarkerOptions({icon: new treeEtatLieux.categories[type].icon, iconOffset: new Point(-16, -32)});

	       	var marker:Marker = new Marker(latlng, markerOptions);
	       	var html:String = "<b>" + name + "</b> <br/>" + address;
	        marker.addEventListener(MapMouseEvent.CLICK, function(e:MapMouseEvent):void {
				marker.openInfoWindow(new InfoWindowOptions({contentHTML:html}));
				showStat(markerXml);
	        });
	        return marker;
     } 

     private function showStat(markerXml:XML):void {
        //affiche la box des stats
        InitBoxStat();
        EtatLieuTitre.text = markerXml.@titre;
        idRub.text = markerXml.@idRub;
        idSite.text = markerXml.@idSite;

		//vérifie s'il faut charger le détail des stats

	    //charge le kml
    	chargeKML(markerXml);
    	
    	//charge les stat
    	chargeStat(markerXml);
    	
        //paramètre la requête pour récupérer le bon fichier xml
		srvEtatDiag.cancel();
		var params:Object = new Object();
		params.f = "GetStatEtatDiag";
		params.id = markerXml.@idRub;
		params.site = markerXml.@idSite;
		trace ("showStat:srvEtatDiag.url="+srvEtatDiag.url+"?f="+params.f+"&id="+params.id+"&site="+params.site);
		srvEtatDiag.send(params);
 
     }

     private function ShowListeDiag(idSite:String, idRub:String, idDoc:String, titreSelect:String):void {

        // Create a non-modal TitleWindow container.
        var wListe:twEtatDiagListe = twEtatDiagListe(
            PopUpManager.createPopUp(this, twEtatDiagListe, false));
            
		var params:Object = new Object();
		params.f = "GetFlexEtatDiagListe";
		params.id = idRub;
		params.site = idSite;
		params.idDoc = idDoc;

		titreSelect = EtatLieuTitre.text+" : "+titreSelect;

		wListe.useHttpService(urlExeAjax,params,titreSelect);

        PopUpManager.centerPopUp(wListe);
		
		trace ("nShowListeDiag=" +urlExeAjax+"?f="+params.f+"&id="+params.id+"&site="+params.site+"&idDoc="+params.idDoc);
     }

	private function PurgeCarte():void{
		//suprrime le tableau des markers par type
        for each (var cat:Object in treeEtatLieux.categories){
        	cat.markers= new ArrayCollection();
        }
		//vide la carte
		map.clearOverlays();
	}

    private function toggleCategory(type:String):void {
		CursorManager.setBusyCursor();
       for (var i:Number = 0; i < treeEtatLieux.categories[type].markers.length; i++) {
         var marker:Marker = treeEtatLieux.categories[type].markers[i];
         if (!marker.visible) {
           marker.visible = true;
         } else {
           marker.visible = false;
         }
       } 
		CursorManager.removeBusyCursor();

	}

