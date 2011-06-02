// ActionScript file
import flash.events.ErrorEvent;

import formulaires.formulaire_batiments;

import mx.collections.ArrayCollection;
import mx.containers.TitleWindow;
import mx.controls.Alert;
import mx.events.ListEvent;
import mx.events.TreeEvent;
import mx.managers.PopUpManager;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;


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

private function onStartup() : void {
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
	
	if(SelectedNode>0) roDiagnostique.getFields(SelectedNode);
}

private function testButtonClicked() : void {
	var _window:formulaire_batiments;
	_window = formulaire_batiments(PopUpManager.createPopUp(this, formulaire_batiments, true));
	PopUpManager.centerPopUp(_window);
	_window.showNode(SelectedNode);
	logThis("button clicked");
} 

private function logThis( txt : String ) : void {
	debugTest.text+=txt+"\n";
	debugTest.verticalScrollPosition = debugTest.maxVerticalScrollPosition;
}

private function displayNodeProperties( event:ResultEvent ) : void {
	var obj:Object;
	var arr:Array = new Array();
	var arrc:ArrayCollection = new ArrayCollection();
	
	var tmpArr:Array;
	var tmpStr:String="";
	tmpArr = event.result.type;
	
	if(!tmpArr){
		tmpStr="-";
	}else{
		for (var i:int=0; i<tmpArr.length; ++i){
			tmpStr+=tmpArr[i]['name']+"  ";
		}
	}
	
	obj={prop:"lib",		val:event.result.lib};			arr.push(obj);
	obj={prop:"id_lieu",	val:event.result.id_lieu};		arr.push(obj);
	obj={prop:"id_parent",	val:event.result.lieu_parent};	arr.push(obj);
	obj={prop:"niv",		val:event.result.niv};			arr.push(obj);
	obj={prop:"type",		val:tmpStr};					arr.push(obj);
	
	arrc.source=arr;
	dg.dataProvider=arrc
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
