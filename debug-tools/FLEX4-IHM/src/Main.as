// ActionScript file
import mx.collections.ArrayCollection;
import mx.events.ListEvent;
import mx.events.TreeEvent;
import mx.rpc.events.ResultEvent;


private var TreeObject:XML;
private var xmlTree:XML

private function init():void {
	/*  old methode, with an xml file 
	httpTree.send(); */
	
	xmlTree = 
	<node idLieu="-1" lib="root">
	</node>;
	treeTree.dataProvider=xmlTree;
	roDiagnostique.getXmlNode(1);
	treeTree.showRoot=false;
}

private function treeLoaded():void {
	// assign the http result data as xml to tree element
	TreeObject = XML(httpTree.lastResult.node);
	treeTree.dataProvider = TreeObject;
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
	var tt:int;
	tt = event.currentTarget.selectedItem.attribute("idLieu");
	logThis( "tree item has been clicked. item is:"+
			 event.currentTarget.selectedItem.attribute("lib") );
	
	if(tt>0) roDiagnostique.getFields(tt);
}

private function testButtonClicked() : void {
	logThis("button clicked");
} 

private function logThis( txt : String ) : void {
	debugTest.text+=txt+"\n";
}

private function displayNodeProperties( event:ResultEvent ) : void {
	var obj:Object;
	var arr:Array = new Array();
	var arrc:ArrayCollection = new ArrayCollection();
	
	obj={prop:"niv",		val:event.result[0].niv};			arr.push(obj);
	obj={prop:"lib",		val:event.result[0].lib};			arr.push(obj);
	obj={prop:"id_lieu",	val:event.result[0].id_lieu};		arr.push(obj);
	obj={prop:"id_parent",	val:event.result[0].lieu_parent};	arr.push(obj);
	
	arrc.source=arr;
	dg.dataProvider=arrc
}

private function updateTreeStructure( event:ResultEvent ) : void {
	//xmlTree.appendChild(event.result);
	treeTree.dataProvider[0].appendChild(event.result);
	logThis(xmlTree);

	//treeTree.dataProvider=xmlTree;
}
