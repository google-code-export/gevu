<?php
class GEVU_Rapport{

	public function GetSolusProbEtab($objSite, $idEtab){

			$grille = new Grille($objSite);
			$cc = new Model_DbTable_Gevu_couts();
			
			//récupère la liste des batiments
			/*
			$url = $objSite->infos['urlExeAjax'].'?f=GetTree&site='.$objSite->id.'&ParamNom=GetOntoTree&type=bat&id='.$idEtab;
			$strXml = $objSite->GetCurl($url);
			$xmlBats = simplexml_load_string($strXml);
			$rBats = $xmlBats->xpath("/tree/treechildren/treeitem/treerow");
			*/
		    $rs = $grille->GetTreeProb($idEtab,false, true);
		    $xml ="<solus>";
			$x=0;
		    foreach($rs as $r) {
		    	//on ne prend en compte que les réponses non
		    	if($r['RepCont']=='Non'){
			    	$xml .="<prob>"
			    		."<idRub>".$objSite->XmlParam->XML_entities($r['idRub'])."</idRub>"
			    		."<idDon>".$objSite->XmlParam->XML_entities($r['idDon'])."</idDon>"
			    		."<titre>".$objSite->XmlParam->XML_entities($r['titreArt'])."</titre>"
			    		."<lieu>".$objSite->XmlParam->XML_entities($r['titreRubPar']." _ ".$r['titreRub'])."</lieu>"
			    		."<texte>".$objSite->XmlParam->XML_entities($r['affir'])."</texte>"
			    		."<reglementaire>".$r['regle']."</reglementaire>";
			    	$xml .="<couts>";    	
					//récupère les couts des solutions liées aux critères
			    	$couts = $cc->findSolusByIdsCriteres("'".$r['idCrit']."'");
			    	foreach($couts as $c){
			    		$xml .="<coutSolus>";
			    		$xml .= $this->getXmlRow($c);
	
			    		//récupère les couts des produits liées aux solutions
			    		$coutsProd = $cc->findProduitsByIdSolution($c['id_solution']);    	
			    		foreach($coutsProd as $cP){
			    			$xml .="<coutProd>";
			    			$xml .= $this->getXmlRow($cP);
			    			$xml .="</coutProd>";
			    		}    	
			    		$xml .="</coutSolus>";    	
			    	}
			    	$xml .="</couts>";    	
			    	$xml .="</prob>";
			    	$x++;
		    	}
		    }
		    $xml .="</solus>";
		    
		    return $xml;
    }    
  	
    function getXmlRow($r){
    	$xml ="";
    	foreach($r as $lib=>$val){
    		$xml .="<".$lib.">".$val."</".$lib.">";
    	}
    	return $xml;
    }
}
?>