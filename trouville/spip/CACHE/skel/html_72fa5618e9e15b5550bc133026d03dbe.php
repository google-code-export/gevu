<?php
/*
 * Squelette : ../plugins/grilles/exec/template/donnees_tous.html
 * Date :      Mon, 03 Dec 2007 21:39:13 GMT
 * Compile :   Thu, 02 Jul 2009 19:07:53 GMT (0.11s)
 * Boucles :   _form, _auteur, _rep, _body, _docs, _donnees, _head
 */ 
//
// <BOUCLE forms>
//
function BOUCLE_formhtml_72fa5618e9e15b5550bc133026d03dbe(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("forms.documents"), # SELECT
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
		'_form', # boucle
		''); # serveur
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$t0 .= interdire_scripts(vide($Pile['vars']['documents'] = interdire_scripts((($Pile[$SP]['documents'] == 'oui') ? ' ':''))));
	}

	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE auteurs>
//
function BOUCLE_auteurhtml_72fa5618e9e15b5550bc133026d03dbe(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("auteurs.nom"), # SELECT
		array('auteurs' => 'spip_auteurs','lien' => 'spip_auteurs_articles','articles' => 'spip_articles'), # FROM
		
			array(
			array('=', 'auteurs.id_auteur', spip_abstract_quote($Pile[$SP]['id_auteur'])), 
			array('=', 'lien.id_auteur', 'auteurs.id_auteur'), 
			array('=', 'lien.id_article', 'articles.id_article'), 
			array('=', 'articles.statut', '"publie"'), 
			array('!=', 'auteurs.statut', '"5poubelle"')), # WHERE
		array(), # WHERE pour jointure
		"auteurs.id_auteur", # GROUP
		array(), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'auteurs', # table
		'_auteur', # boucle
		''); # serveur
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$t0 .= interdire_scripts(typo($Pile[$SP]['nom']));
	}

	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE forms_donnees_champs>
//
function BOUCLE_rephtml_72fa5618e9e15b5550bc133026d03dbe(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("forms_donnees_champs.valeur"), # SELECT
		array('forms_donnees_champs' => 'spip_forms_donnees_champs'), # FROM
		
			array(
			array('=', 'forms_donnees_champs.id_donnee', spip_abstract_quote($Pile[$SP-1]['id_donnee'])), 
			array('=', 'forms_donnees_champs.champ', spip_abstract_quote($Pile[$SP]['champ']))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array(), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'forms_donnees_champs', # table
		'_rep', # boucle
		''); # serveur
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$t1 = interdire_scripts($Pile[$SP]['valeur']);
		$t0 .= (($t1 && $t0) ? '<br />' : '') . $t1;
	}

	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE forms_champs>
