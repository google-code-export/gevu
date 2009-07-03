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
if (!include_spip('inc/autoriser'))
	include_spip('inc/autoriser_compat');

function action_forms_lier_donnees(){
	global $auteur_session;
	$args = _request('arg');
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = urldecode(_request('redirect'));
	$cherche_donnee = _request('cherche_donnee');
	$id_donnee = intval(_request('id_donnee'));
	if (!$id_donnee) $id_donnee = intval(_request('_id_donnee'));
	//deb ajout lucky semiosis
	$id_form = intval(_request('id_form'));
	//fin ajout lucky semiosis

	if ($redirect==NULL) $redirect="";
	if (!include_spip("inc/securiser_action"))
		include_spip("inc/actions");
	if (verifier_action_auteur("forms_lier_donnees-$args",$hash,$id_auteur)==TRUE) {
		$args = explode(",",$args);
		$id_article = intval($args[0]);
		$faire = $args[1];
		if ($faire=='ajouter'){
			if ($id_article && $id_donnee && autoriser('modifier','article',$id_article)){
				$res = spip_query("SELECT * FROM spip_forms_donnees_articles WHERE id_article="._q($id_article)." AND id_donnee="._q($id_donnee));
				if (!$row = spip_fetch_array($res))
					spip_query("INSERT INTO spip_forms_donnees_articles (id_article,id_donnee) VALUES ("._q($id_article).","._q($id_donnee).")");
				$redirect = parametre_url($redirect,'cherche_donnee','');
			}
			if (!$id_donnee){
				if ($cherche_donnee)
					$redirect = parametre_url($redirect,'cherche_donnee',$cherche_donnee);
				$redirect = parametre_url($redirect,'ajouter','1');
			}
			//deb ajout lucky semiosis
			//echo "LSD = ".$id_article."_".$id_form."_".autoriser('modifier','article',$id_article);
			if ($id_article && $id_form && autoriser('modifier','article',$id_article)){
				//ajoute une donnée
				spip_query("INSERT INTO spip_forms_donnees (id_form, id_auteur, date, ip, url, confirmation,statut, cookie, rang) ".
				"VALUES ("._q($id_form).","._q($id_auteur).", NOW(),"._q($GLOBALS['ip']).","._q($url).", '$confirmation', '$statut',"._q($cookie).","._q($rang).")");
				//ajoute le lien avec l'article
				spip_query("INSERT INTO spip_forms_donnees_articles (id_article,id_donnee) 
					SELECT "._q($id_article).", MAX(id_donnee) FROM spip_forms_donnees WHERE id_form="._q($id_form));
				//récupère la définition des champs du formulaires
				$res = spip_query("SELECT champ, type FROM spip_forms_champs WHERE id_form="._q($id_form));
				while ($row = spip_fetch_array($res)){
					if($row['type']=='texte')
						$defvalue = " ";
					if($row['type']=='date')
						$defvalue = "now()";
					//ajoute une valeur
					spip_query("INSERT INTO spip_forms_donnees_champs (champ,id_donnee, valeur) 
						SELECT "._q($row['champ']).", MAX(id_donnee), "._q($defvalue)." FROM spip_forms_donnees WHERE id_form="._q($id_form));
				}
				$redirect = parametre_url($redirect,'cherche_donnee','');
			}
			//fin ajout lucky semiosis
		}
		if ($faire='retirer'){
			$id_donnee = intval($args[2]);
			if ($id_article && $id_donnee && autoriser('modifier','article',$id_article))
				spip_query("DELETE FROM spip_forms_donnees_articles WHERE id_article="._q($id_article)." AND id_donnee="._q($id_donnee));
		}
	}
	redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
}

?>