<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/mots');
include_spip('inc/meta');
include_spip('base/abstract_sql');

function exec_install_g2g() {

	$version_base = 0.1;
	lire_metas();
	/*
	if(isset($GLOBALS['meta']['g2g_base_version']))
		echo "OUI ".$GLOBALS['meta']['g2g_base_version'];
	else
		echo "NON";
	*/
	
	if ($GLOBALS['meta']['g2g_base_version']==$version_base)
		return;

    $requetes=array(
	    // ajouter les colonnes
        "ALTER TABLE spip_groupes_mots ADD id_parent BIGINT NOT NULL DEFAULT 0 AFTER id_groupe",
        // ajouter l'index
        "ALTER TABLE spip_groupes_mots ADD INDEX (id_parent)",
    );

    foreach($requetes as $requete) {
        if(!spip_query($requete)) die("Echec sur $requete : ".mysql_error());
    }
	ecrire_meta('g2g_base_version',$version_base);
}

?>