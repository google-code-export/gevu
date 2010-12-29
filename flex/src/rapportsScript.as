import com.adobe.serialization.json.JSON;

import compo.*;

import flash.events.Event;

import mx.collections.ArrayCollection;
import mx.controls.Alert;
import mx.events.DropdownEvent;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;

//include the constant definition of the server endpoint URL
include "grillesconfig.as";

[Bindable]
public var dataArr:ArrayCollection = new ArrayCollection();
[Bindable]
public var exi:Object;
[Bindable]
public var idExi:String = "1";

[Bindable] private var urlBD:String = ENDPOINT_EXEAJAX+"?f=GetBDs";
[Bindable] private var rsBD:Object;
[Bindable] public var selectedBD:Object;
[Bindable] private var rsEtab:Object;
[Bindable] public var selectedEtab:Object;

public function init():void
{
	/*
	var twLog:twLogin= twLogin(
        PopUpManager.createPopUp(this, twLogin, true));
	twLog.endPoint=ENDPOINT_SERVICE;
    PopUpManager.centerPopUp(twLog);
	*/
	
    srvBD.send();    	
} 

public function readXmlBD(event:ResultEvent):void{
    //récupère les geoloc
    rsBD = event.result.menuitems.menuitem;
}

public function readXmlEtab(event:ResultEvent):void{
	var arr:Object = event.result.grilles;
	if(arr){
	    rsEtab = arr.terre;	
	}
}


public function readXmlSolus(event:ResultEvent):void{
	var arr:Object = event.result.solus;
	pSolusProb.removeAllChildren();
	if(arr){
        for each (var prob:Object in arr.prob)
        {
        	var p:hbSolusProb=new hbSolusProb();
        	p.name = prob.idRub+"_"+prob.idDon
        	p.prob = prob;
			pSolusProb.addChild(p);			
        }
	}
}

public function choixBD(event:DropdownEvent):void{
	
	selectedBD = this.cbBD.selectedItem;
    
	srvEtab.cancel();
	srvEtab.url = ENDPOINT_EXECARTO
	var params:Object = new Object();
	params.f = "get_arbo_grille";
	params.site = selectedBD.value;
	params.idGrille = 55;
	trace ("choixBD:srvEtab.url="+ENDPOINT_EXECARTO+"?f="+params.f+"&site="+params.site+"&idGrille="+params.idGrille);
	srvEtab.send(params);
	
}

public function selectEtab(event:Event):void {
	selectedEtab = event.currentTarget.selectedItem;
	if(selectedEtab){
		boxEtab.visible=true;
		nomEtab.text = selectedEtab.titreRub;
		pSolusProb.removeAllChildren(); 		
		selectCout.removeAllChildren();
		showSolusProbEtab();
		cbSelection.init(); 		
	}
}

