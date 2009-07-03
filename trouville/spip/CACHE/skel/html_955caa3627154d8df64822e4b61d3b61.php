<?php
/*
 * Squelette : ../dist/modeles/pagination.html
 * Date :      Mon, 03 Dec 2007 21:39:43 GMT
 * Compile :   Thu, 02 Jul 2009 19:07:22 GMT (0.17s)
 * Pas de boucle
 */ 

//
// Fonction principale du squelette ../dist/modeles/pagination.html
//
function html_955caa3627154d8df64822e4b61d3b61($Cache, $Pile, $doublons=array(), $Numrows=array(), $SP=0) {
	$page = (interdire_scripts($Pile[0]['bloc_ancre']) .
interdire_scripts(vide($Pile['vars']['bornes'] = interdire_scripts(calcul_bornes_pagination(entites_html($Pile[0]['page_courante']),interdire_scripts(entites_html($Pile[0]['nombre_pages'])),'10')))) .
interdire_scripts(vide($Pile['vars']['premiere'] = reset($Pile["vars"]['bornes']))) .
interdire_scripts(vide($Pile['vars']['derniere'] = end($Pile["vars"]['bornes']))) .
interdire_scripts(vide($Pile['vars']['separateur'] = '|')) .
((strval($t1 = (($Pile["vars"]['premiere'] > '1') ? '...':''))!='') ?
		(('<a href=\'' .
	interdire_scripts(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),'')) .
	'#' .
	interdire_scripts(entites_html($Pile[0]['ancre'])) .
	'\' class=\'lien_pagination\'>') . $t1 . ('</a> ' .
	$Pile["vars"]['separateur'])) :
		('')) .
'

