<?php
session_start();

$ajax = true;

require_once(dirname(__FILE__) . "/functions.inc.php");
require_once(dirname(__FILE__) . "/XmlSerializer.class.php"); 

//require_once ("C:/wamp/www/gevu/param/ParamPage.php");
//require_once ($_SERVER["DOCUMENT_ROOT"]."/param/ParamPage.php");
require_once ($_SERVER["DOCUMENT_ROOT"]."/gevu/param/ParamPage.php");

$objSite = new Site($SITES, DEFSITE, $scope, false);
//echo ($objSite->infos["SQL_HOST"].", ".$objSite->infos["SQL_LOGIN"].", ".$objSite->infos["SQL_DB"]);

	/**
	 * the list of fields in the table. We need this to filtrer les valeurs
	 */
	$fields = array(
		'id_donnee'=>"fd.id_donnee"
		, 'ligne_1'=>"fdc1.valeur"
		, 'ligne_2'=>"fdc2.valeur"
		, 'ligne_3'=>"fdc3.valeur"
		, 'ligne_4'=>"fdc4.valeur"
		, 'ligne_5'=>"fdc5.valeur"
		, 'mot_1'=>"m.titre"
		, 'multiple_1_1'=>"fdc7_1.valeur"
		, 'multiple_1_2'=>"fdc7_1.valeur"
		, 'multiple_2_1'=>"fdc8_1.valeur"
		, 'multiple_2_2'=>"fdc8_2.valeur"
		, 'multiple_2_3'=>"fdc8_3.valeur"
		, 'multiple_2_4'=>"fdc8_4.valeur"
		, 'multiple_2_5'=>"fdc8_5.valeur"
		, 'multiple_2_6'=>"fdc8_6.valeur"
		, 'multiple_3_1'=>"fdc9_1.valeur"
		, 'multiple_3_2'=>"fdc9_2.valeur"
		, 'multiple_3_3'=>"fdc9_3.valeur"
		, 'multiple_3_4'=>"fdc9_4.valeur"
		, 'texte_1'=>"fdc10.valeur"
		, 'texte_2'=>"fdc11.valeur"
		);
		

/**
 * we need to escape the value, so we need to know what it is
 * possible values: text, long, int, double, date, defined
 */
$filter_type = "text";

