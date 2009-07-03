<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_instituer_forms_donnee_dist($id_form, $id_donnee, $statut, $rang=NULL)
{

	$res =
	"\n<div id='instituer_forms_donnee-$id_donnee'>" .
	"\n<center>" . 
	"<b>" .
	_T('forms:texte_donnee_statut') .
	"</b>" .
	"\n<select name='statut_nouv' size='1' class='fondl'\n" .
	"onchange=\"this.nextSibling.nextSibling.src='" .
	_DIR_IMG_PACK .
	"' + puce_statut(options[selectedIndex].value);" .
	" setvisibility('valider_statut', 'visible');\">\n" .
	"<option"  . mySel("prop", $statut)  . " style='background-color: #FFF1C6'>" ._T('texte_statut_propose_evaluation') ."</option>\n" .
	"<option"  . mySel("publie", $statut)  . " style='background-color: #B4E8C5'>" ._T('texte_statut_publie') ."</option>\n" .
	"<option"  . mySel("poubelle", $statut) .
	http_style_background('rayures-sup.gif')  . '>'  ._T('texte_statut_poubelle') ."</option>\n" .
	"<option"  . mySel("refuse", $statut)  . " style='background-color: #FFA4A4'>" ._T('texte_statut_refuse') ."</option>\n" .
	"</select>" .
	" &nbsp; " .
	http_img_pack("puce-".puce_statut($statut).'.gif', "", "border='0'") .
	"  &nbsp;\n";
	if ($rang!==NULL){
		$res .= "<input name='rang_nouv' size='4' class='fondl' value='$rang' onchange=\"setvisibility('valider_statut', 'visible');\" />";
	}
	$res .= "<span class='visible_au_chargement' id='valider_statut'>" .
	"<input type='submit' value='"._T('bouton_valider')."' class='fondo' />" .
	"</span>" .
	 "</center>"
	. '</div>';
  
	return ajax_action_auteur('instituer_forms_donnee',$id_donnee,'donnees_edit', "id_form=$id_form&id_donnee=$id_donnee", $res);
}

// http://doc.spip.org/@puce_statut_article
function puce_statut_donnee($id, $statut, $id_form, $ajax = false) {
	global $spip_lang_left, $dir_lang, $connect_statut, $options;
	static $script=NULL;
	
	switch ($statut) {
	case 'publie':
		$clip = 1;
		$puce = 'verte';
		$title = _T('info_article_publie');
		break;
	case 'prop':
		$clip = 0;
		$puce = 'orange';
		$title = _T('info_article_propose');
		break;
	case 'refuse':
		$clip = 2;
		$puce = 'rouge';
		$title = _T('info_article_refuse');
		break;
	case 'poubelle':
		$clip = 3;
		$puce = 'poubelle';
		$title = _T('info_article_supprime');
		break;
	}
	$puce = "puce-$puce.gif";

	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('publierdans', 'form', $id_form)) {
	  // les versions de MSIE ne font pas toutes pareil sur alt/title
	  // la combinaison suivante semble ok pour tout le monde.
	  $titles = array(
			  "orange" => _T('texte_statut_propose_evaluation'),
			  "verte" => _T('texte_statut_publie'),
			  "rouge" => _T('texte_statut_refuse'),
			  "poubelle" => _T('texte_statut_poubelle'));
		if ($ajax){
		  $action = "onmouseover=\"montrer('statutdecalforms_donnee$id');\"";
		  $inser_puce = 
		  	// "\n<div class='puce_forms_donnee' id='statut$id'$dir_lang>" .
				"\n<div class='puce_forms_donnee_fixe' $action>" .
			  http_img_pack($puce, $title, "id='imgstatutforms_donnee$id' style='margin: 1px;'") ."</div>"
				. "\n<div class='puce_forms_donnee_popup' id='statutdecalforms_donnee$id' onmouseout=\"cacher('statutdecalforms_donnee$id');\" style=' margin-left: -".((11*$clip)+1)."px;'>\n"
				. afficher_script_statut($id, 'forms_donnee', -1, 'puce-orange.gif', 'prop', $titles['orange'], $action)
				. afficher_script_statut($id, 'forms_donnee', -12, 'puce-verte.gif', 'publie', $titles['verte'], $action)
				. afficher_script_statut($id, 'forms_donnee', -23, 'puce-rouge.gif', 'refuse', $titles['rouge'], $action)
				. afficher_script_statut($id, 'forms_donnee', -34, 'puce-poubelle.gif', 'poubelle', $titles['poubelle'], $action)
			. "</div>"
			//. "</div>"
			;
		}
		else{
		  $inser_puce = "\n<div class='puce_forms_donnee' id='statut$id'$dir_lang>".
			  http_img_pack($puce, $title, "id='imgstatutforms_donnee$id' style='margin: 1px;'") ."</div>";
			if ($script==NULL){
				$action = "'".generer_url_ecrire('puce_statut_forms_donnee',"id='+id",true);
				$script = "<script type='text/javascript'><!--\n";
				$script .= "$(document).ready(function(){
					$('div.puce_forms_donnee').mouseover( function() {
						if (this.puce_loaded) return;
						this.puce_loaded = true;
						id = $(this).id();
						id = id.substr(6,id.length-1);
						$('#statut'+id).load($action,function(){ 
								$('#statutdecalforms_donnee'+id).show(); 
								/*$('#statut'+id).mouseover(function(){ $(this).children('.puce_forms_donnee_popup').show(); });*/
							});
						});
					
				})";
				$script .= "//--></script>";
				$inser_puce = $script . $inser_puce;
			}
		}
	} else {
		$inser_puce = http_img_pack($puce, $title, "id='imgstatutforms_donnee$id' style='margin: 1px;'");
	}
	return $inser_puce;
}

?>
