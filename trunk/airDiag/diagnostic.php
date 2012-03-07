<?php
session_start();
//local
require_once ("param/ParamPage.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Paramètres de diagnostic</title>
<style type="text/css">
#globalPass
	{
	position:absolute;
	left:50%; 
	top:50%;
	width:420px;
	height:300px;
	margin-top: -308px; /* moitié de la hauteur */
	margin-left: -210px; /* moitié de la largeur */
	border: 1px solid #FFFFFF;
	background-repeat:no-repeat;
	background-color:#000000;
	font-family:Helvetica, sans-serif;
	font-size:15px;
	color:#FFFFFF;
    }

.BlocTextePass
	{
	width:410px;
	margin: 10px;
	margin-top:140px;
    }
</style>
</head>

<body bgcolor="#ffffff">
	<div id='globalPass'>
		<div class='BlocTextePass'>	
			<h3 align="center">Vous allez entrer dans une zone sécurisée</h3>		
			<form name="formulaire" method="post" action="index.php">
			<!--  
			<p align="center">Base de données : <SELECT name="site">
			<?php 
				foreach($objSite->sites as $k => $s){
					//echo $objSite->id." ".$k;
					if($site == $k){
						echo "<OPTION VALUE='".$k."' selected='selected' >".$s["NOM"]."</OPTION>";
					}else{
						echo "<OPTION VALUE='".$k."' >".$s["NOM"]."</OPTION>";
					}
				}
			?>
			</SELECT>
			</p>
			-->
			<p align="center">Login : 
			<input name="login_uti" type="text" id="login_uti" />
			</p>
			<p align="center">Mot de passe : 
			<input name="mdp_uti" type="password" id="mdp_uti" />
			</p>
			<p align="center">
			<input type="submit" name="Submit" value="Envoyer"/>
			</p>
			</form>
			
		</div>
	</div><!--Fin div globalPass-->
</body>
</html>

