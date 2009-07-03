<?php
/*
 * Squelette : dist/inc-rubriques.html
 * Date :      Mon, 03 Dec 2007 21:39:42 GMT
 * Compile :   Thu, 02 Jul 2009 19:33:18 GMT (0.02s)
 * Boucles :   _re, _test_expose, _sous_rubriques, _rubriques
 */ 
//
// <BOUCLE boucle>
//
function BOUCLE_rehtml_f84d615e5ef60879da71de6ea450a201(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {
	$save_numrows = ($Numrows['_sous_rubriques']);
	$t0 = ((strval($t1 = BOUCLE_sous_rubriqueshtml_f84d615e5ef60879da71de6ea450a201($Cache, $Pile, $doublons, $Numrows, $SP))!='') ?
		('
			<ul>
				' . $t1 . '
			</ul>
			') :
		(''));
	$Numrows['_sous_rubriques'] = ($save_numrows);
	return $t0;
}


//
// <BOUCLE rubriques>
//
function BOUCLE_test_exposehtml_f84d615e5ef60879da71de6ea450a201(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("rubriques.id_rubrique",
		"rubriques.lang"), # SELECT
		array('rubriques' => 'spip_rubriques'), # FROM
		
			array(
			array('=', 'rubriques.id_rubrique', spip_abstract_quote($Pile[$SP]['id_parent'])), 
			array('=', 'rubriques.statut', '"publie"')), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array(), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'rubriques', # table
		'_test_expose', # boucle
		''); # serveur
	$t0 = "";
	$SP++;
	$old_lang = $GLOBALS['spip_lang'];

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {
		if (!$GLOBALS['forcer_lang'])
	 		$GLOBALS['spip_lang'] = ($x = $Pile[$SP]['lang']) ? $x : $old_lang;
		$t0 .= (calcul_exposer($Pile[$SP]['id_rubrique'], 'id_rubrique', $Pile[0]) ? ' ' : '');
	}

	$GLOBALS['spip_lang'] = $old_lang;
	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE rubriques>
//
function BOUCLE_sous_rubriqueshtml_f84d615e5ef60879da71de6ea450a201(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("rubriques.id_parent",
		"rubriques.id_rubrique",
		"0+rubriques.titre AS num",
		"rubriques.titre",
		"rubriques.lang"), # SELECT
		array('rubriques' => 'spip_rubriques'), # FROM
		
			array(
			array('=', 'rubriques.id_parent', spip_abstract_quote($Pile[$SP]['id_rubrique'])), 
			array('=', 'rubriques.statut', '"publie"')), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array('num', 'rubriques.titre'), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'rubriques', # table
		'_sous_rubriques', # boucle
		''); # serveur
	$t0 = "";
	$SP++;
	$old_lang = $GLOBALS['spip_lang'];

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {
		if (!$GLOBALS['forcer_lang'])
	 		$GLOBALS['spip_lang'] = ($x = $Pile[$SP]['lang']) ? $x : $old_lang;
		$t0 .= ((strval($t1 = BOUCLE_test_exposehtml_f84d615e5ef60879da71de6ea450a201($Cache, $Pile, $doublons, $Numrows, $SP))!='') ?
		($t1 . ('
					<li><a href="' .
		htmlspecialchars(vider_url(generer_url_rubrique($Pile[$SP]['id_rubrique']))) .
		'"' .
		((strval($t3 = (calcul_exposer($Pile[$SP]['id_rubrique'], 'id_rubrique', $Pile[0]) ? 'on' : ''))!='') ?
				(' class="' . $t3 . '"') :
				('')) .
		'>' .
		interdire_scripts(couper(typo($Pile[$SP]['titre']),'80')) .
		'</a>' .
		BOUCLE_rehtml_f84d615e5ef60879da71de6ea450a201($Cache, $Pile, $doublons, $Numrows, $SP) .
		'	</li>
				')) :
		(''));
	}

	$GLOBALS['spip_lang'] = $old_lang;
	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE rubriques>
//
function BOUCLE_rubriqueshtml_f84d615e5ef60879da71de6ea450a201(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("rubriques.id_rubrique",
		"0+rubriques.titre AS num",
		"rubriques.titre",
		"rubriques.lang"), # SELECT
		array('rubriques' => 'spip_rubriques'), # FROM
		
			array(
			array('=', 'rubriques.id_parent', 0), 
			array('=', 'rubriques.statut', '"publie"')), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array('num', 'rubriques.titre'), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'rubriques', # table
		'_rubriques', # boucle
		''); # serveur
	$t0 = "";
	$SP++;
	$old_lang = $GLOBALS['spip_lang'];

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {
		if (!$GLOBALS['forcer_lang'])
	 		$GLOBALS['spip_lang'] = ($x = $Pile[$SP]['lang']) ? $x : $old_lang;
		$t0 .= ('
		<li>
			<a href="' .
htmlspecialchars(vider_url(generer_url_rubrique($Pile[$SP]['id_rubrique']))) .
'"' .
((strval($t1 = (calcul_exposer($Pile[$SP]['id_rubrique'], 'id_rubrique', $Pile[0]) ? 'on' : ''))!='') ?
		(' class="' . $t1 . '"') :
		('')) .
'>' .
interdire_scripts(couper(typo($Pile[$SP]['titre']),'80')) .
'</a>

			' .
((strval($t1 = BOUCLE_sous_rubriqueshtml_f84d615e5ef60879da71de6ea450a201($Cache, $Pile, $doublons, $Numrows, $SP))!='') ?
		('
			<ul>
				' . $t1 . '
			</ul>
			') :
		('')) .
'

		</li>
	');
	}

	$GLOBALS['spip_lang'] = $old_lang;
	@spip_abstract_free($result,'');
	return $t0;
}



//
// Fonction principale du squelette dist/inc-rubriques.html
//
function html_f84d615e5ef60879da71de6ea450a201($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = ('

' .
((strval($t1 = BOUCLE_rubriqueshtml_f84d615e5ef60879da71de6ea450a201($Cache, $Pile, $doublons, $Numrows, $SP))!='') ?
		(('
<div class="rubriques">
	<h2 class="menu-titre">' .
		_T('public/spip/ecrire:rubriques') .
		'</h2>
	<ul>
	') . $t1 . '

	</ul>
</div>
') :
		('')));

	return analyse_resultat_skel('html_f84d615e5ef60879da71de6ea450a201', $Cache, $page);
}

?>