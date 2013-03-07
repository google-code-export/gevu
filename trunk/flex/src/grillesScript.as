import compo.*;

import mx.collections.ArrayCollection;
import mx.controls.Alert;
import mx.controls.dataGridClasses.DataGridColumn;
import mx.controls.listClasses.IDropInListItemRenderer;
import mx.events.CloseEvent;
import mx.events.DataGridEvent;
import mx.managers.CursorManager;
import mx.managers.PopUpManager;
import mx.rpc.AsyncToken;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;
import mx.rpc.http.HTTPService;

//include the constant definition of the server endpoint URL
include "grillesconfig.as";


[Bindable]
public var dataArr:ArrayCollection = new ArrayCollection();
[Bindable]
public var exi:Object;
[Bindable] public var idExi:String = "";
[Bindable] public var idScenar:int;

public var uidCopie:String;

 
public function login():void
{
	/*construction de la fenêtre d'édition*/
	var twLog:twLogin= twLogin(
        PopUpManager.createPopUp(this, twLogin, true));
	twLog.endPoint=ENDPOINT_SERVICE;
	twLog.callback = init;
    PopUpManager.centerPopUp(twLog);
	boxGen.visible = true;
}

public function init():void{
	
}
	
	

