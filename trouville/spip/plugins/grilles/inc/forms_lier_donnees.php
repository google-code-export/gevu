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

include_spip("inc/presentation");
include_spip("inc/layer");
include_spip("base/forms");
include_spip("inc/forms");

function inc_forms_lier_donnees($id_article, $script, $deplie=false){
  global $spip_lang_left, $spip_lang_right, $options;
	global $connect_statut, $options,$connect_id_auteur, $couleur_claire ;
	
	$out = "";
	$out .= "<a name='tables'></a>";
	if (_request('cherche_donnee') || $deplie){
		$bouton = bouton_block_visible("tables_article");
		$debut_block = 'debut_block_visible';
	}
	else{
		$bouton = bouton_block_invisible("tables_article");
		$debut_block = 'debut_block_invisible';
	}

	$out .= debut_cadre_enfonce("../"._DIR_PLUGIN_FORMS."img_pack/table-24.gif", true, "", $bouton._T('forms:tables'));

	$lesdonnees = array();
	//
	// Afficher les donnees liees, rangees par tables
	//
	list($s,$les_donnees) = Forms_formulaire_article_afficher_donnees($id_article,$script);
	$out .= $s;
	
	$out .= $debut_block("tables_article",true);
	//
	// Afficher le formulaire de recherche des donnees des tables
	//
	$out .= Forms_formulaire_article_chercher_donnee($id_article,$les_donnees, $script);
	$out .= fin_block(true);
	
	$out .= fin_cadre_enfonce(true);
	return $out;
}

function Forms_formulaire_article_chercher_donnee($id_article,$les_donnees, $script){
  global $spip_lang_right,$spip_lang_left,$couleur_claire,$couleur_foncee;
	$out = "";
	$recherche = _request('cherche_donnee');
	
	if (!include_spip("inc/securiser_action"))
		include_spip("inc/actions");
	$redirect = ancre_url(generer_url_ecrire($script,"id_article=$id_article"),'tables');
	$action = generer_action_auteur("forms_lier_donnees","$id_article,ajouter");
	$out .= "<form action='$action' method='post' class='ajaxAction' >";
	$out .= form_hidden($action);
	$out .= "<input type='hidden' name='redirect' value='$redirect' />";
	$out .= "<input type='hidden' name='idtarget' value='forms_lier_donnees-$id_article' />";
	$out .= "<input type='hidden' name='redirectajax' value='".generer_url_ecrire('forms_lier_donnees',"id_article=$id_article")."' />";
	$out .= "<div style='text-align:$spip_lang_left'>";

	//deb ajout lucky semiosis
	$out .= Forms_boite_selection_table();
	//$out .= "<input id ='autocompleteMe' type='text' name='cherche_donnee' value='$recherche' class='forml' />";
	//$out .= Forms_boite_selection_donnees($recherche?$recherche:((_request('ajouter')!==NULL)?"":$recherche),$les_donnees);
	//$script_rech = generer_url_ecrire("recherche_donnees","id_article=$id_article",true);
	//fin ajout lucky semiosis
	
	$out .= "<input type='hidden' name='autocompleteUrl' value='$script_rech' />";

	$out .= "<style type='text/css' media='all'>
.autocompleter
{
	border: 1px solid $couleur_foncee;
	width: 350px;
	background-color: $couleur_claire;
}
.autocompleter ul li
{
	padding: 2px 10px;
	white-space: nowrap;
	font-size: 11px;
}
.selectAutocompleter
{
	background-color: $couleur_foncee;
}</style>";
	
	$out .= "</div>";
	$out .= "<div style='text-align:$spip_lang_right'>";
	$out .= "<input type='submit' name='ajouter' value='"._T('bouton_ajouter')."' class='fondo' />";
	$out .= "</div>";
	$out .= "</form>";
	return $out;
}

function Forms_formulaire_article_afficher_donnees($id_article, $script){
	global $exec;
	
	$out = "";

	$les_donnees = array();
	$liste = array();
	$forms = array();
	$retour = self();
	
	$res = spip_query("SELECT id_donnee FROM spip_forms_donnees_articles AS d WHERE d.id_article="._q($id_article));
	while ($row = spip_fetch_array($res)){
		list($id_form,$titreform,$t) = Forms_liste_decrit_donnee($row['id_donnee']);
		if (count($t)){
			$liste[$id_form][$row['id_donnee']]=$t;
			$forms[$id_form] = $titreform;
		}
	}
	
	if (count($liste)) {
		$out .= "<div class='liste liste-donnees'>";
		$out .= "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>";
		$table = array();
		foreach($liste as $id_form=>$donnees){
			$vals = array();
			$vals[] = "";
			$vals[] = "<a href='".generer_url_ecrire("donnees_tous","id_form=$id_form&retour=".urlencode($retour))."'>".$forms[$id_form]."</a>";
			$vals[] = "";
			$table[] = $vals;
			foreach($donnees as $id_donnee=>$champs){
				$les_donnees[] = $id_donnee;
				$vals = array();
				$vals[] = $id_donnee;
				$vals[] = "<a href='".generer_url_ecrire("donnees_edit","id_form=$id_form&id_donnee=$id_donnee&retour=".urlencode($retour))."'>"
					.implode(", ",$champs)."</a>";
				$redirect = ancre_url(generer_url_ecrire($script,"id_article=$id_article"),'tables');
				$action = generer_action_auteur("forms_lier_donnees","$id_article,retirer,$id_donnee",urlencode($redirect));
				$action = ancre_url($action,"forms_lier_donnees-$id_article");
				$redirajax = generer_url_ecrire("forms_lier_donnees","id_article=$id_article");
				$vals[] = "<a href='$action' rel='$redirajax' class='ajaxAction' >"
					. _T('forms:lien_retirer_donnee')."&nbsp;". http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' align='middle'")
					. "</a>";
				$table[] = $vals;
			}
		}
		$largeurs = array('', '', '', '', '');
		$styles = array('arial11', 'arial11', 'arial2', 'arial11', 'arial11');
		$out .= afficher_liste($largeurs, $table, $styles, false);
	
		$out .= "</table></div>\n";
	}
	$les_donnees = implode (',',$les_donnees);
	return array($out,$les_donnees) ;
}

