import compo.*;

import mx.collections.ArrayCollection;
import mx.events.DropdownEvent;
import mx.rpc.events.ResultEvent;
import mx.managers.PopUpManager;

//include the constant definition of the server endpoint URL
include "grillesconfig.as";


[Bindable]
public var dataArr:ArrayCollection = new ArrayCollection();
[Bindable]
public var exi:Object;
private var idExi:String = "";
[Bindable]
public var selectedItem:Object;

[Bindable]	private var urlBD:String = ENDPOINT_EXEAJAX+"?f=GetBDs";
[Bindable]	private var rsBD:Object;
 
public function init():void
{
	//construction de la fenêtre d'édition
	var twLog:twLogin= twLogin(
        PopUpManager.createPopUp(this, twLogin, true));
	twLog.endPoint=ENDPOINT_SERVICE;
    PopUpManager.centerPopUp(twLog);

    srvBD.send();    	
} 

public function readXmlBD(event:ResultEvent):void{
    //récupère les geoloc
    rsBD = event.result.menuitems.menuitem;
}

public function choixBD(event:DropdownEvent):void{
	
	selectedItem = this.cbBD.selectedItem;
    
    //paramètre la requête pour récupérer la liste des territoires
	tTerre.srvTerre.cancel();
	tTerre.srvTerre.url= ENDPOINT_EXECARTO;
	//tTerre.srvTerre.url= "http://localhost/gevu/bdd/carto/etabs1.xml";
	//tTerre.srvTerre.send();
	var params:Object = new Object();
	params.f = "get_arbo_territoire";
	params.site = selectedItem.value;
	trace ("choixBD:srvTerre.url="+tTerre.srvTerre.url+"?f="+params.f+"&site="+params.site);
	tTerre.srvTerre.send(params);
	
	
}
