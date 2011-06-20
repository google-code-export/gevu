// ActionScript file
import flash.events.ErrorEvent;

import formulaires.*;

import mx.collections.ArrayCollection;
import mx.controls.Alert;
import mx.events.ListEvent;
import mx.events.TreeEvent;
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
private var map:formulaire_carte;
private var FormulaireBatiments:formulaire_batiments;
private var FormulaireDiagnostics:formulaire_diagnostics;
private var FormulaireDiagnosticsxvoirie:formulaire_diagnosticsxvoirie;
private var FormulaireDocs:formulaire_docs;
private var FormulaireEspacesxexterieurs:formulaire_espacesxexterieurs;
private var FormulaireEspacesinterieurs:formulaire_espacesxinterieurs;
private var FormulaireEtablissements:formulaire_etablissements;
private var FormulaireGeorss:formulaire_georss;
private var FormulaireGeos:formulaire_geos;

private function reorganizeTabs(arr:Array) : void {
	
	if(Tab.getChildByName("BatimentsTab")==null){
		if(inArray(arr, "BatimentsTab")){
			Tab.addChild( BatimentsTab );
		}
	}else{
		if(!inArray(arr, "BatimentsTab")){
			Tab.removeChild( BatimentsTab );
		}
	}
	if(Tab.getChildByName("DiagnosticsTab")==null){
		if(inArray(arr, "DiagnosticsTab")){
			Tab.addChild( DiagnosticsTab );
		}
	}else{
		if(!inArray(arr, "DiagnosticsTab")){
			Tab.removeChild( DiagnosticsTab );
		}
	}
	if(Tab.getChildByName("DiagnosticsxvoirieTab")==null){
		if(inArray(arr, "DiagnosticsxvoirieTab")){
			Tab.addChild( DiagnosticsxvoirieTab );
		}
	}else{
		if(!inArray(arr, "DiagnosticsxvoirieTab")){
			Tab.removeChild( DiagnosticsxvoirieTab );
		}
	}
	if(Tab.getChildByName("DocsTab")==null){
		if(inArray(arr, "DocsTab")){
			Tab.addChild( DocsTab );
		}
	}else{
		if(!inArray(arr, "DocsTab")){
			Tab.removeChild( DocsTab );
		}
	}
	if(Tab.getChildByName("EspacesxexterieursTab")==null){
		if(inArray(arr, "EspacesxexterieursTab")){
			Tab.addChild( EspacesxexterieursTab );
		}
	}else{
		if(!inArray(arr, "EspacesxexterieursTab")){
			Tab.removeChild( EspacesxexterieursTab );
		}
	}
	if(Tab.getChildByName("EspacesxinterieursTab")==null){
		if(inArray(arr, "EspacesxinterieursTab")){
			Tab.addChild( EspacesxinterieursTab );
		}
	}else{
		if(!inArray(arr, "EspacesxinterieursTab")){
			Tab.removeChild( EspacesxinterieursTab );
		}
	}
	if(Tab.getChildByName("EtablissementsTab")==null){
		if(inArray(arr, "EtablissementsTab")){
			Tab.addChild( EtablissementsTab );
		}
	}else{
		if(!inArray(arr, "EtablissementsTab")){
			Tab.removeChild( EtablissementsTab );
		}
	}
	if(Tab.getChildByName("GeorssTab")==null){
		if(inArray(arr, "GeorssTab")){
			Tab.addChild( GeorssTab );
		}
	}else{
		if(!inArray(arr, "GeorssTab")){
			Tab.removeChild( GeorssTab );
		}
	}
	if(Tab.getChildByName("GeosTab")==null){
		if(inArray(arr, "GeosTab")){
			Tab.addChild( GeosTab );
		}
	}else{
		if(!inArray(arr, "GeosTab")){
			Tab.removeChild( GeosTab );
		}
	}
}