function GetQuery() {
	global $filter_field, $filter_type, $fields;

	$from = ' FROM spip_forms_donnees fd
					 LEFT JOIN spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee AND fdc1.champ = "ligne_1"
					 LEFT JOIN spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee AND fdc2.champ = "ligne_2"
					 LEFT JOIN spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee AND fdc3.champ = "ligne_3"
					 LEFT JOIN spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee AND fdc4.champ = "ligne_4"
					 LEFT JOIN spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee AND fdc5.champ = "ligne_5"
					 LEFT JOIN spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee AND fdc6.champ = "mot_1"
					 LEFT JOIN spip_mots m ON m.id_mot = fdc6.valeur
					LEFT JOIN spip_forms_donnees_champs fdc7_1 ON fdc7_1.id_donnee = fd.id_donnee AND fdc7_1.champ = "multiple_1" AND fdc7_1.valeur = "multiple_1_1"
					LEFT JOIN spip_forms_donnees_champs fdc7_2 ON fdc7_2.id_donnee = fd.id_donnee AND fdc7_2.champ = "multiple_1" AND fdc7_2.valeur = "multiple_1_2"
					LEFT JOIN spip_forms_donnees_champs fdc8_1 ON fdc8_1.id_donnee = fd.id_donnee AND fdc8_1.champ = "multiple_2" AND fdc8_1.valeur = "multiple_2_1"
					LEFT JOIN spip_forms_donnees_champs fdc8_2 ON fdc8_2.id_donnee = fd.id_donnee AND fdc8_2.champ = "multiple_2" AND fdc8_2.valeur = "multiple_2_2"
					LEFT JOIN spip_forms_donnees_champs fdc8_3 ON fdc8_3.id_donnee = fd.id_donnee AND fdc8_3.champ = "multiple_2" AND fdc8_3.valeur = "multiple_2_3"
					LEFT JOIN spip_forms_donnees_champs fdc8_4 ON fdc8_4.id_donnee = fd.id_donnee AND fdc8_4.champ = "multiple_2" AND fdc8_4.valeur = "multiple_2_4"
					LEFT JOIN spip_forms_donnees_champs fdc8_5 ON fdc8_5.id_donnee = fd.id_donnee AND fdc8_5.champ = "multiple_2" AND fdc8_5.valeur = "multiple_2_5"
					LEFT JOIN spip_forms_donnees_champs fdc8_6 ON fdc8_6.id_donnee = fd.id_donnee AND fdc8_6.champ = "multiple_2" AND fdc8_6.valeur = "multiple_2_6"
					LEFT JOIN spip_forms_donnees_champs fdc9_1 ON fdc9_1.id_donnee = fd.id_donnee AND fdc9_1.champ = "multiple_3" AND fdc9_1.valeur = "multiple_3_1"
					LEFT JOIN spip_forms_donnees_champs fdc9_2 ON fdc9_2.id_donnee = fd.id_donnee AND fdc9_2.champ = "multiple_3" AND fdc9_2.valeur = "multiple_3_2"
					LEFT JOIN spip_forms_donnees_champs fdc9_3 ON fdc9_3.id_donnee = fd.id_donnee AND fdc9_3.champ = "multiple_3" AND fdc9_3.valeur = "multiple_3_3"
					LEFT JOIN spip_forms_donnees_champs fdc9_4 ON fdc9_4.id_donnee = fd.id_donnee AND fdc9_4.champ = "multiple_3" AND fdc9_4.valeur = "multiple_3_4"
					LEFT JOIN spip_forms_donnees_champs fdc10 ON fdc10.id_donnee = fd.id_donnee AND fdc10.champ = "texte_1"
					LEFT JOIN spip_forms_donnees_champs fdc11 ON fdc11.id_donnee = fd.id_donnee AND fdc11.champ = "texte_2"
			';

	/*
  	Modifier   	Effacer  	70 	multiple_1 	multiple_1_1  	R�glementaire 	1
	Modifier 	Effacer 	70 	multiple_1 	multiple_1_2 	Souhaitable 	2
	Modifier 	Effacer 	70 	multiple_2 	multiple_2_1 	Travail 	1
	Modifier 	Effacer 	70 	multiple_2 	multiple_2_2 	ERP_IOP 	2
	Modifier 	Effacer 	70 	multiple_2 	multiple_2_3 	Logement 	3
	Modifier 	Effacer 	70 	multiple_2 	multiple_2_4 	Voirie 	4
	Modifier 	Effacer 	70 	multiple_3 	multiple_3_1 	motrice 	1
	Modifier 	Effacer 	70 	multiple_3 	multiple_3_2 	auditive 	2
	Modifier 	Effacer 	70 	multiple_3 	multiple_3_3 	visuelle 	3
	Modifier 	Effacer 	70 	multiple_3 	multiple_3_4 	cognitive 	4	
	*/
	//v�rifie si on fait une requ�te pour renvoyer les controles sans mot clef
	if($_REQUEST["idMot"]==-1)
		$where = " WHERE fd.id_form =" . @$_REQUEST["idGrille"] . " AND m.id_mot IS NULL";
	else
		$where = " WHERE fd.id_form =" . @$_REQUEST["idGrille"] . " AND fdc6.valeur = " . @$_REQUEST["idMot"];

	if (@$_REQUEST['filter'] != "") {
		if(substr($_REQUEST["champ"],0,8)=='multiple'){
			$valeur = $_REQUEST["champ"];	
		}else{
			$valeur = $_REQUEST["filter"];	
		}
		
		$filter = $fields[$_REQUEST["orderField"]];
		
		$filter = $fields[$_REQUEST["orderField"]];
		$where .= " AND ". $filter . " LIKE " . GetSQLValueStringForSelect($valeur, $filter_type);	
	}

	$order = "";
	if (@$_REQUEST["orderField"] != "") {
		$order = "ORDER BY " . @$_REQUEST["orderField"] . " " . (in_array(@$_REQUEST["orderDirection"], array("ASC", "DESC")) ? @$_REQUEST["orderDirection"] : "ASC");
	}

	$groupby = " GROUP BY fd.id_donnee ";
	
	return array("from"=>$from,"where"=>$where,"groupby"=>$groupby,"order"=>$order);
	
}

