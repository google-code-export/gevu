<?php
/*
 * Squelette : ../plugins/grilles/formulaires/forms_champ_multiple.html
 * Date :      Mon, 03 Dec 2007 21:39:12 GMT
 * Compile :   Thu, 02 Jul 2009 19:16:23 GMT (0.04s)
 * Boucles :   _choix, _champs, _f
 */ 
//
// <BOUCLE forms_champs_choix>
//
function BOUCLE_choixhtml_aab0a975070b72d49a3a0d39ce91799b(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("forms_champs_choix.champ",
		"forms_champs_choix.choix",
		"forms_champs_choix.titre"), # SELECT
		array('forms_champs_choix' => 'spip_forms_champs_choix'), # FROM
		
			array(
			array('=', 'forms_champs_choix.id_form', spip_abstract_quote($Pile[$SP]['id_form'])), 
			array('=', 'forms_champs_choix.champ', spip_abstract_quote($Pile[$SP]['champ']))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array('forms_champs_choix.rang'), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'forms_champs_choix', # table
		'_choix', # boucle
		''); # serveur
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$t0 .= ('
	<span class=\'spip_form_choix_multiple\'>
	&nbsp; <input type=\'checkbox\' name=\'' .
interdire_scripts($Pile[$SP]['champ']) .
'[]\' id=\'' .
interdire_scripts($Pile[$SP]['choix']) .
'\' value=\'' .
interdire_scripts($Pile[$SP]['choix']) .
'\'
	' .
interdire_scripts((in_any($Pile[$SP]['choix'],forms_valeur($Pile[0]['valeur'],interdire_scripts($Pile[$SP]['champ'])),'') ? 'checked="checked"':'')) .
' />
	<label for=\'' .
interdire_scripts($Pile[$SP]['choix']) .
'\'>' .
interdire_scripts(supprimer_numero(typo($Pile[$SP]['titre']))) .
'</label>
	</span>
	');
	}

	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE forms_champs>
//
function BOUCLE_champshtml_aab0a975070b72d49a3a0d39ce91799b(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("forms_champs.id_form",
		"forms_champs.champ"), # SELECT
		array('forms_champs' => 'spip_forms_champs'), # FROM
		
			array(
			array('=', 'forms_champs.id_form', spip_abstract_quote($Pile[$SP]['id_form'])), 
			array('=', 'forms_champs.champ', spip_abstract_quote($Pile[0]['champ']))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array(), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'forms_champs', # table
		'_champs', # boucle
		''); # serveur
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$t0 .= (' 
' .
((strval($t1 = BOUCLE_choixhtml_aab0a975070b72d49a3a0d39ce91799b($Cache, $Pile, $doublons, $Numrows, $SP))!='') ?
		('
	' . $t1 . '
') :
		('')) .
'
');
	}

	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE forms>
//
function BOUCLE_fhtml_aab0a975070b72d49a3a0d39ce91799b(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

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
BOUCLE_champshtml_aab0a975070b72d49a3a0d39ce91799b($Cache, $Pile, $doublons, $Numrows, $SP) .
'
');
	}

	@spip_abstract_free($result,'');
	return $t0;
}



//
// Fonction principale du squelette ../plugins/grilles/formulaires/forms_champ_multiple.html
//
function html_aab0a975070b72d49a3a0d39ce91799b($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = BOUCLE_fhtml_aab0a975070b72d49a3a0d39ce91799b($Cache, $Pile, $doublons, $Numrows, $SP);

	return analyse_resultat_skel('html_aab0a975070b72d49a3a0d39ce91799b', $Cache, $page);
}

?>