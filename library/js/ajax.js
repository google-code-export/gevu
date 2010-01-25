//--------------------------------------------
// AJAX Functions
//--------------------------------------------


function AppendResult(url,doc,ajoute,cont,flex) {
  try {
	document.documentElement.style.cursor = "wait";
  
  	if(!cont)
  		cont = "box";
	
	if(!flex)flex="flex='1' "; else flex="";

	p = new XMLHttpRequest();
	p.onload = null;
	p.open("GET", url, false);
	p.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	p.send(null);

	if (p.status != "200" ){
	      alert("Réception erreur " + p.status);
	}else{
	    response = p.responseText;
		xulData="<"+cont+" id='dataBox' " + flex + 
	          "xmlns='http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul'>" +
	          response + "</"+cont+">";
		var parser=new DOMParser();
		var resultDoc=parser.parseFromString(xulData,"text/xml");
		if(!ajoute){
			//vide le conteneur
			while(doc.hasChildNodes())
				doc.removeChild(doc.firstChild);
		}
		//ajoute le résultat
		doc.appendChild(resultDoc.documentElement);
		document.documentElement.style.cursor = "auto";
	}
	
	return resultDoc ;
	//dump("AppendResult OUT \n");
   } catch(ex2){alert(ex2);document.documentElement.style.cursor = "auto";}
	
}

function InsertBeforeResult(url,doc) {
  try {
	document.documentElement.style.cursor = "wait";
	p = new XMLHttpRequest();
	p.onload = null;
	p.open("GET", url, false);
	p.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	p.send(null);

	if (p.status != "200" ){
	      alert("Réception erreur " + p.status);
	}else{
	    response = p.responseText;
		xulData="<vbox id='dataBox' flex='1'  " +
	          "xmlns='http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul'>" +
	          response + "</vbox>";
		var parser=new DOMParser();
		var resultDoc=parser.parseFromString(xulData,"text/xml");
		//ajoute le résultat
		parent = doc.parentNode
		parent.insertBefore(resultDoc.documentElement, doc.nextSibling
		);
		document.documentElement.style.cursor = "auto";
	}
	return resultDoc ;
	dump("InsertBeforeResult OUT \n");
   } catch(ex2){alert(ex2);document.documentElement.style.cursor = "auto";}
	
}




function UploadFile(url, file){
	
	 /*
	  * http://xulfr.org/wiki/ApplisWeb/Request
	  */
	document.documentElement.style.cursor = "wait";
	
	
	 const BOUNDARY = "111222111"; //ce qui va nous servir de délimiteur
	
	 const MULTI    = "@mozilla.org/io/multiplex-input-stream;1";
	 const FINPUT   = "@mozilla.org/network/file-input-stream;1";
	 const STRINGIS = "@mozilla.org/io/string-input-stream;1";
	 const BUFFERED = "@mozilla.org/network/buffered-input-stream;1";
	
	 const nsIMultiplexInputStream = Components.interfaces.nsIMultiplexInputStream;
	 const nsIFileInputStream      = Components.interfaces.nsIFileInputStream;
	 const nsIStringInputStream    = Components.interfaces.nsIStringInputStream;
	 const nsIBufferedInputStream  = Components.interfaces.nsIBufferedInputStream;
	
	 /* 1 */
	 var mis = Components.classes[MULTI].createInstance(nsIMultiplexInputStream);
	
	 /* 2 */
	 var fin = Components.classes[FINPUT].createInstance(nsIFileInputStream);
	 fin.init(file, 0x01, 0444, null); //fic est un objet de type fichier
	 var buf = Components.classes[BUFFERED].createInstance(nsIBufferedInputStream);
	 buf.init(fin, 4096);
	
	 /* 3 */
	 var hsis = Components.classes[STRINGIS].createInstance(nsIStringInputStream);
	 var sheader = new String();
	 sheader += "\r\n";
	 sheader += "--" + BOUNDARY + "\r\nContent-disposition: form-data;name=\"addfile\"\r\n\r\n1";
	 sheader += "\r\n" + "--" + BOUNDARY + "\r\n"
	 sheader += "Content-disposition: form-data;name=\"filename\";filename=\"" + file.leafName + "\"\r\n";
	 sheader += "Content-Type: application/octet-stream\r\n";
	 sheader += "Content-Length: " + file.fileSize+"\r\n\r\n";
	 hsis.setData(sheader, sheader.length);
	
	 /* 4 */
	 var endsis = Components.classes[STRINGIS].createInstance(nsIStringInputStream);
	 var bs = new String("\r\n--" + BOUNDARY + "--\r\n");
	 endsis.setData(bs, bs.length);
	
	 /* 5 */
	 mis.appendStream(hsis);
	 mis.appendStream(buf);
	 mis.appendStream(endsis);
	
	 /* 6 */
	 var xmlr = new XMLHttpRequest();
	 xmlr.open("POST", url, false);  //À faire : remplacer par l'URL correcte
	 xmlr.setRequestHeader("Content-Length", mis.available() - 2 );
	 //Je ne sais pas pouquoi -2, je dois faire une erreur quelque part
	 xmlr.setRequestHeader("Content-Type", "multipart/form-data; boundary=" + BOUNDARY);
	
	 /* 8 */
	 xmlr.send(mis);	
	document.documentElement.style.cursor = "auto";
		
}

