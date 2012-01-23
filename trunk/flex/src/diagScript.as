//include the constant definition of the server endpoint URL
include "grillesconfig.as";

import com.adobe.serialization.json.JSON;

import compo.*;
import compo.ariane;
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
import flash.events.MouseEvent;
import flash.utils.getDefinitionByName;

import mx.collections.ArrayCollection;
import mx.collections.ICollectionView;
import mx.collections.XMLListCollection;
import mx.containers.Canvas;
import mx.controls.Alert;
import mx.controls.LinkButton;
import mx.events.ListEvent;
import mx.events.TreeEvent;
import mx.managers.PopUpManager;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;

[Bindable][Embed("images/voie.png")]public var voieIcon:Class;
[Bindable] public var exi:Object;
[Bindable] public var idExi:String = "";
[Bindable] public var dataGeo:Object;
[Bindable] public var dataStat:Object;

private var TreeObject:XML;
private var xmlTree:XML
private var idLieu:int;
public var idBase:String; 
private var arrSelect:Array;
private var objLieuFind:Array; 

//création des références d'objet pour la création dynamique
private var o1:compo.ariane;
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
	cartoIF.visible = true;

	//construction de la listes des bases disponibles
	var dataBases:Array = JSON.decode(this.exi.droit_3);
	cbBases.dataProvider = dataBases;
	//roDiagnostique.getTablesNames();

}

