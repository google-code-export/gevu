<?php
/*
 * Squelette : formulaires/formulaire_login.html
 * Date :      Mon, 03 Dec 2007 21:39:13 GMT
 * Compile :   Thu, 02 Jul 2009 17:04:46 GMT (0.20s)
 * Pas de boucle
 */ 

//
// Fonction principale du squelette formulaires/formulaire_login.html
//
function html_f8e412fa2ea0292d4e0221750ffab70f($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = ('<'.'?php header("' . 'Cache-Control: no-store, no-cache, must-revalidate' . '"); ?'.'>' .
'<'.'?php header("' . 'Pragma: no-cache' . '"); ?'.'>' .
((strval($t1 = interdire_scripts(entites_html($Pile[0]['echec_cookie'])))!='') ?
		($t1 . ('
<fieldset class="reponse_formulaire">
<legend>' .
	_T('public/spip/ecrire:avis_erreur_cookie') .
	'</legend>
<p>' .
	_T('public/spip/ecrire:login_cookie_oblige') .
	'</p>
<p>' .
	_T('public/spip/ecrire:login_cookie_accepte') .
	'</p>
</fieldset>')) :
		('')) .
'

	<script type="text/javascript" src="' .
interdire_scripts(eval('return '.'_DIR_IMG_PACK'.';')) .
'md5.js"> </script>
	<form id="login' .
((strval($t1 = interdire_scripts(entites_html($Pile[0]['login'])))!='') ?
		('_' . $t1) :
		('')) .
'"
		action="' .
interdire_scripts(entites_html($Pile[0]['action2'])) .
'"
		method="post"' .
((strval($t1 = interdire_scripts(((entites_html($Pile[0]['source']) == 'spip') ? ' ':'')))!='') ?
		($t1 . ('
		onsubmit=\'if (this.session_password.value) {
			this.session_password_md5.value = calcMD5("' .
	interdire_scripts(entites_html($Pile[0]['alea_actuel'])) .
	'" + this.session_password.value);
			this.next_session_password_md5.value = calcMD5("' .
	interdire_scripts(entites_html($Pile[0]['alea_futur'])) .
	'" + this.session_password.value);
			this.session_password.value = "";
			}\'')) :
		('')) .
'>
	<fieldset>
	<legend>' .
_T('public/spip/ecrire:form_forum_identifiants') .
'</legend>
		' .
((strval($t1 = interdire_scripts($Pile[0]['erreur']))!='') ?
		('<p class="reponse_formulaire">' . $t1 . '</p>') :
		('')) .
'
		' .
((strval($t1 = interdire_scripts((entites_html($Pile[0]['login']) ? '':' ')))!='') ?
		(('<p><label for="var_login">' .
	_T('public/spip/ecrire:login_login2') .
	'</label>') . $t1 . '<br />
		<input type="text" class="forml" id="var_login" name="var_login" value="" size="40" /></p>') :
		('')) .
'
		
' .
((strval($t1 = interdire_scripts(entites_html($Pile[0]['login'])))!='') ?
		(('
' .
	inserer_attribut(reduire_image(affiche_logos(calcule_logo('id_auteur', 'ON', $Pile[0]['id_auteur'],'',  ''), '', ''),'100','80'),'alt',interdire_scripts(entites_html($Pile[0]['login_alt']))) .
	'
<input type="hidden" name="session_login_hidden" value="') . $t1 . ('" />
<script type="text/javascript"><!--
document.write("<p>' .
	_T('public/spip/ecrire:login_login') .
	'&nbsp;<strong>' .
	interdire_scripts(entites_html($Pile[0]['login_alt'])) .
	'<" + "/strong><br />&#91;<a href=\'' .
	interdire_scripts(parametre_url(parametre_url(parametre_url(entites_html($Pile[0]['action2']),'cookie_admin','non'),'url',interdire_scripts($Pile[0]['url'])),'retour',interdire_scripts($Pile[0]['self']))) .
	'\'>' .
	_T('public/spip/ecrire:login_autre_identifiant') .
	'<" + "/a>&#93;</p>")
//--></script>
<noscript>
	<p class="reponse_formulaire">' .
	_T('public/spip/ecrire:login_non_securise') .
	' <a href="' .
	interdire_scripts(entites_html($Pile[0]['action'])) .
	'">' .
	_T('public/spip/ecrire:login_recharger') .
	'</a>.</p>
	<p><label for="session_login">' .
	_T('public/spip/ecrire:login_login2') .
	'</label><br />
	<input type="text" class="forml" name="session_login" id="session_login"' .
	((strval($t2 = interdire_scripts(entites_html($Pile[0]['login'])))!='') ?
			(' value="' . $t2 . '"') :
			('')) .
	' size="40" /></p>
</noscript>

	<p><label' .
	((strval($t2 = interdire_scripts(entites_html($Pile[0]['login'])))!='') ?
			(' for="var_login_' . $t2 . '"') :
			('')) .
	'>' .
	_T('public/spip/ecrire:login_pass2') .
	'</label><br />
	<input type="password" class="forml" name="session_password"' .
	((strval($t2 = interdire_scripts(entites_html($Pile[0]['login'])))!='') ?
			(' id="var_login_' . $t2 . '"') :
			('')) .
	' value="" size="20" /></p>
	<p><input type="checkbox" name="session_remember" id="session' .
	((strval($t2 = interdire_scripts(entites_html($Pile[0]['login'])))!='') ?
			('_' . $t2) :
			('')) .
	'" value="oui"' .
	((strval($t2 = interdire_scripts(filtre_rester_connecte($Pile[0]['prefs'])))!='') ?
			($t2 . 'checked="checked"') :
			('')) .
	'/>
	<label for="session' .
	((strval($t2 = interdire_scripts(entites_html($Pile[0]['login'])))!='') ?
			('_' . $t2) :
			('')) .
	'">' .
	_T('public/spip/ecrire:login_rester_identifie') .
	'</label></p>

	<input type="hidden" name="session_password_md5" value="" />
	<input type="hidden" name="next_session_password_md5" value="" />
	<input type="hidden" name="essai_login" value="oui" />
')) :
		('')) .
'	<input type="hidden" name="url" value="' .
interdire_scripts(entites_html($Pile[0]['url'])) .
'" />
	<div class="spip_bouton"><input type="submit" value="' .
_T('public/spip/ecrire:bouton_valider') .
'" /></div>
	</fieldset>
	</form>
<script type="text/javascript"><!--
document.getElementById(\'var_login' .
((strval($t1 = interdire_scripts(entites_html($Pile[0]['login'])))!='') ?
		('_' . $t1) :
		('')) .
'\').focus();
--></script>' .
((strval($t1 = interdire_scripts(entites_html($Pile[0]['auth_http'])))!='') ?
		('
	<form action="' . $t1 . ('" method="get">' .
	((strval($t2 = interdire_scripts(form_hidden(entites_html($Pile[0]['auth_http']))))!='') ?
			('
	' . $t2 . '
	') :
			('')) .
	'
	<fieldset>
	<legend>' .
	_T('public/spip/ecrire:login_sans_cookiie') .
	'</legend>
	<div>' .
	_T('public/spip/ecrire:login_preferez_refuser') .
	'
	<input type="hidden" name="essai_auth_http" value="oui"/>
	' .
	((strval($t2 = interdire_scripts(entites_html($Pile[0]['url'])))!='') ?
			('<input type="hidden" name="url" value="' . $t2 . '"/>') :
			('')) .
	'
	<div class="spip_bouton"><input type="submit" value="' .
	_T('public/spip/ecrire:login_sans_cookiie') .
	'"/></div></div>
	</fieldset>
	</form>
')) :
		('')));

	return analyse_resultat_skel('html_f8e412fa2ea0292d4e0221750ffab70f', $Cache, $page);
}

?>