function Forms_boite_selection_donnees($recherche, $les_donnees){
	$out = "";
	$liste_res = Forms_liste_recherche_donnees($recherche,$les_donnees);

	if (count($liste_res)){
		$out .= "<select name='id_donnee' class='fondl' style='width:100%' size='10'>";
		foreach($liste_res as $titre=>$donnees){
			$out .= "<option value=''>$titre</option>";
			foreach($donnees as $id_donnee=>$champs){
				$out .= "<option value='$id_donnee'>&nbsp;&nbsp;&nbsp;";
				$out .= implode (", ",$champs);
				$out .= "</option>";
			}
		}
		$out .= "</select>";
	}
	$out .= "<input id='_id_donnee' type='hidden' name='_id_donnee' value='' />";
	return $out;
}

//deb ajout lucky semiosis
function Forms_boite_selection_table(){
	$out = "";
	include_spip('base/abstract_sql');
	$res = spip_query("SELECT titre, id_form FROM spip_forms WHERE type_form = 'table'");
	while ($row = spip_fetch_array($res)){
		$out .= "<option value='".$row['id_form']."'>".$row['titre']."</option>";
	}
	if($out <> "")
		$out = "<select name='id_form' class='fondl' style='width:100%' >".$out."</select>";

	return $out;
}
//fin ajout lucky semiosis


function Forms_liste_recherche_donnees($recherche,$les_donnees){
	$table = array();
	if ($recherche!==NULL){
		include_spip('base/abstract_sql');
		$in = calcul_mysql_in('id_donnee',$les_donnees,'NOT');
		if (!strlen($recherche))
			$res = spip_query("SELECT * FROM spip_forms_donnees_champs WHERE $in GROUP BY id_donnee");
		else {
			$res = spip_query("SELECT * FROM spip_forms_donnees_champs WHERE $in AND valeur LIKE "._q("$recherche%")." GROUP BY id_donnee");
			if (spip_num_rows($res)<10){
				$res = spip_query("SELECT * FROM spip_forms_donnees_champs WHERE $in AND valeur LIKE "._q("%$recherche%")." GROUP BY id_donnee");
			}
		}
		while ($row = spip_fetch_array($res)){
			list($id_form,$titreform,$t) = Forms_liste_decrit_donnee($row['id_donnee']);
			if (count($t))
				$table[$titreform][$row['id_donnee']]=$t;
		}
	}
	return $table;
}

function Forms_liste_decrit_donnee($id_donee){
	$t = array();$titreform="";
	//deb ajout lucky semiosis
	/*
	$res2 = spip_query("SELECT c.titre,dc.valeur,f.titre AS titreform,f.id_form 
	FROM spip_forms_donnees_champs AS dc 
		JOIN spip_forms_donnees AS d ON d.id_donnee=dc.id_donnee
		JOIN spip_forms_champs AS c ON c.champ=dc.champ AND c.id_form=d.id_form
		JOIN spip_forms AS f ON f.id_form=d.id_form
	WHERE c.specifiant='oui' AND dc.id_donnee="._q($id_donee)." AND f.linkable='oui' ORDER BY c.rang");
	*/
	$res2 = spip_query("SELECT c.titre, dc.valeur, f.titre AS titreform, f.id_form, c.type, m.titre mtitre
	FROM spip_forms_donnees_champs AS dc
		INNER JOIN spip_forms_donnees AS d ON d.id_donnee = dc.id_donnee
		INNER JOIN spip_forms_champs AS c ON c.champ = dc.champ
			AND c.id_form = d.id_form
		LEFT JOIN spip_mots AS m ON m.id_mot = dc.valeur
		INNER JOIN spip_forms AS f ON f.id_form = d.id_form
	WHERE dc.id_donnee = "._q($id_donee)." ORDER BY c.rang");
	//fin ajout lucky semiosis

	/*var_dump("SELECT c.titre,dc.valeur FROM spip_forms_donnees_champs AS dc 
	JOIN spip_forms_donnees AS d ON d.id_donnee=dc.id_donnee
	JOIN spip_forms_champs AS c ON c.champ=dc.champ AND c.id_form=d.id_form
	WHERE c.specifiant='oui' AND dc.id_donnee="._q($row['id_donnee'])." ORDER BY c.rang");*/
	while ($row2 = spip_fetch_array($res2)){
		//deb ajout lucky semiosis
		if($row2['type']=='mot'){
			$t[$row2['titre']] = $row2['mtitre'];
		}else
			//fin ajour lucky semiosis
			$t[$row2['titre']] = $row2['valeur'];
		$titreform = $row2['titreform'];
		$id_form = $row2['id_form'];
	}
	return array($id_form,$titreform,$t);
}
?>