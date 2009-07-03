<?php
/*
 * Squelette : formulaires/formulaire_recherche.html
 * Date :      Mon, 03 Dec 2007 21:39:13 GMT
 * Compile :   Thu, 02 Jul 2009 19:33:18 GMT (0.00s)
 * Pas de boucle
 */ 

//
// Fonction principale du squelette formulaires/formulaire_recherche.html
//
function html_d41b511157f0da079728025fac792941($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = ('<div class="formulaire_spip formulaire_recherche">
<a name="formulaire_recherche" id="formulaire_recherche"></a>

<form action="' .
interdire_scripts(entites_html($Pile[0]['lien'])) .
'" method="get">
	' .
interdire_scripts(form_hidden(entites_html($Pile[0]['lien']))) .
'
	' .
((strval($t1 = interdire_scripts(entites_html($Pile[0]['lang'])))!='') ?
		('<input type="hidden" name="lang" value="' . $t1 . '" />') :
		('')) .
'
	<label for="recherche">' .
_T('public/spip/ecrire:info_rechercher') .
'</label>
	<input type="text" class="forml" name="recherche" id="recherche" value="' .
interdire_scripts(sinon(entites_html($Pile[0]['recherche']),(_T('public/spip/ecrire:info_rechercher') .
	'" onfocus="this.value=\'\';'))) .
'" />
</form>

</div>');

	return analyse_resultat_skel('html_d41b511157f0da079728025fac792941', $Cache, $page);
}

?>