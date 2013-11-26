/**
 * change this to point to your poll.php on the web server
 */
public const ENDPOINT_URL:String = "grilles.php";
//public const DOMAIN_URL:String = "krys.alceane.lan";
public const DOMAIN_URL:String = "localhost";

[Bindable] public var ENDPOINT_SERVICE:String = "http://"+DOMAIN_URL+"/gevu/services/index.php";
public const ENDPOINT_IMPORT:String = "http://"+DOMAIN_URL+"/gevu/services/import.php";
public const ENDPOINT_RAPPORT:String = "http://"+DOMAIN_URL+"/gevu/services/rapport.php";
public const ENDPOINT_GRAPH:String = "http://"+DOMAIN_URL+"/gevu/public/graph";
public const ENDPOINT_VIS3D:String = "http://"+DOMAIN_URL+"/gevu/alceane/visites/Residence%20Zampa.html";