private function onStartup() : void {
	map = new formulaire_carte();
	reorganizeTabs(new Array());
	
	FormulaireGeneral = new formulaire_general();
	FormulaireBatiments = new formulaire_batiments();
	FormulaireDiagnostics = new formulaire_diagnostics();
	FormulaireDiagnosticsxvoirie = new formulaire_diagnosticsxvoirie();
	FormulaireDocs = new formulaire_docs();
	FormulaireEspacesxexterieurs = new formulaire_espacesxexterieurs();
	FormulaireEspacesinterieurs = new formulaire_espacesxinterieurs();
	FormulaireEtablissements = new formulaire_etablissements;
	FormulaireGeorss = new formulaire_georss;
	FormulaireGeos = new formulaire_geos();
	
	GeneralTab.addChild(FormulaireGeneral);
	MapTab.addChild(map);
	BatimentsTab.addChild(FormulaireBatiments);
	DocsTab.addChild(FormulaireDocs);
	DiagnosticsTab.addChild(FormulaireDiagnostics);
	DiagnosticsxvoirieTab.addChild(FormulaireDiagnosticsxvoirie);
	EspacesxexterieursTab.addChild(FormulaireEspacesxexterieurs);
	EspacesxinterieursTab.addChild(FormulaireEspacesinterieurs);
	EtablissementsTab.addChild(FormulaireEtablissements);
	GeorssTab.addChild(FormulaireGeorss);
	GeosTab.addChild(FormulaireGeos);
	
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
	
	//GeosTab.visible=false;
	Tab.removeChild(GeosTab);
	Tab.addChild(GeosTab);
	
	logThis("button clicked");
} 

private function logThis( txt : String ) : void {
	trace( " - "+txt+"\n");
}

private function displayNodeProperties( event:ResultEvent ) : void {
	var obj:Object;
	var arr1:Array = new Array();
	var arrc1:ArrayCollection = new ArrayCollection();
	
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
	
	var childToPreserve:Array = new Array();
	
	for (i=1; i<event.result.length; ++i){
		
		if(event.result[i]['id']==0){
			FormulaireBatiments.displayNodeProperties( event.result[i].data[0] );
			childToPreserve.push("BatimentsTab");
		}
		if(event.result[i]['id']==1){
			FormulaireDiagnostics.displayNodeProperties( event.result[i].data );
			childToPreserve.push("DiagnosticsTab");
		}
		if(event.result[i]['id']==2){
			FormulaireDiagnosticsxvoirie.displayNodeProperties( event.result[i].data[0] );
			childToPreserve.push("DiagnosticsxvoirieTab");
		}
		if(event.result[i]['id']==3){
			FormulaireDocs.displayNodeProperties( event.result[i].data );
			childToPreserve.push("DocsTab");
		}
		if(event.result[i]['id']==5){
			FormulaireEspacesxexterieurs.displayNodeProperties( event.result[i].data[0] );
			childToPreserve.push("EspacesxexterieursTab");
		}
		if(event.result[i]['id']==6){
			FormulaireEspacesinterieurs.displayNodeProperties( event.result[i].data[0] );
			childToPreserve.push("EspacesxinterieursTab");
		}
		if(event.result[i]['id']==7){
			FormulaireEtablissements.displayNodeProperties( event.result[i].data[0] );
			childToPreserve.push("EtablissementsTab");
		}
		if(event.result[i]['id']==8){
			FormulaireGeorss.displayNodeProperties( event.result[i].data[0] );
			childToPreserve.push("GeorssTab");
		}
		if(event.result[i]['id']==9){
			map.showLatLng(event.result[i].data[0].lat,
				           event.result[i].data[0].lng,
						   event.result[i].data[0].zoom_min);
			FormulaireGeos.displayNodeProperties( event.result[i].data[0] );
			childToPreserve.push("GeosTab");
		}
	}
	reorganizeTabs(childToPreserve);
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

private function inArray(arr:Array, val:String):Boolean{
	for(var i:int; i<arr.length; ++i){
		if(arr[i]==val)
			return true;
	}
	return false;
}