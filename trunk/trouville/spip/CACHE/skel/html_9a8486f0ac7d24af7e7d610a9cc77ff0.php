<?php
/*
 * Squelette : dist/login.html
 * Date :      Sun, 19 Jul 2009 09:33:34 GMT
 * Compile :   Sun, 19 Jul 2009 09:38:32 GMT (0.03s)
 * Pas de boucle
 */ 

//
// Fonction principale du squelette dist/login.html
//
function html_9a8486f0ac7d24af7e7d610a9cc77ff0($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = ('
' .
'<?php header("X-Spip-Cache: 86400"); ?>'.'<?php header("Cache-Control: max-age=86400"); ?>' .
'<'.'?php header("' . ('Content-Type: text/html; charset=' .
	interdire_scripts($GLOBALS['meta']['charset'])) . '"); ?'.'>' .
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="' .
lang_dir(($Pile[0]['lang'] ? $Pile[0]['lang'] : $GLOBALS['spip_lang']),'ltr','rtl') .
'" lang="' .
htmlentities($Pile[0]['lang'] ? $Pile[0]['lang'] : $GLOBALS['spip_lang']) .
'">
<head>
	<title>' .
_T('public/spip/ecrire:login_acces_prive') .
' - ' .
interdire_scripts(textebrut(typo($GLOBALS['meta']['nom_site']))) .
'</title>
	<meta http-equiv="Content-Type" content="text/html; charset=' .
interdire_scripts($GLOBALS['meta']['charset']) .
'" />
	<meta name="robots" content="none" />
	<meta name="generator" content="SPIP" />
	' .
((strval($t1 = interdire_scripts(find_in_path('favicon.ico')))!='') ?
		('<link rel="shortcut icon" href="' . $t1 . '" />') :
		('')) .
'
	<link rel="stylesheet" href="' .
interdire_scripts(direction_css(find_in_path('spip_style.css'))) .
'" type="text/css" />
	<style type="text/css">
	<!--
		body.page_login { background: #fff; text-align: center; font: 1em Verdana, Tahoma, Arial, Helvetica, sans-serif; }
		.formulaire_spip { text-align: ' .
lang_dir(($Pile[0]['lang'] ? $Pile[0]['lang'] : $GLOBALS['spip_lang']),'left','right') .
'; font-size: 12px; }
		.formulaire_menu_lang { float: ' .
lang_dir(($Pile[0]['lang'] ? $Pile[0]['lang'] : $GLOBALS['spip_lang']),'right','left') .
'; }
		.formulaire_menu_lang label { display: none; }
		.formulaire_login { clear: both; }
		.spip_logos { float: right; padding-left: 10px; }
		.forml { width: 12em; font-family: inherit; font-size: inherit; }
		.spip_bouton input { float: ' .
lang_dir(($Pile[0]['lang'] ? $Pile[0]['lang'] : $GLOBALS['spip_lang']),'right','left') .
'; }
		.reponse_formulaire { font-weight: bold; color: red; }
		a { color: #e86519; text-decoration: none; }
	-->
	</style>
</head>
<body class="page_login">

<div style="text-align: center; width: 400px; margin: 0 auto; padding: 3em 0;">

		<h3 class="spip">' .
interdire_scripts(typo($GLOBALS['meta']['nom_site'])) .
'<br />
			<small>' .
_T('public/spip/ecrire:login_acces_prive') .
'</small>
		</h3>

		' .
executer_balise_dynamique('MENU_LANG_ECRIRE',
	array(htmlentities($Pile[0]['lang'] ? $Pile[0]['lang'] : $GLOBALS['spip_lang'])),
	array(''), $GLOBALS['spip_lang'],36) .
'

		<div class="formulaire_spip formulaire_login">
			' .
executer_balise_dynamique('LOGIN_PRIVE',
	array($Pile[0]['url']),
	array(''), $GLOBALS['spip_lang'],39) .
'

			<p style="text-align: center;">
			' .
((strval($t1 = tester_config(htmlspecialchars(sinon($GLOBALS['meta']['adresse_site'],'.')),'mode_inscription'))!='') ?
		(('&#91;<a href="' .
	interdire_scripts(generer_url_public('spip_inscription','focus=nom_inscription')) .
	'&amp;mode=') . $t1 . ('" target="spip_pass" onclick="javascript:window.open(this.href, \'spip_pass\', \'scrollbars=yes, resizable=yes, width=480, height=400\'); return false;">' .
	_T('public/spip/ecrire:login_sinscrire') .
	'</a>&#93;')) :
		('')) .
'
			&#91;<a href="' .
interdire_scripts(generer_url_public('spip_pass')) .
'" target="spip_pass" onclick="javascript:window.open(this.href, \'spip_pass\', \'scrollbars=yes, resizable=yes, width=480, height=330\'); return false;">' .
_T('public/spip/ecrire:login_motpasseoublie') .
'</a>&#93;
			&#91;<a href="' .
htmlspecialchars(sinon($GLOBALS['meta']['adresse_site'],'.')) .
'/">' .
_T('public/spip/ecrire:login_retoursitepublic') .
'</a>&#93;
			</p>

		</div>
</div>

</body>
</html>');

	return analyse_resultat_skel('html_9a8486f0ac7d24af7e7d610a9cc77ff0', $Cache, $page);
}

?>