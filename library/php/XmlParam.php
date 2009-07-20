<?php
Class XmlParam{
	public $FicXml;
	public $trace;
	public $xml;

	function __construct($FicXml = -1, $src=-1, $dom=-1) {
		$this->trace = TRACE;
		
		if ($FicXml !=-1) {
		    $this->FicXml = $FicXml;
			
			if ($xml = simplexml_load_file($FicXml)){
				$this->xml = $xml;
			}else{
				return false;
			}
		}
    	if($dom!=-1)
    		$this->xml = simplexml_import_dom($dom);	
		if ($src !=-1) {
    		if ($xml = simplexml_load_string($src))
    			$this->xml = $xml;
		}
	}
	
	public function GetElements($Xpath){
		if($this->trace)
			echo 'XmlParam GetElements On cherche le xpath '.$Xpath.'<br/>';

		if ($this->xml){
			return $this->xml->xpath($Xpath);
		}else{
			return -1;
		}
		
		
	}
	
	public function GetCount($Xpath){
		
		if($this->trace)
			echo 'XmlParam GetCount du xpath '.$Xpath.'<br/>';
		return count($this->xml->xpath($Xpath));
	}
	
	public function XML_entities($str)
	{
		//$str = str_replace("'","''",$str);
	    return preg_replace(array("'&'", "'\"'", "'<'", "'>'"), array('&#38;', '&#34;','&lt;','&gt;'), $str);
	}

}
?>