protected function cbBases_changeHandler(event:ListEvent):void
{
	idBase = cbBases.selectedItem.id;
	idBase = idBase.substr(idBase.indexOf("_")+1);
	cnvTerre.enabled = true;
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

protected function treeTree_itemEditEndHandler(event:ListEvent):void
{
	var i:int = event.itemRenderer.data.attribute("idLieu");
	var arr:Array = new Array();
	arr["lib"]= event.currentTarget.itemEditorInstance.text;
	roDiagnostique.editLieu(i,arr,idBase);	
}

protected function lieuxEdit_resultHandler(event:ResultEvent):void
{
	// TODO Auto-generated method stub
	var mess:String = event.result as String;
}


private function btnFind_clickHandler(event:MouseEvent):void
{
	//sauvegarde le tree
	treeTree.dataProvider = xmlTree;
	if (txtFind.text!="" && event.target.id == "btnFind"){
		roDiagnostique.findLieu("",txtFind.text, idBase);
	}else if(idFind.text!="" && event.target.id == "btnFindId"){
		roDiagnostique.findLieu(idFind.text,"", idBase);
	}else{
		Alert.show("Veuillez saisir une recherche", "Recherche d'un lieu");		
	}
	
}

private function displayReponse(event:ResultEvent) : void {
	
	objLieuFind = event.result as Array;
	arrSelect = new Array;
	
	FindId.removeAllChildren();
	FindLib.removeAllChildren();

	if(objLieuFind.id.length!=0){
		//repFindId.text = objLieuFind.lib.length + " identifiant(s) trouvé(s)";
		treeSelectFind("id");
		treeAfficheFind("id");
	}
	if(objLieuFind.lib.length!=0){
		//repFindLib.text = objLieuFind.lib.length + " libellé(s) trouvé(s)";
		treeSelectFind("lib");
		treeAfficheFind("lib");
	}
}

private function treeAfficheFind(type:String) : void {
	for each(var oL:Object in objLieuFind[type]){
		var lnk:LinkButton = new LinkButton;
		lnk.label = oL.ariane[oL.ariane.length-1].lib;
		lnk.data = oL.tree;
		lnk.addEventListener(MouseEvent.CLICK, treeChargeFind);
		if(type=="id"){
			FindId.addChild(lnk);
		}
		if(type=="lib"){
			FindLib.addChild(lnk);
		}
	}
}

private function treeChargeFind(event:MouseEvent):void{
	//met à jour le tree
	treeTree.dataProvider = event.target.data;
	//ouvre les item
	treeTree.openItems = event.target.data..node;
}


protected function btnShowAll_clickHandler(event:MouseEvent):void
{
	treeTree.dataProvider = xmlTree;
	treeTree.openItems = xmlTree;
}


private function treeSelectFind(type:String) : void {
	//pour chaque lieu trouvé
	for each(var oL:Object in objLieuFind[type]){
		//vérifie la présence de chaque niveau du plus global au plus précis
		var idLieu:int = -1, i:int=1;
		for each(var oN:Object in oL.ariane){
			var x:XML = treeTree.dataProvider[0].descendants().(@idLieu == oN['id_lieu'])[0];
			if(x){
				//vérifie s'il faut charger les enfants du noeud
				if(x[0].node.@fake=="1"){
					roDiagnostique.getXmlNode(oN['id_lieu'],idBase);
				}
				//ouvre le noeud
				treeTree.expandItem(x,true);
				//garde le noeud pour le sélectionner
				arrSelect.push(x);
				//conserve l'identifiant pour ajouter les nouveaux noeuds
				idLieu = oN['id_lieu'];
			}else{
				//création du noeud
				roDiagnostique.getXmlNode(oN['id_lieu'],idBase);
				var xN:XML = <node />;
				xN.@idLieu = oN['id_lieu'];
				xN.@lib = oN['lib'];
				xN.@niv = oN['niv'];
				//fake à 1 pour le chargement des données
				xN.@fake = 1;
				treeTree.dataProvider[0].descendants().(@idLieu == idLieu)[0].appendChild(xN.node);
				//fake à 0 pour la selection du noeud
				xN.@fake = 0;
				arrSelect.push(xN.node);
			}
			i++;
		}			
	}
	treeTree.selectedItems=arrSelect;
}

private function treeItemClicked(event:ListEvent) : void {
	idLieu = event.currentTarget.selectedItem.attribute("idLieu");
	
	if(idLieu>0) roDiagnostique.getNodeRelatedData(idLieu, idExi, idBase);
	
	//map.showNode(idLieu);
}

private function displayNodeProperties(event:ResultEvent) : void {
	var obj:Object = event.result;
	var ClassReference:Class;
	var instance:Object;
	
	//initialise les tabbox
	tabDiag.removeAllChildren();
	
	for(var item:String in obj){
		var arr:Array = item.split("_");
		var className:String;
		var place:int;
		//vérifie si on traite un objet du modele
		if(arr.length == 4){
			//création dynamique de l'objet
			className="compo.form."+arr[3];
			ClassReference = getDefinitionByName(className) as Class;			
			instance = new ClassReference();
			//gestion des cas particuliers
			place = -1;
			switch (arr[3]) {
				case "diagnostics":
					if(obj[item].enfants){
						instance.NodeData = obj[item];
						place = 1;
					}
					this.dataStat = obj[item].diag.stat.EtatDiag 
					stat.idLieu = this.idLieu;
					stat.init();
					break;
				case "geos":
					dataGeo = obj["Models_DbTable_Gevu_geos"][0];
					carto.init();			
					cartoIF.callChangeGeo();
					break;
				case "docsxlieux":
					break;
				default:
					place = 1;
					instance.NodeData = obj[item][0];
			}
		}
		//vérifie la place de l'objet
		if(item == "ariane"){
			//on ajoute un élément pour la création d'un nouveau lieu
			var o:Object  = {id_lieu:-1, lib:"-> AJOUTER"};
			obj[item][obj[item].length] = o;
			bbAriane.NodeData = obj[item];
		}else{
			if(place > 0){
				//ajoute l'objet
				tabDiag.addChild(DisplayObject(instance));				
			}
		}
	}	
	
}

public function ajouterLieu():void{
	Alert.show("Voilà le nouveau lieu");
}

private function updateTreeStructure(event:ResultEvent) : void {
	
	if(!event.result) return;
	
	/* get the id of the node */
	var x:XML = <root></root>;
	x.appendChild(event.result);
	var idnoeud:int;
	idnoeud = x.node.attribute("idLieu");
	
	/* add the new real node */
	treeTree.dataProvider[0].descendants().(@idLieu == idnoeud)[0].appendChild(x.node.node);
	
	if(arrSelect){
		//ajoute le noeud à la sélection
		x = treeTree.dataProvider[0].descendants().(@idLieu == idnoeud)[0];
		treeTree.expandItem(x,true);
		arrSelect.push(x);
		treeTree.selectedItems = arrSelect;
	}

	/* delete the old fake one */
	delete  treeTree.dataProvider[0].descendants().(@idLieu==idnoeud)[0].children()[0];

	//conserve les data du tree complet
	xmlTree = treeTree.dataProvider[0] as XML;
	
}