function findAll() {
	global $conn, $filter_field, $filter_type;


	$arrSql = GetQuery();
	
	//calculate the number of rows in this table
	$sql = "SELECT count(distinct(fd.id_donnee)) AS cnt ".$arrSql["from"]." ".$arrSql["where"];
	$rscount = mysql_query($sql); 
	$row_rscount = mysql_fetch_assoc($rscount);
	$totalrows = (int) $row_rscount["cnt"];
	
	//get the page number, and the page size
	$pageNum = (int)@$_REQUEST["pageNum"];
	$pageSize = (int)@$_REQUEST["pageSize"];
	
	//calculate the start row for the limit clause
	$start = $pageNum * $pageSize;

	//construct the query, using the where and order condition
	$query_recordset = "SELECT fd.id_donnee, fdc1.valeur ligne_1, fdc2.valeur ligne_2, fdc3.valeur ligne_3, fdc4.valeur ligne_4, fdc5.valeur ligne_5
			, m.id_mot mot_1_id, m.titre mot_1
			, IF(ISNULL(fdc7_1.valeur),'false','true') multiple_1_1, IF(ISNULL(fdc7_2.valeur),'false','true') multiple_1_2
			, IF(ISNULL(fdc8_1.valeur),'false','true') multiple_2_1, IF(ISNULL(fdc8_2.valeur),'false','true') multiple_2_2, IF(ISNULL(fdc8_3.valeur),'false','true') multiple_2_3, IF(ISNULL(fdc8_4.valeur),'false','true') multiple_2_4
			, IF(ISNULL(fdc8_5.valeur),'false','true') multiple_2_5, IF(ISNULL(fdc8_6.valeur),'false','true') multiple_2_6
			, IF(ISNULL(fdc9_1.valeur),'false','true') multiple_3_1, IF(ISNULL(fdc9_2.valeur),'false','true') multiple_3_2, IF(ISNULL(fdc9_3.valeur),'false','true') multiple_3_3, IF(ISNULL(fdc9_4.valeur),'false','true') multiple_3_4
			, fdc10.valeur texte_1, fdc11.valeur texte_2
		".$arrSql["from"]." ".$arrSql["where"]." ".$arrSql["groupby"]." ".$arrSql["order"];
	
	//if we use pagination, add the limit clause
	if ($pageNum >= 0 && $pageSize > 0) {	
		$query_recordset = sprintf("%s LIMIT %d, %d", $query_recordset, $start, $pageSize);
	}

	$recordset = mysql_query($query_recordset, $conn);
	
	//if we have rows in the table, loop through them and fill the array
	$toret = array();
	while ($row_recordset = mysql_fetch_assoc($recordset)) {
		array_push($toret, $row_recordset);
	}
	
	//create the standard response structure
	$toret = array(
		"data" => $toret, 
		"metadata" => array (
			"totalRows" => $totalrows,
			"pageNum" => $pageNum
		)
	);

	return $toret;
}

/**
 * constructs and executes a sql count query against the selected database
 * can take the following parameters:
 * $_REQUEST["filter"] - the filter value
 * returns : an array of the form
 * array (
 * 		data => number_of_rows, 
 * 		metadata => array()
 * ) 
 */
function rowCount() {
	global $conn, $filter_field, $filter_type;

	$from = " FROM spip_forms_donnees fd
		INNER JOIN spip_forms_donnees_champs fdc ON fdc.id_donnee = fd.id_donnee ";
	
	$where = " WHERE fd.id_form =" . @$_REQUEST["idGrille"];
	if (@$_REQUEST['filter'] != "") {
		$where .= " AND " . $filter_field . " LIKE " . GetSQLValueStringForSelect(@$_REQUEST["filter"], $filter_type);	
	}

	//calculate the number of rows in this table
	$rscount = mysql_query("SELECT count(*) AS cnt $from $where"); 
	$row_rscount = mysql_fetch_assoc($rscount);
	$totalrows = (int) $row_rscount["cnt"];
	
	//create the standard response structure
	$toret = array(
		"data" => $totalrows, 
		"metadata" => array()
	);

	return $toret;
}

