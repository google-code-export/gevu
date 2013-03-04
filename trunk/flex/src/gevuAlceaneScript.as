import compo.twLogin;

import mx.managers.PopUpManager;
import mx.controls.Tree;

[Bindable] public var exi:Object;
[Bindable] public var idExi:String = "";
[Bindable] public var dataGeo:Object;
[Bindable] public var dataStat:Object;

private function login():void
{
	//construction de la fenêtre d'édition
	var twLog:twLogin= twLogin(
		PopUpManager.createPopUp(this, twLogin, true));
	twLog.callback = init;
	PopUpManager.centerPopUp(twLog);
	//init();
} 

private function init():void
{
	lblUser.text = exi.ExiRole;
}

/**
 083	 *  Expands every branch of the tree starting from the specified node
 084	 *  If no xml is passed as an argument, every branch of the tree will be opened.
 085	 *  @param xml Tree node from which the differente branches of the tree will be opened.
 086	 */
public function expandEveryBranchFrom(tree:Tree, xmlSource:XML, xml:XML=null):void{
	if(xml==null){
		expandEveryBranchFrom(tree, null, xmlSource);
	}else{
		tree.expandItem(xml,true,false);
		tree.expandChildrenOf(xml,true);
		for each (var element:XML in xml.children()){
			expandEveryBranchFrom(tree, null, element);
		}
	}
}
