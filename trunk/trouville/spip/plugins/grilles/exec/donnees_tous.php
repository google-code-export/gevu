<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip('exec/template/tables_affichage');

function exec_donnees_tous(){
	$res = spip_query("SELECT type_form FROM spip_forms WHERE id_form="._q(_request('id_form')));
	if (!$row = spip_fetch_array($res)) die ('erreur formulaire inexistant');
	$type_form = $row['type_form'];
	if ($type_form=='table')
		echo affichage_donnees_tous('table',_T("forms:toutes_tables"),_T("forms:tables"),_T("forms:icone_ajouter_donnee"));
	else
		echo affichage_donnees_tous($type_form?$type_form:'form',_T("forms:tous_formulaires"),_T("forms:formulaire"),_T("forms:icone_ajouter_donnee"));
}

?>