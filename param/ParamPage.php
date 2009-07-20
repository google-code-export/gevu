<?php

require_once ("Constantes.php");

if(isset($_POST['login_uti'])) {
	$login=$_POST['login_uti'];
	$mdp=$_POST['mdp_uti'];
	if(TRACE)
		echo "ParamPage:post:$login, $mdp<br/>";
} else {
	$login=$_SESSION['loginSess'];
	$mdp=$_SESSION['mdpSess'];
	$idAuteur=$_SESSION['IdAuteur'];
	if(TRACE)
		echo "ParamPage:session:$login, $mdp, $idAuteur<br/>";
}

	//pour défaut on prend toute les options
	$_SESSION['version']="V2";
	if(!isset($_SESSION['type_contexte'])) 
		$_SESSION['type_contexte'] = array ('multiple_2_1', 'multiple_2_2', 'multiple_2_3', 'multiple_2_4', 'multiple_2_5', 'multiple_2_6');
	if(!isset($_SESSION['type_controle'])) 
		$_SESSION['type_controle'] = array ('multiple_1_1','multiple_1_2');
	if(!isset($_SESSION['IdAuteur'])) 
		$_SESSION['IdAuteur']=1;
	
	if(isset($_GET['f']))
		$fonction = $_GET['f'];
	else
		$fonction = '';
	if(isset($_GET['cols']))
		$cols = $_GET['cols'];
	else
		$cols = -1;
	if(isset($_GET['id']))
		$id = $_GET['id'];
	else
		$id = -1;
	if(isset($_GET['ppp']))
		$ppp = $_GET['ppp'];
	else
		$ppp = -1;



// vérification du site en cours
//echo("_SESSION['site']=".$_SESSION['site']);
if(isset($_GET['site'])){
	$site = $_GET['site'];
}
if(isset($_POST['site'])){
	$site = $_POST['site'];
}
if(isset($_SESSION['site']))
	$site=$_SESSION['site'];
if(!$site)
	$site = DEFSITE;
$_SESSION['site']=$site;

if(!isset($_SESSION['ShowLegendeControle']))
	$_SESSION['ShowLegendeControle']=true;
if(!isset($_SESSION['ShowCarte']))
	$_SESSION['ShowCarte']=false;
if(!isset($_SESSION['ShowDocs']))
	$_SESSION['ShowDocs']=true;
if(!isset($_SESSION['ContEditAll']))
	$_SESSION['ContEditAll']=true;
if(!isset($_SESSION['ContEditPublie']))
	$_SESSION['ContEditPublie']=false;
if(!isset($_SESSION['version']))
	$_SESSION['version']="V1";
	
if(!isset($_SESSION['ForceCalcul']))
	$_SESSION['ForceCalcul']=true;
	
	
if(TRACE)
	echo "ParamPage:session".print_r($_SESSION)."<br/>";

	
if(isset($_GET['type']))
	$type = $_GET['type'];
else
	$type = 'voirie';
	
if(isset($_GET['FicXml']))
	$FicXml = $_GET['FicXml'];
else
	$FicXml = XmlParam;

if(isset($_GET['ParamNom']))
	$ParamNom = $_GET['ParamNom'];
else
	$ParamNom = "GetOntoTree";

if(isset($_GET['box']))
	$box = $_GET['box'];
else
	$box = "singlebox";

if(isset($_GET['UrlNom']))
	$UrlNom = $_GET['UrlNom'];
else
	$UrlNom = "Traduction";

if(isset($_GET['url']))
	$url = $_GET['url'];
else
	$url = "";
	
	
if(isset($_GET['So']))
	$So = $_GET['So'];
else
	$So = "Traduction";

if(isset($_GET['id']))
	$id = $_GET['id'];
else
	$id = $SITES[$site]["DEF_ID"];

if(isset($_GET['idDon']))
	$idDon = $_GET['idDon'];
else
	$idDon = -1;
	
$objSite = new Site($SITES, $site, false);

//print_r($objSite);
$objSiteSync = new Site($SITES, SYNCSITE, false);

if($id!=-1)
	$g = new Granulat($id,$objSite);
	
	
function ChercheAbo ($login, $mdp, $objSite)
	{
		// connexion serveur
		$link = mysql_connect($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"]) or die("Impossible de se connecter : " . mysql_error());	
		// Sélection de la base de données
		//mysql_select_db("solacc", $link);	
		mysql_select_db($objSite->infos["SQL_DB"], $link);	
		
		$sql = "SELECT id_auteur, nom, login, email, statut  FROM spip_auteurs WHERE login = '".$login."' AND pass = md5( CONCAT(alea_actuel,'$mdp'))";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	  	if(TRACE)
	  		echo "ParamPage:ChercheAbo:sql=".$sql."<br/>";
			
		mysql_close($link);
	  	$nbre_lignes = mysql_num_rows($req);
	  	if(TRACE)
	  		echo "ParamPage:ChercheAbo:nbre_lignes=".$nbre_lignes."<br/>";
		if ($nbre_lignes == 1)
		{
			while($resultat = mysql_fetch_assoc($req))
				{	
					$_SESSION['IdAuteur'] = $resultat['id_auteur'];
					$_SESSION['NomSess'] = $resultat['nom'];
					$_SESSION['EmailSess'] = $resultat['email'];
					$_SESSION['loginSess'] = $resultat['login'];	
					$_SESSION['IpSess'] = $_SERVER['REMOTE_ADDR'];
					$_SESSION['mdpSess'] = $mdp;
					//gestion du statut
					if($resultat['statut']=="0minirezo")
						$_SESSION['role'] = "administrateur";
					if($resultat['statut']=="1comite")
						$_SESSION['role'] = "lecteur";
				}
			
		}
		else
		{
			include("diagnostic.php");
			exit;
		}
		if(TRACE)
		  	echo "ParamPage:ChercheAbo:session=".print_r($_SESSION)."<br/>";
	}
	
	

?>