//include the constant definition of the server endpoint URL
include "grillesconfig.as";

import com.adobe.serialization.json.JSON;

import compo.*;

import mx.collections.ArrayCollection;
import mx.controls.Alert;
import mx.events.ListEvent;
import mx.events.TreeEvent;
import mx.managers.PopUpManager;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;

[Bindable][Embed("images/voie.png")]public var voieIcon:Class;
[Bindable] public var exi:Object;
[Bindable] public var idExi:String = "";

private var TreeObject:XML;
private var xmlTree:XML
private var SelectedNode:int;
private var idBase:String 

public function login():void
{
	//construction de la fenêtre d'édition
	var twLog:twLogin= twLogin(
	PopUpManager.createPopUp(this, twLogin, true));
	twLog.endPoint=ENDPOINT_SERVICE;
	twLog.callback = init;
	PopUpManager.centerPopUp(twLog);
	//init();
} 


public function init():void
{
	boxGen.visible = true;

	//construction de la listes des bases disponibles
	var dataBases:Array = JSON.decode(this.exi.droit_3);
	cbBases.dataProvider = dataBases;
	//roDiagnostique.getTablesNames();

}

protected function cbBases_changeHandler(event:ListEvent):void
{
	idBase = cbBases.selectedItem.id;
	idBase = idBase.substr(idBase.indexOf("_")+1);
	initTreeTerre();
}


public function initTreeTerre():void{
	//construction du tree des territoires
	xmlTree = 
		<node idLieu="-1" lib="root" fake="0">
			<node idLieu="1" lib="univers" fake="0">
				<node idLieu="-10"  fake="1" />
			</node>
		</node>;
	treeTree.dataProvider=xmlTree;
	roDiagnostique.getXmlNode(1,idBase);
	treeTree.showRoot=false;	
	
}

public function ForceCalcul():void{
	var params:Object = new Object();
	params.f = "SetChoixAffichage";
	params.idXul = 'ForceCalcul';
	if(cbForceCalcul.selected){
		params.valeur = "true";		
	}else{
		params.valeur = "false";				
	}
	//trace ("ForceCalcul:srvFC.url="+urlExeAjax+"?f="+params.f+"&idXul="+params.idXul+"&valeur="+params.valeur);
	//srvFC.send(params);			
}

private function PurgeCarte():void{
	//vide la carte
	//map.clearOverlays();
}

private function faultHandlerService(fault:FaultEvent, os:String=""):void {
	var str:String;
	str = "Code: "+fault.fault.faultCode.toString()+"\n"+
		"Detail: "+fault.fault.faultDetail.toString()+"\n"+
		"String: "+fault.fault.faultString.toString()+"\n";
	
	if (os!="")
		os = " - "+os;
	Alert.show(str, "FaultHandlerService"+os);
}

private function treeItemOpened(event:TreeEvent) : void {
	if (event.item.node.attribute("fake")==1)
	{
		var i:int = event.item.attribute("idLieu");
		roDiagnostique.getXmlNode(i, idBase);
	}
}


private function treeItemClicked(event:ListEvent) : void {
	SelectedNode = event.currentTarget.selectedItem.attribute("idLieu");
	
	if(SelectedNode>0) roDiagnostique.getNodeRelatedData(SelectedNode);
	
	//map.showNode(SelectedNode);
}

private function displayNodeProperties(event:ResultEvent) : void {
	var obj:Object;
	var arr1:Array = new Array();
	var arrc1:ArrayCollection = new ArrayCollection;
	
	var i:int;
	var tmpStr:String="";
	
	for (i=2; i<event.result.length; ++i){
		tmpStr+=event.result[i]['name']+"  ";
	}
	obj={prop:"lib",		val:event.result[0].data.lib};			arr1.push(obj);
	obj={prop:"id_lieu",	val:event.result[0].data.id_lieu};		arr1.push(obj);
	obj={prop:"id_parent",	val:event.result[0].data.lieu_parent};	arr1.push(obj);
	obj={prop:"niv",		val:event.result[0].data.niv};			arr1.push(obj);
	obj={prop:"type",		val:tmpStr};							arr1.push(obj);
	
	arrc1.source=arr1;
		
}

private function updateTreeStructure(event:ResultEvent) : void {
	/* get the id of the node */
	var x:XML = <root></root>;
	x.appendChild(event.result);
	var idnoeud:int;
	idnoeud = x.node.attribute("idLieu");
	
	/* add the new real node */
	treeTree.dataProvider[0].descendants().(@idLieu == idnoeud)[0].appendChild(x.node.node);
	
	/* delete the old fake one */
	delete  treeTree.dataProvider[0].descendants().(@idLieu==idnoeud)[0].children()[0];
}