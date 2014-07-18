<?php
try {
	require_once( "../application/configs/config.php" );
		
	//$_GET["idLieu"]=1; $_GET["idBase"]="gevu_ref";$_GET["type"]= "csvCrit";
	
	if(!isset($_GET["idLieu"]) || !isset($_GET["idBase"]) || !isset($_GET["type"])){
		echo "variables invalides";
	}else{
		
		
		switch ($_GET["type"]) {
			case "csvCrit":
				$titreDoc = "gevuCriteres.csv";
				$dbCrit = new Models_DbTable_Gevu_criteres();
				$arr = $dbCrit->exporte();
				$csv_data = array_to_scv($arr, true);		
				break;
			default:
				;
			break;
		}

		header("Content-Disposition: attachment; filename=\"$titreDoc\"; ");
		header("Content-Type: text/csv");
		print_r($csv_data);
		
	}		
	
}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
	echo "Message: " . $e->getMessage() . "\n";
}

/**
* Generatting CSV formatted string from an array.
* By Sergey Gurevich.
*/
function array_to_scv($array, $header_row = true, $col_sep = ",", $row_sep = "\n", $qut = '"')
{
	if (!is_array($array) or !is_array($array[0])) return false;
	
	//Header row.
	if ($header_row)
	{
		foreach ($array[0] as $key => $val)
		{
			//Escaping quotes.
			$key = str_replace($qut, "$qut$qut", $key);
			$output .= "$col_sep$qut$key$qut";
		}
		$output = substr($output, 1)."\n";
	}
	//Data rows.
	foreach ($array as $key => $val)
	{
		$tmp = '';
		foreach ($val as $cell_key => $cell_val)
		{
			//Escaping quotes.
			$cell_val = str_replace($qut, "$qut$qut", $cell_val);
			$tmp .= "$col_sep$qut$cell_val$qut";
		}
		$output .= substr($tmp, 1).$row_sep;
	}
	
	return $output;
}