/**
 * constructs and executes a sql insert query against the selected database
 * can take the following parameters:
 * $_REQUEST["field_name"] - the list of fields which appear here will be used as values for insert. 
 * If a field does not appear, null will be used.  
 * returns : an array of the form
 * array (
 * 		data => array(
 * 			"primary key" => primary_key_value, 
 * 			"field1" => "value1"
 * 			...
 * 		), 
 * 		metadata => array()
 * ) 
 */
function insert() {
	global $conn;

	//build and execute the insert query
	$query_insert = sprintf("INSERT INTO `spip_forms_donnees_champs` (id_donnee,champ,valeur,maj) VALUES (%s,%s,%s,now())" ,			
			GetSQLValueString($_REQUEST["id_donnee"], "int"), # 
			GetSQLValueString($_REQUEST["champ"], "text"), # 
			GetSQLValueString($_REQUEST["valeur"], "text")# 
	);
	$ok = mysql_query($query_insert);
	
	if ($ok) {
		// return the new entry, using the insert id
		$toret = array(
			"data" => array(
				array(
					"id_donnee" => $_REQUEST["id_donnee"], 
					"champ" => $_REQUEST["champ"], # 
					"valeur" => $_REQUEST["valeur"]# 
				)
			), 
			"metadata" => array()
		);
	} else {
		// we had an error, return it
		$toret = array(
			"data" => array("error" => mysql_error()), 
			"metadata" => array()
		);
	}
	return $toret;
}