//
function BOUCLE_bodyhtml_72fa5618e9e15b5550bc133026d03dbe(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {
	$in0 = array();
	$in0[]= 'separateur';
	$in0[]= 'textestatique';

	// REQUETE
	$result = spip_optim_select(
		array("forms_champs.champ",
		"FIELD(forms_champs.type," . join(',',array_map('spip_abstract_quote', $in0)) . ") AS cpt1"), # SELECT
		array('forms_champs' => 'spip_forms_champs'), # FROM
		
			array(
			array('=', 'forms_champs.id_form', spip_abstract_quote($Pile[$SP]['id_form']))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array('forms_champs.rang'), # ORDER
		'', # LIMIT
		'', # sous
		
			array(
			array('=', 'cpt1', 0)), # HAVING
		'forms_champs', # table
		'_body', # boucle
		''); # serveur
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$t0 .= ('
		<td>

		' .
BOUCLE_rephtml_72fa5618e9e15b5550bc133026d03dbe($Cache, $Pile, $doublons, $Numrows, $SP) .
'

		</td>
		');
	}

	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE documents>
//
function BOUCLE_docshtml_72fa5618e9e15b5550bc133026d03dbe(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("documents.id_document"), # SELECT
		array('L1' => 'spip_documents_donnees','documents' => 'spip_documents'), # FROM
		
			array(
			array('=', 'L1.id_donnee', spip_abstract_quote($Pile[$SP]['id_donnee'])), 
			array('(documents.taille > 0 OR documents.distant="oui")')), # WHERE
		array(1 => array('documents', 'id_document')), # WHERE pour jointure
		"documents.id_document", # GROUP
		array(), # ORDER
		'0,1', # LIMIT
		'', # sous
		
			array(), # HAVING
		'documents', # table
		'_docs', # boucle
		''); # serveur
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$t0 .= ('
		' .
((strval($t1 = ($Pile["vars"]['documents'] ? '':' '))!='') ?
		($t1 . '<td>') :
		('')) .
'
		<a href=\'' .
$Pile["vars"]['lien_edit'] .
'\'>' .
interdire_scripts(vide($Pile['vars']['src'] = interdire_scripts(concat(eval('return '.'_DIR_IMG_PACK'.';'),'attachment.gif')))) .
'<img src=\'' .
$Pile["vars"]['src'] .
'\' largeur=\'\' hauteur=\'\' alt=\'' .
_T('public/spip/ecrire:titre_documents_joints') .
'\' /></a>
		' .
((strval($t1 = ($Pile["vars"]['documents'] ? '':' '))!='') ?
		($t1 . '</td>') :
		('')) .
'
		');
	}

	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE forms_donnees>
//
function BOUCLE_donneeshtml_72fa5618e9e15b5550bc133026d03dbe(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {

	// REQUETE
	$result = spip_optim_select(
		array("forms_donnees.id_auteur",
		"forms_donnees.id_donnee",
		"forms_donnees.id_form",
		"forms_donnees.statut",
		"forms_donnees.rang",
		"forms_donnees.date",
		"forms_donnees.ip",
		"forms_donnees.url"), # SELECT
		array('forms_donnees' => 'spip_forms_donnees'), # FROM
		
			array(
			array('=', 'forms_donnees.id_form', spip_abstract_quote($Pile[$SP]['id_form'])), 
			array('NOT', 
			array('=', 'forms_donnees.statut', "'poubelle'"))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array('forms_donnees.rang'), # ORDER
		'', # LIMIT
		'', # sous
		
			array(), # HAVING
		'forms_donnees', # table
		'_donnees', # boucle
		''); # serveur
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$t0 .= ('
' .
interdire_scripts(vide($Pile['vars']['lien_supp'] = '<?php echo generer_action_auteur("'.'forms_donnee_supprime'.'","'.($Pile[$SP]['id_form'] .
		':' .
		$Pile[$SP]['id_donnee']).'","'.interdire_scripts(urlencode(concat(eval('return '.'_DIR_RESTREINT_ABS'.';'),quote_amp(self())))).'"); ?>')) .
interdire_scripts(vide($Pile['vars']['lien_edit'] = interdire_scripts(parametre_url(parametre_url(generer_url_ecrire('donnees_edit',('id_form=' .
		$Pile[$SP]['id_form'])),'id_donnee',$Pile[$SP]['id_donnee']),'retour',quote_amp(self()))))) .
'<tr class=\'tr_liste\'>
		<td>' .
forms_puce_statut_donnee($Pile[$SP]['id_donnee'],interdire_scripts($Pile[$SP]['statut']),$Pile[$SP]['id_form']) .
'</td>
		<td><a href=\'' .
$Pile["vars"]['lien_edit'] .
'\'>' .
$Pile[$SP]['id_donnee'] .
'</a></td>
		<td>' .
(($Pile[$SP]['rang'])?($Pile[$SP]['rang']):recuperer_numero($Pile[0]['titre'])) .
'</td>
		<td>' .
vider_date($Pile[$SP]['date']) .
'</td>
		<td>' .
((strval($t1 = BOUCLE_auteurhtml_72fa5618e9e15b5550bc133026d03dbe($Cache, $Pile, $doublons, $Numrows, $SP))!='') ?
		($t1) :
		(interdire_scripts($Pile[$SP]['ip']))) .
'</th>
		<td>' .
interdire_scripts($Pile[$SP]['url']) .
'</td>
		' .
BOUCLE_bodyhtml_72fa5618e9e15b5550bc133026d03dbe($Cache, $Pile, $doublons, $Numrows, $SP) .
'
		' .
((strval($t1 = $Pile["vars"]['documents'])!='') ?
		($t1 . '<td>') :
		('')) .
'
		' .
BOUCLE_docshtml_72fa5618e9e15b5550bc133026d03dbe($Cache, $Pile, $doublons, $Numrows, $SP) .
'
		' .
((strval($t1 = $Pile["vars"]['documents'])!='') ?
		($t1 . '</td>') :
		('')) .
'
	</tr>
');
	}

	@spip_abstract_free($result,'');
	return $t0;
}


//
// <BOUCLE forms_champs>
//
function BOUCLE_headhtml_72fa5618e9e15b5550bc133026d03dbe(&$Cache, &$Pile, &$doublons, &$Numrows, $SP) {
	$in1 = array();
	$in1[]= 'separateur';
	$in1[]= 'textestatique';

	// REQUETE
	$result = spip_optim_select(
		array("FIELD(forms_champs.type," . join(',',array_map('spip_abstract_quote', $in1)) . ") AS cpt2",
		"forms_champs.specifiant",
		"forms_champs.titre"), # SELECT
		array('forms_champs' => 'spip_forms_champs'), # FROM
		
			array(
			array('=', 'forms_champs.id_form', spip_abstract_quote($Pile[$SP]['id_form']))), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array('forms_champs.rang'), # ORDER
		'', # LIMIT
		'', # sous
		
			array(
			array('=', 'cpt2', 0)), # HAVING
		'forms_champs', # table
		'_head', # boucle
		''); # serveur
	$Numrows['_head']['total'] = @spip_abstract_count($result,'');
	$Numrows['_head']['compteur_boucle'] = 0;
	$t0 = "";
	$SP++;

	// RESULTATS
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {

		$Numrows['_head']['compteur_boucle']++;
		$t0 .= ('
		<th ' .
((strval($t1 = interdire_scripts((($Pile[$SP]['specifiant'] == 'oui') ? '':'class="neutre"')))!='') ?
		($t1 . interdire_scripts(vide($Pile['vars']['colonnes_sans_tri'] = concat(concat($Pile["vars"]['colonnes_sans_tri'],','),plus($Numrows['_head']['compteur_boucle'],'5'))))) :
		('')) .
'>' .
interdire_scripts(typo($Pile[$SP]['titre'])) .
'</th>
		');
	}

	@spip_abstract_free($result,'');
	return $t0;
}



//
// Fonction principale du squelette ../plugins/grilles/exec/template/donnees_tous.html
//
function html_72fa5618e9e15b5550bc133026d03dbe($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = ('<?php header("X-Spip-Cache: 0"); ?>'.'<?php header("Cache-Control: no-store, no-cache, must-revalidate"); ?><?php header("Pragma: no-cache"); ?>' .
interdire_scripts(vide($Pile['vars']['colonnes_sans_tri'] = '0,1')) .
BOUCLE_formhtml_72fa5618e9e15b5550bc133026d03dbe($Cache, $Pile, $doublons, $Numrows, $SP) .
'
	<link rel="stylesheet" type="text/css" href="' .
interdire_scripts(eval('return '.'_DIR_PLUGIN_FORMS'.';')) .
'img_pack/donnees_tous.css" />
	<script type="text/javascript" src="' .
interdire_scripts(eval('return '.'_DIR_PLUGIN_FORMS'.';')) .
'javascript/jquery.tablesorter.dev.js"></script>
' .
((strval($t1 = BOUCLE_headhtml_72fa5618e9e15b5550bc133026d03dbe($Cache, $Pile, $doublons, $Numrows, $SP))!='') ?
		(('
<div class=\'liste\'>
<div style=\'position: relative;\'>
	<div style=\'position: absolute; top: -12px; left: 3px;\'><img src=\'' .
		interdire_scripts(eval('return '.'_DIR_PLUGIN_FORMS'.';')) .
		'img_pack/' .
		interdire_scripts($Pile[0]['type_form']) .
		'-24.png\' alt="" /></div>
	<div style=\'background-color: ' .
		interdire_scripts(entites_html($Pile[0]['couleur_claire'])) .
		'; color: black; padding: 3px; padding-left: 30px; border-bottom: 1px solid #444444;\' class=\'verdana2\'>
	<b>' .
		_T('forms:tous_formulaires') .
		'</b>
	</div>
</div>
<table class="arial11 donnees" id=\'donnees\' width=\'auto\' cellpadding=\'5\' cellspacing=\'0\' border=\'0\'>
<thead>
	<tr>
		<th class=\'neutre\'></th>
		<th class=\'neutre\'>id</th>
		<th>Rang</th>
		<th>Date</th>
		<th>De</th>
		<th>Page</th>
		') . $t1 . ('
		' .
		interdire_scripts(vide($Pile['vars']['colonnes_sans_tri'] = concat(concat($Pile["vars"]['colonnes_sans_tri'],','),plus($Numrows['_head']['total'],'6')))) .
		((strval($t3 = $Pile["vars"]['documents'])!='') ?
				($t3 . '<th class=\'neutre\'>&nbsp;</th>') :
				('')) .
		'
	</tr>
</thead>
<tbody>
' .
		BOUCLE_donneeshtml_72fa5618e9e15b5550bc133026d03dbe($Cache, $Pile, $doublons, $Numrows, $SP) .
		'
</tbody>
</table>
</div>
')) :
		('')) .
'
	<script type="text/javascript"><!--
	var search_string=\'' .
_T('public/spip/ecrire:bouton_chercher') .
'\';
	var clear_string=\'' .
_T('public/spip/ecrire:info_tout_afficher') .
'\';
	var apply_string=\'' .
_T('public/spip/ecrire:bouton_valider') .
'\';
	$(document).ready(function() {
		$("table#donnees").tableSorter({
		  
			sortClassAsc: \'headerSortUp\', 		// class name for ascending sorting action to header
			sortClassDesc: \'headerSortDown\',	// class name for descending sorting action to header
			headerClass: \'header\', 				// class name for headers (th\'s)
		
			disableHeader: [' .
$Pile["vars"]['colonnes_sans_tri'] .
'], // disable column can be a string / number or array containing string or number. 
			dateFormat: \'dd/mm/yyyy\' // set date format for non iso dates default us, in this case override and set uk-format
		});
		$("div#sorting").hide();
	});
	$(document).sortStart(function(){
		$("div#sorting").show();
	}).sortStop(function(a){
		$("div#sorting").hide();
	});
	// -->
	</script>	');

	return analyse_resultat_skel('html_72fa5618e9e15b5550bc133026d03dbe', $Cache, $page);
}

?>