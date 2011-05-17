// ActionScript file
import mx.collections.ArrayCollection;
import mx.events.ListEvent;
import mx.events.TreeEvent;
import mx.rpc.events.ResultEvent;

private var arr:Array;
private var arrc:ArrayCollection;

private function init():void {
	// send the http request
	httpTree.send();
}

private function treeLoaded():void {
	// assign the http result data as xml to tree element
	xmlTree = XML(httpTree.lastResult.node);
	treeTree.dataProvider = xmlTree;
}	

private function onStartup() : void {
	/* roDiagnostique.getAll(); */
}

private function dogetAllResult( event:ResultEvent ) : void {
	dg.dataProvider = event.result; 
}

private function dogetSonResult( event:ResultEvent ) : void {
	dg.dataProvider = event.result; 
}

private function treeItemOpened( event:TreeEvent ) : void {
	logThis("tree item has been developped");
}

private function treeItemClicked( event:ListEvent ) : void {
	var str:String;
	var tt:int;
	tt = event.currentTarget.selectedItem.attribute("idLieu");
	str=event.currentTarget.selectedItem.attribute("lib");
	logThis("tree item has been clicked. item is:"+str);
	
	roDiagnostique.getFields(tt);
}

private function testButtinClicked() : void {
	logThis("button clicked");
	roDiagnostique.getSon(-1);
} 

private function logThis( txt : String ) : void {
	debugTest.text+=txt+"\n";
}

private function displayNodeProperties( event:ResultEvent ) : void {
	arrc=new ArrayCollection();
	arr=new Array();
	var obj1:Object={prop:"niv",		val:event.result[0].niv};	arr.push(obj1);
	var obj2:Object={prop:"lib",		val:event.result[0].lib};	arr.push(obj2);
	var obj3:Object={prop:"id_lieu",	val:event.result[0].id_lieu};	arr.push(obj3);
	var obj4:Object={prop:"id_parent",	val:event.result[0].lieu_parent};	arr.push(obj4);
	
	
	arrc.source=arr;
	dg.dataProvider=arrc
}

