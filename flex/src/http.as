import mx.controls.Alert;
import mx.managers.CursorManager;
import mx.rpc.AsyncToken;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;
import mx.rpc.http.HTTPService;

include "grillesconfig.as";

private var gateway:HTTPService = new HTTPService();


	public function initGateway():void
	{
		gateway.url = ENDPOINT_URL;
		gateway.method = "POST";
		gateway.useProxy = false;
		gateway.resultFormat = "e4x";
	    gateway.addEventListener(ResultEvent.RESULT, resultHandler);
	    gateway.addEventListener(FaultEvent.FAULT, faultHandler);
	}

	public function resultHandler(e:ResultEvent):void
	{
	    var topass:* = deserialize(e.result, e);
	    e.token.handler.call(null, topass);
	}
	
	public function faultHandler(e:FaultEvent):void
	{
	    CursorManager.removeBusyCursor();
		var errorMessage:String = "Connection error: " + e.fault.faultString; 
	    if (e.fault.faultDetail) 
	    { 
	        errorMessage += "\n\nAdditional detail: " + e.fault.faultDetail; 
	    } 
	    Alert.show(errorMessage);
	}

	public function doRequestDirect(param:String, callback:Function):void{
		
		    var urlRequest:URLRequest = new URLRequest(ENDPOINT_URL);
		    urlRequest.data = param;
			trace ("http.as:doRequestDirect:query=" +ENDPOINT_URL+"?"+urlRequest.data);
		    urlRequest.method = URLRequestMethod.POST;
		    var urlLoader:URLLoader = new URLLoader(urlRequest);
		    urlLoader.addEventListener("complete", callback);
		
	}

	public function doRequest(method_name:String, parameters:Object, callback:Function):void
	{
		
	    // add the method to the parameters list
	    parameters['f'] = method_name;
	
	    gateway.request = parameters;
	
	    var call:AsyncToken = gateway.send();
	    call.request_params = gateway.request;
		
	    var query:String=gateway.url+"?bug=true";
		for (var i:String in parameters)
		{
		    query= query+"&"+i+"="+parameters[i];
		}
		trace ("http.as:doRequest:query=" +query);
		
	    call.handler = callback;
	}

	public function deserialize(obj:*, e:*):*
	{
		if(obj=="")return;
		
	    var toret:Object = {};
	    
	    toret.originalEvent = e;
	
	    if (obj.data.elements("error").length() > 0)
	    {
	        toret.isError = true;
	        toret.data = obj.data;
	    }
	    else
	    {
	        toret.isError = false;
	        toret.metadata = obj.metadata;
	        toret.data = obj.data;
	    }
	
	    return toret;
	}
