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
private var FormulaireBatiments:formulaire_batiments;
private var FormulaireDiagnostics:formulaire_diagnostics;
private var FormulaireDiagnosticsxvoirie:formulaire_diagnosticsxvoirie;
private var FormulaireDocs:formulaire_docs;
private var FormulaireEspacesxexterieurs:formulaire_espacesxexterieurs;
private var FormulaireGeos:formulaire_geos;

private function removeTabs() : void {
	
	if(Tab.getChildByName("BatimentsTab")!=null)
		Tab.removeChild( BatimentsTab );
	
	if(Tab.getChildByName("DiagnosticsTab")!=null)
		Tab.removeChild( DiagnosticsTab );
	
	if(Tab.getChildByName("DiagnosticsxvoirieTab")!=null)
		Tab.removeChild( DiagnosticsxvoirieTab );
	
	if(Tab.getChildByName("DocsTab")!=null)
		Tab.removeChild( DocsTab );
	
	if(Tab.getChildByName("EspacesxexterieursTab")!=null)
		Tab.removeChild( EspacesxexterieursTab );
	
	if(Tab.getChildByName("GeosTab")!=null)
		Tab.removeChild( GeosTab );
	
}

private function onStartup() : void {
	map = new formulaire_carte();
	removeTabs();
	
	FormulaireGeneral = new formulaire_general();
	FormulaireAutres = new formulaire_autres();
	FormulaireBatiments = new formulaire_batiments();
	FormulaireDiagnostics = new formulaire_diagnostics();
	FormulaireDiagnosticsxvoirie = new formulaire_diagnosticsxvoirie();
	FormulaireDocs = new formulaire_docs();
	FormulaireEspacesxexterieurs = new formulaire_espacesxexterieurs();
	FormulaireGeos = new formulaire_geos();
	
	GeneralTab.addChild(FormulaireGeneral);
	AutresTab.addChild(FormulaireAutres);
	MapTab.addChild(map);
	BatimentsTab.addChild(FormulaireBatiments);
	DocsTab.addChild(FormulaireDocs);
	DiagnosticsTab.addChild(FormulaireDiagnostics);
	DiagnosticsxvoirieTab.addChild(FormulaireDiagnosticsxvoirie);
	EspacesxexterieursTab.addChild(FormulaireEspacesxexterieurs);
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
	var arr2:Array = new Array();
	var arrc2:ArrayCollection = new ArrayCollection();
	
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
	
	
	removeTabs();
	
	
	for (i=1; i<event.result.length; ++i){
		tmpStr+=event.result[i]['name']+"  ";
		for (var j:String in event.result[i].data){
			obj={prop:j, val:event.result[i].data[j]};
			arr2.push(obj);
		}
		
		if(event.result[i]['id']==0){
			FormulaireBatiments.displayNodeProperties( event.result[i].data[0] );
			Tab.addChild( BatimentsTab );
		}
		if(event.result[i]['id']==1){
			FormulaireDiagnostics.displayNodeProperties( event.result[i].data );
			Tab.addChild( DiagnosticsTab );
		}
		if(event.result[i]['id']==2){
			FormulaireDiagnosticsxvoirie.displayNodeProperties( event.result[i].data[0] );
			Tab.addChild( DiagnosticsxvoirieTab );
		}
		if(event.result[i]['id']==3){
			FormulaireDocs.displayNodeProperties( event.result[i].data );
			Tab.addChild( DocsTab );
		}
		if(event.result[i]['id']==5){
			FormulaireEspacesxexterieurs.displayNodeProperties( event.result[i].data[0] );
			Tab.addChild( EspacesxexterieursTab );
		}
		if(event.result[i]['id']==9){
			map.showLatLng(event.result[i].data[0].lat,
				           event.result[i].data[0].lng,
						   event.result[i].data[0].zoom_min);
			FormulaireGeos.displayNodeProperties( event.result[i].data[0] );
			Tab.addChild( GeosTab );
		}
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
