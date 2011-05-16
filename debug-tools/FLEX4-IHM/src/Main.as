// ActionScript file
import mx.logging.Log;


/*
private function boutonClickHandler():void{
	txtInput.text="Hello World of Zero!";
}*/

private function init():void {
	// send the http request
	httpTree.send();
}

private function treeLoaded():void {
	// assign the http result data as xml to tree element
	xmlTree = XML(httpTree.lastResult.node);
	treeTree.dataProvider = xmlTree;
}	


/*  remote object test  */
import mx.rpc.events.ResultEvent;

private function onStartup() : void {
	roDiagnostique.getAll();
}

private function doContactsResult( event:ResultEvent ) : void {
	dg.dataProvider = event.result; 
}
