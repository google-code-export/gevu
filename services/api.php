<?php
try {
	require_once( "../application/configs/config.php" );
		
	if(!isset($_GET["idLieu"]) || !isset($_GET["idBase"])){
		echo "variables invalides";
	}else{
		$imgFolder = WEB_ROOT."/images/";
		$diag = new GEVU_Diagnostique($_GET["idBase"]);
		//l'utilisateur par défaut est API
		$arr = $diag->getNodeRelatedData($_GET["idLieu"], 1);
		//construction de la réponse
		$html = "";
		if($arr["___diagnostics"]["diag"]["DiagNon"]){
			$statDiag = $arr["___diagnostics"]["diag"]["stat"]["EtatDiag"];
			$html .= "<table><tr>";
			$html .= "<tr>";
			foreach ($statDiag as $d) {
				$html .= "<td>".$d["id"]."<br/><img src='".$imgFolder.$d["handi"].".png' /></td>";
			}
			$html .= "</tr>";
			$html .= "<tr><td colspan='4' ><a href='http://www.gevu.org'><img src='".$imgFolder."logo.png' /></a></td></tr>";				
			$html .= "</table>";
		}
		if (isset($arr["Models_DbTable_Gevu_diagext"])){
			$statDiagExt = $arr["Models_DbTable_Gevu_diagext"][0];
			$html .= "<table>";
			$html .= "<tr>";
			$html .= "<td>auditif<br/><img src='".$imgFolder.$statDiagExt["auditif"].".png' /></td>";				
			$html .= "<td>cognitif<br/><img src='".$imgFolder.$statDiagExt["cognitif"].".png' /></td>";				
			$html .= "<td>moteur<br/><img src='".$imgFolder.$statDiagExt["moteur"].".png' /></td>";				
			$html .= "<td>visuel<br/><img src='".$imgFolder.$statDiagExt["visuel"].".png' /></td>";				
			$html .= "</tr>";
			$html .= "<tr><td ><a href='".$statDiagExt["source"]."'>source</a></td></tr>";				
			$html .= "</table>";
		}
		echo $html;
	}	
}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
	echo "Message: " . $e->getMessage() . "\n";
}
