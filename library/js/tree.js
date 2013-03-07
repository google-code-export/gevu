var start = new Object();
var end = new Object();

function GetTreeSelect(idTree,idTrace,colTrace){

  try {
	tree = document.getElementById(idTree);
	//pour gérer la multisélection
	var numRanges = tree.view.selection.getRangeCount();
	for (var t = 0; t < numRanges; t++){
		tree.view.selection.getRangeAt(t,start,end);
		for (var v = start.value; v <= end.value; v++){
			//alert("Item " + tree.view.getCellText(v,c) + " sélectionné.");
			for (var i = 0; i < idTrace.length; i++){
				c = tree.treeBoxObject.columns[colTrace[i]];
				document.getElementById(idTrace[i]).value=tree.view.getCellText(v,c);
			}
		}
	}
  } catch(ex2){ alert("tree:GetTreeSelect:"+ex2); }
}


function Tree_AddItem(parentitem, cells)
{
  try {
	//alert(cells[0]);
	var parent;
	parent = document.getElementById(parentitem);
	if (!parent){ return null; alert("null");}

	var item = document.createElement("treeitem");
	item.setAttribute("id", "treeitem" + cells[0]);
	var row = document.createElement("treerow");
	var cell0 = document.createElement("treecell");
	var cell1 = document.createElement("treecell");
	var cell2 = document.createElement("treecell");
	var cell3 = document.createElement("treecell");

	cell0.setAttribute("label", cells[0]);
	cell1.setAttribute("label", cells[1]);
	cell2.setAttribute("label", cells[2]);
	cell3.setAttribute("label", cells[3]);

	row.appendChild(cell0);
	row.appendChild(cell1);
	row.appendChild(cell2);
	row.appendChild(cell3);

	item.appendChild(row);

	parent = GetItemOrChildren(parent,item,cells[0]);

	// set open status of the item
	parent.setAttribute("open", "true");
	
  } catch(ex2){ alert("::"+ex2); }
 }

 function GetItemOrChildren(parent,item,id)
{
	// we distinguish the case that
	//		the container of the item is empty --> create new treechildren object and append item
	//		a treechildren-object already exists --> get the id and append new item to this one
	if (parent.getAttribute("container") != "true") {
		//alert('no conteneur');
		var children = document.createElement("treechildren");
		parent.setAttribute("container", "true");
		//item.setAttribute("container", "true");
		children.setAttribute("id", "treechildren" + id);
		children.appendChild(item);
		children.setAttribute("open", "true");
		parent.appendChild(children);
	} else {
		//alert('conteneur');
		var listenodes = parent.childNodes;
		var premnode = listenodes.item(0);					
		premnode.appendChild(item);
		
		//	deplacement du nouvel element en fin de liste			
		
		var container = parent.getElementsByTagName('treechildren')[0];
		try { container.removeChild(item) } catch(e) { }
		container.appendChild(item);
		
	}
	return parent;
}
 
 function GetTreeDom(file)
{
	
  try {
	var tree = document.getElementById(TreeId);
	
	var doc = "<rdf:RDF xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#' xmlns:lex='http://lex#'>";
	doc += "<rdf:Description rdf:about='urn:roots'>";
	doc += "<lex:titre>"+file.path+"</lex:titre>";
	doc += "<lex:visible>check_yes</lex:visible>";
	doc += "<lex:icone>images/cell.png</lex:icone>";
	doc += "<lex:file>photo_001</lex:file>";
	doc += "<lex:ID>0</lex:ID>";
	doc += "</rdf:Description>";	

	cID = tree.treeBoxObject.columns[2];
	for (i=0; i<tree.treeBoxObject.view.rowCount; i++)
	{
		IDi = i;//tree.treeBoxObject.view.getCellText(i,cID);
		doc += "<rdf:Description rdf:about='urn:root:"+IDi+"'>";

		for (j=0; j<tree.treeBoxObject.columns.count; j++)
		{
			c = tree.treeBoxObject.columns[j];
			name =tree.treeBoxObject.columns[j].element.getAttribute('label');
			if(name=="visible")
				val = GetVisible(tree, i);
			else
				val = tree.treeBoxObject.view.getCellText(i,c);
			doc += "<lex:"+name+">"+val+"</lex:"+name+">";
		}
		doc += "</rdf:Description>";													   
	}
	//création des hiérarchies
	doc += "<rdf:Seq rdf:about='urn:roots'>";
	doc += "<rdf:li rdf:resource='urn:root:0'/>";
	doc += "</rdf:Seq>";	
	doc += GetHierarchie(tree, 0);
	
	doc += "</rdf:RDF>";

  } catch(ex){ dump(ex); }
	
	return doc;
}

function GetHierarchie(tree, i)
{
	doc = "";
	cID = tree.treeBoxObject.columns[2];
	if(i<tree.treeBoxObject.view.rowCount){
		niv = tree.treeBoxObject.view.getLevel(i);
		isContainer = tree.treeBoxObject.view.isContainer(i);
		dump("-- "+i+" Hiérarchie "+isContainer+" --> "+niv+"\n");
		if(isContainer){
			IDi = i;//tree.treeBoxObject.view.getCellText(i,cID);
			doc += "<rdf:Seq rdf:about='urn:root:"+IDi+"'>";
			//création de la liste du niveau
			for (j=0; j<tree.treeBoxObject.view.rowCount; j++)
			{
				if(tree.treeBoxObject.view.getLevel(j)==(niv+1)){
					//vérifie que la branche est un enfant
					if(tree.treeBoxObject.view.getParentIndex(j)==i){
						IDj = j;//tree.treeBoxObject.view.getCellText(j,cID);
						doc += "<rdf:li rdf:resource='urn:root:"+IDj+"'/>";
					}
				}
			}
			doc += "</rdf:Seq>";
		}
		
		doc += GetHierarchie(tree, i+1);	
	}
	return doc;	
}

function SaveTree(file)
{
	//http://developer.mozilla.org/fr/docs/Extraits_de_code:Fichiers_E/S
	//var tree = document.getElementById(TreeId);
	//var doc = tree.treeBoxObject.view;//GetTreeDom();
	var doc = GetTreeDom(file);

	dump("SaveTree lancée "+file.path+"\n");
  try {

	var serializer = new XMLSerializer();
	var foStream = Components.classes["@mozilla.org/network/file-output-stream;1"]
				   .createInstance(Components.interfaces.nsIFileOutputStream);
	foStream.init(file, 0x02 | 0x08 | 0x20, 0666, 0); // write, create, truncate
	//serializer.serializeToStream(doc, foStream, "");   // rememeber, doc is the DOM tree
	
	foStream.write(doc, doc.length);
	foStream.close();			   	

  } catch(ex){ dump(ex); }

	dump("SaveTree finite\n");
}
 