function insertNew() {
	global $conn, $objSite;
	
	//r�cup�ration de l'article o� stocker la grille � partir du mots-clef
	$sql = "SELECT a.id_article, a.id_rubrique FROM spip_articles a
		INNER JOIN spip_forms_donnees_articles fda ON fda.id_article = a.id_article 
		INNER JOIN spip_forms_donnees_champs fdc ON fdc.id_donnee = fda.id_donnee AND fdc.valeur = ".$_REQUEST["mot_1"]."
		INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fda.id_donnee AND fd.id_form = ".$_REQUEST["id_form"]."
		GROUP BY a.id_article
		";
	$rs = mysql_query($sql); 
	$row_rs = mysql_fetch_assoc($rs);
	$idArt = $row_rs["id_article"];
	$idRub = $row_rs["id_rubrique"];
	$idGrille = $_REQUEST["id_form"];
	
	//cr�ation de la donn�e
	$g = new Grille($objSite);
	$idDon = $g->AddDonnee($idRub,$idGrille,false,$idArt,true);
		
	//cr�ation des enregistrements
	$row = array("grille"=>$idGrille,"champ"=>"ligne_1","valeur"=>$_REQUEST["ligne_1"]);
	$g->SetChamp($row, $idDon);

	$row = array("grille"=>$idGrille,"champ"=>"mot_1","valeur"=>$_REQUEST["mot_1"]);
	$g->SetChamp($row, $idDon);

	$row = array("grille"=>$idGrille,"champ"=>"ligne_2","valeur"=>$_REQUEST["ligne_2"]);
	$g->SetChamp($row, $idDon);
	
	$row = array("grille"=>$idGrille,"champ"=>"ligne_3","valeur"=>$_REQUEST["ligne_3"]);
	$g->SetChamp($row, $idDon);
	
	$row = array("grille"=>$idGrille,"champ"=>"ligne_4","valeur"=>$_REQUEST["ligne_4"]);
	$g->SetChamp($row, $idDon);
	
	$row = array("grille"=>$idGrille,"champ"=>"ligne_5","valeur"=>$_REQUEST["ligne_5"]);
	$g->SetChamp($row, $idDon);
	
	$row = array("grille"=>$idGrille,"champ"=>"ligne_5","valeur"=>$_REQUEST["ligne_5"]);
	$g->SetChamp($row, $idDon);
	
	$row = array("grille"=>$idGrille,"champ"=>"multiple_1","valeur"=>"multiple_1_1");
	if($_REQUEST["multiple_1_1"]=="true"){
		$g->SetChamp($row, $idDon);
	}else{
		$g->DelChamp($row, $idDon);
	}
	$row = array("grille"=>$idGrille,"champ"=>"multiple_1","valeur"=>"multiple_1_2");
	if($_REQUEST["multiple_1_2"]=="true"){
		$g->SetChamp($row, $idDon);
	}else{
		$g->DelChamp($row, $idDon);
	}
	
	$row = array("grille"=>$idGrille,"champ"=>"multiple_2","valeur"=>"multiple_2_1");
	if($_REQUEST["multiple_2_1"]=="true"){
		$g->SetChamp($row, $idDon);
	}else{
		$g->DelChamp($row, $idDon);
	}
	$row = array("grille"=>$idGrille,"champ"=>"multiple_2","valeur"=>"multiple_2_2");
	if($_REQUEST["multiple_2_2"]=="true"){
		$g->SetChamp($row, $idDon);
	}else{
		$g->DelChamp($row, $idDon);
	}
	$row = array("grille"=>$idGrille,"champ"=>"multiple_2","valeur"=>"multiple_2_3");
	if($_REQUEST["multiple_2_3"]=="true"){
		$g->SetChamp($row, $idDon);
	}else{
		$g->DelChamp($row, $idDon);
	}
	$row = array("grille"=>$idGrille,"champ"=>"multiple_2","valeur"=>"multiple_2_4");
	if($_REQUEST["multiple_2_4"]=="true"){
		$g->SetChamp($row, $idDon);
	}else{
		$g->DelChamp($row, $idDon);
	}
	$row = array("grille"=>$idGrille,"champ"=>"multiple_2","valeur"=>"multiple_2_5");
	if($_REQUEST["multiple_2_5"]=="true"){
		$g->SetChamp($row, $idDon);
	}else{
		$g->DelChamp($row, $idDon);
	}
	$row = array("grille"=>$idGrille,"champ"=>"multiple_2","valeur"=>"multiple_2_6");
	if($_REQUEST["multiple_2_6"]=="true"){
		$g->SetChamp($row, $idDon);
	}else{
		$g->DelChamp($row, $idDon);
	}
	
	$row = array("grille"=>$idGrille,"champ"=>"multiple_3","valeur"=>"multiple_3_1");
	if($_REQUEST["multiple_3_1"]=="true"){
		$g->SetChamp($row, $idDon);
	}else{
		$g->DelChamp($row, $idDon);
	}
	$row = array("grille"=>$idGrille,"champ"=>"multiple_3","valeur"=>"multiple_3_2");
	if($_REQUEST["multiple_3_2"]=="true"){
		$g->SetChamp($row, $idDon);
	}else{
		$g->DelChamp($row, $idDon);
	}
	$row = array("grille"=>$idGrille,"champ"=>"multiple_3","valeur"=>"multiple_3_3");
	if($_REQUEST["multiple_3_3"]=="true"){
		$g->SetChamp($row, $idDon);
	}else{
		$g->DelChamp($row, $idDon);
	}
	$row = array("grille"=>$idGrille,"champ"=>"multiple_3","valeur"=>"multiple_3_4");
	if($_REQUEST["multiple_3_4"]=="true"){
		$g->SetChamp($row, $idDon);
	}else{
		$g->DelChamp($row, $idDon);
	}
	
	$row = array("grille"=>$idGrille,"champ"=>"texte_1","valeur"=>$_REQUEST["texte_1"]);
	$g->SetChamp($row, $idDon);
	$row = array("grille"=>$idGrille,"champ"=>"texte_2","valeur"=>$_REQUEST["texte_2"]);
	$g->SetChamp($row, $idDon);
			
	// return the new entry, using the insert id
	$toret = array(
		"data" => array(
			array(
				"id_donnee" => $idDon
				,"ligne_1" => $_REQUEST["ligne_1"]
				,"mot_1" => $_REQUEST["mot_1"]
				,"ligne_2" => $_REQUEST["ligne_2"]
				,"ligne_3" => $_REQUEST["ligne_3"]
				,"ligne_4" => $_REQUEST["ligne_4"]
				,"ligne_5" => $_REQUEST["ligne_5"]
				,"multiple_1_1" => $_REQUEST["multiple_1_1"]
				,"multiple_1_2" => $_REQUEST["multiple_1_2"]
				,"multiple_2_1" => $_REQUEST["multiple_2_1"]
				,"multiple_2_2" => $_REQUEST["multiple_2_2"]
				,"multiple_2_3" => $_REQUEST["multiple_2_3"]
				,"multiple_2_4" => $_REQUEST["multiple_2_4"]
				,"multiple_2_5" => $_REQUEST["multiple_2_5"]
				,"multiple_2_6" => $_REQUEST["multiple_2_6"]
				,"multiple_3_1" => $_REQUEST["multiple_3_1"]
				,"multiple_3_2" => $_REQUEST["multiple_3_2"]
				,"multiple_3_3" => $_REQUEST["multiple_3_3"]
				,"multiple_3_4" => $_REQUEST["multiple_3_4"]
				,"texte_1" => $_REQUEST["texte_1"]
				,"texte_2" => $_REQUEST["texte_2"]
				,"id_form" => $_REQUEST["id_form"]
			)
		), 
		"metadata" => array()
	);

	return $toret;
}

