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

	if (!defined('_DIR_PLUGIN_FORMS')){
		$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
		define('_DIR_PLUGIN_FORMS',(_DIR_PLUGINS.end($p))."/");
	}

	function Forms_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees" 
		AND (!isset($GLOBALS['meta']['activer_forms']) OR $GLOBALS['meta']['activer_forms']!="non") ) {

		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['naviguer']->sousmenu["tables_tous"]= new Bouton(
			"../"._DIR_PLUGIN_FORMS."img_pack/table-24.gif",  // icone
			_T("forms:grilles") //titre
			);
		}
		return $boutons_admin;
	}
	
	function Forms_affiche_milieu($flux) {
		$exec =  $flux['args']['exec'];
		if ($exec=='articles'){
			$id_article = $flux['args']['id_article'];
			$forms_lier_donnees = charger_fonction('forms_lier_donnees','inc');
			$flux['data'] .= "<div id='forms_lier_donnees'><div id='forms_lier_donnees-$id_article'>";
			$flux['data'] .= $forms_lier_donnees($id_article, $exec);
			$flux['data'] .= "</div></div>";
		}
		return $flux;
	}
	
	function Forms_affiche_droite($flux){
		if (_request('exec')=='articles_edit'){
			include_spip('inc/forms');
			$flux['data'] .= Forms_afficher_insertion_formulaire($flux['arg']['id_article']);
		}
		return $flux;
	}
	function Forms_header_prive($flux){
		$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."spip_forms.css' type='text/css' media='all' />\n";
		$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."img_pack/date_picker.css' type='text/css' media='all' />\n";
		$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."img_pack/jtip.css' type='text/css' media='all' />\n";
		$flux .= "<script type='text/javascript'><!--\n var ajaxcharset='utf-8';\n//--></script>";
		if (_request('exec')=='articles'){
			$flux .= "<script src='".find_in_path('javascript/iautocompleter.js')."' type='text/javascript'></script>\n"; 
			$flux .= "<script src='".find_in_path('javascript/interface.js')."' type='text/javascript'></script>\n"; 
			if (!_request('var_noajax'))
				$flux .= "<script src='"._DIR_PLUGIN_FORMS."javascript/forms_lier_donnees.js' type='text/javascript'></script>\n";
		}
		if (_request('exec')=='forms_edit'){
			$flux .= "<script src='"._DIR_PLUGIN_FORMS."javascript/interface.js' type='text/javascript'></script>";
			if (!_request('var_noajax'))
				$flux .= "<script src='"._DIR_PLUGIN_FORMS."javascript/forms_edit.js' type='text/javascript'></script>";
			$flux .= 	"<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."spip_forms_prive.css' type='text/css' media='all' />\n";
		
			if($GLOBALS['meta']['multi_rubriques']=="oui" || $GLOBALS['meta']['multi_articles']=="oui")
				$active_langs = "'".str_replace(",","','",$GLOBALS['meta']['langues_multilingue'])."'";
			else
				$active_langs = "";
			$flux .= "<script src='".find_in_path('forms_lang.js')."' type='text/javascript'></script>\n". 
			"<script type='text/javascript'>\n".
			"var forms_def_lang='".$GLOBALS["spip_lang"]."';var forms_avail_langs=[$active_langs];\n".
			"$(forms_init_lang);\n".
			"</script>\n";
		}
		if (_request('exec')=='donnees_edit'){
			$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."img_pack/donnees_edit.css' type='text/css' media='all' />\n";
			$flux .= "<script src='"._DIR_PLUGIN_FORMS."javascript/interface.js' type='text/javascript'></script>";
			if (!_request('var_noajax'))
				$flux .= "<script src='"._DIR_PLUGIN_FORMS."javascript/donnees_edit.js' type='text/javascript'></script>";
		}
		return $flux;
	}
?>