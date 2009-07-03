<?php
// Database configuration
require_once ("../param/Constantes.php");
if(isset($_GET['site'])){
	$idSite = $_GET['site'];
	$db_server   = $SITES[$idSite]["SQL_HOST"];
	$db_name     = $SITES[$idSite]["SQL_DB"];
	$db_username = $SITES[$idSite]["SQL_LOGIN"];
	$db_password = $SITES[$idSite]["SQL_PWD"];
}else{
	$idSite = -1;
}

//construction du choix des sites
$fSite = "<form action='CreaDump.php' method='get'>
S&eacute;lectionner un site : <select name='site' >";
$i=0;

while ($s = current($SITES)) {
	$selected="";
	if(key($SITES)==$site)
		$selected=" selected=\"selected\"";
	$fSite .= "<option value='".key($SITES)."' ".$selected." >".$s["NOM"]."</option>";
	next($SITES);
}
$fSite .= "</select>
	<input name='valider' type='submit' value='valider' />
	</form><br/>\n";
echo $fSite;

if($idSite!=-1){
	echo "Votre base est en cours de sauvegarde.......
	
	";
	system("mysqldump --host=$db_server --user=$db_name --password=$db_password $db_username > $db_name.sql");
	echo "C'est fini. Vous pouvez récupérer la base par FTP";
}
?>