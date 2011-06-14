// ActionScript file
import adobe.utils.MMEndCommand;

import com.google.maps.Map;

import flash.events.ErrorEvent;

import formulaires.*;

import mx.collections.ArrayCollection;
import mx.containers.TitleWindow;
import mx.controls.Alert;
import mx.events.ListEvent;
import mx.events.TreeEvent;
import mx.managers.PopUpManager;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;

[Bindable] private var mesDonnees_AC:ArrayCollection = new ArrayCollection([
	{Type:"Voiture", Marque:"Renault", 
		Couleur:"Rouge", activation:true, prix:80000, type:"0", val:"sdf"}, 
	{Type:"Voiture", Marque:"Renault", 
		Couleur:"Vert", activation:false, prix:35000, type:"1", val:1},  
	{Type:"Voiture", Marque:"Peugeot", 
		Couleur:"Jaune", activation:false, prix:22300, type:"0", val:100},  
	{Type:"Voiture", Marque:"Citroen", 
		Couleur:"Orange", activation:true, prix:20000, type:"1", val:false},  
	{Type:"Moto", Marque:"Honda", 
		Couleur:"Rouge", activation:false, prix:20000, type:"0", val:"qsd"}, 
	{Type:"Moto", Marque:"Honda", 
		Couleur:"Rouge", activation:false, prix:30000, type:"0", val:"qsd" },  
	{Type:"Moto", Marque:"Yamaha", 
		Couleur:"Rouge", activation:false, prix:3000, type:"1", val:"qsd"},  
	{Type:"Moto", Marque:"Yamaha", 
		Couleur:"Rouge", activation:false, prix:1200, type:"1", val:"qsd"} ]);

private var TreeObject:XML;
private var xmlTree:XML

private var SelectedNode:int;

private function init():void {
	xmlTree = 
	<node idLieu="-1" lib="root" fake="0">
		<node idLieu="1" lib="univers" fake="0">
			<node idLieu="-10"  fake="1" />
		</node>
	</node>;
	treeTree.dataProvider=xmlTree;
	roDiagnostique.getXmlNode(1);
	treeTree.showRoot=false;
}	

private var FormulaireGeneral:formulaire_general;
private var FormulaireAutres:formulaire_autres;
private var map:formulaire_carte;
private var tstgrid:formulaire_test;
private var FormulaireGeos:formulaire_geos;

private function onStartup() : void {
	map = new formulaire_carte();
	FormulaireGeneral = new formulaire_general();
	FormulaireAutres = new formulaire_autres();
	FormulaireGeos = new formulaire_geos();
	tstgrid = new formulaire_test();
	
	GeneralTab.addChild(FormulaireGeneral);
	AutresTab.addChild(FormulaireAutres);
	MapTab.addChild(map);
	TestTab.addChild(tstgrid);
	GeosTab.addChild(FormulaireGeos);
	tstgrid.dg.dataProvider=mesDonnees_AC;
}

private function treeItemOpened( event:TreeEvent ) : void {
	logThis("tree item has been developped");
	if (event.item.node.attribute("fake")==1)
	{
		logThis("has fake child, must be reloaded");
		var i:int = event.item.attribute("idLieu");
		roDiagnostique.getXmlNode(i);
	}
}

private function treeItemClicked( event:ListEvent ) : void {
	SelectedNode = event.currentTarget.selectedItem.attribute("idLieu");
	logThis( "tree item has been clicked. item is:"+
			 event.currentTarget.selectedItem.attribute("lib") );
	
	if(SelectedNode>0) roDiagnostique.getNodeRelatedData(SelectedNode);
	map.showNode(SelectedNode);
}

private function testButtonClicked() : void {
	/*var _window:formulaire_batiments;
	_window = formulaire_batiments(PopUpManager.createPopUp(this, formulaire_batiments, false));
	PopUpManager.centerPopUp(_window);
	_window.showNode(SelectedNode);*/
	logThis("button clicked");
} 

private function logThis( txt : String ) : void {
	//debugTest.text+=txt+"\n";
	//debugTest.verticalScrollPosition = debugTest.maxVerticalScrollPosition;
}

private function displayNodeProperties( event:ResultEvent ) : void {
	var obj:Object;
	var arr1:Array = new Array();
	var arrc1:ArrayCollection = new ArrayCollection();
	var arr2:Array = new Array();
	var arrc2:ArrayCollection = new ArrayCollection();
	
	/*var tmpArr:Array;
	var tmpStr:String="";
	tmpArr = event.result.type;
	
	if(!tmpArr){
		tmpStr="-";
	}else{
		for (var i:int=0; i<tmpArr.length; ++i){
			tmpStr+=tmpArr[i]['name']+"  ";
		}
	}
	*/
	var i:int;
	var tmpStr:String="";
	
	for (i=1; i<event.result.length; ++i){
		tmpStr+=event.result[i]['name']+"  ";
	}
	obj={prop:"lib",		val:event.result[0].data.lib};			arr1.push(obj);
	obj={prop:"id_lieu",	val:event.result[0].data.id_lieu};		arr1.push(obj);
	obj={prop:"id_parent",	val:event.result[0].data.lieu_parent};	arr1.push(obj);
	obj={prop:"niv",		val:event.result[0].data.niv};			arr1.push(obj);
	obj={prop:"type",		val:tmpStr};							arr1.push(obj);
	
	arrc1.source=arr1;
	FormulaireGeneral.Tableau.dataProvider=arrc1;
	
	for (i=1; i<event.result.length; ++i){
		tmpStr+=event.result[i]['name']+"  ";
		for (var j:String in event.result[i].data){
			obj={prop:j, val:event.result[i].data[j]};
			arr2.push(obj);
		}
		
		if(event.result[i]['id']==9)
			//map.showLatLng(event.result[i].data.lat, event.result[i].data.lng, event.result[i].data.zoom_min);
			FormulaireGeos.displayNodeProperties( event.result[i].data);
	}
	arrc2.source=arr2;
	FormulaireAutres.Tableau.dataProvider=arrc2;
}

private function updateTreeStructure( event:ResultEvent ) : void {
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

private function faultHandlerService(fault:FaultEvent, os:String=""):void {
	var str:String;
	str = "Code: "+fault.fault.faultCode.toString()+"\n"+
	      "Detail: "+fault.fault.faultDetail.toString()+"\n"+
	      "String: "+fault.fault.faultString.toString()+"\n";
	
	if (os!="")
		os = " - "+os;
	Alert.show(str, "FaultHandlerService"+os);
}

private function ErrorHandlerService(fault:ErrorEvent, os:String=""):void {
	var str:String;
	/*
	trace(fault.fault.faultCode.toString());
	trace(fault.fault.faultDetail.toString());
	trace(fault.fault.faultString.toString());
	trace(fault.fault.rootCause.toString());
	
	str = "Code: "+fault.fault.faultCode.toString()+"\n"+
	      "Detail: "+fault.fault.faultDetail.toString()+"\n"+
	      "String: "+fault.fault.faultString.toString()+"\n";*/
	
	if (os!="")
		os = " - "+os;
	Alert.show(str, "ErrorHandlerService"+os);
}
