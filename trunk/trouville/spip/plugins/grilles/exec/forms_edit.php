<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * ??? 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/forms');
include_spip('inc/forms_edit');
include_spip('inc/forms_type_champs'); // gestion des types de champs

function Forms_formulaire_confirme_suppression($id_form,$nb_reponses,$redirect,$retour,$prefixei18n='forms'){
	global $spip_lang_right;
	$out = "<div class='verdana3'>";
	if ($nb_reponses){
			$out .= "<p><strong>"._T("forms:attention")."</strong> ";
			$out .= _T("$prefixei18n:info_supprimer_formulaire_reponses")."</p>\n";
	}
	else{
		$out .= "<p>";
		$out .= _T("$prefixei18n:info_supprimer_formulaire")."</p>\n";
	}
	$link = generer_action_auteur('forms_supprime',"$id_form",_DIR_RESTREINT_ABS.($retour?(str_replace('&amp;','&',$retour)):generer_url_ecrire('forms_tous',"",false,true)));
	$out .= "<form method='POST' action='$link' style='float:$spip_lang_right'>";
	$out .= form_hidden($link);
	$out .= "<div style='text-align:$spip_lang_right'>";
	$out .= "&nbsp;<input type='submit' name='supp_confirme' value=\""._T('item_oui')."\" class='fondo'>";
	$out .= "</div>";
	$out .= "</form>\n";

	$out .= "<form method='POST' action='$redirect' style='float:$spip_lang_right'>\n";
	$out .= form_hidden($redirect);
	$out .= "<div style='text-align:$spip_lang_right'>";
	$out .= "&nbsp;<input type='submit' name='supp_rejet' value=\""._T('item_non')."\" class='fondo'>";
	$out .= "</div>";
	$out .= "</form><br />\n";
	$out .= "</div>";

	return $out;
}

function contenu_boite_resume($id_form, $row, &$apercu){
	$prefixei18n = 'forms';
	$is_form = 	in_array($row['type_form'],array('','sondage'));
	if (!$is_form)
		$prefixei18n = $row['type_form'];

	$out = "";

	// centre resume ---------------------------------------------------------------
	$out .= debut_cadre_relief("../"._DIR_PLUGIN_FORMS."img_pack/form-24.png",true);

	//$out .= gros_titre($row['titre'],'',false);

	if ($row['descriptif']) {
		$out .= "<div class='descriptif'><strong>"._T('info_descriptif')."</strong>";
		$out .= propre($row['descriptif']);
		$out .= "</div>\n";
	}
	//modif SamSzo
	/*
	if ($email = unserialize($row['email'])) {
		$out .= "<div class='email'><strong>"._T('email_2')."</strong>";
		$out .= $email['defaut'];
		$out .= "</div>\n";
	}
	*/
	if ($row['champconfirm']){
		$champconfirm_known = false;
		$out .= "<div class='champconfirm'><strong>"._T('forms:confirmer_reponse')."</strong>";
		$res2 = spip_query("SELECT titre FROM spip_forms_champs WHERE type='email' AND id_form="._q($id_form)." AND champ="._q($champconfirm));
		if ($row2 = spip_fetch_array($res2)){
			$out .= $row2['nom'] . " ";
			$champconfirm_known = true;
		}
		$out .= "</div>\n";
		if (($champconfirm_known == true) && ($row['texte'])) {
			$out .= "<div class='texte'><strong>"._T('info_texte')."</strong>";
			$out .= nl2br(entites_html($row['texte']));
			$out .= "</div>\n";
		}
	}

	if (spip_fetch_array(spip_query("SELECT * FROM spip_forms_champs WHERE id_form="._q($id_form)))) {
		$out .= "<br />";
		$out .= "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
		$out .= bouton_block_invisible("preview_form");
		$out .= "<strong class='verdana3' style='text-transform: uppercase;'>"
			._T("forms:apparence_formulaire")."</strong>";
		$out .= "</div>\n";

		$out .= debut_block_visible("apercu");
		$out .= _T("forms:info_apparence")."<p>\n";
		$out .= "<div id='apercu'>$apercu</div>";
		$out .= fin_block();
	}

	if ($GLOBALS['spip_version_code']<1.92)		ob_start(); // des echo direct en 1.9.1
	$liste = afficher_articles(_T("$prefixei18n:articles_utilisant"),
		array('FROM' => 'spip_articles AS articles, spip_forms_articles AS lien',
		'WHERE' => "lien.id_article=articles.id_article AND id_form="._q($id_form)." AND statut!='poubelle'",
		'ORDER BY' => "titre"));
	if ($GLOBALS['spip_version_code']<1.92) {
		$liste = ob_get_contents();
		ob_end_clean();
	}

	$out .= $liste;

	$out .= fin_cadre_relief(true);
	return $out;
}

