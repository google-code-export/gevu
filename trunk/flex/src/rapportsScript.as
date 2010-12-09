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
	var sols:Array = selectCout.getChildren();
	var cReg:int=0;
	var cSou:int=0;
	var c:int=0;
	for each(var sol:hbSelectSolus in sols){
		var couts:Array = sol.selectProb.getChildren();
		for each(var ct:hbCout in couts){
			c=0;
			if(ct.n_unite.value !=0) c = int(ct.unite.text)*ct.n_unite.value;
			if(ct.n_metre_lineaire.value !=0) c = int(ct.metre_lineaire.text)*ct.n_metre_lineaire.value;
			if(ct.n_metre_carre.value !=0) c = int(ct.metre_carre.text)*ct.n_metre_carre.value;
			if(ct.n_achat.value !=0) c = int(ct.achat.text)*ct.n_achat.value;
			if(ct.n_pose.value !=0) c = int(ct.pose.text)*ct.n_pose.value;
			if(ct.prob.reglementaire)cReg+=c; else cSou+=c;
			c=0;
			/*
			var coutsP:Array = cSolus.couts.getChildren();
			for each(var cProd:hbProdCout in coutsP){
				ct=hbCout(cProd.detailCout.getChildAt(0));
				if(ct.n_unite.value !=0) c = int(ct.unite.text)*ct.n_unite.value;
				if(ct.n_metre_lineaire.value !=0) c = int(ct.metre_lineaire.text)*ct.n_metre_lineaire.value;
				if(ct.n_metre_carre.value !=0) c = int(ct.metre_carre.text)*ct.n_metre_carre.value;
				if(ct.n_achat.value !=0) c = int(ct.achat.text)*ct.n_achat.value;
				if(ct.n_pose.value !=0) c = int(ct.pose.text)*ct.n_pose.value;
			}				
			if(prob.regle.selected)cReg+=c;
			if(prob.souha.selected)cSou+=c;
			*/
		}
	}
	coutReg.text = cReg + " € H.T.";
	coutSou.text = cSou + " € H.T.";
	coutTot.text = cReg + cSou + " € H.T.";

}

