//include the constant definition of the server endpoint URL
include "grillesconfig.as";

import com.adobe.serialization.json.JSON;

import compo.*;

import flash.events.Event;
import flash.net.URLRequest;
import flash.net.URLRequestMethod;
import flash.net.URLVariables;
import flash.net.navigateToURL;

import mx.collections.ArrayCollection;
import mx.controls.Alert;
import mx.events.DropdownEvent;
import mx.events.ListEvent;
import mx.events.TreeEvent;
import mx.managers.PopUpManager;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;


[Bindable][Embed("images/voie.png")]public var voieIcon:Class;

[Bindable]public var dataArr:ArrayCollection = new ArrayCollection();
[Bindable]public var exi:Object;
[Bindable]public var idExi:String = "1";

[Bindable] private var rsSolu:Array;
[Bindable] private var rsProd:Array;
[Bindable] private var rsCout:Array;
[Bindable] private var rsDiag:Array;
[Bindable] private var rsBD:Array;
[Bindable] public var idBase:String;
[Bindable] public var idLieu:int=-1;
private var libLieu:String;
private var xmlTree:XML


public function login():void
{
	var twLog:twLogin= twLogin(
        PopUpManager.createPopUp(this, twLogin, true));
	twLog.callback = setBD;
    PopUpManager.centerPopUp(twLog);
	    	
} 

public function setBD():void{
	//initialise la liste des bases de données suivant un utilisateur
	rsBD = JSON.decode(exi.droit_3);
	/*
	roSolus.getAll();
	roProd.getAll();
	roCout.getAll();
	*/
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
	libLieu = event.currentTarget.selectedItem.attribute("lib");
	getSolusProb();
}


private function updateTreeStructure(event:ResultEvent) : void {
	
	if(!event.result) return;
	
	/* get the id of the node */
	var x:XML = <root></root>;
	x.appendChild(event.result);
	var idnoeud:int;
	idnoeud = x.node.attribute("idLieu");
	
	//vérifie si le noeud existe
	var objTree:XMLList = treeTree.dataProvider[0].descendants().(@idLieu == idnoeud);
	if(objTree.length()){
		/* add the new real node */
		objTree[0].appendChild(x.node.node);
				
		/* delete the old fake one */
		delete  treeTree.dataProvider[0].descendants().(@idLieu==idnoeud)[0].children()[0];
		
		//conserve les data du tree complet
		xmlTree = treeTree.dataProvider[0] as XML;
	}	
}


protected function getSolusProb_resultHandler(event:ResultEvent):void
{
	rsDiag = event.result as Array;
	pSolusProb.removeAllChildren();
	if(rsDiag){
		var nb:int = rsDiag.length;
		for (var i:Number=0; i < nb;i++){
			var prob:Object = rsDiag[i];
			var p:hbProb=new hbProb();
			p.name = prob.diagIdLieu+"_"+prob.id_diag;
			p.prob = prob;
			pSolusProb.addChild(p);
		}
	}
	
}


public function choixBD(event:DropdownEvent):void{
	
	idBase = this.cbBD.selectedItem["id"];
	idBase = idBase.substr(idBase.indexOf("_")+1);	
	initTree();

}