function exec_forms_edit(){
	global $spip_lang_right;
	$retour = _request('retour');

	$id_form = intval(_request('id_form'));

	$new = _request('new');
	$supp_form = intval(_request('supp_form'));
	$supp_rejet = _request('supp_rejet');
	$titre = _request('titre');

	Forms_install();
	if ($supp_form)
		$id_form = $supp_form;

	if ($retour)
		$retour = urldecode($retour);
	else
		$retour = generer_url_ecrire('forms_tous',"","",true);
  include_spip("inc/presentation");
	include_spip("inc/config");

	$nb_reponses = 0;
	if ($id_form)
		if ($row = spip_fetch_array(spip_query("SELECT COUNT(*) AS num FROM spip_forms_donnees WHERE id_form="._q($id_form)." AND confirmation='valide'")))
			$nb_reponses = $row['num'];


	$redirect = generer_url_ecrire('forms_edit',(intval($id_form)?"id_form=$id_form":""));
	if ($retour)
		$redirect = parametre_url($redirect,"retour",urlencode($retour));

	//
	// Affichage de la page
	//
	if ($id_form){
		$champ_visible = _request('champ_visible');
		$nouveau_champ = _request('nouveau_champ');
		$result = spip_query("SELECT * FROM spip_forms WHERE id_form="._q($id_form));
		if ($row = spip_fetch_array($result)) {
			$id_form = $row['id_form'];
			$titre = $row['titre'];
		}
		$focus = "";
		$action_link = generer_action_auteur("forms_edit","$id_form",urlencode($redirect));
	}

	$ajax_charset = _request('var_ajaxcharset');
	$bloc = _request('bloc');
	if ($ajax_charset && $bloc=='dummy') {
		ajax_retour("");
	}
	if ($ajax_charset && $bloc=='apercu') {
		include_spip('public/assembler');
		$GLOBALS['var_mode']='calcul';
		$apercu = recuperer_fond('modeles/form',array('id_form'=>$id_form,'var_mode'=>'calcul'));
		ajax_retour($apercu);
	}
	if ($ajax_charset && $bloc=='resume') {
		include_spip('public/assembler');
		$GLOBALS['var_mode']='calcul';
		$apercu = recuperer_fond('modeles/form',array('id_form'=>$id_form,'var_mode'=>'calcul'));
		ajax_retour(contenu_boite_resume($id_form, $row, $apercu));
	}
	if ($ajax_charset && $bloc=='proprietes') {
		ajax_retour(boite_proprietes($id_form, $row, $focus, $action_link, $redirect));
	}
	$bloc = explode("-",$bloc);
	if ($ajax_charset && $bloc[0]=='champs') {
		ajax_retour(Forms_zone_edition_champs($id_form, $champ_visible, $nouveau_champ,$redirect,isset($bloc[2])?$bloc[2]:false));
	}


	debut_page("&laquo; $titre &raquo;", "documents", "forms","");

	// Recupere les donnees ---------------------------------------------------------------
	if ($new == 'oui' && !$titre) {
		$row['type_form'] = _request('type_form')?_request('type_form'):""; // possibilite de passer un type par defaut dans l'url de creation
		$prefixei18n = 'forms';
		$is_form = 	in_array($row['type_form'],array('','sondage'));
		if (!$is_form)
			$prefixei18n = $row['type_form'];

		$titre = _T("$prefixei18n:nouveau_formulaire");
		include_spip('inc/charset');
		$row['titre'] = $titre = unicode2charset(html2unicode($titre));
		$row['descriptif'] = "";
		$row['modifiable'] = 'non';
		$row['multiple'] = 'non';
		$row['forms_obligatoires'] = "";
		$row['email'] = array();
		$row['champconfirm'] = "";
		$row['texte'] = "";
		$row['moderation'] = "priori";
		$row['public'] = "non";
		$row['linkable'] = "non";
		$row['documents'] = "non";
		$focus = "antifocus";

		$action_link = generer_action_auteur("forms_edit","new",urlencode($redirect));
	}
	$prefixei18n = 'forms';
	$is_form = 	in_array($row['type_form'],array('','sondage'));
	if (!$is_form)
		$prefixei18n = $row['type_form'];


	// gauche raccourcis ---------------------------------------------------------------
	debut_gauche();

	echo "<br /><br />\n";
	debut_boite_info();
	if ($retour) {
		icone_horizontale(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_FORMS."img_pack/form-24.png", "rien.gif",'right');
	}
	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('administrer','form',$id_form)) {
		if ($nb_reponses){
			$nretour = urlencode(self());
			icone_horizontale(_T("forms:suivi_reponses")."<br />".(($nb_reponses==0)?_T("forms:aucune_reponse"):(($nb_reponses==1)?_T("forms:une_reponse"):_T("forms:nombre_reponses",array('nombre'=>$nb_reponses)))),
				generer_url_ecrire(in_array($row['type_form'],array('','sondage'))?'forms_reponses':'donnees_tous',"id_form=$id_form"), "../"._DIR_PLUGIN_FORMS."img_pack/donnees-24.png", "rien.gif");
			icone_horizontale(_T("forms:telecharger_reponses"),
				generer_url_ecrire('forms_telecharger',"id_form=$id_form&retour=$nretour"), _DIR_PLUGIN_FORMS."img_pack/donnees-exporter-24.png", "rien.gif");
		}

		if (include_spip('inc/snippets'))
			echo boite_snippets(_T("$prefixei18n:formulaire"),_DIR_PLUGIN_FORMS."img_pack/form-24.gif",'forms',$id_form);

		$link = parametre_url(self(),'new','');
		$link = parametre_url($link,'supp_form', $id_form);
		if (!$retour) {
			$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('form_tous')));
		}
		echo "<p>";
		icone_horizontale(_T("$prefixei18n:supprimer_formulaire"), $link, "../"._DIR_PLUGIN_FORMS."img_pack/supprimer-24.png", "rien.gif");
		echo "</p>";
	}
	fin_boite_info();

	// gauche apercu ---------------------------------------------------------------
	echo "<div id='apercu_gauche'>";
	include_spip('public/assembler');
	$GLOBALS['var_mode']='calcul';
	echo $apercu = recuperer_fond('modeles/form',array('id_form'=>$id_form,'var_mode'=>'calcul'));
	echo "</div>";



	// droite ---------------------------------------------------------------
	creer_colonne_droite();
	debut_droite();

	if (!$new){
		echo gros_titre($row['titre'],'',false);

		if ($supp_form && $supp_rejet==NULL)
			echo Forms_formulaire_confirme_suppression($id_form,$nb_reponses,$redirect,$retour,$prefixei18n);
		echo "<div id='barre_onglets'>";
		echo debut_onglet();
		echo onglet(_L("Aper&ccedil;u"),ancre_url(self(),"resume"),'','resume');
		echo onglet(_L("Propri&eacute;t&eacute;s"),ancre_url(self(),"proprietes"),'','proprietes');
		echo onglet(_L("Champs"),ancre_url(self(),"champs"),'','champs');
		echo fin_onglet();
		echo "</div>";
	}

	$out = "";
	if ($id_form){
		$out .= "<div id='resume' name='resume'>";
		$out .= contenu_boite_resume($id_form, $row, $apercu);
		$out .= "</div>";
	}

	// centre proprietes ---------------------------------------------------------------
	$out .= "<div id='proprietes' name='proprietes'>";
	$out .= boite_proprietes($id_form, $row, $focus, $action_link, $redirect);
	$out .= "</div>";
	
	// edition des champs ---------------------------------------------------------------
	$out .= "<div id='champs' name='champs'>";
	$out .= Forms_zone_edition_champs($id_form, $champ_visible, $nouveau_champ,$redirect);
	$out .= "</div>\n";

	echo $out;

	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	echo fin_page();
}

?>
