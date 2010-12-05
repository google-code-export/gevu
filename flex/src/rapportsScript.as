import compo.*;

import mx.collections.ArrayCollection;
import mx.events.DropdownEvent;
import mx.rpc.events.ResultEvent;

//include the constant definition of the server endpoint URL
include "grillesconfig.as";


[Bindable]
public var dataArr:ArrayCollection = new ArrayCollection();
[Bindable]
public var exi:Object;
private var idExi:String = "";

[Bindable]	private var urlBD:String = ENDPOINT_EXEAJAX+"?f=GetBDs";
[Bindable]	private var rsBD:Object;
[Bindable]public var selectedBD:Object;
[Bindable]	private var rsEtab:Object;
[Bindable]public var selectedEtab:Object;

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
        	p.prob = prob;
			pSolusProb.addChild(p);			
        }
	}
}

public function choixBD(event:DropdownEvent):void{
	
	selectedBD = this.cbBD.selectedItem;
    
    /*
    //paramètre la requête pour récupérer la liste des territoires
	tTerre.srvTerre.cancel();
	tTerre.srvTerre.url= ENDPOINT_EXECARTO;
	//tTerre.srvTerre.url= "http://localhost/gevu/bdd/carto/etabs1.xml";
	//tTerre.srvTerre.send();
	var params:Object = new Object();
	params.f = "get_arbo_territoire";
	params.site = selectedBD.value;
	trace ("choixBD:srvTerre.url="+tTerre.srvTerre.url+"?f="+params.f+"&site="+params.site);
	tTerre.srvTerre.send(params);
	*/
	
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
		nomEtab.text = selectedEtab.titreRub;
		pSolusProb.removeAllChildren(); 		
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

public function calculerCout():void {
	var probs:Array = pSolusProb.getChildren();
	var cReg:int=0;
	var cSou:int=0;
	var c:int=0;
	for each(var prob:hbSolusProb in probs){
		var coutsS:Array = prob.couts.getChildren();
		for each(var cSolus:hbSolusCout in coutsS){
			c=0;
			if(cSolus.cbuni.selected) c = int(cSolus.unite.text);
			if(cSolus.cbml.selected) c = int(cSolus.metre_lineaire.text);
			if(cSolus.cbm2.selected) c = int(cSolus.metre_carre.text);
			if(cSolus.cbachat.selected) c = int(cSolus.achat.text);
			if(cSolus.cbpose.selected) c = int(cSolus.pose.text);
			if(int(cSolus.num.text)>1){
				c = c * int(cSolus.num.text);
			}
			if(prob.regle.selected)cReg+=c;
			if(prob.souha.selected)cSou+=c;
			c=0;
			var coutsP:Array = cSolus.couts.getChildren();
			for each(var cProd:hbProdCout in coutsP){
				if(cProd.cbuni.selected) c = int(cProd.unite.text);
				if(cProd.cbml.selected) c = int(cProd.metre_lineaire.text);
				if(cProd.cbm2.selected) c = int(cProd.metre_carre.text);
				if(cProd.cbachat.selected) c = int(cProd.achat.text);
				if(cProd.cbpose.selected) c = int(cProd.pose.text);
				if(int(cProd.num.text)>1){
					c = c * int(cProd.num.text);
				}
			}				
			if(prob.regle.selected)cReg+=c;
			if(prob.souha.selected)cSou+=c;
		}
	}
	coutReg.text = cReg + " € H.T.";
	coutSou.text = cSou + " € H.T.";
	coutTot.text = cReg + cSou + " € H.T.";

}

