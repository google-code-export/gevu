// ActionScript file

import flash.events.ErrorEvent;

import formulaires.*;

import mx.collections.ArrayCollection;
import mx.collections.XMLListCollection;
import mx.controls.Alert;
import mx.events.ItemClickEvent;
import mx.events.ListEvent;
import mx.events.TreeEvent;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;

private var TreeObject:XML;
private var xmlTree:XML
private var SelectedNode:int;
private var ButtonArray:Array;

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
private var FormulaireEspaces:formulaire_espaces;
private var FormulaireEspacesxexterieurs:formulaire_espacesxexterieurs;
private var FormulaireEspacesinterieurs:formulaire_espacesxinterieurs;
private var FormulaireEtablissements:formulaire_etablissements;
private var FormulaireGeorss:formulaire_georss;
private var FormulaireGeos:formulaire_geos;
private var FormulaireNiveaux:formulaire_niveaux;
private var FormulaireObjetsxexterieurs:formulaire_objetsxexterieurs;
private var FormulaireObjetsxinterieurs:formulaire_objetsxinterieurs;
private var FormulaireObjetsxvoiries:formulaire_objetsxvoiries;

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
	if(Tab.getChildByName("EspacesTab")==null){
		if(inArray(arr, "EspacesTab")){
			Tab.addChild( EspacesTab );
		}
	}else{
		if(!inArray(arr, "EspacesTab")){
			Tab.removeChild( EspacesTab );
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
	if(Tab.getChildByName("NiveauxTab")==null){
		if(inArray(arr, "NiveauxTab")){
			Tab.addChild( NiveauxTab );
		}
	}else{
		if(!inArray(arr, "NiveauxTab")){
			Tab.removeChild( NiveauxTab );
		}
	}
	if(Tab.getChildByName("ObjetsxexterieursTab")==null){
		if(inArray(arr, "ObjetsxexterieursTab")){
			Tab.addChild( ObjetsxexterieursTab );
		}
	}else{
		if(!inArray(arr, "ObjetsxexterieursTab")){
			Tab.removeChild( ObjetsxexterieursTab );
		}
	}
	if(Tab.getChildByName("ObjetsxinterieursTab")==null){
		if(inArray(arr, "ObjetsxinterieursTab")){
			Tab.addChild( ObjetsxinterieursTab );
		}
	}else{
		if(!inArray(arr, "ObjetsxinterieursTab")){
			Tab.removeChild( ObjetsxinterieursTab );
		}
	}
	if(Tab.getChildByName("ObjetsxvoiriesTab")==null){
		if(inArray(arr, "ObjetsxvoiriesTab")){
			Tab.addChild( ObjetsxvoiriesTab );
		}
	}else{
		if(!inArray(arr, "ObjetsxvoiriesTab")){
			Tab.removeChild( ObjetsxvoiriesTab );
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
	FormulaireEspaces = new formulaire_espaces();
	FormulaireEspacesxexterieurs = new formulaire_espacesxexterieurs();
	FormulaireEspacesinterieurs = new formulaire_espacesxinterieurs();
	FormulaireEtablissements = new formulaire_etablissements;
	FormulaireGeorss = new formulaire_georss;
	FormulaireGeos = new formulaire_geos();
	FormulaireNiveaux = new formulaire_niveaux();
	FormulaireObjetsxexterieurs = new formulaire_objetsxexterieurs();
	FormulaireObjetsxinterieurs = new formulaire_objetsxinterieurs();
	FormulaireObjetsxvoiries = new formulaire_objetsxvoiries();
	
	GeneralTab.addChild(FormulaireGeneral);
	MapTab.addChild(map);
	BatimentsTab.addChild(FormulaireBatiments);
	DocsTab.addChild(FormulaireDocs);
	DiagnosticsTab.addChild(FormulaireDiagnostics);
	DiagnosticsxvoirieTab.addChild(FormulaireDiagnosticsxvoirie);
	EspacesTab.addChild(FormulaireEspaces);
	EspacesxexterieursTab.addChild(FormulaireEspacesxexterieurs);
	EspacesxinterieursTab.addChild(FormulaireEspacesinterieurs);
	EtablissementsTab.addChild(FormulaireEtablissements);
	GeorssTab.addChild(FormulaireGeorss);
	GeosTab.addChild(FormulaireGeos);
	NiveauxTab.addChild(FormulaireNiveaux);
	ObjetsxexterieursTab.addChild(FormulaireObjetsxexterieurs);
	ObjetsxinterieursTab.addChild(FormulaireObjetsxinterieurs);
	ObjetsxvoiriesTab.addChild(FormulaireObjetsxvoiries);
	
	
	SelectedNode = 1;
	roDiagnostique.getNodeRelatedData(SelectedNode);
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
	/*Tab.removeChild(GeosTab);
	Tab.addChild(GeosTab);*/
	var xl:XMLListCollection=new XMLListCollection();
	xl.addItem(treeTree.dataProvider.descendants().(@idLieu == 1));
	xl.addItem(treeTree.dataProvider.descendants().(@idLieu == 2));
	treeTree.openItems = xl;
	
	logThis("button clicked");
} 

private function logThis( txt : String ) : void {
	//debugTest.text+=txt+"\n";
}

private function displayNodeProperties( event:ResultEvent ) : void {
	var obj:Object;
	var arr1:Array = new Array();
	var arrc1:ArrayCollection = new ArrayCollection();
	
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
	FormulaireGeneral.Tableau.dataProvider=arrc1;
	
	var childToPreserve:Array = new Array();
	
	for (i=1; i<event.result.length; ++i){
		if(event.result[i]['id']==-2){
			updateBreadCrumb( event.result[i].data );
		}
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
		if(event.result[i]['id']==4){
			FormulaireEspaces.displayNodeProperties( event.result[i].data );
			childToPreserve.push("EspacesTab");
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
		if(event.result[i]['id']==10){
			FormulaireNiveaux.displayNodeProperties( event.result[i].data[0] );
			childToPreserve.push("NiveauxTab");
		}
		if(event.result[i]['id']==11){
			FormulaireObjetsxexterieurs.displayNodeProperties( event.result[i].data[0] );
			childToPreserve.push("ObjetsxexterieursTab");
		}
		if(event.result[i]['id']==12){
			FormulaireObjetsxinterieurs.displayNodeProperties( event.result[i].data[0] );
			childToPreserve.push("ObjetsxinterieursTab");
		}
		if(event.result[i]['id']==13){
			FormulaireObjetsxvoiries.displayNodeProperties( event.result[i].data[0] );
			childToPreserve.push("ObjetsxvoiriesTab");
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
			
private function answerToBreadCrumbChange(event:ItemClickEvent):void {					
	trace( ButtonArray[event.index]['id'] );
	/*
	 * from here:
	 * I can try to open the node called "ButtonArray[event.index]['id']"
	 * and modify tree's view depending on it.
	 */
	 var tmpXml:XML = treeTree.dataProvider.descendants().(@idLieu == ButtonArray[event.index]['id'])[0];
	treeTree.selectedItem = tmpXml;
	treeTree.expandItem(tmpXml, false);
	SelectedNode = ButtonArray[event.index]['id'];
	logThis( "tree item has been clicked. item is:"+
			 ButtonArray[event.index]['label'] );
	if(SelectedNode>0) roDiagnostique.getNodeRelatedData(SelectedNode);
	map.showNode(SelectedNode);
}
				
private function updateBreadCrumb(arr:Array):void{
	//
	var i:int;
	var obj:Object;
	ButtonArray=[];
	for (i=0; i<arr.length; ++i){
		obj={label:arr[i][0], id:arr[i][1]};
		ButtonArray[i]=obj;
	}
	BC.dataProvider = ButtonArray;
}
