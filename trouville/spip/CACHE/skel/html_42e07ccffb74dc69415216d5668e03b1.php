<?php
/*
 * Squelette : dist/404.html
 * Date :      Mon, 03 Dec 2007 21:39:42 GMT
 * Compile :   Thu, 02 Jul 2009 19:33:18 GMT (0.02s)
 * Pas de boucle
 */ 

//
// Fonction principale du squelette dist/404.html
//
function html_42e07ccffb74dc69415216d5668e03b1($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = ('<?php header("X-Spip-Cache: 86400"); ?>'.'<?php header("Cache-Control: max-age=86400"); ?>' .
'<'.'?php header("' . 'Cache-Control: no-store, no-cache, must-revalidate' . '"); ?'.'>' .
'<'.'?php header("' . 'Pragma: no-cache' . '"); ?'.'>' .
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="' .
lang_dir(($Pile[0]['lang'] ? $Pile[0]['lang'] : $GLOBALS['spip_lang']),'ltr','rtl') .
'" lang="' .
htmlentities($Pile[0]['lang'] ? $Pile[0]['lang'] : $GLOBALS['spip_lang']) .
'">
<head>
	<title>' .
_T('public/spip/ecrire:pass_erreur') .
' 404 - ' .
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

	<!-- Ceci est la feuille de style par defaut pour le code genere par SPIP -->
	<link rel="stylesheet" href="' .
interdire_scripts(direction_css(find_in_path('spip_style.css'))) .
'" type="text/css" media="all" />
	<!-- Feuille de styles CSS pour l\'affichage du site sur ecran -->
	<link rel="stylesheet" href="' .
interdire_scripts(direction_css(find_in_path('habillage.css'))) .
'" type="text/css" media="projection, screen, tv" />
	<!-- Feuille de styles CSS pour l\'impression -->
	<link rel="stylesheet" href="' .
interdire_scripts(direction_css(find_in_path('impression.css'))) .
'" type="text/css" media="print" />
</head>
<body class="page_404">
	<div id="page">

	' .
'
	' .

'<?php
	$contexte_inclus = array(\'fond\' => ' . argumenter_squelette('inc-entete') . ',
	\'lang\' => ' . argumenter_squelette($GLOBALS["spip_lang"]) . ');
	include(\'ecrire/public.php\');
?'.'>' .
'

	' .
'
	<div id="hierarchie"><a href="' .
htmlspecialchars(sinon($GLOBALS['meta']['adresse_site'],'.')) .
'/">' .
_T('public/spip/ecrire:accueil_site') .
'</a> &gt; ' .
_T('public/spip/ecrire:pass_erreur') .
' 404</div>

	<div id="conteneur">

		<div id="contenu">

			<div class="cartouche">
			<h1 class="titre">' .
_T('public/spip/ecrire:pass_erreur') .
' 404</h1>
			</div>
			' .
((strval($t1 = interdire_scripts(propre($Pile[0]['erreur'])))!='') ?
		('<div class="chapo">' . $t1 . '</div>') :
		('')) .
'

		</div><!-- fin contenu -->

		' .
'
		<div id="navigation">

			' .
'
			' .

'<?php
	$contexte_inclus = array(\'fond\' => ' . argumenter_squelette('inc-rubriques') . ',
	\'lang\' => ' . argumenter_squelette($GLOBALS["spip_lang"]) . ');
	include(\'ecrire/public.php\');
?'.'>' .
'

		</div><!-- fin navigation -->

	</div><!-- fin conteneur -->

	' .
'
	' .

'<?php
	$contexte_inclus = array(\'fond\' => ' . argumenter_squelette('inc-pied') . ',
	\'skel\' => ' . argumenter_squelette('dist/404.html') . ',
	\'lang\' => ' . argumenter_squelette($GLOBALS["spip_lang"]) . ');
	include(\'ecrire/public.php\');
?'.'>' .
'

	</div><!-- fin page -->
' .
"<div style=\"background-image: url('http://localhost/gevu/trouville/spip/spip.php?action=cron');\"></div>" .
'
</body>
</html>');

	return analyse_resultat_skel('html_42e07ccffb74dc69415216d5668e03b1', $Cache, $page);
}

?>