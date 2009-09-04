<?php
/*
 * Squelette : dist/inc-pied.html
 * Date :      Sun, 19 Jul 2009 09:33:34 GMT
 * Compile :   Sun, 19 Jul 2009 09:38:20 GMT (0.00s)
 * Pas de boucle
 */ 

//
// Fonction principale du squelette dist/inc-pied.html
//
function html_41532c9143f310ca5a5e6f0e2a669ffd($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = ('<br class="nettoyeur" />
<div id="pied">
<small>
	<a href="' .
interdire_scripts(generer_url_public('backend')) .
'" title="' .
_T('public/spip/ecrire:syndiquer_site') .
'"><img src="' .
interdire_scripts(find_in_path('feed.png')) .
'" alt="' .
_T('public/spip/ecrire:icone_suivi_activite') .
'" width="16" height="16" class="format_png" />&nbsp;RSS&nbsp;2.0</a>
	| <a href="' .
interdire_scripts(generer_url_public('plan')) .
'">' .
_T('public/spip/ecrire:plan_site') .
'</a>
	| <a href="ecrire/">' .
_T('public/spip/ecrire:espace_prive') .
'</a>
	| <a href="http://www.spip.net/" title="' .
_T('public/spip/ecrire:site_realise_avec_spip') .
'"><img src="' .
interdire_scripts(find_in_path('spip.png')) .
'" alt="SPIP" width="48" height="16" class="format_png" /></a>
	| <a href="' .
interdire_scripts(entites_html($Pile[0]['skel'])) .
'" title="' .
_T('public/spip/ecrire:voir_squelette') .
'" rel="nofollow">' .
_T('public/spip/ecrire:squelette') .
'</a>
</small>
</div>');

	return analyse_resultat_skel('html_41532c9143f310ca5a5e6f0e2a669ffd', $Cache, $page);
}

?>