' .
interdire_scripts(vide($Pile['vars']['i'] = $Pile["vars"]['premiere'])) .
((strval($t1 = (($Pile["vars"]['i'] <= $Pile["vars"]['derniere']) ? ' ':''))!='') ?
		($t1 . (((strval($t2 = interdire_scripts(vide($Pile['vars']['item'] = mult(moins($Pile["vars"]['i'],'1'),interdire_scripts(entites_html($Pile[0]['pas']))))))!='') ?
			('
	' . $t2 . '
	') :
			('')) .
	
//#INCLURE recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )).
recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )) .
	'
')) :
		('')) .
'
' .
interdire_scripts(vide($Pile['vars']['i'] = plus($Pile["vars"]['i'],'1'))) .
((strval($t1 = (($Pile["vars"]['i'] <= $Pile["vars"]['derniere']) ? ' ':''))!='') ?
		($t1 . (((strval($t2 = interdire_scripts(vide($Pile['vars']['item'] = mult(moins($Pile["vars"]['i'],'1'),interdire_scripts(entites_html($Pile[0]['pas']))))))!='') ?
			('
	' . $t2 . '
	') :
			('')) .
	
//#INCLURE recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )).
recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )) .
	'
')) :
		('')) .
'
' .
interdire_scripts(vide($Pile['vars']['i'] = plus($Pile["vars"]['i'],'1'))) .
((strval($t1 = (($Pile["vars"]['i'] <= $Pile["vars"]['derniere']) ? ' ':''))!='') ?
		($t1 . (((strval($t2 = interdire_scripts(vide($Pile['vars']['item'] = mult(moins($Pile["vars"]['i'],'1'),interdire_scripts(entites_html($Pile[0]['pas']))))))!='') ?
			('
	' . $t2 . '
	') :
			('')) .
	
//#INCLURE recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )).
recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )) .
	'
')) :
		('')) .
'
' .
interdire_scripts(vide($Pile['vars']['i'] = plus($Pile["vars"]['i'],'1'))) .
((strval($t1 = (($Pile["vars"]['i'] <= $Pile["vars"]['derniere']) ? ' ':''))!='') ?
		($t1 . (((strval($t2 = interdire_scripts(vide($Pile['vars']['item'] = mult(moins($Pile["vars"]['i'],'1'),interdire_scripts(entites_html($Pile[0]['pas']))))))!='') ?
			('
	' . $t2 . '
	') :
			('')) .
	
//#INCLURE recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )).
recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )) .
	'
')) :
		('')) .
'
' .
interdire_scripts(vide($Pile['vars']['i'] = plus($Pile["vars"]['i'],'1'))) .
((strval($t1 = (($Pile["vars"]['i'] <= $Pile["vars"]['derniere']) ? ' ':''))!='') ?
		($t1 . (((strval($t2 = interdire_scripts(vide($Pile['vars']['item'] = mult(moins($Pile["vars"]['i'],'1'),interdire_scripts(entites_html($Pile[0]['pas']))))))!='') ?
			('
	' . $t2 . '
	') :
			('')) .
	
//#INCLURE recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )).
recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )) .
	'
')) :
		('')) .
'
' .
interdire_scripts(vide($Pile['vars']['i'] = plus($Pile["vars"]['i'],'1'))) .
((strval($t1 = (($Pile["vars"]['i'] <= $Pile["vars"]['derniere']) ? ' ':''))!='') ?
		($t1 . (((strval($t2 = interdire_scripts(vide($Pile['vars']['item'] = mult(moins($Pile["vars"]['i'],'1'),interdire_scripts(entites_html($Pile[0]['pas']))))))!='') ?
			('
	' . $t2 . '
	') :
			('')) .
	
//#INCLURE recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )).
recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )) .
	'
')) :
		('')) .
'
' .
interdire_scripts(vide($Pile['vars']['i'] = plus($Pile["vars"]['i'],'1'))) .
((strval($t1 = (($Pile["vars"]['i'] <= $Pile["vars"]['derniere']) ? ' ':''))!='') ?
		($t1 . (((strval($t2 = interdire_scripts(vide($Pile['vars']['item'] = mult(moins($Pile["vars"]['i'],'1'),interdire_scripts(entites_html($Pile[0]['pas']))))))!='') ?
			('
	' . $t2 . '
	') :
			('')) .
	
//#INCLURE recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )).
recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )) .
	'
')) :
		('')) .
'
' .
interdire_scripts(vide($Pile['vars']['i'] = plus($Pile["vars"]['i'],'1'))) .
((strval($t1 = (($Pile["vars"]['i'] <= $Pile["vars"]['derniere']) ? ' ':''))!='') ?
		($t1 . (((strval($t2 = interdire_scripts(vide($Pile['vars']['item'] = mult(moins($Pile["vars"]['i'],'1'),interdire_scripts(entites_html($Pile[0]['pas']))))))!='') ?
			('
	' . $t2 . '
	') :
			('')) .
	
//#INCLURE recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )).
recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )) .
	'
')) :
		('')) .
'
' .
interdire_scripts(vide($Pile['vars']['i'] = plus($Pile["vars"]['i'],'1'))) .
((strval($t1 = (($Pile["vars"]['i'] <= $Pile["vars"]['derniere']) ? ' ':''))!='') ?
		($t1 . (((strval($t2 = interdire_scripts(vide($Pile['vars']['item'] = mult(moins($Pile["vars"]['i'],'1'),interdire_scripts(entites_html($Pile[0]['pas']))))))!='') ?
			('
	' . $t2 . '
	') :
			('')) .
	
//#INCLURE recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )).
recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )) .
	'
')) :
		('')) .
'
' .
interdire_scripts(vide($Pile['vars']['i'] = plus($Pile["vars"]['i'],'1'))) .
((strval($t1 = (($Pile["vars"]['i'] <= $Pile["vars"]['derniere']) ? ' ':''))!='') ?
		($t1 . (((strval($t2 = interdire_scripts(vide($Pile['vars']['item'] = mult(moins($Pile["vars"]['i'],'1'),interdire_scripts(entites_html($Pile[0]['pas']))))))!='') ?
			('
	' . $t2 . '
	') :
			('')) .
	
//#INCLURE recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )).
recuperer_fond('',array('fond' => 'modeles/paginationitem' ,'num' => $Pile["vars"]['i'] ,'texte' => $Pile["vars"]['item'] ,'separateur' => $Pile["vars"]['separateur'] ,'url' => interdire_scripts(ancre_url(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),$Pile["vars"]['item']),interdire_scripts(entites_html($Pile[0]['ancre'])))) ,'page_courante' => interdire_scripts(entites_html($Pile[0]['page_courante'])) ,'derniere' => $Pile["vars"]['derniere'] ,'lang' => $GLOBALS["spip_lang"] )) .
	'
')) :
		('')) .
'
' .
((strval($t1 = (($Pile["vars"]['derniere'] < interdire_scripts(entites_html($Pile[0]['nombre_pages']))) ? '...':''))!='') ?
		(($Pile["vars"]['separateur'] .
	'<a href=\'' .
	interdire_scripts(parametre_url(entites_html($Pile[0]['url']),interdire_scripts(entites_html($Pile[0]['debut'])),interdire_scripts(mult(moins(entites_html($Pile[0]['nombre_pages']),'1'),interdire_scripts(entites_html($Pile[0]['pas'])))))) .
	'#' .
	interdire_scripts(entites_html($Pile[0]['ancre'])) .
	'\' class=\'lien_pagination\'>') . $t1 . '</a>') :
		('')) .
'
');

	return analyse_resultat_skel('html_955caa3627154d8df64822e4b61d3b61', $Cache, $page);
}

?>