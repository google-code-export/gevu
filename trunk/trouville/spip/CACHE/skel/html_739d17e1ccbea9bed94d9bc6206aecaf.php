<?php
/*
 * Squelette : formulaires/formulaire_menu_lang.html
 * Date :      Mon, 03 Dec 2007 21:39:14 GMT
 * Compile :   Thu, 02 Jul 2009 17:04:45 GMT (0.00s)
 * Pas de boucle
 */ 

//
// Fonction principale du squelette formulaires/formulaire_menu_lang.html
//
function html_739d17e1ccbea9bed94d9bc6206aecaf($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = ('<div class="formulaire_spip formulaire_menu_lang">
<a name="formulaire_menu_lang" id="formulaire_menu_lang"></a>

<form method="post" action="' .
interdire_scripts(entites_html($Pile[0]['url'])) .
'">
	' .
interdire_scripts(form_hidden(entites_html($Pile[0]['url']))) .
'
	<label for="' .
interdire_scripts(entites_html($Pile[0]['nom'])) .
'">' .
_T('public/spip/ecrire:info_langues') .
'</label>
	<select class="forml" name="' .
interdire_scripts(entites_html($Pile[0]['nom'])) .
'" id="' .
interdire_scripts(entites_html($Pile[0]['nom'])) .
'" onchange="document.location.href=\'' .
interdire_scripts(entites_html($Pile[0]['url'])) .
'&amp;' .
interdire_scripts(entites_html($Pile[0]['nom'])) .
'=\'+this.options[this.selectedIndex].value">
	' .
interdire_scripts($Pile[0]['langues']) .
'</select>
	<noscript>
		<input type="submit" value="&gt;&gt;" />
	</noscript>
</form>

</div>');

	return analyse_resultat_skel('html_739d17e1ccbea9bed94d9bc6206aecaf', $Cache, $page);
}

?>