public function showSolusProbEtab():void {
	if(selectedEtab){
		srvSolus.cancel();
		srvSolus.url = ENDPOINT_EXEAJAX;
		var params:Object = new Object();
		params.f = "GetSolusProbEtab";
		params.site = selectedBD.value;
		params.idEtab = selectedEtab.idRub;
		trace ("showSolusProbEtab:srvSolus.url="+ENDPOINT_EXEAJAX+"?f="+params.f+"&site="+params.site+"&idEtab="+params.idEtab);
		srvSolus.send(params);		
	}
}
public function ForceCalcul():void{
	srvFC.cancel();
	srvFC.url = ENDPOINT_EXEAJAX;
	var params:Object = new Object();
	params.f = "SetChoixAffichage";
	params.idXul = ForceCalcul;
	if(cbForceCalcul.selected){
		params.valeur = "true";		
	}else{
		params.valeur = "false";				
	}
	trace ("ForceCalcul:srvFC.url="+ENDPOINT_EXEAJAX+"?f="+params.f+"&idXul="+params.idXul+"&valeur="+params.valeur);
	srvFC.send(params);			
		
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
			var coutsP:Array = ct.sousCout.getChildren();
			for each(var ctP:hbCout in coutsP){
				if(ctP.n_unite.value !=0) c += int(ctP.unite.text)*ctP.n_unite.value;
				if(ctP.n_metre_lineaire.value !=0) c += int(ctP.metre_lineaire.text)*ctP.n_metre_lineaire.value;
				if(ctP.n_metre_carre.value !=0) c += int(ctP.metre_carre.text)*ctP.n_metre_carre.value;
				if(ctP.n_achat.value !=0) c += int(ctP.achat.text)*ctP.n_achat.value;
				if(ctP.n_pose.value !=0) c += int(ctP.pose.text)*ctP.n_pose.value;
			}				
			if(ct.prob.reglementaire)cReg+=c; else cSou+=c;
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
		idSolus = sol.cout.id_solution;
		couts = sol.selectProb.getChildren();
		pProb = new Array;
		for each(var ct:hbCout in couts){
			pProb.push(GetArrCout(ct));
		}
		pArr.push({"idSolus":idSolus, "couts":pProb});
	}	
	var pxml:String = JSON.encode(pArr);
    
    if(!send) return pxml;

	var request:URLRequest = new URLRequest(ENDPOINT_RAPPORT);
	var variables:URLVariables = new URLVariables();
	variables.pxml=pxml;
	variables.site=selectedBD.value;
	variables.id=selectedEtab.idRub;
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
	idRub = ct.prob.idRub;
	idDon = ct.prob.idDon;
	regle = ct.prob.reglementaire != null;
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

	var coutsP:Array = ct.sousCout.getChildren();
	for each(var ctP:hbCout in coutsP){
		pSousCout.push(GetArrCout(ctP));
	}

	pProb.push({"idRub":idRub,"idDon":idDon,"idSol":idSol,"idProd":idProd,"idCrit":idCrit,"idCout":idCout,"regle":regle,"Couts":pCout,"SousCouts":pSousCout});

	return pProb;
}


public function decocheCout():void {
	var Probs:Array = pSolusProb.getChildren();
	for each(var Prob:hbSolusProb in Probs){
		var Sols:Array = Prob.couts.getChildren();
		for each(var Sol:hbSolusCout in Sols){
			var Prods:Array = Sol.couts.getChildren();
			for each(var Prod:hbProdCout in Prods){
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
	
	if(selectedEtab){
		var pxml:String = calculerRapport(false);
		if(pxml){
			var pArr:Array = new Array;
			var idRapport:String = cbSelection.cb.selectedItem["id_rapport"];
			pArr["site"]=selectedBD.value;
			pArr["id_exi"]=idExi;
			pArr["selection"]=pxml;
			pArr["id_lieu"]=selectedEtab.idRub;		
			ROS.edit(idRapport,pArr);
		}
	}
}

public function ShowSelection(pxml:String):void{
	
	if(pxml){
		var pArr:Object = JSON.decode(pxml);
		if(pArr){
			//décoche toute les solutions
			decocheCout();
        	var name:String="";
        	var c:hbSolusCout;
        	var p:hbSolusProb;
        	var sc:hbProdCout;
	        for each (var solus:Object in pArr)
	        {
		        for each (var couts:Object in solus.couts)
		        {
			        for each (var cout:Object in couts)
			        {
			        	//récupère le problème
			        	name = cout.idRub+"_"+cout.idDon;
			        	p=hbSolusProb(this.pSolusProb.getChildByName(name));
			        	if(p){
				        	name = "crit:"+cout.idCrit+"_cout:"+cout.idCout;
				        	//récupère la solution
				        	c=hbSolusCout(p.couts.getChildByName(name));
				        	if(c){				        		
						        for each (var SousCouts:Object in cout.SousCouts)
						        {
							        for each (var souscout:Object in SousCouts)
							        {
							        	//récupère le produit
							        	name = "cout:"+souscout.idCout+"_prod:"+souscout.idProd;
							        	sc=hbProdCout(c.couts.getChildByName(name));
							        	if(sc){
							        		sc.cbRef.selected = true;
							        		sc.cout.save = souscout.Couts;
							        		sc.garde();
							        	}			        		
							        }			        	
						        }
				        		c.cbRef.selected = true;
				        		c.cout.save = cout.Couts;
				        		c.garde();				        			
				        	}			        		
			        	}
			        				        	
			        }		        	
		        }
	        }
		}
	}

}

	public function faultHandlerService(fault:FaultEvent):void
	{
		Alert.show(fault.fault.faultCode.toString(), "FaultHandlerService");
	}
	private function fillHandler(e:Object):void
	{
		if(!e)return;
		this.cbSelection.init();
	}
