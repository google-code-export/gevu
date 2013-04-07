<?php
require_once( "../../jardindesconnaissances/library/ArrayMixer.php" );
require_once("creaImgHandicateur.php");

$x = new ArrayMixer();
$x->append(array('audio0.jpg','audio1.jpg','audio2.jpg','audio3.jpg'));
$x->append(array('cog0.jpg','cog1.jpg','cog2.jpg','cog3.jpg'));
$x->append(array('moteur0.jpg','moteur1.jpg','moteur2.jpg','moteur3.jpg'));
$x->append(array('visu0.jpg','visu1.jpg','visu2.jpg','visu3.jpg'));
$x->proceed();
$ls = $x->result();
$num = 0;
foreach ($ls as $imgs) {
	echo "<div>image ".$num;
	$url = "";
	$arrImg = explode("_",$imgs);
	foreach ($arrImg as $i) {
		if($i){
			$url .= substr($i, 0, 1)."=".$i."&";
			echo "<img src='".$i."' />";
		}
	}
	echo " image dynamique :<img src='creaImgHandicateur.php?".$url."crea=0' />";
	creaHandiImg($arrImg[0],$arrImg[1],$arrImg[2],$arrImg[3],true);
	echo "</div>";
	$num++;
}
