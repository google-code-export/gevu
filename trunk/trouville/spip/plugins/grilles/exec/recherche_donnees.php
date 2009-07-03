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

function exec_recherche_donnees(){
	$id_article = intval(_request('id_article'));
	if (_request('field')=='cherche_donnee')
		$recherche = _request('value');
	include_spip("inc/forms_lier_donnees");
	
	// recuperer les donnees deja liees
	list($s,$les_donnees) = Forms_formulaire_article_afficher_donnees($id_article,"");
	
	// recuperer les donnees que l'on peut lier
	$liste = Forms_liste_recherche_donnees($recherche,$les_donnees);
	
	$out = "<ajaxresponse>";
	foreach($liste as $titre=>$donnees){
		$out .= "<item>
 <text><![CDATA[$titre -----------]]></text>
 <value><![CDATA[]]></value>
</item>
";
		foreach ($donnees as $id_donnee=>$champ) {
		$texte = implode (" ",$champ);
		$out .= "<item>
 <text><![CDATA[$texte]]></text>
 <value><![CDATA[$texte]]></value>
 <id_donnee><![CDATA[$id_donnee]]></id_donnee>
</item>
";
		}
	}
	$out .= "</ajaxresponse>";
	$c = $GLOBALS['meta']["charset"];
	header('Content-Type: text/xml; charset='. $c);
	$c = '<' . "?xml version='1.0' encoding='" . $c . "'?" . ">\n";
	echo $c, $out;
	exit;
}

?>