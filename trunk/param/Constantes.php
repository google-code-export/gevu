<?php

  //
  // Fichier contenant les definitions de constantes
  //define ("PathRoot",$_SERVER["DOCUMENT_ROOT"]."/gevu"); 
  define ("PathRoot","C:/wamp/www/gevu"); 
  define ("WebRoot","http://localhost/gevu"); 
  
	// *** chemin de toutes les bases et les spip en service ***
  define("TT_CLASS_BASE", PathRoot."/library/php/");
	// Include the class files.
	require_once(TT_CLASS_BASE."AllClass.php");

  define ("TRACE", false);

  define ("DEFSITE", "trouville");
  define ("SYNCSITE", "trouville");
 
  
  $DB_OPTIONS = array (
		'ERROR_DISPLAY' => true
		);
  
  define ("MaxMarker", 300);
  define ("DELIM",'*');
  define ("jsPathRoot",PathRoot."/library/js/");

  define ("XmlParam",PathRoot."/param/gevu.xml");
  define ("XmlScena",PathRoot."/param/scenarisation.xml");
  
  define('EOL', "\r\n");

  
  date_default_timezone_set('Europe/Paris');

$SiteTrouvilleVoirie = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "gevu_alceane",
	"NOM" => "gevu trouville Big",//je sais pas
    "SITE_PARENT" => array(
		"trouville" => "Ville"
		),
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"GRILLE_SIG_PROB" => 60,
	"GRILLE_CONTROL_V1" => 54,
	"GRILLE_CONTROL_V2" => 70,
	"GRILLE_OBS" => 67,
	"GRILLE_LIGNE_TRANS" => 72,
	"GRILLE_CHAINE_DEPLA" => 71,
	"GRILLE_ETAB" => 55,
	"GRILLE_VOIRIE" => 62,
	"GRILLE_TERRE" => 66,
	"GRILLE_ACTEUR" => 73,
	"GRILLE_GEORSS" => 81,

	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"CHAMPS_CONTROL_DEFFICIENCE" => array("champ"=>"multiple_3","valeur"=>array("moteur"=>"multiple_3_1","audio"=>"multiple_3_2","visu"=>"multiple_3_3","cog"=>"multiple_3_4")),

	"MOT_CLEF_OBS" => 151,
	"MOT_CLEF_DEF_TYPE_CARTE" => 4,
	"MOT_CLEF_PANG" => 64,
	"MOT_CLEF_GARE" => 62,
	"MOT_CLEF_LIGNE_TRANS" => 167,
	"MOT_CLEF_CHAINE_DEPLA" => 168,
	"MOT_CLEF_Pays" => 54,
	"MOT_CLEF_Region" =>56,
	"MOT_CLEF_Departement"=>57,
	"MOT_CLEF_Intercommunalite"=>58,
	"MOT_CLEF_Commune"=>59,
	"MOT_CLEF_Ilot"=>60,
	"MOT_CLEF_Canton"=>138,
	"MOT_CLEF_NA."=>139,
	"MOT_CLEF_Quartier"=>146,

	"RUB_TERRE" => 5479,
	"RUB_PORTE1" => 50,
	"RUB_PORTE2" => 74,
	"RUB_PORTE_FACE" => 1342,
	
	"DEF_ID" => 5479,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"CARTE_TYPE_DOC" => "75,76",
	"gmKey" => "ABQIAAAATs5RWdW8iTPuaiLrkvnqjRTRqFFvaBKBgdVodt96I8idUV-28RTOELCsfx_7hD62HnKwk7Lm8Cg_lQ",
	"lienAdminSpip" => WebRoot."/trouville/spip/ecrire",
	"urlExeAjax" => WebRoot."//library/php/ExeAjax.php",
	"MenuContexte" => "menu_contextuel_Trouville.xul",
	"urlCarto" => WebRoot."/design/BlocCarte.php",
	"urlVideo" => WebRoot."/design/BlocVideo.php",
	"urlLibPhp" => WebRoot."/library/php/",
	"urlLibJs" => WebRoot."/library/js/",
	"urlLibSwf" => WebRoot."/library/swf/",
	"pathUpload" => PathRoot."/trouville/spip/IMG/",
	"pathXulJs" => WebRoot."/library/js/",	
	"pathSpip" => WebRoot."/trouville/spip/",
	"pathImages" => WebRoot."/design/images/"
	); 
  
