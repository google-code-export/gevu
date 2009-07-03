<?php
/*
 * Squelette : ../plugins/grilles/formulaires/forms.html
 * Date :      Mon, 03 Dec 2007 21:39:12 GMT
 * Compile :   Thu, 02 Jul 2009 18:34:49 GMT (0.09s)
 * Boucles :   _form
 */ 
//
// <BOUCLE forms>
//
function BOUCLE_formhtml_23426d630b4505485fb62e437d29d02b(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("forms.id_form",
		"forms.descriptif",
		"forms.type_form"), # SELECT
		array('forms' => 'spip_forms'), # FROM
		
			array(
			array('=', 'forms.id_form', spip_abstract_quote(interdire_scripts(entites_html($Pile[0]['id_form']))))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array(), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'forms', # table
		'_form', # boucle
		''); # serveur
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$t0 .= ('
<a name=\'form' .
$Pile[$SP]['id_form'] .
'\'></a>
<div class=\'spip_forms\'>

<div class=\'spip_descriptif\'>' .
interdire_scripts(propre($Pile[$SP]['descriptif'])) .
'</div>
' .
((strval($t1 = interdire_scripts((($Pile[$SP]['type_form'] == 'sondage-public') ? ' ':'')))!='') ?
		($t1 . ('
		<a href=\'' .
	interdire_scripts(parametre_url(generer_url_public('sondage'),'id_form',$Pile[$SP]['id_form'])) .
	'\' class=\'spip_in resultats_sondage\'
		 target="spip_sondage" onclick="javascript:window.open(this.href, \'spip_sondage\', \'scrollbars=yes, resizable=yes, width=450, height=300\'); return false;" 
		 onkeypress="javascript:window.open(this.href, \'spip_sondage\', \'scrollbars=yes,resizable=yes, width=450, height=300\'); return false;">' .
	_T('forms:voir_resultats') .
	'</a>
')) :
		('')) .
'
' .
((strval($t1 = interdire_scripts(entites_html($Pile[0]['formok'])))!='') ?
		('<p class=\'spip_form_ok\'>' . $t1 . ('
	' .
	((strval($t2 = interdire_scripts((($Pile[$SP]['type_form'] == 'sondage') ? ' ':'')))!='') ?
			($t2 . ('	<a href=\'' .
		interdire_scripts(entites_html($Pile[0]['self'])) .
		'#form' .
		$Pile[$SP]['id_form'] .
		'\'>' .
		_T('forms:valider') .
		'</a>')) :
			('')) .
	'
	' .
	((strval($t2 = interdire_scripts(entites_html($Pile[0]['reponse'])))!='') ?
			('<span class=\'spip_form_ok_confirmation\'>' . $t2 . '</span>') :
			('')) .
	'
</p>')) :
		('')) .
'
' .
((strval($t1 = interdire_scripts($Pile[0]['erreur_message']))!='') ?
		('<p class=\'spip_form_erreur\'>' . $t1 . '</p>') :
		('')) .
'
' .
((strval($t1 = interdire_scripts(entites_html($Pile[0]['url_validation'])))!='') ?
		('<img src=\'' . $t1 . '\' width=\'1\' height=\'1\' alt=\'validation de la saisie\' />') :
		('')) .
'
' .
((strval($t1 = interdire_scripts((entites_html($Pile[0]['formvisible']) ? ' ':'')))!='') ?
		($t1 . ('
	' .
	((strval($t2 = interdire_scripts(entites_html($Pile[0]['formactif'])))!='') ?
			($t2 . ('
	<form method=\'post\' action=\'' .
		interdire_scripts(entites_html($Pile[0]['self'])) .
		'#form' .
		$Pile[$SP]['id_form'] .
		'\'
		enctype=\'multipart/form-data\'>
	')) :
			('')) .
	'	
		<div>
		' .
	interdire_scripts(form_hidden(entites_html($Pile[0]['self']))) .
	'
		<input type=\'hidden\' name=\'ajout_reponse\' value=\'oui\' />
		<input type=\'hidden\' name=\'id_form\' value=\'' .
	$Pile[$SP]['id_form'] .
	'\' />
		' .
	((strval($t2 = interdire_scripts(entites_html($Pile[0]['id_donnee'])))!='') ?
			('<input type=\'hidden\' name=\'id_donnee\' value=\'' . $t2 . '\' />') :
			('')) .
	'
		
		<input type=\'hidden\' name=\'retour_form\' value=\'' .
	interdire_scripts(entites_html($Pile[0]['url_retour'])) .
	'\' />
		' .
	((strval($t2 = interdire_scripts((($Pile[$SP]['type_form'] == 'sondage') ? ' ':'')))!='') ?
			($t2 . '<input type=\'hidden\' name=\'ajout_cookie_form\' value=\'oui\' />') :
			('')) .
	'
		</div>
			' .
	
'<?php
	$contexte_inclus = array(\'fond\' => ' . argumenter_squelette(interdire_scripts(entites_html($Pile[0]['class']))) . ',
	\'id_form\' => ' . argumenter_squelette($Pile[$SP]['id_form']) . ',
	\'affiche_sondage\' => ' . argumenter_squelette($Pile[0]['affiche_sondage']) . ',
	\'erreur\' => ' . argumenter_squelette($Pile[0]['erreur']) . ',
	\'valeurs\' => ' . argumenter_squelette($Pile[0]['valeurs']) . ',
	\'lang\' => ' . argumenter_squelette($GLOBALS["spip_lang"]) . ');
	include(\'public.php\');
?'.'>' .
	'
	' .
	((strval($t2 = interdire_scripts(entites_html($Pile[0]['formactif'])))!='') ?
			($t2 . '
	</form>
	') :
			('')) .
	'
	' .
	'
	' .
	((strval($t2 = interdire_scripts(calculer_notes()))!='') ?
			('<div class=\'spip_form_notes\'>' . $t2 . '</div>') :
			('')) .
	'
')) :
		('')) .
'
</div>
');
	}

	@spip_abstract_free($result,'');
	return $t0;
}



//
// Fonction principale du squelette ../plugins/grilles/formulaires/forms.html
//
function html_23426d630b4505485fb62e437d29d02b($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = (BOUCLE_formhtml_23426d630b4505485fb62e437d29d02b($Cache, $Pile, $doublons, $Numrows, $SP) .
'
');

	return analyse_resultat_skel('html_23426d630b4505485fb62e437d29d02b', $Cache, $page);
}

?>