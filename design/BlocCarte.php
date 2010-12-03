<?php
//vérifie si la page est include ou ajax
session_start();
if(!$g){
	$ajax = true;
	require_once("../param/ParamPage.php");
}

//récupération de donnée géo 
$arrGeo = $g->GetGeo(-1,$idDon);
//print_r($arrGeo);

?>
<html >
  <head>

<script src="http://maps.google.com/maps?file=api&v=2.x&key=<?php echo $objSite->infos["gmKey"]; ?>" type="text/javascript"></script>
<script src="../library/js/GoogleCarto6.js" type="text/javascript"></script>

<script src="<?php echo $objSite->infos["pathXulJs"]; ?>interface.js" type="text/javascript"></script>
<script src="<?php echo $objSite->infos["pathXulJs"]; ?>ajax.js" type="text/javascript"></script>

<script type="text/javascript">
	var pathRoot = '<?php echo $objSite->infos["urlLibPhp"]; ?>';
	var deflat = <?php echo $arrGeo['lat']; ?>;
	var deflng = <?php echo $arrGeo['lng']; ?>;
	var defzoom = <?php echo $arrGeo['zoom']; ?>;
	var defType = <?php echo $arrGeo['type'];	?>;
	var idRub = <?php echo $arrGeo['id'];	?>;
	var mot = -1;
	var mapQuery = '<?php echo $arrGeo['query'];	?>';
	var site = '<?php echo $objSite->id; ?>';
	var alpha = 'a';
	MiniCarte = false;
	var urlExeAjax = "<?php echo $objSite->infos["urlExeAjax"]; ?>";

</script>
  </head>
  <body onload="initPage()" onunload="GUnload()" >

		<div id='map' style="height:400px;width:450px;" ></div>

  </body>
</html>