$SiteTrouvilleERP2 = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "gevu_alceane",
	"NOM" => "gevu trouville ERP 2",//je sais pas
    "SITE_PARENT" => array(
		"trouville" => "Ville"
		),
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"GRILLE_SIG_PROB" => 60,
	"GRILLE_CONTROL_V1" => 54,
	"GRILLE_CONTROL_V2" => 70,
	"GRILLE_OBS" => 67,
	"GRILLE_LIGNE_TRANS" => 72,
	"GRILLE_CHAINE_DEPLA" => 71,
	"GRILLE_ETAB" => 55,
	"GRILLE_VOIRIE" => 62,
	"GRILLE_TERRE" => 66,
	"GRILLE_ACTEUR" => 73,
	"GRILLE_GEORSS" => 81,

	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"CHAMPS_CONTROL_DEFFICIENCE" => array("champ"=>"multiple_3","valeur"=>array("moteur"=>"multiple_3_1","audio"=>"multiple_3_2","visu"=>"multiple_3_3","cog"=>"multiple_3_4")),

	"MOT_CLEF_OBS" => 151,
	"MOT_CLEF_DEF_TYPE_CARTE" => 4,
	"MOT_CLEF_PANG" => 64,
	"MOT_CLEF_GARE" => 62,
	"MOT_CLEF_LIGNE_TRANS" => 167,
	"MOT_CLEF_CHAINE_DEPLA" => 168,
	"MOT_CLEF_Pays" => 54,
	"MOT_CLEF_Region" =>56,
	"MOT_CLEF_Departement"=>57,
	"MOT_CLEF_Intercommunalite"=>58,
	"MOT_CLEF_Commune"=>59,
	"MOT_CLEF_Ilot"=>60,
	"MOT_CLEF_Canton"=>138,
	"MOT_CLEF_NA."=>139,
	"MOT_CLEF_Quartier"=>146,

	"RUB_TERRE" => 5479,
	"RUB_PORTE1" => 50,
	"RUB_PORTE2" => 74,
	"RUB_PORTE_FACE" => 1342,
	
	"DEF_ID" => 5479,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"CARTE_TYPE_DOC" => "75,76",
	"gmKey" => "ABQIAAAATs5RWdW8iTPuaiLrkvnqjRTRqFFvaBKBgdVodt96I8idUV-28RTOELCsfx_7hD62HnKwk7Lm8Cg_lQ",
	"lienAdminSpip" => WebRoot."/trouville/spip2/ecrire",
	"urlExeAjax" => WebRoot."//library/php/ExeAjax.php",
	"MenuContexte" => "menu_contextuel_Trouville.xul",
	"urlCarto" => WebRoot."/design/BlocCarte.php",
	"urlVideo" => WebRoot."/design/BlocVideo.php",
	"urlLibPhp" => WebRoot."/library/php/",
	"urlLibJs" => WebRoot."/library/js/",
	"urlLibSwf" => WebRoot."/library/swf/",
	"pathUpload" => PathRoot."/trouville/spip2/IMG/",
	"pathXulJs" => WebRoot."/library/js/",	
	"pathSpip" => WebRoot."/trouville/spip2/",
	"pathImages" => WebRoot."/design/images/"
	); 

$SiteTrouvilleERP1 = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "gevu_trouville_erp1",
	"NOM" => "gevu trouville ERP 1",//je sais pas
    "SITE_PARENT" => array(
		"trouville" => "Ville"
		),
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"GRILLE_SIG_PROB" => 60,
	"GRILLE_CONTROL_V1" => 54,
	"GRILLE_CONTROL_V2" => 70,
	"GRILLE_OBS" => 67,
	"GRILLE_LIGNE_TRANS" => 72,
	"GRILLE_CHAINE_DEPLA" => 71,
	"GRILLE_ETAB" => 55,
	"GRILLE_VOIRIE" => 62,
	"GRILLE_TERRE" => 66,
	"GRILLE_ACTEUR" => 73,
	"GRILLE_GEORSS" => 81,

	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"CHAMPS_CONTROL_DEFFICIENCE" => array("champ"=>"multiple_3","valeur"=>array("moteur"=>"multiple_3_1","audio"=>"multiple_3_2","visu"=>"multiple_3_3","cog"=>"multiple_3_4")),

	"MOT_CLEF_OBS" => 151,
	"MOT_CLEF_DEF_TYPE_CARTE" => 4,
	"MOT_CLEF_PANG" => 64,
	"MOT_CLEF_GARE" => 62,
	"MOT_CLEF_LIGNE_TRANS" => 167,
	"MOT_CLEF_CHAINE_DEPLA" => 168,
	"MOT_CLEF_Pays" => 54,
	"MOT_CLEF_Region" =>56,
	"MOT_CLEF_Departement"=>57,
	"MOT_CLEF_Intercommunalite"=>58,
	"MOT_CLEF_Commune"=>59,
	"MOT_CLEF_Ilot"=>60,
	"MOT_CLEF_Canton"=>138,
	"MOT_CLEF_NA."=>139,
	"MOT_CLEF_Quartier"=>146,

	"RUB_TERRE" => 5479,
	"RUB_PORTE1" => 50,
	"RUB_PORTE2" => 74,
	"RUB_PORTE_FACE" => 1342,
	
	"DEF_ID" => 5479,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"CARTE_TYPE_DOC" => "75,76",
	"gmKey" => "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRT6Iog2weP-QbQiUhGbROJqrHDJKxRo0SHzsEPAbHqiIlyfJPkWUJB0dg",
	"lienAdminSpip" => WebRoot."/trouville/spip1/ecrire",
	"urlExeAjax" => WebRoot."//library/php/ExeAjax.php",
	"MenuContexte" => "menu_contextuel_Trouville.xul",
	"urlCarto" => WebRoot."/design/BlocCarte.php",
	"urlVideo" => WebRoot."/design/BlocVideo.php",
	"urlLibPhp" => WebRoot."/library/php/",
	"urlLibJs" => WebRoot."/library/js/",
	"urlLibSwf" => WebRoot."/library/swf/",
	"pathUpload" => PathRoot."/trouville/spip1/IMG/",
	"pathXulJs" => WebRoot."/library/js/",	
	"pathSpip" => WebRoot."/trouville/spip1/",
	"pathImages" => WebRoot."/design/images/"
	); 
	
	
