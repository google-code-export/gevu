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

function exec_forms_lier_donnees(){
	$id_article = intval(_request('id_article'));
	$forms_lier_donnees = charger_fonction('forms_lier_donnees','inc');
	$out = $forms_lier_donnees($id_article, _request('script'), true);
	ajax_retour($out);
}

?>