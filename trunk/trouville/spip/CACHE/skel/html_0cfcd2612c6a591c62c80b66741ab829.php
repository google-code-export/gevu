<?php
/*
 * Squelette : ../plugins/grilles/modeles/form.html
 * Date :      Mon, 03 Dec 2007 21:39:10 GMT
 * Compile :   Thu, 02 Jul 2009 18:34:49 GMT (0.06s)
 * Boucles :   _f
 */ 
//
// <BOUCLE forms>
//
function BOUCLE_fhtml_0cfcd2612c6a591c62c80b66741ab829(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("forms.id_form"), # SELECT
		array('forms' => 'spip_forms'), # FROM
		
			array(
			array('=', 'forms.id_form', spip_abstract_quote($Pile[$SP]['id_form']))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array(), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'forms', # table
		'_f', # boucle
		''); # serveur
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$t0 .= ('
' .
executer_balise_dynamique('FORMS',
	array($Pile[$SP]['id_form'],$Pile[0]['id_article'],$Pile[0]['id_donnee'],$Pile[0]['class']),
	array(''), $GLOBALS['spip_lang'],2) .
'
');
	}

	@spip_abstract_free($result,'');
	return $t0;
}



//
// Fonction principale du squelette ../plugins/grilles/modeles/form.html
//
function html_0cfcd2612c6a591c62c80b66741ab829($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = BOUCLE_fhtml_0cfcd2612c6a591c62c80b66741ab829($Cache, $Pile, $doublons, $Numrows, $SP);

	return analyse_resultat_skel('html_0cfcd2612c6a591c62c80b66741ab829', $Cache, $page);
}

?>