<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
    	
    }

    public function indexAction()
    {
		$s = new GEVU_Statistique();
    	
	    /*;
	    $dbUD = new Model_DbTable_Flux_UtiDoc();
		$rs = array();
	    $arr = $dbUD->findDocByUtiTag('luckysemiosis','agent');
	    foreach ($arr as $d){
		    	$tags = $dbUD->findTagByDocUti($d["doc_id"],$d["uti_id"]);
		    	$r = array("d:"=>$d["titre"],"dt:"=>$d["pubDate"],"u:"=>$d["url"],"t:"=>$tags);
		    	$rs[] = $r;
		}
		*/
    }

}



