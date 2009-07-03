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

function exec_tables_tous(){
	echo afficher_tables_tous('table',_T("forms:toutes_tables"),_T("forms:tables"),_T("forms:icone_creer_table"));
}

?>