function GetAjaxResult(url) {
  try {
	document.documentElement.style.cursor = "wait";
    response = "";
	p = new XMLHttpRequest();
	p.onload = null;
	p.open("GET", url, false);
	p.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	p.send(null);

	if (p.status != "200" ){
	      alert("Réception erreur " + p.status);
	}else{
	    response = p.responseText;
		document.documentElement.style.cursor = "auto";
	}
	return response;
	dump("GetAjaxResult OUT \n");
   } catch(ex2){alert("Ajax:GetAjaxResult:"+ex2);document.documentElement.style.cursor = "auto";}
	
}

function GetResult(url) {
  try {
	document.documentElement.style.cursor = "wait";
    response = "";
	p = new XMLHttpRequest();
	p.onload = null;
	//p.open("GET", urlExeAjax+"?f=GetCurl&url="+url, false);
	p.open("GET", url, false);
	p.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	p.send(null);

	if (p.status != "200" ){
	      alert("Réception erreur " + p.status);
	}else{
	    response = p.responseText;
		document.documentElement.style.cursor = "auto";
	}
	return response;
	dump("GetResult OUT \n");
   } catch(ex2){alert(ex2);dump("::"+ex2);	document.documentElement.style.cursor = "auto";}

}

function AfficheResult(response,params) {
  try {
	dump("AfficheResult IN response"+response+" "+params+"\n");
	document.getElementById(params).value = response;
  } catch(ex2){dump("Ajax:AfficheResult:"+ex2);}
}

function RefreshResult(response, params) {
	document.documentElement.style.cursor = "wait";
   	//alert(url);
	arrP = params.split(",");
	document.getElementById(arrP[0]).value = response;
	AjaxRequest(arrP[1],"AfficheResult",arrP[2])
	//document.documentElement.style.cursor = "auto";
}

function AjaxRequest(url,fonction_sortie,params) {

 	this.url = encodeURI(url);
 	this.fonction_sortie = fonction_sortie;
 	this.params = params;
	dump("AjaxRequest IN "+url+" "+params+"\n");
	//alert(params);

	var ajaxRequest = this;

    if (window.XMLHttpRequest) {

	    this.req = new XMLHttpRequest();										// XMLHttpRequest natif (Gecko, Safari, Opera, IE7)

		try {
	    	netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead");		// Mozilla Security
	   	} catch (e) {}

		this.req.onreadystatechange = function () { processReqChange(); }

		this.req.open("GET", this.url, true);
		this.req.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
        this.req.send(null);

		try {
	    	//console.log("request: "+url);
	   	} catch (e) {}

	} else if (window.ActiveXObject) {

	    this.req = new ActiveXObject("Microsoft.XMLHTTP");						 // IE/Windows ActiveX

        if (this.req) {
            this.req.onreadystatechange = this.req.onreadystatechange = function () { processReqChange(); }
            this.req.open("GET", this.url, true);
			this.req.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
            this.req.send();
		}

    } else {

		alert("Votre navigateur ne connait pas l'objet XMLHttpRequest.");

	}

}

function processReqChange() {

	try {
	   	////console.log("state:"+this.req.readyState);
	} catch (e) {}

	if (this.req.readyState == 4) {				// quand le fichier est chargé

		if (this.req.status == 200) {			// detécter problèmes de format

			try {
    			netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead");
   			} catch (e) {}

			try {
	   			////console.log(this.req.responseText);
			} catch (e) {}

			//eval(this.fonction_sortie+"(this.req.responseXML.documentElement)");
			eval(this.fonction_sortie+"(this.req.responseText,'"+this.params+"')");

		} else {

			alert("Il y avait un probleme avec le XML: " + this.req.statusText);

		}
	}
}