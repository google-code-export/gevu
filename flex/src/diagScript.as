//include the constant definition of the server endpoint URL
include "grillesconfig.as";

import com.adobe.serialization.json.JSON;

import compo.*;
import compo.form.ariane;
import compo.form.batiments;
import compo.form.diagnostics;
import compo.form.docsxlieux;
import compo.form.espaces;
import compo.form.espacesxexterieurs;
import compo.form.espacesxinterieurs;
import compo.form.etablissements;
import compo.form.georss;
import compo.form.geos;
import compo.form.niveaux;
import compo.form.objetsxexterieurs;
import compo.form.objetsxinterieurs;
import compo.form.objetsxvoiries;
import compo.form.observations;
import compo.form.parcelles;
import compo.form.problemes;

import flash.display.DisplayObject;
import flash.utils.getDefinitionByName;

import mx.collections.ArrayCollection;
import mx.containers.Canvas;
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
private var idLieu:int;
private var idBase:String 

//création des références d'objet pour la création dynamique
private var o1:compo.form.ariane;
private var o2:compo.form.batiments;
private var o3:compo.form.diagnostics;
private var o4:compo.form.docsxlieux;
private var o5:compo.form.espaces;
private var o6:compo.form.espacesxexterieurs;
private var o7:compo.form.espacesxinterieurs;
private var o8:compo.form.etablissements;
private var o9:compo.form.georss;
private var o10:compo.form.geos;
private var o11:compo.form.niveaux;
private var o12:compo.form.objetsxexterieurs;
private var o13:compo.form.objetsxinterieurs;
private var o14:compo.form.objetsxvoiries;
private var o15:compo.form.observations;
private var o16:compo.form.parcelles;
private var o17:compo.form.problemes;


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
	idLieu = event.currentTarget.selectedItem.attribute("idLieu");
	
	if(idLieu>0) roDiagnostique.getNodeRelatedData(idLieu, idBase);
	
	//map.showNode(idLieu);
}

private function displayNodeProperties(event:ResultEvent) : void {
	var obj:Object = event.result;
	var ClassReference:Class;
	var instance:Object;
	
	//initialise les tabbox
	boxDiag.removeAllChildren();
	
	for(var item:String in obj){
		var arr:Array = item.split("_");
		var className:String;
		//vérifie si on traite un objet du modele
		if(arr.length == 4){
			className="compo.form."+arr[3];
		}else{
			className="compo.form."+item;
		}
		//création dynamique de l'objet
		ClassReference = getDefinitionByName(className) as Class;			
		instance = new ClassReference();
		instance.NodeData = obj[item][0];
		//ajoute l'objet au tabbox
		boxDiag.addChild(DisplayObject(instance));	
	}	
	
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