/**
 * constructs and executes a sql update query against the selected database
 * can take the following parameters:
 * $_REQUEST[primary_key] - thethe value of the primary key
 * $_REQUEST[field_name] - the list of fields which appear here will be used as values for update. 
 * If a field does not appear, null will be used.  
 * returns : an array of the form
 * array (
 * 		data => array(
 * 			"primary key" => primary_key_value, 
 * 			"field1" => "value1"
 * 			...
 * 		), 
 * 		metadata => array()
 * ) 
 */
function update() {
	global $conn;

	$valeur = $_REQUEST["valeur"];
	$champ = $_REQUEST["champ"];
	$toret="";
	
	//gestion des s�lections multiples
	if(substr($_REQUEST["champ"],0,8)=='multiple'){
	    $arrC = split("_",$_REQUEST["champ"]);
		$valeur = $_REQUEST["valeur"];
		$_REQUEST["valeur"]=$_REQUEST["champ"];
		$_REQUEST["champ"]='multiple_'.$arrC[1];
		
		if($valeur=='false'){
			//si la valeur est false on efface
			$toret=delete();
		}else{
			//si elle est vrai on cr�e la ligne
			$toret=insert();
		}
	}
	
	//gestion des lignes abscentes
	$query = sprintf("SELECT * FROM `spip_forms_donnees_champs` WHERE id_donnee = %s AND champ= %s", 
			GetSQLValueString($_REQUEST["id_donnee"], "int"),
			GetSQLValueString($champ, "text")
		);
	$recordset = mysql_query($query, $conn);
	$num_rows = mysql_num_rows($recordset);
	
	if ($num_rows == 0) {
		$toret=insert();
	}
		
	//gestion par d�faut
	if($toret==""){
		$query_update = sprintf("UPDATE `spip_forms_donnees_champs` SET valeur = %s, maj = now() WHERE id_donnee = %s AND champ= %s", 
				GetSQLValueString($valeur, "text"), 
				GetSQLValueString($_REQUEST["id_donnee"], "int"),
				GetSQLValueString($champ, "text")
			);
			$ok = mysql_query($query_update);
			if ($ok) {
				// return the updated entry
				$toret = array(
					"data" => array(
						array(
							"id_donnee" => $_REQUEST["id_donnee"], 
							"champ" => $_REQUEST["champ"], #
							"valeur" => $_REQUEST["valeur"] #
						)
					), 
					"metadata" => array()
				);
			} else {
				// an update error, return it
				$toret = array(
					"data" => array("error" => mysql_error()), 
					"metadata" => array()
				);
			}
	}
	
	return $toret;
}

