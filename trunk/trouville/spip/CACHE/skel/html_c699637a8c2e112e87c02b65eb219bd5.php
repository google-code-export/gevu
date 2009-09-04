<?php
/*
 * Squelette : ../plugins/grilles/formulaires/forms_select_mot.html
 * Date :      Mon, 03 Dec 2007 21:39:12 GMT
 * Compile :   Thu, 02 Jul 2009 18:34:50 GMT (0.03s)
 * Boucles :   _mots2, _G2, _mots, _mots_suite, _G
 */ 
//
// <BOUCLE mots>
//
function BOUCLE_mots2html_c699637a8c2e112e87c02b65eb219bd5(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("mots.id_mot",
		"mots.titre"), # SELECT
		array('mots' => 'spip_mots'), # FROM
		
			array(
			array('=', 'mots.id_groupe', spip_abstract_quote($Pile[$SP]['id_groupe']))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array('mots.titre'), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'mots', # table
		'_mots2', # boucle
		''); # serveur
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$t0 .= ('
			<option value=\'' .
$Pile[$SP]['id_mot'] .
'\' ' .
(($Pile[$SP]['id_mot'] == forms_valeur($Pile[0]['valeur'],interdire_scripts(entites_html($Pile[0]['champ'])))) ? 'selected="selected"':'') .
'>
				&nbsp;&nbsp;&nbsp;' .
interdire_scripts(supprimer_numero(typo($Pile[$SP]['titre']))) .
'
			</option>
	    ');
	}

	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE groupes_mots>
//
function BOUCLE_G2html_c699637a8c2e112e87c02b65eb219bd5(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("groupes_mots.id_groupe",
		"groupes_mots.obligatoire",
		"groupes_mots.titre"), # SELECT
		array('groupes_mots' => 'spip_groupes_mots'), # FROM
		
			array(
			array('=', 'groupes_mots.id_groupe', spip_abstract_quote(interdire_scripts(entites_html($Pile[0]['id_groupe']))))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array(), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'groupes_mots', # table
		'_G2', # boucle
		''); # serveur
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$t0 .= ('
		' .
((strval($t1 = BOUCLE_mots2html_c699637a8c2e112e87c02b65eb219bd5($Cache, $Pile, $doublons, $Numrows, $SP))!='') ?
		(('
		<select name=\'' .
		interdire_scripts(entites_html($Pile[0]['champ'])) .
		'\' id=\'' .
		interdire_scripts(entites_html($Pile[0]['champ'])) .
		'\' 
			class=\'' .
		interdire_scripts((($Pile[$SP]['obligatoire'] == 'oui') ? 'fondl':'fondo')) .
		'\'>
			<option value=\'\'>' .
		interdire_scripts(typo($Pile[$SP]['titre'])) .
		'</option>
			') . $t1 . '
		</select>
		') :
		('')) .
'
	');
	}

	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE mots>
//
function BOUCLE_motshtml_c699637a8c2e112e87c02b65eb219bd5(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("mots.id_mot",
		"mots.titre"), # SELECT
		array('mots' => 'spip_mots'), # FROM
		
			array(
			array('=', 'mots.id_groupe', spip_abstract_quote($Pile[$SP]['id_groupe']))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array('mots.titre'), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'mots', # table
		'_mots', # boucle
		''); # serveur

	// Partition
	$nombre_boucle = @spip_abstract_count($result,"");
	$debut_boucle = ceil(($nombre_boucle * 0)/2);
	$fin_boucle = min(ceil (($nombre_boucle * 1)/2) - 1, $nombre_boucle - 1);
	$Numrows['_mots']["grand_total"] = $nombre_boucle;
	$Numrows['_mots']["total"] = max(0,$fin_boucle - $debut_boucle + 1);
	$Numrows['_mots']['compteur_boucle'] = 0;
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$Numrows['_mots']['compteur_boucle']++;
		if ($Numrows['_mots']['compteur_boucle']-1 >= $debut_boucle) {
		if ($Numrows['_mots']['compteur_boucle']-1 > $fin_boucle) break;

		$t0 .= ('<li>
			<input type="' .
interdire_scripts(choixsiegal(entites_html($Pile[0]['unseul']),'oui','radio','checkbox')) .
'" name="' .
interdire_scripts(entites_html($Pile[0]['champ'])) .
'[]" id="mot' .
$Pile[$SP]['id_mot'] .
'" value="' .
$Pile[$SP]['id_mot'] .
'" 
			' .
(in_any($Pile[$SP]['id_mot'],forms_valeur($Pile[0]['valeur'],interdire_scripts(entites_html($Pile[0]['champ']))),'') ? 'checked="checked"':'') .
' />
			' .
((strval($t1 = interdire_scripts(supprimer_numero(typo($Pile[$SP]['titre']))))!='') ?
		(('<label for="mot' .
	$Pile[$SP]['id_mot'] .
	'">') . $t1 . '</label>') :
		('')) .
'</li>
	    ');
		}

	}

	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE mots>
//
function BOUCLE_mots_suitehtml_c699637a8c2e112e87c02b65eb219bd5(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("mots.id_mot",
		"mots.titre"), # SELECT
		array('mots' => 'spip_mots'), # FROM
		
			array(
			array('=', 'mots.id_groupe', spip_abstract_quote($Pile[$SP]['id_groupe']))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array('mots.titre'), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'mots', # table
		'_mots_suite', # boucle
		''); # serveur

	// Partition
	$nombre_boucle = @spip_abstract_count($result,"");
	$debut_boucle = ceil(($nombre_boucle * 1)/2);
	$fin_boucle = min(ceil (($nombre_boucle * 2)/2) - 1, $nombre_boucle - 1);
	$Numrows['_mots_suite']["grand_total"] = $nombre_boucle;
	$Numrows['_mots_suite']["total"] = max(0,$fin_boucle - $debut_boucle + 1);
	$Numrows['_mots_suite']['compteur_boucle'] = 0;
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$Numrows['_mots_suite']['compteur_boucle']++;
		if ($Numrows['_mots_suite']['compteur_boucle']-1 >= $debut_boucle) {
		if ($Numrows['_mots_suite']['compteur_boucle']-1 > $fin_boucle) break;

		$t0 .= ('<li>
			<input type="' .
interdire_scripts(choixsiegal(entites_html($Pile[0]['unseul']),'oui','radio','checkbox')) .
'" name="' .
interdire_scripts(entites_html($Pile[0]['champ'])) .
'[]" id="mot' .
$Pile[$SP]['id_mot'] .
'" value="' .
$Pile[$SP]['id_mot'] .
'" 
			' .
(in_any($Pile[$SP]['id_mot'],forms_valeur($Pile[0]['valeur'],interdire_scripts(entites_html($Pile[0]['champ']))),'') ? 'checked="checked"':'') .
' />
			' .
((strval($t1 = interdire_scripts(supprimer_numero(typo($Pile[$SP]['titre']))))!='') ?
		(('<label for="mot' .
	$Pile[$SP]['id_mot'] .
	'">') . $t1 . '</label>') :
		('')) .
'</li>
	    ');
		}

	}

	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE groupes_mots>
//
function BOUCLE_Ghtml_c699637a8c2e112e87c02b65eb219bd5(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("groupes_mots.id_groupe"), # SELECT
		array('groupes_mots' => 'spip_groupes_mots'), # FROM
		
			array(
			array('=', 'groupes_mots.id_groupe', spip_abstract_quote(interdire_scripts(entites_html($Pile[0]['id_groupe'])))), 
			array('=', 'groupes_mots.unseul', "'non'")), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array(), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'groupes_mots', # table
		'_G', # boucle
		''); # serveur
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$t0 .= ('
	' .
((strval($t1 = BOUCLE_motshtml_c699637a8c2e112e87c02b65eb219bd5($Cache, $Pile, $doublons, $Numrows, $SP))!='') ?
		('
	<ul class="choix_mots">
		' . $t1 . '
	</ul>
	') :
		('')) .
'
	' .
((strval($t1 = BOUCLE_mots_suitehtml_c699637a8c2e112e87c02b65eb219bd5($Cache, $Pile, $doublons, $Numrows, $SP))!='') ?
		('
	<ul class="choix_mots">
		' . $t1 . '
	</ul>
	') :
		('')) .
'
');
	}

	@spip_abstract_free($result,'');
	return $t0;
}



//
// Fonction principale du squelette ../plugins/grilles/formulaires/forms_select_mot.html
//
function html_c699637a8c2e112e87c02b65eb219bd5($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = (((strval($t1 = BOUCLE_Ghtml_c699637a8c2e112e87c02b65eb219bd5($Cache, $Pile, $doublons, $Numrows, $SP))!='') ?
		($t1) :
		(('
	' .
	BOUCLE_G2html_c699637a8c2e112e87c02b65eb219bd5($Cache, $Pile, $doublons, $Numrows, $SP) .
	'
'))) .
'
<br class="nettoyeur" />
');

	return analyse_resultat_skel('html_c699637a8c2e112e87c02b65eb219bd5', $Cache, $page);
}

?>