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

  define ("DEFSITE", "local");
  define ("SYNCSITE", "serveur");
 
  
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
    
$SiteLocal = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "gevutrouville",
	"NOM" => "gevu trouville local",//je sais pas
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
	"lienAdminSpip" => "http://localhost/gevu/trouville/spip/ecrire",
	"urlExeAjax" => WebRoot."//library/php/ExeAjax.php",
	"MenuContexte" => "menu_contextuel_Trouville.xul",
	"urlCarto" => WebRoot."/design/BlocCarte.php",
	"urlVideo" => WebRoot."/design/BlocVideo.php",
	"urlLibPhp" => WebRoot."/library/php/",
	"urlLibJs" => WebRoot."/library/js/",
	"urlLibSwf" => WebRoot."/library/swf/",
	"pathUpload" => PathRoot."/spipsync/IMG/",
	"pathXulJs" => WebRoot."/library/js/",	
	"pathSpip" => "http://localhost/gevu/trouville/spip/",
	"pathImages" => WebRoot."/design/images/"
	); 
$SiteServeur = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "onadadev",
	"NOM" => "gevu serveur",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,

	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"GRILLE_SIG_PROB" => 60,
	"GRILLE_OBS" => 67,
	"GRILLE_CONTROL_V1" => 54,
	"GRILLE_CONTROL_V2" => 70,
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
	"RUB_PORTE_FACE1" => 1342,
	"RUB_PORTE_FACE2" => 1341,

	"DEF_ID" => 5479,
	"DEF_LAT" => 50.63705,
	"DEF_LNG" => 3.06994,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"CARTE_TYPE_DOC" => "75,76",
	"gmKey" => "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRQPuSe5bSrCkW0z0AK5OduyCmU7hRSB6XyMSlG4GUuaIVi6tnDRGuEsWw",


	"lienAdminSpip" => "http://localhost/onadabase/spip/ecrire",
	"urlExeAjax" => WebRoot."/library/php/ExeAjax.php",
	"jsXulParam" => WebRoot."/param/onadabase.xml",
	"MenuContexte" => "menu_contextuel_Trouville.xul",
	"urlSite" => WebRoot."/library/php/Site.php",	
	"urlCarto" => WebRoot."/design/BlocCarte.php",
	"urlVideo" => WebRoot."/design/BlocVideo.php",
	"urlLibPhp" => WebRoot."/library/php/",
	"urlLibJs" => WebRoot."/library/js/",
	"urlLibSwf" => WebRoot."/library/swf/",
	"pathUpload" => "C:/wamp/www/onadabase/spip/IMG/",
	"pathXulJs" => WebRoot."/library/js/",	
	"pathSpip" => "http://localhost/onadabase/spip/",
	"pathImages" => WebRoot."/design/images/"
	); 
	
$SITES = array(
	"local" => $SiteLocal
	,"serveur" => $SiteServeur
	);

?>