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

include_spip('inc/forms');

function afficher_tables_tous($type_form, $titre_page, $titre_type, $titre_creer){
	global $spip_lang_right;
  include_spip("inc/presentation");
	include_spip('public/assembler');

  Forms_install();
	
	debut_page($titre_page, "documents", "forms");
	debut_gauche();
	debut_boite_info();
	echo _T("forms:boite_info");
	echo "<p>";
	fin_boite_info();
	
	creer_colonne_droite();
	if (include_spip('inc/snippets'))
		echo boite_snippets($titre_type,_DIR_PLUGIN_FORMS."img_pack/$type_form-24.gif",'forms','forms');
	
	debut_droite();
	
	$contexte = array('type_form'=>$type_form,'titre_liste'=>$titre_page,'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
	echo recuperer_fond("exec/template/tables_tous",$contexte);
	
	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('creer','form')) {
		echo "<div align='right'>";
		$link=generer_url_ecrire('forms_edit', "new=oui&type_form=$type_form");
		$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
		icone($titre_creer, $link, "../"._DIR_PLUGIN_FORMS. "img_pack/$type_form-24.png", "creer.gif");
		echo "</div>";
	}
	
	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	echo fin_page();
}


function affichage_donnees_tous($type_form, $titre_page, $titre_type, $titre_ajouter){
	global $spip_lang_right;
  	include_spip("inc/presentation");
	include_spip('public/assembler');
  Forms_install();
	
	echo debut_page($titre_page, "documents", "forms");
	if (!$retour = _request('retour'))
		$retour = generer_url_ecrire($type_form.'s_tous');
	echo "<table><tr><td>";
	echo "<div style='float:left;'>";
	echo icone_horizontale(_T('icone_retour'), urldecode($retour), "../"._DIR_PLUGIN_FORMS."img_pack/$type_form-24.png", "rien.gif",false);
	echo "</div>";
	$url_edit = generer_url_ecrire('donnees_edit',"id_form="._request('id_form'));
	$url_edit = parametre_url($url_edit,'retour',urlencode(self()));
	echo "<div style='float:left;'>";
	echo icone_horizontale($titre_ajouter, $url_edit, "../"._DIR_PLUGIN_FORMS."img_pack/donnees-24.png", "creer.gif",false);
	echo "</div>";
	
	$row=spip_fetch_array(spip_query("SELECT titre FROM spip_forms WHERE id_form="._q(_request('id_form'))));
	echo gros_titre($row['titre']);
	echo "<div class='verdana2'>";
	echo '<p><div id="sorting">
	<div></div>
	</div>
	<div id="filter"></div></p></div>
	<div style="clear:both">&nbsp;</div>';
	
	$contexte = array('id_form'=>_request('id_form'),'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
	echo recuperer_fond("exec/template/donnees_tous",$contexte);
	
	echo "</td></tr></table><br />\n";
	

	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	echo fin_page();
}

function affichage_donnee_edit($type_form, $titre_page, $titre_type, $titre_ajouter){
	global $spip_lang_right;
  include_spip("inc/presentation");
	include_spip('public/assembler');

  Forms_install();
  $id_form = intval(_request('id_form'));
  $id_donnee = intval(_request('id_donnee'));
  $res = spip_query("SELECT id_form,statut FROM spip_forms_donnees WHERE id_donnee="._q($id_donnee));
  if ($row = spip_fetch_array($res))
  if (!$id_form && $id_donnee){
		$id_form = $row['id_form'];
  }
  $statut = $row['statut'];
  
	$contexte = array('id_form'=>$id_form,'id_donnee'=>$id_donnee,'type_form'=>$type_form,'titre_liste'=>$titre_page,'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
	$formulaire = recuperer_fond("modeles/form",$contexte);
	$row = spip_fetch_array(spip_query("SELECT COUNT(id_donnee) AS n FROM spip_forms_donnees WHERE id_form="._q($id_form)));
	$nb_reponses = intval($row['n']);
	
	debut_page($titre_page, "documents", "forms");
	debut_gauche();
	debut_boite_info();
	if ($retour = _request('retour')) {
		echo icone_horizontale(_T('icone_retour'), urldecode($retour), "../"._DIR_PLUGIN_FORMS."img_pack/$type_form-24.png", "rien.gif",false);
	}
	icone_horizontale(_T("forms:suivi_reponses")."<br />".(($nb_reponses==0)?_T("forms:aucune_reponse"):(($nb_reponses==1)?_T("forms:une_reponse"):_T("forms:nombre_reponses",array('nombre'=>$nb_reponses)))),
		generer_url_ecrire('donnees_tous',"id_form=$id_form"), "../"._DIR_PLUGIN_FORMS."img_pack/donnees-24.png", "rien.gif");
	echo "<p>";
	fin_boite_info();
	
 	$res = spip_query("SELECT documents FROM spip_forms WHERE id_form="._q($id_form));
 	$row = spip_fetch_array($res);
 	if ($row['documents']=='oui'){
		if ($id_donnee){
			# affichage sur le cote des pieces jointes, en reperant les inserees
			# note : traiter_modeles($texte, true) repere les doublons
			# aussi efficacement que propre(), mais beaucoup plus rapidement
			echo afficher_documents_colonne($id_donnee, "donnee", _request('exec'));
		} else {
			# ICI GROS HACK
			# -------------
			# on est en new ; si on veut ajouter un document, on ne pourra
			# pas l'accrocher a l'article (puisqu'il n'a pas d'id_article)...
			# on indique donc un id_article farfelu (0-id_auteur) qu'on ramassera
			# le moment venu, c'est-ˆ-dire lors de la creation de l'article
			# dans editer_article.
			echo afficher_documents_colonne(0-$GLOBALS['auteur_session']['id_auteur'], "donnee", _request('exec'));
		}
 	}
	
	creer_colonne_droite();
	if ($id_donnee){
		$table_donnee_deplace = charger_fonction('table_donnee_deplace','inc');
		echo ajax_action_auteur('table_donnee_deplace',"$id_form-$id_donnee",'donnees_edit', "id_form=$id_form&id_donnee=$id_donnee", 
			$table_donnee_deplace($id_donnee,$id_form));		
	}
	
	/*if (include_spip('inc/snippets'))
		echo boite_snippets($titre_type,_DIR_PLUGIN_FORMS."img_pack/$type_form-24.gif",'forms','forms');*/
	
	debut_droite();
	if ($id_donnee){
		echo debut_cadre_relief();
		$instituer_forms_donnee = charger_fonction('instituer_forms_donnee','inc');
		echo $instituer_forms_donnee($id_form,$id_donnee,$statut);
		echo fin_cadre_relief();
	}

	echo "<div class='verdana2'>$formulaire</div>";
	
	if ($id_donnee) {
		if ($GLOBALS['spip_version_code']<1.92)		ob_start(); // des echo direct en 1.9.1
		$liste = afficher_articles(_T("forms:info_articles_lies_donnee"),
			array('FROM' => 'spip_articles AS articles, spip_forms_donnees_articles AS lien',
			'WHERE' => "lien.id_article=articles.id_article AND id_donnee="._q($id_donnee)." AND statut!='poubelle'",
			'ORDER BY' => "titre"));
		if ($GLOBALS['spip_version_code']<1.92) {
			$liste = ob_get_contents();
			ob_end_clean();
		}
		echo $liste;
	}
	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	echo fin_page();
}
?>