/**
 * change this to point to your poll.php on the web server
 */
public const ENDPOINT_URL:String = "grilles.php";
//public const DOMAIN_URL:String = "krys.alceane.lan";
public const DOMAIN_URL:String = "localhost/gevu";
//public const DOMAIN_URL:String = "www.gevu.org";

[Bindable] public var ENDPOINT_SERVICE:String = "http://"+DOMAIN_URL+"/services/index.php";
public const ENDPOINT_IMPORT:String = "http://"+DOMAIN_URL+"/services/import.php";
public const ENDPOINT_RAPPORT:String = "http://"+DOMAIN_URL+"/services/rapport.php";
public const ENDPOINT_GRAPH:String = "http://"+DOMAIN_URL+"/public/graph";
public const ENDPOINT_VIDE:String = "http://"+DOMAIN_URL+"/public/vide.html";