$SiteTrouville = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "gevu_villeneuve",
	"NOM" => "gevu trouville global",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
    "SITE_ENFANT" => -1,
	
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"GRILLE_SIG_PROB" => 60,
	"GRILLE_CONTROL_V1" => 54,
	"GRILLE_CONTROL_V2" => 70,
	"GRILLE_OBS" => 67,
	"GRILLE_LIGNE_TRANS" => 72,
	"GRILLE_CHAINE_DEPLA" => 71,
	"GRILLE_ETAB" => 55,
	"GRILLE_VOIRIE" => 62,
	"GRILLE_TERRE" => 66,
	"GRILLE_ACTEUR" => 73,
	"GRILLE_GEORSS" => 81,

	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"CHAMPS_CONTROL_DEFFICIENCE" => array("champ"=>"multiple_3","valeur"=>array("moteur"=>"multiple_3_1","audio"=>"multiple_3_2","visu"=>"multiple_3_3","cog"=>"multiple_3_4")),

	"MOT_CLEF_OBS" => 151,
	"MOT_CLEF_DEF_TYPE_CARTE" => 4,
	"MOT_CLEF_PANG" => 64,
	"MOT_CLEF_GARE" => 62,
	"MOT_CLEF_LIGNE_TRANS" => 167,
	"MOT_CLEF_CHAINE_DEPLA" => 168,
	"MOT_CLEF_Pays" => 54,
	"MOT_CLEF_Region" =>56,
	"MOT_CLEF_Departement"=>57,
	"MOT_CLEF_Intercommunalite"=>58,
	"MOT_CLEF_Commune"=>59,
	"MOT_CLEF_Ilot"=>60,
	"MOT_CLEF_Canton"=>138,
	"MOT_CLEF_NA."=>139,
	"MOT_CLEF_Quartier"=>146,

	"RUB_TERRE" => 5479,
	"RUB_PORTE1" => 50,
	"RUB_PORTE2" => 74,
	"RUB_PORTE_FACE" => 1342,
	
	"DEF_ID" => 5479,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"CARTE_TYPE_DOC" => "75,76",
	"gmKey" => "ABQIAAAATs5RWdW8iTPuaiLrkvnqjRTRqFFvaBKBgdVodt96I8idUV-28RTOELCsfx_7hD62HnKwk7Lm8Cg_lQ",
	"lienAdminSpip" => WebRoot."/trouville/spip/ecrire",
	"urlExeAjax" => WebRoot."//library/php/ExeAjax.php",
	"MenuContexte" => "menu_contextuel_Trouville.xul",
	"urlCarto" => WebRoot."/design/BlocCarte.php",
	"urlVideo" => WebRoot."/design/BlocVideo.php",
	"urlLibPhp" => WebRoot."/library/php/",
	"urlLibJs" => WebRoot."/library/js/",
	"urlLibSwf" => WebRoot."/library/swf/",
	"pathUpload" => PathRoot."/trouville/spip/IMG/",
	"pathXulJs" => WebRoot."/library/js/",	
	"pathSpip" => WebRoot."/trouville/spip/",
	"pathImages" => WebRoot."/design/images/"
	); 
	
$SITES = array(
	"trouville" => $SiteTrouville
	,"trouvilleERP1" => $SiteTrouvilleERP1 
	,"trouvilleERP2" => $SiteTrouvilleERP2
	,"trouvilleVoirie" => $SiteTrouvilleVoirie
	);

?>