/**
 * constructs and executes a sql update query against the selected database
 * can take the following parameters:
 * $_REQUEST[primary_key] - thethe value of the primary key
 * returns : an array of the form
 * array (
 * 		data => deleted_row_primary_key_value, 
 * 		metadata => array()
 * ) 
 */
function delete() {
	global $conn;

	//prise en compte des choix multiple
	if(substr($_REQUEST["champ"],0,8)=='multiple'){
		// check to see if the record actually exists in the database
		$query_recordset = sprintf("SELECT * FROM `spip_forms_donnees_champs` WHERE id_donnee = %s AND champ= %s AND valeur= %s",
			GetSQLValueString($_REQUEST["id_donnee"], "int"),
			GetSQLValueString($_REQUEST["champ"], "text"),
			GetSQLValueString($_REQUEST["valeur"], "text")
		);
	}else{	
		// check to see if the record actually exists in the database
		$query_recordset = sprintf("SELECT * FROM `spip_forms_donnees_champs` WHERE id_donnee = %s ",
			GetSQLValueString($_REQUEST["id_donnee"], "int")
		);
	}
	
	$recordset = mysql_query($query_recordset, $conn);
	$num_rows = mysql_num_rows($recordset);

	if ($num_rows > 0) {
		$row_recordset = mysql_fetch_assoc($recordset);
		//prise en compte des choix multiple
		if(substr($_REQUEST["champ"],0,8)=='multiple'){
			// check to see if the record actually exists in the database
			$query_delete = sprintf("DELETE FROM `spip_forms_donnees_champs` WHERE id_donnee = %s AND champ= %s AND valeur= %s", 
				GetSQLValueString($_REQUEST["id_donnee"], "int"),
				GetSQLValueString($_REQUEST["champ"], "text"),
				GetSQLValueString($_REQUEST["valeur"], "text")
			);
		}else{	
			$query_delete = sprintf("DELETE FROM `spip_forms_donnees_champs` WHERE id_donnee = %s ", 
				GetSQLValueString($_REQUEST["id_donnee"], "int")
			);
		}
		$ok = mysql_query($query_delete);
		if ($ok) {
			// delete went through ok, return OK
			$toret = array(
				"data" => $row_recordset["id_donnee"], 
				"metadata" => array()
			);
		} else {
			$toret = array(
				"data" => array("error" => mysql_error()), 
				"metadata" => array()
			);
		}

	} else {
		// no row found, return an error
		$toret = array(
			"data" => array("error" => "No row found"), 
			"metadata" => array()
		);
	}
	return $toret;
}

/**
 * we use this as an error response, if we do not receive a correct method
 * 
 */
$ret = array(
	"data" => array("error" => "No operation"), 
	"metadata" => array()
);

/**
 * check for the database connection 
 * 
 * 
 */
if ($conn === false) {
	$ret = array(
		"data" => array("error" => "database connection error, please check your settings !"), 
		"metadata" => array()
	);
} else {
	
	$conn = mysql_pconnect($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"]) or trigger_error(mysql_error(),E_USER_ERROR);
	mysql_select_db($objSite->infos["SQL_DB"], $conn);
	mysql_query("SET NAMES 'utf8'");
	
	mysql_select_db($database_conn, $conn);

	/**
	 * simple dispatcher. The $_REQUEST["method"] parameter selects the operation to execute. 
	 * must be one of the values findAll, insert, update, delete, Count
	 */
	// execute the necessary function, according to the operation code in the post variables
	switch (@$_REQUEST["method"]) {
		case "FindAll":
			$ret = findAll();
		break;
		case "Insert": 
			$ret = insertNew();
		break;
		case "Update": 
			$ret = update();
		break;
		case "Delete": 
			$ret = delete();
		break;
		case "Count":
			$ret = rowCount();
		break;
	}
}


$serializer = new XmlSerializer();
echo $serializer->serialize($ret);
die();
?>
