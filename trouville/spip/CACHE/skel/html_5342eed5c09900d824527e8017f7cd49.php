<?php
/*
 * Squelette : ../plugins/grilles/exec/template/tables_tous.html
 * Date :      Mon, 03 Dec 2007 21:39:13 GMT
 * Compile :   Thu, 02 Jul 2009 19:07:22 GMT (0.11s)
 * Boucles :   _rep, _forms
 */ 
//
// <BOUCLE forms_donnees>
//
function BOUCLE_rephtml_5342eed5c09900d824527e8017f7cd49(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("1"), # SELECT
		array('forms_donnees' => 'spip_forms_donnees'), # FROM
		
			array(
			array('=', 'forms_donnees.id_form', spip_abstract_quote($Pile[$SP]['id_form']))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array(), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'forms_donnees', # table
		'_rep', # boucle
		''); # serveur
	$Numrows['_rep']['total'] = @spip_abstract_count($result,'');
	$t0 = "";
	$SP++;
	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE forms>
//
function BOUCLE_formshtml_5342eed5c09900d824527e8017f7cd49(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("forms.id_form",
		"forms.type_form",
		"forms.titre"), # SELECT
		array('forms' => 'spip_forms'), # FROM
		
			array(
			array('=', 'forms.type_form', spip_abstract_quote(interdire_scripts(entites_html(sinon($Pile[0]['type_form'],'')))))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array(), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'forms', # table
		'_forms', # boucle
		''); # serveur

	// Partition
	$nombre_boucle = @spip_abstract_count($result,"");
	$debut_boucle = intval(_request("debut_forms"));
	$fin_boucle = min($debut_boucle+(($a = intval(('10 ' .
interdire_scripts(entites_html(sinon($Pile[0]['type_form'],'form')))))) ? $a : 10) - 1, $nombre_boucle - 1);
	$Numrows['_forms']["grand_total"] = $nombre_boucle;
	$Numrows['_forms']["total"] = max(0,$fin_boucle - $debut_boucle + 1);
	$Numrows['_forms']['compteur_boucle'] = 0;
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$Numrows['_forms']['compteur_boucle']++;
		if ($Numrows['_forms']['compteur_boucle']-1 >= $debut_boucle) {
		if ($Numrows['_forms']['compteur_boucle']-1 > $fin_boucle) break;

		$t0 .= ('
' .
((strval($t1 = BOUCLE_rephtml_5342eed5c09900d824527e8017f7cd49($Cache, $Pile, $doublons, $Numrows, $SP))!='') ?
		($t1) :
		(interdire_scripts(vide($Pile['vars']['donnees'] = $Numrows['_rep']['total'])))) .
'
' .
interdire_scripts(vide($Pile['vars']['lien_edite'] = interdire_scripts(parametre_url(generer_url_ecrire('forms_edit',('id_form=' .
		$Pile[$SP]['id_form'])),'retour',urlencode(quote_amp(self())))))) .
interdire_scripts(vide($Pile['vars']['lien_affiche'] = interdire_scripts(parametre_url(generer_url_ecrire(interdire_scripts((match($Pile[$SP]['type_form'],'(^$|^sondage$)') ? 'forms_reponses':'donnees_tous')),('id_form=' .
		$Pile[$SP]['id_form'])),'retour',urlencode(quote_amp(self())))))) .
((strval($t1 = ($Pile["vars"]['donnees'] ? '':' '))!='') ?
		($t1 . ('
	' .
	((strval($t2 = interdire_scripts((match($Pile[$SP]['type_form'],'(^$|^sondage$)') ? ' ':'')))!='') ?
			($t2 . (' ' .
		interdire_scripts(vide($Pile['vars']['lien_affiche'] = '')))) :
			('')) .
	'
	' .
	((strval($t2 = interdire_scripts((match($Pile[$SP]['type_form'],'(^$|^sondage$)') ? '':' ')))!='') ?
			($t2 . (' 
		' .
		interdire_scripts(vide($Pile['vars']['lien_affiche'] = interdire_scripts(parametre_url(generer_url_ecrire(interdire_scripts(concat($Pile[$SP]['type_form'],'_donnee_edit')),('id_form=' .
				$Pile[$SP]['id_form'])),'retour',urlencode(quote_amp(self())))))))) :
			('')) .
	'
')) :
		('')) .
'
' .
interdire_scripts(vide($Pile['vars']['lien_duplique'] = '<?php echo generer_action_auteur("'.'forms_duplique'.'","'.$Pile[$SP]['id_form'].'","'.interdire_scripts(urlencode(concat(eval('return '.'_DIR_RESTREINT_ABS'.';'),quote_amp(self())))).'"); ?>')) .
((strval($t1 = ($Pile["vars"]['snippet_present'] ? ' ':''))!='') ?
		($t1 . (' ' .
	interdire_scripts(vide($Pile['vars']['lien_exporte'] = '<?php echo generer_action_auteur("'.'snippet_exporte'.'","'.('forms:' .
			$Pile[$SP]['id_form']).'"); ?>')))) :
		('')) .
'
' .
interdire_scripts(vide($Pile['vars']['lien_vider'] = '<?php echo generer_action_auteur("'.'forms_donnees_vide'.'","'.$Pile[$SP]['id_form'].'","'.interdire_scripts(urlencode(concat(eval('return '.'_DIR_RESTREINT_ABS'.';'),quote_amp(self())))).'"); ?>')) .
interdire_scripts(vide($Pile['vars']['lien_supprimer'] = interdire_scripts(ancre_url(parametre_url(parametre_url(generer_url_ecrire('forms_edit',('supp_form=' .
		$Pile[$SP]['id_form'])),'type_form',interdire_scripts(entites_html(sinon($Pile[0]['type_form'],'')))),'retour',urlencode(quote_amp(self()))),'resume')))) .
'<tr class=\'tr_liste\'>
<td class="arial11">
<img src=\'' .
interdire_scripts(eval('return '.'_DIR_IMG_PACK'.';')) .
'puce-' .
($Pile["vars"]['donnees'] ? 'verte':'orange') .
'-breve.gif\' width=\'7\' height=\'7\' border=\'0\' alt=\'puce\'>&nbsp;&nbsp;
</td>
<td class="arial11">
	<a href=\'' .
$Pile["vars"]['lien_edite'] .
'\' class=\'cellule-h\'>
	' .
interdire_scripts(typo($Pile[$SP]['titre'])) .
'</a>
</td>
<td class="arial1">&nbsp;
</td>
<td class="arial1">' .
((strval($t1 = $Pile["vars"]['lien_affiche'])!='') ?
		('<a href=\'' . $t1 . ('\' title=\'' .
	_T('forms:afficher') .
	'\'><img src=\'' .
	interdire_scripts(eval('return '.'_DIR_PLUGIN_FORMS'.';')) .
	'img_pack/donnees-24.png\' width=\'24\' height=\'24\' alt=\'' .
	_T('forms:afficher') .
	'\' /></a>')) :
		('')) .
'</td>
<td class="arial1">' .
_T((($Pile["vars"]['donnees'] == '0') ? 'forms:aucune_reponse':'')) .
_T((($Pile["vars"]['donnees'] == '1') ? 'forms:une_reponse':'')) .
'
' .
((strval($t1 = (($Pile["vars"]['donnees'] > '1') ? ' ':''))!='') ?
		($t1 . ('  ' .
	interdire_scripts(eval('return '.('_T("forms:nombre_reponses",array("nombre"=>' .
		$Pile["vars"]['donnees'] .
		')) ').';')) .
	'  ')) :
		('')) .
'</td>
<td class="arial1"><a href=\'' .
$Pile["vars"]['lien_edite'] .
'\' title=\'' .
_T('forms:editer') .
'\'><img src=\'' .
interdire_scripts(eval('return '.'_DIR_PLUGIN_FORMS'.';')) .
'img_pack/editer-24.png\' width=\'24\' height=\'24\' alt=\'' .
_T('forms:editer') .
'\' /></a></td>
<td class="arial1"><a href=\'' .
$Pile["vars"]['lien_duplique'] .
'\' title=\'' .
_T('forms:dupliquer') .
'\'><img src=\'' .
interdire_scripts(eval('return '.'_DIR_PLUGIN_FORMS'.';')) .
'img_pack/dupliquer-24.png\' width=\'24\' height=\'24\' alt=\'' .
_T('forms:dupliquer') .
'\' /></a></td>
' .
((strval($t1 = $Pile["vars"]['lien_exporte'])!='') ?
		('<td class="arial1"><a href=\'' . $t1 . ('\' title=\'' .
	_T('forms:exporter') .
	'\'><img src=\'' .
	interdire_scripts(eval('return '.'_DIR_PLUGIN_FORMS'.';')) .
	'img_pack/exporter-form-24.png\' width=\'24\' height=\'24\' alt=\'' .
	_T('forms:exporter') .
	'\' /></a></td>')) :
		('')) .
'
<td>&nbsp;</td>
<td class="arial1">' .
((strval($t1 = ($Pile["vars"]['donnees'] ? ' ':''))!='') ?
		($t1 . ('<a href=\'' .
	$Pile["vars"]['lien_vider'] .
	'\' title=\'' .
	_T('forms:vider') .
	'\'><img src=\'' .
	interdire_scripts(eval('return '.'_DIR_PLUGIN_FORMS'.';')) .
	'img_pack/vider-24.png\' width=\'24\' height=\'24\' alt=\'' .
	_T('forms:vider') .
	'\' /></a>')) :
		('')) .
'</td>
<td class="arial1"><a href=\'' .
$Pile["vars"]['lien_supprimer'] .
'\' title=\'' .
_T('forms:supprimer') .
'\'><img src=\'' .
interdire_scripts(eval('return '.'_DIR_PLUGIN_FORMS'.';')) .
'img_pack/supprimer-24.png\' width=\'24\' height=\'24\' alt=\'' .
_T('forms:supprimer') .
'\' /></a></td>
</tr>
');
		}

	}

	@spip_abstract_free($result,'');
	return $t0;
}



//
// Fonction principale du squelette ../plugins/grilles/exec/template/tables_tous.html
//
function html_5342eed5c09900d824527e8017f7cd49($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = ('<?php header("X-Spip-Cache: 0"); ?>'.'<?php header("Cache-Control: no-store, no-cache, must-revalidate"); ?><?php header("Pragma: no-cache"); ?>' .
interdire_scripts(vide($Pile['vars']['snippet_present'] = interdire_scripts(eval('return '.'defined(\'_DIR_PLUGIN_SNIPPETS\')'.';')))) .

(($f = (((strval($t1 = BOUCLE_formshtml_5342eed5c09900d824527e8017f7cd49($Cache, $Pile, $doublons, $Numrows, $SP))!='') ?
		(('
<div class=\'liste\'>
<div style=\'position: relative;\'>
	<div style=\'position: absolute; top: -12px; left: 3px;\'><img src=\'' .
		interdire_scripts(eval('return '.'_DIR_PLUGIN_FORMS'.';')) .
		'img_pack/' .
		interdire_scripts(entites_html(sinon($Pile[0]['type_form'],'form'))) .
		'-24.png\' alt="" /></div>
	<div style=\'background-color: ' .
		interdire_scripts(entites_html($Pile[0]['couleur_claire'])) .
		'; color: black; padding: 3px; padding-left: 30px; border-bottom: 1px solid #444444;\' class=\'verdana2\'>
	<b>' .
		((strval($t3 = interdire_scripts((entites_html(sinon($Pile[0]['titre_liste'],'')) ? '':' ')))!='') ?
				($t3 . _T('forms:tous_formulaires')) :
				('')) .
		interdire_scripts(entites_html(sinon($Pile[0]['titre_liste'],''))) .
		'</b>
	</div>
</div>
<table width=\'100%\' cellpadding=\'5\' cellspacing=\'0\' border=\'0\'>
' .
		((strval($t3 = calcul_pagination(
	(isset($Numrows['_forms']['grand_total']) ?
		$Numrows['_forms']['grand_total'] : $Numrows['_forms']['total']
	), '_forms', (($a = intval(('10 ' .
interdire_scripts(entites_html(sinon($Pile[0]['type_form'],'form')))))) ? $a : 10), true ))!='') ?
				(('<tr style=\'background-color: #dddddd;\'>
<td class="arial1" style=\'border-bottom: 1px solid #444444;\' colspan="' .
			($Pile["vars"]['snippet_present'] ? '11':'10') .
			'">
<div class=\'pagination\'>') . $t3 . '</div>
</td>
</tr>') :
				('')) .
		'
') . $t1 . '
</table></div>
') :
		(''))))?
				'<div id="fragment_html_5342eed5c09900d824527e8017f7cd49_forms" class="fragment">'.$f.'<!-- /fragment_html_5342eed5c09900d824527e8017f7cd49_forms --></div><?php stop_inclure("fragment_html_5342eed5c09900d824527e8017f7cd49_forms"); ?>':'')
 .
'
&nbsp;<br/>');

	return analyse_resultat_skel('html_5342eed5c09900d824527e8017f7cd49', $Cache, $page);
}

?>