<?php

define('_DIR_PLUGIN_ACCES_RESTREINT',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));

function g2g_affiche_groupe_mot($flux) {
	//echo "g2g_affiche_groupe_mot<br/>";
	g2g_install();
	$exec =  $flux['args']['exec'];
	if ($exec=='mots_tous'){
		include_spip('exec/g2g');
		$id_groupe = $flux['args']['id_groupe'];
		$flux['data'] .= exec_g2g_affiche_groupe_enfant($id_groupe);
	}
	return $flux;
}

function g2g_arbo_groupe($flux) {
	//echo "g2g_arbo_groupe<br/>";
	$exec =  $flux['args']['exec'];
	if ($exec=='mots_edit'){
		include_spip('exec/g2g');
		$id_groupe = $flux['args']['id_groupe'];
		$flux['data'] .= exec_g2g_arbo_groupe($id_groupe);
	}
	return $flux;
}

function g2g_dans_groupe($flux) {
	//echo "g2g_dans_groupe<br/>";
	g2g_install();
	$exec =  $flux['args']['exec'];
	if ($exec=='mots_type'){
		include_spip('exec/g2g');
		$id_groupe = $flux['args']['id_groupe'];
		$flux['data'] .= exec_g2g_dans_groupe($id_groupe);
	}
	return $flux;
}

function g2g_install(){
	include_spip('exec/install_g2g');
	exec_install_g2g();
}

?>