<?php
/*
 * Squelette : ../plugins/grilles/formulaires/forms_structure.html
 * Date :      Mon, 03 Dec 2007 21:39:12 GMT
 * Compile :   Thu, 02 Jul 2009 18:34:49 GMT (0.25s)
 * Boucles :   _champs, _form
 */ 
//
// <BOUCLE forms_champs>
//
function BOUCLE_champshtml_ead2362fc08b4d59efd4b9f601e9169d(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("forms_champs.type",
		"forms_champs.titre",
		"forms_champs.champ",
		"forms_champs.obligatoire",
		"forms_champs.aide",
		"forms_champs.id_form",
		"forms_champs.extra_info"), # SELECT
		array('forms_champs' => 'spip_forms_champs'), # FROM
		
			array(
			array('=', 'forms_champs.id_form', spip_abstract_quote($Pile[$SP]['id_form']))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array('forms_champs.rang'), # ORDER
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
((strval($t1 = interdire_scripts(entites_html($Pile[0]['affiche_sondage'])))!='') ?
		($t1 . (' ' .
	interdire_scripts($Pile[0]['resultats_sondage']))) :
		('')) .
'
' .
((strval($t1 = interdire_scripts((entites_html($Pile[0]['affiche_sondage']) ? '':' ')))!='') ?
		($t1 . ('
	' .
	((strval($t2 = interdire_scripts(((typo($Pile[$SP]['type']) == 'separateur') ? interdire_scripts(typo($Pile[$SP]['titre'])):'')))!='') ?
			((($Pile["vars"]['need_fieldset'] ? '':'</fieldset>') .
		'<fieldset><legend>') . $t2 . ('</legend> ' .
		interdire_scripts(vide($Pile['vars']['need_fieldset'] = '0')))) :
			('')) .
	'
	' .
	((strval($t2 = ($Pile["vars"]['need_fieldset'] ? ' ':''))!='') ?
			($t2 . ('<fieldset><legend>' .
		interdire_scripts(typo($Pile[$SP-1]['titre'])) .
		'</legend> ' .
		interdire_scripts(vide($Pile['vars']['need_fieldset'] = '0')))) :
			('')) .
	'
	' .
	((strval($t2 = interdire_scripts(((typo($Pile[$SP]['type']) == 'separateur') ? '':' ')))!='') ?
			($t2 . ('
		<div class=\'spip_form_champ\'>
			' .
		interdire_scripts(((typo($Pile[$SP]['type']) == 'textestatique') ? interdire_scripts(typo($Pile[$SP]['titre'])):'')) .
		'
			' .
		((strval($t3 = interdire_scripts(((typo($Pile[$SP]['type']) == 'textestatique') ? '':' ')))!='') ?
				($t3 . ('
				' .
			interdire_scripts(vide($Pile['vars']['afficher'] = '1')) .
			'<span class=\'spip_form_label\'>
					' .
			((strval($t4 = interdire_scripts((match(typo($Pile[$SP]['type']),'^(select|multiple|mot)') ? '':' ')))!='') ?
					($t4 . ('<label for="' .
				interdire_scripts($Pile[$SP]['champ']) .
				'">' .
				interdire_scripts(typo($Pile[$SP]['titre'])) .
				'</label>')) :
					('')) .
			'
					' .
			((strval($t4 = interdire_scripts((match(typo($Pile[$SP]['type']),'^(select|multiple|mot)') ? ' ':'')))!='') ?
					($t4 . ('<span class=\'label\'>' .
				interdire_scripts(typo($Pile[$SP]['titre'])) .
				'</span>')) :
					('')) .
			'
					' .
			((strval($t4 = interdire_scripts((($Pile[$SP]['obligatoire'] == 'oui') ? _T('forms:info_obligatoire_02'):'')))!='') ?
					(('<span class=\'spip_form_label_obligatoire' .
				((strval($t5 = (forms_valeur($Pile[0]['erreur'],interdire_scripts($Pile[$SP]['champ'])) ? ' ':''))!='') ?
						($t5 . 'obligatoire_oublie') :
						('')) .
				'\'>
						') . $t4 . '</span>') :
					('')) .
			'
					' .
			interdire_scripts((strlen(typo($Pile[$SP]['titre'])) ? ':':'')) .
			'
				</span>
				' .
			((strval($t4 = interdire_scripts(($Pile[$SP]['aide'] ? '?':'')))!='') ?
					(('<span class="formInfo"><a href="' .
				interdire_scripts(generer_url_public('forms_tip',('id_form=' .
					$Pile[$SP]['id_form'] .
					'&champ=' .
					interdire_scripts($Pile[$SP]['champ']) .
					'&width=200'))) .
				'" class="jTip" name="" id=\'aide_' .
				$Pile[0]['id_form_'] .
				interdire_scripts($Pile[$SP]['champ']) .
				'\'>') . $t4 . '</a></span>') :
					('')) .
			'
				' .
			((strval($t4 = interdire_scripts(((typo($Pile[$SP]['type']) == 'date') ? ' ':'')))!='') ?
					($t4 . (' ' .
				interdire_scripts(vide($Pile['vars']['afficher'] = '0')) .
				interdire_scripts(vide($Pile['vars']['date-picker'] = '1')) .
				'<input type=\'text\' name=\'' .
				interdire_scripts($Pile[$SP]['champ']) .
				'\' id=\'' .
				interdire_scripts($Pile[$SP]['champ']) .
				'\' value="' .
				entites_html(forms_valeur($Pile[0]['valeurs'],interdire_scripts($Pile[$SP]['champ']),'')) .
				'" 
						class=\'' .
				interdire_scripts((($Pile[$SP]['obligatoire'] == 'oui') ? 'forml':'formo')) .
				((strval($t5 = (forms_valeur($Pile[0]['erreur'],interdire_scripts($Pile[$SP]['champ'])) ? ' ':''))!='') ?
						($t5 . ' champ_obli_oubli') :
						('')) .
				' date-picker\'
						size=\'40\' />
				')) :
					('')) .
			'
				' .
			((strval($t4 = interdire_scripts(((typo($Pile[$SP]['type']) == 'email') ? ' ':'')))!='') ?
					($t4 . (' ' .
				interdire_scripts(vide($Pile['vars']['afficher'] = '0')) .
				'<span class=\'spip_form_label_details\'>' .
				_T('forms:champ_email_details') .
				'</span>
					<input type=\'text\' name=\'' .
				interdire_scripts($Pile[$SP]['champ']) .
				'\' id=\'' .
				interdire_scripts($Pile[$SP]['champ']) .
				'\' value="' .
				entites_html(forms_valeur($Pile[0]['valeurs'],interdire_scripts($Pile[$SP]['champ']),'')) .
				'" 
						class=\'' .
				interdire_scripts((($Pile[$SP]['obligatoire'] == 'oui') ? 'forml':'formo')) .
				((strval($t5 = (forms_valeur($Pile[0]['erreur'],interdire_scripts($Pile[$SP]['champ'])) ? ' ':''))!='') ?
						($t5 . ' champ_obli_oubli') :
						('')) .
				'\' 
						size=\'40\' />
				')) :
					('')) .
			'
				' .
			((strval($t4 = interdire_scripts(((typo($Pile[$SP]['type']) == 'url') ? ' ':'')))!='') ?
					($t4 . (' ' .
				interdire_scripts(vide($Pile['vars']['afficher'] = '0')) .
				'<span class=\'spip_form_label_details\'>' .
				_T('forms:champ_url_details') .
				'</span>
					<input type=\'text\' name=\'' .
				interdire_scripts($Pile[$SP]['champ']) .
				'\' id=\'' .
				interdire_scripts($Pile[$SP]['champ']) .
				'\' value="' .
				entites_html(forms_valeur($Pile[0]['valeurs'],interdire_scripts($Pile[$SP]['champ']),'')) .
				'" 
						class=\'' .
				interdire_scripts((($Pile[$SP]['obligatoire'] == 'oui') ? 'forml':'formo')) .
				((strval($t5 = (forms_valeur($Pile[0]['erreur'],interdire_scripts($Pile[$SP]['champ'])) ? ' ':''))!='') ?
						($t5 . ' champ_obli_oubli') :
						('')) .
				'\' 
						size=\'40\' />
				')) :
					('')) .
			'
				' .
			((strval($t4 = interdire_scripts(((typo($Pile[$SP]['type']) == 'texte') ? ' ':'')))!='') ?
					($t4 . (' ' .
				interdire_scripts(vide($Pile['vars']['afficher'] = '0')) .
				forms_textarea(forms_valeur($Pile[0]['valeurs'],interdire_scripts($Pile[$SP]['champ']),''),'10','80',interdire_scripts($Pile[$SP]['champ']),interdire_scripts($Pile[$SP]['champ']),(interdire_scripts(concat((($Pile[$SP]['obligatoire'] == 'oui') ? 'forml':'formo'),(forms_valeur($Pile[0]['erreur'],interdire_scripts($Pile[$SP]['champ'])) ? 'champ_obli_oubli':''))) .
					' ')) .
				'
				')) :
					('')) .
			'
				' .
			((strval($t4 = interdire_scripts(((typo($Pile[$SP]['type']) == 'fichier') ? ' ':'')))!='') ?
					($t4 . (' ' .
				interdire_scripts(vide($Pile['vars']['afficher'] = '0')) .
				'<input type=\'file\' name=\'' .
				interdire_scripts($Pile[$SP]['champ']) .
				'\' id=\'' .
				interdire_scripts($Pile[$SP]['champ']) .
				'\'
						class=\'' .
				interdire_scripts((($Pile[$SP]['obligatoire'] == 'oui') ? 'forml':'formo')) .
				((strval($t5 = (forms_valeur($Pile[0]['erreur'],interdire_scripts($Pile[$SP]['champ'])) ? ' ':''))!='') ?
						($t5 . ' champ_obli_oubli') :
						('')) .
				'\' 
						size=\'40\' />
					' .
				forms_valeur($Pile[0]['valeurs'],interdire_scripts($Pile[$SP]['champ']),'') .
				'
				')) :
					('')) .
			'
				' .
			((strval($t4 = interdire_scripts(((typo($Pile[$SP]['type']) == 'select') ? ' ':'')))!='') ?
					($t4 . (' ' .
				interdire_scripts(vide($Pile['vars']['afficher'] = '0')) .
				
'<?php
	$contexte_inclus = array(\'fond\' => ' . argumenter_squelette('formulaires/forms_champ_select') . ',
	\'id_form\' => ' . argumenter_squelette($Pile[$SP]['id_form']) . ',
	\'champ\' => ' . argumenter_squelette($Pile[$SP]['champ']) . ',
	\'valeur\' => ' . argumenter_squelette($Pile[0]['valeurs']) . ',
	\'lang\' => ' . argumenter_squelette($GLOBALS["spip_lang"]) . ');
	include(\'public.php\');
?'.'>' .
				'
				')) :
					('')) .
			'
				' .
			((strval($t4 = interdire_scripts(((typo($Pile[$SP]['type']) == 'multiple') ? ' ':'')))!='') ?
					($t4 . (' ' .
				interdire_scripts(vide($Pile['vars']['afficher'] = '0')) .
				
'<?php
	$contexte_inclus = array(\'fond\' => ' . argumenter_squelette('formulaires/forms_champ_multiple') . ',
	\'id_form\' => ' . argumenter_squelette($Pile[$SP]['id_form']) . ',
	\'champ\' => ' . argumenter_squelette($Pile[$SP]['champ']) . ',
	\'valeur\' => ' . argumenter_squelette($Pile[0]['valeurs']) . ',
	\'lang\' => ' . argumenter_squelette($GLOBALS["spip_lang"]) . ');
	include(\'public.php\');
?'.'>' .
				'
				')) :
					('')) .
			'
				' .
			((strval($t4 = interdire_scripts(((typo($Pile[$SP]['type']) == 'mot') ? ' ':'')))!='') ?
					($t4 . (' ' .
				interdire_scripts(vide($Pile['vars']['afficher'] = '0')) .
				
'<?php
	$contexte_inclus = array(\'fond\' => ' . argumenter_squelette('formulaires/forms_select_mot') . ',
	\'id_groupe\' => ' . argumenter_squelette(interdire_scripts($Pile[$SP]['extra_info'])) . ',
	\'champ\' => ' . argumenter_squelette($Pile[$SP]['champ']) . ',
	\'valeur\' => ' . argumenter_squelette($Pile[0]['valeurs']) . ',
	\'lang\' => ' . argumenter_squelette($GLOBALS["spip_lang"]) . ');
	include(\'public.php\');
?'.'>' .
				'
				')) :
					('')) .
			'
				' .
			((strval($t4 = interdire_scripts(((typo($Pile[$SP]['type']) == 'articles_mot') ? ' ':'')))!='') ?
					($t4 . (' ' .
				interdire_scripts(vide($Pile['vars']['afficher'] = '0')) .
				
'<?php
	$contexte_inclus = array(\'fond\' => ' . argumenter_squelette('formulaires/forms_select_article_mot') . ',
	\'id_mot\' => ' . argumenter_squelette(interdire_scripts($Pile[$SP]['extra_info'])) . ',
	\'champ\' => ' . argumenter_squelette($Pile[$SP]['champ']) . ',
	\'valeur\' => ' . argumenter_squelette($Pile[0]['valeurs']) . ',
	\'lang\' => ' . argumenter_squelette($GLOBALS["spip_lang"]) . ');
	include(\'public.php\');
?'.'>' .
				'
				')) :
					('')) .
			'
				' .
			'
				' .
			((strval($t4 = ($Pile["vars"]['afficher'] ? ' ':''))!='') ?
					($t4 . ('
					<input type=\'text\' name=\'' .
				interdire_scripts($Pile[$SP]['champ']) .
				'\' id=\'' .
				interdire_scripts($Pile[$SP]['champ']) .
				'\' value="' .
				entites_html(forms_valeur($Pile[0]['valeurs'],interdire_scripts($Pile[$SP]['champ']),'')) .
				'" 
						class=\'' .
				((strval($t5 = interdire_scripts(typo($Pile[$SP]['type'])))!='') ?
						($t5 . ' ') :
						('')) .
				interdire_scripts((($Pile[$SP]['obligatoire'] == 'oui') ? 'forml':'formo')) .
				((strval($t5 = (forms_valeur($Pile[0]['erreur'],interdire_scripts($Pile[$SP]['champ'])) ? ' ':''))!='') ?
						($t5 . ' champ_obli_oubli') :
						('')) .
				'\' 
						size=\'40\' />
				')) :
					('')) .
			'
				' .
			((strval($t4 = forms_valeur($Pile[0]['erreur'],interdire_scripts($Pile[$SP]['champ'])))!='') ?
					('<span class=\'erreur\'>' . $t4 . '</span>') :
					('')) .
			'
				<span class=\'nettoyeur\'> </span>
			')) :
				('')) .
		'
		</div>
	')) :
			('')) .
	'
')) :
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
function BOUCLE_formhtml_ead2362fc08b4d59efd4b9f601e9169d(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("forms.id_form",
		"forms.titre"), # SELECT
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
' .
interdire_scripts(vide($Pile['vars']['need_fieldset'] = '1')) .
interdire_scripts(vide($Pile['vars']['date-picker'] = '0')) .
BOUCLE_champshtml_ead2362fc08b4d59efd4b9f601e9169d($Cache, $Pile, $doublons, $Numrows, $SP) .
'
</fieldset>
' .
((strval($t1 = interdire_scripts((entites_html($Pile[0]['affiche_sondage']) ? '':' ')))!='') ?
		($t1 . ('
	' .
	'
	<p style=\'display:none;\'><label for="nobotnobot">' .
	_T('public/spip/ecrire:antispam_champ_vide') .
	'</label>
	<input type="text" name="nobotnobot" id="nobotnobot" value="' .
	interdire_scripts(entites_html($Pile[0]['nobotnobot'])) .
	'" size="10" /></p>
	
	<div style=\'text-align:' .
	lang_dir(($Pile[0]['lang'] ? $Pile[0]['lang'] : $GLOBALS['spip_lang']),'right','left') .
	'\' class=\'spip_bouton\'><input type="submit" name=\'Valider\' value="' .
	_T('public/spip/ecrire:bouton_valider') .
	'" /></div>
')) :
		('')) .
'
<script src="' .
interdire_scripts(find_in_path('javascript/jtip.js')) .
'" type="text/javascript"></script>
' .
((strval($t1 = ($Pile["vars"]['date-picker'] ? ' ':''))!='') ?
		($t1 . ('
<script src="' .
	interdire_scripts(find_in_path('javascript/jquery-dom.js')) .
	'" type="text/javascript"></script>
<script src="' .
	interdire_scripts(find_in_path('javascript/datePicker.js')) .
	'" type="text/javascript"></script>
')) :
		('')) .
'
<script type="text/javascript"><!--
$(\'input.formo\').bind(\'focus\',function(){$(this).removeClass(\'formo\').addClass(\'formo-focus\');});
$(\'input.formo\').bind(\'blur\',function(){$(this).removeClass(\'formo-focus\').addClass(\'formo\');});
$(\'input.forml\').bind(\'focus\',function(){$(this).removeClass(\'forml\').addClass(\'forml-focus\');});
$(\'input.forml\').bind(\'blur\',function(){$(this).removeClass(\'forml-focus\').addClass(\'forml\');});
' .
((strval($t1 = ($Pile["vars"]['date-picker'] ? ' ':''))!='') ?
		($t1 . ('
	$.datePicker.setDateFormat(\'ddmmyyyy\',\'/\');
	' .
	unicode2charset(charset2unicode(
//#INCLURE recuperer_fond('',array('fond' => 'formulaires/date_picker_init' ,'html' => $Pile[0]['html'] ,'lang' => $GLOBALS["spip_lang"] )).
recuperer_fond('',array('fond' => 'formulaires/date_picker_init' ,'html' => $Pile[0]['html'] ,'lang' => $GLOBALS["spip_lang"] )),'html')) .
	'
	$(\'input.date-picker\').datePicker({startDate:\'01/01/1900\'});
')) :
		('')) .
'
//--></script>
');
	}

	@spip_abstract_free($result,'');
	return $t0;
}



//
// Fonction principale du squelette ../plugins/grilles/formulaires/forms_structure.html
//
function html_ead2362fc08b4d59efd4b9f601e9169d($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = BOUCLE_formhtml_ead2362fc08b4d59efd4b9f601e9169d($Cache, $Pile, $doublons, $Numrows, $SP);

	return analyse_resultat_skel('html_ead2362fc08b4d59efd4b9f601e9169d', $Cache, $page);
}

?>