public function initTree():void{
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

public function getSolusProb():void {
	nomEtab.text = this.libLieu;
	pSolusProb.removeAllChildren(); 		
	selectCout.removeAllChildren();
	RORapport.getSolusProb(idLieu, idBase);
	cbSelection.init(); 		
}


public function calculerCout():void {
	var sols:Array = selectCout.getChildren();
	var cReg:int=0;
	var cSou:int=0;
	var c:int=0;
	for each(var sol:hbSelectSolus in sols){
		var couts:Array = sol.selectProb.getChildren();
		for each(var ct:hbCout in couts){
			c=0;
			if(ct.n_unite.value !=0) c += int(ct.unite.text)*ct.n_unite.value;
			if(ct.n_metre_lineaire.value !=0) c += int(ct.metre_lineaire.text)*ct.n_metre_lineaire.value;
			if(ct.n_metre_carre.value !=0) c += int(ct.metre_carre.text)*ct.n_metre_carre.value;
			if(ct.n_achat.value !=0) c += int(ct.achat.text)*ct.n_achat.value;
			if(ct.n_pose.value !=0) c += int(ct.pose.text)*ct.n_pose.value;
			var coutsP:Array = null;//ct.sousCout.getChildren();
			for each(var ctP:hbCout in coutsP){
				if(ctP.n_unite.value !=0) c += int(ctP.unite.text)*ctP.n_unite.value;
				if(ctP.n_metre_lineaire.value !=0) c += int(ctP.metre_lineaire.text)*ctP.n_metre_lineaire.value;
				if(ctP.n_metre_carre.value !=0) c += int(ctP.metre_carre.text)*ctP.n_metre_carre.value;
				if(ctP.n_achat.value !=0) c += int(ctP.achat.text)*ctP.n_achat.value;
				if(ctP.n_pose.value !=0) c += int(ctP.pose.text)*ctP.n_pose.value;
			}				
			//if(ct.prob.reglementaire)cReg+=c; else cSou+=c;
		}
	}
	coutReg.text = cReg + " € H.T.";
	coutSou.text = cSou + " € H.T.";
	coutTot.text = cReg + cSou + " € H.T.";

}

public function calculerRapport(send:Boolean):*{
	
	var SelSols:Array = selectCout.getChildren();
	
	if(SelSols.length == 0){
		Alert.show("Merci de sélectionner au moins une solution ou un produit.");
		return;
	}
	
	var pArr:Array = new Array; var pProb:Array; var couts:Array;
	var idSolus:int; var libSolus:String;
	
	//récupération des données sélectionnnées
	for each(var sol:hbSelectSolus in SelSols){
		idSolus = sol.solus.id_solution;
		couts = sol.selectProb.getChildren();
		pProb = new Array;
		for each(var ct:hbCout in couts){
			pProb.push(GetArrCout(ct));
		}
		pArr.push({"idSolus":idSolus, "couts":pProb});
	}	
	var pxml:String = JSON.encode(pArr);
	trace(pxml);    
    if(!send) return pxml;

	var request:URLRequest = new URLRequest(ENDPOINT_RAPPORT);
	var variables:URLVariables = new URLVariables();
	variables.pxml=pxml;
	variables.site=idBase;
	variables.id= idLieu;
	variables.model=cbModel.cb.selectedItem["id_doc"];
	request.data = variables;
	request.method = URLRequestMethod.POST;
	navigateToURL(request,"_blank");
	
}

public function GetArrCout(ct:hbCout):Array{
	var pCout:Array; var pSousCout:Array; var pProb:Array;
	var idRub:int;	var idDon:int;	var idSol:int;	var idProd:int;	var idCrit:int;	var idCout:int;
	var regle:Boolean;
	var lib:String;
	
	pProb = new Array;
	pCout = new Array;
	pSousCout = new Array;
	//idRub = ct.prob.idRub;
	//idDon = ct.prob.idDon;
	//regle = ct.prob.reglementaire != null;
	idSol = ct.cout.id_solution;		
	idCrit = ct.cout.id_critere;		
	idCout = ct.cout.id_cout;
	if(ct.cout.id_produit)
		idProd = ct.cout.id_produit;
	else			
		idProd = -1;
	//if(ct.n_unite.value !=0) 
	pCout.push({"type":"unite","val":ct.unite.text, "q":ct.n_unite.value});
	//if(ct.n_metre_lineaire.value !=0) 
	pCout.push({"type":"metre_lineaire","val":ct.metre_lineaire.text, "q":ct.n_metre_lineaire.value});
	//if(ct.n_metre_carre.value !=0) 
	pCout.push({"type":"metre_carre","val":ct.metre_carre.text, "q":ct.n_metre_carre.value});
	//if(ct.n_achat.value !=0) 
	pCout.push({"type":"achat","val":ct.achat.text, "q":ct.n_achat.value});
	//if(ct.n_pose.value !=0) 
	pCout.push({"type":"pose","val":ct.pose.text, "q":ct.n_pose.value});

	var coutsP:Array = null;//ct.sousCout.getChildren();
	for each(var ctP:hbCout in coutsP){
		pSousCout.push(GetArrCout(ctP));
	}

	pProb.push({"idRub":idRub,"idDon":idDon,"idSol":idSol,"idProd":idProd,"idCrit":idCrit,"idCout":idCout,"regle":regle,"Couts":pCout,"SousCouts":pSousCout});

	return pProb;
}


public function decocheCout():void {
	var Probs:Array = pSolusProb.getChildren();
	for each(var Prob:hbProb in Probs){
		var Sols:Array = Prob.solutions.getChildren();
		for each(var Sol:hbSolus in Sols){
			var Prods:Array = Sol.produits.getChildren();
			for each(var Prod:hbProd in Prods){
				if(Prod.cbRef.selected){
					Prod.cbRef.selected=false;
					Prod.garde();					
				}
			}
			if(Sol.cbRef.selected){
				Sol.cbRef.selected=false;
				Sol.garde();
			}						
		}
	}
}

private function SauveSelection():void{
	
		var pxml:String = calculerRapport(false);
		if(pxml){
			var pArr:Array = new Array;
			var idRapport:String = cbSelection.cb.selectedItem["id_rapport"];
			pArr["id_base"]= idBase;
			pArr["id_exi"]=idExi;
			pArr["selection"]=pxml;
			pArr["id_lieu"]=idLieu;		
			ROS.edit(idRapport,pArr);
		}
}

public function ShowSelection(pxml:String):void{
	
	if(pxml){
		var pArr:Object = JSON.decode(pxml);
		if(pArr){
			//décoche toute les solutions
			decocheCout();
        	var name:String="";
        	var c:hbSolus;
        	var p:hbProb;
        	var sc:hbProd;
	        for each (var solus:Object in pArr)
	        {
		        for each (var couts:Object in solus.couts)
		        {
			        for each (var cout:Object in couts)
			        {
			        	//récupère le problème
			        	name = cout.idRub+"_"+cout.idDon;
			        	p=hbProb(this.pSolusProb.getChildByName(name));
			        	if(p){
				        	name = "crit:"+cout.idCrit+"_cout:"+cout.idCout;
				        	//récupère la solution
				        	c=hbSolus(p.solutions.getChildByName(name));
				        	if(c){				        		
						        for each (var SousCouts:Object in cout.SousCouts)
						        {
							        for each (var souscout:Object in SousCouts)
							        {
							        	//récupère le produit
							        	name = "cout:"+souscout.idCout+"_prod:"+souscout.idProd;
							        	sc=hbProd(c.produits.getChildByName(name));
							        	if(sc){
							        		sc.cbRef.selected = true;
							        		sc.produit.cout.save = souscout.Couts;
							        		sc.garde();
							        	}			        		
							        }			        	
						        }
				        		c.cbRef.selected = true;
				        		c.solution.cout.save = cout.Couts;
				        		c.garde();				        			
				        	}			        		
			        	}
			        				        	
			        }		        	
		        }
	        }
		}
	}

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

private function fillHandler(e:Object):void
	{
		if(!e)return;
		this.cbSelection.init();
	}
protected function roSolusGetAll_resultHandler(event:ResultEvent):void
{
	this.rsSolu = event.result as Array;
}
protected function roProdGetAll_resultHandler(event:ResultEvent):void
{
	this.rsProd = event.result as Array;
}
protected function roCoutGetAll_resultHandler(event:ResultEvent):void
{
	this.rsCout = event.result as Array;
}
