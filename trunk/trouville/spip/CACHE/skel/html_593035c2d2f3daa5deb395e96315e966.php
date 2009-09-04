<?php
/*
 * Squelette : dist/inc-entete.html
 * Date :      Sun, 19 Jul 2009 09:33:34 GMT
 * Compile :   Sun, 19 Jul 2009 14:47:22 GMT (0.02s)
 * Pas de boucle
 */ 

//
// Fonction principale du squelette dist/inc-entete.html
//
function html_593035c2d2f3daa5deb395e96315e966($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = ('<div id="entete">

' .
'

<a rel="start" href="' .
htmlspecialchars(sinon($GLOBALS['meta']['adresse_site'],'.')) .
'/">
' .
(affiche_logos(calcule_logo('id_syndic', 'ON', "'0'",'',  ''), '', '') ? (inserer_attribut(inserer_attribut(filtrer('image_reduire',affiche_logos(calcule_logo('id_syndic', 'ON', "'0'",'',  ''), '', ''),'770','200'),'alt',interdire_scripts(textebrut(typo($GLOBALS['meta']['nom_site'])))),'title',interdire_scripts(textebrut(typo($GLOBALS['meta']['nom_site'])))) .
	'

	'):('<span id="nom_site_spip">' .
	interdire_scripts(typo($GLOBALS['meta']['nom_site'])) .
	'</span>
')) .
'
</a>


' .
executer_balise_dynamique('FORMULAIRE_RECHERCHE',
	array(),
	array(''), $GLOBALS['spip_lang'],10) .
'

<br class="nettoyeur" />
</div>');

	return analyse_resultat_skel('html_593035c2d2f3daa5deb395e96315e966', $Cache, $page);
}

?>